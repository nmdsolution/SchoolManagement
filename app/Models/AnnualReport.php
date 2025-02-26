<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AnnualReport
 *
 * @property int $id
 * @property int $class_section_id
 * @property int|null $class_teacher_id user_id
 * @property int $session_year_id
 * @property int $total_students
 * @property int $male_students
 * @property int $female_students
 * @property float $avg
 * @property int $total_coef
 * @property int $total_points
 * @property string $term_report_ids
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnnualClassDetails> $annual_report_class_detail
 * @property-read int|null $annual_report_class_detail_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnnualClassDetails> $last_student
 * @property-read int|null $last_student_count
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $student
 * @property-read int|null $student_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnnualClassDetails> $top_student
 * @property-read int|null $top_student_count
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereClassTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereFemaleStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereMaleStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereTermReportIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereTotalCoef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereTotalPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereTotalStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AnnualReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_section_id',
        'session_year_id',
        'class_teacher_id',
        'male_students',
        'female_students',
        'total_students',
        'avg',
        'total_coef',
        'total_points',
        'term_report_ids'
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
    public function annual_report_class_detail()
    {
        return $this->hasMany(AnnualClassDetails::class);   
    }

    public function top_student()
    {
        return $this->hasMany(AnnualClassDetails::class);
    }

    public function last_student()
    {
        return $this->hasMany(AnnualClassDetails::class);
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

    public function getTermReportIdsAttribute($value){
        return json_decode($value);
    }
}
