<?php

namespace App\Http\Controllers;

use App\Domain\Exam\Repositories\ExamClassSectionRepository;
use App\Domain\Exam\Repositories\ExamRepository;
use App\Domain\Exam\Repositories\ExamSequenceRepository;
use App\Domain\Exam\Repositories\ExamTermRepository;
use App\Domain\Exam\Services\ExamMarksService;
use App\Domain\Exam\Services\ExamResultService;
use App\Domain\Exam\Services\ExamService;
use App\Domain\Exam\Services\ExamTimetableService;
use App\Domain\Grade\Services\GradeService;
use App\Domain\Student\Repositories\StudentsRepository;
use App\Exceptions\ExamNotCompletedException;
use App\Exceptions\GradeNotFoundException;
use App\Exceptions\GradesOverlapException;
use App\Http\Requests\Exam\ExamMarksListRequest;
use App\Http\Requests\Exam\ShowExamResultRequest;
use App\Http\Requests\Exam\StoreExamRequest;
use App\Http\Requests\Exam\StoreSequentialExamRequest;
use App\Http\Requests\Exam\SubmitExamMarksRequest;
use App\Http\Requests\Exam\UpdateExamRequest;
use App\Http\Requests\Exam\UpdateExamResultMarksRequest;
use App\Http\Requests\StoreGradeRequest;
use App\Models\AnnualReport;
use App\Models\ClassGroup;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\EffectiveDomain;
use App\Models\Exam;
use App\Models\ExamClassSection;
use App\Models\ExamMarks;
use App\Models\ExamReport;
use App\Models\ExamReportClassDetails;
use App\Models\ExamReportClassSubject;
use App\Models\ExamReportStudentSequence;
use App\Models\ExamReportStudentSubject;
use App\Models\ExamResult;
use App\Models\ExamResultGroup;
use App\Models\ExamSequence;
use App\Models\ExamStatistics;
use App\Models\ExamTerm;
use App\Models\ExamTimetable;
use App\Models\Grade;
use App\Models\Group;
use App\Models\SessionYear;
use App\Models\Settings;
use App\Models\Students;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Printing\DashboardPrints;
use App\Printing\ExamPrints;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Brick\Math\Exception\DivisionByZeroException;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDF;
use PhpOffice\PhpWord\TemplateProcessor;
use Throwable;

class ExamController extends Controller
{
    public function __construct(
        private StudentsRepository $studentsRepository,
        private ExamRepository $examReposotiry,
        private ExamService $examService,
        private ExamTermRepository $examTermRepository,
        private ExamSequenceRepository $examSequenceRepository,
        private ExamTimetableService $examTimetableService,
        private ExamMarksService $examMarksService,
        private GradeService $gradeService,
        private ExamResultService $examResultService
        ) {}

    /**
     * Display a listing of the resource.
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function index()
    {
        if (!Auth::user()->can('create-specific-exam') && !Auth::user()->can('list-specific-exam')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class_sections = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();
        return response(view('exams.specific-exam', compact('class_sections')));
    }

    public function sequentialIndex(): Response|Redirector|RedirectResponse|Application|ResponseFactory
    {
//        $this->authorize('listSequential');
        if (!Auth::user()->can('list-sequential-exam')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $class_sections = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        $exam_terms = $this->examTermRepository->getAllCenter();

        $ids = $exam_terms->pluck('id');
        $sequences = $this->examSequenceRepository->getByTermId($ids, 1);

        return response(view('exams.sequential-exam',  compact('class_sections', 'sequences', 'exam_terms')));
    }

    public function sequentialExamStore(StoreSequentialExamRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();
            $subject = Subject::find($request->timetable_subject_id);
            $session_year = getSettings('session_year',Auth::user()->center->id);

            $request->merge([
                'subject_name' => $subject->name,
            ]);
            $exam = $this->examReposotiry->create($request->toArray());

            $this->examClassSectionRepository->create([
                'exam_id'          => $exam->id,
                'class_section_id' => $request->class_section_id,
            ]);

            $this->examService->createExamTimetable($request, $exam);
            DB::commit();

            $response = array(
                'error'   => false,
                'message' => "Exam created Successfully",
            );
        } catch (Throwable $e) {

            DB::rollBack();
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
            );

        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(StoreExamRequest $request)
    {
//        $this->authorize('create', Exam::class);

        if (!Auth::user()->can('create-specific-exam')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        
        try {
            DB::beginTransaction();

            $request->merge([
                'type' => 2,
                'subject_name' => $request->name
            ]);
            $exam = $this->examReposotiry->create($request->toArray());

            if ($request->class_section_id) {
                $exam_class_section = [];
                foreach ($request->class_section_id as $class_section_id) {
                    $data = ClassSection::find($class_section_id);
                    if ($data) {
                        $exam_class_section[] = array(
                            'exam_id'          => $exam->id,
                            'class_section_id' => $data->id,
                        );
                    }
                }
                $this->examClassSectionRepository->insertMany($exam_class_section);
            }
            DB::commit();
            $response = array(
                'error'   => false,
                'message' => "Exam created Successfully",
            );
        } catch (Throwable $e) {
            DB::rollBack();
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e->getMessage()
            );
        }
        return response()->json($response);
    }

    public function show(Request $request)
    {
        if (!Auth::user()->can('list-specific-exam')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        list($total, $res) = $this->examClassSectionRepository->getForShow($request->toArray());

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        $auto_publish_exam = getSettings('auto_publish_exams');
        $publish_exam = $auto_publish_exam['auto_publish_exams'] ?? 0;
        foreach ($res as $row) {
            $operate = '<div class="actions">';

            $publish = false;
            if (Auth::user()->hasRole('Center')) {
                $publish = true;
            }

            if (Auth::user()->hasRole('Teacher') && Auth::user()->can('exam-publish') && Auth::user()->teacher->class_section->id === $row->class_section_id && $publish_exam === "0" && $row->exam->type == 1) {
                $publish = true;
            }
            if (!$row->publish) {
                $operate .= '<a title="Create Timetable" href="' . route('exam-timetable.index', ['exam_id' => $row->exam_id, 'class_section_id' => $row->class_section_id,]) . '" class="btn btn-sm btn-primary-light btn-rounded btn-icon"><i class="feather-calendar"></i></a>';
            }

            if ($publish) {
                if ($row->publish == 0) {
                    $operate .= '<a href="#" class="btn btn-sm btn-success-light btn-rounded btn-icon publish-exam-result" data-id=' . $row->exam->id . ' title="Publish Exam Result"><i class="feather-check-circle"></i></a>&nbsp;&nbsp;';
                } else {
                    $operate .= '<a href="#" class="btn btn-sm btn-warning-light btn-rounded btn-icon publish-exam-result" data-id=' . $row->exam->id . ' title="Unpublish Exam Result"><i class="feather-x-circle"></i></a>&nbsp;&nbsp;';
                }
            }

            if (Auth::user()->can('exam-delete')) {
                $operate .= '<a href="' . route('exams.destroy', $row->id) . '" class="btn btn-sm btn-danger-light btn-rounded btn-icon delete-form" data-id=' . $row->id . '><i class="feather-trash"></i></a>';
            }


            $operate .= "</div>";
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->exam->name;
            $tempRow['description'] = $row->exam->description;
            $tempRow['class_name'] = $row->class_section->full_name;
            $tempRow['session_year_id'] = $row->session_year_id;
            $tempRow['session_year_name'] = $row->exam->session_year->name;
            $tempRow['timetable'] = $row->class_timetable;
            $tempRow['publish'] = $row->publish;
            $tempRow['teacher_status'] = $row->exam->teacher_status;
            $tempRow['student_status'] = $row->exam->student_status;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        if(request()->get('print')){
            $classSection = null;
            if (!empty($request->class_section_id)) {
                $classSection = ClassSection::find($request->class_section_id);
            }

            $pdf = ExamPrints::getInstance(get_center_id(), 'P');

            $pdf->printSpecificExamList($rows, $classSection);

            return response(
                $pdf->Output('', 'SPECIFIC EXAM LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateExamRequest $request, $id)
    {
        try {
            $this->examReposotiry->update($request->toArray(), $id);
            $response = array(
                'error'   => false,
                'message' => trans('data_update_successfully'),
            );
        } catch (Throwable $e) {
            DB::rollBack();
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e->getMessage()
            );
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $examClassSection = ExamClassSection::findOrFail($id);
            $examClassSection->exam->delete();
            $response = array(
                'error'   => false,
                'message' => trans('data_delete_successfully')
            );
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function publishExamResult($id)
    {
        try {
            $this->examTimetableService->publishResult($id);

            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                // 'message' => trans('error_occurred'),
                'message' => $e->getMessage(),
            );
        }
        return response()->json($response);
    }

    public function uploadSpecificExamMarks()
    {
        if (!Auth::user()->can('exam-upload-marks')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $exams = $this->examService->getCompletedExamsForMarksUpload();
        return response(view('exams.upload-marks.specific-exam', compact('exams')));
    }

    public function uploadSequentialExamMarks(Request $request)
    {
        if (!Auth::user()->can('exam-upload-marks')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $data = $this->examService->getSequentialExamData($request);
        $exams = $data['exams'];
        $class_sections = $data['class_sections'] ?? null;
        $sequences = $data['sequences'] ?? null;

        if ($request->ajax()) {
            return response()->json(['exams' => $exams]);
        }
        return response(view('exams.upload-marks.sequential-exam', compact('exams', 'class_sections', 'sequences')));
    }

    public function getExamSubjects($exam_id, $class_section_id)
    {
        try {
            $subjects = ExamTimetable::with('subject')->where('exam_id', $exam_id)->where('class_section_id', $class_section_id);
            if (Auth::user()->hasRole('Teacher')) {
                $subject_id = SubjectTeacher::where([
                    'teacher_id'       => Auth::user()->teacher->id,
                    'class_section_id' => $class_section_id
                ])->select('subject_id')->pluck('subject_id');
                $subjects->whereIn('subject_id', $subject_id);
            }
            $subjects = $subjects->get();
            $response = array(
                'error'   => false,
                'message' => trans('data_fetch_successfully'),
                'data'    => $subjects
            );
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function marksList(ExamMarksListRequest $request)
    {
        try {
            $data = $this->examMarksService->getMarksList($request->validated());

            return response()->json($data);

        } catch (ExamNotCompletedException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production')
                    ? trans('error_occurred')
                    : $e->getMessage()
            ], 500);
        }
    }

    public function submitMarks(SubmitExamMarksRequest $request): JsonResponse
    {
        try {
            $this->examMarksService->submitMarks($request->validated());

            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully')
            ]);

        } catch (GradeNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage(),
                'data' => $e->getMessage()
            ], 500);
        }
    }
    public function getSubjectByExam($exam_id)
    {
        try {
            $exam_timetable = ExamTimetable::with('subject')->where('exam_id', $exam_id)->get();
            $response = array(
                'error'   => false,
                'message' => trans('data_fetch_successfully'),
                'data'    => $exam_timetable
            );
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function indexGrades()
    {
        if (!Auth::user()->can('grade-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $grades = Grade::where('center_id', Auth::user()->center->id)->currentMedium()->get();
        return response(view('exams.exam-grade', compact('grades')));
    }

    public function createGrades(StoreGradeRequest $request)
    {
        try {
            $this->gradeService->validateGradesOverlap($request->grade);
            $this->gradeService->createGrades($request->validated());

            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully')
            ]);

        } catch (GradesOverlapException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage()
            ], 500);
        }
    }

    public function destroyGrades($id)
    {
        if (!Auth::user()->can('grade-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        try {
            $grade = Grade::find($id);
            $grade->delete();
            $response = array(
                'error'   => false,
                'message' => trans('data_delete_successfully'),
            );
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function getExamResultIndex(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|Redirector|RedirectResponse|Application
    {
        if (!Auth::user()->can('exam-result')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $exams = Exam::owner()->with('exam_class_section')->whereHas('exam_class_section', function ($q) {
            $q->where('publish', 1);
        })->currentSessionYear()
            ->get();

        return view('exams.show_exam_result', compact('exams'));
    }

    public function showExamResult(ShowExamResultRequest $request)
    {
        try {
            $result = $this->examResultService->getExamResults(
                $request->validated()
            );

            if ($request->filled('print')) {
                return $result;
            }

            return response()->json($result);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage()
            ], 500);
        }
    }

    public function updateExamResultMarks(UpdateExamResultMarksRequest $request)
    {
        try {
            $this->examResultService->updateResultMarks($request->validated());

            return response()->json([
                'error' => false,
                'message' => trans('data_update_successfully')
            ]);

        } catch (GradeNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);

        } catch (DivisionByZeroException $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage()
            ], 500);
        }
    }


    public function getExamReportIndex($id, $termID)
    {
        $currentSessionYear = getSettings('session_year');
        $sessionYearData = SessionYear::where('id', $currentSessionYear['session_year'])->firstOrFail();
        ExamReport::findOrFail($id);
        $examReportDetails = ExamReportStudentSubject::where(['exam_report_id' => $id, 'student_id' => 3])->get();
        $term = ExamTerm::with('sequence')->where('id', $termID)->firstOrFail();
        $student = Students::with(['user', 'class_section.class.medium', 'class_section.section', 'class_section.teacher'])->where('id', 3)->first();
        $settings = getSettings(['report_header', 'report_color', 'report_low_subject_average', 'report_blame', 'report_honor_roll']);

        $examResultGroups = ExamResultGroup::owner()
            ->with([
                'subjects' => function ($q) use ($student) {
                    $q->where('class_id', $student->class_section->class->id);
                }
            ])->whereHas('subjects.teacher', function ($q) use ($student) {
                $q->where('class_section_id', $student->class_section->id);
            })->whereHas('examResultGroupSubject', function ($q) use ($student) {
                $q->where('class_id', $student->class_section->class->id);
            })->get();
        $classSubject = ClassSubject::where('class_id', $student->class_section->class->id)->get();
        $examReportClassSubjects = ExamReportClassSubject::where('class_section_id', $student->class_section->id)->get();

        foreach ($examResultGroups as $group) {
            foreach ($group->subjects as $subject) {
                $class_subject = $classSubject->filter(function ($q) use ($subject) {
                    return $q->subject_id === $subject->id;
                })->first();

                $class_marks_details = $examReportClassSubjects->filter(function ($data) use ($subject) {
                    return $data->subject_id === $subject->id;
                })->first();
                $subject->class_subject = (object)$class_subject->toArray();
                $subject->class_details = (object)$class_marks_details->toArray();
            }
        }

        $studentClassPerformance = ExamReportClassDetails::where(['exam_report_id' => $id, 'student_id' => $student->id])->first();
        $classPerformance = ExamReportClassDetails::select(DB::raw('count(avg) as class_size,MAX(avg) as max_avg,MIN(avg) as min_avg,AVG(avg) as class_avg'))->where(['exam_report_id' => $id])->first();
        $low_subject_average = getSettings('report_low_subject_average');
        $low_subject_average = $low_subject_average['report_low_subject_average'] ?? 0;
        $grades = Grade::owner()->currentMedium()->orderBy('ending_range', 'DESC')->get();
        $effective_domain = EffectiveDomain::owner()->currentMedium()->orderBy('name', 'ASC')->get();
        $terms = ExamTerm::owner()->where('session_year_id', $currentSessionYear['session_year'])->currentMedium()->get();
        $sequences = ExamSequence::whereIn('exam_term_id', $terms->pluck('id'))->get();

        $examReportStudentSequence = ExamReportStudentSequence::where(['student_id' => $student->id, 'class_section_id' => $student->class_section->id])->whereHas('examTerm', function ($q) use ($currentSessionYear) {
            $q->where('session_year_id', $currentSessionYear['session_year']);
        })->get();

        //        Pdf::setOptions(['isHtml5ParserEnabled' => true, 'dpi' => 300]);
        $pdf = SnappyPdf::loadView('exams.result-report', compact('sessionYearData', 'student', 'examResultGroups', 'term', 'settings', 'grades', 'effective_domain', 'examReportDetails', 'studentClassPerformance', 'classPerformance', 'low_subject_average', 'terms', 'sequences', 'examReportStudentSequence'));
        return $pdf->stream();
        //        return response(view('exams.result-report', compact('sessionYearData', 'student', 'examResultGroups', 'term', 'settings', 'grades', 'effective_domain', 'examReportDetails', 'studentClassPerformance', 'classPerformance', 'low_subject_average', 'terms', 'sequences', 'examReportStudentSequence')));
    }


    public function exam_report()
    {
        $session_year = getSettings('session_year');
        $exams = Exam::whereHas('exam_class_section', function ($q) {
            $q->where('publish', 1);
        })
            ->whereHas('exam_class_section.class_section.class', function ($q) {
                $q->activeMediumOnly();
            })
            ->where('session_year_id', $session_year['session_year'])->where('type', 2)->Owner()->get()->pluck('name', 'id');

        $select_value = null;
        if (count($exams)) {
            $data = json_decode($exams, true);
            $select_value = array_keys($data)[0];
        }
        $class_groups = Group::where('center_id', get_center_id())->get()->pluck('name', 'id');

        return view('exams.report', compact('exams', 'select_value', 'class_groups'));
    }

    public function exam_report_top_students(Request $request)
    {
        $offset = 0;
        $limit = 15;
        $sort = 'obtained_marks';
        $order = 'DESC';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            $sort = $_GET['sort'];
        if (isset($_GET['order']))
            $order = $_GET['order'];

        $session_year = getSettings('session_year');
        // return $request->all();

        if ($request->class_group_id) {
            $class_group = ClassGroup::where('group_id', $request->class_group_id)->get()->pluck('class_id');
            $class_section_group = ClassSection::whereIn('class_id', $class_group)->get()->pluck('id');

            $timetable_ids = ExamTimetable::where('exam_id', $request->exam_id)->where('session_year_id', $session_year['session_year'])->whereIn('class_section_id', $class_section_group)->get()->pluck('id');
        } else {
            $timetable_ids = ExamTimetable::where('exam_id', $request->exam_id)->where('session_year_id', $session_year['session_year'])->get()->pluck('id');
        }

        $fail_student_ids = ExamMarks::where('session_year_id', $session_year['session_year'])->where('passing_status', 0)->groupBy('student_id')->whereIn('exam_timetable_id', $timetable_ids)->whereIn('exam_timetable_id', $timetable_ids)->get()->pluck('student_id');

        $sql = ExamResult::where('exam_id', $request->exam_id)->where('session_year_id', $session_year['session_year'])->whereNotIn('student_id', $fail_student_ids);

        if ($request->class_group_id) {
            $sql = $sql->whereIn('class_section_id', $class_section_group);
        }

        if ($request->class_id) {
            if ($request->section_id) {
                $class_section_id = ClassSection::where('class_id', $request->class_id)->where('section_id', $request->section_id)->get()->first();
                $sql->where('class_section_id', $class_section_id->id);
            } else {
                $class_section = ClassSection::where('class_id', $request->class_id)->pluck('id');
                $sql->whereIn('class_section_id', $class_section);
            }
        }
        if ($request->top_student != null) {
            $limit = $request->top_student;
            $total = $request->top_student;
        } else {
            $total = $sql->count();
        }

        $sql->orderBy('obtained_marks', 'DESC');
        // $total = $sql->count();
        $sql->skip($offset)->take($limit);
        // $res = $sql->get();
        $highest_marks = $sql->groupBy('obtained_marks')->get()->pluck('obtained_marks');

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;

        foreach ($highest_marks as $key => $makrs) {
            $exam_results = ExamResult::where('exam_id', $request->exam_id)->where('obtained_marks', $makrs)->where('session_year_id', $session_year['session_year']);
            if ($request->class_id) {
                if ($request->section_id) {
                    $class_section_id = ClassSection::where('class_id', $request->class_id)->where('section_id', $request->section_id)->get()->first();
                    $exam_results->where('class_section_id', $class_section_id->id);
                } else {
                    $class_section = ClassSection::where('class_id', $request->class_id)->pluck('id');
                    $exam_results->whereIn('class_section_id', $class_section);
                }
            }
            $exam_results = $exam_results->whereNotIn('student_id', $fail_student_ids)->get();
            $student_name = array();
            $total_marks = 0;
            $obtained_marks = 0;
            $percentage = 0;
            $grade = '';
            foreach ($exam_results as $result) {

                $student_name[] = " " . $result->student->user->full_name;
                $total_marks = $result->total_marks;
                $obtained_marks = $result->obtained_marks;
                $percentage = $result->percentage;
                $grade = $result->grade;
            }
            $tempRow['no'] = $no++;
            $tempRow['name'] = $student_name;
            $tempRow['total_marks'] = $total_marks;
            $tempRow['obtained_marks'] = $obtained_marks;
            $tempRow['percentage'] = number_format($percentage, 2) . " %";
            $tempRow['grade'] = $grade;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function get_class($id = null)
    {
        $session_year = getSettings('session_year');
        $classes = Exam::with('exam_class_section.class_section.class')->Owner()->where('session_year_id', $session_year['session_year'])->find($id);

        if ($classes) {
            $response = [
                'error'   => false,
                'message' => 'Data fetch Successfully',
                'data'    => $classes
            ];
        } else {
            $response = [
                'error'   => true,
                'message' => 'No data found',
            ];
        }
        return response()->json($response);
    }

    public function overall_result($exam_id = null, $class_group_id = null)
    {
        try {
            $session_year = getSettings('session_year');
            $pass = 0;
            $fail = 0;
            $classes = array();
            $total_attempt_exams = 0;

            if ($class_group_id) {
                $class_group = ClassGroup::where('group_id', $class_group_id)->get()->pluck('class_id');
                $class_section_group = ClassSection::whereIn('class_id', $class_group)->get()->pluck('id');
                $exam_timetable = ExamTimetable::where('exam_id', $exam_id)->whereIn('class_section_id', $class_section_group)->pluck('id');
            } else {
                $exam_timetable = ExamTimetable::where('exam_id', $exam_id)->pluck('id');
            }


            if (count($exam_timetable) > 0) {
                // Get publish class section
                if ($class_group_id) {
                    $exam_class_section = ExamClassSection::where('exam_id', $exam_id)->where('publish', 1)->whereHas('exam', function ($q) use ($session_year) {
                        $q->where('session_year_id', $session_year['session_year'])
                            ->Owner();
                    })->whereIn('class_section_id', $class_section_group)->get()->pluck('class_section_id');
                } else {
                    $exam_class_section = ExamClassSection::where('exam_id', $exam_id)->where('publish', 1)->whereHas('exam', function ($q) use ($session_year) {
                        $q->where('session_year_id', $session_year['session_year'])
                            ->Owner();
                    })->get()->pluck('class_section_id');
                }

                // ------------------------------

                // Overall performance
                $fail = ExamMarks::groupBy('student_id')->whereIn('exam_timetable_id', $exam_timetable)->where('passing_status', 0)->where('session_year_id', $session_year['session_year'])->whereHas('student', function ($q) use ($exam_class_section) {
                    $q->whereIn('class_section_id', $exam_class_section);
                })->get()->count();

                $pass = ExamMarks::groupBy('student_id')->whereIn('exam_timetable_id', $exam_timetable)->where('session_year_id', $session_year['session_year'])->whereHas('student', function ($q) use ($exam_class_section) {
                    $q->whereIn('class_section_id', $exam_class_section);
                })->get()->count() - $fail;

                // Class wise performance
                $fail_data = array();
                $pass_data = array();
                $classes = array();
                $total_students = 0;
                $class_sections = ClassSection::whereIn('id', $exam_class_section)->with('class')->whereHas('class', function ($q) {
                    $q->Owner()
                        ->activeMediumOnly();
                })->get()->pluck('class.id');
                foreach ($class_sections as $key => $class_section) {
                    $class_name = ClassSchool::find($class_section)->name;
                    if (!in_array($class_name, $classes)) {
                        $classes[] = $class_name;

                        $class_section_ids = ClassSection::where('class_id', $class_section)->Owner()->get()->pluck('id');

                        $exam_marks = ExamMarks::whereHas('student', function ($q) use ($class_section_ids) {
                            $q->whereIn('class_section_id', $class_section_ids)->Owner();
                        })->groupBy('student_id')->whereIn('exam_timetable_id', $exam_timetable)->where('passing_status', 0)->get()->where('session_year_id', $session_year['session_year'])->count();

                        $fail_data[] = $exam_marks;
                        $student = ExamResult::whereIn('class_section_id', $class_section_ids)->where('session_year_id', $session_year['session_year'])->where('exam_id', $exam_id)->get()->count();
                        // $student = Students::whereIn('class_section_id', $class_section_ids)->Owner()->get()->count();
                        $pass_data[] = $student - $exam_marks;
                    }
                }
                //
            }
            $session_year = getSettings('session_year');

            if ($class_group_id) {
                $exam_statistics = ExamStatistics::where('exam_id', $exam_id)->where('session_year_id', $session_year['session_year'])->whereIn('class_section_id', $class_section_group)->get();
            } else {
                $exam_statistics = ExamStatistics::where('exam_id', $exam_id)->where('session_year_id', $session_year['session_year'])->get();
            }
            $total_students = array_sum($exam_statistics->pluck('total_student')->toArray());
            $total_attempt_exams = array_sum($exam_statistics->pluck('total_attempt_student')->toArray());
            $total_pass = array_sum($exam_statistics->pluck('pass')->toArray());
            $total_fail = $total_attempt_exams - $total_pass;


            $data = [
                'pass'                => number_format($pass, 2),
                'fail'                => number_format($fail, 2),
                'class'               => $classes,
                'class_wise_pass'     => $pass_data,
                'class_wise_fail'     => $fail_data,
                'total_attempt_exams' => $total_attempt_exams,
                'total_students'      => $total_students,
                'total_pass'          => $total_pass,
                'total_fail'          => $total_fail
            ];

            $response = [
                'error'   => false,
                'message' => 'Data fetch Successfully',
                'data'    => $data,
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            $data = [
                'pass'            => 0,
                'fail'            => 0,
                'class'           => [],
                'class_wise_pass' => [],
                'class_wise_fail' => []
            ];

            $response = [
                'error'   => false,
                'message' => $th->getMessage(),
                'data'    => $data,
            ];
            return response()->json($response);
        }
    }

    public function subject_wise_highest_lowest($exam_id = null, $class_id = null, $section_id = null)
    {
        try {
            $session_year = getSettings('session_year');
            if ($class_id && $section_id == null) {
                $class_section_ids = ClassSection::where('class_id', $class_id)->get()->pluck('id');
            } else {
                $class_section_ids = ClassSection::where('class_id', $class_id)->where('section_id', $section_id)->get()->pluck('id');
            }

            $publish_exam_status = ExamClassSection::where('exam_id', $exam_id)->where('publish', 1)->get()->pluck('class_section_id');
            if ($exam_id && $class_id == null) {
                $exam_timetable = ExamTimetable::where('exam_id', $exam_id)->whereIn('class_section_id', $publish_exam_status)->where('session_year_id', $session_year['session_year'])->get()->pluck('id');
            } else {
                $exam_timetable = ExamTimetable::where('exam_id', $exam_id)->whereIn('class_section_id', $publish_exam_status)->where('session_year_id', $session_year['session_year'])->whereIn('class_section_id', $class_section_ids)->get()->pluck('id');
            }

            $subjects = Subject::Owner()->activeMediumOnly()->get();
            $highest = [];
            $lowest = [];
            foreach ($subjects as $key => $subject) {
                // HIGHEST DATA
                $highest_subject_mark = ExamMarks::whereIn('exam_timetable_id', $exam_timetable)->where('session_year_id', $session_year['session_year'])->where('subject_id', $subject->id)->max('obtained_marks');

                $lowest_subject_mark = ExamMarks::whereIn('exam_timetable_id', $exam_timetable)->where('session_year_id', $session_year['session_year'])->where('subject_id', $subject->id)->min('obtained_marks');
                $highest_data = Subject::select('id', 'name')->with([
                    'exam_marks' => function ($q) use ($session_year, $exam_timetable, $highest_subject_mark) {
                        $q->whereIn('exam_timetable_id', $exam_timetable)
                            ->where('session_year_id', $session_year['session_year'])
                            ->where('obtained_marks', $highest_subject_mark);
                    }, 'exam_marks.student.user:id,first_name,last_name', 'exam_marks.timetable:id,total_marks'
                ])->find($subject->id);
                // LOWEST DATA
                $lowest_data = Subject::select('id', 'name')->with([
                    'exam_marks' => function ($q) use ($session_year, $exam_timetable, $lowest_subject_mark) {
                        $q->whereIn('exam_timetable_id', $exam_timetable)
                            ->where('session_year_id', $session_year['session_year'])
                            ->where('obtained_marks', $lowest_subject_mark);
                    }, 'exam_marks.student.user:id,first_name,last_name', 'exam_marks.timetable:id,total_marks'
                ])->find($subject->id);

                if (count($highest_data->exam_marks)) {
                    $highest[] = $highest_data;
                }
                if (count($lowest_data->exam_marks)) {
                    $lowest[] = $lowest_data;
                }
            }


            // GRAPH DATA
            $exam_class_section = ExamClassSection::where('exam_id', $exam_id)->where('publish', 1)->whereHas('exam', function ($q) use ($session_year) {
                $q->where('session_year_id', $session_year['session_year'])
                    ->Owner();
            })->get()->pluck('class_section_id');
            // ------------------------------

            // EXAM WISE
            // return $exam_timetable;
            $fail = ExamMarks::groupBy('student_id')->whereIn('exam_timetable_id', $exam_timetable)->where('passing_status', 0)->where('session_year_id', $session_year['session_year'])->with([
                'student' => function ($q) use ($exam_class_section) {
                    $q->whereIn('class_section_id', $exam_class_section);
                }
            ])->get()->count();

            $pass = ExamMarks::groupBy('student_id')->whereIn('exam_timetable_id', $exam_timetable)->where('session_year_id', $session_year['session_year'])->whereHas('student', function ($q) use ($exam_class_section) {
                $q->whereIn('class_section_id', $exam_class_section);
            })->get()->count() - $fail;


            // GENDER WISE RATIO
            $fail_student_ids = ExamMarks::groupBy('student_id')->whereIn('exam_timetable_id', $exam_timetable)->where('passing_status', 0)->get()->pluck('student_id');

            if ($class_id) {
                $passedStudents = Students::Owner()->whereNotIn('id', $fail_student_ids)
                    ->whereHas('studentSessions', function ($query) use ($class_section_ids) {
                        $query->where('session_year_id', getSettings('session_year')['session_year']);
                        $query->whereIn('class_section_id', $class_section_ids);
                    });

                $total_student = $passedStudents->get()->count();

                $male_student = $passedStudents->whereHas('user', function ($q) {
                        $q->where('gender', 'male');
                })->get()->count();

            } else {
                $total_student = Students::Owner()->whereNotIn('id', $fail_student_ids)->whereIn('class_section_id', $publish_exam_status)->get()->count();
                $male_student = Students::Owner()->whereNotIn('id', $fail_student_ids)->whereIn('class_section_id', $publish_exam_status)->whereHas('user', function ($q) {
                    $q->where('gender', 'male');
                })->get()->count();
            }
            $female_student = $total_student - $male_student;

            $data = [
                'highest'      => $highest,
                'lowest'       => $lowest,
                'pass'         => number_format($pass, 2),
                'fail'         => number_format($fail, 2),
                'total_male'   => $male_student,
                'total_female' => $female_student
            ];

            $response = [
                'error'   => false,
                'message' => 'Data fetch Successfully',
                'data'    => $data
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            $data = [
                'highest'      => '',
                'lowest'       => '',
                'pass'         => 0,
                'fail'         => 0,
                'total_male'   => 0,
                'total_female' => 0
            ];

            $response = [
                'error'   => false,
                'message' => 'Data fetch Successfully',
                'data'    => $data
            ];

            return response()->json($response);
        }
    }

    public function class_wise_report(Request $request)
    {
        $offset = $_GET['offset'] ?? 0;
        $limit = $_GET['limit'] ?? 15;
        $sort = $_GET['sort'] ?? 'obtained_marks';
        $order = $_GET['order'] ?? 'DESC';

        $session_year = getSettings('session_year');
        $exam_id = $request->exam_id;

        // GET FAIL STUDENT ID
        if ($request->class_group_id) {
            $class_group = ClassGroup::where('group_id', $request->class_group_id)->get()->pluck('class_id');
            $class_section_group = ClassSection::whereIn('class_id', $class_group)->get()->pluck('id');

            $timetable_ids = ExamTimetable::where('exam_id', $exam_id)->where('session_year_id', $session_year['session_year'])->whereIn('class_section_id', $class_section_group)->get()->pluck('id');
            $exam_class_section = ExamClassSection::where('exam_id', $exam_id)->whereIn('class_section_id', $class_section_group)->get()->pluck('class_section.class_id')->toArray();
        } else {
            $timetable_ids = ExamTimetable::where('exam_id', $exam_id)->where('session_year_id', $session_year['session_year'])->get()->pluck('id');
            $exam_class_section = ExamClassSection::where('exam_id', $exam_id)->get()->pluck('class_section.class_id')->toArray();
        }

        $fail_student_ids = ExamMarks::where('session_year_id', $session_year['session_year'])->where('passing_status', 0)->groupBy('student_id')->whereIn('exam_timetable_id', $timetable_ids)->get()->pluck('student_id');
        $class_ids = array_unique($exam_class_section);


        $sql = ClassSchool::whereIn('id', $class_ids)->with(['class_section' => function ($q) use ($exam_id, $session_year, $fail_student_ids) {
            $q->withSum(['exam_statistic' => function ($query) use ($exam_id, $session_year) {
                $query->where('exam_id', $exam_id)->where('session_year_id', $session_year['session_year']);
            }], 'total_student')
                ->withSum(['exam_statistic' => function ($query) use ($exam_id, $session_year) {
                    $query->where('exam_id', $exam_id)->where('session_year_id', $session_year['session_year']);
                }], 'total_attempt_student')
                ->withSum(['exam_statistic' => function ($query) use ($exam_id, $session_year) {
                    $query->where('exam_id', $exam_id)->where('session_year_id', $session_year['session_year']);
                }], 'pass')
                ->withMax(['exam_result' => function ($q) use ($exam_id, $session_year, $fail_student_ids) {
                    $q->where('exam_id', $exam_id)->where('session_year_id', $session_year['session_year'])->whereNotIn('student_id', $fail_student_ids);
                }], 'percentage')
                ->withMin(['exam_result' => function ($q) use ($exam_id, $session_year, $fail_student_ids) {
                    $q->where('exam_id', $exam_id)->where('session_year_id', $session_year['session_year'])->whereNotIn('student_id', $fail_student_ids);
                }], 'percentage');
        }]);

        $total = $sql->count();
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;

        foreach ($res as $key => $row) {
            $total_students = 0;
            $total_attempt = 0;
            $total_pass = 0;
            $pass_per = 0;
            $fail_per = 0;
            $highest_per = array();
            $lowest_per = array();
            foreach ($row->class_section as $class_section) {
                $total_students += $class_section->exam_statistic_sum_total_student;
                $total_attempt += $class_section->exam_statistic_sum_total_attempt_student;
                $total_pass += $class_section->exam_statistic_sum_pass;
                $highest_per[] = $class_section->exam_result_max_percentage;
                $lowest_per[] = $class_section->exam_result_min_percentage;
            }
            if ($total_attempt) {
                $pass_per = number_format(($total_pass * 100) / $total_attempt, 2);
                $fail_per = number_format(100 - $pass_per, 2);
            }

            $lowest_per = array_filter($lowest_per);
            $highest_per = array_filter($highest_per);

            $tempRow['no'] = $no++;
            $tempRow['class_name'] = $row->name;
            $tempRow['total_student'] = $total_students;
            $tempRow['total_attempt'] = $total_attempt;
            $tempRow['pass'] = $pass_per . ' %';
            $tempRow['fail'] = $fail_per . ' %';
            $tempRow['highest_per'] = number_format(max($highest_per), 2) . ' %';
            $tempRow['lowest_per'] = number_format(min($lowest_per), 2) . ' %';

            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function fail_student_list(Request $request)
    {
        $offset = 0;
        $limit = 10;
        $sort = 'obtained_marks';
        $order = 'DESC';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            $sort = $_GET['sort'];
        if (isset($_GET['order']))
            $order = $_GET['order'];

        $session_year = getSettings('session_year');
        if ($request->class_group_id) {
            $class_group = ClassGroup::where('group_id', $request->class_group_id)->get()->pluck('class_id');
            $class_section_group = ClassSection::whereIn('class_id', $class_group)->get()->pluck('id');

            $timetable_ids = ExamTimetable::where('exam_id', $request->exam_id)->where('session_year_id', $session_year['session_year'])->whereIn('class_section_id', $class_section_group)->get()->pluck('id');
        } else {
            $timetable_ids = ExamTimetable::where('exam_id', $request->exam_id)->where('session_year_id', $session_year['session_year'])->get()->pluck('id');
        }
        $fail_student_ids = ExamMarks::where('session_year_id', $session_year['session_year'])->where('passing_status', 0)->groupBy('student_id')->whereIn('exam_timetable_id', $timetable_ids)->get()->pluck('student_id');

        $sql = ExamResult::where('exam_id', $request->exam_id)->where('session_year_id', $session_year['session_year'])->whereIn('student_id', $fail_student_ids);

        if ($request->class_group_id) {
            $sql = $sql->whereIn('class_section_id', $class_section_group);
        }

        if ($request->class_id) {
            if ($request->section_id) {
                $class_section_id = ClassSection::where('class_id', $request->class_id)->where('section_id', $request->section_id)->get()->first();
                $sql->where('class_section_id', $class_section_id->id);
            } else {
                $class_section = ClassSection::where('class_id', $request->class_id)->pluck('id');
                $sql->whereIn('class_section_id', $class_section);
            }
        }

        $total = $sql->count();
        $sql->orderBy('obtained_marks', 'DESC');
        $sql->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;

        foreach ($res as $key => $row) {

            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->student->user->full_name;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function top_students(Request $request)
    {
        $session_year = getSettings('session_year');

        $timetable_ids = ExamTimetable::where('exam_id', $request->exam_id)->where('session_year_id', $session_year['session_year'])->groupBy('class_section_id')->get()->pluck('id');


        $fail_student_ids = ExamMarks::where('session_year_id', $session_year['session_year'])->where('passing_status', 0)->groupBy('student_id')->whereIn('exam_timetable_id', $timetable_ids)->whereIn('exam_timetable_id', $timetable_ids)->get()->pluck('student_id');

        $highest_marks = array();

        $classes = ExamClassSection::where('exam_id', $request->exam_id)->get()->pluck('class_section.class_id')->toArray();

        $classes = array_unique($classes);
        $class_school = ClassSchool::with([
            'class_section' => function ($q) use ($request, $fail_student_ids, $session_year) {
                $q->withMax([
                    'exam_result' => function ($q) use ($request, $fail_student_ids, $session_year) {
                        $q->where('exam_id', $request->exam_id)
                            ->whereNotIn('student_id', $fail_student_ids)
                            ->where('session_year_id', $session_year['session_year']);
                    }
                ], 'obtained_marks');
            }
        ])->whereIn('id', $classes)->get();

        $class_section_ids = array();
        foreach ($class_school as $key => $class) {
            $highest_marks[] = $class->class_section->max('exam_result_max_obtained_marks');
            foreach ($class->class_section as $class_section) {
                $class_section_ids[] = $class_section->id;
            }
        }

        // return $class_section_ids;
        $data = array();

        foreach ($class_section_ids as $key => $class_section) {
            $student_name = array();

            foreach ($highest_marks as $key => $mark) {

                $exam_results = ExamResult::select('class_section_id', 'student_id', 'obtained_marks', 'percentage', 'exam_id')->where('class_section_id', $class_section)->where('obtained_marks', $mark)->where('session_year_id', $session_year['session_year'])->where('exam_id', $request->exam_id)->get();

                if (count($exam_results) > 0) {

                    foreach ($exam_results as $key => $exam_result) {
                        $student_name[] = $exam_result->student->full_name;
                    }
                }
                break;
            }
            $data[] = [
                'student_name'   => str_replace(',', ', ', implode(',', $student_name)),
                'class'          => $exam_result->class_section->class->name,
                'obtained_marks' => $exam_result->obtained_marks,
                'percentage'     => number_format($exam_result->percentage, 2) . ' %',
            ];
        }

        $data = array_filter($data);
        $response = [
            'error'   => false,
            'message' => 'Data fetch successfully',
            'data'    => $data
        ];

        return response()->json($response);
    }

    public function exam_overview(Request $request)
    {

        $session_year = getSettings('session_year');
        $exams_overview = Exam::select('id', 'name')->whereHas('exam_class_section', function ($q) {
            $q->where('publish', 1);
        })->with(['exam_statistics' => function ($q) use ($session_year) {
            $q->where('session_year_id', $session_year['session_year']);
        }])
            ->withSum(['exam_statistics' => function ($q) use ($session_year) {
                $q->where('session_year_id', $session_year['session_year']);
            }], 'pass')
            ->withSum(['exam_statistics' => function ($q) use ($session_year) {
                $q->where('session_year_id', $session_year['session_year']);
            }], 'total_attempt_student')
            ->where('session_year_id', $session_year['session_year'])->with('exam_statistics.class_section.class', 'exam_statistics.class_section.section')->find($request->exam_id);

        $response = [
            'error'   => false,
            'message' => 'Data fetch successfully',
            'data'    => $exams_overview
        ];
        return response()->json($response);
    }

    public function class_report($report_id)
    {
        $data = array();
        $header_left = getSettings('report_left_header', null, getCurrentMedium()->id);
        $header_right = getSettings('report_right_header', null, getCurrentMedium()->id);
        $header_left = $header_left['report_left_header'];
        $header_right = $header_right['report_right_header'];
        $header_logo = Settings::where('type', 'report_header_logo')->where('center_id', get_center_id())->currentMedium()->first();
        $session_year = getSettings('session_year');


        $exam_report = ExamReport::withMax(['exam_report_class_detail as male_highest_avg' => function ($q) {
            $q->whereHas('student.user', function ($q) {
                $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
            });
        }], 'avg')
            ->withMax(['exam_report_class_detail as female_highest_avg' => function ($q) {
                $q->whereHas('student.user', function ($q) {
                    $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                });
            }], 'avg')
            ->withMin(['exam_report_class_detail as male_lowest_avg' => function ($q) {
                $q->whereHas('student.user', function ($q) {
                    $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
                });
            }], 'avg')
            ->withMin(['exam_report_class_detail as female_lowest_avg' => function ($q) {
                $q->whereHas('student.user', function ($q) {
                    $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                });
            }], 'avg')
            ->withCount(['exam_report_class_detail as male_more_than_ten' => function ($q) {
                $q->where('avg', '>=', 10)
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
                    });
            }])
            ->withCount(['exam_report_class_detail as female_more_than_ten' => function ($q) {
                $q->where('avg', '>=', 10)
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                    });
            }])
            ->withCount(['exam_report_class_detail as male_less_than_ten' => function ($q) {
                $q->where('avg', '<=', 10)
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
                    });
            }])
            ->withCount(['exam_report_class_detail as female_less_than_ten' => function ($q) {
                $q->where('avg', '<=', 10)
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                    });
            }])
            ->withCount(['attendance as total_days' => function ($q) use ($session_year) {
                $q->select(DB::raw('count(distinct(date))'))->where('session_year_id', $session_year['session_year']);
            }])
            ->withCount(['student as total_male_student' => function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
                });
            }])
            ->withCount(['student as total_female_student' => function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                });
            }])
            ->withCount(['attendance as total_male_present' => function ($q) use ($session_year) {
                $q->where('type', 1)->where('session_year_id', $session_year['session_year'])
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
                    });
            }])
            ->withCount(['attendance as total_female_present' => function ($q) use ($session_year) {
                $q->where('type', 1)->where('session_year_id', $session_year['session_year'])
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                    });
            }])
            ->with(['top_student' => function ($q) {
                $q->take(5)->orderBy('rank', 'asc');
            }])
            ->with(['last_student' => function ($q) {
                $q->take(5)->orderBy('rank', 'DESC');
            }])
            ->with(['exam_report_class_detail' => function ($q) {
                $q->orderBy('rank', 'asc');
            }])
            ->find($report_id)->append('attendance');
        $exam_report->class_section->class;
        $exam_report->class_section->class->stream;
        $exam_report->class_section->section;

        $header_color = getSettings('report_color');
        if ($header_color) {
            $header_color = $header_color['report_color'];
        } else {
            $header_color = '#000000';
        }

        $encouragement = getSettings('encouragement', null, getCurrentMedium()->id);
        $congratulations = getSettings('congratulations', null, getCurrentMedium()->id);
        $warning = getSettings('warning', null, getCurrentMedium()->id);
        $report_blame = getSettings('report_blame', null, getCurrentMedium()->id);
        $report_honor_roll = getSettings('report_honor_roll', null, getCurrentMedium()->id);

        if (!$encouragement) {
            $encouragement = 10;
        } else {
            $encouragement = $encouragement['encouragement'];
        }
        if (!$congratulations) {
            $congratulations = 10;
        } else {
            $congratulations = $congratulations['congratulations'];
        }
        if (!$warning) {
            $warning = 10;
        } else {
            $warning = $warning['warning'];
        }
        if (!$report_blame) {
            $report_blame = 10;
        } else {
            $report_blame = $report_blame['report_blame'];
        }
        if (!$report_honor_roll) {
            $report_honor_roll = 10;
        } else {
            $report_honor_roll = $report_honor_roll['report_honor_roll'];
        }

        $settings = getSettings([
            'report_blame',
            'report_honor_roll',
            'report_honor_roll_absences',
            'report_blame_min',
            'report_blame_max',
            'report_warning_min',
            'report_warning_max',
            'average_blame_min',
            'average_blame_max',
            'average_warning_min',
            'average_warning_max',
            'encouragement_min',
            'encouragement_max',
            'congratulations_min',
            'congratulations_max',
        ], get_center_id(), getCurrentMedium()->id);

        $data = [
            'header_left'  => $header_left,
            'header_right' => $header_right,
            'header_logo'  => $header_logo,
            'header_color' => $header_color,

            'encouragement'     => $encouragement,
            'congratulations'   => $congratulations,
            'warning'           => $warning,
            'report_blame'      => $report_blame,
            'report_honor_roll' => $report_honor_roll,

        ];
        $data = array_merge($data, $settings);


        $pdf = ExamPrints::getInstance(get_center_id(), 'P');

        $pdf->printClassReport($exam_report, $data);

        return response(
            $pdf->Output('', 'CLASS EXAM REPORT.pdf'),
            200,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
        // $pdf = PDF::loadView('exams.class_report_pdf', compact('data', 'exam_report'));
        // return $pdf->stream();
    }

    public function annual_class_report($report_id)
    {
        $data = array();
        $header_left = getSettings('report_left_header', null, getCurrentMedium()->id);
        $header_right = getSettings('report_right_header', null, getCurrentMedium()->id);
        $header_left = $header_left['report_left_header'];
        $header_right = $header_right['report_right_header'];
        $header_logo = Settings::where('type', 'report_header_logo')->where('center_id', get_center_id())->currentMedium()->first();
        $session_year = getSettings('session_year');


        $exam_report = AnnualReport::withMax(['annual_report_class_detail as male_highest_avg' => function ($q) {
            $q->whereHas('student.user', function ($q) {
                $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
            });
        }], 'avg')
            ->withMax(['annual_report_class_detail as female_highest_avg' => function ($q) {
                $q->whereHas('student.user', function ($q) {
                    $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                });
            }], 'avg')
            ->withMin(['annual_report_class_detail as male_lowest_avg' => function ($q) {
                $q->whereHas('student.user', function ($q) {
                    $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
                });
            }], 'avg')
            ->withMin(['annual_report_class_detail as female_lowest_avg' => function ($q) {
                $q->whereHas('student.user', function ($q) {
                    $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                });
            }], 'avg')
            ->withCount(['annual_report_class_detail as male_more_than_ten' => function ($q) {
                $q->where('avg', '>=', 10)
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
                    });
            }])
            ->withCount(['annual_report_class_detail as female_more_than_ten' => function ($q) {
                $q->where('avg', '>=', 10)
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                    });
            }])
            ->withCount(['annual_report_class_detail as male_less_than_ten' => function ($q) {
                $q->where('avg', '<=', 10)
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
                    });
            }])
            ->withCount(['annual_report_class_detail as female_less_than_ten' => function ($q) {
                $q->where('avg', '<=', 10)
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                    });
            }])
            ->withCount(['attendance as total_days' => function ($q) use ($session_year) {
                $q->select(DB::raw('count(distinct(date))'))->where('session_year_id', $session_year['session_year']);
            }])
            ->withCount(['student as total_male_student' => function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
                });
            }])
            ->withCount(['student as total_female_student' => function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                });
            }])
            ->withCount(['attendance as total_male_present' => function ($q) use ($session_year) {
                $q->where('type', 1)->where('session_year_id', $session_year['session_year'])
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
                    });
            }])
            ->withCount(['attendance as total_female_present' => function ($q) use ($session_year) {
                $q->where('type', 1)->where('session_year_id', $session_year['session_year'])
                    ->whereHas('student.user', function ($q) {
                        $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                    });
            }])
            ->with(['top_student' => function ($q) {
                $q->take(5)->orderBy('rank', 'asc');
            }])
            ->with(['last_student' => function ($q) {
                $q->take(5)->orderBy('rank', 'DESC');
            }])
            ->with(['annual_report_class_detail' => function ($q) {
                $q->orderBy('rank', 'asc');
            }])
            ->find($report_id)->append('attendance');
        $exam_report->class_section->class;
        $exam_report->class_section->class->stream;
        $exam_report->class_section->section;

        $header_color = getSettings('report_color');
        if ($header_color) {
            $header_color = $header_color['report_color'];
        } else {
            $header_color = '#000000';
        }

        $encouragement = getSettings('encouragement', null, getCurrentMedium()->id);
        $congratulations = getSettings('congratulations', null, getCurrentMedium()->id);
        $warning = getSettings('warning', null, getCurrentMedium()->id);
        $report_blame = getSettings('report_blame', null, getCurrentMedium()->id);
        $report_honor_roll = getSettings('report_honor_roll', null, getCurrentMedium()->id);

        if (!$encouragement) {
            $encouragement = 10;
        } else {
            $encouragement = $encouragement['encouragement'];
        }
        if (!$congratulations) {
            $congratulations = 10;
        } else {
            $congratulations = $congratulations['congratulations'];
        }
        if (!$warning) {
            $warning = 10;
        } else {
            $warning = $warning['warning'];
        }
        if (!$report_blame) {
            $report_blame = 10;
        } else {
            $report_blame = $report_blame['report_blame'];
        }
        if (!$report_honor_roll) {
            $report_honor_roll = 10;
        } else {
            $report_honor_roll = $report_honor_roll['report_honor_roll'];
        }

        $settings = getSettings([
            'report_blame',
            'report_honor_roll',
            'report_honor_roll_absences',
            'report_blame_min',
            'report_blame_max',
            'report_warning_min',
            'report_warning_max',
            'average_blame_min',
            'average_blame_max',
            'average_warning_min',
            'average_warning_max',
            'encouragement_min',
            'encouragement_max',
            'congratulations_min',
            'congratulations_max',
        ], get_center_id(), getCurrentMedium()->id);

        $data = [
            'header_left'  => $header_left,
            'header_right' => $header_right,
            'header_logo'  => $header_logo,
            'header_color' => $header_color,

            'encouragement'     => $encouragement,
            'congratulations'   => $congratulations,
            'warning'           => $warning,
            'report_blame'      => $report_blame,
            'report_honor_roll' => $report_honor_roll,

        ];
        $data = array_merge($data, $settings);


        $pdf = ExamPrints::getInstance(get_center_id(), 'P');

        $pdf->printAnnualClassReport($exam_report, $data);

        return response(
            $pdf->Output('', 'ANNUAL CLASS EXAM REPORT.pdf'),
            200,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
        // $pdf = PDF::loadView('exams.class_report_pdf', compact('data', 'exam_report'));
        // return $pdf->stream();
    }

    public function honor_roll($report_id)
    {

        return view('exams.honor_roll', compact('report_id'));
    }

    public function honor_roll_student_list($report_id, Request $request)
    {
        if (!Auth::user()->can('class-report')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'DESC';
        $currentMedium = getCurrentMedium();

        $session_year = getSettings('session_year');

        $report_honor_roll = getSettings('report_honor_roll', null, getCurrentMedium()->id);
        if (!$report_honor_roll) {
            $report_honor_roll = 10;
        }

        $sql = ExamReportClassDetails::where('exam_report_id', $report_id)->where('avg', '>=', $report_honor_roll['report_honor_roll']);

        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->orWhereHas('student.user', function ($q) use ($search) {
                    $q->where(DB::raw('CONCAT_WS(" ", first_name, last_name)'), 'like', "%$search%");
                });
            });
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;

        foreach ($res as $row) {

            $row = (object)$row;
            $operate = '<a href=' . url('student-honor-roll-file', $row->id) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-icon edit-data default-button" data-id=' . $row->id . ' title="Class Report" data-toggle="modal" data-target="#editModal"><i class="fa fa-file"></i></a>&nbsp;&nbsp;';


            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['student_name'] = $row->student->user->full_name;
            $tempRow['action'] = $operate;
            // $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function honor_roll_certificate($id)
    {


        $certificate_file = getSettings('honor_roll_certificate_file');
        if ($certificate_file) {
            $exam_report_detail = ExamReportClassDetails::find($id);

            $student_name = $exam_report_detail->student->user->full_name;
            $term_name = $exam_report_detail->exam_report->exam_term->name;
            $session_year = $exam_report_detail->exam_report->session_year->name;

            $class_section = $exam_report_detail->exam_report->class_section->class->name . ' - ' . $class_section = $exam_report_detail->exam_report->class_section->section->name . ' - ' . $class_section = $exam_report_detail->exam_report->class_section->class->medium->name;

            $certificate_file = $certificate_file['honor_roll_certificate_file'];

            $school_certificate = new TemplateProcessor($certificate_file);
            $school_certificate->setValue('student_name', $student_name);
            $school_certificate->setValue('class_section', $class_section);
            $school_certificate->setValue('term', $term_name);
            $school_certificate->setValue('session_year', $session_year);
            $school_certificate->setValue('average', $exam_report_detail->avg . '/20 ');
            $school_certificate->setValue('rank', $exam_report_detail->rank);

            $school_certificate->saveAs('student-honor-roll-certificate.docx');
            return response()->download('student-honor-roll-certificate.docx');
        } else {
            return redirect()->back()->withErrors('No certificate found');
        }
    }

    public function unpublish_exam_result()
    {

        $session_year = getSettings('session_year');
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            $sort = $_GET['sort'];
        if (isset($_GET['order']))
            $order = $_GET['order'];

        // $sql = Exam::whereHas('timetable', function ($q) {
        //     $q->where('date', '<=', Carbon::now());
        // })->with(['exam_class_section' => function ($q) {
        //     $q->where('publish', 0);
        // }])->where('session_year_id', $session_year['session_year'])
        //     ->Owner();

        $sql = Exam::whereHas('timetable', function ($q) {
            $q->where('date', '<=', Carbon::now());
        })->whereHas('exam_class_section', function ($q) {
            $q->where('publish', 0);
        })->where('session_year_id', $session_year['session_year'])
        ->whereHas('class_section.class',function($q){
            $q->where('medium_id',getCurrentMedium()->id);
        })->Owner();


        $total = $sql->count();

        $sql = $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get()->append('class_name');

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        $date = getSettings('date_formate');
        $total_row = 0;
        foreach ($res as $row) {
            if (count($row->exam_class_section)) {
                $tempRow['id'] = $row->id;
                $tempRow['no'] = $no++;
                $tempRow['name'] = $row->name;
                $tempRow['class'] = $row->class_section->first()->full_name;
                $tempRow['start_date'] = $row->timetable->min('date') != '0000-00-00' ? date($date['date_formate'], strtotime($row->timetable->min('date'))) : null;
                $tempRow['end_date'] = $row->timetable->max('date') != '0000-00-00' ? date($date['date_formate'], strtotime($row->timetable->max('date'))) : null;
                $total_row++;
                $rows[] = $tempRow;
            }
        }

        if(request()->get('print')){
            $pdf = DashboardPrints::getInstance(get_center_id(), 'P');

            $pdf->printUnpublishedExamResult($rows);

            return response(
                $pdf->Output('', 'UNPUBLISHED EXAM RESULTS.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function pendding_exam_result()
    {
        $session_year = getSettings('session_year');
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';
        $class_section_id = request()->get('class_id') ?? '';
        $sequence_id = request()->get('sequence_id') ?? '';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            $sort = $_GET['sort'];
        if (isset($_GET['order']))
            $order = $_GET['order'];

        $sql = Exam::where('session_year_id', $session_year['session_year'])->with('class_section')
        ->whereHas('timetable', function ($q) use($class_section_id){
            $q->where('date', '<=', Carbon::now())
                ->whereIn('marks_upload_status', [0, 2])
                ->groupBy('class_section_id');
            if($class_section_id!=''){
                $q->where('class_section_id', $class_section_id);
            }
        })->whereHas('class_section.class',function($q){
            $q->where('medium_id',getCurrentMedium()->id);
        });

        if($sequence_id != ''){
            $sql->where('exam_sequence_id', $sequence_id);
        }

        $total = $sql->count();

        // $sql->orderBy('id', 'asc')->skip($offset)->take($limit);
        $res = $sql->get()->append('date_between');

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        $date = getSettings('date_formate');

        foreach ($res as $row) {
            if (count($row->timetable)) {
                foreach ($row->timetable as $key => $timetable) {
                    $tempRow['id'] = $row->id;
                    $tempRow['no'] = $no++;
                    $tempRow['name'] = $row->name;
                    $class = $row->class_section->first();
                    $class->class->stream;
                    $tempRow['class'] = $class->full_name;
                    
                    $subject = $timetable->subject;
                    $teacher = $subject->teacher->first();
                    $tempRow['sequence'] = $row->sequence->name;
                    $tempRow['teacher'] = $teacher!=null ? $teacher->user->first_name : "";
                    
                    $tempRow['subject'] = $timetable->pendding_subject_marks;

                    $tempRow['start_date'] = $row->date_between['min_date'] != '0000-00-00' ? date($date['date_formate'], strtotime($row->date_between['min_date'])) : null;
                    $tempRow['end_date'] = $row->date_between['max_date'] != '0000-00-00' ? date($date['date_formate'], strtotime($row->date_between['max_date'])) : null;
                    $rows[] = $tempRow;
                }
            }
        }

        usort($rows, function($a, $b) use($sort, $order){
            if($order == 'asc'){
                return strcmp($a[$sort], $b[$sort]);
            }else{
                return -1 * strcmp($a[$sort], $b[$sort]);
            }
        });

        if(request()->get('print')){
            $pdf = DashboardPrints::getInstance(get_center_id(), 'P');

            $pdf->printPendingExamMarks($rows);

            return response(
                $pdf->Output('', 'PENDING EXAM MARKS.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function upcoming_exam()
    {
        $session_year = getSettings('session_year');

        $offset = $_GET['offset'] ?? 0;
        $limit = $_GET['limit'] ?? 10;
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'DESC';

        $sql = Exam::query()->whereHas('timetable', function ($q) use ($session_year) {
            $q->where('session_year_id', $session_year['session_year'])
                ->where('date', '>=', Carbon::now());
        })->where('session_year_id', $session_year['session_year'])->Owner();

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get()->append('class_name');

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        $date = getSettings('date_formate');

        foreach ($res as $row) {
            if (count($row->timetable)) {
                foreach ($row->timetable as $key => $timetable) {
                    $tempRow['id'] = $row->id;
                    $tempRow['no'] = $no++;
                    $tempRow['name'] = $row->name;
                    $tempRow['class'] = $timetable->class_section->full_name;
                    $tempRow['subject'] = $timetable->pendding_subject_marks;

                    $tempRow['start_date'] = $row->date_between['min_date'] != '0000-00-00' ? date($date['date_formate'], strtotime($row->date_between['min_date'])) : null;
                    $tempRow['end_date'] = $row->date_between['max_date'] != '0000-00-00' ? date($date['date_formate'], strtotime($row->date_between['max_date'])) : null;
                    $rows[] = $tempRow;
                }
            }
        }

        if(request()->get('print')){
            $pdf = DashboardPrints::getInstance(get_center_id(), 'P');

            $pdf->printUpcomingExams($rows);

            return response(
                $pdf->Output('', 'UPCOMING EXAMS.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function top_student_list(Request $request)
    {
        $session_year = getSettings('session_year');

        $offset = $_GET['offset'] ?? 0;
        $limit = $_GET['limit'] ?? 10;
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'DESC';

        $validatedData = Validator::make($request->all(), [
            'class_id' => 'required',
            'subject_id' => 'required',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'message' => "Not everything was sent",
            ]);
        }

        $classId = $request->input('class_id');
        $sequenceId = $request->input('sequence_id');
        $gender = $request->input('gender');
        $subjectId = $request->input('subject_id');

        $sessionYearId = getSettings('session_year')['session_year'];

        $examsQuery = Exam::query()->where('session_year_id', $sessionYearId)->whereHas('timetable', function ($q) use ($sequenceId, $subjectId, $classId) {
            $q->where('date', '<=', Carbon::now());

            if ($classId) {
                $q->where('class_section_id', $classId);
            }

            if ($subjectId) {
                $q->where('subject_id', $subjectId);
            }
        })->Owner()->where('type', 1);

        if ($sequenceId) {
            $examsQuery->whereHas('sequence', function ($query) use ($sequenceId) {
                $query->where('id', $sequenceId);
            });
        }

        $examIds = $examsQuery->get()->pluck('id');

        $timetable_ids = array();

        $timetable_ids = ExamTimetable::query()->where('session_year_id', $sessionYearId)->groupBy('class_section_id');

        $timetable_ids->whereIn('exam_id', $examIds);

        $timetable_ids = $timetable_ids->get()->pluck('id');

        $fail_student_ids = ExamMarks::where('session_year_id', $sessionYearId)->where('passing_status', 0)->groupBy('student_id')->whereIn('exam_timetable_id', $timetable_ids)->get()->pluck('student_id');

        $highest_marks = array();

        $classes = ExamClassSection::whereIn('exam_id', $examIds)->get()->pluck('class_section.class_id')->toArray();

        $classes = array_unique($classes);

        $class_school = ClassSchool::with([
            'class_section' => function ($q) use ($sessionYearId, $examIds, $request, $fail_student_ids) {
                $q->with([
                    'exam_result' => function ($q) use ($sessionYearId, $examIds, $request, $fail_student_ids) {
                        $q->whereIn('exam_id', $examIds)
                            ->whereNotIn('student_id', $fail_student_ids)
                            ->where('session_year_id', $sessionYearId)
                            ->orderBy('obtained_marks', "DESC")
                            ->limit(10);
                    }
                ], 'obtained_marks');
            }
        ])->whereIn('id', $classes)->get();

        $class_section_ids = array();

        foreach ($class_school as $key => $class) {
            foreach ($class->class_section as $class_section) {

                foreach($class_section->exam_result as $result) {
                    $highest_marks[] = $result->obtained_marks;
                }

                $class_section_ids[] = $class_section->id;
            }
        }

        $data = array();
        $bulkData = array();
        $bulkData['total'] = count($class_section_ids);
        $rows = array();
        $tempRow = array();
        $no = 1;
        $date = getSettings('date_formate');

        $highest_marks = array_unique($highest_marks);

        foreach ($class_section_ids as $key => $class_section) {

            $student_name = array();

            foreach ($highest_marks as $key => $mark) {
                $student_name = [];

                $exam_results_query = ExamResult::select('class_section_id', 'student_id', 'obtained_marks', 'percentage', 'exam_id')
                    ->where('class_section_id', $class_section)
                    ->where('obtained_marks', $mark)
                    ->where('session_year_id', $sessionYearId)
                    ->whereIn('exam_id', $examIds);

                if ($gender) {
                    $exam_results_query->whereHas('student.user', function ($query) use ($gender) {
                        $query->where('gender', $gender);
                    });
                }

                $exam_results = $exam_results_query->get();

                if (count($exam_results) > 0) {
                    foreach ($exam_results as $key => $exam_result) {
                        $student_name[] = $exam_result->student->full_name;
                    }

                    $first = $exam_results[0];

                    $tempRow['No'] = $no++;
                    $tempRow['name'] = str_replace(',', ', ', implode(',', $student_name));
                    $tempRow['class'] = $first->class_section->class->name;
                    $tempRow['marks'] = $first->obtained_marks;
                    $tempRow['precentage'] = $first->obtained_marks;
                    if (count($exam_results) > 1) {
                        $tempRow['gender'] = 'Varies';
                    } else {
                        $tempRow['gender'] = $first->student->user->gender;
                    }

                    $rows[] = $tempRow;
                }
            }

        }


        $bulkData['rows'] = $rows;
        if ($request->get('print')) {
            $pdf = DashboardPrints::getInstance(get_center_id(), 'P');

            $pdf->printStartStudents($rows);

            return response(
                $pdf->Output('', 'STAR STUDENTS.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }
        return response()->json($bulkData);

    }

    public function student_honor_roll_certificate(Request $request)
    {

        $request->validate([
            'exam_report_id' => 'required'
        ],[
            'exam_report_id.required' => trans('please_select_records')
        ]);

        $school_logo = Settings::where('type', 'report_header_logo')->where('center_id', get_center_id())->currentMedium()->first();
        $school_logo = $school_logo->getRawOriginal('message');
        

        $school_name = getSettings('school_name');
        $school_name = $school_name['school_name'];

        $student_honor_roll_text = getSettings('student_honor_roll_text');


        // return $request->all();
        $exam_report_id = explode(",", $request->exam_report_id);

        $exam_report_detail = ExamReportClassDetails::whereIn('id',$exam_report_id)->get();

        $pdf = PDF::loadView('students.honor_roll_certificate', compact('school_logo', 'school_name','exam_report_detail','student_honor_roll_text'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream();
    }

    public function sequentialShow(Request $request): \Illuminate\Http\Response|JsonResponse|Redirector|RedirectResponse|Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        if (!Auth::user()->can('list-sequential-exam')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = 'exams.id';
        $order = $request->order ?? 'DESC';
        $session_year = getSettings('session_year');
        $sql = ExamClassSection::owner()->with([
            'class_section.class.stream',
            'class_section.section',
            'exam.session_year',
            'exam.term',
            'exam.sequence',
            'class_timetable.subject'
        ]);


        if (!empty($request->class_section_id)) {
            $sql = $sql->where('class_section_id', $request->class_section_id);
        }

        if (!empty($request->sequence_id)) {
            $sql = $sql->whereHas('exam', function ($q) use ($request) {
                $q->where('exam_sequence_id', $request->sequence_id);
            });
        }

        $sql = $sql->whereHas('class_section.class', function ($q) {
            $q->activeMediumOnly();
        })->whereHas('exam', function ($q) use($session_year){
            $q->where('type', 1)->where('session_year_id',$session_year['session_year']);
        })->whereHas('class_section.class',function($q){
            $q->where('center_id',get_center_id());
        });

        if (!empty($_GET['search'])) {
            $search = $_GET['search'];

            $sql = $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhereHas('exam', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")->orwhere('description', 'LIKE', "%$search%");
                    })->orWhereHas('exam.term', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->orWhereHas('exam.sequence', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->orWhereHas('exam.session_year', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->orWhereHas('class_section.class', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->orWhereHas('class_section.section', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    });
            });
        }


        $sql = $sql->join('exams', 'exam_class_sections.exam_id', '=', 'exams.id')->groupBy('class_section_id', 'exams.exam_sequence_id');
        $sql = $sql->orderBy($sort, $order);

        $total = count($sql->get());
        $sql->skip($offset)->take($limit);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = '<div class="actions">';

            $operate .= '<a title="Active All Exams" href="' . route('exams.update', $row->exam->id) . '" class="btn btn-sm btn-primary-light btn-rounded btn-icon edit-data set-form-url activate-all-exams"><i class="feather-check-circle"></i></a>';
            $operate .= '<a title="Deactivate All Exams" href="' . route('exams.update', $row->exam->id) . '" class="btn btn-sm btn-primary-light btn-rounded btn-icon edit-data set-form-url deactivate-all-exams"><i class="feather-x-circle"></i></a>';


            $operate .= "</div>";
            $tempRow['id'] = $row->class_section_id;
            $tempRow['exam_sequence_id'] = $row->exam->exam_sequence_id;
            $tempRow['no'] = $no++;
            $tempRow['class_name'] = $row->class_section->full_name;
            $tempRow['session_year_id'] = $row->session_year_id;
            $tempRow['exam_term_id'] = $row->exam->exam_term_id;
            $tempRow['term_name'] = $row->exam->term->name ?? '';
            $tempRow['sequence_name'] = $row->exam->sequence->name ?? '';
            $tempRow['sequence_start_date'] = $row->exam->sequence->start_date ?? '';
            $tempRow['sequence_end_date'] = $row->exam->sequence->end_date ?? '';
            $tempRow['teacher_status'] = $row->exam->teacher_status ?? 0;
            $tempRow['student_status'] = $row->exam->student_status ?? 0;
            $tempRow['exams'] = Exam::owner()->with(['timetable','timetable.subject'])->where(['exam_sequence_id'=>$row->exam->exam_sequence_id])->whereHas('exam_class_section',function ($q) use($row){
                $q->where(['class_section_id'=>$row->class_section_id,'publish'=>0]);
            })->get();
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        if(request()->get('print')){
            $classSection = null;
            if (!empty($request->class_section_id)) {
                $classSection = ClassSection::find($request->class_section_id);
            }

            $pdf = ExamPrints::getInstance(get_center_id(), 'L');

            $pdf->printSequentialExamList($rows, $classSection);

            return response(
                $pdf->Output('', 'SPECIFIC EXAM LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }
    public function sequentialUpdate(Request $request)
    {
        //        if (!Auth::user()->can('exam-create')) {
        //            $response = array(
        //                'message' => trans('no_permission_message')
        //            );
        //            return redirect(route('home'))->withErrors($response);
        //        }
        $validator = Validator::make(
            $request->all(),
            [
                'class_section_id'=> 'required|numeric',
                'exam_sequence_id'    => 'required|numeric',
                'teacher_status' => 'required|numeric|in:0,1',
                'student_status' => 'required|numeric|in:0,1',
            ]
        );
        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        try {
            DB::beginTransaction();
            $exam = Exam::where(['exam_sequence_id'=>$request->exam_sequence_id])->whereHas('exam_class_section',function($q) use($request){
                $q->where(['class_section_id'=>$request->class_section_id,'publish'=>0]);
            })->update([
                'teacher_status'=>$request->teacher_status,
                'student_status'=>$request->student_status,
            ]);
            DB::commit();
            $response = array(
                'error'   => false,
                'message' => trans('data_update_successfully'),
            );
        } catch (Throwable $e) {
            DB::rollBack();
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e->getMessage()
            );
        }
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getForShow(Request $request): array
    {
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'DESC';

        $sql = ExamClassSection::owner()->with([
            'class_section.class',
            'class_section.section',
            'exam.session_year',
            'exam.term',
            'exam.sequence',
            'class_timetable.subject'
        ])->whereHas('class_section.class', function ($q) {
            $q->activeMediumOnly();
        })->whereHas('exam', function ($q) {
            $q->where('type', 2);
        });

        if (!empty($_GET['search'])) {
            $search = $_GET['search'];

            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhereHas('exam', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")->orwhere('description', 'LIKE', "%$search%");
                    })->orWhereHas('exam.term', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->orWhereHas('exam.sequence', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->orWhereHas('exam.session_year', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->orWhereHas('class_section.class', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })->orWhereHas('class_section.section', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    });
            });
        }

        if (!empty($request->class_section_id)) {
            $sql->where('class_section_id', $request->class_section_id);
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();
        return [$total, $res];
    }

}
