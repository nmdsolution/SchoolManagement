<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExamReportStudentSubject
 *
 * @property int $id
 * @property int $exam_report_id
 * @property int $student_id
 * @property int $subject_id
 * @property int $subject_total
 * @property int $subject_rank
 * @property float $subject_avg
 * @property string|null $subject_grade
 * @property string|null $subject_remarks
 * @property string|null $sequence_marks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExamReport $examReport
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereExamReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSequenceMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSubjectAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSubjectGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSubjectRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSubjectRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSubjectTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamReportStudentSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_report_id',
        'student_id',
        'subject_id',
        'subject_total',
        'subject_rank',
        'subject_avg',
        'subject_grade',
        'subject_remarks',
        'sequence_marks'
    ];

    // Displays student performance in a subject and for a class and term and displays the following information in the fillable above.

    public function getSequenceMarksAttribute($value)
    {
        return json_decode($value);
    }

    public function examReport() {
        return $this->belongsTo(ExamReport::class);
    }
}
