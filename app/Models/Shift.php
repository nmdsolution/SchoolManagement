<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Shift
 *
 * @property int $id
 * @property string $title
 * @property string $start_time
 * @property string $end_time
 * @property int $status
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift withoutTrashed()
 * @mixin \Eloquent
 */
class Shift extends Model
{
    use HasFactory;
    use softDeletes;
    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Center')) {
            return $query->where('center_id',Auth::user()->center->id);
        } else if(Auth::user()->staff->first()) {
            return $query->where('center_id',Auth::user()->center->id);
        }
        return $query;
    }
}
