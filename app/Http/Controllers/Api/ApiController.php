<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\Event;
use App\Models\ExamTimetable;
use App\Models\Holiday;
use App\Models\SessionYear;
use App\Models\Slider;
use App\Models\Students;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->fcm_id = '';
            $user->save();
            $user->currentAccessToken()->delete();
            $response = array(
                'error'   => false,
                'message' => 'Logout Successfully done.',
                'code'    => 200,
            );
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'code'    => 103,
            );
            return response()->json($response, 200);
        }
    }

    public function getHolidays(Request $request): JsonResponse
    {
        // $validator = Validator::make($request->all(), [
        //     'assignment_id' => 'nullable|numeric',
        //     'subject_id' => 'nullable|numeric',
        // ]);

        // if ($validator->fails()) {
        //     $response = array(
        //         'error' => true,
        //         'message' => $validator->errors()->first(),
        //     );
        //     return response()->json($response);
        // }
        // $student = $request->user()->student;

        try {
            $data = Holiday::get();
            $response = array(
                'error'   => false,
                'message' => "Holidays Fetched Successfully",
                'data'    => $data,
                'code'    => 200,
            );
        } catch (\Exception $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'code'    => 103,
            );
        }
        return response()->json($response);
    }

    public function getSliders(Request $request): JsonResponse
    {
        try {
            $data = Slider::whereHas('access', static function ($q) {
                $q->owner();
            })->get();
            $response = array(
                'error'   => false,
                'message' => "Sliders Fetched Successfully",
                'data'    => $data,
                'code'    => 200,
            );
        } catch (\Exception $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'code'    => 103,
            );
        }
        return response()->json($response);
    }

    public function getSessionYear(Request $request): JsonResponse
    {
        try {
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            $data = SessionYear::find($session_year_id);
            $response = array(
                'error'   => false,
                'message' => "Session Year Fetched Successfully",
                'data'    => $data,
                'code'    => 200,
            );
        } catch (\Exception $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'code'    => 103,
            );
        }
        return response()->json($response);
    }

    public function getSettings(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:privacy_policy,contact_us,about_us,terms_condition,app_settings,fees_settings',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first(),
                'code'    => 102,
            );
            return response()->json($response);
        }
        try {
            $settings = getSettings();
            if ($request->type === "app_settings") {
                $session_year = $settings['session_year'] ?? "";
                $calender = !empty($session_year) ? SessionYear::find($session_year) : null;

                $data['app_link'] = $settings['app_link'] ?? "";
                $data['ios_app_link'] = $settings['ios_app_link'] ?? "";
                $data['app_version'] = $settings['app_version'] ?? "";
                $data['ios_app_version'] = $settings['ios_app_version'] ?? "";
                $data['force_app_update'] = $settings['force_app_update'] ?? "";
                $data['app_maintenance'] = $settings['app_maintenance'] ?? "";
                $data['session_year'] = $calender;
                $data['school_name'] = $settings['school_name'] ?? "";
                $data['school_tagline'] = $settings['school_tagline'] ?? "";
                $data['teacher_app_link'] = $settings['teacher_app_link'] ?? "";
                $data['teacher_ios_app_link'] = $settings['teacher_ios_app_link'] ?? "";
                $data['teacher_app_version'] = $settings['teacher_app_version'] ?? "";
                $data['teacher_ios_app_version'] = $settings['teacher_ios_app_version'] ?? "";
                $data['teacher_force_app_update'] = $settings['teacher_force_app_update'] ?? "";
                $data['teacher_app_maintenance'] = $settings['teacher_app_maintenance'] ?? "";
                $data['online_payment'] = $settings['online_payment'] ?? "1";
                $data['auto_publish_exams'] = $settings['auto_publish_exams'] ?? 0;

                if (isset($settings['razorpay_status']) && $settings['razorpay_status']) {
                    if (isset($settings['fees_due_date'])) {
                        $date = date('Y-m-d', strtotime($settings['fees_due_date']));
                    }
                    $data['fees_settings'] = array(
                        'razorpay_status'         => $settings['razorpay_status'],
                        'razorpay_secret_key'     => $settings['razorpay_secret_key'] ?? "",
                        'razorpay_api_key'        => $settings['razorpay_api_key'] ?? "",
                        'razorpay_webhook_secret' => $settings['razorpay_webhook_secret'] ?? "",
                        'razorpay_webhook_url'    => $settings['razorpay_webhook_url'] ?? "",
                        'currency_code'           => $settings['currency_code'] ?? "",
                        'currency_symbol'         => $settings['currency_symbol'] ?? "",
                        'fees_due_date'           => $date ?? "",
                        'fees_due_charges'        => $settings['fees_due_charges'] ?? "",
                    );
                }

                if (isset($settings['stripe_status']) && $settings['stripe_status']) {
                    if (isset($settings['fees_due_date'])) {
                        $date = date('Y-m-d', strtotime($settings['fees_due_date']));
                    }
                    $data['fees_settings'] = array(
                        'stripe_status'          => $settings['stripe_status'],
                        'stripe_publishable_key' => $settings['stripe_publishable_key'] ?? "",
                        'stripe_secret_key'      => $settings['stripe_secret_key'] ?? "",
                        'stripe_webhook_secret'  => $settings['stripe_webhook_secret'] ?? "",
                        'stripe_webhook_url'     => $settings['stripe_webhook_url'] ?? "",
                        'currency_code'          => $settings['currency_code'] ?? "",
                        'currency_symbol'        => $settings['currency_symbol'] ?? "",
                        'fees_due_date'          => $date ?? "",
                        'fees_due_charges'       => $settings['fees_due_charges'] ?? "",
                    );
                }
                if (!empty($settings['online_exam_terms_condition'])) {
                    $data['online_exam_terms_condition'] = htmlspecialchars_decode($settings['online_exam_terms_condition']);
                } else {
                    $data['online_exam_terms_condition'] = "";
                }
            } else {
                $data = $settings[$request->type] ?? "";
            }
            $response = array(
                'error'   => false,
                'message' => "Data Fetched Successfully",
                'data'    => $data,
                'code'    => 200,
            );
        } catch (\Exception $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'code'    => 103,
            );
        }
        return response()->json($response);
    }

    protected function forgotPassword(Request $request): JsonResponse
    {
        $input = $request->only('email');
        $validator = Validator::make($input, [
            'email' => "required|email"
        ]);
        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first(),
                'code'    => 102,
            );
            return response()->json($response);
        }

        try {
            $response = Password::sendResetLink($input);
            if ($response === Password::RESET_LINK_SENT) {
                $response = array(
                    'error'   => false,
                    'message' => "Forgot Password email send successfully",
                    'code'    => 200,
                );
            } else {
                $response = array(
                    'error'   => true,
                    'message' => "Cannot send Reset Password Link.Try again later.",
                    'code'    => 108,
                );
            }
        } catch (\Exception $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'code'    => 103,
            );
        }
        return response()->json($response);
    }

    protected function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password'     => 'required',
            'new_password'         => 'required|between:8,12',
            'new_confirm_password' => 'same:new_password',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first(),
                'code'    => 102,
            );
            return response()->json($response);
        }

        try {
            $user = $request->user();
            if (Hash::check($request->current_password, $user->password)) {
                $user->update(['password' => Hash::make($request->new_password)]);
                $response = array(
                    'error'   => false,
                    'message' => "Password Changed successfully.",
                    'code'    => 200,
                );
            } else {
                $response = array(
                    'error'   => true,
                    'message' => "Invalid Password",
                    'code'    => 109,
                );
            }
        } catch (\Exception $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'code'    => 103,
            );
        }
        return response()->json($response);
    }

    public function getCenters(Request $request): JsonResponse
    {
        try {
            $data = Center::get();
            $response = array(
                'error'   => false,
                'message' => "Centers Fetched Successfully",
                'data'    => $data,
                'code'    => 200,
            );
        } catch (\Exception $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'code'    => 103,
            );
        }
        return response()->json($response);
    }

    public function test(Request $request)
    {
        $timetable_date = date('Y-m-d', strtotime("2023-05-30"));


        $data = [
            ['check_start_time' => "13:10:00", 'check_end_time' => "14:10:00"],
            ['check_start_time' => "12:50:00", 'check_end_time' => "13:50:00"],
            ['check_start_time' => "13:10:00", 'check_end_time' => "13:50:00"],
            ['check_start_time' => "12:50:00", 'check_end_time' => "14:10:00"],
            ['check_start_time' => "11:00:00", 'check_end_time' => "11:50:00"],
            ['check_start_time' => "04:00:00", 'check_end_time' => "05:00:00"]
        ];

        foreach ($data as $row) {
            $check_start_time = date("H:i:s", strtotime('+1 minutes', strtotime($row['check_start_time'])));
            $check_end_time = date("H:i:s", strtotime('-1 minutes', strtotime($row['check_end_time'])));
            $exam_timetable_exists = ExamTimetable::where(['class_section_id' => 5, 'date' => $timetable_date])
                ->whereBetween('start_time', [$check_start_time, $check_end_time])
                ->orWhereBetween('end_time', [$check_start_time, $check_end_time])
                ->orWhere(function ($q) use ($check_end_time, $check_start_time) {
                    $q->where('start_time', '<=', $check_start_time)->where('end_time', '>=', $check_end_time);
                })->count();

            if ($exam_timetable_exists) {
                echo $check_start_time . " - " . $check_end_time . " : Slot is not Free<br>";
            } else {
                echo $check_start_time . " - " . $check_end_time . " : Slot is Free<br>";
            }
        }
    }

    public function course(Request $request)
    {

        if (!Auth::user()->student) {
            $validator = Validator::make($request->all(), [
                'child_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error'   => true,
                    'message' => $validator->errors()->first()
                ];
                return response()->json($response, 400);
            }

            $student_id = $request->child_id;
        } else {
            $student_id = Auth::user()->student->id;
        }


        if ($request->purchase == 'true') {
            $courses = Course::with('course_section.file')->whereHas('course_student', function ($q) use ($student_id) {
                $q->where('student_id', $student_id);
            });

            if ($request->id) {
                $courses = $courses->find($request->id);
            } else {
                $courses = $courses->paginate(10);
            }


            if ($courses) {
                $response = [
                    'error'   => false,
                    'message' => 'Data fetch successfully',
                    'data'    => $courses
                ];
            } else {
                $response = [
                    'error'   => false,
                    'message' => 'No data found',
                ];
            }
            return response()->json($response);
        }

        $course_student = CourseStudent::where('student_id', $student_id)->pluck('course_id');
        $courses = Course::with('course_section')->has('course_section')->whereNotIn('id', $course_student)->orderBy('id', 'DESC');
        if ($request->id) {
            $courses = $courses->find($request->id);
        } else {
            $courses = $courses->paginate(10);
        }
        if ($courses) {
            $response = [
                'error'   => false,
                'message' => 'Data fetch Successfully',
                'data'    => $courses

            ];
        } else {
            $response = [
                'error'   => false,
                'message' => 'No data found',
            ];
        }

        return response()->json($response);
    }

    public function buycourse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                'error'   => true,
                'message' => $validator->errors()->first()
            ];
            return response()->json($response, 400);
        }
        if (!Auth::user()->student) {
            $validator = Validator::make($request->all(), [
                'child_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error'   => true,
                    'message' => $validator->errors()->first()
                ];
                return response()->json($response, 400);
            }
            $user = $request->user();
            $children = $user->parent->children()->where('id', $request->child_id)->first();
            if (empty($children)) {
                return response()->json(array(
                    'error'   => true,
                    'message' => "Invalid Child ID Passed.",
                    'code'    => 105,
                ));
            }
        }


        if ($request->child_id) {
            $student_id = $request->child_id;
        } else {
            $student_id = Auth::user()->student->id;
        }

        $rowexists = CourseStudent::where('course_id', $request->course_id)->where('student_id', $student_id)->first();
        if (!$rowexists) {
            $course = Course::find($request->course_id);
            $course_student = new CourseStudent();
            $course_student->course_id = $request->course_id;
            $course_student->student_id = $student_id;
            $course_student->price = $course->price;
            $course_student->save();
            $courses = Course::with('course_section.file')->find($request->course_id);
            $response = array(
                'error'   => false,
                'message' => trans('Course Successfully purchased.'),
                'code'    => 200,
                'data'    => $courses
            );
        } else {
            $response = array(
                'error'   => true,
                'message' => trans('Course Already Purchased'),
                'code'    => 103,
            );
        }
        return response()->json($response);
    }

    public function event(Request $request)
    {
        if (!Auth::user()->student) {
            $validator = Validator::make($request->all(), [
                'child_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error'   => true,
                    'message' => $validator->errors()->first()
                ];
                return response()->json($response, 400);
            }
            $user = $request->user();
            $children = $user->parent->children()->where('id', $request->child_id)->first();
            if (empty($children)) {
                return response()->json(array(
                    'error'   => true,
                    'message' => "Invalid Child ID Passed.",
                    'code'    => 105,
                ));
            }
            $student_id = $request->child_id;
        } else {
            $student_id = Auth::user()->student->id;
        }
        $get_center_id = Students::find($student_id)->center_id;
        $session_year = SessionYear::where('center_id', $get_center_id)->where('default', 1)->get()->first();
        $event = Event::where('center_id', $get_center_id)->where('session_year_id', $session_year->id)->paginate(10);
        if ($event->isNotEmpty()) {
            $response = [
                'error'   => false,
                'message' => 'Data fetch Successfully',
                'data'    => $event
            ];
        } else {
            $response = [
                'error'   => false,
                'message' => 'No data found',
            ];
        }
        return response()->json($response);
    }
}
