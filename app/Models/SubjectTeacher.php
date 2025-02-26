<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\SubjectTeacher
 *
 * @property int $id
 * @property int $class_section_id
 * @property int $subject_id
 * @property int $teacher_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassSection $class
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\ClassSection $section
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\Teacher $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher owner($class_section_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher subjectTeacher($class_section_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher withoutTrashed()
 * @mixin \Eloquent
 */
class SubjectTeacher extends Model
{
    use SoftDeletes;

    protected $hidden = [
        "deleted_at",
        "created_at",
        "updated_at"
    ];

    public function class_section()
    {
        return $this->belongsTo(ClassSection::class)->with('class', 'section')->withTrashed();
    }

    public function class()
    {
        return $this->belongsTo(ClassSection::class)->with('class')->withTrashed();
    }

    public function section()
    {
        return $this->belongsTo(ClassSection::class)->with('section')->withTrashed();
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class)->with('user')->withTrashed();
    }

    public function scopeSubjectTeacher($query, $class_section_id = null)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacher_id = $user->teacher()->pluck('id');
            return $query->whereIn('teacher_id', $teacher_id);
        }
        return $query;
    }

    public function scopeOwner($query, $class_section_id = null)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacher_id = $user->teacher()->pluck('id');
            return $query->whereIn('teacher_id', $teacher_id);
        }
        if ($user->hasRole('Center')) {
            $class_section_id = ClassSection::owner()->select('id')->pluck('id');
            return $query->whereIn('class_section_id', $class_section_id);
        }
        return $query;
    }
}