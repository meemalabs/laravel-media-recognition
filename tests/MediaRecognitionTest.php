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
    public function it_can_pass_a_test()
    {
        $this->markTestIncomplete();
    }
}
