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
     * The return value "null" indicates that a recognition has been ran, but it just has no results.
     *
     * @return array
     */
    public function recognitionData()
    {
        $recognition = $this->recognition()->latest()->first();

        if (! $recognition) {
            return [];
        }

        return [
            'labels' => count($recognition->labels['Labels']) ? $recognition->labels['Labels'] : null,
            'faces' => count($recognition->faces['FaceDetails']) ? $recognition->faces['FaceDetails'] : null,
            'moderation' => count($recognition->moderation['ModerationLabels']) ? $recognition->moderation['ModerationLabels'] : null,
            'texts' => count($recognition->ocr['TextDetections']) ? $recognition->ocr['TextDetections'] : null,
        ];
    }
}
