<?php

namespace Meema\MediaRecognition\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Meema\MediaRecognition\Facades\Recognize;

class StartVideoModerationDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?int $mediaId;

    private ?int $minConfidence;

    /**
     * Create a new job instance.
     *
     * @param int|null $mediaId
     * @param int|null $minConfidence
     */
    public function __construct($mediaId = null, $minConfidence = null)
    {
        $this->mediaId = $mediaId;
        $this->minConfidence = $minConfidence;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Recognize::startContentModeration($this->mediaId, $this->minConfidence);
    }
}
