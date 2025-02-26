<?php
namespace App\Models\Competency;

use App\Models\Center;
use App\Models\Mediums;
use App\Models\ClassSchool;
use App\Models\SessionYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Competency\CompetencyDomain
 *
 * @property int $id
 * @property string $name
 * @property int|null $number
 * @property int|null $total_marks
 * @property int $center_id
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $medium_id
 * @property-read Center $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Competency\Competency> $competencies
 * @property-read int|null $competencies_count
 * @property-read mixed $rank
 * @property-read Mediums|null $medium
 * @property-read SessionYear $sessionYear
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain activeMediumOnly()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain owner($center_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain query()
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain whereTotalMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CompetencyDomain whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CompetencyDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
        'total_marks',
        'center_id',
        'session_year_id',
        'medium_id',
    ];

    protected $append = [
        'rank'
    ];

    public function getRankAttribute() {
        return $this->number;
    }

    public function sessionYear(): BelongsTo
    {
        return $this->belongsTo(SessionYear::class);
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function competencies(): HasMany
    {
        return $this->hasMany(Competency::class);
    }

    public function medium() {
        return $this->belongsTo(Mediums::class)->select('name', 'id')->withTrashed();
    }
    
    public function scopeactiveMediumOnly($query) {
        $activeMedium = getCurrentMedium();
        $query->where('medium_id', $activeMedium->id);
    }

    public function scopeOwner($query, $center_id = null) {
        if (Auth::user()->hasRole('Center')) {
            $query->where('center_id', Auth::user()->center->id);
        }

        if (Auth::user()->hasRole('Teacher')) {
            if ($center_id) {
                $query->whereIn('center_id', $center_id);
            }
        }

        if (Auth::user()->staff->first()) {
            $query->where('center_id', Session()->get('center_id'));
        }
        return $query;
    }
}
