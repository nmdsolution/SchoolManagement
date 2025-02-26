<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

/**
 * App\Models\FeesType
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $choiceable 0 - no 1 - yes
 * @property int|null $center_id
 * @property int|null $medium_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesClass> $fees_class
 * @property-read int|null $fees_class_count
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType activeMediumOnly()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereChoiceable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType withoutTrashed()
 * @mixin \Eloquent
 */
class FeesType extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function fees_class(){
        return $this->hasMany(FeesClass::class ,'fees_type_id');
    }

    public function scopeOwner($query)
    {
        return $query->where('center_id',get_center_id());
    }

    public function scopeActiveMediumOnly($query)
    {
        return $query->where('medium_id',getCurrentMedium()->id);
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
