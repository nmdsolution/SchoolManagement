<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

/**
 * App\Models\StaffRole
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $staff_id
 * @property int|null $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Role|null $role
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole whereStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole whereUserId($value)
 * @mixin \Eloquent
 */
class StaffRole extends Model
{
    use HasFactory;

    public function role()
    {
        return $this->belongsTo(Role::class)->with('permissions');
    }
}
