<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExamReport
 *
 * @property int $id
 * @property int $class_section_id
 * @property int|null $class_teacher_id user_id
 * @property int $exam_term_id
 * @property int $session_year_id
 * @property float $avg
 * @property int $male_students
 * @property int $female_students
 * @property int $total_students
 * @property int $total_coef
 * @property int $total_points
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamReportClassDetails> $exam_report_class_detail
 * @property-read int|null $exam_report_class_detail_count
 * @property-read \App\Models\ExamTerm $exam_term
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamReportClassDetails> $last_student
 * @property-read int|null $last_student_count
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $student
 * @property-read int|null $student_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamReportClassDetails> $top_student
 * @property-read int|null $top_student_count
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereClassTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereFemaleStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereMaleStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereTotalCoef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereTotalPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereTotalStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_section_id',
        'exam_term_id',
        'session_year_id',
        'class_teacher_id',
        'male_students',
        'female_students',
        'total_students',
        'avg',
        'total_coef',
        'total_points'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the class_section that owns the ExamReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class_section()
    {
        return $this->belongsTo(ClassSection::class);
    }

    /**
     * Get the exam_term that owns the ExamReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam_term()
    {
        return $this->belongsTo(ExamTerm::class);
    }

    /**
     * Get the session_year that owns the ExamReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function session_year()
    {
        return $this->belongsTo(SessionYear::class);
    }

    /**
     * Get all of the exam_report_class_detail for the ExamReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exam_report_class_detail()
    {
        return $this->hasMany(ExamReportClassDetails::class);   
    }

    public function top_student()
    {
        return $this->hasMany(ExamReportClassDetails::class);
    }

    public function last_student()
    {
        return $this->hasMany(ExamReportClassDetails::class);
    }

    /**
     * Get all of the attendance for the ExamReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'class_section_id', 'class_section_id');
    }

    /**
     * Get all of the student for the ExamReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function student()
    {
        return $this->hasMany(Students::class, 'class_section_id', 'class_section_id');
    }

    public function getAttendanceAttribute()
    {
        $data = array();
        // Male student attendance
        $male_student = 1;
        $female_student = 1;
        $total_day = 1;
        if ($this->total_male_student) {
            $male_student = $this->total_male_student;
        }
        if ($this->total_female_student) {
            $female_student = $this->total_female_student;
        }
        if ($this->total_days) {
            $total_day = $this->total_days;
        }

        $data['male_presents'] = number_format(($this->total_male_present * 100) / ($male_student * $total_day), 2);
        
        // Female student attendance
        $data['female_presents'] = number_format(($this->total_female_present * 100) / ($female_student * $total_day), 2);

        // Overall attendance
        $data['overall_attendance'] = number_format((($this->total_female_present + $this->total_male_present) * 100) / (($female_student + $male_student) * $total_day), 2);


        return (object) $data;
    }

}
