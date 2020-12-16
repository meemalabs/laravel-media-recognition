<?php

namespace Meema\MediaRecognition\Recognizers;

use Aws\Rekognition\RekognitionClient;
use Exception;
use Meema\MediaRecognition\Contracts\MediaRecognition as MediaRecognitionInterface;
use Meema\MediaRecognition\Models\MediaRecognition;
use Meema\MediaRecognition\Traits\InteractsWithStorage;

class Rekognition implements MediaRecognitionInterface
{
    use InteractsWithStorage;

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
     * The input image as base64-encoded bytes.
     *
     * @var string|null
     */
    protected ?string $blob = null;

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
     * Set the base64 encoded image.
     *
     * @param string $blob
     *
     * @return $this
     */
    public function blob(string $blob)
    {
        $this->blob = $blob;

        return $this;
    }

    /**
     * Sets the image to be analyzed.
     *
     * @return array
     * @throws \Exception
     */
    protected function setImage(): array
    {
        if (is_string($this->blob)) {
            $settings['Image'] = [
                'Bytes' => $this->blob,
            ];

            return $settings;
        }

        $disk = $this->disk ?? config('media-recognition.disk');
        $bucketName = config("filesystems.disks.$disk.bucket");

        if (! $bucketName) {
            throw new Exception('Please make sure to set a S3 bucket name.');
        }

        $settings['Image'] = [
            'S3Object' => [
                'Bucket' => $bucketName,
                'Name' => $this->path,
            ],
        ];

        return $settings;
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
        $settings = $this->setImage();

        $settings['MinConfidence'] = $minConfidence ?? config('media-recognition.min_confidence');

        if (is_int($maxLabels)) {
            $settings['MaxLabels'] = $maxLabels;
        }

        $results = $this->client->detectLabels($settings);

        if (! config('media-recognition.track_media_recognitions')) {
            return $results;
        }

        if (is_null($mediaId)) {
            throw new Exception('Please make sure to set a $mediaId.');
        }

        MediaRecognition::updateOrCreate([
            'model_id' => $mediaId,
            'model_type' => config('media-converter.media_model'),
        ], ['labels' => $results->toArray()]);

        return $results;
    }

    /**
     * Detects faces & analyzes them.
     *
     * @param int|null $mediaId
     * @param array $attributes
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectFaces($mediaId = null, $attributes = ['DEFAULT'])
    {
        $settings = $this->setImage();

        $settings['Attributes'] = $attributes;

        $results = $this->client->detectFaces($settings);

        $this->updateOrCreate('faces', $mediaId, $results);

        return $results;
    }

    /**
     * Detects faces & analyzes them.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectModeration($mediaId = null, $minConfidence = null)
    {
        $settings = $this->setImage();

        $settings['MinConfidence'] = $minConfidence ?? config('media-recognition.min_confidence');

        $results = $this->client->detectModerationLabels($settings);

        $this->updateOrCreate('moderation', $mediaId, $results);

        return $results;
    }

    /**
     * Detects text in an image (OCR).
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectText($mediaId = null, $minConfidence = null)
    {
        $settings = $this->setImage();

        $results = $this->client->detectText($settings);

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
}
