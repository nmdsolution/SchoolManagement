<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Assignment
 *
 * @property int $id
 * @property int $class_section_id
 * @property int $subject_id
 * @property string $name
 * @property string|null $instructions
 * @property string $due_date
 * @property int|null $points
 * @property int $resubmission
 * @property int|null $extra_days_for_resubmission
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $file
 * @property-read int|null $file_count
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\AssignmentSubmission|null $submission
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment assignmentTeachers()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereExtraDaysForResubmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereResubmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Assignment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Assignment extends Model
{
    use HasFactory;

    protected $hidden = ["deleted_at", "updated_at"];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($assignment) { // before delete() method call this
            //Deletes all the Assignment Submissions first
            $assignment_submission = AssignmentSubmission::where('assignment_id', $assignment->id)->get();
            if ($assignment_submission) {
                foreach ($assignment_submission as $submission) {
                    if (isset($submission->file)) {
                        foreach ($submission->file as $file) {
                            if (Storage::disk('public')->exists($file->file_url)) {
                                Storage::disk('public')->delete($file->file_url);
                            }
                        }
                        $submission->delete();
                    }
                }
            }

            //After that Delete Assignment and its files from the server
            if ($assignment->file) {
                foreach ($assignment->file as $file) {
                    if (Storage::disk('public')->exists($file->file_url)) {
                        Storage::disk('public')->delete($file->file_url);
                    }
                }
            }
            $assignment->file()->delete();
        });
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function submission()
    {
        return $this->hasOne(AssignmentSubmission::class);
    }

    public function class_section()
    {
        return $this->belongsTo(ClassSection::class)->with('class', 'section');
    }

    public function file()
    {
        return $this->morphMany(File::class, 'modal');
    }

    public function scopeAssignmentTeachers($query)
    {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacher_id = $user->teacher()->select('id')->pluck('id')->first();
            $subject_teacher = SubjectTeacher::select('class_section_id', 'subject_id')->where('teacher_id', $teacher_id)->get();
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
