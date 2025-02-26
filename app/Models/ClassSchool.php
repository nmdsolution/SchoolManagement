<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Competency\Competency;
use Illuminate\Database\Eloquent\Model;
use App\Models\Competency\ClassCompetency;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\ClassSchool
 *
 * @property int $id
 * @property string $name
 * @property int $medium_id
 * @property int|null $stream_id
 * @property int|null $shift_id
 * @property int|null $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $report_card_id
 * @property int $report_layout
 * @property string $report_footer_table
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSubject> $allSubjects
 * @property-read int|null $all_subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcement
 * @property-read int|null $announcement_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $boys_attendance
 * @property-read int|null $boys_attendance_count
 * @property-read \App\Models\Center|null $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassGroup> $class_group
 * @property-read int|null $class_group_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSection> $class_section
 * @property-read int|null $class_section_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Competency> $competencies
 * @property-read int|null $competencies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSubject> $coreSubject
 * @property-read int|null $core_subject_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSubject> $electiveSubject
 * @property-read int|null $elective_subject_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ElectiveSubjectGroup> $electiveSubjectGroup
 * @property-read int|null $elective_subject_group_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamResultGroupSubject> $examResultSubjectGroups
 * @property-read int|null $exam_result_subject_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesClass> $fees_class
 * @property-read int|null $fees_class_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $female_student
 * @property-read int|null $female_student_count
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $girls_attendance
 * @property-read int|null $girls_attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $male_student
 * @property-read int|null $male_student_count
 * @property-read \App\Models\Mediums $medium
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @property-read \App\Models\Shift|null $shift
 * @property-read \App\Models\Stream|null $stream
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool activeMediumOnly()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool owner($center_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereReportCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereReportFooterTable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereReportLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereStreamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool withoutTrashed()
 * @mixin \Eloquent
 */
class ClassSchool extends Model {
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'medium_id',
        'stream_id',
        'shift_id',
        'center_id',
    ];

    protected $table = 'classes';
    protected $hidden = ["deleted_at", "created_at", "updated_at"];
    protected $appends = ['full_name'];

    public function announcement() {
        return $this->morphMany(Announcement::class, 'table');
    }

    public function medium() {
        return $this->belongsTo(Mediums::class)->select('name', 'id')->withTrashed();
    }

    public function sections() {
        return $this->belongsToMany(Section::class, 'class_sections', 'class_id', 'section_id')->wherePivot('deleted_at', null);
    }

    public function competencies()
    {
        return $this->belongsToMany(Competency::class, 'class_competency', 'class_id', 'competency_id')
                    ->using(ClassCompetency::class)
                    ->withPivot('cote', 'id')
                    ->withTimestamps();
    }

    public function coreSubject() {
        return $this->hasMany(ClassSubject::class, 'class_id')->where('type', 'Compulsory')->with('subject');
    }

    public function electiveSubject() {
        return $this->hasMany(ClassSubject::class, 'class_id')->where('type', 'Elective')->with('subject', 'subjectGroup');
    }

    public function allSubjects() {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }

//    public function subjects() {
//        return $this->belongsToMany(Subject::class, ClassSubject::class, 'class_id', 'subject_id');
//    }

    public function electiveSubjectGroup() {
        return $this->hasMany(ElectiveSubjectGroup::class, 'class_id');
    }

    public function fees_class() {
        return $this->hasMany(FeesClass::class, 'class_id')->with('fees_type');
    }

    /**
     * Get the center associated with the ClassSchool
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    /**
     * Get the center that owns the ClassSchool
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function center() {
        return $this->belongsTo(Center::class);
    }

    public function class_section() {
        return $this->hasMany(ClassSection::class, 'class_id');
    }

    public function examResultSubjectGroups() {
        return $this->hasMany(ExamResultGroupSubject::class, 'class_id');
    }

    public function stream() {
        return $this->belongsTo(Stream::class, 'stream_id')->select('id', 'name');
    }

    public function shift() {
        return $this->belongsTo(Shift::class, 'shift_id')->select('title', 'id', 'start_time', 'end_time');
    }

    public function scopeOwner($query, $center_id = null) {
        if (Auth::user()->hasRole('Center')) {
            $query->where('center_id', Auth::user()->center->id);
        }

        if (Auth::user()->hasRole('Teacher')) {
            $class_section = SubjectTeacher::where('teacher_id', Auth::user()->teacher->id)->select('class_section_id')->pluck('class_section_id');

            $query->whereIn('id', $class_section);
            if ($center_id) {
                $query->whereIn('center_id', $center_id);
            }
        }
        if (Auth::user()->staff->first()) {
            $query->where('center_id', Session()->get('center_id'));
        }
        return $query;
    }

    public function scopeactiveMediumOnly($query) {
        $activeMedium = getCurrentMedium();
        $query->where('medium_id', $activeMedium->id);
    }

    /**
     * Get all of the student for the ClassSchool
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function male_student() {
        return $this->hasManyThrough(Students::class, ClassSection::class, 'class_id');
    }

    public function female_student() {
        return $this->hasManyThrough(Students::class, ClassSection::class, 'class_id');
    }

    /**
     * Get all of the boys_attendance for the ClassSchool
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function boys_attendance() {
        return $this->hasManyThrough(Attendance::class, ClassSection::class, 'class_id');
    }

    public function girls_attendance() {
        return $this->hasManyThrough(Attendance::class, ClassSection::class, 'class_id');
    }

    public function attendance() {
        return $this->hasManyThrough(Attendance::class, ClassSection::class, 'class_id');
    }

    public function getAttendanceAttribute() {
        $session_year = getSettings('session_year');
        $class = ClassSchool::withCount(['boys_attendance' => function ($q) use ($session_year) {
            $q->where('type', 1)
                ->where('session_year_id', $session_year['session_year'])
                ->whereHas('student.user', function ($q) {
                    $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
                });
        }])
            ->withCount(['girls_attendance' => function ($q) use ($session_year) {
                $q->where('type', 1)
                    ->where('session_year_id', $session_year['session_year'])
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                    });
            }])
            ->withCount(['male_student' => function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
                });
            }])
            ->withCount(['female_student' => function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                });
            }])
            ->withCount(['attendance' => function ($q) {
                $q->select(DB::raw('count(distinct(date))'));
            }])
            ->find($this->id);
        $data = array();

        if ($class->male_student_count && $class->attendance_count && $class->female_student_count) {
            $data['boys_attendance'] = number_format(($class->boys_attendance_count * 100) / ($class->male_student_count * $class->attendance_count), 2);
            $data['girls_attendance'] = number_format(($class->girls_attendance_count * 100) / ($class->female_student_count * $class->attendance_count), 2);
        } else {
            $data['boys_attendance'] = number_format(0, 2);
            $data['girls_attendance'] = number_format(0, 2);
        }
        return $data;
    }

    /**
     * Get all of the class_group for the ClassSchool
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function class_group() {
        return $this->hasMany(ClassGroup::class, 'class_id');
    }

    public function getFullNameAttribute() {
        $name = $this->name;
        if ($this->relationLoaded('stream')) {
            $name .= isset($this->stream->name) ? ' - ' . $this->stream->name : '';
        }
//        if ($this->relationLoaded('medium')) {
//            $name .= ' (' . $this->medium->name . ')';
//        }
        return $name;
    }
}
