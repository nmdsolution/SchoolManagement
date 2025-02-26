<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExamResult
 *
 * @property int $id
 * @property int $exam_id
 * @property int $class_section_id
 * @property int $student_id
 * @property int $total_marks
 * @property int $obtained_marks
 * @property float $percentage
 * @property string $grade
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\Exam $exam
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereObtainedMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereTotalMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExamResult extends Model
{
    use HasFactory;
    protected $hidden = ["deleted_at","created_at","updated_at"];
    protected $fillable = [
        'exam_id',
        'class_section_id',
        'student_id',
        'total_marks',
        'obtained_marks',
        'percentage',
        'grade',
        'session_year_id'
    ];

    public function student(){
        return $this->belongsTo(Students::class ,'student_id')->withTrashed();
    }
    public function session_year(){
        return $this->belongsTo(SessionYear::class,'session_year_id');
    }

    public function exam(){
        return $this->belongsTo(Exam::class,'exam_id');
    }

    /**
     * Get the class_section that owns the ExamResult
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class_section()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function scopeCurrentSessionYear($query) {
        return $query->where('session_year_id', getSettings('session_year')['session_year']);
    }
}
