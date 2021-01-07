<?php

namespace Meema\MediaRecognition\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Meema\MediaRecognition\Events\TextAnalysisCompleted;
use Meema\MediaRecognition\Facades\Recognize;

class StartTextDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $path;

    /**
     * $mimeType may be 'image', 'video' or the actual mime type of the file.
     * It allows us to reduce an HTTP request to check for the mime type.
     * If not assigned, it will check the mime type for whether it is an image or a video source.
     *
     * @var string|null
     */
    private ?string $mimeType = null;

    private ?int $mediaId = null;

    private array $filters;

    /**
     * Create a new job instance.
     *
     * @param string $path
     * @param string|null $mimeType
     * @param int|null $mediaId
     * @param array $filters
     */
    public function __construct(string $path, $mimeType = null, $mediaId = null, $filters = [])
    {
        $this->path = $path;
        $this->mimeType = $mimeType;
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
        $this->ensureMimeTypeIsSet();

        if (Str::contains($this->mimeType, 'image')) {
            $result = Recognize::source($this->path, $this->mimeType)->detectText($this->mediaId, $this->filters);

            // we need to manually fire the event for image analyses because unlike the video analysis,
            // AWS is not sending a webhook upon completion of the image analysis
            event(new TextAnalysisCompleted($result));

            return;
        }

        if (Str::contains($this->mimeType, 'video')) {
            Recognize::source($this->path, $this->mimeType)->detectText($this->mediaId, $this->filters);

            return;
        }
    }

    protected function ensureMimeTypeIsSet()
    {
        if (is_null($this->mimeType)) {
            $this->mimeType = Storage::disk(config('media-recognition.disk'))->mimeType($this->path);
        }
    }
}
