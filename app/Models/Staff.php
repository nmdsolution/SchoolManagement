<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Staff
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $center_id Super admin staff if NULL
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Center|null $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StaffRole> $staff_role
 * @property-read int|null $staff_role_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Staff newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Staff newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Staff owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Staff query()
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereUserId($value)
 * @mixin \Eloquent
 */
class Staff extends Model {
    use HasFactory;

    /**
     * Get the user that owns the Staff
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get all of the staff_role for the Staff
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staff_role() {
        return $this->hasMany(StaffRole::class);
    }

    public function scopeOwner($query) {
        if (Auth::user()->hasRole('Super Admin')) {
            return $query = Staff::where('center_id', NULL);
        } else if (Auth::user()->hasRole('Center')) {
            return $query = Staff::where('center_id', Auth::user()->center->id);
        } else if (Auth::user()->staff->first()) {
            if (Session()->get('center_id') != -1) {
                return $query = Staff::where('center_id', Session()->get('center_id'));
            } else {
                return $query = Staff::where('center_id', NULL);
            }

        }
        return $query;
    }

    /**
     * Get the center that owns the Staff
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function center() {
        return $this->belongsTo(Center::class);
    }
}
