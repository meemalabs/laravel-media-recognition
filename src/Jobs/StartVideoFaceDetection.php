<?php

namespace Meema\MediaRecognition\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Meema\MediaRecognition\Facades\Recognize;

class StartVideoFaceDetection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?int $mediaId;

    private string $faceAttribute;

    /**
     * Create a new job instance.
     *
     * @param int|null $mediaId
     * @param string $faceAttribute
     */
    public function __construct($mediaId = null, $faceAttribute = 'DEFAULT')
    {
        $this->mediaId = $mediaId;
        $this->faceAttribute = $faceAttribute;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Recognize::startFaceDetection($this->mediaId, $this->faceAttribute);
    }
}
