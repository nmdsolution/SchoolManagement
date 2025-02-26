<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExamReportClassSubject
 *
 * @property int $id
 * @property int $exam_report_id
 * @property int $class_section_id
 * @property int $subject_id
 * @property float $min
 * @property float $max
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereExamReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamReportClassSubject extends Model
{
    use HasFactory;
}
