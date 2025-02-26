<?php

namespace App\Models\Competency;

use App\Models\ClassSchool;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Competency\ClassCompetency
 *
 * @property int $id
 * @property int $class_id
 * @property int $competency_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $cote
 * @property-read ClassSchool $class
 * @property-read \App\Models\Competency\Competency $competency
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Competency\CompetencyType> $competencyTypes
 * @property-read int|null $competency_types_count
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetency query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetency whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetency whereCompetencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetency whereCote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassCompetency whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClassCompetency extends Pivot
{
    use HasFactory;

    protected $table = 'class_competency';

    protected $fillable = [
        'class_id',
        'competency_id',
    ];

    protected $with = ['competencyTypes', 'competency'];

    public function class()
    {
        return $this->belongsTo(ClassSchool::class, 'class_id');
    }

    public function competency()
    {
        return $this->belongsTo(Competency::class, 'competency_id');
    }

    public function competencyTypes()
    {
        return $this->belongsToMany(CompetencyType::class, 'class_competency_type', 'class_competency_id', 'competency_type_id')
                    ->using(ClassCompetencyType::class)
                    ->withPivot('total_marks');
    }
}
