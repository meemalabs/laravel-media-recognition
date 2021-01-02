<?php

namespace Meema\MediaRecognition\Contracts;

interface MediaRecognition
{
    /**
     * Sets the source/path where the file is stored.
     *
     * @param string $source
     * @return $this
     */
    public function source(string $source);

    /**
     * Sets the source/path where the file is stored.
     *
     * @param string $path
     * @return $this
     */
    public function path(string $path);

    /**
     * Detects labels/objects in an image or video.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @param int|null $maxLabels
     * @return mixed
     * @throws \Exception
     */
    public function detectLabels($mediaId = null, $minConfidence = null, $maxLabels = null);

    /**
     * Detects labels/objects in an image.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @param int|null $maxLabels
     * @return mixed
     * @throws \Exception
     */
    public function detectImageLabels($mediaId = null, $minConfidence = null, $maxLabels = null);

    /**
     * Detects labels/objects in a video.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @param int|null $maxLabels
     * @return mixed
     * @throws \Exception
     */
    public function detectVideoLabels($mediaId = null, $minConfidence = null, $maxLabels = null);

    /**
     * Detects faces in an image or video & analyzes them.
     *
     * @param int|null $mediaId
     * @param array $attributes
     * @return mixed
     * @throws \Exception
     */
    public function detectFaces($mediaId = null, $attributes = ['DEFAULT']);

    /**
     * Detects faces in an image & analyzes them.
     *
     * @param int|null $mediaId
     * @param array $attributes
     * @return mixed
     * @throws \Exception
     */
    public function detectImageFaces($mediaId = null, $attributes = ['DEFAULT']);

    /**
     * Detects faces in an image & analyzes them.
     *
     * @param int|null $mediaId
     * @param array $attributes
     * @return mixed
     * @throws \Exception
     */
    public function detectVideoFaces($mediaId = null, $attributes = ['DEFAULT']);

    /**
     * Detects moderation labels in an image or video.
     * This can be useful for children-friendly images or NSFW images/videos.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @return mixed
     * @throws \Exception
     */
    public function detectModeration($mediaId = null, $minConfidence = null);

    /**
     * Detects moderation labels in an image.
     * This can be useful for children-friendly images or NSFW images.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @return mixed
     * @throws \Exception
     */
    public function detectImageModeration($mediaId = null, $minConfidence = null);

    /**
     * Detects moderation labels in an video.
     * This can be useful for children-friendly videos or NSFW videos.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @return mixed
     * @throws \Exception
     */
    public function detectVideoModeration($mediaId = null, $minConfidence = null);

    /**
     * Detects text in an image or video (OCR).
     *
     * @param int|null $mediaId
     * @param array|null $filters
     * @return mixed
     * @throws \Exception
     */
    public function detectText($mediaId = null, array $filters = null);

    /**
     * Detects text in an image (OCR).
     *
     * @param int|null $mediaId
     * @param array|null $filters
     * @return mixed
     * @throws \Exception
     */
    public function detectImageText($mediaId = null, array $filters = null);

    /**
     * Detects text in a video (OCR).
     *
     * @param int|null $mediaId
     * @param array|null $filters
     * @return mixed
     * @throws \Exception
     */
    public function detectVideoText($mediaId = null, array $filters = null);

    /**
     * Get the "faces" from the video analysis.
     *
     * @param string $jobId
     * @param int $mediaId
     * @return \Aws\Result
     * @throws \Exception
     */
    public function getFacesByJobId(string $jobId, int $mediaId);

    /**
     * Get the labels from the video analysis.
     *
     * @param string $jobId
     * @param int $mediaId
     * @return \Aws\Result
     * @throws \Exception
     */
    public function getLabelsByJobId(string $jobId, int $mediaId);

    /**
     * Get the "content moderation" from the video analysis.
     *
     * @param string $jobId
     * @param int $mediaId
     * @return \Aws\Result
     * @throws \Exception
     */
    public function getContentModerationByJobId(string $jobId, int $mediaId);

    /**
     * Get the "text detection" from the video analysis.
     *
     * @param string $jobId
     * @param int $mediaId
     * @return \Aws\Result
     * @throws \Exception
     */
    public function getTextDetectionByJobId(string $jobId, int $mediaId);
}
