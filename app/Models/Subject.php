<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Subject
 *
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string $bg_color
 * @property string $image
 * @property int $medium_id
 * @property string $type Theory / Practical
 * @property int|null $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $department_id
 * @property-read \App\Models\Center|null $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSubject> $classSubject
 * @property-read int|null $class_subject_count
 * @property-read \App\Models\Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamMarks> $exam_marks
 * @property-read int|null $exam_marks_count
 * @property-read \App\Models\Mediums $medium
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubjectTeacher> $subject_teachers
 * @property-read int|null $subject_teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teacher> $teacher
 * @property-read int|null $teacher_count
 * @method static \Illuminate\Database\Eloquent\Builder|Subject activeMediumOnly()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject subjectTeacher()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject withoutTrashed()
 * @mixin \Eloquent
 */
class Subject extends Model {
    use SoftDeletes;
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    protected $fillable = [
        'name',
        'center_id',
        'medium_id',
        'type',
        'code',
        'bg_color',
        'image',
        'department_id',
    ];

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function medium() {
        return $this->belongsTo(Mediums::class)->withTrashed();
    }

    public function scopeSubjectTeacher($query) {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $subjects_ids = $user->teacher->subjects()->pluck('subject_id');
            return $query->whereIn('id', $subjects_ids);
        }
        return $query;
    }

    //Getter Attributes
    public function getImageAttribute($value) {
        return url(Storage::url($value));
    }

    public function center() {
        return $this->belongsTo(Center::class);
    }

    public function classSubject() {
        return $this->hasMany(ClassSubject::class);
    }

    public function subject_teachers(){
        return $this->hasMany(SubjectTeacher::class);
    }

    public function teacher() {
        return $this->belongsToMany(Teacher::class, SubjectTeacher::class, 'subject_id', 'teacher_id')->groupBy('subject_id');
    }

    public function scopeOwner($query) {
        if (Auth::user()->hasRole('Center')) {
            $query->where('center_id', Auth::user()->center->id);
        }

        if (Auth::user()->hasRole('Teacher')) {
            //Find the Subjects in which teacher is assigned
            $subjects = SubjectTeacher::where('teacher_id', Auth::user()->teacher->id)->select('subject_id')->pluck('subject_id');
            $query->whereIn('id', $subjects);
        }
    }

    public function scopeactiveMediumOnly($query) {
        $activeMedium = getCurrentMedium();
        $query->where('medium_id', $activeMedium->id);
    }

    /**
     * Get all of the exam_marks for the Subject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exam_marks() {
        return $this->hasMany(ExamMarks::class);
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }
}
