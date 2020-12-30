<?php

namespace Meema\MediaRecognition\Contracts;

interface MediaRecognition
{
    /**
     * Detects labels/objects in an image.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @param int|null $maxLabels
     * @return mixed
     * @throws \Exception
     */
    public function detectLabels($mediaId = null, $minConfidence = null, $maxLabels = null);

    /**
     * Detects faces in an image & analyzes them.
     *
     * @param int|null $mediaId
     * @param array $attributes
     * @return mixed
     * @throws \Exception
     */
    public function detectFaces($mediaId = null, $attributes = ['DEFAULT']);

    /**
     * Detects moderation labels in an image.
     * This can be useful for children-friendly images or NSFW images.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @return mixed
     * @throws \Exception
     */
    public function detectModeration($mediaId = null, $minConfidence = null);

    /**
     * Detects text in an image (OCR).
     *
     * @param int|null $mediaId
     * @return mixed
     * @throws \Exception
     */
    public function detectText($mediaId = null);
}
