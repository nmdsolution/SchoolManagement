<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Holiday
 *
 * @property int $id
 * @property string $date
 * @property string $title
 * @property string|null $description
 * @property int|null $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday query()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Holiday extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'date', 'title', 'center_id', 'description'
    ];

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Center')) {
            return $query->where('center_id', Auth::user()->center->id);
        } else if (Auth::user()->hasRole('Teacher')) {
            return $query->where('center_id', Session()->get('center_id'));
        } else if (Auth::user()->staff->first()) {
            return $query->where('center_id', Session()->get('center_id'));
        } else if (Auth::user()->hasRole('Student')) {
            return $query->where('center_id', Auth::user()->student->center_id);
        }

        return $query;

    }
}
