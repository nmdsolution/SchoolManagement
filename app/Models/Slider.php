<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role;

/**
 * App\Models\Slider
 *
 * @property int $id
 * @property string $image
 * @property string|null $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SliderAccess> $access
 * @property-read int|null $access_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Center> $center_access
 * @property-read int|null $center_access_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $role_access
 * @property-read int|null $role_access_count
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider query()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereUrl($value)
 * @mixin \Eloquent
 */
class Slider extends Model
{
    use HasFactory;

    public function access()
    {
        return $this->hasMany(SliderAccess::class);
    }

    public function center_access()
    {
        return $this->belongsToMany(Center::class, SliderAccess::class)->groupBy('center_id', 'slider_id');
    }

    public function role_access()
    {
        return $this->belongsToMany(Role::class, SliderAccess::class)->groupBy('role_id', 'slider_id');
    }

    //Getter Attributes
    public function getImageAttribute($value)
    {
        return url(Storage::url($value));
    }
}
