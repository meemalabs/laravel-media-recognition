<?php

namespace Meema\MediaRecognition\Traits;

trait InteractsWithStorage
{
    /**
     * The disk of where the file to analyze is stored.
     *
     * @var string
     */
    protected string $disk;

    /**
     * The path to the source of the file to analyze.
     *
     * @var string
     */
    protected string $source;

    /**
     * Set which S3 disk to use.
     *
     * @param string $disk
     * @return $this
     */
    public function disk(string $disk)
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * The equivalent of the S3 Key / the path of the file inside the bucket.
     *
     * @param string $source
     * @return $this
     */
    public function source(string $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Ensures the source/path not to be null if it is null it will thrown an exception.
     *
     * @return void
     * @throws \Exception
     */
    public function ensureSourceIsNotNull()
    {
        if (is_null($this->source)) {
            throw new \Exception('please set a source to run the analysis on');
        }
    }
}
