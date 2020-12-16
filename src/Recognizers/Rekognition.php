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
                'Name' => $this->path,
            ],
        ];
    }

    /**
     * Sets the video to be analyzed.
     *
     * @param $type - used to create a readable identifier.
     * @return void
     * @throws \Exception
     */
    protected function setVideoSettings($type): void
    {
        $disk = $this->disk ?? config('media-recognition.disk');
        $bucketName = config("filesystems.disks.$disk.bucket");

        if (! $bucketName) {
            throw new Exception('Please make sure to set a S3 bucket name.');
        }

        $this->settings['Video'] = [
            'S3Object' => [
                'Bucket' => $bucketName,
                'Name' => $this->path,
            ],
        ];

        $this->settings['NotificationChannel'] = [
            'RoleArn' => config('media-recognition.iam_arn'),
            'SNSTopicArn' => config('media-recognition.sns_topic_arn'),
        ];

        $uniqueId = $type.'_'.$this->mediaId;
        // Idempotent token used to identify the start request.
        // If you use the same token with multiple StartCelebrityRecognition requests, the same JobId is returned.
        // Use ClientRequestToken to prevent the same job from being accidentally started more than once.
        $this->settings['ClientRequestToken'] = $uniqueId;

        // the JobTag is set to be the media id, so we can adjust the media record with the results once the webhook comes in
        $this->settings['JobTag'] = $uniqueId;
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
        $settings = $this->setImageSettings();

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
     * Starts the detection of labels/objects in a video.
     *
     * @param int $mediaId
     * @param int|null $minConfidence
     * @param int $maxResults
     * @return \Aws\Result
     * @throws \Exception
     */
    public function startDetectingLabels(int $mediaId, $minConfidence = null, $maxResults = 1000)
    {
        $this->mediaId = $mediaId;

        $this->setVideoSettings('StartLabelDetection');
        $this->settings['MinConfidence'] = $minConfidence ?? config('media-recognition.min_confidence');
        $this->settings['MaxResults'] = $maxResults;

        $results = $this->client->startLabelDetection($this->settings);

        if ($results['JobId']) {
            $this->updateJobId($results['JobId']);
        }

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
        $settings = $this->setImageSettings();

        $settings['Attributes'] = $attributes;

        $results = $this->client->detectFaces($settings);

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
        $settings = $this->setImageSettings();

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
        $settings = $this->setImageSettings();

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

    /**
     * @param $jobId
     * @return void
     * @throws \Exception
     */
    protected function updateJobId($jobId)
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
        ], ['job_id' => $jobId]);
    }
}
