<?php

namespace App\Models;

use App\Models\Competency\Competency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\ExamMarks
 *
 * @property int $id
 * @property int $exam_timetable_id
 * @property int $student_id
 * @property int $subject_id
 * @property float $obtained_marks
 * @property string|null $teacher_review
 * @property int $passing_status 1=Pass, 0=Fail
 * @property int $session_year_id
 * @property string|null $grade
 * @property int|null $exam_result_group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Competency $competency
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exam> $exam
 * @property-read int|null $exam_count
 * @property-read \App\Models\ExamSequence $sequence
 * @property-read \App\Models\Students $student
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\ExamTerm $term
 * @property-read \App\Models\ExamTimetable $timetable
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereExamResultGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereExamTimetableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereObtainedMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks wherePassingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereTeacherReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamMarks extends Model {
    use HasFactory;

    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $fillable = ['obtained_marks', 'passing_status', 'grade', 'exam_timetable_id', 'student_id', 'subject_id', 'session_year_id'];

//    protected $appends = ['total_marks'];

    public function timetable() {
        return $this->belongsTo(ExamTimetable::class, 'exam_timetable_id');
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function student() {
        return $this->belongsTo(Students::class);
    }

    public function exam() {
        return $this->hasManyThrough(Exam::class, ExamTimetable::class, 'id', 'id', 'exam_timetable_id', 'exam_id');
    }

    public function scopeOwner() {
        return $this->whereHas('exam', function ($q) {
            $q->owner();
        });
    }

    public function scopeCurrentSessionYear($query) {
        return $query->where('session_year_id', getSettings('session_year')['session_year']);
    }

    // // Working demo
    // public function results(){
    //     return $this->hasManyThrough(ExamMarks::class,ExamTimetable::class,'exam_id','exam_timetable_id')->orderBy('date','asc');
    // }

//    public function getTotalMarksAttribute($query)
//    {
//        return ExamTimetable::find($this->exam_timetable_id)->total_marks;
//    }

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function term()
    {
        return $this->belongsTo(ExamTerm::class, 'exam_term_id');
    }

    public function sequence()
    {
        return $this->belongsTo(ExamSequence::class, 'exam_sequence_id');
    }
}
