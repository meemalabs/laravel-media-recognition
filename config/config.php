<?php

return [
    /**
     * IAM Credentials from AWS.
     */
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],

    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'version' => 'latest',

    /*
     * The disk where the image/video to be analyzed is stored. Choose
     * one of the disks you've configured in config/filesystems.php.
    */
    'disk' => env('RECOGNITION_DISK', 'local'),

];
