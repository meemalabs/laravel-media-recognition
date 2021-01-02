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
}
