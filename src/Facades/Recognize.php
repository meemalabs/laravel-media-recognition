<?php

namespace Meema\MediaRecognition\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Aws\Rekognition\RekognitionClient getClient()
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
