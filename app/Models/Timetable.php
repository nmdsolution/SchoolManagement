<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Timetable
 *
 * @property int $id
 * @property int $subject_teacher_id
 * @property int $class_section_id
 * @property string $start_time
 * @property string $end_time
 * @property string|null $note
 * @property int $day 1=monday,2=tuesday,3=wednesday,4=thursday,5=friday,6=saturday,7=sunday
 * @property string $day_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read mixed $center_name
 * @property-read \App\Models\SubjectTeacher $subject
 * @property-read \App\Models\SubjectTeacher $subject_teacher
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable query()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereDayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereSubjectTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable withoutTrashed()
 * @mixin \Eloquent
 */
class Timetable extends Model
{
    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    use SoftDeletes;

    public function subject_teacher()
    {
        return $this->belongsTo(SubjectTeacher::class)->with('subject', 'teacher.user:id,first_name,last_name')->withTrashed();
    }

    public function class_section()
    {
        return $this->belongsTo(ClassSection::class)->with('class', 'section');
    }

    public function subject()
    {
        return $this->belongsTo(SubjectTeacher::class, 'subject_teacher_id')->with('subject')->withTrashed();
    }

    public function getCenterNameAttribute()
    {
        $center_id = $this->class_section->class->center_id;
        $center_name = Center::select('name')->find($center_id)->name;
        return $center_name;
    }
}
