<?php

namespace Meema\MediaRecognition\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FacialAnalysisCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public ?int $mediaId;

    /**
     * Create a new event instance.
     *
     * @param $message
     * @param int|null $mediaId
     */
    public function __construct($message, int $mediaId = null)
    {
        $this->message = $message;
        $this->mediaId = $mediaId;
    }
}
