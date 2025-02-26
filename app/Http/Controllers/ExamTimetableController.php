<?php

namespace App\Http\Controllers;

use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\ExamClassSection;
use App\Models\ExamTimetable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;
use App\Domain\Exam\Services\ExamTimetableService;

class ExamTimetableController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    private $examTimetableService;

    public function __construct(ExamTimetableService $examTimetableService) {
        $this->examTimetableService = $examTimetableService;
    }

    public function index() {
        if (!Auth::user()->canany(['exam-timetable-create', 'exam-timetable-list'])) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $exams = Exam::owner()->whereHas('exam_class_section', function ($q) {
            $q->where('publish', 0);
        })->whereHas('exam_class_section.class_section.class', function ($q) {
            $q->activeMediumOnly();
        })->where('type', 2)->get();

        $class_sections = ClassSection::owner()->with('class', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();
        return response(view('exams.exam-timetable', compact('exams', 'class_sections')));
    }

    public function store(Request $request) {
        if (!Auth::user()->can('exam-timetable-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'exam_id'                   => 'required|numeric',
                'class_section_id'          => 'required|numeric',
                'timetable.*.passing_marks' => 'lte:timetable.*.total_marks',
                'timetable.*.end_time'      => 'after:timetable.*.start_time',
            ],
            [
                'timetable.*.passing_marks.lte' => trans('passing_marks_should_less_than_or_equal_to_total_marks'),
                'timetable.*.end_time.after'    => trans('end_time_should_be_greater_than_start_time')
            ]
        );

        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        $response = $this->examTimetableService->storeTimetable($request->all());

        return response()->json($response);
    }

    public function show(Request $request) {
        if (!Auth::user()->canany(['exam-timetable-create', 'exam-timetable-list'])) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $response = $this->examTimetableService->getTimetables($request->all());

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id) {
        if (!Auth::user()->can('exam-timetable-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $response = $this->examTimetableService->deleteTimetable($id);

        return response()->json($response);
    }

    public function getClassesByExam($exam_id) {
        try {
            $exam_classes = ExamClassSection::with(['class_section.class', 'class_section.section'])->where('exam_id', $exam_id)->get();
            $response = array(
                'error' => false,
                'data'  => $exam_classes
            );
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function getSubjectsByClass($class_section_id,Request $request) {
        try {
            $class = ClassSection::find($class_section_id)->class_id;

            $exam_subjects = ClassSubject::whereHas('subject', function ($q) {
                $q->owner();
            })->with('subject')->where('class_id', $class)->get();

            $response = array(
                'error' => false,
                'data'  => $exam_subjects
            );
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }


    public function updateTimetable(Request $request) {
        if (!Auth::user()->can('exam-timetable-create') && !Auth::user()->hasRole('Teacher')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'edit_timetable'                 => 'required|array',
                'edit_timetable.*.subject_id'    => 'required',
                'edit_timetable.*.total_marks'   => 'required',
                'edit_timetable.*.passing_marks' => 'required|lte:edit_timetable.*.total_marks',
                'edit_timetable.*.start_time'    => 'required',
                'edit_timetable.*.end_time'      => 'required|after:edit_timetable.*.start_time',
                'edit_timetable.*.date'          => 'required',
            ],
            [
                'edit_timetable.*.passing_marks.lte' => trans('passing_marks_should_less_than_or_equal_to_total_marks'),
                'edit_timetable.*.end_time.after'    => trans('end_time_should_be_greater_than_start_time')
            ]
        );

        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        $response = $this->examTimetableService->updateTimetable($request->all());

        return response()->json($response);
    }

    public function deleteTimetable($id) {
        if (!Auth::user()->can('exam-timetable-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        try {
            $exam_timetable = ExamTimetable::find($id);
            $exam_timetable->delete();
            $response = array(
                'error'   => false,
                'message' => trans('data_delete_successfully'),
                'status'  => 200
            );
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function updateTotalMarks(Request $request) {
        if (!Auth::user()->can('exam-timetable-edit') && !Auth::user()->hasRole('Teacher')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'exam_timetable_id' => 'required',
                'total_marks'       => 'required',
                'passing_marks'     => 'required|lte:total_marks',
            ],
            [
                'passing_marks.lte' => trans('passing_marks_should_less_than_or_equal_to_total_marks'),
            ]
        );

        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        $response = $this->examTimetableService->updateTotalMarks($request->all());

        return response()->json($response);
    }
}