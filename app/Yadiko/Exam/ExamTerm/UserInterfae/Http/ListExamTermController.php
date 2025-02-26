<?php


namespace App\Yadiko\Exam\ExamTerm\UserInterfae\Http;


use App\Http\Controllers\Controller;
use App\Http\Requests\ExamTermRequest;
use App\Models\ExamTerm;
use App\Services\ExamService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class ListExamTermController extends Controller
{
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

// Retrieve exam terms
$examTerms = ExamTerm::owner()->with('examSequences')->get();

return view('exams.term', compact('examTerms'));
}

/**
 * Store a newly created resource in storage.
 *
 * @param ExamTermRequest $request
 * @return JsonResponse
 */
public function store(ExamTermRequest $request): JsonResponse {
    try {
        DB::beginTransaction();

        $session_year = getSettings('session_year');

        $term = ExamTerm::query()->create([
            'name'            => $request->name,
            'start_date'            => $request->start_date,
            'end_date'            => $request->end_date,
            'session_year_id' => $session_year['session_year'],
            'center_id'       => Auth::user()->center->id,
            'medium_id'       => getCurrentMedium()->id
        ]);

        if (!empty($request->sequence_name)) {
            ExamService::createDummyExamSequence($term->id, $request->sequence_name);
        }

        DB::commit();

        $response = [
            'error'   => false,
            'message' => trans('data_store_successfully')
        ];
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
 * Display the specified resource.
 *
 * @param ExamTermRequest $request
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
        ->with('examSequences')  // Add eager loading for sequences
        ->where('session_year_id', getSettings('session_year')['session_year'])
        ->currentMedium();

    if (!empty($request->search)) {
        $search = $request->search;
        $search_columns = array('id', 'name');
        $sql->where(function ($query) use ($search_columns, $search) {
            foreach ($search_columns as $column) {
                $query->orWhere($column, 'LIKE', "%$search%");
            }
        });
    }

    $total = $sql->count();

    $sql->orderBy($sort, $order)->skip($offset)->take($limit);
    $res = $sql->get();

    $bulkData = array();
    $bulkData['total'] = $total;
    $rows = array();
    $no = 1;

    foreach ($res as $row) {
        $sequences = $row->examSequences->pluck('name')->toArray();

        $tempRow = [
            'id' => $row->id,
            'no' => $no++,
            'name' => $row->name,
            'start_date' => $row->start_date,
            'end_date' => $row->end_date,
            'sequences' => !empty($sequences) ? implode(', ', $sequences) : '',
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at
        ];

        $rows[] = $tempRow;
    }

    $bulkData['rows'] = $rows;
    return response()->json($bulkData);
}
public function addSequence(Request $request, $id): JsonResponse {
    $request->validate([
        'sequence_name' => 'required|string|max:255',
    ]);

    try {
        $term = ExamTerm::findOrFail($id);
        $existingSequences = $term->examSequences()->pluck('name')->toArray();

        if (in_array($request->sequence_name, $existingSequences)) {
            return response()->json(['error' => true, 'message' => 'Sequence already exists.']);
        }

        // Create the new sequence with center_id
        $term->examSequences()->create([
            'name' => $request->sequence_name,
            'center_id' => Auth::user()->center->id // Assuming the user has a center relationship
        ]);

        return response()->json(['error' => false, 'message' => 'Sequence added successfully.']);
    } catch (Throwable $e) {
        \Log::error('Error adding sequence: ' . $e->getMessage());
        return response()->json(['error' => true, 'message' => 'An error occurred while adding the sequence.', 'details' => $e->getMessage()]);
    }
}
}