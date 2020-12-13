<?php

namespace Meema\MediaRecognition\Tests;

use Meema\MediaRecognition\Providers\MediaRecognitionServiceProvider;
use Orchestra\Testbench\TestCase;

class MediaRecognitionTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [MediaRecognitionServiceProvider::class];
    }
}
