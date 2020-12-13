<?php

namespace Meema\MediaRecognition;

use Aws\Credentials\Credentials;
use Aws\Rekognition\RekognitionClient;
use Exception;
use Illuminate\Support\Manager;
use Meema\MediaRecognition\Recognizers\Rekognition;

class MediaRecognitionManager extends Manager
{
    /**
     * Get a driver instance.
     *
     * @param string|null $name
     * @return mixed
     */
    public function engine($name = null)
    {
        return $this->driver($name);
    }

    /**
     * Create an Amazon MediaRecognition Converter instance.
     *
     * @return \Meema\MediaRecognition\Recognizers\Rekognition
     * @throws \Exception
     */
    public function createMediaRecognitionDriver(): Rekognition
    {
        $this->ensureAwsSdkIsInstalled();

        $config = $this->config['media-recognition'];

        $credentials = $this->getCredentials($config['credentials']);

        $client = $this->setMediaRecognitionClient($config, $credentials);

        return new Rekognition($client);
    }

    /**
     * Sets the Recognition client.
     *
     * @param array $config
     * @param \Aws\Credentials\Credentials $credentials
     * @return \Aws\Rekognition\RekognitionClient
     */
    protected function setMediaRecognitionClient(array $config, Credentials $credentials): RekognitionClient
    {
        return new RekognitionClient([
            'version' => $config['version'],
            'region' => $config['region'],
            'credentials' => $credentials,
        ]);
    }

    /**
     * Get credentials of AWS.
     *
     * @param array $credentials
     * @return \Aws\Credentials\Credentials
     */
    protected function getCredentials(array $credentials): Credentials
    {
        return new Credentials($credentials['key'], $credentials['secret']);
    }

    /**
     * Ensure the AWS SDK is installed.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function ensureAwsSdkIsInstalled()
    {
        if (! class_exists(RekognitionClient::class)) {
            throw new Exception('Please install the AWS SDK PHP using `composer require aws/aws-sdk-php`.');
        }
    }

    /**
     * Get the default media recognition driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return 'recognition';
    }
}
