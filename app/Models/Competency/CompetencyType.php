<?php

namespace App\Models\Competency;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Competency\CompetencyType
 *
 * @property int $id
 * @property string $name
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Competency\ClassCompetency> $classCompetencies
 * @property-read int|null $class_competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Competency\Competency> $competencies
 * @property-read int|null $competencies_count
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType owner()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyType withoutTrashed()
 * @mixin \Eloquent
 */
class CompetencyType extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'center_id'
    ];

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Center')) {
            return $query->where('center_id', Auth::user()->center->id);
        } else if(Auth::user()->hasRole('Teacher')) {
            return $query->where('center_id', Session()->get('center_id'));
        } else if(Auth::user()->staff->first()) {
            return $query->where('center_id', Session()->get('center_id'));
        }
        return $query;
    }

    public function competencies()
    {
        return $this->belongsToMany(Competency::class)
            ->using(ClassCompetency::class)
            ->withPivot('id', 'competencyTypes');
            ;
    }

    public function classCompetencies()
    {
        return $this->belongsToMany(ClassCompetency::class, 'class_competency_type', 'competency_type_id', 'class_competency_id')
                    ->withPivot('total_marks');
    }
}