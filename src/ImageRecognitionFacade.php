<?php

namespace Meema\LaravelImageRecognition;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Meema\LaravelImageRecognition\VisionClass
 */
class ImageRecognitionFacade extends Facade
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
