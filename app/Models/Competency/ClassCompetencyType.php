<?php

namespace App\Models\Competency;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\Competency\ClassCompetencyType
 *
 * @property int $id
 * @property int $class_competency_id
 * @property int $competency_type_id
 * @property string $total_marks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Competency\ClassCompetency $classCompetency
 * @property-read \App\Models\Competency\CompetencyType $competencyType
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetencyType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetencyType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetencyType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetencyType whereClassCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetencyType whereCompetencyTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetencyType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetencyType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetencyType whereTotalMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetencyType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClassCompetencyType extends Pivot
{
    use HasFactory;

    protected $table = 'class_competency_type'; // Nom de la table pivot

    protected $fillable = [
        'class_competency_id',
        'competency_type_id',
        'total_marks', // Champ pour stocker les notes
    ];

    public function classCompetency()
    {
        return $this->belongsTo(ClassCompetency::class, 'class_competency_id');
    }

    public function competencyType()
    {
        return $this->belongsTo(CompetencyType::class, 'competency_type_id');
    }
} 