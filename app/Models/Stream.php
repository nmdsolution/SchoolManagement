<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Stream
 *
 * @property int $id
 * @property string $name
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Stream newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream query()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream withoutTrashed()
 * @mixin \Eloquent
 */
class Stream extends Model
{
    use HasFactory;
    use softDeletes;

    protected $fillable=[
        'name',
        'center_id',
    ];

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
