<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Attendance
 *
 * @property int $id
 * @property int $class_section_id
 * @property int $student_id
 * @property int $session_year_id
 * @property int $type 0=Absent, 1=Present
 * @property string $date
 * @property string $remark
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance withoutTrashed()
 * @mixin \Eloquent
 */
class Attendance extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $hidden = ["remark","deleted_at","created_at","updated_at"];

    public function student()
    {
        return $this->belongsTo(Students::class)->with('user');
    }

    public function scopeCurrentSessionYear($query) {
        return $query->where('session_year_id', getSettings('session_year')['session_year']);
    }
}
