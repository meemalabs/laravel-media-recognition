<?php

namespace Meema\MediaRecognition\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * MediaConversion Model.
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string $job_id
 * @property array $labels
 * @property array $faces
 * @property array $moderation
 * @property array $ocr
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition query()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereFaces($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereModeration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereOcr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MediaRecognition extends Model
{
    protected $guarded = [];

    protected $casts = [
        'labels' => 'array',
        'faces' => 'array',
        'moderation' => 'array',
        'ocr' => 'array',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
