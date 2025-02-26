<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExamStatistics
 *
 * @property int $id
 * @property int|null $exam_id
 * @property int|null $class_section_id
 * @property int|null $total_student
 * @property int|null $total_attempt_student
 * @property int|null $pass
 * @property int|null $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSection|null $class_section
 * @property-read \App\Models\Exam|null $exam
 * @property-read mixed $percentage
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics wherePass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereTotalAttemptStudent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereTotalStudent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamStatistics extends Model
{
    use HasFactory;
    protected $hidden = ['created_at','updated_at'];
    protected $appends = ['percentage'];
    protected $fillable = [
        'exam_id',
        'class_section_id',
        'total_student',
        'total_attempt_student',
        'pass',
        'session_year_id',
    ];

    /**
     * Get the exam that owns the ExamStatistics
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function getPercentageAttribute()
    {
        return number_format(($this->pass * 100) / $this->total_attempt_student, 2);
    }

    /**
     * Get the class_section that owns the ExamStatistics
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class_section()
    {
        return $this->belongsTo(ClassSection::class);
    }
}
