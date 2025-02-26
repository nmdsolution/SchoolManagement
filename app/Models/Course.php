<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Course
 *
 * @property int $id
 * @property string $name
 * @property int $price
 * @property string $duration in Hours
 * @property string $thumbnail
 * @property string|null $description
 * @property string|null $tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $course_category_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseTeacher> $courseTeachers
 * @property-read int|null $course_teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseSection> $course_section
 * @property-read int|null $course_section_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseStudent> $course_student
 * @property-read int|null $course_student_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseTeacher> $course_teacher
 * @property-read int|null $course_teacher_count
 * @property-read mixed $course_category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $students
 * @property-read int|null $students_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCourseCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Course withoutTrashed()
 * @mixin \Eloquent
 */
class Course extends Model {
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'name', 'price', 'duration', 'thumnail', 'description', 'tags',
        'course_category_id'
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $appends = ['course_category'];


    public function users() {
        return $this->belongsToMany(User::class, 'course_teachers', 'course_id', 'user_id')->withTrashed();
    }

    public function courseTeachers() {
        return $this->belongsToMany(CourseTeacher::class, 'course_id', 'id');
    }

    public function course_teacher() {
        return $this->hasMany(CourseTeacher::class);
    }

    public function getThumbnailAttribute($thumbnail) {
        if ($thumbnail) {
            return url(Storage::url($thumbnail));
        }
        return '';
    }

    /**
     * Get the course_section associated with the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function course_section() {
        return $this->hasMany(CourseSection::class);
    }

    public function students() {
        return $this->belongsToMany(Students::class, 'course_students', 'course_id', 'student_id');
    }

    /**
     * Get all of the course_student for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function course_student() {
        return $this->hasMany(CourseStudent::class);
    }

    public function getCourseCategoryAttribute(){
        if($this->course_category_id != null){
            return CourseCategory::find($this->course_category_id);
        }

        return null;
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }
}
