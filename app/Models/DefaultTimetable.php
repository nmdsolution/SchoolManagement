<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DefaultTimetable
 *
 * @property int $id
 * @property string $start_time
 * @property string $end_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable query()
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DefaultTimetable extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'start_time',
        'end_time',
    ];
}
