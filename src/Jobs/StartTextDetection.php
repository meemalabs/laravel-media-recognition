<?php

namespace Meema\MediaRecognition\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Meema\MediaRecognition\Facades\Recognize;

class StartTextDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $path;

    private ?int $mediaId;

    private array $filters;

    /**
     * Create a new job instance.
     *
     * @param string $path
     * @param int|null $mediaId
     * @param array $filters
     */
    public function __construct(string $path, $mediaId = null, $filters = [])
    {
        $this->path = $path;
        $this->mediaId = $mediaId;
        $this->filters = $filters;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        Recognize::source($this->path)->detectText($this->mediaId, $this->filters);
    }
}
