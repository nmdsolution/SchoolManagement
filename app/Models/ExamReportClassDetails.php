<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * App\Models\ExamReportClassDetails
 *
 * @property int $id
 * @property int $exam_report_id
 * @property int $student_id
 * @property float|null $total_obtained_points
 * @property int $total_coef
 * @property float|null $avg
 * @property int|null $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExamReport $exam_report
 * @property-read mixed $subject_wise_details
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereExamReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereTotalCoef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereTotalObtainedPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamReportClassDetails extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'exam_report_id',
        'student_id',
        'total_obtained_points',
        'avg',
        'total_avg',
        'rank',
        'subject_wise_details',
        'total_coef'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id')->withTrashed();
    }

    public function getsubjectWiseDetailsAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Get the exam_report that owns the ExamReportClassDetails
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam_report()
    {
        return $this->belongsTo(ExamReport::class);
    }
}
