<?php

namespace Meema\MediaRecognition\Recognizers;

use Aws\Credentials\Credentials;
use Aws\Rekognition\RekognitionClient;
use Meema\MediaRecognition\Contracts\MediaRecognition;

class Rekognition implements MediaRecognition
{
    /**
     * Client instance of MediaRecognition.
     *
     * @var \Aws\Rekognition\RekognitionClient
     */
    protected RekognitionClient $client;

    /**
     * Construct converter.
     *
     * @param \Aws\Rekognition\RekognitionClient $client
     */
    public function __construct(RekognitionClient $client)
    {
        $config = config('media-recognition');

        $this->client = new RekognitionClient([
            'version' => $config['version'],
            'region' => $config['region'],
            'credentials' => new Credentials($config['credentials']['key'], $config['credentials']['secret']),
        ]);
    }

    /**
     * Get the MediaRecognition Client.
     *
     * @return \Aws\Rekognition\RekognitionClient
     */
    public function getClient(): RekognitionClient
    {
        return $this->client;
    }

    /**
     * Cancels an active job.
     *
     * @param string $id
     * @return \Aws\Result
     */
    public function testing(string $id)
    {
        return $this->client->cancelJob([
            'Id' => $id,
        ]);
    }
}
