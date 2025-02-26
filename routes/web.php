<?php


use App\Models\Settings;
use App\Yadiko\Exam\Download\DownloadExamReport\UserInterface\Http\BulkDownloadReportController;
use App\Yadiko\Exam\ExamReport\UserInterface\Http\ListExamReportController;
use App\Yadiko\Exam\ExamTerm\UserInterfae\Http\ListExamTermController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\CenterController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\MediumController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ParentsController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\ExamTermController;
use App\Http\Controllers\FeesTypeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\FormFieldController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClassGroupController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExamReportController;
use App\Http\Controllers\OnlineExamController;
use App\Http\Controllers\ClassSchoolController;
use App\Http\Controllers\LessonTopicController;
use App\Http\Controllers\SessionYearController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AnnualReportController;
use App\Http\Controllers\ClassTeacherController;
use App\Http\Controllers\ExamSequenceController;
use App\Http\Controllers\GlobalReportController;
use App\Http\Controllers\SuperTeacherController;
use App\Http\Controllers\SystemUpdateController;
use App\Http\Controllers\ExamTimetableController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\StudentSessionController;
use App\Http\Controllers\SubjectTeacherController;
use App\Http\Controllers\CompetencyCloneController;
use App\Http\Controllers\ExamResultGroupController;
use App\Http\Controllers\OnlineExamQuestionController;
use App\Http\Controllers\Competency\CompetencyController;
use App\Http\Controllers\Competency\ObservationController;
use App\Http\Controllers\Competency\LearningUnitController;
use App\Http\Controllers\Competency\CompetencyTypeController;
use App\Http\Controllers\Competency\ClassCompetencyController;
use App\Http\Controllers\Competency\CompetencyMarksController;
use App\Http\Controllers\AnnualProject\AnnualProjectController;
use App\Http\Controllers\Competency\CompetencyDomainController;
use App\Http\Controllers\Competency\CompetencyReportController;
use App\Http\Controllers\FeesDiscountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::post('/check-login', [LoginController::class, 'login'])->name('post.login');

Route::get('/', [HomeController::class, 'login'])->name('login');

Route::group(['middleware' => ['Role', 'auth:sanctum']], static function () {
    Route::group(['middleware' => ['language', 'optimizeImages']], static function () {
        Route::get('/', [HomeController::class, 'index'])->name('dashboard');

        Route::get('unpublish-exam-result', [ExamController::class, 'unpublish_exam_result']);
        Route::get('pendding-exam-result', [ExamController::class, 'pendding_exam_result']);
        Route::get('upcoming-exam', [ExamController::class, 'upcoming_exam']);
        Route::get('top-student-list', [ExamController::class, 'top_student_list']);

        Route::get('upcoming-events', [EventController::class, 'upcoming_events']);
        Route::get('upcoming-holiday', [HolidayController::class, 'upcoming_holiday']);

        Route::get('home', [HomeController::class, 'index'])->name('home');
        Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
        Route::get('subject-by-class-section', [HomeController::class, 'getSubjectByClassSection'])->name('class-section.by.subject');
        Route::get('teacher-by-class-subject', [HomeController::class, 'getTeacherByClassSubject'])->name('teacher.by.class.subject');
        ///new reset password controller
        Route::get('home/reset_password', [HomeController::class, 'resetPasswordView']);
        Route::resource('roles', RoleController::class);
        Route::get('role_list', [RoleController::class, 'role_list']);
        Route::get('user/role/edit/{id?}', [RoleController::class, 'edit']);
        Route::get('user/role/show/{id?}', [RoleController::class, 'show']);
        Route::get('role/delete/{id}', [RoleController::class, 'destroy']);
        Route::resource('users', UserController::class);
        Route::get('user_list', [UserController::class, 'user_list']);
        Route::get('/user/search', [UserController::class, 'search']);

        Route::get('settings', [SettingController::class, 'index']);
        Route::post('settings', [SettingController::class, 'update']);
        Route::get('settings/change-session-year', [SettingController::class, 'changeSessionYear']);

        Route::get('fcm-settings', [SettingController::class, 'fcm_index']);
        Route::post('fcm-settings', [SettingController::class, 'fcm_update']);

        Route::resource('medium', MediumController::class);
        Route::get('medium_list', [MediumController::class, 'show']);

        Route::get('teachers/search/', [TeacherController::class, 'search']);
        Route::get('teachers/generate-email/', [TeacherController::class, 'search']);
        Route::resource('teachers', TeacherController::class);
        Route::get('teacher_list', [TeacherController::class, 'show']);
        Route::post('teacher/upload-bulk-data', [TeacherController::class, 'store_bulk_data']);
        Route::get('teacher/generate-email', [TeacherController::class, 'generateEmail']);
        Route::get('teacher/reset-password', [TeacherController::class, 'resetPasswordIndex'])->name('teacher.reset-password.index');
        Route::get('teacher/reset-password-list', [TeacherController::class, 'resetPasswordShow']);


        Route::resource('section', SectionController::class);
        Route::get('section_list', [SectionController::class, 'show']);

        Route::get('class/subject/list', [ClassSchoolController::class, 'subject_list'])->name('class.subject.list');
        Route::get('class/subject', [ClassSchoolController::class, 'subject'])->name('class.subject');
        Route::get('class/subject/{class_id}', [ClassSchoolController::class, 'updateSubject'])->name('class.subject.edit');
        Route::put('class/subject/{class_id}', [ClassSchoolController::class, 'update_subjects'])->name('class.subject.update');
        Route::delete('class/subject/{class_subject_id}', [ClassSchoolController::class, 'subject_destroy'])->name('class.subject.delete');
        Route::delete('class/subject-group/{group_id}', [ClassSchoolController::class, 'subject_group_destroy'])->name('class.subject-group.delete');
        Route::get('class-list', [ClassSchoolController::class, 'show']);
        Route::get('class-report-edit', [ClassSchoolController::class, 'assignClassReport'])->name('class.report-edit');

        Route::get('get/section/{id?}', [ClassSchoolController::class, 'get_section']);

        Route::resource('class', ClassSchoolController::class);

        Route::get('/class-section/rename/{id}', [ClassSchoolController::class, 'renameClassSection'])->name('class.renameView');

        Route::post('/class-section/rename', [ClassSchoolController::class, 'transferClassSection'])->name('class.rename');

        Route::get('class-subject-list', [ClassSchoolController::class, 'getSubjectsByMediumId']);

        Route::get('assign/class/teacher', [ClassTeacherController::class, 'teacher'])->name('class.teacher');
        Route::post('class/teacher/store', [ClassTeacherController::class, 'assign_teacher'])->name('class.teacher.store');
        Route::get('class-teacher-list', [ClassTeacherController::class, 'show']);
        Route::post('remove-class-teacher/{id}', [ClassTeacherController::class, 'removeClassTeacher']);


        Route::get('/super-teacher', [SuperTeacherController::class, 'index'])->name('super.teacher.index');
        Route::post('/super-teacher/add', [SuperTeacherController::class, 'create'])->name('super.teacher.add');
        Route::get('/super-teacher-list', [SuperTeacherController::class, 'show'])->name('super-teacher-list');
        Route::get('/super-teacher/{id}', [SuperTeacherController::class, 'edit']);
        Route::put('/super-teacher/{id}', [SuperTeacherController::class, 'update'])->name('super-teacher-update');
        Route::delete('/super-teacher-delete/{user_id}', [SuperTeacherController::class, 'delete'])->name('super-teacher-delete');

        Route::resource('subject', SubjectController::class);
        Route::get('subject-list', [SubjectController::class, 'show']);

        Route::get('/parent/search', [ParentsController::class, 'search']);
        Route::resource('parents', ParentsController::class);
        Route::get('parents_list', [ParentsController::class, 'show']);

        Route::resource('session-years', SessionYearController::class);
        Route::get('session_years_list', [SessionYearController::class, 'show']);
        Route::delete('remove-installment-data/{id}',[SessionYearController::class, 'deleteInstallmentData']);

        Route::get('students-list', [StudentController::class, 'show'])->name('students.list');
        Route::get('students-list-by-group', [StudentController::class, 'listByGroup'])->name('students.list_by_group');
        Route::get('students/assign-class', [StudentController::class, 'assignClass'])->name('students.assign-class');
        Route::post('students/assign-class', [StudentController::class, 'assignClass_store'])->name('students.assign-class.store');
        Route::get('students/transfer-student-list', [StudentController::class, 'transferStudentList'])->name('students.transfer-student-list');
        Route::get('students/create_bulk', [StudentController::class, 'createBulkData'])->name('students.create-bulk-data');
        Route::post('students/store_bulk', [StudentController::class, 'storeBulkData'])->name('students.store-bulk-data');
        Route::get('students/generate-id-card', [StudentController::class, 'generate_id_card']);
        Route::get('download/student/sample/file', [StudentController::class, 'student_sample_file']);

        Route::get('new-students', [StudentController::class, 'groupNewStudents'])->name('group-new-students');

        Route::get('student/school/certificate/{id}', [StudentController::class, 'school_certificate']);

        Route::get('get-students', [StudentController::class, 'get_student']);
        Route::get('/get-student-counts', [StudentController::class, 'getStudentCounts']);

        Route::get('/students/print-sex-age-stats', [StudentController::class, 'printStudentStats'])->name('print_sex_age_stats');
        Route::get('/students/print-sex-stats', [StudentController::class, 'printStudentSexStats'])->name('print_sex_stats');

        Route::get('/students/grouped-sex-stats', [StudentController::class, 'printGroupedStudentSexStats'])->name('grouped_sex_stats');
        Route::get('/students/print-grouped-sex-age-stats', [StudentController::class, 'printGroupedStudentStats'])->name('print_grouped_sex_age_stats');

        Route::resource('students', StudentController::class);

        Route::delete('/bulk-delete-students', [StudentController::class, 'bulkDelete'])->name('students.bulk-delete');

        Route::get('get-gender-data', [StudentController::class, 'boys_girls_counter']);
        Route::get('class-group-boys-grils', [StudentController::class, 'boys_girls_class_group_wise']);


        // Route::post('student/generate-id-card', [StudentController::class, 'view_generate_id_card']);

        Route::post('students/generate-id-card', [StudentController::class, 'view_generate_id_card']);

        //student generate roll number
        Route::get('student/assign-roll-number', [StudentController::class, 'indexStudentRollNumber'])->name('students.index-students-roll-number');
        Route::get('student/list-assign-roll-number', [StudentController::class, 'listStudentRollNumber'])->name('students.list-students-roll-number');
        Route::post('student/store-roll-number', [StudentController::class, 'storeStudentRollNumber'])->name('students.store-roll-number');

        Route::resource('subject-teachers', SubjectTeacherController::class);
        Route::get('subject-teachers-list', [SubjectTeacherController::class, 'show']);

        Route::get('timetable/settings', [TimetableController::class, 'settings'])->name('timetable.settings');
        Route::resource('timetable', TimetableController::class);
        Route::get('timetable-list', [TimetableController::class, 'show']);
        Route::get('checkTimetable', [TimetableController::class, 'checkTimetable']);
        Route::post('store-template', [TimetableController::class, 'storeTemplate']);

        Route::get('get-subject-by-class-section', [TimetableController::class, 'getSubjectByClassSection']);
        Route::get('getteacherbysubject', [TimetableController::class, 'getteacherbysubject']);

        Route::get('gettimetablebyclass', [TimetableController::class, 'gettimetablebyclass'])->name('get.timetable.class');
        Route::get('gettimetablebyteacher', [TimetableController::class, 'gettimetablebyteacher']);
        Route::get('get-timetable-by-subject-teacher-class', [TimetableController::class, 'getTimetableBySubjectTeacherClass']);

        Route::get('class-timetable', [TimetableController::class, 'class_timetable']);
        Route::get('teacher-timetable', [TimetableController::class, 'teacher_timetable']);

        Route::resource('attendance', AttendanceController::class);
        Route::get('view-attendance', [AttendanceController::class, 'view'])->name("attendance.view");
        Route::get('student-attendance-list', [AttendanceController::class, 'attendance_show']);
        Route::get('getAttendanceData', [AttendanceController::class, 'getAttendanceData']);
        Route::get('attendance-report/{class_section_id}/{student_id?}', [AttendanceController::class, 'report']);
        Route::get('student-attendance-report', [AttendanceController::class, 'attendance_report']);

        Route::get('student-list', [AttendanceController::class, 'show']);

        Route::resource('lesson', LessonController::class);
        Route::get('search-lesson', [LessonController::class, 'search']);
        Route::delete('file/delete/{id}', [LessonController::class, 'deleteFile'])->name('file.delete');
        Route::resource('lesson-topic', LessonTopicController::class);

        Route::resource('announcement', AnnouncementController::class);
        Route::get('getAssignData', [AnnouncementController::class, 'getAssignData']);

        Route::resource('holiday', HolidayController::class);
        Route::get('holiday-list', [HolidayController::class, 'show']);
        Route::get('holiday-view', [HolidayController::class, 'holiday_view']);

        Route::resource('assignment', AssignmentController::class);
        Route::get('assignment-submission', [AssignmentController::class, 'viewAssignmentSubmission'])->name('assignment.submission');
        Route::put('assignment-submission/{id}', [AssignmentController::class, 'updateAssignmentSubmission'])->name('assignment.submission.update');
        Route::get('assignment-submission-list', [AssignmentController::class, 'assignmentSubmissionList'])->name('assignment.submission.list');

        Route::resource('sliders', SliderController::class);

        Route::resource('exam-terms', ExamTermController::class);
        Route::resource('List_exam-terms', ListExamTermController::class);

        Route::post('exam-terms/{id}/add-sequence', [ListExamTermController::class, 'addSequence']);

        Route::get('exam-sequence-mark', [ExamSequenceController::class, 'sequenceWiseMarksIndex']);
        Route::get('sequence-exam-student', [ExamSequenceController::class, 'sequenceWiseMarksList']);
        Route::put('sequence-exam-mark-update', [ExamSequenceController::class, 'sequenceWiseMarksUpdate']);

        Route::resource('exam-sequences', ExamSequenceController::class);
        Route::post('exam-sequences/{id}/status', [ExamSequenceController::class, 'updateStatus']);
        Route::get('/sequence-classes/{id}', [ExamSequenceController::class, 'getSequenceClasses'])->name('sequence.classes');

        Route::get('exams/exam-result', [ExamController::class, 'getExamResultIndex'])->name('exams.get-result');
        Route::get('exams/show-result', [ExamController::class, 'showExamResult'])->name('exams.show-result');
        Route::post('exams/update-result-marks', [ExamController::class, 'updateExamResultMarks'])->name('exams.update-result-marks');

        Route::post('exams/submit-marks', [ExamController::class, 'submitMarks'])->name('exams.submit-marks');

        Route::get('exams/specific/upload-marks', [ExamController::class, 'uploadSpecificExamMarks'])->name('exams.specific.upload-marks');
        Route::get('exams/sequential/upload-marks', [ExamController::class, 'uploadSequentialExamMarks'])->name('exams.sequential.upload-marks');
        Route::post('exams/update-total-marks', [ExamTimetableController::class, 'updateTotalMarks'])->name('exams.update-total-marks');
        Route::get('exams/marks-list', [ExamController::class, 'marksList'])->name('exams.marks-list');
        //Route::get('exams/marks-status', [ExamController::class, 'makrsStatusIndex'])->name('exams.marks.status');

        Route::get('exams/get-subjects/{exam_id}', [ExamController::class, 'getSubjectByExam'])->name('exams.subject');
        Route::post('exams/publish/{id}', [ExamController::class, 'publishExamResult'])->name('exams.publish');


        Route::post('exams/subject-teacher/create', [ExamController::class, 'storeTeacherExam'])->name('exams.publish');

        Route::get('exams/report', [ExamController::class, 'exam_report'])->name('exams.get-report');
        Route::get('exams/report/top/students/list', [ExamController::class, 'exam_report_top_students'])->name('top-students-list');

        Route::get('fail-students-list', [ExamController::class, 'fail_student_list']);

        Route::get('get-top-students', [ExamController::class, 'top_students']);

        Route::get('exam-overview', [ExamController::class, 'exam_overview']);
        Route::get('attendance-overview', [AttendanceController::class, 'attendance_overview']);


        Route::get('exam/get-class/{id?}', [ExamController::class, 'get_class']);
        Route::get('subject/highest-lowest/{exam_id?}/{class_id?}/{section_id?}/{top_student_class_group_id?}', [ExamController::class, 'subject_wise_highest_lowest']);


        Route::post('exams/subject-teacher/create', [ExamController::class, 'storeTeacherExam']);
        Route::get('exams/report/{id}/{termID}', [ExamController::class, 'getExamReportIndex'])->name('exam-report.index');
        Route::get('overall/result/{exam_id?}/{class_group_id?}', [ExamController::class, 'overall_result'])->name('overall_result');
        Route::get('class-wise-report', [ExamController::class, 'class_wise_report']);

        Route::get('exams/sequential', [ExamController::class, 'sequentialIndex'])->name('exams.sequential.index');
        Route::post('exams/sequential/store', [ExamController::class, 'sequentialExamStore'])->name('exams.sequential.store');
        Route::get('exams/sequential/list', [ExamController::class, 'sequentialShow'])->name('exams.sequential.show');
        Route::put('exams/sequential/update', [ExamController::class, 'sequentialUpdate'])->name('exams.sequential.update');
        Route::resource('exams', ExamController::class);

        Route::post('exams/update-timetable', [ExamTimetableController::class, 'updateTimetable'])->name('exams.update-timetable');
        Route::delete('exams/delete-timetable/{id}', [ExamTimetableController::class, 'deleteTimetable'])->name('exams.delete-timetable');
        Route::get('grades', [ExamController::class, 'indexGrades'])->name('grades');

        Route::get('exams/get-exam-subjects/{exam_id}/{class_section_id}', [ExamController::class, 'getExamSubjects'])->name('exams.subjects');

        Route::post('create-grades', [ExamController::class, 'createGrades'])->name('create-grades');
        Route::get('show-grades', [ExamController::class, 'showGrades'])->name('show-grades');
        Route::put('update-grades/{grade_id}', [ExamController::class, 'updateGrades'])->name('update-grades');
        Route::delete('destroy-grades/{grade_id}', [ExamController::class, 'destroyGrades'])->name('destroy-grades');

        Route::resource('exam-timetable', ExamTimetableController::class);
        Route::get('exam/get-classes/{exam_id}', [ExamTimetableController::class, 'getClassesByExam'])->name('exams.classes');
        Route::get('exam/get-subjects/{class_id}', [ExamTimetableController::class, 'getSubjectsByClass'])->name('exams.class-subjects');
        Route::get('exam/result-subject-group/assigned-list', [ExamResultGroupController::class, 'assignClassSubjectGroupList'])->name('exam.subject-group.assigned-list');
        Route::put('exam/result-subject-group/subject', [ExamResultGroupController::class, 'assignClassSubjectToGroup'])->name('exam.subject-group.assigned');
        Route::delete('exam/result-subject-group/subject', [ExamResultGroupController::class, 'deleteClassSubjectFromGroup'])->name('exam.subject-group.assigned');
        Route::resource('exam/result-subject-group', ExamResultGroupController::class);

        Route::resource('exam-report', ExamReportController::class);
        Route::resource('list_exam-report', ListExamReportController::class);
        Route::get('exam-report-bulk-download/{term_id}/{class_section_id}/{payment_status?}', [BulkDownloadReportController::class, 'bulkDownloadExamReports'])->name('exam-report-bulk-dowload');
        Route::get('exams/report/{id}/term/{termID}/student/{studentID}', [ExamReportController::class, 'downloadExamReport'])->name('exam-report-temp.index');
        Route::get('exams/report/{id}/term/{termID}/student/{studentID}/view', [ExamReportController::class, 'viewExamReport'])->name('exam-report-view');

        Route::resource('annual-report', AnnualReportController::class);
        Route::get('exams/report/annual/class/{classSectionId}/download', [AnnualReportController::class, 'bulkDownloadReports'])->name('annual-report-bulk-dowload');
        Route::get('exams/report/annual/{id}/student/{studentID}', [AnnualReportController::class, 'downloadExamReport'])->name('annual-report-download');

        Route::get('email-settings', [SettingController::class, 'email_index'])->name('setting.email-config-index');
        Route::post('email-settings', [SettingController::class, 'email_update']);
        Route::post('verify-email-settings', [SettingController::class, 'verifyEmailConfigration'])->name('setting.varify-email-config');

        Route::get('privacy-policy', [SettingController::class, 'privacy_policy_index']);
        Route::get('terms-condition', [SettingController::class, 'terms_condition_index']);
        Route::get('contact-us', [SettingController::class, 'contact_us_index']);
        Route::get('about-us', [SettingController::class, 'about_us_index']);

        Route::post('setting-update', [SettingController::class, 'setting_page_update']);

        Route::get('report-settings', [SettingController::class, 'reportSettingIndex'])->name('report-settings.index');
        Route::post('report-settings', [SettingController::class, 'reportSettingUpdate'])->name('report-settings.update');
        Route::delete('settings/delete/effective-domain/{id}', [SettingController::class, 'destroyEffectiveDomain'])->name('settings.effective-domain');

        Route::get('reset-password', static function () {
            return view('students.reset_password');
        })->name('students.reset_password');
        Route::get('reset-password-list', [StudentController::class, 'reset_password']);
        Route::post('student-change-password', [StudentController::class, 'change_password']);

        Route::resource('promote-student', StudentSessionController::class);
        Route::get('getPromoteData', [StudentSessionController::class, 'getPromoteData']);
        Route::get('promote-student-list', [StudentSessionController::class, 'show']);

        Route::get('/student-session/{id}', [StudentSessionController::class, 'deleteStudentSession']);

        Route::get('promoted-student', [StudentSessionController::class, 'promoted_student']);
        Route::get('promoted-student-list', [StudentSessionController::class, 'promoted_student_list']);


        Route::get('resetpassword', [HomeController::class, 'resetpassword'])->name('resetpassword');
        Route::get('checkPassword', [HomeController::class, 'checkPassword']);
        Route::post('changePassword', [HomeController::class, 'changePassword']);

        Route::get('edit-profile', [HomeController::class, 'editProfile'])->name('edit -profile');
        Route::post('update-profile', [HomeController::class, 'updateProfile'])->name('update-profile');

        Route::get('profile', [UserController::class, 'profile']);
        Route::put('profile/{id}', [UserController::class, 'update_profile'])->name('profile');

        Route::get('change-password', [UserController::class, 'change_password']);
        Route::post('change-password', [UserController::class, 'update_password']);


        Route::resource('language', LanguageController::class);
        Route::get('language-sample', [LanguageController::class, 'language_sample']);
        Route::get('language-list', [LanguageController::class, 'show']);

        Route::get('set-language/{lang}', [LanguageController::class, 'set_language']);
        Route::get('sendtest', [SettingController::class, 'test_mail']);

        // fees
        Route::resource('fees-type', FeesTypeController::class);

        Route::get('fees/classes', [FeesTypeController::class, 'feesClassListIndex'])->name('fees.class.index');
        Route::get('fees/summary', [FeesTypeController::class, 'feeStatusSummary'])->name('fees-summary');
        Route::get('fees/classes/list', [FeesTypeController::class, 'feesClassList'])->name('fees.class.list');
        Route::post('class/fees-type', [FeesTypeController::class, 'updateFeesClass'])->name('class.fees.type.update');
        Route::delete('class/fees-type/{fees_class_id}', [FeesTypeController::class, 'removeFeesClass'])->name('class.fees.type.delete');

        Route::get('fees/paid', [FeesTypeController::class, 'feesPaidListIndex'])->name('fees.paid.index');
        Route::get('fees/paid/list', [FeesTypeController::class, 'feesPaidList'])->name('fees.paid.list');
        Route::post('fees/paid/store', [FeesTypeController::class, 'feesPaidStore'])->name('fees.paid.store');

        Route::put('fees/paid/update/{id}', [FeesTypeController::class, 'feesPaidUpdate'])->name('fees.paid.udpate');
        Route::delete('fees/paid/remove-choiceable-fees/{id}', [FeesTypeController::class, 'feesPaidRemoveChoiceableFees'])->name('fees.paid.remove.choiceable.fees');
        Route::delete('fees/paid/remove-installment-fees/{id}', [FeesTypeController::class, 'feesPaidRemoveInstallmentFees'])->name('fees.paid.remove.installment.fees');
        Route::delete('fees/paid/clear-data/{id}', [FeesTypeController::class, 'clearFeesPaidData'])->name('fees.paid.clear.data');

        Route::post('fees/optional-paid/store', [FeesTypeController::class, 'optionalFeesPaidStore'])->name('fees.optional-paid.store');
        Route::post('fees/compulsory-paid/store', [FeesTypeController::class, 'compulsoryFeesPaidStore'])->name('fees.compulsory-paid.store');

        Route::get('fees/transaction-logs', [FeesTypeController::class, 'feesTransactionsLogsIndex'])->name('fees.transactions.log.index');
        Route::get('fees/transaction-logs/list', [FeesTypeController::class, 'feesTransactionsLogsList'])->name('fees.transactions.log.list');

        Route::get('fees/paid/receipt-pdf/{id}', [FeesTypeController::class, 'feesPaidReceiptPDF'])->name('fees.paid.receipt.pdf');
        Route::get('fees/fees-receipt', static function () {
            return view('fees.fees_receipt');
        })->name('fees.receipt');

        Route::get('fees-discounts', [FeesDiscountController::class, 'index'])->name('fees.discounts.index');
        Route::post('fees-discounts', [FeesDiscountController::class, 'store'])->name('fees.discounts.store');
        Route::get('fees-discounts/list', [FeesDiscountController::class, 'show'])->name('fees.discounts.show');
        Route::put('fees-discounts/{id}', [FeesDiscountController::class, 'update'])->name('fees.discounts.update');
        Route::delete('fees-discounts/{id}', [FeesDiscountController::class, 'destroy'])->name('fees.discounts.destroy');
        Route::post('fees-discounts/toggle/{id}', [FeesDiscountController::class, 'toggleStatus'])->name('fees.discounts.toggle');

        // Online Exam
        Route::get('online-exam/terms-conditions', [OnlineExamController::class, 'onlineExamTermsConditionIndex'])->name('online-exam.terms-conditions');
        Route::post('online-exam/store-terms-conditions', [OnlineExamController::class, 'storeOnlineExamTermsCondition'])->name('online-exam.store-terms-conditions');

        Route::resource('online-exam', OnlineExamController::class);
        Route::post('online-exam/add-new-question', [OnlineExamController::class, 'storeExamQuestionChoices'])->name('online-exam.add-new-question');
        Route::get('online-exam/get-class-subject-questions/{id}', [OnlineExamController::class, 'getClassSubjectQuestions'])->name('online-exam-question.get-class-subject-questions');
        Route::get('get-subject-online-exam', [OnlineExamController::class, 'getSubjects']);
        Route::get('get-exam-question-index/{id?}', [OnlineExamController::class, 'examQuestionsIndex'])->name('exam.questions.index');
        Route::post('online-exam/store-questions-choices', [OnlineExamController::class, 'storeQuestionsChoices'])->name('online-exam.store-choice-question');
        Route::delete('online-exam/remove-choiced-question/{id}', [OnlineExamController::class, 'removeQuestionsChoices'])->name('online-exam.remove-choice-question');
        Route::get('online-exam/result/{id}', [OnlineExamController::class, 'onlineExamResultIndex'])->name('online-exam.result.index');
        Route::get('online-exam/result-show/{id}', [OnlineExamController::class, 'showOnlineExamResult'])->name('online-exam.result.show');

        Route::resource('online-exam-question', OnlineExamQuestionController::class);
        Route::delete('online-exam-question/remove-option/{id}', [OnlineExamQuestionController::class, 'removeOptions']);
        Route::delete('online-exam-question/remove-answer/{id}', [OnlineExamQuestionController::class, 'removeAnswers']);
        // End Online Exam Routes

        Route::get('app-settings', [SettingController::class, 'app_index']);
        Route::post('app-settings', [SettingController::class, 'app_update']);
        Route::get('system-update', [SystemUpdateController::class, 'index'])->name('system-update.index');
        Route::post('system-update', [SystemUpdateController::class, 'update'])->name('system-update.update');

        Route::patch('centers/status/{id}', [CenterController::class, 'statusChange']);
        Route::get('set-user-center/{id?}', [CenterController::class, 'set_user_center']);
        // set_user_center

        Route::get('centers/clone', [CenterController::class, 'centerClone'])->name('centers.clone');
        Route::post('centers/clone', [CenterController::class, 'cloneCenter'])->name('centers.clone.store');

        Route::resource('centers', CenterController::class);

        Route::get('import-form-fields/index', [FormFieldController::class, 'importIndex'])->name('form-fields.import.index');
        Route::get('import-form-fields/show', [FormFieldController::class, 'importShow'])->name('form-fields.import.show');
        Route::post('import-form-fields/import/{id}', [FormFieldController::class, 'importStore'])->name('form-fields.import.store');
        Route::patch('form-fields/change-rank', [FormFieldController::class, 'changeRank']);
        Route::resource('form-fields', FormFieldController::class);

        Route::get('class-section-by-center', [ClassSchoolController::class, 'class_section_by_center'])->name('class-section-by-center');
        Route::get('class-by-center', [ClassSchoolController::class, 'class_by_center']);
        Route::get('set-center/{id?}', [CenterController::class, 'set_center']);

        Route::get('class-report', [ClassSchoolController::class, 'class_report']);
        Route::get('master-sheet/{report_id}', [ExamReportController::class, 'viewMasterSheet'])->name('view-master-sheet');
        Route::get('master-sheet/{report_id}/seq/{seq_id}', [ExamReportController::class, 'viewMasterSheet'])->name('view-seq-master-sheet');
        Route::get('class-report/{report_id}', [ExamController::class, 'class_report'])->whereNumber('report_id');
        Route::get('annual-class-report/{report_id}', [ExamController::class, 'annual_class_report'])->name('annual-class-report');
        Route::get('class-section-list', [ClassSchoolController::class, 'class_section_list']);
        Route::get('student-honor-roll/{report_id}', [ExamController::class, 'honor_roll']);
        Route::get('honor-roll-student-list/{report_id}', [ExamController::class, 'honor_roll_student_list']);
        Route::get('student-honor-roll-file/{id}', [ExamController::class, 'honor_roll_certificate']);
        Route::post('students/honor-roll-certificates', [ExamController::class, 'student_honor_roll_certificate']);

        Route::get('annual-master-sheet', [ClassSchoolController::class, 'annual_report'])->name('annual-master-sheets');
        Route::get('list-annual-master-sheet', [ClassSchoolController::class, 'list_annual_reports'])->name('annual-report-list');
        Route::get('annual-master-sheet/{report_id}', [ExamReportController::class, 'viewAnnualMasterSheet'])->name('view-annual-master-sheet');
        

        Route::get('set-active-medium/{id}', [MediumController::class, 'setActiveMedium'])->name('medium.active');

        //course
        Route::get('course-list', [CourseController::class, 'store']);
        Route::delete('/deletesuperteacher/{course_id}/{user_id}', [CourseController::class, 'deletesuperteacher']);
        Route::get('/superteachercourses', [CourseController::class, 'superteachercourses']);
        Route::get('course/edit/{id}', [CourseController::class, 'edit']);
        Route::get('course/material/{id}', [CourseController::class, 'material']);
        Route::get('course/material/delete/{id}', [CourseController::class, 'material_delete']);

        Route::get('course/report', [CourseController::class, 'report'])->name('course.report');
        Route::get('course/report/detail', [CourseController::class, 'report_list'])->name('course.report.list');

        Route::resource('course', CourseController::class);
        Route::resource('course_category', CourseCategoryController::class);
        Route::get('course_category/edit/{id}', [CourseCategoryController::class, 'edit']);

        Route::resource('expense', ExpenseController::class);

        Route::resource('event', EventController::class);

        Route::get('/salarypaid', [ExpenseController::class, 'salarypaid'])->name('salary.index');
        Route::post('/addsalaryexpense', [ExpenseController::class, 'addsalaryexpense'])->name('salary.store');
        Route::get('/getsalary/{id}', [ExpenseController::class, 'getsalary']);

        Route::put('class-group/add-class', [ClassGroupController::class, 'addClassInGroup'])->name('class-group.add-class');
        Route::delete('class-group/remove-class', [ClassGroupController::class, 'removeClassFromGroup'])->name('class-group.remove-class');
        Route::resource('class-group', ClassGroupController::class);

        Route::get('test', static function () {
            dd(Auth::user()->toArray());
        });

        Route::resource('stream',StreamController::class);

        Route::resource('shifts',ShiftController::class);


        Route::get('exam-term-documents', [ExamTermController::class, 'examTermDocuments'])->name('et_documents');
        Route::get('class-doc-list', [ExamTermController::class, 'classDocList']);
        Route::get('print-marks-sheet', [ExamTermController::class, 'marksSheet'])->name('print-marks-sheet');
        // Route::get('print-marks-sheet/{exam-term-id}/class/{class-section-id}', [ExamTermController::class, 'marksSheet'])->name('print-marks-sheet');
        Route::get('print-attendance-list', [ExamTermController::class, 'attendanceList'])->name('print-attendance-list');

        Route::get('school-statistics', [CenterController::class, 'centerStatistics'])->name('center_statistics');

        Route::get('annual-best-report', [ClassSchoolController::class, 'annual_best_report'])->name('annual-best-report');

        Route::get('list-annual-best-sheet', [ClassSchoolController::class, 'list_class_groups'])->name('annual-best-list');

        // for listing the statistics.
        Route::get('list-class-group-statistics', [ClassSchoolController::class, 'list_class_groups_statistics'])->name('class-group-list');

//        Route::get('list-annual-best-subject-sheet', [ClassSchoolController::class, 'list_class_subjects'])->name('annual-best-subject-list');

        Route::get('annual_class_best_report/{group_name}', [ClassSchoolController::class, 'list_annual_best_reports'])->name('list-annual-best-reports');

        Route::get('/list-classes', [CenterController::class, 'listClasses']);

        Route::get('annual_class_best_in_subject_report/{group_name}', [ClassSchoolController::class, 'list_annual_best_in_subject_reports'])->name('list-annual-best-in-subject-reports');

        Route::resource('accounting/income', \App\Http\Controllers\IncomeController::class);
        Route::resource('accounting/income-category', \App\Http\Controllers\IncomeCategoryController::class);

        Route::get('get-section-classes', [SectionController::class, 'getClasses']);

        // Competency domain
        Route::resource('competency-domain', CompetencyDomainController::class);
        Route::resource('competency', CompetencyController::class);
        Route::get('class-competency', [ClassCompetencyController::class, 'index'])->name('class-competency.index');
        Route::post('class-competency', [ClassCompetencyController::class, 'store'])->name('class-competency.store');
        Route::get('class-competency/{class}/edit', [ClassCompetencyController::class, 'edit'])->name('class-competency.edit');
        Route::put('class-competency/{class}', [ClassCompetencyController::class, 'update'])->name('class-competency.update');
        Route::delete('class-competency/{class}', [ClassCompetencyController::class, 'destroy'])->name('class-competency.destroy');

        // Learning Unit
        Route::resource('learning-units', LearningUnitController::class);

        Route::get('competency-type/assign-class', [CompetencyTypeController::class, 'assignClass'])
            ->name('competency-type.assign-class');
        Route::post('competency-type/assign-class', [CompetencyTypeController::class, 'assignClassStore'])
            ->name('competency-type.assign-class-store');
        Route::post('competency-type/competency-list', [CompetencyTypeController::class, 'competencyList'])
            ->name('competency-type.competency-list');
        Route::resource('competency-type', CompetencyTypeController::class);

        Route::get('competency-marks', [CompetencyMarksController::class, 'index'])->name('competency.marks.index');
        Route::get('competency-marks/students', [CompetencyMarksController::class, 'getStudents'])->name('competency.marks.students');
        Route::post('competency-marks', [CompetencyMarksController::class, 'store'])->name('competency.marks.store');
        Route::get('competency-marks/edit/{student_id}', [CompetencyMarksController::class, 'edit'])->name('competency.marks.edit');
        Route::get('competency-marks/student', [CompetencyMarksController::class, 'uploadStudentMarks'])->name('competency.marks.upload-student');
        Route::get('competency-marks/students-list', [CompetencyMarksController::class, 'studentsList'])->name('competency.marks.students-list');
        Route::post('competency-marks/update', [CompetencyMarksController::class, 'updateStudentMarks'])->name('competency.marks.update');
        
        Route::get('competency-marks/get-class-competencies', [CompetencyMarksController::class, 'getClassCompetencies'])
            ->name('competency.marks.class-competencies');

        Route::post('competency-report/generate-report/{student}', [CompetencyReportController::class, 'generateReport'])
            ->name('competency-report.generate-report');

        Route::get('competency-report/report-card-list', [CompetencyReportController::class, 'reportCardList'])
            ->name('competency-report.report-card-list');

        Route::post('competency-report/report-card-list-data', [CompetencyReportController::class, 'reportCardListData'])
            ->name('competency-report.report-card-list-data');

        Route::post('competency-clone', [CompetencyCloneController::class, 'cloneCompetencies'])->name('competency.clone');

        Route::resource('competency-observation', ObservationController::class);

        // Annual projects
        Route::get('annual-project', [AnnualProjectController::class, 'show'])->name('annual-project.show');
        Route::post('annual-project/class-project/{classSubject}', [AnnualProjectController::class, 'storeCLassProject'])
            ->whereNumber('classSubject')
            ->name('annual-project.store-class-project');
        
        Route::post('annual-project/type', [AnnualProjectController::class, 'storeType'])->name('annual-project.store-type');
        Route::delete('annual-project/type/{annualProjectType}', [AnnualProjectController::class, 'destroyType'])->name('annual-project.destroy-type');
        Route::delete('annual-project/type/{annualProjectType}/force-delete', [AnnualProjectController::class, 'forceDestroyType'])->name('annual-project.force-destroy-type');

        Route::get('global-report', [GlobalReportController::class, 'index'])->name('global-report.index');
        Route::get('global-report/sequence/{sequence}', [GlobalReportController::class, 'sequence'])->name('global-report.sequence');
        Route::get('global-report/term/{term}', [GlobalReportController::class, 'term'])->name('global-report.term');

        Route::get('class-report/global-data', [ClassSchoolController::class, 'global_data'])->name('class-report.global-data');

        // Departments
        Route::get('department/list', [DepartmentController::class, 'list'])->name('department.list');
        Route::resource('department', DepartmentController::class);

        Route::get('/testing', function () {
            $firstList =  [
                "BESSALI ANGELINE",
                "DOUDOU SENA BOBO DAREL",
                "KOZA NARGABA FRANCIS ",
                "NGAMA NGOUH ETIENNE",
                "AMINATOU DJIBRILLA ",
                "MBIONGUE MISSECK SHADRIN DANIEL",
                "NYAZOE LAMY NOLYA",
                "TCHAM BEKOLO PHILLIP ENZO",
                "ABOU JEAN PIERRE",
                "MOMBARA DOUMDI ALEXANDRE",
                 "AMINATOU SAMSIA",
                 "BOUGNINE KIZITO",
                 "NGAZI NGAZI CONSTANTIN",
                 "BOUBOU WILLIAM",
                 "NERTA SYLVAIN",
                 "IBRAHIM IDRISSA",
                 "MOHAMADOU NOUROU (B-OYA)",
                 "MAAZOU IBRAHIM",
                "ABBO DOUA WILFROED",
                "MOHAMADOU NOUROU (MND)",
            ];

            $secondList = [
                "WANTOUNA ESPOIR FREDERICK",
                "AMINATOU DJIBRILLA ",
                "GORO EMMANUEL",
                "MBIONGUE MISSECK SHADRIN DANIEL",
                "NYAZOE LAMY NOLYA",
                "SOLA DIANIE SARAH",
                "TCHAM BEKOLO PHILLIP ENZO",
                "YANDE IYA JOSIANE",
                "ABOU JEAN PIERRE",
                "BESSALI ANGELINE",
                 "DOUDOU SENA BOBO DAREL",
                 "KOZA NARGABA FRANCIS ",
                 "NGAMA NGOUH ETIENNE",
                 "MOMBARA DOUMDI ALEXANDRE",
                 "AMINATOU SAMSIA",
                 "BOUGNINE KIZITO",
                 "NGAZI NGAZI CONSTANTIN",
                 "BOUBOU WILLIAM",
                 "NERTA SYLVAIN",
                 "IBRAHIM IDRISSA",
                 "MOHAMADOU NOUROU (B-OYA)",
                 "MAAZOU IBRAHIM",
                 "GOUNTHE EKANI DIEUDONNE",
                 "ABBO DOUA WILFROED",
                 "MOHAMADOU NOUROU (MND)",
            ];

            $firstMap = collect($firstList);
            $secondMap = collect($secondList);

            $difference = $secondMap->diff($firstMap);

            dd($difference->toArray());
        });

        Route::get('/report', [\App\Http\Controllers\FixingController::class, 'reportShow']);

        Route::get("/clear-unassigned-students", [\App\Http\Controllers\FixingController::class, 'schoolTesting']);

        Route::get('/delete-useless', [\App\Http\Controllers\FixingController::class, 'deleteUseless']);

        Route::get('/update-student-names', [StudentController::class, 'updateStudentNamesUpperCase'])->name('update.student.names');

        Route::post('/competency-report/bulk-download', [CompetencyReportController::class, 'bulkDownload'])
            ->name('competency-report.bulk-download');

    });
});

Route::post('password/reset', [UserController::class, 'reset_password']);
Route::get('set-new-password/{token}', [UserController::class, 'set_new_password']);
Route::post('set-new-password/', [UserController::class, 'store_new_password']);


Route::get('demo-email', [HomeController::class, 'demo_email']);

// webhooks
Route::post('webhook/razorpay', [WebhookController::class, 'razorpay']);
Route::post('webhook/stripe', [WebhookController::class, 'stripe']);

Route::get('page/privacy-policy', static function () {
    $settings = Settings::where('type', 'privacy_policy')->first();
    echo $settings['message'] ?? "";
});
Route::get('clear', static function () {
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
});

Route::get('storage-link', static function () {
    try {
        Artisan::call('storage:link');
        echo "storage link created";
    } catch (Exception) {
        echo "Storage Link already exists";
    }
});

Route::get('migrate', static function () {
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('migrate');
    echo "migration done";
});

Route::get('seeder_install', static function () {
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('db:seed --class=InstallationSeeder');
    echo "Seeder installation done";
    return redirect()->back();
});


