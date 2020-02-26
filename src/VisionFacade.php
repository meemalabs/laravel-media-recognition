<?php

namespace Meema\LaravelVision;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Meema\LaravelVision\VisionClass
 */
class VisionFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'vision';
    }
}
