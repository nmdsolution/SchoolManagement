<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExamReportStudentSequence
 *
 * @property int $id
 * @property int $class_section_id
 * @property int $student_id
 * @property int $exam_term_id
 * @property int $exam_sequence_id
 * @property float $total
 * @property int $total_coef
 * @property float $avg
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExamTerm $examTerm
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereExamSequenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereTotalCoef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamReportStudentSequence extends Model
{
    use HasFactory;


    // displays student performance from his class_section , the sequence, the average rank and total cofficient

    // the data here is used to determine what the student average and other metrices for a particular sequence.

    public function examTerm()
    {
        return $this->belongsTo(ExamTerm::class);
    }
    
    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id')->withTrashed();
    }
}
