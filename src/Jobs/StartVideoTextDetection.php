<?php

namespace Meema\MediaRecognition\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Meema\MediaRecognition\Facades\Recognize;

class StartVideoTextDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?int $mediaId;

    private array $filters;

    /**
     * Create a new job instance.
     *
     * @param int|null $mediaId
     * @param array $filters
     */
    public function __construct($mediaId = null, $filters = [])
    {
        $this->mediaId = $mediaId;
        $this->filters = $filters;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Recognize::startTextDetection($this->mediaId, $this->filters);
    }
}
