<?php

namespace App\Models\Competency;

use App\Models\ExamTerm;
use App\Models\ClassSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Competency\Evaluation
 *
 * @property int $id
 * @property int $exam_term_id
 * @property int $learning_unit_id
 * @property int $class_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ClassSchool $class
 * @property-read \App\Models\Competency\CompetencyDomain $competency_domain
 * @property-read ExamTerm $exam_term
 * @property-read \App\Models\Competency\LearningUnit $learning_unit
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereLearningUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_term_id',
        'learning_unit_id',
        'class_id',
    ];

    public function class()
    {
        return $this->belongsTo(ClassSchool::class, 'class_id');
    }

    public function competency_domain()
    {
        return $this->belongsTo(CompetencyDomain::class);
    }

    public function learning_unit()
    {
        return $this->belongsTo(LearningUnit::class);
    }

    public function exam_term()
    {
        return $this->belongsTo(ExamTerm::class);
    }
}
