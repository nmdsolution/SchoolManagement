<?php

namespace App\Models\AnnualProject;

use App\Models\ExamSequence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\AnnualProject\ClassAnnualProjectType
 *
 * @property int $id
 * @property int $annual_project_id
 * @property int $exam_sequence_id
 * @property int $annual_project_type_id
 * @property int $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AnnualProject\AnnualProject $annualProject
 * @property-read \App\Models\AnnualProject\AnnualProjectType $annualProjectType
 * @property-read ExamSequence $examSequence
 * @method static \Illuminate\Database\Eloquent\Builder|ClassAnnualProjectType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassAnnualProjectType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassAnnualProjectType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassAnnualProjectType whereAnnualProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassAnnualProjectType whereAnnualProjectTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassAnnualProjectType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassAnnualProjectType whereExamSequenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassAnnualProjectType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassAnnualProjectType whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassAnnualProjectType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClassAnnualProjectType extends Model
{
    use HasFactory;

    protected $table = 'class_annual_project_types';

    protected $fillable = [
        'annual_project_type_id',
        'annual_project_id',
        'exam_sequence_id',
        'total',
    ];

    protected $casts = [
        'total' => 'integer',
    ];

    public function annualProjectType()
    {
        return $this->belongsTo(AnnualProjectType::class);
    }

    public function examSequence()
    {
        return $this->belongsTo(ExamSequence::class);
    }

    public function annualProject()
    {
        return $this->belongsTo(AnnualProject::class);
    }
}
