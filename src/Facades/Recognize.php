<?php

namespace Meema\MediaRecognition\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Aws\Rekognition\RekognitionClient getClient()
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition source(string $source, string $mimeType = null, int $mediaId = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition path(string $source, string $mimeType = null, int $mediaId = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectLabels($minConfidence = null, $maxLabels = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectImageLabels($minConfidence = null, $maxLabels = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectVideoLabels($minConfidence = null, $maxLabels = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectFaces($attributes = ['DEFAULT'])
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectImageFaces($attributes = ['DEFAULT'])
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectVideoFaces($attributes = ['DEFAULT'])
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectModeration($minConfidence = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectImageModeration($minConfidence = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectVideoModeration($minConfidence = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectText(array $filters = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectImageText(array $filters = null)
 * @method static \Meema\MediaRecognition\Contracts\MediaRecognition detectVideoText(array $filters = null)
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
