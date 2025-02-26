<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use WpOrg\Requests\Auth;

/**
 * App\Models\ExamTimetable
 *
 * @property int $id
 * @property int $exam_id
 * @property int|null $class_id
 * @property int $subject_id
 * @property int $total_marks
 * @property int $passing_marks
 * @property string $date
 * @property string $start_time
 * @property string $end_time
 * @property int $session_year_id
 * @property int $marks_upload_status 0 = Pending , 1 = Submitted , 2 = In progress
 * @property int|null $class_section_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassSection|null $class_section
 * @property-read \App\Models\Exam $exam
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamMarks> $exam_marks
 * @property-read int|null $exam_marks_count
 * @property-read mixed $pendding_subject_marks
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\Subject $subject
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable checkIfSlotAvailable($class_section, $date, $start_time, $end_time, $update_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereMarksUploadStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable wherePassingMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereTotalMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamTimetable extends Model {
    use HasFactory;
    use Compoships;

    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $appends = ["pendding_subject_marks"];
    protected $fillable = [
        'exam_id',
        'subject_id',
        'class_section_id',
        'total_marks',
        'passing_marks',
        'date',
        'start_time',
        'end_time',
        'session_year_id',
        'marks_upload_status'
    ];

    public function subject() {
        return $this->belongsTo(Subject::class, 'subject_id')->withTrashed();
    }

    public function exam() {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function class_section() {
        return $this->belongsTo(ClassSection::class, 'class_section_id');
    }

    public function session_year() {
        return $this->belongsTo(SessionYear::class, 'session_year_id');
    }

    public function exam_marks() {
        return $this->hasMany(ExamMarks::class, 'exam_timetable_id');
    }

    public function scopeCheckIfSlotAvailable($query, $class_section, $date, $start_time, $end_time, $update_id = NULL) {
        $date = date('Y-m-d', strtotime($date));
        $start_time = date("H:i:s", strtotime('+1 minutes', strtotime($start_time)));
        $end_time = date("H:i:s", strtotime('-1 minutes', strtotime($end_time)));
        $query->where(['class_section_id' => $class_section, 'date' => $date])->where(function ($q) use ($end_time, $start_time) {
            $q->whereBetween('start_time', [$start_time, $end_time])
                ->orWhereBetween('end_time', [$start_time, $end_time])
                ->orWhere(function ($q) use ($start_time, $end_time) {
                    $q->where('start_time', '<=', $start_time)->where('end_time', '>=', $end_time);
                });
        });
        if (!empty($update_id)) {
            $query->where('id', '!=', $update_id);
        }
    }

    public function getPenddingSubjectMarksAttribute() {
        $exam_timetable = self::where('exam_id', $this->exam_id)->whereIn('marks_upload_status', [0, 2])->where('class_section_id', $this->class_section_id)->get()->pluck('subject.name')->toArray();
        return implode(', ', $exam_timetable);
    }
}
