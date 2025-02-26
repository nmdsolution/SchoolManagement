<?php

namespace App\Http\Controllers;

use App\Domain\Exam\Services\ExamSequenceService;
use App\Domain\Exam\Services\StudentService;
use App\Http\Requests\Exam\ExamSequenceRequest;
use App\Http\Requests\Exam\SequenceWiseMarksListRequest;
use App\Http\Requests\Exam\StoreExamSequenceRequest;
use App\Models\AutoSequenceExam;
use App\Models\ClassSection;
use App\Models\Exam;
use App\Models\ExamMarks;
use App\Models\ExamSequence;
use App\Models\ExamTerm;
use App\Printing\ExamPrints;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class ExamSequenceController extends Controller {
    protected ExamSequenceService $examSequenceService;
    protected StudentService $studentService;

    public function __construct(ExamSequenceService $examSequenceService, StudentService $studentService)
    {
        $this->examSequenceService = $examSequenceService;
        $this->studentService = $studentService;
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(): Factory|View|Redirector|Application|RedirectResponse {
        if (!Auth::user()->can('exam-sequence-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $exam_term = ExamTerm::owner()->currentMedium()->get();
        $class_sections = ClassSection::owner()->whereHas('class',function($q){
            $q->where('center_id',get_center_id());
            $q->activeMediumOnly();
        })->with('class.stream', 'section')->get();

        return view('exams.sequence', compact('exam_term', 'class_sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreExamSequenceRequest $request
     * @return JsonResponse
     */
    public function store(StoreExamSequenceRequest $request): JsonResponse
    {
        try {
            $this->examSequenceService->create($request->validated());
            $response = [
                'error'   => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (Throwable $e) {
            $response = [
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
            ];
        }
        return response()->json($response);

    }

    /**
     * Display the specified resource.
     *
     * @param ExamSequenceRequest $request
     * @return JsonResponse
     */
    public function show(ExamSequenceRequest $request): JsonResponse {
        if (!Auth::user()->can('exam-sequence-list')) {
            $response = [
                'message' => trans('no_permission_message')
            ];
            return response()->json($response);
        }

        $examTermIds = ExamTerm::owner()->currentMedium()->currentSessionYear()->get()->pluck('id')->toArray();
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'DESC';
        $search = $request->search ?? null;

        $data = $this->examSequenceService->getSequences($examTermIds, $offset, $limit, $sort, $order, $search);

        return response()->json($data);
    }


    public function update(ExamSequenceRequest $request): JsonResponse
    {
        $id = $request->id;
        $data = $request->all();
        $response = $this->examSequenceService->update($id, $data);
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse {
        try {
            $sequence = ExamSequence::owner()->withCount('exam')->findOrFail($id);
            if ($sequence->exam_count > 0) {
                $response = [
                    'error'   => true,
                    'message' => "Please Delete all the related exams first."
                ];
            } else {
                $sequence->delete();
                $response = [
                    'error'   => false,
                    'message' => trans('data_delete_successfully')
                ];
            }
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
            );
        }
        return response()->json($response);
    }

    public function sequenceWiseMarksIndex() {
        $exam_terms = ExamTerm::owner()->currentMedium()->get()->pluck('id');
        $sequences = ExamSequence::Owner()->whereIn('exam_term_id', $exam_terms)->get()->pluck('name', 'id');
        $classSections = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();
        return view('exams.sequence_marks.index', compact('sequences', 'classSections'));
    }

    public function sequenceWiseMarksList(SequenceWiseMarksListRequest $request): JsonResponse
    {
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'erss.avg';
        $order = $request->order ?? 'DESC';
        $classSectionId = $request->class_section_id ?? 0;
        $sequenceId = $request->sequence_id;
        $search = $request->search ?? null;

        $sessionYear = getSettings('session_year')['session_year'];
        $data = $this->studentService->getStudentsWithMarks($classSectionId, $sequenceId, $sessionYear, $offset, $limit, $sort, $order, $search);

        if ($request->get('print')) {
            $classSection = ClassSection::find($classSectionId);
            $examSequence = ExamSequence::find($sequenceId);

            $pdf = ExamPrints::getInstance(get_center_id(), 'P');
            $pdf->printExamSequenceMarks($data['rows'], $classSection, $examSequence);

            return response(
                $pdf->Output('', 'SEQUENTIAL EXAM MARKS LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        return response()->json($data);
    }

    public function sequenceWiseMarksUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'exam_marks'                  => 'required|array',
            'exam_marks.*.id'             => 'required|integer',
            'exam_marks.*.obtained_marks' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            ExamMarks::owner()->upsert($request->exam_marks, ['id'], ['obtained_marks']);

            $response = [
                'error'   => false,
                'message' => trans('data_update_successfully')
            ];
        } catch (\Exception $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
            );
        }
        return response()->json($response);
    }
/**
 * Get classes assigned to a specific exam sequence
 *
 * @param int $id Sequence ID
 * @return JsonResponse
 */
public function getSequenceClasses($id): JsonResponse
{
    try {
        // Verify sequence exists and belongs to current
        // user/center
        $sequence = ExamSequence::owner()->findOrFail($id);

        // Get class sections from the auto_sequence_exam table
        $classSectionIds = AutoSequenceExam::where('exam_sequence_id', $id)
            ->pluck('class_section_id')
            ->unique()
            ->values()
            ->toArray();

        return response()->json([
            'error' => false,
            'class_sections' => $classSectionIds
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => trans('error_occurred'),
            'data' => $e->getMessage()
        ], 500);
    }
}
public function updateStatus(Request $request, $id): JsonResponse
{
    // Validate the incoming request
    $request->validate([
        'status' => 'required|boolean',
    ]);

    try {
        // Find the sequence by ID
        $sequence = ExamSequence::owner()->findOrFail($id);

        // Update the status
        $sequence->status = $request->status;
        $sequence->save();

        return response()->json(['message' => 'Status updated successfully']);
    } catch (Throwable $e) {
        return response()->json([
            'error' => true,
            'message' => trans('error_occurred'),
            'data' => $e->getMessage()
        ], 500);
    }
}
}
