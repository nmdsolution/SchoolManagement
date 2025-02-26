<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\EffectiveDomain
 *
 * @property int $id
 * @property string $name
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $medium_id
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain currentMedium()
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain owner()
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain query()
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EffectiveDomain extends Model
{
    use HasFactory;

    public function scopeOwner($query)
    {
        if (Auth::user()->hasRole('Center')) {
            $query->where('center_id', Auth::user()->center->id);
        }

        if (Auth::user()->hasRole('Teacher')) {
            $center_ids = CenterTeacher::where('teacher_id', Auth::user()->teacher->id)->select('center_id')->pluck('center_id');
            $query->whereIn('center_id', $center_ids);
        }

        if (Auth::user()->hasRole('Student')) {
            $query->where('center_id', Auth::user()->student->center_id);
        }

//TODO : cannot fetch data here from multiple centers
//        if (Auth::user()->hasRole('Parent')) {
//            $query->whereIn('center_id', Auth::user()->parent->children->pluck('center_id'));
//        }
    }

    public function scopeCurrentMedium($query){
        $query->where('medium_id', getCurrentMedium()->id);
    }
}
