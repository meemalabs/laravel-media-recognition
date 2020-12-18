<?php

namespace Meema\MediaRecognition\Recognizers;

use Aws\Rekognition\RekognitionClient;
use Exception;
use Meema\MediaRecognition\Contracts\MediaRecognition as MediaRecognitionInterface;
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
}
