<?php

namespace Meema\MediaRecognition\Tests;

use Dotenv\Dotenv;
use Illuminate\Support\Facades\Config;
use Meema\MediaRecognition\Providers\MediaRecognitionServiceProvider;
use Orchestra\Testbench\TestCase;

class MediaRecognitionTestCase extends TestCase
{
    public $jobSettings = [];

    protected function getPackageProviders($app)
    {
        return [MediaRecognitionServiceProvider::class];
    }

    public function initializeDotEnv()
    {
        if (! file_exists(__DIR__.'/../.env')) {
            return;
        }

        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();
    }

    public function initializeSettings()
    {
        // let's make sure these config values are set
        Config::set('media-converter.credentials.key', env('AWS_ACCESS_KEY_ID'));
        Config::set('media-converter.credentials.secret', env('AWS_SECRET_ACCESS_KEY'));
        Config::set('media-recognition.disk', env('REKOGNITION_DISK', 's3'));
        Config::set('media-recognition.iam_arn', env('AWS_IAM_REKOGNITION_ARN'));
        Config::set('media-recognition.sns_topic_arn', env('AWS_SNS_TOPIC_ARN'));
        Config::set('filesystems.disks.s3.bucket', env('AWS_S3_BUCKET'));
    }
}
