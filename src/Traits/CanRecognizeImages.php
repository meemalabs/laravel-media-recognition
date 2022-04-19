<?php

namespace Meema\MediaRecognition\Traits;

use Exception;

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
     * @param  string  $blob
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
     *
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
     * @param  int|null  $minConfidence
     * @param  null  $maxLabels
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectImageLabels($minConfidence = null, $maxLabels = null)
    {
        $this->setImageSettings();

        $this->settings['MinConfidence'] = $minConfidence ?? config('media-recognition.min_confidence');

        if (is_int($maxLabels)) {
            $this->settings['MaxLabels'] = $maxLabels;
        }

        $results = $this->client->detectLabels($this->settings);

        if (is_null($this->mediaId)) {
            return $results;
        }

        $this->updateOrCreate('labels', $results);

        return $results;
    }

    /**
     * @param  array  $attributes
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectImageFaces($attributes = ['DEFAULT'])
    {
        $this->setImageSettings();

        $this->settings['Attributes'] = $attributes;

        $results = $this->client->detectFaces($this->settings);

        if (is_null($this->mediaId)) {
            return $results;
        }

        $this->updateOrCreate('faces', $results);

        return $results;
    }

    /**
     * @param  int|null  $minConfidence
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectImageModeration($minConfidence = null)
    {
        $this->setImageSettings();

        $this->settings['MinConfidence'] = $minConfidence ?? config('media-recognition.min_confidence');

        $results = $this->client->detectModerationLabels($this->settings);

        if (is_null($this->mediaId)) {
            return $results;
        }

        $this->updateOrCreate('moderation', $results);

        return $results;
    }

    /**
     * @param  array|null  $filters
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectImageText(array $filters = null)
    {
        $this->setImageSettings();

        if (is_array($filters)) {
            $this->settings['Filters'] = $filters;
        }

        $results = $this->client->detectText($this->settings);

        if (is_null($this->mediaId)) {
            return $results;
        }

        $this->updateOrCreate('ocr', $results);

        return $results;
    }
}
