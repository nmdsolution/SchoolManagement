<?php

namespace App\Models\AnnualProject;

use App\Models\Center;
use App\Models\Mediums;
use App\Models\SessionYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\AnnualProject\AnnualProjectType
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $center_id
 * @property int $session_year_id
 * @property int $medium_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnnualProject\AnnualProject> $annualProjects
 * @property-read int|null $annual_projects_count
 * @property-read Center $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnnualProject\ClassAnnualProjectType> $classAnnualProjectTypes
 * @property-read int|null $class_annual_project_types_count
 * @property-read Mediums $medium
 * @property-read SessionYear $sessionYear
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType currentMediumOnly()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType owner()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProjectType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AnnualProjectType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'center_id',
        'session_year_id',
        'medium_id'
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function sessionYear()
    {
        return $this->belongsTo(SessionYear::class);
    }

    public function medium()
    {
        return $this->belongsTo(Mediums::class);
    }

    public function classAnnualProjectTypes()
    {
        return $this->hasMany(ClassAnnualProjectType::class);
    }

    public function annualProjects()
    {
        return $this->hasMany(AnnualProject::class);
    }

    public function scopeCurrentMediumOnly($query)
    {
        return $query->where('medium_id', getCurrentMedium()->id);
    }

    public function scopeOwner($query)
    {
        return $query->where('center_id', get_center_id());
    }

    public function scopeCurrentSessionYear($query)
    {
        return $query->where('session_year_id', getSessionYearData()->id);
    }
}
