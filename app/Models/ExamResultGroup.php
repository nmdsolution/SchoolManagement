<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\ExamResultGroup
 *
 * @property int $id
 * @property string $name
 * @property int $center_id
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $subjects
 * @property-read int|null $subjects_count
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup withoutTrashed()
 * @mixin \Eloquent
 */
class ExamResultGroup extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Compoships;

    protected $fillable = [
        'name',
        'position',
        'center_id',
    ];

    public function subjects()
    {
        // IF you don't provide the class id it will give inaccurate results with other class data also
        return $this->belongsToMany(Subject::class, ExamResultGroupSubject::class)->withPivot('class_id', 'subject_id');
    }

    public function examResultGroupSubject($value = null)
    {
        // IF you don't provide the class id it will give inaccurate results with other class data also
        return $this->hasMany(ExamResultGroupSubject::class, 'exam_result_group_id');
    }

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Center')) {
            $query = $query->where('center_id', Auth::user()->center->id);
        }
        return $query;
    }
}
