<?php

namespace Meema\MediaRecognition\Traits;

use Illuminate\Support\Facades\Log;
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
            return [
                'labels' => null,
                'faces' => null,
                'moderation' => null,
                'texts' => null,
            ];
        }

        // null indicates that the "recognition" has not been ran for the category
        $labels = $faces = $moderation = $texts = null;

        if ($recognition->labels && is_array($recognition->labels['Labels'])) {
            $labels = $recognition->labels['Labels'];
        }

        if ($recognition->faces && is_array($recognition->faces['FaceDetails'])) {
            $faces = $recognition->faces['FaceDetails'];
        }

        Log::info('before moderation');
        if ($recognition->moderation && is_array($recognition->moderation['ModerationLabels'])) {
            Log::info('after', $recognition->moderation['ModerationLabels']);
            $moderation = $recognition->moderation['ModerationLabels'];
        }

        if ($recognition->ocr && is_array($recognition->ocr['TextDetections'])) {
            $texts = $recognition->ocr['TextDetections'];
        }

        return [
            'labels' => $labels,
            'faces' => $faces,
            'moderation' => $moderation,
            'texts' => $texts,
        ];
    }
}
