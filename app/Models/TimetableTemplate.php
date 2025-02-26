<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TimetableTemplate
 *
 * @property int $id
 * @property array $periods
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Center|null $center
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate wherePeriods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TimetableTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['center_id' ,'periods'];

    protected $casts = [
        'periods' => 'array',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'center_id'
    ];

    public function center() {
        return $this->hasOne(Center::class);
    }
}
