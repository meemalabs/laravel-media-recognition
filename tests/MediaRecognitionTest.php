<?php

namespace Meema\MediaRecognition\Tests;

use Aws\Rekognition\RekognitionClient;

class MediaRecognitionTest extends MediaRecognitionTestCase
{
    /**
     * @var \Aws\Rekognition\RekognitionClient
     */
    protected $client;

    /**
     * Setup client and results.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->getMockBuilder(RekognitionClient::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /** @test */
    public function it_can_detect_image_labels()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_can_detect_image_faces()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_can_detect_image_moderation()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_can_detect_image_text()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_can_detect_video_labels()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_can_detect_video_faces()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_can_detect_video_moderation()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_can_detect_video_text()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_can_get_video_labels_by_job_id()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_can_get_video_faces_by_job_id()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_can_get_video_moderation_by_job_id()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_can_get_video_text_by_job_id()
    {
        $this->markTestIncomplete();
    }
}
