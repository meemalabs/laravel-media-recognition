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
 * @property array $results
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition query()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaRecognition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MediaRecognition extends Model
{
    protected $guarded = [];

    protected $casts = [
        'results' => 'array',
    ];

    public static function create($results, $modelId)
    {
        $conversion = new MediaRecognition();
        $conversion->model_type = config('media-converter.media_model');
        $conversion->model_id = $modelId;
        $conversion->results = $results;
        $conversion->save();
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
