# MediaRecognition Package for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/meema/laravel-media-recognition.svg?style=flat-square)](https://packagist.org/packages/meema/laravel-media-recognition)
[![StyleCI](https://github.styleci.io/repos/264578171/shield?branch=master)](https://github.styleci.io/repos/264578171)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/meemalabs/laravel-media-recognition/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/meemalabs/laravel-media-recognition/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/meema/laravel-media-recognition.svg?style=flat-square)](https://packagist.org/packages/meema/laravel-media-recognition)
[![License](https://img.shields.io/github/license/meemalabs/laravel-media-recognition.svg?style=flat-square)](https://github.com/meemalabs/laravel-media-recognition/blob/master/LICENSE.md)
<!-- [[![Test](https://github.com/meemalabs/laravel-media-recognition/workflows/Test/badge.svg?branch=master)](https://github.com/meemalabs/laravel-media-recognition/actions) -->
<!-- [[![Build Status](wip)](ghactions) -->

At the current state, this is a wrapper package for AWS Rekognition with some extra handy methods.

![laravel-media-recognition package image](https://banners.beyondco.de/Media%20Recognition.png?theme=light&packageManager=composer+require&packageName=meema%2Flaravel-media-recognition&pattern=architect&style=style_1&description=Easily+%26+quickly+analyze%2Frecognize+the+content+of+your+images.&md=1&showWatermark=1&fontSize=175px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

## Usage

``` php
use Meema\MediaRecognition\Facades\Recognize;

// run any of the following methods:
Recognize::path($path);
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
```

The following is the content of the published config file:

```php
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

];
```

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
