<?php

namespace Meema\MediaRecognition\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Meema\MediaRecognition\Events\FacialAnalysisCompleted;
use Meema\MediaRecognition\Facades\Recognize;

class StartFaceDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $path;

    /**
     * $mediaType may be 'image' or 'video' and it allows us to reduce an HTTP request to check for the mime type.
     * If not assigned, it will check the mime type for whether it is an image or a video source.
     *
     * @var string|null
     */
    private ?string $mediaType;

    private ?int $mediaId;

    private array $faceAttribute;

    /**
     * Create a new job instance.
     *
     * @param string $path
     * @param string|null $mediaType
     * @param int|null $mediaId
     * @param string $faceAttribute
     */
    public function __construct(string $path, $mediaType = null, $mediaId = null, $faceAttribute = 'DEFAULT')
    {
        $this->path = $path;
        $this->mediaId = $mediaId;
        $this->faceAttribute = [$faceAttribute];
        $this->mediaType = $mediaType;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        if (is_null($this->mediaType)) {
            $mimeType = Storage::disk(config('media-recognition.disk'))->mimeType($this->path);
        } else {
            $mimeType = $this->mediaType;
        }

        if (Str::contains($mimeType, 'image')) {
            $result = Recognize::source($this->path)->detectFaces($this->mediaId, $this->faceAttribute);

            // we need to manually fire the event for image analyses because unlike the video analysis, AWS is not sending a webhook upon completion
            event(new FacialAnalysisCompleted($result));

            return;
        }

        if (Str::contains($mimeType, 'video')) {
            Recognize::source($this->path)->detectFaces($this->mediaId, $this->faceAttribute);

            return;
        }

        throw new \Exception('$mimeType does neither indicate being a video nor an image');
    }
}
