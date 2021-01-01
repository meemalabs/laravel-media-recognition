<?php

namespace Meema\MediaRecognition\Traits;

use Exception;
use Meema\MediaRecognition\Models\MediaRecognition;

trait CanRecognizeImages
{
    /**
     * The input image as base64-encoded bytes.
     *
     * @var string|null
     */
    protected ?string $blob = null;

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
     * @return void
     * @throws \Exception
     */
    protected function setImageSettings(): void
    {
        $this->ensureSourceIsNotNull();

        if (is_string($this->blob)) {
            $this->settings['Image'] = [
                'Bytes' => $this->blob,
            ];

            return;
        }

        $disk = $this->disk ?? config('media-recognition.disk');
        $bucketName = config("filesystems.disks.$disk.bucket");

        if (! $bucketName) {
            throw new Exception('Please make sure to set a S3 bucket name.');
        }

        $this->settings['Image'] = [
            'S3Object' => [
                'Bucket' => $bucketName,
                'Name' => $this->source,
            ],
        ];
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
        $this->setImageSettings();

        $this->settings['MinConfidence'] = $minConfidence ?? config('media-recognition.min_confidence');

        if (is_int($maxLabels)) {
            $this->settings['MaxLabels'] = $maxLabels;
        }

        $results = $this->client->detectLabels($this->settings);

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
     * Detects faces in an image & analyzes them.
     *
     * @param int|null $mediaId
     * @param array $attributes
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectFaces($mediaId = null, $attributes = ['DEFAULT'])
    {
        $this->setImageSettings();

        $this->settings['Attributes'] = $attributes;

        $results = $this->client->detectFaces($this->settings);

        $this->updateOrCreate('faces', $mediaId, $results);

        return $results;
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
}
