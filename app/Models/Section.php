<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Section
 *
 * @property int $id
 * @property string $name
 * @property int|null $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSchool> $classes
 * @property-read int|null $classes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Section owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Section withoutTrashed()
 * @mixin \Eloquent
 */
class Section extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'center_id'
    ];

    protected $hidden = ["deleted_at","created_at","updated_at"];
    public function classes() {
        return $this->belongsToMany(ClassSchool::class, 'class_sections', 'section_id', 'class_id');
    }

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Center')) {
            return $query->where('center_id',Auth::user()->center->id);
        } else if(Auth::user()->hasRole('Teacher'))
        {
            return $query->where('center_id',Session()->get('center_id'));
        } else if(Auth::user()->staff->first()) {
            return $query->where('center_id',Session()->get('center_id'));
        }
        return $query;
    }
}
