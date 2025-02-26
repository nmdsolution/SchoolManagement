<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AnnualSubjectReport
 *
 * @property int $id
 * @property int|null $annual_report_id
 * @property int $class_section_id
 * @property int $subject_id
 * @property int $student_id
 * @property int $subject_total
 * @property float $subject_avg
 * @property int $subject_rank
 * @property string $subject_grade
 * @property string $subject_remarks
 * @property string $term_marks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\Students $student
 * @property-read \App\Models\Subject $subject
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereAnnualReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereSubjectAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereSubjectGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereSubjectRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereSubjectRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereSubjectTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereTermMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AnnualSubjectReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'annual_report_id',
        'class_section_id', 
        'subject_id', 
        'student_id', 
        'subject_total',
        "subject_avg",
        'subject_rank',
        'subject_grade',
        'subject_remarks',
        'term_marks'
    ];

    public function getTermMarksAttribute($value)
    {
        return json_decode($value, true);
    }

    public function student() {
        return $this->belongsTo(\App\Models\Students::class);
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function class_section() {
        return $this->belongsTo(ClassSection::class);
    }
}
