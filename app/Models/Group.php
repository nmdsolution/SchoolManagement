<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Group
 *
 * @property int $id
 * @property string $name
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassGroup> $class_group
 * @property-read int|null $class_group_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSchool> $classes
 * @property-read int|null $classes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'center_id', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at'];

    public function classes()
    {
        return $this->belongsToMany(ClassSchool::class, ClassGroup::class, 'group_id', 'class_id');
    }

    /**
     * Get all of the class_group for the Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function class_group()
    {
        return $this->hasMany(ClassGroup::class);
    }

    public function scopeOwner($query)
    {
        return $query->where('center_id', get_center_id());
        // $query->where('center_id', Auth::user()->center->id);
    }
}
