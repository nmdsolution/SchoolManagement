<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\ClassSubject
 *
 * @property int $id
 * @property int $class_id
 * @property string $type Compulsory / Elective
 * @property int $subject_id
 * @property int $weightage
 * @property int|null $elective_subject_group_id if type=Elective
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\ElectiveSubjectGroup|null $subjectGroup
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject subjectTeacher($class_section_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereElectiveSubjectGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereWeightage($value)
 * @mixin \Eloquent
 */
class ClassSubject extends Model
{
    use HasFactory;
    use Compoships;

    protected $fillable = [
        'class_id',
        'type',
        'subject_id',
        'weightage',
        'elective_subject_group_id',
    ];

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function class()
    {
        return $this->belongsTo(ClassSchool::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class)->where('deleted_at', null);
    }

    public function subjectGroup()
    {
        return $this->belongsTo(ElectiveSubjectGroup::class, 'elective_subject_group_id');
    }

    public function scopeSubjectTeacher($query, $class_section_id = null)
    {
        //Kind of Owner scope
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            if ($class_section_id) {
                $subjects_ids = $user->teacher->subjects()->where('class_section_id', $class_section_id)->pluck('subject_id');
            } else {
                $subjects_ids = $user->teacher->subjects()->pluck('subject_id');
            }
            return $query->whereIn('subject_id', $subjects_ids);
        }

        if ($user->hasRole('Center')) {
            if ($class_section_id) {
                $class_id = ClassSection::where('id', $class_section_id)->pluck('class_id');
                $query->whereHas('class', function ($q) use ($class_id) {
                    $q->whereIn('class_id', $class_id);
                });
            } else {
                $query->whereHas('class', function ($q) {
                    $q->owner();
                });
            }

        }
        return $query;
    }
}
