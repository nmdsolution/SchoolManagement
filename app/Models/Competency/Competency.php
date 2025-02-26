<?php

namespace App\Models\Competency;

use App\Models\ClassSchool;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Competency\Competency
 *
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property int $competency_domain_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ClassSchool> $classes
 * @property-read int|null $classes_count
 * @property-read \App\Models\Competency\CompetencyDomain $competency_domain
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Competency\CompetencyType> $competency_types
 * @property-read int|null $competency_types_count
 * @method static \Illuminate\Database\Eloquent\Builder|Competency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Competency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Competency onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Competency query()
 * @method static \Illuminate\Database\Eloquent\Builder|Competency whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competency whereCompetencyDomainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competency whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Competency withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Competency withoutTrashed()
 * @mixin \Eloquent
 */
class Competency extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'competency_domain_id',
    ];


    public function competency_domain(): BelongsTo
    {
        return $this->belongsTo(CompetencyDomain::class, 'competency_domain_id');
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(ClassSchool::class, 'class_competency', 'competency_id', 'class_id')
            ->withPivot('cote', 'id')
            ->using(ClassCompetency::class);
    }

    public function competency_types()
    {
        return $this->belongsToMany(CompetencyType::class)
            ->withPivot('total_marks');
    }
}
