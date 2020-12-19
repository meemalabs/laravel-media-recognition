<?php

namespace Meema\MediaRecognition\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Meema\MediaRecognition\Facades\Recognize;

class StartVideoLabelDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?int $mediaId;

    private ?int $minConfidence;

    private int $maxResults;

    /**
     * Create a new job instance.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     * @param int $maxResults
     */
    public function __construct($mediaId = null, $minConfidence = null, $maxResults = 1000)
    {
        $this->mediaId = $mediaId;
        $this->minConfidence = $minConfidence;
        $this->maxResults = $maxResults;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Recognize::startLabelDetection($this->mediaId, $this->minConfidence, $this->maxResults);
    }
}
