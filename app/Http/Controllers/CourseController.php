<?php

namespace App\Http\Controllers;

use App\Domain\Course\Repositories\CourseRepository;
use App\Domain\Course\Services\CourseReportService;
use App\Domain\Course\Services\CourseService;
use App\Domain\Course\Services\CourseTeacherService;
use App\Domain\Teacher\Services\SuperTeacherCourseService;
use App\Http\Requests\Course\CourseReportRequest;
use App\Http\Requests\Course\ShowCoursesRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Http\Requests\Teacher\SuperTeacherCoursesRequest;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseTeacher;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class CourseController extends Controller
{
    public function __construct(
        private CourseService $courseService,
        private CourseRepository $courseRepository,
        private CourseTeacherService $courseTeacherService,
        private SuperTeacherCourseService $superTeacherCourseService,
        private CourseReportService $courseReportService
    ) {}

    public function index()
    {
        if (!Auth::user()->can('course-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $super_teachers = User::whereHas("roles", function ($q) {
            $q->where("name", "Super Teacher");
        })->get()->pluck('full_name', 'id');

        $categories = CourseCategory::all()->pluck('name', 'id')->toArray();
        $categories = array(0 => __("No Category")) + $categories;

        return view('course.index', compact('super_teachers', 'categories'));
    }

    public function create() {}

    public function store(Request $request)
    {
        if (!Auth::user()->can('course-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $response = $this->courseService->createCourse($request->all());
        return response()->json($response);
    }

    public function show(ShowCoursesRequest $request)
    {
        try {
            $coursesData = $this->courseService->getCoursesData(
                $request->validated()
            );

            return response()->json($coursesData);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('course-edit')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $super_teachers = User::whereHas("roles", function ($q) {
            $q->where("name", "Super Teacher");
        })->get()->pluck('full_name', 'id');

        $categories = CourseCategory::all()->pluck('name', 'id')->toArray();
        $categories = array(0 => __("No Category")) + $categories;

        $course = Course::find($id);
        return view('course.edit', compact('super_teachers', 'course', 'categories'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        try {
            $this->courseService->updateCourse($course, $request->validated());
            return redirect('course')->with('success', 'Data Update Successfully');
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            ]);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->courseService->deleteCourse($id);
            
            return response()->json([
                'error' => false,
                'message' => trans('data_deleted_successfully')
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => trans('record_not_found')
            ], 404);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function deletesuperteacher($course_id, $user_id)
    {

        $course_teacher = CourseTeacher::where('course_id', $course_id)->where('user_id', $user_id)->first();

        $course_teacher->delete();
        $response = [
            'error' => false,
            'message' => trans('data_deleted_successfully')
        ];
        return response()->json($response);
    }

    public function superteachercourses(SuperTeacherCoursesRequest $request)
    {
        try {
            $data = $this->superTeacherCourseService->getTeacherCourses(
                $request->validated()
            );

            return response()->json($data);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            ], 500);
        }
    }



    public function material(int $id)
    {
        try {
            $data = $this->courseService->getMaterialData($id);
            return view('course.material', $data);

        } catch (AccessDeniedHttpException $e) {
            return redirect('course')->with('error', $e->getMessage());
        } catch (Throwable $e) {
            return redirect('course')->with('error', trans('error_occurred'));
        }
    }

    public function material_delete(int $id)
    {
        try {
            $this->courseService->deleteMaterial($id);
            
            return response()->json([
                'error' => false,
                'message' => trans('data_deleted_successfully')
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => trans('error_occurred')
            ], 500);
        }
    }

    public function report()
    {
        if (!Auth::user()->can('course-report')) {
            $response = [
                'error' => true,
                'message' => trans('no_permission_message')
            ];
            return response()->json($response);
        }
        return view('course.report');
    }

    public function report_list(CourseReportRequest $request)
    {
        try {
            $reportData = $this->courseReportService->generateReport(
                $request->validated()
            );

            return response()->json($reportData);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            ], 500);
        }
    }
}
