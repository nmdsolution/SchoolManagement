<?php

namespace App\Http\Controllers;

use App\Domain\Exam\Services\ExamService;
use App\Http\Requests\Exam\ExamTermRequest;
use App\Models\ClassSection;
use App\Models\Exam;
use App\Models\ExamTerm;
use App\Models\Students;
use App\Printing\StudentPrints;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class ExamTermController extends Controller {
    /**
     * Display a listing of the resource.
     *
     */
    public function index(): Factory|View|Redirector|Application|RedirectResponse {
        if (!Auth::user()->can('exam-term-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

// Retrieve exam terms for current medium
$examTerms = ExamTerm::owner()
    ->where('medium_id', getCurrentMedium()->id)
    ->with('examSequences')
    ->get();

return view('exams.term', compact('examTerms'));
}

/**
 * Store a newly created resource in storage.
 *
 * @param \App\Http\Requests\Exam\ExamTermRequest $request
 * @return JsonResponse
 */
public function store(ExamTermRequest $request): JsonResponse {
    try {
        DB::beginTransaction();

        $session_year = getSettings('session_year');

        $term = ExamTerm::query()->create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'session_year_id' => $session_year['session_year'],
            'center_id' => Auth::user()->center->id,
            'medium_id' => getCurrentMedium()->id
        ]);

        if (!empty($request->sequence_name)) {
            ExamService::createDummyExamSequence($term->id, $request->sequence_name);
        }

        DB::commit();

        $response = [
            'error' => false,
            'message' => trans('data_store_successfully')
        ];
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

/**
 * Display the specified resource.
 *
 * @param \App\Http\Requests\Exam\ExamTermRequest $request
 * @return JsonResponse
 */
public function show(ExamTermRequest $request): JsonResponse {
    if (!Auth::user()->can('exam-term-list')) {
        $response = array(
            'message' => trans('no_permission_message')
        );
        return response()->json($response);
    }

    $offset = $request->offset ?? 0;
    $limit = $request->limit ?? 10;
    $sort = $request->sort ?? 'id';
    $order = $request->order ?? 'DESC';

    $sql = ExamTerm::owner()
        ->where('medium_id', getCurrentMedium()->id)
        ->where('session_year_id', getSettings('session_year')['session_year']);

    if (!empty($request->search)) {
        $search = $request->search;
        $sql->where(function ($query) use ($search) {
            $query->where('id', 'LIKE', "%$search%")
                ->orWhere('name', 'LIKE', "%$search%");
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
        $tempRow['id'] = $row->id;
        $tempRow['no'] = $no++;
        $tempRow['name'] = $row->name;
        $tempRow['start_date'] = $row->start_date;
        $tempRow['end_date'] = $row->end_date;
        $tempRow['created_at'] = $row->created_at;
        $tempRow['updated_at'] = $row->updated_at;
        $rows[] = $tempRow;
    }

    $bulkData['rows'] = $rows;
    return response()->json($bulkData);
}


    public function update(ExamTermRequest $request, $id) {
        try {
            $data = ExamTerm::findOrFail($id);
            $data->update([
                'name'            => $request->name,
                'start_date'            => $request->start_date,
                'end_date'            => $request->end_date,
            ]);
            $response = [
                'error'   => false,
                'message' => trans('data_update_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
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
    public function destroy($id): JsonResponse {
        try {
            $term = ExamTerm::withCount('sequence')->findOrFail($id);
            if ($term->sequence_count > 0) {
                $response = array(
                    'error'   => true,
                    'message' => "Please Delete all the rerlated sequeces first."
                );
            } else {
                $term->delete();
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


    public function examTermDocuments(): \Illuminate\Contracts\View\View|Factory|JsonResponse|Application
    {
        if (!Auth::user()->canAny(['class-report', 'exam-term-documents'])) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $session_year = getSettings('session_year');

        $exam_terms = ExamTerm::query()->where('session_year_id', $session_year['session_year'])->Owner()->currentMedium()->get()->pluck('name', 'id');

        return view('documents.exam_term_documents', compact('exam_terms'));
    }

    public function classDocList(Request $request): JsonResponse
    {
        if (!Auth::user()->can('list-sequential-exam')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $exam_ids = Exam::query()
            ->where('exam_term_id', $request->exam_term_id)
            ->where('session_year_id', getSettings('session_year')['session_year'])
            ->get()->pluck('id')->toArray();

        $sql = ClassSection::query()
            ->whereHas('class', function ($query) {
                $query->activeMediumOnly();
            })->owner()
            ->with(['class', 'class.stream', 'section']);

        $total = $sql->count();
        $res = $sql->get();
        $bulkData = $rows =  $tempRow =array();
        $bulkData['total'] = $total;
        $no = 1;

        foreach ($res as $row) {

            $row = (object)$row;

            $params = "exam_term_id=$request->exam_term_id&class_section_id=$row->id";

            $operate = '<a href=' . route('print-attendance-list', [$params]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-sm btn-outline-primary  btn-rounded btn-icon honor-roll default-button edit-data" data-id=' . $row->id . ' title="Attendance List">' . trans('attendance_list') . '</a>&nbsp;&nbsp;';

            $operate .= '<a href=' . route('print-marks-sheet', [$params]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button" data-id=' . $row->id . ' title="Marks List" >' . trans('marks_sheet') .'</a>&nbsp;&nbsp;';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['class_section'] = $row->full_name;
            $tempRow['action'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function marksSheet(Request $request): \Illuminate\Http\Response|Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $exam_term_id = $request->exam_term_id;

        $class_section_id = $request->class_section_id;

        $examTerm = ExamTerm::findOrFail($exam_term_id);

        $classSection = ClassSection::findOrFail($class_section_id);

        $pdf = StudentPrints::getInstance(get_center_id(), 'P');

        $pdf->printClassMarkSheet($this->getClassSectionList($class_section_id), $classSection, $examTerm);

        return response(
            $pdf->Output('', 'MARKS SHEET.pdf'),
            200,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }

    public function attendanceList(Request $request): \Illuminate\Http\Response|Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $exam_term_id = $request->exam_term_id;
            $class_section_id = $request->class_section_id;

            $examTerm = ExamTerm::query()->find($exam_term_id);

            $classSection = ClassSection::query()->findOrFail($class_section_id);
            $classSection->class->stream;
            $classSection->section;

            $pdf = StudentPrints::getInstance(get_center_id(), 'L', 'ATTENDANCE LIST');

            $pdf->printAttendanceList($this->getClassSectionList($class_section_id), $classSection, $examTerm);

            return response(
                $pdf->Output('', 'ATTENDANCE LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        } catch (Throwable $throwable) {
            return response([
                'error' => true,
                'message' => $throwable->getMessage()
            ]);
        }
    }

    private function getClassSectionList($class_section_id): array
    {
        $sessionYearId = getSettings('session_year')['session_year'];

        $sql = Students::owner()->with('user:id,first_name,last_name,gender')
            ->whereHas('studentSessions', function ($query) use ($class_section_id, $sessionYearId) {
                $query->where('session_year_id', $sessionYearId);
                $query->where('active', true);
                $query->where('class_section_id', $class_section_id);
            });

        $res = $sql->orderBy('roll_number', 'asc')->get();

        $rows = array();
        $tempRow = array();
        $no = 1;
        $data = getSettings('date_formate');
        foreach ($res as $row) {
            $assign_student = '<input type="checkbox" class="assign_student"  name="assign_student" value=' . $row->id . '>';
            $tempRow['chk'] = $assign_student;
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['user_id'] = $row->user_id;
            $tempRow['first_name'] = $row->user->first_name;
            $tempRow['last_name'] = $row->user->last_name;
            $tempRow['gender'] = $row->user->gender;
            $tempRow['admission_no'] = $row->admission_no;
            $tempRow['roll_number'] = $row->roll_number;
            $tempRow['admission_date'] = date($data['date_formate'], strtotime($row->admission_date));
            $rows[] = $tempRow;
        }

        return $rows;
    }
}
