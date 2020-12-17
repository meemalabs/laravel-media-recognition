<?php

namespace Meema\MediaRecognition\Traits;

use Exception;

trait CanRecognizeVideos
{
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

        $this->setVideoSettings('labels');
        $this->settings['MinConfidence'] = $minConfidence ?? config('media-recognition.min_confidence');
        $this->settings['MaxResults'] = $maxResults;

        $results = $this->client->startLabelDetection($this->settings);

        if ($results['JobId']) {
            $this->updateJobId($results['JobId'], 'labels');
        }

        return $results;
    }

    /**
     * Starts the detection of faces in a video.
     *
     * @param int $mediaId
     * @param array $attributes
     * @return \Aws\Result
     * @throws \Exception
     */
    public function startDetectingFaces($mediaId = null, $attributes = ['DEFAULT'])
    {
        $this->mediaId = $mediaId;

        $this->setVideoSettings('faces');

        $this->settings['MinConfidence'] = $minConfidence ?? config('media-recognition.min_confidence');

        $results = $this->client->startFaceDetection($this->settings);

        if ($results['JobId']) {
            $this->updateJobId($results['JobId'], 'faces');
        }

        return $results;
    }
}
