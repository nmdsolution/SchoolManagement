<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\SliderAccess
 *
 * @property int $id
 * @property int $slider_id
 * @property int $center_id
 * @property int $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess owner()
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess query()
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess whereSliderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SliderAccess extends Model
{
    use HasFactory;

    protected $table = 'slider_access';

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


        if (Auth::user()->hasRole('Parent')) {
            $query->whereIn('center_id', Auth::user()->parent->children->pluck('center_id'));
        }

        // Check the role
        $query->whereIn('role_id', Auth::user()->roles->pluck('id'));
    }
}
