<?php

return [
    /*
    * The fully qualified class name of the "media" model.
    */
    'media_model' => \App\Models\Media::class,

    /**
     * IAM Credentials from AWS.
     *
     * Please note, if you are intending to use Laravel Vapor, rename
     * From: AWS_ACCESS_KEY_ID - To: e.g. VAPOR_ACCESS_KEY_ID
     * From: AWS_SECRET_ACCESS_KEY - To: e.g. VAPOR_SECRET_ACCESS_KEY
     * and ensure that your Vapor environment has these values defined.
     */
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],

    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),

    /**
     * Specify the version of the Rekognition API you would like to use.
     * Please only adjust this value if you know what you are doing.
     */
    'version' => 'latest',

    /*
     * The disk that your images/videos to analyze are stored on.
     * Choose one of the disks you've configured in config/filesystems.php.
     */
    'disk' => env('REKOGNITION_DISK', 's3'),

    /**
     * Specify the IAM Role ARN.
     *
     * You can find the Role ARN visiting the following URL:
     * https://console.aws.amazon.com/iam/home?region=us-east-1#/roles
     * Please note to adjust the "region" in the URL above.
     * Tip: in case you need to create a new Role, you may name it `Rekognition_Default_Role`
     * by making use of this name, AWS Rekognition will default to using this IAM Role.
     */
    'iam_arn' => env('AWS_IAM_ARN'),

    /**
     * Specify the AWS SNS Topic ARN.
     * This triggers the webhook to be sent.
     *
     * It can be found by selecting your "Topic" when visiting the following URL:
     * https://console.aws.amazon.com/sns/v3/home?region=us-east-1#/topics
     * Please note to adjust the "region" in the URL above.
     */
    'sns_topic_arn' => env('AWS_SNS_TOPIC_ARN'),

    /**
     * Specifies the minimum confidence level for the labels to return.
     * Amazon Rekognition doesn't return any labels with confidence lower than this specified value.
     *
     * If min_confidence is not specified, the operation returns labels with a confidence
     * values greater than or equal to 55 percent.
     *
     * Type: Float
     * Valid Range: Minimum value of 0. Maximum value of 100.
     */
    'min_confidence' => 55,

];
