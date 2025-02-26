<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FeesDiscount
 *
 * @property int $id
 * @property string $name
 * @property int $center_id
 * @property string|null $description
 * @property string $amount
 * @property mixed $applicable_status
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $students
 * @property-read int|null $students_count
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount activeMediumOnly()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount whereApplicableStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesDiscount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FeesDiscount extends Model
{
    protected $fillable = [
        'name',
        'description',
        'amount',
        'applicable_status',
        'active',
        'center_id'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function students()
    {
        return $this->belongsToMany(Students::class, 'student_fees_discounts');
    }

    public function scopeOwner($query)
    {
        return $query->where('center_id', get_center_id());
    }

    public function scopeActiveMediumOnly($query)
    {
        return $query->where('medium_id',getCurrentMedium()->id);
    }

}