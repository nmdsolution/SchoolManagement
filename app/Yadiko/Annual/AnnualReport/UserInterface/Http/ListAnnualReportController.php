<?php


namespace App\Yadiko\Annual\AnnualReport\UserInterface\Http;

use App\Http\Controllers\Controller;
use App\Models\AnnualReport;
use App\Models\ClassSection;
use App\Printing\ExamPrints;
use Illuminate\Http\Request;
use App\Models\AnnualClassDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ListAnnualReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('exam-report')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class_sections = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();
        return view('exams.annual-report-index', compact('class_sections'));
    }
    public function show($id, Request $request)
    {
        if (!Auth::user()->can('exam-report')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()
            );
            return response()->json($response);
        }
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'rank';
        $order = $request->order ?? 'ASC';

        $annualReport = AnnualReport::where([
            'class_section_id' => $request->class_section_id,
        ])->first();
        if (!$annualReport) {
            $response = array(
                'error'   => true,
                'message' => "Exam Report Doesn't Exists.Please Generate First.",
                'rows'    => [],
                'total'   => 0
            );
            return response()->json($response);
        }

        $sql = AnnualClassDetails::with('student')
            ->join('students', 'annual_class_details.student_id', '=', 'students.id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->where(['annual_report_id' => $annualReport->id]);

        $total = $sql->count();
        if($sort=="student_name"){
            $sort = "users.first_name";
        }
        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($res as $row) {
            $operate = '<a href="' . route('annual-report-download', [
                    $row->annual_report_id,
                    $row->student_id
                ]) . '" target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Download" download><i class="feather-file"></i></a>&nbsp;&nbsp;';
            $operate .= '<a href="' . route('annual-report-download', [
                    $row->annual_report_id,
                    $row->student_id
                ]) . '" target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="View" ><i class="feather-eye"></i></a>&nbsp;&nbsp;';
            studentResultRows($tempRow, $row, $operate, $rows);
        }

        if(request()->get('print')){
            $classSection = null;
            if (!empty($request->class_section_id)) {
                $classSection = ClassSection::find($request->class_section_id);
            }

            $pdf = ExamPrints::getInstance(get_center_id(), 'P');

            $pdf->printExamResultList($rows, $classSection);

            return response(
                $pdf->Output('', 'ANNUAL RESULT LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }
}