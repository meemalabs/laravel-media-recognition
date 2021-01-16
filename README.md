# Media Recognition Package for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/meema/laravel-media-recognition.svg?style=flat-square)](https://packagist.org/packages/meema/laravel-media-recognition)
[![StyleCI](https://github.styleci.io/repos/264578171/shield?branch=main)](https://github.styleci.io/repos/264578171)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/meemalabs/laravel-media-recognition/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/meemalabs/laravel-media-recognition/?branch=main)
[![Total Downloads](https://img.shields.io/packagist/dt/meema/laravel-media-recognition.svg?style=flat-square)](https://packagist.org/packages/meema/laravel-media-recognition)
[![License](https://img.shields.io/github/license/meemalabs/laravel-media-recognition.svg?style=flat-square)](https://github.com/meemalabs/laravel-media-recognition/blob/main/LICENSE.md)
<!-- [[![Test](https://github.com/meemalabs/laravel-media-recognition/workflows/Test/badge.svg?branch=main)](https://github.com/meemalabs/laravel-media-recognition/actions) -->
<!-- [[![Build Status](wip)](ghactions) -->

At the current state, this is a wrapper package for AWS Rekognition with some extra handy methods.

![laravel-media-recognition package image](https://banners.beyondco.de/Media%20Recognition.png?theme=light&packageManager=composer+require&packageName=meema%2Flaravel-media-recognition&pattern=architect&style=style_1&description=Easily+%26+quickly+analyze%2Frecognize+the+content+of+your+images.&md=1&showWatermark=1&fontSize=175px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

## Usage

``` php
use Meema\MediaRecognition\Facades\Recognize;

// run any of the following methods:
// note: any of the detect*() method parameters are optional and will default to config values

// "image operations"
$recognize = Recognize::path('images/persons.jpg', 'image/jpeg'); // while the $mimeType parameter is optional, it is recommended for performance reasons
$recognize->detectLabels($minConfidence = null, $maxLabels = null)
$recognize->detectFaces($attributes = ['DEFAULT'])
$recognize->detectModeration($minConfidence = null)
$recognize->detectText()

// "video operations"
$recognize = Recognize::path('videos/amazing-video.mp4', 'video/mp4');
$recognize->startLabelDetection($minConfidence = null, $maxResults = 1000)
$recognize->startFaceDetection(string $faceAttribute = 'DEFAULT')
$recognize->startContentModeration(int $minConfidence = null)
$recognize->startTextDetection(array $filters = null)

// get the analysis/status of your jobs
$recognize->getLabelsByJobId(string $jobId)
$recognize->getFacesByJobId(string $jobId)
$recognize->getContentModerationByJobId(string $jobId)
$recognize->getTextDetectionByJobId(string $jobId)

// if you want to track your media recognitions, use the Recognizable trait on your media model && run the included migration
$media = Media::first();
$media->recognize($path)->detectFaces(); // you may chain any of the detection methods
```

## Installation

You can install the package via composer:

```bash
composer require meema/laravel-media-recognition
```

The package will automatically register itself.

Next, publish the config file with:

```bash
php artisan vendor:publish --provider="Meema\MediaRecognition\Providers\MediaRecognitionServiceProvider" --tag="config"
```

Next, please add the following keys their values to your `.env` file.

```bash
AWS_ACCESS_KEY_ID=xxxxxxx
AWS_SECRET_ACCESS_KEY=xxxxxxx
AWS_DEFAULT_REGION=us-east-1
AWS_SNS_TOPIC_ARN=arn:aws:sns:us-east-1:000000000000:RekognitionUpdate
AWS_S3_BUCKET=bucket-name
```

The following is the content of the published config file:

```php
return [
    /**
     * The fully qualified class name of the "media" model.
     */
    'media_model' => \App\Models\Media::class,

    /**
     * IAM Credentials from AWS.
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

    /**
     * The S3 bucket name where the image/video to be analyzed is stored.
     */
    'bucket' => env('AWS_S3_BUCKET'),

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

];
```

## Preparing Your Media Model (optional)

This package includes a trait for your "Media model" that you may use to define the relationship of your media model with the tracked recognitions.

Simply use it as follows:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Meema\MediaRecognition\Traits\Recognizable;

class Media extends Model
{
    use Recognizable;

    // ...
}
```

### Set Up Webhooks (optional)

This package makes use of webhooks in order to communicate the updates of the AWS Rekognition job. Please follow the following steps to enable webhooks for yourself.

Please note, this is only optional, and you should only enable this if you want to track the Rekognition job's results for long-lasting processes (e.g. analyzing video).

#### Setup Expose

First, let's use [Expose](https://beyondco.de/docs/expose/getting-started/installation) to "expose" / generate a URL for our local API. Follow the Expose documentation on how you can get started and generate a "live" & sharable URL for within your development environment.

It should be as simple as `cd my-laravel-api && expose`.

#### Setup AWS SNS Topic & Subscription

Second, let's create an AWS SNS Topic which will notify our "exposed" API endpoint:

1. Open the Amazon SNS console at https://console.aws.amazon.com/sns/v3/home
2. In the navigation pane, choose Topics, and then choose "Create new topic".
3. For Topic name, enter `RekognitionUpdate`, and then choose "Create topic".

![AWS SNS Topic Creation Screenshot](https://i.imgur.com/4MKtfuY.png)

4. Choose the topic ARN link for the topic that you just created. It looks something like this: `arn:aws:sns:region:123456789012:RekognitionUpdate`.
5. On the Topic details: `RekognitionUpdate` page, in the Subscriptions section, choose "Create subscription".
6. For Protocol, choose "HTTPS". For Endpoint, enter exposed API URL that you generated in a previous step, including the API URI.

For example,
```
https://meema-api.sharedwithexpose.com/api/webhooks/media-recognition
```

7. Choose "Create subscription".

#### Confirming Your Subscription

Finally, we need to confirm the subscription which is easily done by navigating to the `RekognitionUpdate` Topic page. There, you should see the following section:

![AWS SNS Subscription Confirmation Screenshot](https://i.imgur.com/oTPwNen.png)

By default, AWS will have sent a post request to URL you defined in your "Subscription" setup. You can view request in the Expose interface, by visiting the "Dashboard Url", which should be similar to: `http://127.0.0.1:4040`

Once you are in the Expose dashboard, you need to locate the `SubscribeURL` value. Once located, copy it and use it to confirm your SNS Topic Subscription.

![AWS SNS Subscription Confirmation Screenshot](https://i.imgur.com/ECGIBUY.png)

Now, your API will receive webhooks as AWS provides updates!

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email chris@cion.agency instead of using the issue tracker.

## Credits

- [Chris Breuer](https://github.com/Chris1904)
- [Folks at Meema](https://github.com/meemalabs)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

Made with ❤️ by Meema, Inc.
