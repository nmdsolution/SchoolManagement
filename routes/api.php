<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ParentApiController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\TeacherApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:sanctum'], static function () {
    Route::post('logout', [ApiController::class, 'logout']);
});

/**
 * STUDENT APIs
 **/
Route::group(['prefix' => 'student'], static function () {

    //Non Authenticated APIs
    Route::post('login', [StudentApiController::class, 'login']);
    Route::post('forgot-password', [StudentApiController::class, 'forgotPassword']);

    //Authenticated APIs
    Route::group(['middleware' => 'auth:sanctum'], static function () {
        Route::get('subjects', [StudentApiController::class, 'subjects']);
        Route::get('class-subjects', [StudentApiController::class, 'classSubjects']);
        Route::post('select-subjects', [StudentApiController::class, 'selectSubjects']);
        Route::get('parent-details', [StudentApiController::class, 'getParentDetails']);
        Route::get('timetable', [StudentApiController::class, 'getTimetable']);
        Route::get('lessons', [StudentApiController::class, 'getLessons']);
        Route::get('lesson-topics', [StudentApiController::class, 'getLessonTopics']);
        Route::get('assignments', [StudentApiController::class, 'getAssignments']);
        Route::post('submit-assignment', [StudentApiController::class, 'submitAssignment']);
        Route::post('delete-assignment-submission', [StudentApiController::class, 'deleteAssignmentSubmission']);
        Route::get('attendance', [StudentApiController::class, 'getAttendance']);
        Route::get('announcements', [StudentApiController::class, 'getAnnouncements']);
        Route::get('get-exam-list', [StudentApiController::class, 'getExamList']); // Exam list Route
        Route::get('get-exam-details', [StudentApiController::class, 'getExamDetails']); // Exam Details Route
        Route::get('exam-marks', [StudentApiController::class, 'getExamMarks']); // Exam Details Route

        // online exam routes
        Route::get('get-online-exam-list', [StudentApiController::class, 'getOnlineExamList']); // Get Online Exam List Route
        Route::get('get-online-exam-questions', [StudentApiController::class, 'getOnlineExamQuestions']); // Get Online Exam Questions Route
        Route::post('submit-online-exam-answers', [StudentApiController::class, 'submitOnlineExamAnswers']); // Submit Online Exam Answers Details Route
        Route::get('get-online-exam-result-list', [StudentApiController::class, 'getOnlineExamResultList']); // Online exam result list Route
        Route::get('get-online-exam-result', [StudentApiController::class, 'getOnlineExamResult']); // Online exam result  Route

        //reports
        Route::get('get-online-exam-report', [StudentApiController::class, 'getOnlineExamReport']); // Online Exam Report Route
        Route::get('get-assignments-report', [StudentApiController::class, 'getAssignmentReport']); // Assignment Report Route

        //fees
        Route::get('fees-class-list', [StudentApiController::class, 'getFeesClassList']); //Fees Details
        Route::get('fees-transaction-list', [StudentApiController::class, 'getFeesPaymentTransactions']); //Fees Transaction Status
        Route::post('fees-transaction', [StudentApiController::class, 'feesTransaction']); //Fees Transaction Status
        Route::get('fees-paid-receipt-pdf', [StudentApiController::class, 'feesPaidReceiptPDF']); //Fees Transaction Status
//        Route::post('add-fees-choice', [StudentApiController::class, 'storeFeesChoice']); //Fees Details
        Route::get('fees-paid-list', [StudentApiController::class, 'feesPaidList']); //Fees Details
        Route::get('fees-transactions-list', [ParentApiController::class, 'getFeesPaymentTransactions']); //Fees Payment Transaction Details

        Route::apiResource('comments', CommentController::class);
    });
});

/**
 * PARENT APIs
 **/
Route::group(['prefix' => 'parent'], static function () {
    //Non Authenticated APIs
    Route::post('login', [ParentApiController::class, 'login']);
    //Authenticated APIs
    Route::group(['middleware' => ['auth:sanctum',]], static function () {
        Route::get('announcements', [ParentApiController::class, 'getAnnouncements']);

        //fees without child id
        Route::get('fees-class-list', [ParentApiController::class, 'getFeesClassList']); //Fees Details
        Route::post('fees-transaction', [ParentApiController::class, 'feesTransaction']); //Fees Transaction Status
        Route::get('fees-paid-receipt-pdf', [ParentApiController::class, 'feesPaidReceiptPDF']); //Fees Transaction Status
        Route::get('fees-transactions-list', [ParentApiController::class, 'getFeesPaymentTransactions']); //Fees Payment Transaction Details

        Route::group(['middleware' => ['auth:sanctum', 'checkChild']], static function () {

            Route::get('subjects', [ParentApiController::class, 'subjects']);
            Route::get('class-subjects', [ParentApiController::class, 'classSubjects']);
            Route::get('timetable', [ParentApiController::class, 'getTimetable']);
            Route::get('lessons', [ParentApiController::class, 'getLessons']);
            Route::get('lesson-topics', [ParentApiController::class, 'getLessonTopics']);
            Route::get('assignments', [ParentApiController::class, 'getAssignments']);
            Route::get('attendance', [ParentApiController::class, 'getAttendance']);
            // Route::get('announcements', [ParentApiController::class, 'getAnnouncements']);
            Route::get('teachers', [ParentApiController::class, 'getTeachers']);
            Route::get('get-exam-list', [ParentApiController::class, 'getExamList']); // Exam list Route
            Route::get('get-exam-details', [ParentApiController::class, 'getExamDetails']); // Exam Details Route
            Route::get('exam-marks', [ParentApiController::class, 'getExamMarks']); //Exam Marks

            //fees
//            Route::post('add-fees-choice', [ParentApiController::class, 'storeFeesChoice']); //Fees Details
            Route::get('fees-paid-list', [ParentApiController::class, 'feesPaidList']); //Fees Details

            // online exam routes
            Route::get('get-online-exam-list', [ParentApiController::class, 'getOnlineExamList']); // Get Online Exam List Route
            Route::get('get-online-exam-result-list', [ParentApiController::class, 'getOnlineExamResultList']); // Online exam result list Route
            Route::get('get-online-exam-result', [ParentApiController::class, 'getOnlineExamResult']); // Online exam result  Route

            //reports
            Route::get('get-online-exam-report', [ParentApiController::class, 'getOnlineExamReport']); // Online Exam Report Route
            Route::get('get-assignments-report', [ParentApiController::class, 'getAssignmentReport']); // Assignment Report Route

            Route::post('guardian', [ParentApiController::class, 'guardian']);
            Route::get('guardian', [ParentApiController::class, 'list_guardian']);
            Route::post('guardian/delete', [ParentApiController::class, 'guardian_delete']);
            Route::post('guardian/update', [ParentApiController::class, 'guardian_update']);
        });
    });
});

/**
 * TEACHER APIs
 **/
Route::group(['prefix' => 'teacher'], static function () {
    //Non Authenticated APIs
    Route::post('login', [TeacherApiController::class, 'login']);
    Route::post('forgot-password', [TeacherApiController::class, 'forgotPassword']);
    //Authenticated APIs
    Route::group(['middleware' => ['auth:sanctum',]], static function () {
        Route::get('classes', [TeacherApiController::class, 'classes']);

        Route::get('subjects', [TeacherApiController::class, 'subjects']);
        Route::get('centers', [TeacherApiController::class, 'center']);

        //Assignment
        Route::get('get-assignment', [TeacherApiController::class, 'getAssignment']);
        Route::post('create-assignment', [TeacherApiController::class, 'createAssignment']);
        Route::post('update-assignment', [TeacherApiController::class, 'updateAssignment']);
        Route::post('delete-assignment', [TeacherApiController::class, 'deleteAssignment']);

        //Assignment Submission
        Route::get('get-assignment-submission', [TeacherApiController::class, 'getAssignmentSubmission']);
        Route::post('update-assignment-submission', [TeacherApiController::class, 'updateAssignmentSubmission']);

        //File
        Route::post('delete-file', [TeacherApiController::class, 'deleteFile']);
        Route::post('update-file', [TeacherApiController::class, 'updateFile']);

        //Lesson
        Route::get('get-lesson', [TeacherApiController::class, 'getLesson']);
        Route::post('create-lesson', [TeacherApiController::class, 'createLesson']);
        Route::post('update-lesson', [TeacherApiController::class, 'updateLesson']);
        Route::post('delete-lesson', [TeacherApiController::class, 'deleteLesson']);

        //Topic
        Route::get('get-topic', [TeacherApiController::class, 'getTopic']);
        Route::post('create-topic', [TeacherApiController::class, 'createTopic']);
        Route::post('update-topic', [TeacherApiController::class, 'updateTopic']);
        Route::post('delete-topic', [TeacherApiController::class, 'deleteTopic']);

        //Announcement
        Route::get('get-announcement', [TeacherApiController::class, 'getAnnouncement']);
        Route::post('send-announcement', [TeacherApiController::class, 'sendAnnouncement']);
        Route::post('update-announcement', [TeacherApiController::class, 'updateAnnouncement']);
        Route::post('delete-announcement', [TeacherApiController::class, 'deleteAnnouncement']);

        Route::get('get-attendance', [TeacherApiController::class, 'getAttendance']);
        Route::post('submit-attendance', [TeacherApiController::class, 'submitAttendance']);


        //Exam
        Route::get('exam/terms', [TeacherApiController::class, 'getTerms']);
        Route::post('exam/create', [TeacherApiController::class, 'createExam']);
        Route::post('exam/edit', [TeacherApiController::class, 'editExam']);
        Route::post('exam/publish', [TeacherApiController::class, 'publishExamResult']);
        Route::get('get-exam-list', [TeacherApiController::class, 'getExamList']); // Exam list Route
        Route::get('get-exam-details', [TeacherApiController::class, 'getExamDetails']); // Exam Details Route
        Route::post('submit-exam-marks/subject', [TeacherApiController::class, 'submitExamMarksBySubjects']); // Submit Exam Marks By Subjects Route
        Route::post('submit-exam-marks/student', [TeacherApiController::class, 'submitExamMarksByStudent']); // Submit Exam Marks By Students Route

        Route::group(['middleware' => ['auth:sanctum', 'checkStudent']], static function () {
            Route::get('get-student-result', [TeacherApiController::class, 'GetStudentExamResult']); // Student Exam Result
            Route::get('get-student-marks', [TeacherApiController::class, 'GetStudentExamMarks']); // Student Exam Marks
        });

        //Student List
        Route::get('student-list', [TeacherApiController::class, 'getStudentList']);
        Route::get('student-details', [TeacherApiController::class, 'getStudentDetails']);

        //Schedule List
        Route::get('teacher_timetable', [TeacherApiController::class, 'getTeacherTimetable']);
    });
});

/**
 * GENERAL APIs
 **/
Route::group(['middleware' => 'auth:sanctum'], static function () {
    Route::get('sliders', [ApiController::class, 'getSliders']);
    Route::get('courses', [ApiController::class, 'course']);
    Route::post('buycourse', [ApiController::class, 'buycourse']);  //Buy Course Student Route
    Route::get('event', [ApiController::class, 'event']);

    Route::get('comments', [CommentController::class, 'index']);
});

Route::get('holidays', [ApiController::class, 'getHolidays']);
Route::get('current-session-year', [ApiController::class, 'getSessionYear']);
Route::get('settings', [ApiController::class, 'getSettings']);
Route::post('forgot-password', [ApiController::class, 'forgotPassword']);
Route::get('centers', [ApiController::class, 'getCenters']);
Route::group(['middleware' => ['auth:sanctum',]], static function () {
    Route::post('change-password', [ApiController::class, 'changePassword']);
});


Route::get('test', static function(){
    echo "working";
})->middleware('throttle:1,1');