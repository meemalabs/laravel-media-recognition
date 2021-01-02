<?php

namespace Meema\MediaRecognition\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Meema\MediaRecognition\Facades\Recognize;

class StartModerationDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $path;

    private ?int $mediaId;

    private ?int $minConfidence;

    /**
     * Create a new job instance.
     *
     * @param string $path
     * @param int|null $mediaId
     * @param int|null $minConfidence
     */
    public function __construct(string $path, $mediaId = null, $minConfidence = null)
    {
        $this->path = $path;
        $this->mediaId = $mediaId;
        $this->minConfidence = $minConfidence;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        Recognize::source($this->path)->detectModeration($this->mediaId, $this->minConfidence);
    }
}
