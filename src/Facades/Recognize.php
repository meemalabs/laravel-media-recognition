<?php

namespace Meema\MediaRecognition\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Aws\Rekognition\RekognitionClient getClient()
 * @method static \Aws\Result detectLabels($mediaId = null, $minConfidence = null, $maxLabels = null)
 * @method static \Aws\Result detectFaces($mediaId = null, $attributes = ['DEFAULT'])
 * @method static \Aws\Result detectModeration($mediaId = null, $minConfidence = null)
 * @method static \Aws\Result detectText($mediaId = null)
 * @method static \Aws\Result startLabelDetection($mediaId = null, $minConfidence = null, $maxResults = 1000)
 * @method static \Aws\Result startFaceDetection($mediaId = null, string $faceAttribute = 'DEFAULT')
 * @method static \Aws\Result startContentModeration($mediaId = null, int $minConfidence = null)
 * @method static \Aws\Result startTextDetection($mediaId = null, array $filters = null)
 * @method static \Aws\Result getLabelsByJobId(string $jobId, int $mediaId)
 * @method static \Aws\Result getFacesByJobId(string $jobId, int $mediaId)
 * @method static \Aws\Result getContentModerationByJobId(string $jobId, int $mediaId)
 * @method static \Aws\Result getTextDetectionByJobId(string $jobId, int $mediaId)
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
