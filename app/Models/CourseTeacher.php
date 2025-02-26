<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\CourseTeacher
 *
 * @property int $id
 * @property int|null $course_id
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTeacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTeacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTeacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTeacher whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTeacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTeacher whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTeacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTeacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTeacher whereUserId($value)
 * @mixin \Eloquent
 */
class CourseTeacher extends Model
{
    use HasFactory;
   

    protected $fillable=[
        'course_id',
        'user_id',
    ];
   
}
