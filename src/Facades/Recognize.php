<?php

namespace Meema\MediaRecognition\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Aws\Rekognition\RekognitionClient getClient()
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition source(string $source, $mimeType = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition path(string $path, $mimeType = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectLabels($mediaId = null, $minConfidence = null, $maxLabels = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectImageLabels($mediaId = null, $minConfidence = null, $maxLabels = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectVideoLabels($mediaId = null, $minConfidence = null, $maxLabels = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectFaces($mediaId = null, $attributes = ['DEFAULT'])
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectImageFaces($mediaId = null, $attributes = ['DEFAULT'])
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectVideoFaces($mediaId = null, $attributes = ['DEFAULT'])
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectModeration($mediaId = null, $minConfidence = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectImageModeration($mediaId = null, $minConfidence = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectVideoModeration($mediaId = null, $minConfidence = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectText($mediaId = null, array $filters = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectImageText($mediaId = null, array $filters = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectVideoText($mediaId = null, array $filters = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition getLabelsByJobId(string $jobId, int $mediaId)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition getFacesByJobId(string $jobId, int $mediaId)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition getContentModerationByJobId(string $jobId, int $mediaId)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition getTextDetectionByJobId(string $jobId, int $mediaId)
 */
class Recognize extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'recognize';
    }
}
