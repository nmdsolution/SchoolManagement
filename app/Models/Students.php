<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;

/**
 * App\Models\Students
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $class_section_id
 * @property string $admission_no
 * @property int|null $roll_number
 * @property string $admission_date
 * @property string|null $minisec_matricule
 * @property mixed|null $status
 * @property int $is_new_admission
 * @property int|null $father_id
 * @property int|null $mother_id
 * @property int|null $guardian_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $center_id
 * @property int|null $session_year_id
 * @property string $dynamic_field_values
 * @property string $nationality
 * @property int $repeater
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcement
 * @property-read int|null $announcement_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance_absent
 * @property-read int|null $attendance_absent_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance_present
 * @property-read int|null $attendance_present_count
 * @property-read \App\Models\Center|null $center
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseStudent> $coursesStudent
 * @property-read int|null $courses_student_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamMarks> $exam_marks
 * @property-read int|null $exam_marks_count
 * @property-read \App\Models\ExamReportStudentSequence|null $exam_report_student_sequence
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamResult> $exam_result
 * @property-read int|null $exam_result_count
 * @property-read \App\Models\Parents|null $father
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesPaid> $fees_paid
 * @property-read int|null $fees_paid_count
 * @property-read mixed $class_name
 * @property-read mixed $father_image
 * @property-read mixed $full_name
 * @property-read mixed $mother_image
 * @property-read mixed $session_year
 * @property-read \App\Models\Parents|null $guardian
 * @property-read \App\Models\Parents|null $mother
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudentSessions> $studentSessions
 * @property-read int|null $student_sessions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudentAttendance> $student_attendance
 * @property-read int|null $student_attendance_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Students currentClassSection()
 * @method static \Illuminate\Database\Eloquent\Builder|Students currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|Students newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Students newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Students ofTeacher()
 * @method static \Illuminate\Database\Eloquent\Builder|Students onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Students owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Students query()
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereAdmissionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereAdmissionNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereDynamicFieldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereFatherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereGuardianId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereIsNewAdmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereMinisecMatricule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereMotherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereRepeater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereRollNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Students withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Students withoutTrashed()
 * @mixin \Eloquent
 */
class Students extends Model {
    use SoftDeletes;
    use HasFactory;
    use Searchable;

    protected $appends = ['full_name', 'class_name', 'session_year'];

    protected $hidden = ["deleted_at", "created_at", "updated_at"];

    public function payment_transactions() {
        return $this->hasMany(PaymentTransaction::class, 'student_id');
    }

    public function announcement() {
        return $this->morphMany(Announcement::class, 'table');
    }

    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function class_section() {
        return $this->belongsTo(ClassSection::class)->with('class.medium', 'section');
    }

    public function subjects() {
        $class_section_id = $this->class_section->id;
        $core_subjects = $this->class_section->class->coreSubject;
        $elective_subject_count = $this->class_section->class->electiveSubjectGroup->count();
        $elective_subjects = StudentSubject::query()->where('student_id', $this->id)->where('class_section_id', $class_section_id)->select("subject_id")->with('subject')->get();
        $response = array(
            'core_subject' => $core_subjects
        );
        if ($elective_subject_count > 0) {
            $response['elective_subject'] = $elective_subjects;
        }
        return $response;
    }

    public function classSubjects() {
        $core_subjects = $this->class_section->class->coreSubject;
        $elective_subjects = $this->class_section->class->electiveSubjectGroup->load('electiveSubjects.subject');
        return ['core_subject' => $core_subjects, 'elective_subject_group' => $elective_subjects];
    }


    //Getter Attributes
    public function getFatherImageAttribute($value) {
        return url(Storage::url($value));
    }

    public function getMotherImageAttribute($value) {
        return url(Storage::url($value));
    }

    public function father() {
        return $this->belongsTo(Parents::class, 'father_id');
    }

    public function mother() {
        return $this->belongsTo(Parents::class, 'mother_id');
    }

    public function guardian() {
        return $this->belongsTo(Parents::class, 'guardian_id');
    }

    public function scopeOfTeacher($query) {
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            if (Auth::user()->teacher) {
                $class_section = ClassSection::where('class_teacher_id', Auth::user()->teacher->id)->whereHas('class', function ($q) {
                    $q->where('center_id', session()->get('center_id'));
                });
                // $class_section = $user->teacher->class_section;
                $class_section = $class_section->get()->first();
                if ($class_section) {
                    return $query->where('class_section_id', $class_section->id);
                }
            }

        }
        return $query;
    }

    public function scopeOwner($query) {
        if (Auth::user()->hasRole('Super Admin')) {
            return $query->where('center_id', null);
        } else if (Auth::user()->hasRole('Center')) {
            return $query->where('center_id', Auth::user()->center->id);
        } else if (Auth::user()->staff->first()) {
            return $query->where('center_id', Session()->get('center_id'));
        } else if (Auth::user()->hasRole('Teacher')) {
            return $query->where('center_id', Session()->get('center_id'));
        }
        return $query;
    }

    public function exam_result() {
        return $this->hasMany(ExamResult::class, 'student_id');
    }

    public function exam_marks() {
        return $this->hasMany(ExamMarks::class, 'student_id');
    }

    public function fees_paid() {
        return $this->hasMany(FeesPaid::class, 'student_id');
    }

    /**
     * Get the center that owns the Students
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function center() {
        return $this->belongsTo(Center::class);
    }

    public function getFullNameAttribute() {
        $user = User::withTrashed()->find($this->user_id);
        return $user->first_name . " " . $user->last_name;
    }

    public function getClassNameAttribute() {

        $class_section = ClassSection::find($this->class_section_id);
        if(!empty($class_section)){
            return $class_section->class->name . " - " . $class_section->section->name;
        }
        return "";
    }

    public function getSessionYearAttribute() {
        if (Auth::user()->hasRole('Center')) {
            $session_year = SessionYear::where('default', 1)->where('center_id', Auth::user()->center->id)->get()->first();
            return $session_year->name;
        }
        return '';

    }

    public function coursesStudent() {
        return $this->belongsToMany(CourseStudent::class, 'course_students', 'course_id', 'student_id');
    }

    /**
     * Get all of the attendance for the Students
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendance_present() {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function attendance_absent() {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function attendance() {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function student_attendance() {
        return $this->hasMany(StudentAttendance::class, 'student_id');
    }

    public function exam_report_student_sequence(){
        return $this->hasOne(ExamReportStudentSequence::class,'student_id');
    }

    public function get_dynamic_field($field_name){
        try {
            $arr = json_decode($this->dynamic_field_values, true);
            foreach ($arr as $entry) {
                if(isset($entry[$field_name])) return $entry[$field_name];
            }
            return "";
        } catch (\Throwable $th) {
            return "";
        }
    }

    public function scopeCurrentSessionYear($query) {
        return $query->whereHas('studentSessions', function ($q) {
            $q->where('session_year_id', getSettings('session_year')['session_year']);
        });
    }

    public function studentSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentSessions::class, 'student_id');
    }


    public function scopeCurrentClassSection($query) {
        $sessionYearId = getSettings('session_year')['session_year'];
        return $query->studentSessions()->where('session_year_id', $sessionYearId)->first()->class_section;
    }
}
