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
     * The path to the source of the file to analyze.
     *
     * @var string|null
     */
    protected ?string $mimeType = null;

    /**
     * The id of your model that is related to this recognition.
     *
     * @var int|null
     */
    protected ?int $mediaId = null;

    /**
     * Set which S3 disk to use.
     *
     * @param  string  $disk
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
     * @param  string  $source
     * @param  string|null  $mimeType
     * @param  int|null  $mediaId
     * @return $this
     */
    public function source(string $source, string $mimeType = null, int $mediaId = null)
    {
        $this->source = $source;
        $this->mimeType = $mimeType;
        $this->mediaId = $mediaId;

        return $this;
    }

    /**
     * Alias of source().
     *
     * @param  string  $source
     * @param  string|null  $mimeType
     * @param  int|null  $mediaId
     * @return $this
     */
    public function path(string $source, string $mimeType = null, int $mediaId = null)
    {
        return $this->source($source, $mimeType, $mediaId);
    }

    /**
     * Ensures the source/path not to be null if it is null it will thrown an exception.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function ensureSourceIsNotNull()
    {
        if (is_null($this->source)) {
            throw new \Exception('Please set a $source to run the analysis on');
        }
    }
}
