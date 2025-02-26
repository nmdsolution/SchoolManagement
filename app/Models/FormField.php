<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\NoReturn;

/**
 * App\Models\FormField
 *
 * @property int $id
 * @property string $name
 * @property string $type text,number,textarea,dropdown,checkbox,radio,fileupload
 * @property int $is_required
 * @property string|null $default_values values of radio,checkbox,dropdown,etc
 * @property string|null $other extra HTML attributes
 * @property int|null $center_id Null = Added By Admin
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|FormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereDefaultValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField withoutTrashed()
 * @mixin \Eloquent
 */
class FormField extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'is_required',
        'default_values',
        'other',
        'center_id',
        'rank'
    ];

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Super Admin')) {
            return $query->where('center_id', null);
        } else if(Auth::user()->hasRole('Center')) {
            return $query->where('center_id', Auth::user()->center->id);
        } else if(Auth::user()->staff->first()) {
            return $query->where('center_id', Session()->get('center_id'));
        } else if(Auth::user()->hasRole('Teacher')) {
            return $query->where('center_id', Session()->get('center_id'));
        }

        if (Auth::user()->hasRole('Center')) {
            return $query->where('center_id', Auth::user()->center->id);
        }

        return $query;
    }
}
