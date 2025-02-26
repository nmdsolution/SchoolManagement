<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Group;
use App\Models\ExamTerm;
use App\Models\Students;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use App\Printing\AcademicPrints;
use App\Models\StudentAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('attendance-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class_sections = ClassSection::ClassTeacher()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly()->where('center_id',get_center_id());
        })->get();
        $terms = ExamTerm::owner()->currentSessionYear()->currentMedium()->get();
        return view('attendance.index', compact('class_sections', 'terms'));
    }


    public function view()
    {
        if (!Auth::user()->can('attendance-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class_sections = ClassSection::ClassTeacher()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly()->where('center_id',get_center_id());
        })->get();
        $terms = ExamTerm::owner()->currentSessionYear()->currentMedium()->get();

        return view('attendance.view', compact('class_sections', 'terms'));
    }

    public function getAttendanceData(Request $request)
    {
        $response = Attendance::select('type')->where(['date' => date('Y-m-d', strtotime($request->date)), 'class_section_id' => $request->class_section_id])->pluck('type')->first();
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('attendance-create') || !Auth::user()->can('attendance-edit')) {
            $response = [
                'error'   => true,
                'message' => trans('no_permission_message')
            ];
            return response()->json($response);
        }
    
        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required|numeric',
            'students'         => 'required|array',
            'term_id'          => 'required|numeric'
        ]);
    
        if ($validator->fails()) {
            $response = [
                'error'   => true,
                'message' => $validator->errors()->first()
            ];
            return response()->json($response);
        }
    
        try {
            $data = $validator->validated();
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];
            $class_section_id = $data['class_section_id'];
            $exam_term_id = $data['term_id'];
    
            DB::beginTransaction();
    
            foreach ($data['students'] as $id => $value) {
                $absences = $value['absences'] ?? 0;
                $justified = $value['justified'] ?? 0;
    
                $unjustified = max(0, $absences - $justified);
    
                if ($justified > $absences) {
                    DB::rollBack();
                    $response = [
                        'error'   => true,
                        'message' => trans('justified_less_than_absence')
                    ];
                    return response()->json($response);
                }
    
                $attendance = StudentAttendance::where('student_id', $id)
                    ->where('session_year_id', $session_year_id)
                    ->where('class_section_id', $class_section_id)
                    ->where('exam_term_id', $exam_term_id)
                    ->first();
    
                if ($attendance) {
                    $attendance->update([
                        'total_absences'        => $absences,
                        'justified_absences'    => $justified,
                        'unjustified_absences'  => $unjustified
                    ]);
                } else {
                    StudentAttendance::create([
                        'student_id'            => $id,
                        'session_year_id'       => $session_year_id,
                        'class_section_id'      => $class_section_id,
                        'exam_term_id'          => $exam_term_id,
                        'total_absences'        => $absences,
                        'justified_absences'    => $justified,
                        'unjustified_absences'  => $unjustified
                    ]);
                }
            }
    
            DB::commit();
            $response = [
                'error'   => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $response = [
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e->getMessage()
            ];
            // logger("RESULT: ", $response);
        }
        return response()->json($response);
    }
      
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if (!Auth::user()->can('attendance-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $sort = 'roll_number';
        $order = 'ASC';

        $class_section_id = $request->class_section_id;
        $exam_term_id = $request->term_id;

        if($class_section_id=='' || $exam_term_id == '') return [];
        
        $session_year = getSettings('session_year');
        $session_year_id = $session_year['session_year'];
        
        $chk = StudentAttendance::with('student')->where(['exam_term_id' => $exam_term_id, 'class_section_id' => $class_section_id, 'session_year_id' => $session_year_id])->count();

        if ($chk > 0) {
            //            DB::enableQueryLog();
            $sql2 = StudentAttendance::with('student')->where(['exam_term_id' => $exam_term_id, 'class_section_id' => $class_section_id, 'session_year_id' => $session_year_id]);
            $total = $sql2->count();
            $res = $sql2->get();
            
            $bulkData = array();
            $bulkData['total'] = $total;
            $rows = array();
            $tempRow = array();
            $no = 1;
            foreach ($res as $row) {
                $absences = '<div class="d-flex"><div class="form-inline">
                <input required type="number" min="0" class="form-control"  name="students[' . $row->student_id . '][absences]" value="'.$row->total_absences.'">
                </div></div>';
                $justified = '<div class="d-flex"><div class="form-inline">
                <input required type="number" class="form-control"  name="students[' . $row->student_id . '][justified]" value="'.$row->justified_absences.'">
                </div></div>';
                $unjustified = '<div class="d-flex"><div class="form-inline">
                <input required disabled type="number" class="form-control"  name="students[' . $row->student_id . '][unjustified]" value="'.$row->unjustified_absences.'">
                </div></div>';
                
                $tempRow['id'] = $row->id;
                $tempRow['no'] = $no++;
                $tempRow['student_id'] = $row->student_id;
                $tempRow['admission_no'] = $row->student->admission_no;
                $tempRow['roll_no'] = $row->student->roll_number;
                $tempRow['name'] = $row->student->user->first_name . ' ' . $row->student->user->last_name;
                $tempRow['absences'] = $absences;
                $tempRow['justified'] = $justified;
                $tempRow['unjustified'] = $unjustified;
                $rows[] = $tempRow;
            }
        } else {
            $sql = Students::where('class_section_id', $class_section_id)->with('user');
            if (!empty($_GET['search'])) {
                $search = $_GET['search'];
                $sql->where('id', 'LIKE', "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->whereRaw("concat(first_name,' ',last_name) LIKE '%" . $search . "%'")->orwhere('first_name', 'LIKE', "%$search%")->orwhere('last_name', 'LIKE', "%$search%");
                    });
            }
            $total = $sql->count();
            $sql->orderBy($sort, $order);
            $res = $sql->get();
            $bulkData = array();
            $bulkData['total'] = $total;
            $rows = array();
            $tempRow = array();
            $no = 1;
            foreach ($res as $row) {
                $absences = '<div class="d-flex"><div class="form-inline">
                <input required type="number" min="0" class="form-control"  name="students[' . $row->id . '][absences]" value="0">
                </div></div>';
                $justified = '<div class="d-flex"><div class="form-inline">
                <input required type="number" class="form-control"  name="students[' . $row->id . '][justified]" value="0">
                </div></div>';
                $unjustified = '<div class="d-flex"><div class="form-inline">
                <input required type="number" class="form-control"  name="students[' . $row->student_id . '][unjustified]" value="0">
                </div></div>';

                $tempRow['id'] = $row->id;
                $tempRow['no'] = $no++;
                $tempRow['student_id'] =  $row->id;
                $tempRow['admission_no'] = $row->admission_no;
                $tempRow['roll_no'] = $row->roll_number;
                $tempRow['name'] = $row->user->first_name . ' ' . $row->user->last_name;
                $tempRow['absences'] = $absences;
                $tempRow['justified'] = $justified;
                $tempRow['unjustified'] = $unjustified;
                $rows[] = $tempRow;
            }
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function attendance_show(Request $request)
    {
        if (!Auth::user()->can('attendance-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $sort = 'roll_number';
        $order = 'ASC';

        $class_section_id = $request->class_section_id;
        $exam_term_id = $request->term_id;

        if($class_section_id=='' || $exam_term_id == '') return [];
        
        $session_year = getSettings('session_year');
        $session_year_id = $session_year['session_year'];
        
        $sql2 = StudentAttendance::with('student')->where(['exam_term_id' => $exam_term_id, 'class_section_id' => $class_section_id, 'session_year_id' => $session_year_id]);
        $total = $sql2->count();
        $res = $sql2->get();
        
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        foreach ($res as $row) {
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['student_id'] = $row->student_id;
            $tempRow['admission_no'] = $row->student->admission_no;
            $tempRow['roll_no'] = $row->student->roll_number;
            $tempRow['name'] = $row->student->user->first_name . ' ' . $row->student->user->last_name;
            $tempRow['absences'] = $row->total_absences;
            $tempRow['justified'] = $row->justified_absences;
            $tempRow['unjustified'] = $row->unjustified_absences;
            $rows[] = $tempRow;
        }

        if(request()->get('print')){
            $class = ClassSection::find($class_section_id);
            $pdf = AcademicPrints::getInstance(get_center_id(), 'L');

            $pdf->printClassAttendanceList($rows, $class);

            return response(
                $pdf->Output('', 'ATTENDANCE REPORT.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function report($class_section_id, $student_id = null)
    {

        $session_year = getSettings('session_year');
        $total_present = Attendance::where('session_year_id', $session_year['session_year'])->where('class_section_id', $class_section_id)->where('type', 1);

        $total_absent = Attendance::where('session_year_id', $session_year['session_year'])->where('class_section_id', $class_section_id)->where('type', 0);
        $student = '';
        if ($student_id) {
            $student = Students::select('id', 'user_id')->with('user:id,first_name,last_name,image')->find($student_id)->makeHidden(['class_name', 'session_year']);
            $total_present = $total_present->where('student_id', $student_id);
            $total_absent = $total_absent->where('student_id', $student_id);
        }
        $total_present = $total_present->count();
        $total_absent = $total_absent->count();

        $response = [
            'error'         => false,
            'message'       => 'Data fetched successfully',
            'total_present' => $total_present,
            'total_absent'  => $total_absent,
            'student'       => $student
        ];

        return response()->json($response);
    }

    public function attendance_report(Request $request)
    {
        if (!Auth::user()->can('attendance-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $offset = 0;
        $limit = 10;
        $sort = 'roll_number';
        $order = 'ASC';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            $sort = $_GET['sort'];
        if (isset($_GET['order']))
            $order = $_GET['order'];

        $class_section_id = $request->class_section_id;

        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        $sql = Students::select('id', 'user_id', 'class_section_id', 'roll_number')->withCount(['attendance_present' => function ($q) use ($class_section_id) {
            $q->where('class_section_id', $class_section_id)->where('type', 1);
        }])
            ->withCount(['attendance_absent' => function ($q) use ($class_section_id) {
                $q->where('class_section_id', $class_section_id)->where('type', 0);
            }])
            ->where('class_section_id', $class_section_id)->orderBy('roll_number', 'asc');

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where(DB::raw('CONCAT_WS(" ", first_name, last_name)'), 'like', "%$search%");
                });
        }

        $total = $sql->count();
        // $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $sql->orderBy($sort, $order);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        $present_per = 0;
        $absent_per = 0;
        foreach ($res as $row) {

            $total_days = $row->attendance_present_count + $row->attendance_absent_count;
            // if ($total_days == '' || $total_days == 0) {
            //     $total_days = 1;
            // }

            if ($total_days) {
                $present_per = ($row->attendance_present_count * 100) / $total_days;
                $absent_per = ($row->attendance_absent_count * 100) / $total_days;
            }

            if ($request->greater_than) {
                if ($present_per >= $request->greater_than && !$request->less_than) {
                    $tempRow['id'] = $row->id;
                    $tempRow['no'] = $no++;
                    $tempRow['roll_no'] = $row->roll_number;
                    $tempRow['name'] = $row->full_name;
                    $tempRow['total_days'] = $total_days;
                    $tempRow['present'] = $row->attendance_present_count;
                    $tempRow['absent'] = $row->attendance_absent_count;
                    $tempRow['present_per'] = number_format($present_per, 2) . ' %';
                    $tempRow['absent_per'] = number_format($absent_per, 2) . ' %';
                    $rows[] = $tempRow;
                }
            }
            if ($request->less_than) {
                if ($present_per <= $request->less_than && !$request->greater_than) {
                    $tempRow['id'] = $row->id;
                    $tempRow['no'] = $no++;
                    $tempRow['roll_no'] = $row->roll_number;
                    $tempRow['name'] = $row->full_name;
                    $tempRow['total_days'] = $total_days;
                    $tempRow['present'] = $row->attendance_present_count;
                    $tempRow['absent'] = $row->attendance_absent_count;
                    $tempRow['present_per'] = number_format($present_per, 2) . ' %';
                    $tempRow['absent_per'] = number_format($absent_per, 2) . ' %';
                    $rows[] = $tempRow;
                }
            }

            if ($request->greater_than && $request->less_than) {
                if ($request->greater_than <= $present_per && $request->less_than >= $present_per) {
                    $tempRow['id'] = $row->id;
                    $tempRow['no'] = $no++;
                    $tempRow['roll_no'] = $row->roll_number;
                    $tempRow['name'] = $row->full_name;
                    $tempRow['total_days'] = $total_days;
                    $tempRow['present'] = $row->attendance_present_count;
                    $tempRow['absent'] = $row->attendance_absent_count;
                    $tempRow['present_per'] = number_format($present_per, 2) . ' %';
                    $tempRow['absent_per'] = number_format($absent_per, 2) . ' %';
                    $rows[] = $tempRow;
                }
            }

            if (!$request->greater_than && !$request->less_than) {
                $tempRow['id'] = $row->id;
                $tempRow['no'] = $no++;
                $tempRow['roll_no'] = $row->roll_number;
                $tempRow['name'] = $row->full_name;
                $tempRow['total_days'] = $total_days;
                $tempRow['present'] = $row->attendance_present_count;
                $tempRow['absent'] = $row->attendance_absent_count;
                $tempRow['present_per'] = number_format($present_per, 2) . ' %';
                $tempRow['absent_per'] = number_format($absent_per, 2) . ' %';
                $rows[] = $tempRow;
            }
        }

        if(request()->get('print')){
            $class_section = ClassSection::find($class_section_id);
            $pdf = AcademicPrints::getInstance(get_center_id(), 'L');

            $pdf->printAttendanceReportList($rows, $class_section);

            return response(
                $pdf->Output('', 'ATTENDANCE REPORT.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function attendance_overview(Request $request)
    {
        // Attendance section
        $session_year = getSettings('session_year');
        $group = Group::find($request->class_group_id);
        if ($group) {
            $class_group = ClassGroup::where('group_id', $request->class_group_id)->get()->pluck('class_id');
            $class_section = ClassSection::whereIn('class_id', $class_group)->withCount(['attendance' => function ($q) use ($session_year) {
                $q->where('type', 1)
                    ->where('session_year_id', $session_year['session_year']);
            }])
                ->withCount(['absent_attendance' => function ($q) use ($session_year) {
                    $q->where('type', 0)
                        ->where('session_year_id', $session_year['session_year']);
                }])->with('class', 'section')
                ->get();

            $response = [
                'error'      => false,
                'message'    => 'Data fetched successfully',
                'group_name' => $group->name,
                'data'       => $class_section
            ];
        } else {
            $response = [
                'error'      => true,
                'message'    => 'No data found',
                'group_name' => '',
                'data'       => ''
            ];
        }

        return response()->json($response);
    }
}
