<?php

namespace Meema\MediaRecognition\Contracts;

interface MediaRecognition
{
    /**
     * @param int|string $mediaId
     * @param int|null $minConfidence
     * @param int|null $maxLabels
     * @return mixed
     */
    public function detectLabels($mediaId, int $minConfidence, int $maxLabels);

    public function detectFaces();

    public function detectText();

}
