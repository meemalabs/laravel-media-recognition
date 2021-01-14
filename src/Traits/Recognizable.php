<?php

namespace Meema\MediaRecognition\Traits;

use Meema\MediaRecognition\Facades\Recognize;
use Meema\MediaRecognition\Models\MediaRecognition;

trait Recognizable
{
    /**
     * Get all of the media items' conversions.
     */
    public function recognition()
    {
        return $this->morphOne(MediaRecognition::class, 'model');
    }

    /**
     * Start a media "recognition".
     *
     * @param string $path
     * @param string|null $mimeType
     * @return \Meema\MediaRecognition\Contracts\MediaRecognition
     */
    public function recognize(string $path, string $mimeType = null)
    {
        return Recognize::source($path, $mimeType, $this->id);
    }

    /**
     * Return all recognition data.
     *
     * @return array
     */
    public function recognitionData()
    {
        $recognition = $this->recognition()->first();

        return [
            'labels' => $recognition->labels['Labels'] ?? [],
            'faces' => $recognition->faces['FaceDetails'] ?? [],
            'moderation' => $recognition->moderation['ModerationLabels'] ?? [],
            'texts' => $recognition->ocr['TextDetections'] ?? [],
        ];
    }
}
