<?php

namespace Meema\MediaRecognition\Traits;

use Meema\MediaRecognition\Facades\Recognize;
use Meema\MediaRecognition\Models\MediaRecognition;

trait Recognizable
{
    /**
     * Get all of the media items' conversions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function recognition(): \Illuminate\Database\Eloquent\Relations\MorphOne
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
     * Please note, the "Facial Rekognition" response is different from a "video" to an "image".
     *
     * @return array
     */
    public function recognitionData(): array
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

        if ($recognition->faces && is_array($recognition->faces['FaceDetails'])) {
            $faces = $recognition->faces['FaceDetails'];
        } elseif ($recognition->faces && is_array($recognition->faces['Faces'])) {
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
    ): array {
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
                return $this->generateLabelElement($label);
            })->unique('name')->sortByDesc('confidence')->take($limit)->values()->toArray();
        }

        if ($includeFaces) {
            $array['faces'] = collect($data['faces'])->map(function ($face) {
                return $this->generateFaceElement($face);
            })->sortByDesc('confidence')->take($limit)->values()->toArray();
        }

        if ($includeModeration) {
            $array['moderation'] = collect($data['moderation'])->map(function ($moderation) {
                return $this->generateModerationElement($moderation);
            })->unique('name')->sortByDesc('confidence')->take($limit)->values()->toArray();
        }

        if ($includeTexts) {
            $array['texts'] = collect($data['texts'])->map(function ($text) {
                return $this->generateTextElement($text);
            })->unique('text')->sortByDesc('confidence')->take($limit)->values()->toArray();
        }

        return $array;
    }

    public function generateLabelElement($label): array
    {
        // image element
        if ($label['Name']) {
            return [
                'name' => $label['Name'],
                'confidence' => $label['Confidence'],
                'timestamp' => null, // timestamps are only available in videos
            ];
        }

        // video element
        return [
            'name' => $label['Label']['Name'],
            'confidence' => $label['Label']['Confidence'],
            'timestamp' => $label['Timestamp'],
        ];
    }

    public function generateFaceElement($face): array
    {
        // image element
        if ($face['BoundingBox']) {
            return [
                'bounding_box' => $face['BoundingBox'],
                'confidence' => $face['Confidence'],
                'timestamp' => null, // timestamps are only available in videos
            ];
        }

        // video element
        return [
            'bounding_box' => $face['Face']['BoundingBox'],
            'confidence' => $face['Face']['Confidence'],
            'timestamp' => $face['Timestamp'],
        ];
    }

    public function generateModerationElement($moderation): array
    {
        // image element
        if ($moderation['Name']) {
            return [
                'name' => $moderation['Name'],
                'confidence' => $moderation['Confidence'],
                'timestamp' => null, // timestamps are only available in videos
            ];
        }

        // video element
        return [
            'name' => $moderation['ModerationLabel']['Name'],
            'confidence' => $moderation['ModerationLabel']['Confidence'],
            'timestamp' => $moderation['Timestamp'],
        ];
    }

    public function generateTextElement($text): array
    {
        // image element
        if ($text['DetectedText']) {
            return [
                'text' => $text['DetectedText'],
                'confidence' => $text['Confidence'],
                'timestamp' => null, // timestamps are only available in videos
            ];
        }

        // video element
        return [
            'text' => $text['TextDetection']['DetectedText'],
            'confidence' => $text['TextDetection']['Confidence'],
            'timestamp' => $text['Timestamp'],
        ];
    }
}
