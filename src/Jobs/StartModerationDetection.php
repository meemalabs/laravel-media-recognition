<?php

namespace Meema\MediaRecognition\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Meema\MediaRecognition\Events\ModerationAnalysisCompleted;
use Meema\MediaRecognition\Facades\Recognize;

class StartModerationDetection implements ShouldQueue
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

    private ?int $minConfidence;

    /**
     * Create a new job instance.
     *
     * @param  string  $path
     * @param  string|null  $mimeType
     * @param  int|null  $mediaId
     * @param  int|null  $minConfidence
     */
    public function __construct(string $path, $mimeType = null, $mediaId = null, $minConfidence = null)
    {
        $this->path = $path;
        $this->mimeType = $mimeType;
        $this->mediaId = $mediaId;
        $this->minConfidence = $minConfidence;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function handle()
    {
        $this->ensureMimeTypeIsSet();

        if (Str::contains($this->mimeType, 'image')) {
            $result = Recognize::source($this->path, $this->mimeType, $this->mediaId)->detectModeration($this->minConfidence);

            // we need to manually fire the event for image analyses because unlike the video analysis,
            // AWS is not sending a webhook upon completion of the image analysis
            event(new ModerationAnalysisCompleted($result, $this->mediaId));

            return;
        }

        if (Str::contains($this->mimeType, 'video')) {
            Recognize::source($this->path, $this->mimeType, $this->mediaId)->detectModeration($this->minConfidence);

            return;
        }

        throw new \Exception('$mimeType does neither indicate being a video nor an image');
    }

    protected function ensureMimeTypeIsSet()
    {
        if (is_null($this->mimeType)) {
            $this->mimeType = Storage::disk(config('media-recognition.disk'))->mimeType($this->path);
        }
    }
}
