<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Rawilk\Settings\Models\HasSettings;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $gender
 * @property string|null $email
 * @property string|null $fcm_id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $mobile
 * @property string|null $image
 * @property string|null $dob
 * @property string|null $born_at
 * @property string|null $current_address
 * @property string|null $permanent_address
 * @property int $status
 * @property int $reset_request
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CenterTeacher> $center_teacher
 * @property-read int|null $center_teacher_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read mixed $birth_date
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guardian> $guardian
 * @property-read int|null $guardian_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Parents|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Staff> $staff
 * @property-read int|null $staff_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StaffRole> $staff_role
 * @property-read int|null $staff_role_count
 * @property-read \App\Models\Students|null $student
 * @property-read \App\Models\Teacher|null $teacher
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBornAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFcmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePermanentAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereResetRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use SoftDeletes;
    use HasSettings;

    protected $appends = ['full_name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "first_name",
        "last_name",
        "gender",
        "image",
        "current_address",
        "permanent_address",
        "email",
        'password',
        "mobile",
        "dob",
        'born_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        "deleted_at",
        "created_at",
        "updated_at"
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function student()
    {
        return $this->hasOne(Students::class, 'user_id', 'id');
    }

    public function parent()
    {
        return $this->hasOne(Parents::class, 'user_id', 'id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id', 'id');
    }

    public function center()
    {
        if (Auth::user()->hasRole('Center')) {
            return $this->hasOne(Center::class, 'user_id', 'id');
        } else if (Auth::user()->hasRole('Teacher')) {
            return $this->hasMany(CenterTeacher::class, 'user_id', 'id');
        } else if (Auth::user()->staff->first()) {
            return $this->hasOne(Staff::class)->where('user_id', Auth::user()->id)->where('center_id', Session()->get('center_id'));
        }

        return $this->hasOne(Center::class);

    }

    //Getter Attributes
    public function getImageAttribute($value)
    {
        if ($value) {
            return url(Storage::url($value));
        }
        return '';
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get all of the guardian for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function guardian()
    {
        return $this->hasMany(Guardian::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_teachers', 'course_id', 'user_id');
    }


    /**
     * Get all of the staff_role for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staff_role()
    {
        return $this->hasMany(StaffRole::class);
    }

    /**
     * Get all of the staff for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function checkPermission($ability)
    {
        $permission_name = $ability;
        $permission = Permission::where('name', $permission_name)->get()->first();
        if (!$permission) {
            return false;

        }
        $user_roles = Auth::user()->getRoleNames();
        $role = Role::whereIn('name', $user_roles)->pluck('id');
        if (Auth::user()->staff->first()) {
            $role_has_permission = RoleHasPermission::where('permission_id', $permission->id)->where('medium_id', getCurrentMedium()->id)->whereIn('role_id', $role)->get()->first();
            if ($role_has_permission) {
                return true;
            }
            return false;
        }

        $role_has_permission = RoleHasPermission::where('permission_id', $permission->id)->whereIn('role_id', $role)->get()->first();
        if ($role_has_permission) {
            return true;
        }
        return false;
    }

    public function getBirthDateAttribute()
    {
        return date('d-m-Y', strtotime($this->dob));
    }

    /**
     * Get all of the center_teacher for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function center_teacher()
    {
        return $this->hasMany(CenterTeacher::class);
    }

    public function getLastNameAttribute($last_name)
    {
        return ' ';
    }

}
