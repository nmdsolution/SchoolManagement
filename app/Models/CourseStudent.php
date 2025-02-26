<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CourseStudent
 *
 * @property int $id
 * @property int|null $course_id
 * @property int|null $student_id
 * @property int|null $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\Students|null $student
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseStudent withoutTrashed()
 * @mixin \Eloquent
 */
class CourseStudent extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable=[
        'course_id',
        'student_id',
    ];
    protected $hidden = ['created_at','updated_at','deleted_at'];

   /**
    * Get the course that owns the CourseStudent
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
   public function course()
   {
       return $this->belongsTo(Course::class);
   }
   /**
    * Get the student that owns the CourseStudent
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
   public function student()
   {
       return $this->belongsTo(Students::class);
   }
}
