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
                'Name' => $this->source,
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
     * Starts asynchronous detection of labels/objects in a stored video.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @param int $maxResults
     * @return \Aws\Result
     * @throws \Exception
     */
    public function detectVideoLabels($mediaId = null, $minConfidence = null, $maxResults = 1000)
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
     * @param int|null $mediaId
     * @param array $attributes
     * @return mixed
     * @throws \Exception
     */
    public function detectVideoFaces($mediaId = null, $attributes = ['DEFAULT'])
    {
        $this->mediaId = $mediaId;

        $this->setVideoSettings('faces');
        $this->settings['FaceAttributes'] = $attributes;

        $results = $this->client->startFaceDetection($this->settings);

        if ($results['JobId']) {
            $this->updateJobId($results['JobId'], 'faces');
        }

        return $results;
    }

    /**
     * Starts the detection of faces in a video.
     *
     * @param null $mediaId
     * @param string $faceAttribute
     * @return \Aws\Result
     * @throws \Exception
     */
    public function startFaceDetection($mediaId = null, string $faceAttribute = 'DEFAULT')
    {
        $this->mediaId = $mediaId;

        $this->setVideoSettings('faces');
        $this->settings['FaceAttributes'] = $faceAttribute;

        $results = $this->client->startFaceDetection($this->settings);

        if ($results['JobId']) {
            $this->updateJobId($results['JobId'], 'faces');
        }

        return $results;
    }

    /**
     * Starts asynchronous detection of unsafe content in a stored video.
     *
     * @param null $mediaId
     * @param int|null $minConfidence
     * @return \Aws\Result
     * @throws \Exception
     */
    public function startContentModeration($mediaId = null, $minConfidence = null)
    {
        $this->mediaId = $mediaId;

        $this->setVideoSettings('moderation');

        $this->settings['MinConfidence'] = $minConfidence ?? config('media-recognition.min_confidence');

        $results = $this->client->startContentModeration($this->settings);

        if ($results['JobId']) {
            $this->updateJobId($results['JobId'], 'faces');
        }

        return $results;
    }

    /**
     * Starts asynchronous detection of text in a stored video.
     *
     * @param null $mediaId
     * @param array|null $filters
     * @return \Aws\Result
     * @throws \Exception
     */
    public function startTextDetection($mediaId = null, array $filters = null)
    {
        $this->mediaId = $mediaId;

        $this->setVideoSettings('ocr');

        if (is_array($filters)) {
            $this->settings['Filters'] = $filters;
        }

        $results = $this->client->startTextDetection($this->settings);

        if ($results['JobId']) {
            $this->updateJobId($results['JobId'], 'ocr');
        }

        return $results;
    }

    /**
     * Get the labels from the video analysis.
     *
     * @param string $jobId
     * @param int $mediaId
     * @return \Aws\Result
     * @throws \Exception
     */
    public function getLabelsByJobId(string $jobId, int $mediaId)
    {
        $results = $this->client->getLabelDetection([
            'JobId' => $jobId,
        ]);

        $this->updateVideoResults($results->toArray(), 'labels', $mediaId);

        return $results;
    }

    /**
     * Get the faces from the video analysis.
     *
     * @param string $jobId
     * @param int $mediaId
     * @return \Aws\Result
     * @throws \Exception
     */
    public function getFacesByJobId(string $jobId, int $mediaId)
    {
        $results = $this->client->getFaceDetection([
            'JobId' => $jobId,
        ]);

        $this->updateVideoResults($results->toArray(), 'faces', $mediaId);

        return $results;
    }

    /**
     * Get the "content moderation" from the video analysis.
     *
     * @param string $jobId
     * @param int $mediaId
     * @return \Aws\Result
     * @throws \Exception
     */
    public function getContentModerationByJobId(string $jobId, int $mediaId)
    {
        $results = $this->client->getContentModeration([
            'JobId' => $jobId,
        ]);

        $this->updateVideoResults($results->toArray(), 'moderation', $mediaId);

        return $results;
    }

    /**
     * Get the faces from a video analysis.
     *
     * @param string $jobId
     * @param int $mediaId
     * @return \Aws\Result
     * @throws \Exception
     */
    public function getTextDetectionByJobId(string $jobId, int $mediaId)
    {
        $results = $this->client->getTextDetection([
            'JobId' => $jobId,
        ]);

        $this->updateVideoResults($results->toArray(), 'ocr', $mediaId);

        return $results;
    }
}
