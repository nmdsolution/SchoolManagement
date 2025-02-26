<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\ClassSection
 *
 * @property int $id
 * @property int $class_id
 * @property int $section_id
 * @property int|null $class_teacher_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $absent_attendance
 * @property-read int|null $absent_attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcement
 * @property-read int|null $announcement_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\ClassSchool $class
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSubject> $class_subjects
 * @property-read int|null $class_subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamResult> $exam_result
 * @property-read int|null $exam_result_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamStatistics> $exam_statistic
 * @property-read int|null $exam_statistic_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamStatistics> $exam_statistics
 * @property-read int|null $exam_statistics_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamTimetable> $exam_timetable
 * @property-read int|null $exam_timetable_count
 * @property-read mixed $full_name
 * @property-read mixed $name
 * @property-read \App\Models\Section $section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $student
 * @property-read int|null $student_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubjectTeacher> $subject_teachers
 * @property-read int|null $subject_teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $subjects
 * @property-read int|null $subjects_count
 * @property-read \App\Models\Teacher|null $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection classTeacher()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection owner($center_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection subjectTeacher()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereClassTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection withoutTrashed()
 * @mixin \Eloquent
 */
class ClassSection extends Model {
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'class_id',
        'section_id',
    ];

    protected $hidden = [
        "deleted_at",
        "created_at",
        "updated_at"
    ];

    protected $appends = ['full_name'];
    public function class() {
        return $this->belongsTo(ClassSchool::class)->withTrashed();
    }

    public function section() {
        return $this->belongsTo(Section::class)->withTrashed();
    }

    public function teacher() {
        return $this->belongsTo(Teacher::class, 'class_teacher_id', 'id')->with('user')->withTrashed();
    }

    public function announcement() {
        return $this->morphMany(Announcement::class, 'table');
    }

    public function subject_teachers(): HasMany {
        return $this->hasMany(SubjectTeacher::class);
    }

    /**
     * Get all the exam_timetable for the ClassSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exam_timetable() {
        return $this->hasMany(ExamTimetable::class);
    }

    /**
     * Get all the student for the ClassSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function student() {
        return $this->hasMany(Students::class);
    }

    /**
     * Get all the exam_statistic for the ClassSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exam_statistic() {
        return $this->hasMany(ExamStatistics::class);
    }

    /**
     * Get all the exam_result for the ClassSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exam_result() {
        return $this->hasMany(ExamResult::class);
    }

    public function exam_statistics() {
        return $this->hasMany(ExamStatistics::class);
    }

    /**
     * Get all the attendance for the ClassSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendance() {
        return $this->hasMany(Attendance::class);
    }

    public function absent_attendance() {
        return $this->hasMany(Attendance::class);
    }

    public function subjects() {
        return $this->hasManyThrough(Subject::class, ClassSubject::class, 'class_id', 'id', 'class_id', 'subject_id');
    }

    public function class_subjects() {
        return $this->hasMany(ClassSubject::class, 'class_id', 'class_id');
    }

    public function scopeClassTeacher($query) {
        if (Auth::user()->hasRole('Center')) {
            $query->whereHas('class', function ($query) {
                $query->where('center_id', Auth::user()->center->id);
            });
        }

        if (Auth::user()->hasRole('Teacher')) {
            $teacher = Auth::user()->teacher;
            return $query->where('class_teacher_id', $teacher->id);
        }

        return $query;
    }

    public function scopeSubjectTeacher($query) {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $class_section_ids = $user->teacher->subjects()->pluck('class_section_id');
            return $query->whereIn('id', $class_section_ids);
        }
        return $query;
    }

    public function scopeOwner($query, $center_id = null) {
        if (Auth::user()->hasRole('Center')) {
            $query->whereHas('class', function ($query) {
                $query->where('center_id', Auth::user()->center->id);
            });
        }

        if (Auth::user()->hasRole('Teacher')) {
            if (Auth::user()->teacher) {
                $center_id = $center_id ?? get_center_id();
                $class_section = SubjectTeacher::where('teacher_id', Auth::user()->teacher->id)->select('class_section_id')->pluck('class_section_id');
                $query->whereIn('id', $class_section);
                if ($center_id) {
                    $query->whereHas('class', function ($query) use ($center_id) {
                        $query->where('center_id', $center_id);
                    });
                }
            } else if (Auth::user()->staff->first()) {
                $query->whereHas('class', function ($query) use ($center_id) {
                    $query->where('center_id', Session()->get('center_id'));
                });
                return $query;
            }
        } else if (Auth::user()->staff->first()) {
            $query->whereHas('class', function ($query) use ($center_id) {
                $query->where('center_id', Session()->get('center_id'));
            });
            return $query;
        }
        return $query;
    }

    public function getNameAttribute() {
        $name = "";

        $name .= $this->class->name;

        $name .= ' ' . $this->section->name;

        return $name;
    }


    public function getFullNameAttribute() {
        $name = "";
        if ($this->relationLoaded('class')) {
            $name .= $this->class->name;
        }
        if ($this->relationLoaded('section')) {
            $name .= ' ' . $this->section->name;
        }

        if ($this->relationLoaded('class') && $this->class->relationLoaded('stream')) {
            $name .= isset($this->class->stream->name) ? ' - ' . $this->class->stream->name : '';
        }

        return $name;
    }

}
