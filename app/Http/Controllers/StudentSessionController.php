<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\SessionYear;
use App\Models\Students;
use App\Models\StudentSessions;
use App\Printing\StudentPrints;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentSessionController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('promote-student-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class_sections = ClassSection::with('class.stream', 'section')
            ->whereHas('class', function ($q) {
                $q->where('center_id', Auth::user()->center->id)->activeMediumOnly();
            })->get();
        $session_year = SessionYear::owner()->select('id', 'name')->where('default', 0)->get();
        return view('promote_student.index', compact('class_sections', 'session_year'));
    }

    private function handlePromotion($promote_student_session, $associated_student, $request, $status, $result, $new_class_section_id)
    {
        $promote_student_session->result = $request->$result;
        $promote_student_session->status = $request->$status;
        $promote_student_session->promoted = true;

        switch (true) {
            //  pass & continue
            case ($request->$status == 1 && $request->$result == 1):
                $promote_student_session->class_section_id = $new_class_section_id;
                $associated_student->class_section_id = $new_class_section_id;
                $associated_student->save();
                break;

            // fail & continue
            case ($request->$status == 1 && $request->$result == 0):
                $promote_student_session->class_section_id = $associated_student->class_section_id;
                break;

            // pass & dismiss
            case ($request->$status == 0 && $request->$result == 1):
                $promote_student_session->class_section_id = $new_class_section_id;
                $promote_student_session->active = false;

                break;

            // fail & dismiss
            case ($request->$status == 0 && $request->$result == 0):
                $promote_student_session->class_section_id = $associated_student->class_section_id;
                $promote_student_session->active = false;
                break;
        }

        $associated_student->save();
    }


    public function store(Request $request)
    {
        if (!Auth::user()->can('promote-student-create') || !Auth::user()->can('promote-student-edit')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $request->validate([
            'user_id' => 'required',
            'session_year_id' => 'required|exists:session_years,id',
            'new_class_section_id' => 'required|exists:class_sections,id',
            'class_section_id' => 'required',
        ], [
            'user_id.required' => trans('please_select_row'),
            'session_year_id.required' => trans('please_select_session_year'),
            'new_class_section_id.required' => trans('please_select_new_class_section'),
        ]);

        $user_id = $request->user_id;

        $student_ids = array_filter(explode(',', $user_id));

        $students = Students::whereIn('id', $student_ids)->get()->keyBy('id');

        DB::beginTransaction();

        try {
            foreach ($student_ids as $student_id) {
                $status = "status" . $student_id;
                $result = "result" . $student_id;

                $associated_student = $students[$student_id];

                $promote_student_session = StudentSessions::firstOrNew([
                    'student_id' => $student_id,
                    'session_year_id' => $request->session_year_id
                ]);

                $this->handlePromotion($promote_student_session, $associated_student, $request, $status, $result, $request->new_class_section_id);

                $promote_student_session->save();
            }

            DB::commit();

            $response = [
                'error' => false,
                'message' => trans('data_update_successfully')
            ];
        } catch (Exception $e) {
            DB::rollBack();
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }

    public function getPromoteData(Request $request)
    {
        $response = StudentSessions::where(['class_section_id' => $request->class_section_id])->get();
        return response()->json($response);
    }

    public function show(Request $request)
    {
        if (!Auth::user()->can('promote-student-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
//        $offset = request('offset', 0);
//        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'ASC');

        $request->validate([
            'session_year_id' => 'required|exists:session_years,id',
            'class_section_id' => 'required|exists:class_sections,id',
        ]);

        $sessionYearId = getSettings('session_year')['session_year'];

        $promoteSessionYear = $request->session_year_id;

        $class_section_id = $request->class_section_id;

        $sql = Students::owner()->with(['user'])
            ->whereHas('studentSessions', function ($query) use ($sessionYearId, $class_section_id) {
                $query->where('session_year_id', $sessionYearId);
                $query->where('active', true);
                $query->where('class_section_id', $class_section_id);
            })->whereDoesntHave('studentSessions', function ($query) use ($promoteSessionYear) {
                $query->where('session_year_id', $promoteSessionYear);
            })
            ->orderBy($sort, $order);


        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%")->orwhere('mobile', 'LIKE', "%$search%");
        }
        $total = $sql->count();
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        foreach ($res as $row) {
            $result = '<div class="d-flex h-100"><div class="form-check-inline"><label class="form-check-label">
            <input required type="radio" class="result"  name="result' . $row->id . '" value="1" checked>' . trans('Pass') . '
            </label></div>';
            $result .= '<div class="h-100 form-check-inline"><label class="form-check-label">
            <input type="radio" class="result"  name="result' . $row->id . '" value="0">' . trans('Repeat') . '
            </label></div></div>';

            $status = '<div class="d-flex h-100"><div class="form-check-inline"><label class="form-check-label">
            <input required type="radio" class="status"  name="status' . $row->id . '" value="1" checked>' . trans('continue') . '
            </label></div>';
            $status .= '<div class="h-100 form-check-inline"><label class="form-check-label"> <input type="radio" class="status"  name="status' . $row->id . '" value="0" />' . trans('dismiss') . '
            </label></div></div>';


            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['student_id'] = "<input type='text' name='student_id[]' class='form-control d-none' readonly value=" . $row->id . ">";
            $tempRow['admission_no'] = $row->admission_no;
            $tempRow['roll_no'] = $row->student->roll_number ?? null;
            $tempRow['name'] = $row->user->first_name . ' ' . $row->user->last_name;
            $tempRow['result'] = $result;
            $tempRow['status'] = $status;
            $rows[] = $tempRow;
        }

        if ($request->get('print')) {
            $class_section = ClassSection::find($class_section_id);

            $pdf = StudentPrints::getInstance(get_center_id(), 'L');

            $pdf->printPromoteStudentList($rows, $class_section);

            return response(
                $pdf->Output('', 'STUDENT LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function show_list(Request $request)
    {
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

        $class_section_id = $request->class_section_id;
        $promote_session = $request->promote_session_year_id;

        $current_session_year_id = getSettings('session_year');
        $current_session_year_id = $current_session_year_id['session_year'];


        $class_section = ClassSection::with('class', 'section')->find($request->class_section_id);
        $section_id = $class_section->section->id;


        $new_class_section_id = ClassSection::where('section_id', $section_id)->get()->first();

        $student_session = StudentSessions::select('student_id')->where('class_section_id', $new_class_section_id->id)->where('session_year_id', $current_session_year_id)->orWhere('status', 0)->pluck('student_id');

        $sql = Students::with('user');
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%$search%");
                });
        }
        $total = $sql->whereNotIn('id', $student_session)->where('class_section_id', $class_section_id)->count();

        $sql->orderBy($sort, $order);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        $i = 0;
        foreach ($res as $row) {
            $result = '<div class="d-flex"><div class="form-check-inline"><label class="form-check-label">
                <input type="radio" class="result" checked  name="result' . $row->id . '" value="1">Pass
                </label></div>';
            $result .= '<div class="form-check-inline"><label class="form-check-label">
                <input type="radio" class="result"  name="result' . $row->id . '" value="0">Fail
                </label></div></div>';

            $status = '<div class="d-flex"><div class="form-check-inline"><label class="form-check-label">
                    <input type="radio" class="status" checked  name="status' . $row->id . '" value="1">Continue
                    </label></div>';
            $status .= '<div class="form-check-inline"><label class="form-check-label">
                    <input type="radio" class="status"  name="status' . $row->id . '" value="0">Leave
                    </label></div></div>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['student_id'] = "<input type='text' name='student_id[" . $i++ . "]' class='form-control' readonly value=" . $row->id . ">";
            $tempRow['name'] = $row->user->full_name;
            $tempRow['result'] = $result;
            $tempRow['status'] = $status;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function promoted_student()
    {
        $session_years = SessionYear::Owner()->orderBy('id', 'DESC')->pluck('name', 'id');
        $class_sections = ClassSection::Owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        return view('promote_student.promoted_student', compact('session_years', 'class_sections'));
    }

    public function promoted_student_list(Request $request)
    {
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');

        $sql = StudentSessions::with('student')->where('promoted', true);
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")
                ->orWhereHas('student.user', function ($q) use ($search) {
                    $q->where(DB::raw('CONCAT_WS(" ", first_name, last_name)'), 'like', "%$search%");
                });
        }

        if ($request->session_year_id) {
            $sql = $sql->where('session_year_id', $request->session_year_id);
        }
        if ($request->class_section_id) {
            $sql = $sql->where('class_section_id', $request->class_section_id);
        }

        $total = $sql->count();

        $sql->orderBy($sort, $order);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        $i = 0;

        foreach ($res as $row) {
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['student_name'] = $row->student->user->full_name;
            $tempRow['class_section'] = $row->class_section->name;
            $tempRow['result'] = $row->result;
            $tempRow['status'] = $row->status;
            $rows[] = $tempRow;
        }

        if ($request->get('print')) {
            $class_section = ClassSection::find($request->class_section_id);

            $pdf = StudentPrints::getInstance(get_center_id(), 'L');

            $pdf->printPromotedStudentList($rows, $class_section);

            return response(
                $pdf->Output('', 'STUDENT LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function deleteStudentSession(Request $request, $id) {
        DB::beginTransaction();
        try {
            $idList = $request->input('student_session_list', '');

            if ($idList != '') {
                $idList = explode(',', $idList);
            }

            if ($id == "delete" && $idList == "") {
                return response()->json([
                    'error' => true,
                    'message' => trans('please_select_row')
                ]);
            }

            $studentSessions = StudentSessions::query()->where('id', $id)->where('promoted', true);

            if (!empty($idList)) {
                $studentSessions = StudentSessions::query()->whereIn('id', $idList)->where('promoted', true);
            }

            $studentSessions = $studentSessions->get();

            foreach ($studentSessions as $studentSession) {
                $sessionYearId = $studentSession->session_year_id;

                $student = $studentSession->student;

                $previousSession = StudentSessions::query()->where('student_id', $student->id)
                    ->where('session_year_id', '<', $sessionYearId)
                    ->orderBy('session_year_id', 'DESC')->first();

                $class_section_id = $previousSession->class_section_id;
                $student->class_section_id = $class_section_id;
                $student->save();

                $studentSession->delete();
            }

            DB::commit();

            if ($id == 'delete') {
                return response()->json([
                    'error' => false,
                    'message' => trans("promotion_undone")
                ]);
            }

            return response()->json([
                'error' => false,
                'message' => trans("promotion_undone")
            ]);

        } catch (\Throwable $error) {

            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => "Error " . $error->getMessage()
            ]);
        }
    }
}