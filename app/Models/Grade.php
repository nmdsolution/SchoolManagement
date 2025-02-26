<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Grade
 *
 * @property int $id
 * @property int $starting_range
 * @property int $ending_range
 * @property string $grade
 * @property int|null $center_id
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $medium_id
 * @method static \Illuminate\Database\Eloquent\Builder|Grade currentMedium()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereEndingRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereStartingRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'center_id',
        'medium_id',
        'starting_range',
        'ending_range',
        'grade',
        'remarks',
    ];

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Center')) {
            $query->where('center_id', Auth::user()->center->id);
        }

        if (Auth::user()->hasRole('Teacher')) {
            $center_ids = CenterTeacher::where('teacher_id', Auth::user()->teacher->id)->select('center_id')->pluck('center_id');
            $query->whereIn('center_id', $center_ids);
        }

        if (Auth::user()->hasRole('Student')) {
            $query->where('center_id', Auth::user()->student->center_id);
        }

//TODO : cannot fetch data here from multiple centers
//        if (Auth::user()->hasRole('Parent')) {
//            $query->whereIn('center_id', Auth::user()->parent->children->pluck('center_id'));
//        }
    }

    public function scopeCurrentMedium($query){
        $query->where('medium_id', getCurrentMedium()->id);
    }
}
