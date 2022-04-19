<?php

namespace Meema\MediaRecognition\Contracts;

interface MediaRecognition
{
    /**
     * Sets the source/path where the file is stored.
     *
     * @param  string  $source
     * @param  string|null  $mimeType
     * @param  int|null  $mediaId
     * @return $this
     */
    public function source(string $source, string $mimeType = null, int $mediaId = null);

    /**
     * Sets the source/path where the file is stored.
     *
     * @param  string  $path
     * @param  string|null  $mimeType
     * @param  int|null  $mediaId
     * @return $this
     */
    public function path(string $path, string $mimeType = null, int $mediaId = null);

    /**
     * Detects labels/objects in an image or video.
     *
     * @param  int|null  $minConfidence
     * @param  int|null  $maxLabels
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectLabels($minConfidence = null, $maxLabels = null);

    /**
     * Detects labels/objects in an image.
     *
     * @param  int|null  $minConfidence
     * @param  int|null  $maxLabels
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectImageLabels($minConfidence = null, $maxLabels = null);

    /**
     * Detects labels/objects in a video.
     *
     * @param  int|null  $minConfidence
     * @param  int|null  $maxLabels
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectVideoLabels($minConfidence = null, $maxLabels = null);

    /**
     * Detects faces in an image or video & analyzes them.
     *
     * @param  array  $attributes
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectFaces($attributes = ['DEFAULT']);

    /**
     * Detects faces in an image & analyzes them.
     *
     * @param  array  $attributes
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectImageFaces($attributes = ['DEFAULT']);

    /**
     * Detects faces in an image & analyzes them.
     *
     * @param  array  $attributes
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectVideoFaces($attributes = ['DEFAULT']);

    /**
     * Detects moderation labels in an image or video.
     * This can be useful for children-friendly images or NSFW images/videos.
     *
     * @param  int|null  $minConfidence
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectModeration($minConfidence = null);

    /**
     * Detects moderation labels in an image.
     * This can be useful for children-friendly images or NSFW images.
     *
     * @param  int|null  $minConfidence
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectImageModeration($minConfidence = null);

    /**
     * Detects moderation labels in an video.
     * This can be useful for children-friendly videos or NSFW videos.
     *
     * @param  int|null  $minConfidence
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectVideoModeration($minConfidence = null);

    /**
     * Detects text in an image or video (OCR).
     *
     * @param  array|null  $filters
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectText(array $filters = null);

    /**
     * Detects text in an image (OCR).
     *
     * @param  array|null  $filters
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectImageText(array $filters = null);

    /**
     * Detects text in a video (OCR).
     *
     * @param  array|null  $filters
     * @return mixed
     *
     * @throws \Exception
     */
    public function detectVideoText(array $filters = null);

    /**
     * Get the "faces" from the video analysis.
     *
     * @param  string  $jobId
     * @param  int  $mediaId
     * @return \Aws\Result
     *
     * @throws \Exception
     */
    public function getFacesByJobId(string $jobId, int $mediaId);

    /**
     * Get the labels from the video analysis.
     *
     * @param  string  $jobId
     * @param  int  $mediaId
     * @return \Aws\Result
     *
     * @throws \Exception
     */
    public function getLabelsByJobId(string $jobId, int $mediaId);

    /**
     * Get the "content moderation" from the video analysis.
     *
     * @param  string  $jobId
     * @param  int  $mediaId
     * @return \Aws\Result
     *
     * @throws \Exception
     */
    public function getContentModerationByJobId(string $jobId, int $mediaId);

    /**
     * Get the "text detection" from the video analysis.
     *
     * @param  string  $jobId
     * @param  int  $mediaId
     * @return \Aws\Result
     *
     * @throws \Exception
     */
    public function getTextDetectionByJobId(string $jobId, int $mediaId);
}
