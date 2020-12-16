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
     * @var string
     */
    protected string $blob;

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
     * Detects labels/objects in an image.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @param int|null $maxLabels
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectLabels($mediaId = null, int $minConfidence = null, int $maxLabels = null)
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

        MediaRecognition::create($results, $mediaId);

        return $results;
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
     */
    public function detectFaces()
    {
        //return $this->client->detectLabels([
        //'NotificationChannel' => [
        //    "RoleArn" => config('media-recognition.iam_arn'),
        //    "SNSTopicArn" => config('media-recognition.sns_topic_arn'),
        //],
        //    'Image' => [
        //        'S3Object' => [
        //            'Bucket' => 'meema-stage',
        //            'Name' => 'test-media/people.jpg',
        //        ],
        //    ],
        //]);
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
