<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\CourseCategory
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $thumbnail
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCategory whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CourseCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'thumbnail'];

    public function getThumbnailAttribute($thumbnail) {
        if ($thumbnail) {
            return url(Storage::url($thumbnail));
        }
        return '';
    }

    public function courses(){
        return $this->hasMany(Course::class);
    }
}
