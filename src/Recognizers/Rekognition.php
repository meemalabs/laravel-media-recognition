<?php

namespace Meema\MediaRecognition\Recognizers;

use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Meema\MediaRecognition\Contracts\MediaRecognition as MediaRecognitionInterface;
use Meema\MediaRecognition\Events\FacialAnalysisCompleted;
use Meema\MediaRecognition\Events\LabelAnalysisCompleted;
use Meema\MediaRecognition\Events\ModerationAnalysisCompleted;
use Meema\MediaRecognition\Events\TextAnalysisCompleted;
use Meema\MediaRecognition\Models\MediaRecognition;
use Meema\MediaRecognition\Traits\CanRecognizeImages;
use Meema\MediaRecognition\Traits\CanRecognizeVideos;
use Meema\MediaRecognition\Traits\InteractsWithStorage;

class Rekognition implements MediaRecognitionInterface
{
    use InteractsWithStorage, CanRecognizeImages, CanRecognizeVideos;

    /**
     * Client instance of MediaRecognition.
     *
     * @var \Aws\Rekognition\RekognitionClient
     */
    protected RekognitionClient $client;

    /**
     * The settings provided to the Rekognition job.
     *
     * @var array
     */
    protected array $settings;

    /**
     * Construct converter.
     *
     * @param \Aws\Rekognition\RekognitionClient $client
     * @throws \Exception
     */
    public function __construct(RekognitionClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get the MediaRecognition Client.
     *
     * @return \Aws\Rekognition\RekognitionClient
     */
    public function getClient(): RekognitionClient
    {
        return $this->client;
    }

    /**
     * Detects labels/objects in an image.
     *
     * @param int|null $minConfidence
     * @param int|null $maxLabels
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectLabels($minConfidence = null, $maxLabels = null)
    {
        $this->ensureMimeTypeIsSet();

        if (Str::contains($this->mimeType, 'image')) {
            $result = $this->detectImageLabels($minConfidence, $maxLabels);

            // we need to manually fire the event for image analyses because unlike the video analysis,
            // AWS is not sending a webhook upon completion of the image analysis
            event(new LabelAnalysisCompleted($result->toArray(), $this->mediaId));

            return $result;
        }

        if (Str::contains($this->mimeType, 'video')) {
            return $this->detectVideoLabels($minConfidence, $maxLabels);
        }

        throw new \Exception('$mimeType does neither indicate being a video nor an image');
    }

    /**
     * Detects faces in an image & analyzes them.
     *
     * @param array $attributes
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectFaces($attributes = ['DEFAULT'])
    {
        $this->ensureMimeTypeIsSet();

        if (Str::contains($this->mimeType, 'image')) {
            $result = $this->detectImageFaces($attributes);

            // we need to manually fire the event for image analyses because unlike the video analysis,
            // AWS is not sending a webhook upon completion of the image analysis
            event(new FacialAnalysisCompleted($result->toArray(), $this->mediaId));

            return $result;
        }

        if (Str::contains($this->mimeType, 'video')) {
            return $this->detectVideoFaces($attributes);
        }

        throw new \Exception('$mimeType does neither indicate being a video nor an image');
    }

    /**
     * Detects moderation labels in an image.
     * This can be useful for children-friendly images or NSFW images.
     *
     * @param int|null $minConfidence
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectModeration($minConfidence = null)
    {
        $this->ensureMimeTypeIsSet();

        if (Str::contains($this->mimeType, 'image')) {
            $result = $this->detectImageModeration($minConfidence);

            // we need to manually fire the event for image analyses because unlike the video analysis,
            // AWS is not sending a webhook upon completion of the image analysis
            event(new ModerationAnalysisCompleted($result->toArray(), $this->mediaId));

            return $result;
        }

        if (Str::contains($this->mimeType, 'video')) {
            return $this->detectVideoModeration($minConfidence);
        }

        throw new \Exception('$mimeType does neither indicate being a video nor an image');
    }

    /**
     * Detects text in an image (OCR).
     *
     * @param array|null $filters
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectText(array $filters = null)
    {
        $this->ensureMimeTypeIsSet();

        if (Str::contains($this->mimeType, 'image')) {
            $result = $this->detectImageText($filters);

            // we need to manually fire the event for image analyses because unlike the video analysis,
            // AWS is not sending a webhook upon completion of the image analysis
            event(new TextAnalysisCompleted($result->toArray(), $this->mediaId));

            return $result;
        }

        if (Str::contains($this->mimeType, 'video')) {
            return $this->detectVideoText($filters);
        }

        throw new \Exception('$mimeType does neither indicate being a video nor an image');
    }

    /**
     * @param $type
     * @param $results
     * @return mixed
     * @throws \Exception
     */
    protected function updateOrCreate($type, $results)
    {
        MediaRecognition::updateOrCreate([
            'model_id' => $this->mediaId,
            'model_type' => config('media-converter.media_model'),
        ], [$type => $results->toArray()]);

        return $results;
    }

    /**
     * @param string $jobId
     * @param string $type
     * @return void
     * @throws \Exception
     */
    protected function updateJobId(string $jobId, string $type)
    {
        if (is_null($this->mediaId)) {
            return;
        }

        MediaRecognition::updateOrCreate([
            'model_id' => $this->mediaId,
            'model_type' => config('media-converter.media_model'),
        ], [$type.'_job_id' => $jobId]);
    }

    /**
     * @param array $results
     * @param string $type
     * @param int $mediaId
     * @return void
     */
    protected function updateVideoResults(array $results, string $type, int $mediaId)
    {
        $mediaRecognition = MediaRecognition::where('model_id', $mediaId)->firstOrFail();
        $mediaRecognition->$type = $results;
        $mediaRecognition->save();
    }

    protected function ensureMimeTypeIsSet()
    {
        if (is_null($this->mimeType)) {
            $this->mimeType = Storage::disk(config('media-recognition.disk'))->mimeType($this->source);
        }
    }
}
