<?php

namespace Meema\MediaRecognition\Recognizers;

use Aws\Credentials\Credentials;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Storage;
use Meema\MediaRecognition\Contracts\MediaRecognition;
use Meema\MediaRecognition\Traits\InteractsWithStorage;

class Rekognition implements MediaRecognition
{
    use InteractsWithStorage;

    /**
     * Client instance of MediaRecognition.
     *
     * @var \Aws\Rekognition\RekognitionClient
     */
    protected RekognitionClient $client;

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
     * Detects labels/objects in an image.
     */
    public function detectFaces()
    {
        return $this->client->detectLabels([
            'Image' => [
                'S3Object' => [
                    'Bucket' => 'meema-stage',
                    'Name' => 'test-media/people.jpg',
                ],
            ],
        ]);
    }

    /**
     * Detects faces in an image.
     */
    public function detectFaces()
    {
        //if ($this->path && $this->disk) {
        //    Storage::disk($this->disk)->path($this->path);
        //}

        return $this->client->detectFaces();
    }

    /**
     * Detects text in an image (OCR).
     */
    public function detectText($minConfidence = 50)
    {
        $bytes = '';

        $results = $this->client->detectText([
            'Image' => ['Bytes' => $bytes],
            'MinConfidence' => $minConfidence,
        ])['TextDetections'];
    }
}
