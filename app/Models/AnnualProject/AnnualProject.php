<?php

namespace App\Models\AnnualProject;

use App\Models\Center;
use App\Models\User\User;
use App\Models\SessionYear;
use App\Models\ClassSubject;
use App\Models\Project\Project;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AnnualProject\AnnualProject
 *
 * @property int $id
 * @property int $class_subject_id
 * @property int $center_id
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Center $center
 * @property-read ClassSubject $classSubject
 * @property-read SessionYear $sessionYear
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProject currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProject owner()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProject query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProject whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProject whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProject whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualProject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AnnualProject extends Model 
{
    protected $table = 'annual_projects';

    protected $fillable = [
        'class_subject_id',
        'session_year_id',
        'center_id',
    ];

    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function sessionYear()
    {
        return $this->belongsTo(SessionYear::class);
    }

    public function scopeCurrentSessionYear($q)
    {
        return $q->where('session_year_id', getSessionYearData()->id);
    }

    public function scopeOwner($q)
    {
        return $q->where('center_id', get_center_id());
    }
}