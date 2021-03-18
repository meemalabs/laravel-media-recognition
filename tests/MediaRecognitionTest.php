<?php

use Meema\MediaRecognition\Facades\Recognize;

uses(Meema\MediaRecognition\Tests\MediaRecognitionTestCase::class);

beforeEach(function () {
    $this->initializeDotEnv();
    $this->initializeSettings();
});

it('can recognize source', function () {
    $path = 'test-media/butterfly.jpg';
    $mimeType = 'image/jpeg';

    $recognize = Recognize::source($path, $mimeType);

    $this->assertTrue($recognize != null);
});

it('it can detect image labels', function () {
    $path = 'test-media/butterfly.jpg';
    $mimeType = 'image/jpeg';

    $recognize = Recognize::source($path, $mimeType);
    $response = $recognize->detectLabels();

    $this->assertTrue(count($response['Labels']) > 0);
    $this->assertEquals($response['@metadata']['statusCode'], 200);
});

it('it can detect image faces', function () {
    $path = 'test-media/people.jpg';
    $mimeType = 'image/jpeg';

    $recognize = Recognize::source($path, $mimeType);
    $response = $recognize->detectFaces();

    $this->assertTrue(count($response['FaceDetails']) > 0);
    $this->assertEquals($response['@metadata']['statusCode'], 200);
});

it('it can detect image moderation', function () {
    $path = 'test-media/yoga_swimwear.jpg';
    $mimeType = 'image/jpeg';

    $recognize = Recognize::source($path, $mimeType);
    $response = $recognize->detectModeration();

    $this->assertTrue(count($response['ModerationLabels']) > 0);
    $this->assertEquals($response['@metadata']['statusCode'], 200);
});

it('it can detect image text', function () {
    $path = 'test-media/coffee_monday.jpg';
    $mimeType = 'image/jpeg';

    $recognize = Recognize::source($path, $mimeType);
    $response = $recognize->detectText();

    $this->assertTrue(count($response['TextDetections']) > 0);
    $this->assertEquals($response['@metadata']['statusCode'], 200);
});

it('it can detect video labels', function () {
    $path = 'pest-tests/sample-video.mp4';
    $mimeType = 'video/mp4';

    $recognize = Recognize::source($path, $mimeType);
    $response = $recognize->detectLabels();

    $this->assertTrue($response['JobId'] != null);
    $this->assertEquals($response['@metadata']['statusCode'], 200);
});

it('it can detect video faces', function () {
    $path = 'pest-tests/sample-video.mp4';
    $mimeType = 'video/mp4';

    $recognize = Recognize::source($path, $mimeType);
    $response = $recognize->detectFaces();

    $this->assertTrue($response['JobId'] != null);
    $this->assertEquals($response['@metadata']['statusCode'], 200);
});

it('it can detect video moderation', function () {
    $path = 'pest-tests/sample-video.mp4';
    $mimeType = 'video/mp4';

    $recognize = Recognize::source($path, $mimeType);
    $response = $recognize->detectModeration();

    $this->assertTrue($response['JobId'] != null);
    $this->assertEquals($response['@metadata']['statusCode'], 200);
});

it('it can detect video text', function () {
    $path = 'pest-tests/sample-video.mp4';
    $mimeType = 'video/mp4';

    $recognize = Recognize::source($path, $mimeType);
    $response = $recognize->detectText();

    $this->assertTrue($response['JobId'] != null);
    $this->assertEquals($response['@metadata']['statusCode'], 200);
});
