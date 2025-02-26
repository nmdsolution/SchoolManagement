<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CenterTeacher
 *
 * @property int $id
 * @property int $center_id
 * @property int $teacher_id
 * @property int $user_id Teacher User ID
 * @property string $manage_student_parent 0 => No permission, 1 => Permission
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Center $center
 * @method static \Illuminate\Database\Eloquent\Builder|CenterTeacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CenterTeacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CenterTeacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|CenterTeacher whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CenterTeacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CenterTeacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CenterTeacher whereManageStudentParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CenterTeacher whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CenterTeacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CenterTeacher whereUserId($value)
 * @mixin \Eloquent
 */
class CenterTeacher extends Model
{
    use HasFactory;
    protected $hidden = ['created_at','updated_at'];

    protected $fillable = [
        'center_id','teacher_id','user_id'
    ];

    /**
     * Get the center that owns the CenterTeacher
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function center()
    {
        return $this->belongsTo(Center::class);
    }
}
