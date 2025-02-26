<?php

namespace App\Models\Competency;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Competency\CouncilReview
 *
 * @property int $id
 * @property string|null $review
 * @property int $competency_observation_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CouncilReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouncilReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CouncilReview query()
 * @method static \Illuminate\Database\Eloquent\Builder|CouncilReview whereCompetencyObservationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouncilReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouncilReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouncilReview whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CouncilReview whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CouncilReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'competency_observation_id',
        'review'
    ];
}
