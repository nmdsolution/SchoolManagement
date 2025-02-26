<?php

namespace App\Models\Competency;

use App\Models\Grade;
use App\Models\Students;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Competency\EvaluationResult
 *
 * @property int $id
 * @property int $evaluation_id
 * @property int|null $grade_id
 * @property int $student_id
 * @property int|null $oral
 * @property int|null $written
 * @property int|null $attitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Competency\Evaluation $evaluation
 * @property-read Grade|null $grade
 * @property-read Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationResult whereAttitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationResult whereEvaluationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationResult whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationResult whereOral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationResult whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationResult whereWritten($value)
 * @mixin \Eloquent
 */
class EvaluationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'grade_id',
        'student_id',
        'oral',
        'written',
        'attitude',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }
}
