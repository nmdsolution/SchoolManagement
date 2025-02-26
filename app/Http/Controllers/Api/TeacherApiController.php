<?php

namespace App\Http\Controllers\Api;

use App\Domain\Student\Repositories\StudentsRepository;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\Exam;
use App\Models\ExamClassSection;
use App\Models\ExamMarks;
use App\Models\ExamResult;
use App\Models\ExamSequence;
use App\Models\ExamTerm;
use App\Models\ExamTimetable;
use App\Models\File;
use App\Models\Holiday;
use App\Models\Lesson;
use App\Models\LessonTopic;
use App\Models\Parents;
use App\Models\Students;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Timetable;
use App\Models\User;
use App\Rules\uniqueLessonInClass;
use App\Rules\uniqueTopicInLesson;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class TeacherApiController extends Controller
{
    public function __construct(private StudentsRepository $studentsRepository)
    {
    }

    public function login(Request $request)
    {
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $auth = Auth::user();
            if (!Auth::user()->teacher) {
                $response = array(
                    'error' => true,
                    'message' => 'Invalid Login Credentials',
                    'code' => 101
                );
                return response()->json($response, 200);
            }
            if ($request->fcm_id) {
                $auth->fcm_id = $request->fcm_id;
                $auth->save();
            }

            $token = $auth->createToken($auth->first_name)->plainTextToken;
            $user = $auth->load(['teacher']);
            $response = array(
                'error' => false,
                'message' => 'User logged-in!',
                'token' => $token,
                'data' => $user,
                'code' => 100,
            );
            return response()->json($response, 200);
        } else {
            $response = array(
                'error' => true,
                'message' => 'Invalid Login Credentials',
                'code' => 101
            );
            return response()->json($response, 200);
        }
    }

    public function classes(Request $request)
    {
        try {
            $user = $request->user()->teacher;
            //Find the class in which teacher is assigns as Class Teacher
            $class_teacher = $user->class_sections->load('class.medium', 'section', 'class.center:id,name', 'class.stream');
            $class_teacher_ids = $user->class_sections->load('class.medium', 'section', 'class.center:id,name', 'class.stream')->pluck('class_id');
//            $class_teacher_section_ids = $user->class_sections->load('class.medium', 'section', 'class.center:id,name')->pluck('section_id');

            //Find the Classes in which teacher is taking subjects
            $class_section_ids = $user->classes()->pluck('class_section_id');
            $class_section = ClassSection::whereIn('id', $class_section_ids)->with('class.medium', 'class.stream', 'section', 'class.center:id,name');
//            if ($class_teacher) {
            // $class_section = $class_section->where(function ($q) use ($class_teacher_ids, $class_teacher_section_ids) {
            //     $q->whereNotIn('class_id', $class_teacher_ids)->WhereNotIn('section_id', $class_teacher_section_ids);
            // });

//                $class_section = $class_section->where(function ($q) use ($class_teacher_ids) {
//                    $q->whereNotIn('class_id', $class_teacher_ids);
//                });
//            }
            $class_section = $class_section->get();
            $response = array(
                'error' => false,
                'message' => 'Teacher Classes Fetched Successfully.',
                'data' => [
                    'class_teacher' => $class_teacher,
                    'other' => $class_section
                ],
                'code' => 200,
            );
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response, 200);
        }
    }

    public function subjects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'nullable|numeric',
            'subject_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $user = $request->user();
            $teacher = $user->teacher;
            $subjects = $teacher->subjects();
            if ($request->class_section_id) {
                $subjects = $subjects->where('class_section_id', $request->class_section_id);
            }

            if ($request->subject_id) {
                $subjects = $subjects->where('subject_id', $request->subject_id);
            }
            if ($request->center_id) {
                $subjects->whereHas('subject', function ($q) use ($request) {
                    $q->where('center_id', $request->center_id);
                });
            }
            $subjects = $subjects->with('subject', 'class_section.class.stream', 'subject.center:id,name')->get();

            $response = array(
                'error' => false,
                'message' => 'Teacher Subject Fetched Successfully.',
                'data' => $subjects,
                'code' => 200,
            );
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response, 200);
        }
    }


    public function getAssignment(Request $request)
    {
        if (!Auth::user()->can('assignment-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'nullable|numeric',
            'subject_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $sql = Assignment::assignmentteachers()->with('class_section.class.stream', 'file', 'subject', 'class_section.class.center:id,name');
            if ($request->center_id) {
                $sql->whereHas('class_section.class', function ($q) use ($request) {
                    $q->where('center_id', $request->center_id);
                });
            }

            if ($request->class_section_id) {
                $sql = $sql->where('class_section_id', $request->class_section_id);
            }

            if ($request->subject_id) {
                $sql = $sql->where('subject_id', $request->subject_id);
            }
            $data = $sql->orderBy('id', 'DESC')->paginate();
            $response = array(
                'error' => false,
                'message' => 'Assignment Fetched Successfully.',
                'data' => $data,
                'code' => 200,
            );
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response, 200);
        }
    }

    public function createAssignment(Request $request)
    {
        if (!Auth::user()->can('assignment-create')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            "class_section_id" => 'required|numeric',
            "subject_id" => 'required|numeric',
            "name" => 'required',
            "instructions" => 'nullable',
            "due_date" => 'required|date',
            "points" => 'nullable',
            "resubmission" => 'nullable|boolean',
            "extra_days_for_resubmission" => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $classSection = ClassSchool::whereHas('class_section', static function ($q) use ($request) {
                $q->where('id', $request->class_section_id);
            })->first();
            $session_year = getSettings('session_year', $classSection->center_id);
            $session_year_id = $session_year['session_year'];

            $assignment = new Assignment();
            $assignment->class_section_id = $request->class_section_id;
            $assignment->subject_id = $request->subject_id;
            $assignment->name = $request->name;
            $assignment->instructions = $request->instructions;
            $assignment->due_date = Carbon::parse($request->due_date)->format('Y-m-d H:i:s');
            $assignment->points = $request->points;
            if ($request->resubmission) {
                $assignment->resubmission = 1;
                $assignment->extra_days_for_resubmission = $request->extra_days_for_resubmission;
            } else {
                $assignment->resubmission = 0;
                $assignment->extra_days_for_resubmission = null;
            }
            $assignment->session_year_id = $session_year_id;

            $subject_name = Subject::select('name')->where('id', $request->subject_id)->pluck('name')->first();
            $title = 'New assignment added in ' . $subject_name;
            $body = $request->name;
            $type = "assignment";
            $user = Students::select('user_id')->where('class_section_id', $request->class_section_id)->get()->pluck('user_id');
            $assignment->save();
            send_notification($user, $title, $body, $type);

            if ($request->hasFile('file')) {
                foreach ($request->file as $file_upload) {
                    $file = new File();
                    $file->file_name = $file_upload->getClientOriginalName();
                    $file->type = 1;
                    $file->file_url = $file_upload->store('assignment', 'public');
                    $file->modal()->associate($assignment);
                    $file->save();
                }
            }

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
                'code' => 200,
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function updateAssignment(Request $request)
    {
        if (!Auth::user()->can('assignment-edit')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            "assignment_id" => 'required|numeric',
            "class_section_id" => 'required|numeric',
            "subject_id" => 'required|numeric',
            "name" => 'required',
            "instructions" => 'nullable',
            "due_date" => 'required|date',
            "points" => 'nullable',
            "resubmission" => 'nullable|boolean',
            "extra_days_for_resubmission" => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $classSection = ClassSchool::whereHas('class_section', static function ($q) use ($request) {
                $q->where('id', $request->class_section_id);
            })->first();
            $session_year = getSettings('session_year', $classSection->center_id);
            $session_year_id = $session_year['session_year'];

            $assignment = Assignment::find($request->assignment_id);
            $assignment->class_section_id = $request->class_section_id;
            $assignment->subject_id = $request->subject_id;
            $assignment->name = $request->name;
            $assignment->instructions = $request->instructions;
            $assignment->due_date = Carbon::parse($request->due_date)->format('Y-m-d H:i:s');;
            $assignment->points = $request->points;
            if ($request->resubmission) {
                $assignment->resubmission = 1;
                $assignment->extra_days_for_resubmission = $request->extra_days_for_resubmission;
            } else {
                $assignment->resubmission = 0;
                $assignment->extra_days_for_resubmission = null;
            }

            $assignment->session_year_id = $session_year_id;
            $subject_name = Subject::select('name')->where('id', $request->subject_id)->pluck('name')->first();
            $title = 'Update assignment in ' . $subject_name;
            $body = $request->name;
            $type = "assignment";
            $user = Students::select('user_id')->where('class_section_id', $request->class_section_id)->get()->pluck('user_id');
            $assignment->save();
            send_notification($user, $title, $body, $type);

            if ($request->hasFile('file')) {
                foreach ($request->file as $file_upload) {
                    $file = new File();
                    $file->file_name = $file_upload->getClientOriginalName();
                    $file->type = 1;
                    $file->file_url = $file_upload->store('assignment', 'public');
                    $file->modal()->associate($assignment);
                    $file->save();
                }
            }

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
                'code' => 200,
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function deleteAssignment(Request $request)
    {
        if (!Auth::user()->can('assignment-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        try {
            $assignment = Assignment::find($request->assignment_id);
            $assignment->delete();
            $response = array(
                'error' => false,
                'message' => trans('data_delete_successfully'),
                'code' => 200
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getAssignmentSubmission(Request $request)
    {
        if (!Auth::user()->can('assignment-submission')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required|nullable|numeric'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $sql = AssignmentSubmission::assignmentsubmissionteachers()->with('assignment.subject:id,name', 'student:id,user_id', 'student.user:first_name,last_name,id,image', 'file');
            $data = $sql->where('assignment_id', $request->assignment_id)->get();
            $response = array(
                'error' => false,
                'message' => 'Assignment Fetched Successfully.',
                'data' => $data,
                'code' => 200,
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response, 200);
    }

    public function updateAssignmentSubmission(Request $request)
    {
        if (!Auth::user()->can('assignment-submission')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'assignment_submission_id' => 'required|numeric',
            'status' => 'required|numeric|in:1,2',
            'points' => 'nullable|numeric',
            'feedback' => 'nullable',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }

        try {
            $assignment_submission = AssignmentSubmission::findOrFail($request->assignment_submission_id);
            $assignment_submission->feedback = $request->feedback;
            if ($request->status == 1) {
                $assignment_submission->points = $request->points;
            } else {
                $assignment_submission->points = null;
            }

            $assignment_submission->status = $request->status;
            $assignment_submission->save();

            $assignment_data = Assignment::where('id', $assignment_submission->assignment_id)->with('subject')->first();
            $user = Students::select('user_id')->where('id', $assignment_submission->student_id)->get()->pluck('user_id');
            $title = '';
            $body = '';
            if ($request->status == 2) {
                $title = "Assignment rejected";
                $body = $assignment_data->name . " rejected in " . $assignment_data->subject->name . " subject";
            }
            if ($request->status == 1) {
                $title = "Assignment accepted";
                $body = $assignment_data->name . " accepted in " . $assignment_data->subject->name . " subject";
            }
            $type = "assignment";
            send_notification($user, $title, $body, $type);
            $response = array(
                'error' => false,
                'message' => trans('data_update_successfully'),
                'code' => 200,
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getLesson(Request $request)
    {
        if (!Auth::user()->can('lesson-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'nullable|numeric',
            'class_section_id' => 'nullable|numeric',
            'subject_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            // return get_center_id();
            $sql = Lesson::lessonteachers()->with('file', 'subject:id,name,type,medium_id,center_id', 'subject.center:id,name')->withCount('topic');
            if ($request->center_id) {
                $sql->whereHas('class_section.class', function ($q) use ($request) {
                    $q->where('center_id', $request->center_id);
                });
            }

            if ($request->lesson_id) {
                $sql = $sql->where('id', $request->lesson_id);
            }

            if ($request->class_section_id) {
                $sql = $sql->where('class_section_id', $request->class_section_id);
            }

            if ($request->subject_id) {
                $sql = $sql->where('subject_id', $request->subject_id);
            }
            $data = $sql->orderBy('id', 'DESC')->get();
            $response = array(
                'error' => false,
                'message' => 'Lesson Fetched Successfully.',
                'data' => $data,
                'code' => 200,
            );
            return response()->json($response);
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response, 200);
        }
    }

    public function createLesson(Request $request)
    {
        if (!Auth::user()->can('lesson-create')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'description' => 'required',
                'class_section_id' => 'required|numeric',
                'subject_id' => 'required|numeric',

                'file' => 'nullable|array',
                'file.*.type' => 'nullable|in:1,2,3,4',
                'file.*.name' => 'required_with:file.*.type',
                'file.*.thumbnail' => 'required_if:file.*.type,2,3,4',
                'file.*.file' => 'required_if:file.*.type,1,3',
                'file.*.link' => 'required_if:file.*.type,2,4',

                //            'file.*.type' => 'nullable|in:file_upload,youtube_link,video_upload,other_link',
                //            'file.*.name' => 'required_with:file.*.type',
                //            'file.*.thumbnail' => 'required_if:file.*.type,youtube_link,video_upload,other_link',
                //            'file.*.file' => 'required_if:file.*.type,file_upload,video_upload',
                //            'file.*.link' => 'required_if:file.*.type,youtube_link,other_link',
                //Regex for Youtube Link
                // 'file.*.link'=>['required_if:file.*.type,youtube_link','regex:/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})(?:&list=(\S+))?$/'],
                //Regex for Other Link
                // 'file.*.link'=>'required_if:file.*.type,other_link|url'
            ]
        );

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        $validator2 = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    new uniqueLessonInClass($request->class_section_id, $request->subject_id)
                ]
            ]
        );
        if ($validator2->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator2->errors()->first(),
                'code' => 113,
            );
            return response()->json($response);
        }
        try {
            $lesson = new Lesson();
            $lesson->name = $request->name;
            $lesson->description = $request->description;
            $lesson->class_section_id = $request->class_section_id;
            $lesson->subject_id = $request->subject_id;
            $lesson->save();

            if ($request->file) {
                foreach ($request->file as $key => $file) {
                    if ($file['type']) {
                        $lesson_file = new File();
                        $lesson_file->file_name = $file['name'];
                        $lesson_file->modal()->associate($lesson);

                        if ($file['type'] == "1") {
                            $lesson_file->type = 1;
                            $lesson_file->file_url = $file['file']->store('lessons', 'public');
                        } elseif ($file['type'] == "2") {
                            $lesson_file->type = 2;
                            $lesson_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                            $lesson_file->file_url = $file['link'];
                        } elseif ($file['type'] == "3") {
                            $lesson_file->type = 3;
                            $lesson_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                            $lesson_file->file_url = $file['file']->store('lessons', 'public');
                        } elseif ($file['type'] == "4") {
                            $lesson_file->type = 4;
                            $lesson_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                            $lesson_file->file_url = $file['link'];
                        }
                        $lesson_file->save();
                    }
                }
            }

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
                'code' => 200,
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response);
        }
        return response()->json($response);
    }

    public function updateLesson(Request $request)
    {
        if (!Auth::user()->can('lesson-edit')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'lesson_id' => 'required|numeric',
                'name' => 'required',
                'description' => 'required',
                'class_section_id' => 'required|numeric',
                'subject_id' => 'required|numeric',

                'edit_file' => 'nullable|array',
                'edit_file.*.id' => 'required|numeric',
                'edit_file.*.type' => 'nullable|in:1,2,3,4',
                'edit_file.*.name' => 'required_with:edit_file.*.type',
                'edit_file.*.link' => 'required_if:edit_file.*.type,2,4',

                'file' => 'nullable|array',
                'file.*.type' => 'nullable|in:1,2,3,4',
                'file.*.name' => 'required_with:file.*.type',
                'file.*.thumbnail' => 'required_if:file.*.type,2,3,4',
                'file.*.file' => 'required_if:file.*.type,1,3',
                'file.*.link' => 'required_if:file.*.type,2,4',

                //            'edit_file' => 'nullable|array',
                //            'edit_file.*.id' => 'required|numeric',
                //            'edit_file.*.type' => 'nullable|in:file_upload,youtube_link,video_upload,other_link',
                //            'edit_file.*.name' => 'required_with:edit_file.*.type',
                //            'edit_file.*.link' => 'required_if:edit_file.*.type,youtube_link,other_link',
                //
                //            'file' => 'nullable|array',
                //            'file.*.type' => 'nullable|in:file_upload,youtube_link,video_upload,other_link',
                //            'file.*.name' => 'required_with:file.*.type',
                //            'file.*.thumbnail' => 'required_if:file.*.type,youtube_link,video_upload,other_link',
                //            'file.*.file' => 'required_if:file.*.type,file_upload,video_upload',
                //            'file.*.link' => 'required_if:file.*.type,youtube_link,other_link',

                //Regex for Youtube Link
                // 'file.*.link'=>['required_if:file.*.type,youtube_link','regex:/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})(?:&list=(\S+))?$/'],
                //Regex for Other Link
                // 'file.*.link'=>'required_if:file.*.type,other_link|url'
            ]
        );
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }

        $validator2 = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    new uniqueLessonInClass($request->class_section_id, $request->lesson_id)
                ]
            ]
        );
        if ($validator2->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator2->errors()->first(),
                'code' => 113,
            );
            return response()->json($response);
        }
        try {
            $lesson = Lesson::find($request->lesson_id);
            $lesson->name = $request->name;
            $lesson->description = $request->description;
            $lesson->class_section_id = $request->class_section_id;
            $lesson->subject_id = $request->subject_id;
            $lesson->save();

            // Update the Old Files
            if ($request->edit_file) {
                foreach ($request->edit_file as $file) {
                    if ($file['type']) {
                        $lesson_file = File::find($file['id']);
                        if ($lesson_file) {
                            $lesson_file->file_name = $file['name'];

                            if ($file['type'] == "1") {
                                $lesson_file->type = 1;
                                if (!empty($file['file'])) {
                                    if (Storage::disk('public')->exists($lesson_file->getRawOriginal('file_url'))) {
                                        Storage::disk('public')->delete($lesson_file->getRawOriginal('file_url'));
                                    }
                                    $lesson_file->file_url = $file['file']->store('lessons', 'public');
                                }
                            } elseif ($file['type'] == "2") {
                                $lesson_file->type = 2;
                                if (!empty($file['thumbnail'])) {
                                    if (Storage::disk('public')->exists($lesson_file->getRawOriginal('file_url'))) {
                                        Storage::disk('public')->delete($lesson_file->getRawOriginal('file_url'));
                                    }
                                    $lesson_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                                }

                                $lesson_file->file_url = $file['link'];
                            } elseif ($file['type'] == "3") {
                                $lesson_file->type = 3;
                                if (!empty($file['file'])) {
                                    if (Storage::disk('public')->exists($lesson_file->getRawOriginal('file_url'))) {
                                        Storage::disk('public')->delete($lesson_file->getRawOriginal('file_url'));
                                    }
                                    $lesson_file->file_url = $file['file']->store('lessons', 'public');
                                }

                                if (!empty($file['thumbnail'])) {
                                    if (Storage::disk('public')->exists($lesson_file->getRawOriginal('file_url'))) {
                                        Storage::disk('public')->delete($lesson_file->getRawOriginal('file_url'));
                                    }
                                    $lesson_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                                }
                            } elseif ($file['type'] == "4") {
                                $lesson_file->type = 4;
                                if (!empty($file['thumbnail'])) {
                                    if (Storage::disk('public')->exists($lesson_file->getRawOriginal('file_url'))) {
                                        Storage::disk('public')->delete($lesson_file->getRawOriginal('file_url'));
                                    }
                                    $lesson_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                                }
                                $lesson_file->file_url = $file['link'];
                            }

                            $lesson_file->save();
                        }
                    }
                }
            }

            //Add the new Files
            if ($request->file) {
                foreach ($request->file as $file) {
                    if ($file['type']) {
                        $lesson_file = new File();
                        $lesson_file->file_name = $file['name'];
                        $lesson_file->modal()->associate($lesson);

                        if ($file['type'] == "1") {
                            $lesson_file->type = 1;
                            $lesson_file->file_url = $file['file']->store('lessons', 'public');
                        } elseif ($file['type'] == "2") {
                            $lesson_file->type = 2;
                            $lesson_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                            $lesson_file->file_url = $file['link'];
                        } elseif ($file['type'] == "3") {
                            $lesson_file->type = 3;
                            $lesson_file->file_url = $file['file']->store('lessons', 'public');
                            $lesson_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                        } elseif ($file['type'] == "4") {
                            $lesson_file->type = 4;
                            $lesson_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                            $lesson_file->file_url = $file['link'];
                        }
                        $lesson_file->save();
                    }
                }
            }

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
                'code' => 200,
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function deleteLesson(Request $request)
    {
        if (!Auth::user()->can('lesson-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }

        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $lesson = Lesson::lessonteachers()->where('id', $request->lesson_id)->firstOrFail();
            $lesson->delete();
            $response = array(
                'error' => false,
                'message' => trans('data_delete_successfully'),
                'code' => 200,
            );
        } catch (Throwable) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getTopic(Request $request)
    {
        if (!Auth::user()->can('topic-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $sql = LessonTopic::lessontopicteachers()->with('lesson.class_section', 'lesson.subject', 'file');
            $data = $sql->where('lesson_id', $request->lesson_id)->orderBy('id', 'DESC')->get();
            $response = array(
                'error' => false,
                'message' => 'Topic Fetched Successfully.',
                'data' => $data,
                'code' => 200,
            );
            return response()->json($response);
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response, 200);
        }
    }

    public function createTopic(Request $request)
    {
        if (!Auth::user()->can('topic-create')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'description' => 'required',
                'class_section_id' => 'required|numeric',
                'subject_id' => 'required|numeric',
                'lesson_id' => 'required|numeric',
                'file' => 'nullable|array',
                'file.*.type' => 'nullable|in:1,2,3,4',
                'file.*.name' => 'required_with:file.*.type',
                'file.*.thumbnail' => 'required_if:file.*.type,2,3,4',
                'file.*.file' => 'required_if:file.*.type,1,3',
                'file.*.link' => 'required_if:file.*.type,2,4',
                //            'file' => 'nullable|array',
                //            'file.*.type' => 'nullable|in:file_upload,youtube_link,video_upload,other_link',
                //            'file.*.name' => 'required_with:file.*.type',
                //            'file.*.thumbnail' => 'required_if:file.*.type,youtube_link,video_upload,other_link',
                //            'file.*.file' => 'required_if:file.*.type,file_upload,video_upload',
                //            'file.*.link' => 'required_if:file.*.type,youtube_link,other_link',
                //Regex for Youtube Link
                // 'file.*.link'=>['required_if:file.*.type,youtube_link','regex:/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})(?:&list=(\S+))?$/'],
                //Regex for Other Link
                // 'file.*.link'=>'required_if:file.*.type,other_link|url'
            ]
        );

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102
            );
            return response()->json($response);
        }
        $validator2 = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    new uniqueTopicInLesson($request->lesson_id)
                ]
            ]
        );
        if ($validator2->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator2->errors()->first(),
                'code' => 113,
            );
            return response()->json($response);
        }

        try {
            $topic = new LessonTopic();
            $topic->name = $request->name;
            $topic->description = $request->description;
            $topic->lesson_id = $request->lesson_id;
            $topic->save();

            if ($request->file) {
                foreach ($request->file as $data) {
                    if ($data['type']) {
                        $file = new File();
                        $file->file_name = $data['name'];
                        $file->modal()->associate($topic);

                        if ($data['type'] == "1") {
                            $file->type = 1;
                            $file->file_url = $data['file']->store('lessons', 'public');
                        } elseif ($data['type'] == "2") {
                            $file->type = 2;
                            $file->file_thumbnail = $data['thumbnail']->store('lessons', 'public');
                            $file->file_url = $data['link'];
                        } elseif ($data['type'] == "3") {
                            $file->type = 3;
                            $file->file_thumbnail = $data['thumbnail']->store('lessons', 'public');
                            $file->file_url = $data['file']->store('lessons', 'public');
                        } elseif ($data['type'] == "other_link") {
                            $file->type = 4;
                            $file->file_thumbnail = $data['thumbnail']->store('lessons', 'public');
                            $file->file_url = $data['link'];
                        }

                        $file->save();
                    }
                }
            }

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
                'code' => 200
            );
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response, 200);
        }
        return response()->json($response);
    }

    public function updateTopic(Request $request)
    {
        if (!Auth::user()->can('topic-edit')) {
            $response = array(
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return redirect(route('home'))->withErrors($response);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'topic_id' => 'required|numeric',
                'name' => 'required',
                'description' => 'required',
                'class_section_id' => 'required|numeric',
                'subject_id' => 'required|numeric',
                'edit_file' => 'nullable|array',
                'edit_file.*.type' => 'nullable|in:1,2,3,4',
                'edit_file.*.name' => 'required_with:edit_file.*.type',
                'edit_file.*.link' => 'required_if:edit_file.*.type,2,',

                'file' => 'nullable|array',
                'file.*.type' => 'nullable|in:1,2,3,4',
                'file.*.name' => 'required_with:file.*.type',
                'file.*.thumbnail' => 'required_if:file.*.type,2,3,4',
                'file.*.file' => 'required_if:file.*.type,1,3',
                'file.*.link' => 'required_if:file.*.type,2,4',


                //            'edit_file' => 'nullable|array',
                //            'edit_file.*.type' => 'nullable|in:file_upload,youtube_link,video_upload,other_link',
                //            'edit_file.*.name' => 'required_with:edit_file.*.type',
                //            'edit_file.*.link' => 'required_if:edit_file.*.type,youtube_link,',
                //
                //            'file' => 'nullable|array',
                //            'file.*.type' => 'nullable|in:file_upload,youtube_link,video_upload,other_link',
                //            'file.*.name' => 'required_with:file.*.type',
                //            'file.*.thumbnail' => 'required_if:file.*.type,youtube_link,video_upload,other_link',
                //            'file.*.file' => 'required_if:file.*.type,file_upload,video_upload',
                //            'file.*.link' => 'required_if:file.*.type,youtube_link,other_link',
            ]
        );
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102
            );
            return response()->json($response);
        }
        $validator2 = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    new uniqueTopicInLesson($request->lesson_id, $request->topic_id)
                ],
            ]
        );
        if ($validator2->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator2->errors()->first(),
                'code' => 113,
            );
            return response()->json($response);
        }
        try {
            $topic = LessonTopic::find($request->topic_id);

            $topic->name = $request->name;
            $topic->description = $request->description;
            $topic->save();

            // Update the Old Files
            if ($request->edit_file) {
                foreach ($request->edit_file as $key => $file) {
                    if ($file['type']) {
                        $topic_file = File::find($file['id']);
                        $topic_file->file_name = $file['name'];

                        if ($file['type'] == "1") {
                            // Type File :- File Upload
                            $topic_file->type = 1;
                            if (!empty($file['file'])) {
                                if (Storage::disk('public')->exists($topic_file->getRawOriginal('file_url'))) {
                                    Storage::disk('public')->delete($topic_file->getRawOriginal('file_url'));
                                }
                                $topic_file->file_url = $file['file']->store('lessons', 'public');
                            }
                        } elseif ($file['type'] == "2") {
                            // Type File :- YouTube Link Upload
                            $topic_file->type = 2;
                            if (!empty($file['thumbnail'])) {
                                if (Storage::disk('public')->exists($topic_file->getRawOriginal('file_url'))) {
                                    Storage::disk('public')->delete($topic_file->getRawOriginal('file_url'));
                                }
                                $topic_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                            }

                            $topic_file->file_url = $file['link'];
                        } elseif ($file['type'] == "3") {
                            // Type File :- Vedio Upload
                            $topic_file->type = 3;
                            if (!empty($file['file'])) {
                                if (Storage::disk('public')->exists($topic_file->getRawOriginal('file_url'))) {
                                    Storage::disk('public')->delete($topic_file->getRawOriginal('file_url'));
                                }
                                $topic_file->file_url = $file['file']->store('lessons', 'public');
                            }

                            if (!empty($file['thumbnail'])) {
                                if (Storage::disk('public')->exists($topic_file->getRawOriginal('file_url'))) {
                                    Storage::disk('public')->delete($topic_file->getRawOriginal('file_url'));
                                }
                                $topic_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                            }
                        } elseif ($file['type'] == "4") {
                            $topic_file->type = 4;
                            if (!empty($file['thumbnail'])) {
                                if (Storage::disk('public')->exists($topic_file->getRawOriginal('file_url'))) {
                                    Storage::disk('public')->delete($topic_file->getRawOriginal('file_url'));
                                }
                                $topic_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                            }
                            $topic_file->file_url = $file['link'];
                        }

                        $topic_file->save();
                    }
                }
            }

            //Add the new Files
            if ($request->file) {
                foreach ($request->file as $file) {
                    $topic_file = new File();
                    $topic_file->file_name = $file['name'];
                    $topic_file->modal()->associate($topic);

                    if ($file['type'] == "1") {
                        $topic_file->type = 1;
                        $topic_file->file_url = $file['file']->store('lessons', 'public');
                    } elseif ($file['type'] == "2") {
                        $topic_file->type = 2;
                        $topic_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                        $topic_file->file_url = $file['link'];
                    } elseif ($file['type'] == "3") {
                        $topic_file->type = 3;
                        $topic_file->file_url = $file['file']->store('lessons', 'public');
                        $topic_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                    } elseif ($file['type'] == "4") {
                        $topic_file->type = 4;
                        $topic_file->file_thumbnail = $file['thumbnail']->store('lessons', 'public');
                        $topic_file->file_url = $file['link'];
                    }
                    $topic_file->save();
                }
            }

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
                'code' => 200
            );
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response, 200);
        }
        return response()->json($response);
    }

    public function deleteTopic(Request $request)
    {
        if (!Auth::user()->can('topic-delete')) {
            $response = array(
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return redirect(route('home'))->withErrors($response);
        }
        try {
            $topic = LessonTopic::LessonTopicTeachers()->findOrFail($request->topic_id);
            $topic->delete();
            $response = array(
                'error' => false,
                'message' => trans('data_delete_successfully'),
                'code' => 200
            );
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response, 200);
        }
        return response()->json($response);
    }

    public function updateFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $file = File::find($request->file_id);
            $file->file_name = $request->name;


            if ($file->type == "1") {
                // Type File :- File Upload

                if (!empty($request->file)) {
                    if (Storage::disk('public')->exists($file->getRawOriginal('file_url'))) {
                        Storage::disk('public')->delete($file->getRawOriginal('file_url'));
                    }

                    if ($file->modal_type == "App\Models\Lesson") {

                        $file->file_url = $request->file->store('lessons', 'public');
                    } else if ($file->modal_type == "App\Models\LessonTopic") {

                        $file->file_url = $request->file->store('topics', 'public');
                    } else {

                        $file->file_url = $request->file->store('other', 'public');
                    }
                }
            } elseif ($file->type == "2") {
                // Type File :- YouTube Link Upload

                if (!empty($request->thumbnail)) {
                    if (Storage::disk('public')->exists($file->getRawOriginal('file_url'))) {
                        Storage::disk('public')->delete($file->getRawOriginal('file_url'));
                    }

                    if ($file->modal_type == "App\Models\Lesson") {

                        $file->file_thumbnail = $request->thumbnail->store('lessons', 'public');
                    } else if ($file->modal_type == "App\Models\LessonTopic") {

                        $file->file_thumbnail = $request->thumbnail->store('topics', 'public');
                    } else {

                        $file->file_thumbnail = $request->thumbnail->store('other', 'public');
                    }
                }
                $file->file_url = $request->link;
            } elseif ($file->type == "3") {
                // Type File :- Vedio Upload

                if (!empty($request->file)) {
                    if (Storage::disk('public')->exists($file->getRawOriginal('file_url'))) {
                        Storage::disk('public')->delete($file->getRawOriginal('file_url'));
                    }

                    if ($file->modal_type == "App\Models\Lesson") {

                        $file->file_url = $request->file->store('lessons', 'public');
                    } else if ($file->modal_type == "App\Models\LessonTopic") {

                        $file->file_url = $request->file->store('topics', 'public');
                    } else {

                        $file->file_url = $request->file->store('other', 'public');
                    }
                }

                if (!empty($request->thumbnail)) {
                    if (Storage::disk('public')->exists($file->getRawOriginal('file_url'))) {
                        Storage::disk('public')->delete($file->getRawOriginal('file_url'));
                    }
                    if ($file->modal_type == "App\Models\Lesson") {

                        $file->file_thumbnail = $request->thumbnail->store('lessons', 'public');
                    } else if ($file->modal_type == "App\Models\LessonTopic") {

                        $file->file_thumbnail = $request->thumbnail->store('topics', 'public');
                    } else {

                        $file->file_thumbnail = $request->thumbnail->store('other', 'public');
                    }
                }
            }
            $file->save();

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
                'data' => $file,
                'code' => 200
            );
            return response()->json($response);
        } catch (\Throwable $th) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response, 200);
        }
    }

    public function deleteFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $file = File::findOrFail($request->file_id);
            $file->delete();
            $response = array(
                'error' => false,
                'message' => trans('data_delete_successfully'),
                'code' => 200
            );
            return response()->json($response);
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response, 200);
        }
    }

    public function getAnnouncement(Request $request)
    {
        if (!Auth::user()->can('announcement-list')) {
            $response = array(
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'nullable|numeric',
            'subject_id' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $teacher = Auth::user()->teacher;
            $subject_teacher_ids = SubjectTeacher::where('teacher_id', $teacher->id);
            if ($request->class_section_id) {
                $subject_teacher_ids = $subject_teacher_ids->where('class_section_id', $request->class_section_id);
            }
            if ($request->subject_id) {
                $subject_teacher_ids = $subject_teacher_ids->where('subject_id', $request->subject_id);
            }
            $subject_teacher_ids = $subject_teacher_ids->get()->pluck('id');
            $sql = Announcement::with('table.subject', 'file', 'center:id,name')->where('table_type', 'App\Models\SubjectTeacher')->whereIn('table_id', $subject_teacher_ids);

            if ($request->center_id) {
                $sql->where('center_id', $request->center_id);
            }

            $data = $sql->orderBy('id', 'DESC')->paginate();
            $response = array(
                'error' => false,
                'message' => 'Announcement Fetched Successfully.',
                'data' => $data,
                'code' => 200,
            );
            return response()->json($response);
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
            return response()->json($response, 200);
        }
    }

    public function sendAnnouncement(Request $request)
    {
        if (!Auth::user()->can('announcement-create')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'title' => 'required',
            'description' => 'nullable',
            'file' => 'nullable'
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $classSection = ClassSchool::whereHas('class_section', function ($q) use ($request) {
                $q->where('id', $request->class_section_id);
            })->first();
            $data = getSettings('session_year', $classSection->center_id);
            $teacher_id = Auth::user()->teacher->id;
            $announcement = new Announcement();
            $announcement->title = $request->title;
            $announcement->description = $request->description;
            $announcement->session_year_id = $data['session_year'];

            $subject_teacher = SubjectTeacher::where([
                'teacher_id' => $teacher_id,
                'class_section_id' => $request->class_section_id,
                'subject_id' => $request->subject_id
            ])->with('subject')->firstOrFail();
            if ($subject_teacher) {
                $announcement->table()->associate($subject_teacher);
            }
            $user = Students::select('user_id')->where('class_section_id', $request->class_section_id)->get()->pluck('user_id');


            $title = 'New announcement in ' . $subject_teacher->subject->name;
            $body = $request->title;
            $announcement->save();
            send_notification($user, $title, $body, 'class_section');
            if ($request->hasFile('file')) {
                foreach ($request->file as $file_upload) {
                    $file = new File();
                    $file->file_name = $file_upload->getClientOriginalName();
                    $file->type = 1;
                    $file->file_url = $file_upload->store('announcement', 'public');
                    $file->modal()->associate($announcement);
                    $file->save();
                }
            }

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
                'code' => 200,
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function updateAnnouncement(Request $request)
    {
        if (!Auth::user()->can('announcement-edit')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'announcement_id' => 'required|numeric',
            'class_section_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'title' => 'required'
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $teacher_id = Auth::user()->teacher->id;
            $announcement = Announcement::findOrFail($request->announcement_id);
            $announcement->title = $request->title;
            $announcement->description = $request->description;

            $subject_teacher = SubjectTeacher::where([
                'teacher_id' => $teacher_id,
                'class_section_id' => $request->class_section_id,
                'subject_id' => $request->subject_id
            ])->with('subject')->firstOrFail();
            $announcement->table()->associate($subject_teacher);
            $user = Students::select('user_id')->where('class_section_id', $request->class_section_id)->get()->pluck('user_id');

            $title = 'Update announcement in ' . $subject_teacher->subject->name;
            $body = $request->title;
            $announcement->save();
            send_notification($user, $title, $body, 'class_section');
            if ($request->hasFile('file')) {
                foreach ($request->file as $file_upload) {
                    $file = new File();
                    $file->file_name = $file_upload->getClientOriginalName();
                    $file->type = 1;
                    $file->file_url = $file_upload->store('announcement', 'public');
                    $file->modal()->associate($announcement);
                    $file->save();
                }
            }
            $response = [
                'error' => false,
                'message' => trans('data_update_successfully'),
                'code' => 200,
            ];
        } catch (Throwable $e) {
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    public function deleteAnnouncement(Request $request)
    {
        if (!Auth::user()->can('announcement-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message'),
                'code' => 111
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'announcement_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $announcement = Announcement::findorFail($request->announcement_id);
            $announcement->delete();
            $response = array(
                'error' => false,
                'message' => trans('data_delete_successfully'),
                'code' => 200
            );
        } catch (Throwable $e) {
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            ];
        }
        return response()->json($response);
    }

    public function getAttendance(Request $request)
    {


        if (!Auth::user()->can('attendance-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $class_section_id = $request->class_section_id;
        $attendance_type = $request->type;
        $date = date('Y-m-d', strtotime($request->date));

        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required',
            'date' => 'required|date',
            'type' => 'in:0,1',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $sql = Attendance::where('class_section_id', $class_section_id)->where('date', $date);
            if (isset($attendance_type) && $attendance_type != '') {
                $sql->where('type', $attendance_type);
            }
            $data = $sql->get();
            $holiday = Holiday::where('date', $date)->get();
            if ($holiday->count()) {
                $response = array(
                    'error' => false,
                    'data' => $data,
                    'is_holiday' => true,
                    'holiday' => $holiday,
                    'message' => "Data Fetched Successfully",
                );
            } else {
                if ($data->count()) {
                    $response = array(
                        'error' => false,
                        'data' => $data,
                        'is_holiday' => false,
                        'message' => "Data Fetched Successfully",
                    );
                } else {
                    $response = array(
                        'error' => false,
                        'data' => $data,
                        'is_holiday' => false,
                        'message' => "Attendance not recorded",
                    );
                }
            }
            return response()->json($response);
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
    }


    public function submitAttendance(Request $request)
    {
        if (!Auth::user()->can('attendance-create') || !Auth::user()->can('attendance-edit')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required',
            // 'student_id' => 'required',
            'attendance.*.student_id' => 'required',
            'attendance.*.type' => 'required|in:0,1',
            'date' => 'required|date',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()

            );
            return response()->json($response);
        }
        try {
            $classSection = ClassSchool::whereHas('class_section', function ($q) use ($request) {
                $q->where('id', $request->class_section_id);
            })->first();
            $session_year = getSettings('session_year', $classSection->center_id);
            $session_year_id = $session_year['session_year'];
            $class_section_id = $request->class_section_id;
            $date = date('Y-m-d', strtotime($request->date));
            $getid = Attendance::select('id')->where([
                'date' => $date,
                'class_section_id' => $class_section_id
            ])->get()->toArray();
            for ($i = 0, $iMax = count($request->attendance); $i < $iMax; $i++) {

                if (count($getid) > 0 && isset($getid[$i]['id'])) {
                    $attendance = Attendance::find($getid[$i]['id']);
                } else {
                    $attendance = new Attendance();
                }


                $std_id = $request->attendance[$i]['student_id'];
                $type = $request->attendance[$i]['type'];
                $attendance->class_section_id = $class_section_id;
                $attendance->student_id = $std_id;
                $attendance->session_year_id = $session_year_id;
                if ($request->holiday != '' && $request->holiday == 3) {
                    $attendance->type = $request->holiday;
                } else {
                    $attendance->type = $type;
                }

                $attendance->date = $date;
                $attendance->save();

                $response = [
                    'error' => false,
                    'message' => trans('data_store_successfully')
                ];
            }
        } catch (Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e

            );
        }
        return response()->json($response);
    }

    public function getStudentList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $data = $this->studentsRepository->getStudentListForApi(
                class_section_id: $request->class_section_id
            )->get();

            $response = array(
                'error' => false,
                'message' => "Student Details Fetched Successfully",
                'data' => $data,
                'code' => 200,
            );
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getStudentDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $student_data_ids = Students::select('user_id', 'class_section_id', 'father_id', 'mother_id', 'guardian_id')
                ->where('id', $request->student_id)->get();
            $student_total_present = Attendance::where('student_id', $request->student_id)->where('type', 1)->count();
            $student_total_absent = Attendance::where('student_id', $request->student_id)->where('type', 0)->count();

            $today_date_string = Carbon::now();
            $today_date_string->toDateTimeString();
            $today_date = date('Y-m-d', strtotime($today_date_string));

            $student_today_attendance = Attendance::where('student_id', $request->student_id)->where('date', $today_date)->get();
            if ($student_today_attendance->count()) {
                foreach ($student_today_attendance as $student_attendance) {
                    if ($student_attendance['type'] == 1) {
                        $today_attendance = 'Present';
                    } else {
                        $today_attendance = 'Absent';
                    }
                }
            } else {
                $today_attendance = 'Not Taken';
            }
            foreach ($student_data_ids as $student_data_ids) {
                $father_data = Parents::where('id', $student_data_ids['father_id'])->get();
                $mother_data = Parents::where('id', $student_data_ids['mother_id'])->get();
                if ($student_data_ids['guardian_id'] != 0) {
                    $guardian_data = Parents::where('id', $student_data_ids['guardian_id'])->get();
                    $response = array(
                        'error' => false,
                        'message' => "Student Details Fetched Successfully",
                        'gurdian_data' => $guardian_data,
                        'father_data' => $father_data,
                        'mother_data' => $mother_data,
                        'total_present' => $student_total_present,
                        'total_absent' => $student_total_absent,
                        'today_attendance' => $today_attendance,
                        'code' => 200,
                    );
                } else {
                    $response = array(
                        'error' => false,
                        'message' => "Student Details Fetched Successfully",
                        'father_data' => $father_data,
                        'mother_data' => $mother_data,
                        'total_present' => $student_total_present,
                        'total_absent' => $student_total_absent,
                        'today_attendance' => $today_attendance,
                        'code' => 200,
                    );
                }
            }
            return response()->json($response);
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );

            return response()->json($response);
        }
    }

    public function getTeacherTimetable(Request $request)
    {
        try {
            $teacher = $request->user()->teacher;
            $timetable = Timetable::with('class_section', 'subject')->whereHas('subject_teacher', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })->get()->append('center_name');
            // $timetable = Timetable::where('subject_teacher_id', $teacher->id)->with('class_section', 'subject')->get();
            $response = array(
                'error' => false,
                'message' => "Timetable Fetched Successfully",
                'data' => $timetable,
                'code' => 200,
            );
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function submitExamMarksBySubjects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'class_section_id' => 'required|numeric',
            //            'marks_upload_status' => 'required|numeric|in:0,1,2'
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }

        try {

            $exam_published = Exam::where(['id' => $request->exam_id, 'teacher_status' => 1])->whereHas('exam_class_section', function ($q) {
                $q->where('publish', 1);
            })->first();
            if (isset($exam_published)) {
                $response = array(
                    'error' => true,
                    'message' => trans('exam_published'),
                    'code' => 400,
                );
                return response()->json($response);
            }

            $teacher_id = Auth::user()->teacher->id;
            SubjectTeacher::where([
                'teacher_id' => $teacher_id,
                'subject_id' => $request->subject_id
            ])->firstOrFail();
//            $class_section = ClassSection::where('id', $subject_teacher->class_section_id)->first();
            //check exam status
            $starting_date_db = ExamTimetable::select(DB::raw("min(date)"))->where([
                'exam_id' => $request->exam_id,
                'class_section_id' => $request->class_section_id
            ])->first();
            $starting_date = $starting_date_db['min(date)'];
            $ending_date_db = ExamTimetable::select(DB::raw("max(date)"))->where([
                'exam_id' => $request->exam_id,
                'class_section_id' => $request->class_section_id
            ])->first();
            $ending_date = $ending_date_db['max(date)'];
            $currentTime = Carbon::now();
            $current_date = date($currentTime->toDateString());
            if ($current_date > $starting_date && $current_date < $ending_date) {
                $exam_status = "1"; // Upcoming = 0 , On Going = 1 , Completed = 2
            } elseif ($current_date < $starting_date) {
                $exam_status = "0"; // Upcoming = 0 , On Going = 1 , Completed = 2
            } else {
                $exam_status = "2"; // Upcoming = 0 , On Going = 1 , Completed = 2
            }
            if ($exam_status != 2) {
                $response = array(
                    'error' => true,
                    'message' => trans('exam_not_completed_yet'),
                    'code' => 400
                );
                return response()->json($response);
            }

            $exam_timetable = ExamTimetable::where('exam_id', $request->exam_id)->where(['subject_id' => $request->subject_id, 'class_section_id' => $request->class_section_id])->with('exam')->whereHas('exam', function ($q) {
                $q->where('teacher_status', 1);
            })->firstOrFail();
            $classSection = ClassSchool::whereHas('class_section', static function ($q) use ($request) {
                $q->where('id', $request->class_section_id);
            })->first();
            $auto_publish_exam = getSettings('auto_publish_exams', $classSection->center_id);

            $publish_exam = $auto_publish_exam['auto_publish_exams'] ?? 0;
            $exam_result = [];
            foreach ($request->marks_data as $marks) {
                if ($marks['obtained_marks'] == '/') {
                    ExamMarks::updateOrCreate(
                        [
                            'exam_timetable_id' => $exam_timetable->id,
                            'student_id' => $marks['student_id'],
                            'subject_id' => $request->subject_id,
                        ],
                        [
                            'obtained_marks' => -1,
                            'passing_status' => 0,
                            'session_year_id' => $exam_timetable->session_year_id,
                            'grade' => null,
                        ]
                    );
                    continue;
                }
                $passing_marks = $exam_timetable->passing_marks;
                if ($marks['obtained_marks'] >= $passing_marks) {
                    $status = 1;
                } else {
                    $status = 0;
                }
                $marks_percentage = ($marks['obtained_marks'] / $exam_timetable['total_marks']) * 100;

                $exam_grade = findExamGrade($marks_percentage);
                if ($exam_grade == null) {
                    $response = array(
                        'error' => true,
                        'message' => trans('grades_data_does_not_exists'),
                    );
                    return response()->json($response);
                }
                $update_exam_marks = array(
                    'obtained_marks' => $marks['obtained_marks'],
                    'passing_status' => $status,
                    'grade' => $exam_grade,
                );
                ExamMarks::updateOrCreate([
                    'exam_timetable_id' => $exam_timetable->id,
                    'student_id' => $marks['student_id'],
                    'subject_id' => $request->subject_id,
                    'session_year_id' => $exam_timetable->session_year_id,
                ], $update_exam_marks);

                if ($exam_timetable->exam->type == 1 || $publish_exam) {

                    // add the result
                    $percentage = ($marks['obtained_marks'] / $exam_timetable['total_marks']) * 100;
                    $grade = findExamGrade($percentage);

                    if ($grade == null) {
                        $response = array(
                            'error' => true,
                            'message' => trans('grades_data_does_not_exists'),
                        );
                        return response()->json($response);
                    }
                    $exam_result[] = [
                        'exam_id' => $request->exam_id,
                        'class_section_id' => $request->class_section_id,
                        'student_id' => $marks['student_id'],
                        'total_marks' => $exam_timetable['total_marks'],
                        'obtained_marks' => $marks['obtained_marks'],
                        'percentage' => round($percentage, 2),
                        'grade' => $grade,
                        'session_year_id' => $exam_timetable->session_year_id
                    ];


                }
            }

            //If Exam Type is sequential
            if ($exam_timetable->exam->type == 1 || $publish_exam) {
                $exam_class_section = ExamClassSection::where(['class_section_id' => $request->class_section_id, 'exam_id' => $request->exam_id])->first();
                $exam_class_section->publish = 1;
                $exam_class_section->save();


                ExamResult::upsert($exam_result, ['exam_id', 'class_section_id', 'student_id'], ['total_marks', 'obtained_marks', 'percentage', 'grade', 'session_year_id']);
            }

            $exam_timetable->marks_upload_status = 1;
            $exam_timetable->save();
            //                }
            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
                'code' => 200
            );
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
                'data' => $e->getMessage() . $e->getFile() . $e->getLine()
            );
        }
        return response()->json($response);
    }


    public function submitExamMarksByStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|numeric',
            'student_id' => 'required|numeric',
            //            'class_section_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $student = Students::where('id', $request->student_id)->firstOrFail();
            $exam_published = Exam::where(['id' => $request->exam_id, 'teacher_status' => 1])->whereHas('exam_class_section', function ($q) {
                $q->where('publish', 1);
            })->first();
            if (isset($exam_published)) {
                $response = array(
                    'error' => true,
                    'message' => trans('exam_published'),
                    'code' => 400,
                );
                return response()->json($response);
            }

            //exam status
            $starting_date_db = ExamTimetable::select(DB::raw("min(date)"))->where([
                'exam_id' => $request->exam_id,
                'class_section_id' => $student->class_section_id
            ])->first();
            $starting_date = $starting_date_db['min(date)'];
            $ending_date_db = ExamTimetable::select(DB::raw("max(date)"))->where([
                'exam_id' => $request->exam_id,
                'class_section_id' => $student->class_section_id
            ])->first();
            $ending_date = $ending_date_db['max(date)'];
            $currentTime = Carbon::now();
            $current_date = date($currentTime->toDateString());
            if ($current_date > $starting_date && $current_date < $ending_date) {
                $exam_status = "1"; // Upcoming = 0 , On Going = 1 , Completed = 2
            } elseif ($current_date < $starting_date) {
                $exam_status = "0"; // Upcoming = 0 , On Going = 1 , Completed = 2
            } else {
                $exam_status = "2"; // Upcoming = 0 , On Going = 1 , Completed = 2
            }

            if ($exam_status != 2) {
                $response = array(
                    'error' => true,
                    'message' => trans('exam_not_completed_yet'),
                    'code' => 400
                );
                return response()->json($response);
            }
            $classSection = ClassSchool::whereHas('class_section', static function ($q) use ($request) {
                $q->where('id', $request->class_section_id);
            })->first();
            $auto_publish_exam = getSettings('auto_publish_exams', $classSection->center_id);
            $publish_exam = $auto_publish_exam['auto_publish_exams'] ?? 0;
            foreach ($request->marks_data as $marks) {
                $exam_timetable = ExamTimetable::where([
                    'exam_id' => $request->exam_id,
                    'subject_id' => $marks['subject_id'],
                    'class_section_id' => $student->class_section_id
                ])->firstOrFail();
                if ($marks['obtained_marks'] == '/') {
                    ExamMarks::updateOrCreate(
                        [
                            'exam_timetable_id' => $exam_timetable->id,
                            'student_id' => $marks['student_id'],

                            'subject_id' => $request->subject_id,
                        ],
                        [
                            'obtained_marks' => -1,
                            'passing_status' => 0,
                            'session_year_id' => $exam_timetable->session_year_id,
                            'grade' => null,
                        ]
                    );
                    continue;
                }
                $passing_marks = $exam_timetable->passing_marks;
                if ($marks['obtained_marks'] >= $passing_marks) {
                    $status = 1;
                } else {
                    $status = 0;
                }
                $marks_percentage = ($marks['obtained_marks'] / $exam_timetable->total_marks) * 100;

                $exam_grade = findExamGrade($marks_percentage);
                if ($exam_grade == null) {
                    $response = array(
                        'error' => true,
                        'message' => trans('grades_data_does_not_exists'),
                    );
                    return response()->json($response);
                }
                $update_exam_marks = array(
                    'obtained_marks' => $marks['obtained_marks'],
                    'passing_status' => $status,
                    'grade' => $exam_grade,
                );
                ExamMarks::updateOrCreate([
                    'exam_timetable_id' => $exam_timetable->id,
                    'subject_id' => $marks['subject_id'],
                    'student_id' => $request->student_id,
                    'session_year_id' => $exam_timetable->session_year_id,
                ], $update_exam_marks);

                //If marks upload status is Submitted
                //If Exam Type is sequential

                if ($exam_timetable->exam->type == 1 || $publish_exam) {


                    // add the result
                    $percentage = ($marks['obtained_marks'] / $exam_timetable->total_marks) * 100;
                    $grade = findExamGrade($percentage);

                    if ($grade == null) {
                        $response = array(
                            'error' => true,
                            'message' => trans('grades_data_does_not_exists'),
                        );
                        return response()->json($response);
                    }


                    $exam_result[] = [
                        'exam_id' => $request->exam_id,
                        'class_section_id' => $student->class_section_id,
                        'student_id' => $request->student_id,
                        'total_marks' => $exam_timetable['total_marks'],
                        'obtained_marks' => $marks['obtained_marks'],
                        'percentage' => round($percentage, 2),
                        'grade' => $grade,
                        'session_year_id' => $exam_timetable->session_year_id
                    ];


                    $exam_class_section = ExamClassSection::where(['class_section_id' => $student->class_section_id, 'exam_id' => $request->exam_id])->first();
                    $exam_class_section->publish = 1;
                    $exam_class_section->save();

                    ExamResult::upsert($exam_result, ['exam_id', 'class_section_id', 'student_id'], ['total_marks', 'obtained_marks', 'percentage', 'grade', 'session_year_id']);
                }
            }

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
                'code' => 200,
            );
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
                'data' => $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine()

            );
        }
        return response()->json($response);
    }


    public function GetStudentExamResult(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|nullable'
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $teacher_id = Auth::user()->teacher->id;
            $class_data = ClassSection::where('class_teacher_id', $teacher_id)->with('class.medium', 'section')->get()->first();

            $exam_marks_db = ExamClassSection::with([
                'exam.timetable' => function ($q) use ($request, $class_data) {
                    $q->where('class_section_id', $class_data->id)->with([
                        'exam_marks' => function ($q) use ($request) {
                            $q->where('student_id', $request->student_id);
                        }
                    ])->with('subject:id,name,type,image,code');
                }
            ])->with([
                'exam.results' => function ($q) use ($request) {
                    $q->where('student_id', $request->student_id)->with([
                        'student' => function ($q) {
                            $q->select('id', 'user_id', 'roll_number')->with('user:id,first_name,last_name');
                        }
                    ])->with('session_year:id,name');
                }
            ])->where('class_section_id', $class_data->id)->get();

            if (sizeof($exam_marks_db)) {
                foreach ($exam_marks_db as $data_db) {
                    $starting_date_db = ExamTimetable::select(DB::raw("min(date)"))->where([
                        'exam_id' => $data_db->exam_id,
                        'class_section_id' => $class_data->id
                    ])->first();
                    $starting_date = $starting_date_db['min(date)'];
                    $ending_date_db = ExamTimetable::select(DB::raw("max(date)"))->where([
                        'exam_id' => $data_db->exam_id,
                        'class_section_id' => $class_data->id
                    ])->first();
                    $ending_date = $ending_date_db['max(date)'];
                    $currentTime = Carbon::now();
                    $current_date = date($currentTime->toDateString());
                    if ($current_date > $starting_date && $current_date < $ending_date) {
                        $exam_status = "1"; // Upcoming = 0 , On Going = 1 , Completed = 2
                    } elseif ($current_date < $starting_date) {
                        $exam_status = "0"; // Upcoming = 0 , On Going = 1 , Completed = 2
                    } else {
                        $exam_status = "2"; // Upcoming = 0 , On Going = 1 , Completed = 2
                    }

                    // check wheather exam is completed or not
                    if ($exam_status == 2) {
                        $marks_array = array();

                        // check wheather timetable exists or not
                        if (sizeof($data_db->exam->timetable)) {
                            foreach ($data_db->exam->timetable as $timetable_db) {
                                $total_marks = $timetable_db->total_marks;
                                $exam_marks = array();
                                if (sizeof($timetable_db->exam_marks)) {
                                    foreach ($timetable_db->exam_marks as $marks_data) {
                                        $exam_marks = array(
                                            'marks_id' => $marks_data->id,
                                            'subject_name' => $marks_data->subject->name,
                                            'subject_type' => $marks_data->subject->type,
                                            'total_marks' => $total_marks,
                                            'obtained_marks' => $marks_data->obtained_marks,
                                            'grade' => $marks_data->grade,
                                        );
                                    }
                                } else {
                                    $exam_marks = (object)[];
                                }

                                $marks_array[] = array(
                                    'subject_id' => $timetable_db->subject->id,
                                    'subject_name' => $timetable_db->subject->name,
                                    'subject_type' => $timetable_db->subject->type,
                                    'total_marks' => $total_marks,
                                    'subject_code' => $timetable_db->subject->code,
                                    'marks' => $exam_marks
                                );
                            }
                            $exam_result = array();
                            if (sizeof($data_db->exam->results)) {
                                foreach ($data_db->exam->results as $result_data) {
                                    $exam_result = array(
                                        'result_id' => $result_data->id,
                                        'exam_id' => $result_data->exam_id,
                                        'exam_name' => $data_db->exam->name,
                                        'class_name' => $class_data->class->name . '-' . $class_data->section->name . ' ' . $class_data->class->medium->name,
                                        'student_name' => $result_data->student->user->first_name . ' ' . $result_data->student->user->last_name,
                                        'exam_date' => $starting_date,
                                        'total_marks' => $result_data->total_marks,
                                        'obtained_marks' => $result_data->obtained_marks,
                                        'percentage' => $result_data->percentage,
                                        'grade' => $result_data->grade,
                                        'session_year' => $result_data->session_year->name,
                                    );
                                }
                            } else {
                                $exam_result = (object)[];;
                            }
                            $data[] = array(
                                'exam_id' => $data_db->exam_id,
                                'exam_name' => $data_db->exam->name,
                                'exam_date' => $starting_date,
                                'marks_data' => $marks_array,
                                'result' => $exam_result
                            );
                        }
                    }
                }
                $response = array(
                    'error' => false,
                    'message' => "Exam Marks Fetched Successfully",
                    'data' => isset($data) ? $data : [],
                    'code' => 200,
                );
            } else {
                $response = array(
                    'error' => false,
                    'message' => "Exam Marks Fetched Successfully",
                    'data' => [],
                    'code' => 200,
                );
            }
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function GetStudentExamMarks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|nullable'
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $teacher_id = Auth::user()->teacher->id;
            $class_data = ClassSection::where('class_teacher_id', $teacher_id)->with('class.medium', 'section')->get()->first();
            $exam_marks_db = ExamClassSection::with([
                'exam.timetable' => function ($q) use ($request, $class_data) {
                    $q->where('class_section_id', $class_data->id)->with([
                        'exam_marks' => function ($q) use ($request) {
                            $q->where('student_id', $request->student_id);
                        }
                    ])->with('subject:id,name,type,image');
                }
            ])->whereHas('exam', function ($q) {
                $q->where('teacher_status', 1);
            })->where('class_section_id', $class_data->id)->get();

            if (sizeof($exam_marks_db)) {
                foreach ($exam_marks_db as $data_db) {
                    $marks_array = array();
                    foreach ($data_db->exam->timetable as $marks_db) {
                        $exam_marks = array();
                        if (sizeof($marks_db->exam_marks)) {
                            foreach ($marks_db->exam_marks as $marks_data) {
                                $exam_marks = array(
                                    'marks_id' => $marks_data->id,
                                    'subject_name' => $marks_data->subject->name,
                                    'subject_type' => $marks_data->subject->type,
                                    'total_marks' => $marks_data->timetable->total_marks,
                                    'obtained_marks' => $marks_data->obtained_marks,
                                    'grade' => $marks_data->grade,
                                );
                            }
                        } else {
                            $exam_marks = [];
                        }

                        $marks_array[] = array(
                            'subject_id' => $marks_db->subject->id,
                            'subject_name' => $marks_db->subject->name,
                            'marks' => $exam_marks
                        );
                    }
                    $data[] = array(
                        'exam_id' => $data_db->exam_id,
                        'exam_name' => $marks_db->exam->name,
                        'marks_data' => $marks_array
                    );
                }
                $response = array(
                    'error' => false,
                    'message' => "Exam Marks Fetched Successfully",
                    'data' => $data,
                    'code' => 200,
                );
            } else {
                $response = array(
                    'error' => false,
                    'message' => "Exam Marks Fetched Successfully",
                    'data' => [],
                    'code' => 200,
                );
            }
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function getExamList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'in:0,1,2,3',
            'publish' => 'in:0,1',
            'type' => 'in:1,2',
            //1 = Sequential Exam , 2 = Specific Exam
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $sql = ExamClassSection::owner()->with('exam.session_year:id,name', 'exam.sequence', 'class_section.class', 'class_section.section')
                ->whereHas('exam', function ($q) {
                    $q->where('teacher_status', 1);
                });


            if (isset($request->publish)) {
                $sql->where('publish', $request->publish);
            }

            if (isset($request->type)) {
                $sql->whereHas('exam', function ($q) use ($request) {
                    $q->where('type', $request->type);
                });
            }

            if (isset($request->class_section_id)) {
                $classSection = ClassSchool::whereHas('class_section', static function ($q) use ($request) {
                    $q->where('id', $request->class_section_id);
                })->first();
                $session_year = getSettings('session_year', $classSection->center_id);
                $sql->where('class_section_id', $request->class_section_id)->whereHas('exam', function ($q) use ($session_year) {
                    $q->where('session_year_id', $session_year['session_year']);
                });

            }
            $exam_data_db = $sql->get()->append('center_name');
            foreach ($exam_data_db as $data) {
                // date status
                $starting_date_db = ExamTimetable::select(DB::raw("min(date)"))->where(['exam_id' => $data->exam_id])->first();
                $starting_date = $starting_date_db['min(date)'];

                $ending_date_db = ExamTimetable::select(DB::raw("max(date)"))->where(['exam_id' => $data->exam_id])->first();
                $ending_date = $ending_date_db['max(date)'];

                $currentTime = Carbon::now();
                $current_date = date($currentTime->toDateString());
                if ($current_date > $starting_date && $current_date < $ending_date) {
                    $exam_status = "1"; // Upcoming = 0 , On Going = 1 , Completed = 2
                } elseif ($current_date < $starting_date) {
                    $exam_status = "0"; // Upcoming = 0 , On Going = 1 , Completed = 2
                } else {
                    $exam_status = "2"; // Upcoming = 0 , On Going = 1 , Completed = 2
                }

                // $request->status  =  0 :- all exams , 1 :- Upcoming , 2 :- On Going , 3 :- Completed
                $class_teacher_can_publish_exam = false;
                if (Auth::user()->hasRole('Class Teacher')) {
                    $classSection = ClassSchool::whereHas('class_section', static function ($q) use ($data) {
                        $q->where('id', $data->class_section_id);
                    })->first();
                    $auto_publish_exam = getSettings('auto_publish_exams', $classSection->center_id);
                    $publish_exam = $auto_publish_exam['auto_publish_exams'] ?? 0;
                    // IF Auto Publish is off and Teacher is the Class Teacher of Class Section then and then only set this true else false
                    $class_teacher_can_publish_exam = $publish_exam === "0" && Auth::user()->teacher->class_section->id === $data->class_section_id ? true : false;
                }
                if (isset($request->status)) {
                    // All Details
                    if ($request->status == 0) {
                        $exam_data[] = array(
                            'id' => $data->exam->id,
                            'name' => $data->exam->name,
                            'description' => $data->exam->description,
                            'publish' => $data->publish,
                            'session_year' => $data->exam->session_year->name,
                            'exam_starting_date' => $starting_date,
                            'exam_ending_date' => $ending_date,
                            'exam_status' => $exam_status,
                            'class_section_id' => $data->class_section_id,
                            'class_teacher_can_publish_exam' => $class_teacher_can_publish_exam,
                            'type' => $data->exam->type,
                            'center_name' => $data->center_name,
                            'class_section_name' => $data->class_section->class->name . ' ' . $data->class_section->section->name,
                            'sequence' => $data->exam->sequence,
                        );
                        // Upcoming
                    } else if ($request->status == 1) {
                        if ($exam_status == 0) {
                            $exam_data[] = array(
                                'id' => $data->exam->id,
                                'name' => $data->exam->name,
                                'description' => $data->exam->description,
                                'publish' => $data->publish,
                                'session_year' => $data->exam->session_year->name,
                                'exam_starting_date' => $starting_date,
                                'exam_ending_date' => $ending_date,
                                'exam_status' => $exam_status,
                                'class_section_id' => $data->class_section_id,
                                'class_teacher_can_publish_exam' => $class_teacher_can_publish_exam,
                                'type' => $data->exam->type,
                                'center_name' => $data->center_name,
                                'class_section_name' => $data->class_section->class->name . ' ' . $data->class_section->section->name,
                                'sequence' => $data->exam->sequence,
                            );
                        }
                        //On Going
                    } else if ($request->status == 2) {
                        if ($exam_status == 1) {
                            $exam_data[] = array(
                                'id' => $data->exam->id,
                                'name' => $data->exam->name,
                                'description' => $data->exam->description,
                                'publish' => $data->publish,
                                'session_year' => $data->exam->session_year->name,
                                'exam_starting_date' => $starting_date,
                                'exam_ending_date' => $ending_date,
                                'exam_status' => $exam_status,
                                'class_section_id' => $data->class_section_id,
                                'class_teacher_can_publish_exam' => $class_teacher_can_publish_exam,
                                'type' => $data->exam->type,
                                'center_name' => $data->center_name,
                                'class_section_name' => $data->class_section->class->name . ' ' . $data->class_section->section->name,
                                'sequence' => $data->exam->sequence,
                            );
                        }
                    } else {
                        // Show Completed
                        if ($exam_status == 2) {
                            $exam_data[] = array(
                                'id' => $data->exam->id,
                                'name' => $data->exam->name,
                                'description' => $data->exam->description,
                                'publish' => $data->publish,
                                'session_year' => $data->exam->session_year->name,
                                'exam_starting_date' => $starting_date,
                                'exam_ending_date' => $ending_date,
                                'exam_status' => $exam_status,
                                'class_section_id' => $data->class_section_id,
                                'class_teacher_can_publish_exam' => $class_teacher_can_publish_exam,
                                'type' => $data->exam->type,
                                'center_name' => $data->center_name,
                                'class_section_name' => $data->class_section->class->name . ' ' . $data->class_section->section->name,
                                'sequence' => $data->exam->sequence,
                            );
                        }
                    }
                } else {
                    $exam_data[] = array(
                        'id' => $data->exam->id,
                        'name' => $data->exam->name,
                        'description' => $data->exam->description,
                        'publish' => $data->publish,
                        'session_year' => $data->exam->session_year->name,
                        'exam_starting_date' => $starting_date,
                        'exam_ending_date' => $ending_date,
                        'exam_status' => $exam_status,
                        'class_section_id' => $data->class_section_id,
                        'class_teacher_can_publish_exam' => $class_teacher_can_publish_exam,
                        'type' => $data->exam->type,
                        'center_name' => $data->center_name,
                        'class_section_name' => $data->class_section->class->name . ' ' . $data->class_section->section->name,
                        'sequence' => $data->exam->sequence,
                    );
                }
            }

            $response = array(
                'error' => false,
                'data' => isset($exam_data) ? $exam_data : [],
                'code' => 200,
            );
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
                'data' => $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine()
            );
        }
        return response()->json($response);
    }

    public function getExamDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|nullable',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $exam_data = Exam::owner()->with('timetable.subject', 'center', 'class_section')->where(['id' => $request->exam_id, 'teacher_status' => 1])->get();
            $response = array(
                'error' => false,
                'data' => $exam_data,
                'code' => 200,
            );

        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
                'data' => $e
            );
        }
        return response()->json($response);
    }

    public function center()
    {
        $centers = Auth::user()->teacher->center_teacher->load('center:id,name');
        $data = array();
        if (count($centers)) {
            foreach ($centers as $center) {
                $data[] = [
                    'id' => $center->center->id,
                    'name' => $center->center->name
                ];
            }
            $response = [
                'error' => false,
                'message' => trans('data_fetch_successfully'),
                'data' => $data
            ];
        } else {
            $response = [
                'error' => true,
                'message' => trans('no_data_found'),
            ];
        }
        return response()->json($response);
    }

    public function createExam(Request $request)
    {
        //        if (!Auth::user()->can('exam-create')) {
        //            $response = array(
        //                'message' => trans('no_permission_message')
        //            );
        //            return redirect(route('home'))->withErrors($response);
        //        }
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required|numeric',
            'name' => 'required',
            'session_year_id' => 'required',
            'description' => 'nullable',
            'exam_term_id' => 'nullable|numeric',
            'exam_sequence_id' => 'nullable|numeric',
            'subject_id' => 'required|numeric',
            'total_marks' => 'required|numeric',
            'passing_marks' => 'required|numeric',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        try {
            DB::beginTransaction();

            $class_section = ClassSection::findOrFail($request->class_section_id);
            $subject_teacher = SubjectTeacher::where([
                'class_section_id' => $request->class_section_id,
                'teacher_id' => Auth::user()->teacher->id
            ])->firstOrFail();
            $center = $subject_teacher->class_section->class->center;

            $exam_timetable_exists = ExamTimetable::checkIfSlotAvailable($request->class_section_id, $request->date, $request->start_time, $request->end_time)->count();
            if ($exam_timetable_exists) {
                $response = array(
                    'error' => true,
                    'message' => "Other Exam already exists between " . $request->start_time . " - " . $request->end_time54
                );
                return response()->json($response);
            }
            $exam_term = ExamTerm::find($request->exam_term_id);

            $exam = new Exam();
            $exam->name = $request->name;
            $exam->description = $request->description;
            $exam->type = 1;//Sequential
            $exam->session_year_id = $exam_term->session_year_id;
            $exam->exam_term_id = (int)$request->exam_term_id;
            $exam->exam_sequence_id = (int)$request->exam_sequence_id;
            $exam->center_id = $center->id;
            $exam->teacher_status = 1;
            $exam->student_status = 1;
            $exam->save();

            $exam_classes = array(
                'exam_id' => $exam->id,
                'class_section_id' => $class_section->id,
            );
            ExamClassSection::insert($exam_classes);
            $exam_timetable_date = date('Y-m-d', strtotime($request->date));
            $timetable = array(
                'exam_id' => $exam->id,
                'subject_id' => $request->subject_id,
                'class_section_id' => $request->class_section_id,
                // 'total_marks'      => $request->total_marks,
                // 'passing_marks'    => $request->passing_marks,
                'total_marks' => 20,
                'passing_marks' => 10,
                'date' => $exam_timetable_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'session_year_id' => $exam_term->session_year_id
            );

            ExamTimetable::insert($timetable);

            $current_date = date(Carbon::now()->toDateString());

            if ($current_date >= $exam_timetable_date && $current_date <= $exam_timetable_date) {
                $exam_status = "1"; // Upcoming = 0 , On Going = 1 , Completed = 2
            } elseif ($current_date < $exam_timetable_date) {
                $exam_status = "0"; // Upcoming = 0 , On Going = 1 , Completed = 2
            } else {
                $exam_status = "2"; // Upcoming = 0 , On Going = 1 , Completed = 2
            }
            DB::commit();
            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
                'data' => array(
                    'id' => $exam->id,
                    'name' => $exam->name,
                    'description' => $exam->description,
                    'publish' => 0,
                    'session_year' => $exam->session_year->name,
                    'exam_starting_date' => $exam_timetable_date,
                    'exam_ending_date' => $exam_timetable_date,
                    'exam_status' => $exam_status,
                )
            );
        } catch (Throwable $e) {
            DB::rollBack();
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }


    public function editExam(Request $request)
    {
        $rules = [
            'exam_id' => 'required|numeric',
            //            'class_section_id' => 'nullable|numeric',
            'timetable_id' => 'required|numeric',
            //            'subject_id'       => 'nullable|numeric',
            'total_marks' => 'nullable',
            'passing_marks' => 'nullable|lte:total_marks',
            'start_time' => 'nullable',
            'date' => 'nullable',
        ];
        if (!empty($request->edit_timetable[0]["end_time"]) && $request->edit_timetable[0]["end_time"] != "00:00:00") {
            $rules['end_time'] = 'required|after:start_time';
        }
        $validator = Validator::make($request->all(), $rules,
            [
                'passing_marks.lte' => trans('passing_marks_should_less_than_or_equal_to_total_marks'),
                'end_time.after' => trans('end_time_should_be_greater_than_start_time')
            ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        try {
            DB::beginTransaction();
            $exam = Exam::where(['type' => 1, 'id' => $request->exam_id])->firstOrFail();
            $exam->teacher_status = 1;
            $exam->student_status = 1;
            $exam->save();

            if (isset($request->start_time, $request->end_time)) {
                $exam_timetable_exists = ExamTimetable::checkIfSlotAvailable($request->class_section_id, $request->date, $request->start_time, $request->end_time, $request['timetable_id'])->count();
                if ($exam_timetable_exists) {
                    $response = array(
                        'error' => true,
                        'message' => "Other Exam already exists between " . $request->start_time . " - " . $request->end_time
                    );
                    return response()->json($response);
                }
            }
            $requestData = $request->only('exam_id', 'total_marks', 'passing_marks', 'start_time');
            if (!empty($request->date)) {
                $requestData['date'] = date('Y-m-d', strtotime($request->date));
            }

            ExamTimetable::where('id', $request->timetable_id)->update($requestData);
            $response = array(
                'error' => false,
                'message' => trans('data_update_successfully'),
                'status' => 200
            );

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            );
        }

        return response()->json($response);
    }

    public function getTerms(Request $request): JsonResponse
    {
        try {
            $center_id = Auth::user()->center()->pluck('center_id');
            $sequence = ExamSequence::whereIn('center_id', $center_id)->with('term')->get();
            $data = array();
            foreach ($sequence as $key => $row) {
                $data[$key]['name'] = $row->term->name . ' - ' . $row->name;
                $data[$key]['term_name'] = $row->term->name;
                $data[$key]['sequence_name'] = $row->name;
                $data[$key]['term_id'] = $row->term->id;
                $data[$key]['sequence_id'] = $row->id;
                $data[$key]['center_id'] = $row->center_id;
            }
            $response = array(
                'error' => false,
                'data' => $data,
                'code' => 200,
            );
        } catch (\Exception) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

    public function publishExamResult(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $exam_marks_db = ExamTimetable::where('exam_id', $request->exam_id)->with('exam_marks')->get();

            foreach ($exam_marks_db as $data) {
                if (count($data->exam_marks) == 0) {
                    $response = array(
                        'error' => true,
                        'message' => trans('marks_are_not_submitted'),
                    );
                    return response()->json($response);
                }
            }

            $exam = Exam::with([
                'marks' => function ($query) {
                    $query->with('student:id,class_section_id')->selectRaw('SUM(obtained_marks) as total_obtained_marks,student_id')->groupBy('student_id');
                },
                'timetable' => function ($query) {
                    $query->selectRaw('exam_id,SUM(total_marks) as total_marks')->groupby('class_section_id');
                }
            ])->with('exam_class_section')->where(['id' => $request->exam_id, 'teacher_status' => 1])->first();
            foreach ($exam->exam_class_section as $data) {
                $starting_date_db = ExamTimetable::select(DB::raw("min(date)"))->where([
                    'exam_id' => $data->exam_id,
                    'class_section_id' => $data->id
                ])->first();
                $starting_date = $starting_date_db['min(date)'];
                $ending_date_db = ExamTimetable::select(DB::raw("max(date)"))->where([
                    'exam_id' => $data->exam_id,
                    'class_section_id' => $data->id
                ])->first();
                $ending_date = $ending_date_db['max(date)'];
                $currentTime = \Illuminate\Support\Carbon::now();
                $current_date = date($currentTime->toDateString());
                if ($current_date > $starting_date && $current_date < $ending_date) {
                    $exam_status = "1"; // Upcoming = 0 , On Going = 1 , Completed = 2
                } elseif ($current_date < $starting_date) {
                    $exam_status = "0"; // Upcoming = 0 , On Going = 1 , Completed = 2
                } else {
                    $exam_status = "2"; // Upcoming = 0 , On Going = 1 , Completed = 2
                }
                break;
            }
            $size_of_timetable_array = sizeof($exam->timetable);
            $size_of_marks_array = sizeof($exam->marks);
            if ($exam_status == 2 && $size_of_timetable_array != 0 && $size_of_marks_array != 0) {
                //If Exam timetable is empty then don't allow to publish function
                if ($exam->publish == 0) {
                    // If exam is Unpublished then Insert ExamResult records and Publish the Exam
                    $exam_result = [];
                    foreach ($exam->marks as $exam_marks) {
                        $percentage = ($exam_marks['total_obtained_marks'] * 100) / $exam->timetable[0]['total_marks'];
                        $grade = findExamGrade($percentage);

                        if ($grade == null) {
                            $response = array(
                                'error' => true,
                                'message' => trans('grades_data_does_not_exists'),
                            );
                            return response()->json($response);
                        }

                        $exam_result[] = [
                            'exam_id' => $exam->id,
                            'class_section_id' => $exam_marks['student']['class_section_id'],
                            'student_id' => $exam_marks['student_id'],
                            'total_marks' => $exam->timetable[0]['total_marks'],
                            'obtained_marks' => $exam_marks['total_obtained_marks'],
                            'percentage' => round($percentage, 2),
                            'grade' => $grade,
                            'session_year_id' => $exam->session_year_id,
                        ];
                    }
                    ExamResult::insert($exam_result);
                    $exam->publish = 1;
                } else {
                    //If Exam is already published then unpublished it and delete Exam Result
                    //                    ExamResult::where('exam_id', $id)->delete();
                    //                    $exam->publish = 0;
                }
                $exam->save();
                $response = array(
                    'error' => false,
                    'message' => trans('data_store_successfully'),
                );
            } else {
                $response = array(
                    'error' => true,
                    'message' => trans('exam_not_completed_yet'),
                );
            }
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
            );
        }
        return response()->json($response);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return response()->json($response);
        }
        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $user->reset_request = 1;
            $user->save();
            $response = array(
                'error' => false,
                'message' => "Password Reset Request Sent Successfully",
                'code' => 200,
            );
        } catch (\Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'code' => 103,
            );
        }
        return response()->json($response);
    }

}
