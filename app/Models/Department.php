<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Department
 *
 * @property int $id
 * @property string $name
 * @property int|null $responsible_id
 * @property int $center_id
 * @property int $session_year_id
 * @property int $medium_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Center $center
 * @property-read \App\Models\Mediums $medium
 * @property-read \App\Models\User|null $responsible
 * @property-read \App\Models\SessionYear $sessionYear
 * @method static \Illuminate\Database\Eloquent\Builder|Department currentMediumOnly()
 * @method static \Illuminate\Database\Eloquent\Builder|Department currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Department owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereResponsibleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $subjects
 * @property-read int|null $subjects_count
 * @mixin \Eloquent
 */
class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'center_id', 'session_year_id', 'medium_id', 'responsible_id'
    ];


    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function sessionYear(): BelongsTo
    {
        return $this->belongsTo(SessionYear::class);
    }

    public function medium(): BelongsTo
    {
        return $this->belongsTo(Mediums::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function scopeOwner($q)
    {
        return $this->where('center_id', get_center_id());
    }

    public function scopeCurrentSessionYear($q)
    {
        return $q->where('sesion_year_id', getSessionYearData()->id);
    }

    public function scopeCurrentMediumOnly($q)
    {
        return $q->where('medium_id', getCurrentMedium()->id);
    }
}
