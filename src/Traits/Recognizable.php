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
        $recognition = $this->recognition()->first();

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

        if ($recognition->faces && is_array($recognition->faces['Faces'])) {
            $faces = $recognition->faces['Faces'];
        }

        if ($recognition->moderation && is_array($recognition->moderation['ModerationLabels'])) {
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

    public function minimalRecognitionData(
        bool $includeLabels = true,
        bool $includeFaces = true,
        bool $includeModeration = true,
        bool $includeTexts = true,
        int $limit = 50
    ) {
        $data = $this->recognitionData();

        if (! $data) {
            return [
                'labels' => null,
                'faces' => null,
                'moderation' => null,
                'texts' => null,
            ];
        }

        $array = [];

        if ($includeLabels) {
            $array['labels'] = collect($data['labels'])->map(function ($label) {
                return [
                    'name' => $label['Label']['Name'],
                    'confidence' => $label['Label']['Confidence'],
                    'timestamp' => $label['Timestamp'],
                ];
            })->unique('name')->take($limit)->sortByDesc('confidence')->values();
        }

        if ($includeFaces) {
            $array['faces'] = collect($data['faces'])->map(function ($face) {
                return [
                    'bounding_box' => $face['Face']['BoundingBox'],
                    'confidence' => $face['Face']['Confidence'],
                    'timestamp' => $face['Timestamp'],
                ];
            })->take($limit)->sortByDesc('confidence')->values();
        }

        if ($includeModeration) {
            $array['moderation'] = collect($data['moderation'])->map(function ($label) {
                return [
                    'name' => $label['ModerationLabel']['Name'],
                    'confidence' => $label['ModerationLabel']['Confidence'],
                    'timestamp' => $label['Timestamp'],
                ];
            })->unique('name')->take($limit)->sortByDesc('confidence')->values();
        }

        if ($includeTexts) {
            $array['texts'] = collect($data['texts'])->map(function ($text) {
                return [
                    'text' => $text['TextDetection']['DetectedText'],
                    'confidence' => $text['TextDetection']['Confidence'],
                    'timestamp' => $text['Timestamp'],
                ];
            })->unique('text')->take($limit)->sortByDesc('confidence')->values();
        }

        return $array;
    }
}
