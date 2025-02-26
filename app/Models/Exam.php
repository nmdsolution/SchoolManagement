<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Exam
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $type 1 = Sequential Exam, 2 = Specific Exam
 * @property int $session_year_id
 * @property int|null $exam_term_id
 * @property int $center_id
 * @property int|null $exam_sequence_id
 * @property int $teacher_status
 * @property int $student_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Center $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSection> $class_section
 * @property-read int|null $class_section_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamClassSection> $exam_class_section
 * @property-read int|null $exam_class_section_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamStatistics> $exam_statistics
 * @property-read int|null $exam_statistics_count
 * @property-read mixed $class_name
 * @property-read mixed $date_between
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamMarks> $marks
 * @property-read int|null $marks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamResult> $results
 * @property-read int|null $results_count
 * @property-read \App\Models\ExamSequence|null $sequence
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\ExamTerm|null $term
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamTimetable> $timetable
 * @property-read int|null $timetable_count
 * @method static \Illuminate\Database\Eloquent\Builder|Exam currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam query()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereExamSequenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereStudentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereTeacherStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Exam extends Model {
    use HasFactory;

    protected $hidden = [
        "deleted_at",
        "created_at",
        "updated_at"
    ];

    protected $fillable = [
        'name',
        'description',
        'type',
        'session_year_id',
        'exam_term_id',
        'center_id',
        'exam_sequence_id',
        'teacher_status',
        'student_status',
        'total_marks'
    ];

    public function exam_class_section() {
        return $this->hasMany(ExamClassSection::class);
    }


    public function class_section() {
        return $this->belongsToMany(ClassSection::class, ExamClassSection::class, 'exam_id')->with('class', 'section');
    }

    public function session_year() {
        return $this->belongsTo(SessionYear::class);
    }

    public function marks() {
        return $this->hasManyThrough(ExamMarks::class, ExamTimetable::class, 'exam_id', 'exam_timetable_id')->orderBy('date', 'asc');
    }

    public function timetable() {
        return $this->hasMany(ExamTimetable::class);
    }

    public function results() {
        return $this->hasMany(ExamResult::class, 'exam_id');
    }

    public function term() {
        return $this->belongsTo(ExamTerm::class, 'exam_term_id');
    }

    public function sequence() {
        return $this->belongsTo(ExamSequence::class, 'exam_sequence_id');
    }

    public function scopeCurrentSessionYear($query) {
        return $query->where('session_year_id', getSettings('session_year')['session_year']);
    }

    public function scopeOwner($query) {
        if (Auth::user()->hasRole('Teacher')) {
            // Display only records of the exam in which Subject Teacher is involved
            $teacher = Auth::user()->teacher()->select('id')->first();
            $subject_teacher = SubjectTeacher::where('teacher_id', $teacher->id);
            $class_section_ids = $subject_teacher->pluck('class_section_id');
            $subject_ids = $subject_teacher->pluck('subject_id');
            return $query->whereHas('timetable', function ($q) use ($class_section_ids, $subject_ids) {
                $q->whereIn('class_section_id', $class_section_ids)->whereIn('subject_id', $subject_ids);
            })->where('teacher_status', 1);
            //Subject wise filter is disabled currently
            //            $subject_ids = array_column($subject_teacher->toArray(), 'subject_id');
            //            return $query->whereHas('timetable', function ($q) use ($subject_ids, $class_section_ids) {
            //                $q->whereIn('subject_id', $subject_ids)->whereIn('class_section_id', $class_section_ids);
            //            });
        }

        if (Auth::user()->hasRole('Center')) {
            return $query->where('center_id', Auth::user()->center->id);
        }

        if (Auth::user()->hasRole('Parent') || Auth::user()->hasRole('Student')) {
            return $query->where('student_status', 1);
        }

        return $query;
    }

    /**
     * Get all the report_statistics for the Exam
     *
     * @return HasMany
     */
    public function exam_statistics() {
        return $this->hasMany(ExamStatistics::class);
    }

    public function center() {
        return $this->belongsTo(Center::class);
    }

    public function getClassNameAttribute() {
        $class_name = array();
        foreach ($this->exam_class_section as $exam_class_section) {
            $class_name[] = $exam_class_section->class_section->full_name;
        }
        return implode(', ', $class_name);
    }

    public function getDateBetweenAttribute() {
        $date['min_date'] = ExamTimetable::where('exam_id', $this->id)->min('date');
        $date['max_date'] = ExamTimetable::where('exam_id', $this->id)->max('date');
        return $date;
    }

}
