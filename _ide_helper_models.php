<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Announcement
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $table_type
 * @property int|null $table_id
 * @property int $session_year_id
 * @property int|null $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Center|null $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $file
 * @property-read int|null $file_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $table
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereTableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Announcement withoutTrashed()
 */
	class Announcement extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AnnualClassDetails
 *
 * @property int $id
 * @property int|null $annual_report_id
 * @property int $class_section_id
 * @property int $student_id
 * @property float $avg
 * @property int $rank
 * @property string $term_avgs
 * @property string $term_ranks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AnnualReport|null $annual_report
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\AnnualReport|null $exam_report
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereAnnualReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereTermAvgs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereTermRanks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassDetails whereUpdatedAt($value)
 */
	class AnnualClassDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AnnualClassSubjectReport
 *
 * @property int $id
 * @property int|null $annual_report_id
 * @property int $class_section_id
 * @property int $subject_id
 * @property float $min
 * @property float $max
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereAnnualReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualClassSubjectReport whereUpdatedAt($value)
 */
	class AnnualClassSubjectReport extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AnnualReport
 *
 * @property int $id
 * @property int $class_section_id
 * @property int|null $class_teacher_id user_id
 * @property int $session_year_id
 * @property int $total_students
 * @property int $male_students
 * @property int $female_students
 * @property float $avg
 * @property int $total_coef
 * @property int $total_points
 * @property string $term_report_ids
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnnualClassDetails> $annual_report_class_detail
 * @property-read int|null $annual_report_class_detail_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnnualClassDetails> $last_student
 * @property-read int|null $last_student_count
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $student
 * @property-read int|null $student_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AnnualClassDetails> $top_student
 * @property-read int|null $top_student_count
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereClassTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereFemaleStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereMaleStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereTermReportIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereTotalCoef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereTotalPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereTotalStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualReport whereUpdatedAt($value)
 */
	class AnnualReport extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AnnualSubjectReport
 *
 * @property int $id
 * @property int|null $annual_report_id
 * @property int $class_section_id
 * @property int $subject_id
 * @property int $student_id
 * @property int $subject_total
 * @property float $subject_avg
 * @property int $subject_rank
 * @property string $subject_grade
 * @property string $subject_remarks
 * @property string $term_marks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\Students $student
 * @property-read \App\Models\Subject $subject
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereAnnualReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereSubjectAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereSubjectGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereSubjectRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereSubjectRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereSubjectTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereTermMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnnualSubjectReport whereUpdatedAt($value)
 */
	class AnnualSubjectReport extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Assignment extends \Eloquent {}
}

namespace App\Models{
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
 */
	class AssignmentSubmission extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Attendance
 *
 * @property int $id
 * @property int $class_section_id
 * @property int $student_id
 * @property int $session_year_id
 * @property int $type 0=Absent, 1=Present
 * @property string $date
 * @property string $remark
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance withoutTrashed()
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AutoSequenceExam
 *
 * @property int $id
 * @property int $exam_sequence_id
 * @property int $class_section_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam query()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam whereExamSequenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoSequenceExam whereUpdatedAt($value)
 */
	class AutoSequenceExam extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Center
 *
 * @property int $id
 * @property string $name
 * @property string $domain
 * @property string $support_contact
 * @property string $support_email
 * @property \Illuminate\Contracts\Routing\UrlGenerator|\Illuminate\Contracts\Foundation\Application|string $logo
 * @property string $tagline
 * @property string $address
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Expense> $expenses
 * @property-read int|null $expenses_count
 * @property-read \App\Models\TimetableTemplate|null $timetableTemplate
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Center newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Center newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Center onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Center query()
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereSupportContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereSupportEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereTagline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Center withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Center withoutTrashed()
 */
	class Center extends \Eloquent {}
}

namespace App\Models{
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
 */
	class CenterTeacher extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ClassGroup
 *
 * @property int $id
 * @property int $group_id
 * @property int $class_id
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\Group $group
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassGroup whereUpdatedAt($value)
 */
	class ClassGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ClassReportCard
 *
 * @property-read \App\Models\ClassSchool $class
 * @property-read mixed $report_name
 * @property-read \App\Models\ReportCard|null $reportCard
 * @method static \Illuminate\Database\Eloquent\Builder|ClassReportCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassReportCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassReportCard query()
 */
	class ClassReportCard extends \Eloquent {}
}

namespace App\Models{
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
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereStreamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSchool withoutTrashed()
 */
	class ClassSchool extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ClassSection
 *
 * @property int $id
 * @property int $class_id
 * @property int $section_id
 * @property int|null $class_teacher_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $absent_attendance
 * @property-read int|null $absent_attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcement
 * @property-read int|null $announcement_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\ClassSchool $class
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSubject> $class_subjects
 * @property-read int|null $class_subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamResult> $exam_result
 * @property-read int|null $exam_result_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamStatistics> $exam_statistic
 * @property-read int|null $exam_statistic_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamStatistics> $exam_statistics
 * @property-read int|null $exam_statistics_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamTimetable> $exam_timetable
 * @property-read int|null $exam_timetable_count
 * @property-read mixed $full_name
 * @property-read mixed $name
 * @property-read \App\Models\Section $section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $student
 * @property-read int|null $student_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubjectTeacher> $subject_teachers
 * @property-read int|null $subject_teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $subjects
 * @property-read int|null $subjects_count
 * @property-read \App\Models\Teacher|null $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection classTeacher()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection owner($center_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection subjectTeacher()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereClassTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSection withoutTrashed()
 */
	class ClassSection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ClassSubject
 *
 * @property int $id
 * @property int $class_id
 * @property string $type Compulsory / Elective
 * @property int $subject_id
 * @property int $weightage
 * @property int|null $elective_subject_group_id if type=Elective
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\ElectiveSubjectGroup|null $subjectGroup
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject subjectTeacher($class_section_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereElectiveSubjectGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassSubject whereWeightage($value)
 */
	class ClassSubject extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Comment
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $course_id
 * @property int|null $comment_id
 * @property string|null $title
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment withoutTrashed()
 */
	class Comment extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Course extends \Eloquent {}
}

namespace App\Models{
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
 */
	class CourseCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CourseSection
 *
 * @property int $id
 * @property int $course_id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $file
 * @property-read int|null $file_count
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSection whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSection whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSection whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSection whereUpdatedAt($value)
 */
	class CourseSection extends \Eloquent {}
}

namespace App\Models{
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
 */
	class CourseStudent extends \Eloquent {}
}

namespace App\Models{
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
 */
	class CourseTeacher extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DefaultTimetable
 *
 * @property int $id
 * @property string $start_time
 * @property string $end_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable query()
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DefaultTimetable whereUpdatedAt($value)
 */
	class DefaultTimetable extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EffectiveDomain
 *
 * @property int $id
 * @property string $name
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $medium_id
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain currentMedium()
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain owner()
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain query()
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EffectiveDomain whereUpdatedAt($value)
 */
	class EffectiveDomain extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ElectiveSubjectGroup
 *
 * @property int $id
 * @property int $total_subjects
 * @property int $total_selectable_subjects
 * @property int $class_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSubject> $electiveSubjects
 * @property-read int|null $elective_subjects_count
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereTotalSelectableSubjects($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereTotalSubjects($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElectiveSubjectGroup whereUpdatedAt($value)
 */
	class ElectiveSubjectGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Event
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $location
 * @property int|null $session_year_id
 * @property int|null $center_id
 * @property int|null $medium_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Event activeMediumOnly()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Exam
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $type 1 = Sequential Exam, 2 = Specific Exam
 * @property int $session_year_id
 * @property int|null $exam_term_id
 * @property int $center_id
 * @property int|null $exam_sequence_id
 * @property int $teacher_status
 * @property int $student_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Center $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSection> $class_section
 * @property-read int|null $class_section_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamClassSection> $exam_class_section
 * @property-read int|null $exam_class_section_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamStatistics> $exam_statistics
 * @property-read int|null $exam_statistics_count
 * @property-read mixed $class_name
 * @property-read mixed $date_between
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamMarks> $marks
 * @property-read int|null $marks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamResult> $results
 * @property-read int|null $results_count
 * @property-read \App\Models\ExamSequence|null $sequence
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\ExamTerm|null $term
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamTimetable> $timetable
 * @property-read int|null $timetable_count
 * @method static \Illuminate\Database\Eloquent\Builder|Exam currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam query()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereExamSequenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereStudentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereTeacherStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereUpdatedAt($value)
 */
	class Exam extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamClassSection
 *
 * @property int $id
 * @property int $exam_id
 * @property int|null $class_id
 * @property int|null $class_section_id
 * @property int $publish 0 => No, 1 => Yes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassSection|null $class_section
 * @property-read \App\Models\Exam $exam
 * @property-read mixed $center_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamTimetable> $timetable
 * @property-read int|null $timetable_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamTimetable> $timetableByClassID
 * @property-read int|null $timetable_by_class_i_d_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamTimetable> $timetableByExamID
 * @property-read int|null $timetable_by_exam_i_d_count
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection owner($center_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection wherePublish($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamClassSection whereUpdatedAt($value)
 */
	class ExamClassSection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamMarks
 *
 * @property int $id
 * @property int $exam_timetable_id
 * @property int $student_id
 * @property int $subject_id
 * @property float $obtained_marks
 * @property string|null $teacher_review
 * @property int $passing_status 1=Pass, 0=Fail
 * @property int $session_year_id
 * @property string|null $grade
 * @property int $exam_result_group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exam> $exam
 * @property-read int|null $exam_count
 * @property-read \App\Models\Students $student
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\ExamTimetable $timetable
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereExamResultGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereExamTimetableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereObtainedMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks wherePassingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereTeacherReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamMarks whereUpdatedAt($value)
 */
	class ExamMarks extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamReport
 *
 * @property int $id
 * @property int $class_section_id
 * @property int|null $class_teacher_id user_id
 * @property int $exam_term_id
 * @property int $session_year_id
 * @property float $avg
 * @property int $total_coef
 * @property int $total_points
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamReportClassDetails> $exam_report_class_detail
 * @property-read int|null $exam_report_class_detail_count
 * @property-read \App\Models\ExamTerm $exam_term
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamReportClassDetails> $last_student
 * @property-read int|null $last_student_count
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $student
 * @property-read int|null $student_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamReportClassDetails> $top_student
 * @property-read int|null $top_student_count
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereClassTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereTotalCoef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereTotalPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReport whereUpdatedAt($value)
 */
	class ExamReport extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamReportClassDetails
 *
 * @property int $id
 * @property int $exam_report_id
 * @property int $student_id
 * @property float|null $total_obtained_points
 * @property int $total_coef
 * @property float|null $avg
 * @property int|null $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExamReport $exam_report
 * @property-read mixed $subject_wise_details
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereExamReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereTotalCoef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereTotalObtainedPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassDetails whereUpdatedAt($value)
 */
	class ExamReportClassDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamReportClassSubject
 *
 * @property int $id
 * @property int $exam_report_id
 * @property int $class_section_id
 * @property int $subject_id
 * @property float $min
 * @property float $max
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereExamReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportClassSubject whereUpdatedAt($value)
 */
	class ExamReportClassSubject extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamReportStudentSequence
 *
 * @property int $id
 * @property int $class_section_id
 * @property int $student_id
 * @property int $exam_term_id
 * @property int $exam_sequence_id
 * @property float $total
 * @property int $total_coef
 * @property float $avg
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExamTerm $examTerm
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereExamSequenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereTotalCoef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSequence whereUpdatedAt($value)
 */
	class ExamReportStudentSequence extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamReportStudentSubject
 *
 * @property int $id
 * @property int $exam_report_id
 * @property int $student_id
 * @property int $subject_id
 * @property int $subject_total
 * @property int $subject_rank
 * @property float $subject_avg
 * @property string|null $subject_grade
 * @property string|null $subject_remarks
 * @property string|null $sequence_marks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ExamReport $examReport
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereExamReportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSequenceMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSubjectAvg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSubjectGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSubjectRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSubjectRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereSubjectTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamReportStudentSubject whereUpdatedAt($value)
 */
	class ExamReportStudentSubject extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamResult
 *
 * @property int $id
 * @property int $exam_id
 * @property int $class_section_id
 * @property int $student_id
 * @property int $total_marks
 * @property int $obtained_marks
 * @property float $percentage
 * @property string $grade
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\Exam $exam
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereObtainedMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereTotalMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResult whereUpdatedAt($value)
 */
	class ExamResult extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamResultGroup
 *
 * @property int $id
 * @property string $name
 * @property int $center_id
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subject> $subjects
 * @property-read int|null $subjects_count
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroup withoutTrashed()
 */
	class ExamResultGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamResultGroupSubject
 *
 * @property int $id
 * @property int $subject_id
 * @property int $class_id
 * @property int|null $exam_result_group_id
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\ExamResultGroup|null $group
 * @property-read \App\Models\Subject $subject
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject owner()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereExamResultGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamResultGroupSubject whereUpdatedAt($value)
 */
	class ExamResultGroupSubject extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamSequence
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @property int $id
 * @property string $name
 * @property int $exam_term_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $status
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AutoSequenceExam> $auto_sequence_exam
 * @property-read int|null $auto_sequence_exam_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSection> $auto_sequence_exam_class_section
 * @property-read int|null $auto_sequence_exam_class_section_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exam> $exam
 * @property-read int|null $exam_count
 * @property-read \App\Models\ExamTerm $term
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence owner($center_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamSequence whereUpdatedAt($value)
 */
	class ExamSequence extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamStatistics
 *
 * @property int $id
 * @property int|null $exam_id
 * @property int|null $class_section_id
 * @property int|null $total_student
 * @property int|null $total_attempt_student
 * @property int|null $pass
 * @property int|null $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSection|null $class_section
 * @property-read \App\Models\Exam|null $exam
 * @property-read mixed $percentage
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics wherePass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereTotalAttemptStudent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereTotalStudent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamStatistics whereUpdatedAt($value)
 */
	class ExamStatistics extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamTerm
 *
 * @property int $id
 * @property string $name
 * @property int $session_year_id
 * @property int $center_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $medium_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamSequence> $sequence
 * @property-read int|null $sequence_count
 * @property-read \App\Models\SessionYear $session_year
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm currentMedium()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm owner($center_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTerm whereUpdatedAt($value)
 */
	class ExamTerm extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExamTimetable
 *
 * @property int $id
 * @property int $exam_id
 * @property int|null $class_id
 * @property int $subject_id
 * @property int $total_marks
 * @property int $passing_marks
 * @property string $date
 * @property string $start_time
 * @property string $end_time
 * @property int $session_year_id
 * @property int $marks_upload_status 0 = Pending , 1 = Submitted , 2 = In progress
 * @property int|null $class_section_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\ClassSection|null $class_section
 * @property-read \App\Models\Exam $exam
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamMarks> $exam_marks
 * @property-read int|null $exam_marks_count
 * @property-read mixed $pendding_subject_marks
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\Subject $subject
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable checkIfSlotAvailable($class_section, $date, $start_time, $end_time, $update_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereMarksUploadStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable wherePassingMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereTotalMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamTimetable whereUpdatedAt($value)
 */
	class ExamTimetable extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Expense
 *
 * @property int $id
 * @property int|null $center_id
 * @property int|null $session_year_id
 * @property string $item_name
 * @property int $qty
 * @property float $amount
 * @property string|null $purchase_by
 * @property string|null $purchase_from
 * @property string $date
 * @property float $total_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Center|null $centers
 * @method static \Illuminate\Database\Eloquent\Builder|Expense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense query()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePurchaseBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense wherePurchaseFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Expense withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Expense withoutTrashed()
 */
	class Expense extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FeesChoiceable
 *
 * @property int $id
 * @property int $student_id
 * @property int $class_id
 * @property int|null $fees_type_id
 * @property int $is_due_charges 0 - no 1 - yes
 * @property float $total_amount
 * @property int $session_year_id
 * @property string|null $date
 * @property int|null $payment_transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $center_id
 * @property-read \App\Models\FeesType|null $fees_type
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereFeesTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereIsDueCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable wherePaymentTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesChoiceable whereUpdatedAt($value)
 */
	class FeesChoiceable extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FeesClass
 *
 * @property int $id
 * @property int $class_id
 * @property int $fees_type_id
 * @property int $choiceable 0 - no 1 - yes
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $center_id
 * @property-read \App\Models\ClassSchool $class
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesPaid> $fees_paid
 * @property-read int|null $fees_paid_count
 * @property-read \App\Models\FeesType $fees_type
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereChoiceable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereFeesTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesClass withoutTrashed()
 */
	class FeesClass extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FeesPaid
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int $student_id
 * @property int $class_id
 * @property int|null $mode 0 - cash 1 - cheque 2 - online
 * @property string|null $payment_transaction_id
 * @property string|null $cheque_no
 * @property float $total_amount
 * @property int $is_fully_paid 0 - no 1 - yes
 * @property string $date
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $center_id
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\PaymentTransaction|null $payment_transaction
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereChequeNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereIsFullyPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid wherePaymentTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesPaid withoutTrashed()
 */
	class FeesPaid extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FeesType
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $choiceable 0 - no 1 - yes
 * @property int|null $center_id
 * @property int|null $medium_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FeesClass> $fees_class
 * @property-read int|null $fees_class_count
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType activeMediumOnly()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereChoiceable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FeesType withoutTrashed()
 */
	class FeesType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\File
 *
 * @property int $id
 * @property string $modal_type
 * @property int $modal_id
 * @property string|null $file_name
 * @property string|null $file_thumbnail
 * @property string $type 1 = File Upload, 2 = Youtube Link, 3 = Video Upload, 4 = Other Link
 * @property string $file_url
 * @property int $downloadable 0 -> No, 1 -> Yes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read mixed $file_extension
 * @property-read mixed $type_detail
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $modal
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Eloquent\Builder|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereDownloadable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFileThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereModalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereModalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUpdatedAt($value)
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FormField
 *
 * @property int $id
 * @property string $name
 * @property string $type text,number,textarea,dropdown,checkbox,radio,fileupload
 * @property int $is_required
 * @property string|null $default_values values of radio,checkbox,dropdown,etc
 * @property string|null $other extra HTML attributes
 * @property int|null $center_id Null = Added By Admin
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|FormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField owner()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField query()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereDefaultValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereOther($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FormField withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FormField withoutTrashed()
 */
	class FormField extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Grade
 *
 * @property int $id
 * @property int $starting_range
 * @property int $ending_range
 * @property string $grade
 * @property int|null $center_id
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $medium_id
 * @method static \Illuminate\Database\Eloquent\Builder|Grade currentMedium()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereEndingRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereStartingRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereUpdatedAt($value)
 */
	class Grade extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Group
 *
 * @property int $id
 * @property string $name
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassGroup> $class_group
 * @property-read int|null $class_group_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSchool> $classes
 * @property-read int|null $classes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereUpdatedAt($value)
 */
	class Group extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Guardian
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $student_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $guardianRelationChild
 * @property-read int|null $guardian_relation_child_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian query()
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Guardian whereUserId($value)
 */
	class Guardian extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Holiday
 *
 * @property int $id
 * @property string $date
 * @property string $title
 * @property string|null $description
 * @property int|null $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday query()
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Holiday whereUpdatedAt($value)
 */
	class Holiday extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Income
 *
 * @property int $id
 * @property string $name
 * @property string|null $invoice_id
 * @property string $quantity
 * @property string $amount
 * @property string $total_amount
 * @property string $date
 * @property int|null $payment_method
 * @property string|null $note
 * @property string|null $attach
 * @property string $purchased_by
 * @property string $purchased_from
 * @property int $category_id
 * @property int $session_year_id
 * @property int $medium_id
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\IncomeCategory $category
 * @property-read \App\Models\Center $center
 * @property-read \App\Models\Mediums $medium
 * @method static \Illuminate\Database\Eloquent\Builder|Income newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Income newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Income owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Income query()
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereAttach($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income wherePurchasedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income wherePurchasedFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Income whereUpdatedAt($value)
 */
	class Income extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\IncomeCategory
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property int $status
 * @property int $medium_id
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Income> $incomes
 * @property-read int|null $incomes_count
 * @property-read \App\Models\Mediums $medium
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory owner()
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IncomeCategory whereUpdatedAt($value)
 */
	class IncomeCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\InstallmentFee
 *
 * @property int $id
 * @property string $name
 * @property string $due_date
 * @property int $due_charges in percentage (%)
 * @property int $session_year_id
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\SessionYear $session_year
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee owner()
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee query()
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereDueCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InstallmentFee withoutTrashed()
 */
	class InstallmentFee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Language
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $file
 * @property int $is_rtl
 * @property int $status 1=>active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereIsRtl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereUpdatedAt($value)
 */
	class Language extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Lesson extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LessonTopic
 *
 * @property int $id
 * @property int $lesson_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $file
 * @property-read int|null $file_count
 * @property-read \App\Models\Lesson $lesson
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic lessonTopicTeachers()
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic query()
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereLessonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LessonTopic whereUpdatedAt($value)
 */
	class LessonTopic extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Mediums
 *
 * @property int $id
 * @property string $name
 * @property int|null $center_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Mediums withoutTrashed()
 */
	class Mediums extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ModelHasRole
 *
 * @property int $role_id
 * @property string $model_type
 * @property int $model_id
 * @method static \Illuminate\Database\Eloquent\Builder|ModelHasRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelHasRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelHasRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModelHasRole whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelHasRole whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModelHasRole whereRoleId($value)
 */
	class ModelHasRole extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OnlineExam
 *
 * @property int $id
 * @property int|null $class_subject_id
 * @property string|null $title
 * @property int|null $exam_key
 * @property int|null $duration in minutes
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassSubject|null $class_subject
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OnlineExamQuestionChoice> $question_choice
 * @property-read int|null $question_choice_count
 * @property-read \App\Models\StudentOnlineExamStatus|null $student_attempt
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereExamKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExam withoutTrashed()
 */
	class OnlineExam extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OnlineExamQuestion
 *
 * @property int $id
 * @property int $class_subject_id
 * @property int $question_type 0 - simple 1 - equation based
 * @property string $question
 * @property string|null $image_url
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OnlineExamQuestionAnswer> $answers
 * @property-read int|null $answers_count
 * @property-read \App\Models\ClassSubject $class_subject
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OnlineExamQuestionOption> $options
 * @property-read int|null $options_count
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereClassSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereQuestionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestion whereUpdatedAt($value)
 */
	class OnlineExamQuestion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OnlineExamQuestionAnswer
 *
 * @property int $id
 * @property int $question_id
 * @property int $answer option id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\OnlineExamQuestionOption $options
 * @property-read \App\Models\OnlineExamQuestionOption $question_option
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionAnswer whereUpdatedAt($value)
 */
	class OnlineExamQuestionAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OnlineExamQuestionChoice
 *
 * @property int $id
 * @property int $online_exam_id
 * @property int $question_id
 * @property int|null $marks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\OnlineExam $online_exam
 * @property-read \App\Models\OnlineExamQuestion $questions
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereMarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereOnlineExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionChoice whereUpdatedAt($value)
 */
	class OnlineExamQuestionChoice extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OnlineExamQuestionOption
 *
 * @property int $id
 * @property int $question_id
 * @property string $option
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamQuestionOption whereUpdatedAt($value)
 */
	class OnlineExamQuestionOption extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OnlineExamStudentAnswer
 *
 * @property int $id
 * @property int $student_id
 * @property int $online_exam_id
 * @property int $question_id online exam question choice id
 * @property int $option_id
 * @property string $submitted_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\OnlineExam $online_exam
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereOnlineExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereSubmittedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OnlineExamStudentAnswer whereUpdatedAt($value)
 */
	class OnlineExamStudentAnswer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaidInstallmentFee
 *
 * @property int $id
 * @property int $class_id
 * @property int $student_id
 * @property int|null $parent_id
 * @property int $installment_fee_id
 * @property int $session_year_id
 * @property float $amount
 * @property float|null $due_charges
 * @property string $date
 * @property int $payment_transaction_id
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\InstallmentFee $installment_fee
 * @property-read \App\Models\Parents|null $parent
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee owner()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereDueCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereInstallmentFeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee wherePaymentTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidInstallmentFee whereUpdatedAt($value)
 */
	class PaidInstallmentFee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Parents
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $gender
 * @property string|null $email
 * @property string|null $mobile
 * @property string|null $occupation
 * @property string|null $image
 * @property string|null $dob
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcement
 * @property-read int|null $announcement_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseStudent> $coursesStudent
 * @property-read int|null $courses_student_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $fatherRelationChild
 * @property-read int|null $father_relation_child_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $guardianRelationChild
 * @property-read int|null $guardian_relation_child_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Students> $motherRelationChild
 * @property-read int|null $mother_relation_child_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Parents newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Parents newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Parents onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Parents owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Parents query()
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Parents withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Parents withoutTrashed()
 */
	class Parents extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PasswordReset
 *
 * @property string $email
 * @property string $token
 * @property string|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereToken($value)
 */
	class PasswordReset extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentTransaction
 *
 * @property int $id
 * @property int $student_id
 * @property int $class_id
 * @property int|null $parent_id
 * @property int $mode 0 - cash 1 - cheque 2 - online
 * @property string|null $cheque_no
 * @property int $type_of_fee 0 - compulsory_full , 1 - installments , 2 -optional
 * @property int|null $payment_gateway 1 - razorpay 2 - stripe
 * @property string|null $order_id order_id / payment_intent_id
 * @property string|null $payment_id
 * @property string|null $payment_signature
 * @property int $payment_status 0 - failed 1 - succeed 2 - pending
 * @property float $total_amount
 * @property string|null $date
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $center_id
 * @property-read \App\Models\ClassSchool $class
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereChequeNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction wherePaymentGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction wherePaymentSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereTypeOfFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentTransaction withoutTrashed()
 */
	class PaymentTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PromoteStudents
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudents newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudents newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoteStudents query()
 */
	class PromoteStudents extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ReportCard
 *
 * @property int $id
 * @property string $name
 * @property int $card_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard whereCardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReportCard whereUpdatedAt($value)
 */
	class ReportCard extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RoleHasPermission
 *
 * @property int $permission_id
 * @property int $role_id
 * @property int|null $medium_id
 * @property-read \Spatie\Permission\Models\Permission $permission
 * @property-read \Spatie\Permission\Models\Role $role
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasPermission whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasPermission wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleHasPermission whereRoleId($value)
 */
	class RoleHasPermission extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Section
 *
 * @property int $id
 * @property string $name
 * @property int|null $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSchool> $classes
 * @property-read int|null $classes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Section onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Section owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Section withoutTrashed()
 */
	class Section extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SessionYear
 *
 * @property int $id
 * @property string $name
 * @property int $default
 * @property string $start_date
 * @property string $end_date
 * @property int $include_fee_installments 0 - no 1 - yes
 * @property string $fee_due_date
 * @property int $fee_due_charges in percentage (%)
 * @property int|null $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Center|null $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InstallmentFee> $fee_installments
 * @property-read int|null $fee_installments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StudentSessions> $studentSessions
 * @property-read int|null $student_sessions_count
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear owner()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear query()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereFeeDueCharges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereFeeDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereIncludeFeeInstallments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionYear withoutTrashed()
 */
	class SessionYear extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Settings
 *
 * @method static where(string $string, string $string1)
 * @property int $id
 * @property string $type
 * @property string $message
 * @property int|null $center_id
 * @property string $data_type text / file
 * @property int|null $medium_id
 * @method static \Illuminate\Database\Eloquent\Builder|Settings currentMedium()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings query()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereType($value)
 */
	class Settings extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Shift
 *
 * @property int $id
 * @property string $title
 * @property string $start_time
 * @property string $end_time
 * @property int $status
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shift withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Shift withoutTrashed()
 */
	class Shift extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Slider
 *
 * @property int $id
 * @property string $image
 * @property string|null $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SliderAccess> $access
 * @property-read int|null $access_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Center> $center_access
 * @property-read int|null $center_access_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $role_access
 * @property-read int|null $role_access_count
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider query()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereUrl($value)
 */
	class Slider extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SliderAccess
 *
 * @property int $id
 * @property int $slider_id
 * @property int $center_id
 * @property int $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess owner()
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess query()
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess whereSliderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SliderAccess whereUpdatedAt($value)
 */
	class SliderAccess extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Staff
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $center_id Super admin staff if NULL
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Center|null $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StaffRole> $staff_role
 * @property-read int|null $staff_role_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Staff newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Staff newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Staff owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Staff query()
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Staff whereUserId($value)
 */
	class Staff extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\StaffRole
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $staff_id
 * @property int|null $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\Permission\Models\Role|null $role
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole whereStaffId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StaffRole whereUserId($value)
 */
	class StaffRole extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Stream
 *
 * @property int $id
 * @property string $name
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Stream newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream query()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Stream withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Stream withoutTrashed()
 */
	class Stream extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\StudentAttendance
 *
 * @property int $id
 * @property int $exam_term_id
 * @property int $class_section_id
 * @property int $student_id
 * @property int $session_year_id
 * @property int $total_absences
 * @property int $justified_absences
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\ExamTerm $exam_term
 * @property-read \App\Models\SessionYear $session_year
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereExamTermId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereJustifiedAbsences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereTotalAbsences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentAttendance whereUpdatedAt($value)
 */
	class StudentAttendance extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\StudentOnlineExamStatus
 *
 * @property int $id
 * @property int $student_id
 * @property int $online_exam_id
 * @property int $status 1 - in progress 2 - completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\OnlineExam $online_exam
 * @property-read \App\Models\Students $student_data
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereOnlineExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentOnlineExamStatus whereUpdatedAt($value)
 */
	class StudentOnlineExamStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\StudentSessions
 *
 * @property int $id
 * @property int $student_id
 * @property int $class_section_id
 * @property int $session_year_id
 * @property int $result 1=>Pass,0=>fail
 * @property int $status 1=>continue,0=>leave
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $repeater
 * @property int $active 1=>Active,0=>Dismissed
 * @property int $promoted 1=>promoted,0=>created
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\SessionYear $sessionYear
 * @property-read \App\Models\Students $student
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions wherePromoted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereRepeater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSessions whereUpdatedAt($value)
 */
	class StudentSessions extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\StudentSubject
 *
 * @property int $id
 * @property int $student_id
 * @property int $subject_id
 * @property int $class_section_id
 * @property int $session_year_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Subject $subject
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject currentSessionYear()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject query()
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereSessionYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudentSubject whereUpdatedAt($value)
 */
	class StudentSubject extends \Eloquent {}
}

namespace App\Models{
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
 * @property string $status
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
 */
	class Students extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Subject
 *
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string $bg_color
 * @property string $image
 * @property int $medium_id
 * @property string $type Theory / Practical
 * @property int|null $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Center|null $center
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSubject> $classSubject
 * @property-read int|null $class_subject_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamMarks> $exam_marks
 * @property-read int|null $exam_marks_count
 * @property-read \App\Models\Mediums $medium
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubjectTeacher> $subject_teachers
 * @property-read int|null $subject_teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Teacher> $teacher
 * @property-read int|null $teacher_count
 * @method static \Illuminate\Database\Eloquent\Builder|Subject activeMediumOnly()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject subjectTeacher()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereBgColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereMediumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subject withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Subject withoutTrashed()
 */
	class Subject extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SubjectTeacher
 *
 * @property int $id
 * @property int $class_section_id
 * @property int $subject_id
 * @property int $teacher_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassSection $class
 * @property-read \App\Models\ClassSection $class_section
 * @property-read \App\Models\ClassSection $section
 * @property-read \App\Models\Subject $subject
 * @property-read \App\Models\Teacher $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher owner($class_section_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher subjectTeacher($class_section_id = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SubjectTeacher withoutTrashed()
 */
	class SubjectTeacher extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Teacher
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $qualification
 * @property string|null $qualification_certificate
 * @property int|null $salary
 * @property int $contact_status 0 -> Not visible, 1 -> Visible
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Announcement> $announcement
 * @property-read int|null $announcement_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CenterTeacher> $center_teacher
 * @property-read int|null $center_teacher_count
 * @property-read \App\Models\ClassSection|null $class_section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClassSection> $class_sections
 * @property-read int|null $class_sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubjectTeacher> $classes
 * @property-read int|null $classes_count
 * @property-read mixed $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubjectTeacher> $subjects
 * @property-read int|null $subjects_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher owner()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher teachers()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereContactStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereQualification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereQualificationCertificate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereSalary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher withoutTrashed()
 */
	class Teacher extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Timetable
 *
 * @property int $id
 * @property int $subject_teacher_id
 * @property int $class_section_id
 * @property string $start_time
 * @property string $end_time
 * @property string|null $note
 * @property int $day 1=monday,2=tuesday,3=wednesday,4=thursday,5=friday,6=saturday,7=sunday
 * @property string $day_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\ClassSection $class_section
 * @property-read mixed $center_name
 * @property-read \App\Models\SubjectTeacher $subject
 * @property-read \App\Models\SubjectTeacher $subject_teacher
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable query()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereClassSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereDayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereSubjectTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Timetable withoutTrashed()
 */
	class Timetable extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TimetableTemplate
 *
 * @property int $id
 * @property array $periods
 * @property int $center_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Center|null $center
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate whereCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate wherePeriods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TimetableTemplate whereUpdatedAt($value)
 */
	class TimetableTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $gender
 * @property string|null $email
 * @property string|null $fcm_id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $mobile
 * @property string|null $image
 * @property string|null $dob
 * @property string|null $born_at
 * @property string|null $current_address
 * @property string|null $permanent_address
 * @property int $status
 * @property int $reset_request
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CenterTeacher> $center_teacher
 * @property-read int|null $center_teacher_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read mixed $birth_date
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Guardian> $guardian
 * @property-read int|null $guardian_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Parents|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Staff> $staff
 * @property-read int|null $staff_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StaffRole> $staff_role
 * @property-read int|null $staff_role_count
 * @property-read \App\Models\Students|null $student
 * @property-read \App\Models\Teacher|null $teacher
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBornAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrentAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDob($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFcmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePermanentAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereResetRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

