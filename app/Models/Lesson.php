<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Lesson
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $class_section_id
 * @property int $subject_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $file
 * @property-read int|null $file_count
 * @property-read \App\Models\Subject $subject
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LessonTopic> $topic
 * @property-read int|null $topic_count
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson lessonTeachers()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson query()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Lesson extends Model
{
    use HasFactory;

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($lesson) { // before delete() method call this
            if ($lesson->file) {
                foreach ($lesson->file as $file) {
                    if (Storage::disk('public')->exists($file->file_url)) {
                        Storage::disk('public')->delete($file->file_url);
                    }
                }

                $lesson->file()->delete();
            }
            if ($lesson->topic) {
                $lesson->topic()->delete();
            }
        });
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class_section()
    {
        return $this->belongsTo(ClassSection::class)->with('class', 'section');
    }

    public function file()
    {
        return $this->morphMany(File::class, 'modal');
    }

    public function topic()
    {
        return $this->hasMany(LessonTopic::class);
    }

    public function scopeLessonTeachers($query)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacher_id = $user->teacher()->select('id')->pluck('id')->first();
            if (!is_array(get_center_id())) {
                $subject_teacher = SubjectTeacher::select('class_section_id', 'subject_id')->whereHas('subject',function($q) {
                    $q->where('center_id',get_center_id());
                })->where('teacher_id', $teacher_id)->get();    
            } else {
                $subject_teacher = SubjectTeacher::select('class_section_id', 'subject_id')->where('teacher_id', $teacher_id)->get();
            }
            
            if ($subject_teacher) {
                $subject_teacher = $subject_teacher->toArray();
                $class_section_id = array_column($subject_teacher, 'class_section_id');
                $subject_id = array_column($subject_teacher, 'subject_id');
                return $query->whereIn('class_section_id', $class_section_id)->whereIn('subject_id', $subject_id);
            }
            return $query;

        }
        return $query;
    }
}
