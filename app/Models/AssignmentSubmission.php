<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\AssignmentSubmission
 *
 * @property int $id
 * @property int $assignment_id
 * @property int $student_id
 * @property int $session_year_id
 * @property string|null $feedback
 * @property int|null $points
 * @property int $status 0 = Pending/In Review , 1 = Accepted , 2 = Rejected , 3 = Resubmitted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Assignment $assignment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $file
 * @property-read int|null $file_count
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission assignmentSubmissionTeachers()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignmentSubmission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AssignmentSubmission extends Model
{
    use HasFactory;

    public function file() {
        return $this->morphMany(File::class, 'modal');
    }

    public function assignment() {
        return $this->belongsTo(Assignment::class);
    }

    public function student() {
        return $this->belongsTo(Students::class);
    }

    public function scopeAssignmentSubmissionTeachers($query) {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $teacher_id = $user->teacher()->select('id')->pluck('id')->first();
            $subject_teacher = SubjectTeacher::select('class_section_id', 'subject_id')->where('teacher_id', $teacher_id)->get();
            if ($subject_teacher) {
                $subject_teacher = $subject_teacher->toArray();
                $class_section_id = array_column($subject_teacher, 'class_section_id');
                $subject_id = array_column($subject_teacher, 'subject_id');
                $assignment_id = Assignment::select('id')->whereIn('class_section_id', $class_section_id)->whereIn('subject_id', $subject_id)->get()->pluck('id');
                return $query->whereIn('assignment_id', $assignment_id);
            }
            return $query;
        }
        return $query;
    }

    public function scopeCurrentSessionYear($query) {
        return $query->where('session_year_id', getSettings('session_year')['session_year']);
    }
}
