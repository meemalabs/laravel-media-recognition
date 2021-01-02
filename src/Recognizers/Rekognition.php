<?php

namespace Meema\MediaRecognition\Recognizers;

use Aws\Rekognition\RekognitionClient;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Meema\MediaRecognition\Contracts\MediaRecognition as MediaRecognitionInterface;
use Meema\MediaRecognition\Events\FacialAnalysisCompleted;
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
     * The relating media model's id.
     *
     * @var int|null
     */
    protected ?int $mediaId = null;

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
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @param int|null $maxLabels
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectLabels($mediaId = null, $minConfidence = null, $maxLabels = null)
    {
        $this->ensureMimeTypeIsSet();

        if (Str::contains($this->mimeType, 'image')) {
            $result = $this->detectImageLabels($mediaId, $minConfidence, $maxLabels);

            // we need to manually fire the event for image analyses because unlike the video analysis,
            // AWS is not sending a webhook upon completion of the image analysis
            event(new FacialAnalysisCompleted($result));

            return $result;
        }

        if (Str::contains($this->mimeType, 'video')) {
            return $this->detectVideoLabels($mediaId, $minConfidence, $maxLabels);
        }

        throw new \Exception('$mimeType does neither indicate being a video nor an image');
    }

    /**
     * Detects faces in an image & analyzes them.
     *
     * @param int|null $mediaId
     * @param array $attributes
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectFaces($mediaId = null, $attributes = ['DEFAULT'])
    {
        $this->ensureMimeTypeIsSet();

        if (Str::contains($this->mimeType, 'image')) {
            $result = $this->detectImageFaces($mediaId, $attributes);

            // we need to manually fire the event for image analyses because unlike the video analysis,
            // AWS is not sending a webhook upon completion of the image analysis
            event(new FacialAnalysisCompleted($result));

            return $result;
        }

        if (Str::contains($this->mimeType, 'video')) {
            return $this->detectVideoFaces($mediaId, $attributes);
        }

        throw new \Exception('$mimeType does neither indicate being a video nor an image');
    }

    /**
     * Detects moderation labels in an image.
     * This can be useful for children-friendly images or NSFW images.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectModeration($mediaId = null, $minConfidence = null)
    {
        $this->setImageSettings();

        $this->settings['MinConfidence'] = $minConfidence ?? config('media-recognition.min_confidence');

        $results = $this->client->detectModerationLabels($this->settings);

        $this->updateOrCreate('moderation', $mediaId, $results);

        return $results;
    }

    /**
     * Detects text in an image (OCR).
     *
     * @param int|null $mediaId
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectText($mediaId = null)
    {
        $this->setImageSettings();

        $results = $this->client->detectText($this->settings);

        $this->updateOrCreate('ocr', $mediaId, $results);

        return $results;
    }

    /**
     * @param $type
     * @param $mediaId
     * @param $results
     * @return mixed
     * @throws \Exception
     */
    protected function updateOrCreate($type, $mediaId, $results)
    {
        if (! config('media-recognition.track_media_recognitions')) {
            return $results;
        }

        if (is_null($mediaId)) {
            throw new Exception('Please make sure to set a $mediaId.');
        }

        MediaRecognition::updateOrCreate([
            'model_id' => $mediaId,
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
        if (! config('media-recognition.track_media_recognitions')) {
            return;
        }

        if (is_null($this->mediaId)) {
            throw new Exception('Please make sure to set a $mediaId.');
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
        if (! config('media-recognition.track_media_recognitions')) {
            return;
        }

        $mediaRecognition = MediaRecognition::where('model_id', $mediaId)->firstOrFail();
        $mediaRecognition->$type = $results;
        $mediaRecognition->save();
    }

    protected function ensureMimeTypeIsSet()
    {
        if (is_null($this->mimeType)) {
            $this->mimeType = Storage::disk(config('media-recognition.disk'))->mimeType($this->path);
        }
    }
}
