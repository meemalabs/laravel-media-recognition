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
     * The path of the file to analyze.
     *
     * @var string
     */
    protected string $path;

    /**
     * Set which S3 disk to use.
     *
     * @param string $disk
     *
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
     * @param string $path
     *
     * @return $this
     */
    public function path(string $path)
    {
        $this->path = $path;

        return $this;
    }
}
