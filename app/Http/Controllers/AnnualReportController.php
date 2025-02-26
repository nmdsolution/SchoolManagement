<?php

namespace App\Http\Controllers;

use Exception;
use ZipArchive;
use App\Models\Grade;
use App\Models\ExamTerm;
use App\Models\Settings;
use App\Models\Students;
use App\Models\ExamReport;
use App\Models\SessionYear;
use App\Models\AnnualReport;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Printing\ExamPrints;
use Illuminate\Http\Request;
use App\Models\EffectiveDomain;
use App\Models\ExamResultGroup;
use App\Models\StudentAttendance;
use App\Models\AnnualClassDetails;
use Illuminate\Support\Facades\DB;
use App\Models\AnnualSubjectReport;
use App\Services\YearReportService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Snappy\Facades\SnappyPdf;
use App\Models\AnnualClassSubjectReport;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\directoryExists;

class AnnualReportController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('exam-report')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            DB::beginTransaction();
            $rep_service = new YearReportService();
            $data = $rep_service->createExamReport((object)$validator->validated());
            if (!$data) {
                $response = array(
                    'error'   => true,
                    'message' => "No Exam Data found to generate Report",
                );
            } else {
                $response = array(
                    'error'   => false,
                    'message' => trans('data_store_successfully'),
                );
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e->getMessage() . ' - File ' . $e->getFile() . ' At Line - ' . $e->getLine()
            );
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


// Fetch detailed student information
    private function getStudentDetails($studentID) {
        return Students::with([
            'user', 'class_section.class.medium',
            'class_section.section', 'class_section.teacher'
        ])->findOrFail($studentID);
    }

    // Get report settings for the view
    private function getReportSettings() {
        return getSettings([
            'report_left_header', 'report_right_header', 'report_color',
            'report_low_subject_average', 'report_blame', 'report_honor_roll',
            'report_honor_roll_absences', 'report_blame_min', 'report_blame_max',
            'report_warning_min', 'report_warning_max', 'average_blame_min',
            'average_blame_max', 'average_warning_min', 'average_warning_max',
            'encouragement_min', 'encouragement_max', 'congratulations_min',
            'congratulations_max'
        ], null, getCurrentMedium()->id);
    }

    private function buildExamReport($id, $studentID) {

        $sessionYearData = getSessionYearData();
        $report = AnnualReport::findOrFail($id);
        $student = $this->getStudentDetails($studentID);
        $term_report_ids = $report->term_report_ids;
        
        $annual_subject_report = AnnualSubjectReport::where([
            'annual_report_id' => $id,
            'student_id'     => $studentID
        ])->get();
        
        $term_reports = ExamReport::whereIn('id', $term_report_ids)
                                ->with('exam_term')
                                // ->orderBy('id', 'asc')
                                ->orderBy('exam_term_id', 'asc')
                                ->get();


        $settings = $this->getReportSettings();

        $reportHeaderLogo = getReportHeaderLogo();

        $examResultGroups = ExamResultGroup::owner()
            ->with([
                'subjects' => function ($q) use ($student) {
                    $q->where('class_id', $student->class_section->class->id)->where('exam_result_group_subjects.center_id', Auth::user()->center->id);
                }
                , 'subjects.teacher.user'
            ])->whereHas('subjects.teacher', function ($q) use ($student) {
                $q->where('class_section_id', $student->class_section->id);
            })->whereHas('examResultGroupSubject', function ($q) use ($student) {
                $q->where('class_id', $student->class_section->class->id);
            })->orderBy('position', 'asc')->get();
        $classSubject = ClassSubject::where('class_id', $student->class_section->class->id)->get();
        $annual_class_subject_report = AnnualClassSubjectReport::where('class_section_id', $student->class_section->id)->get();
        foreach ($examResultGroups as $group) {
            foreach ($group->subjects as $subject) {
                $class_subject = $classSubject->filter(function ($q) use ($subject) {
                    return $q->subject_id === $subject->id;
                })->first();

                if($class_subject == null) continue;

                $class_marks_details = $annual_class_subject_report->filter(function ($data) use ($subject) {
                    return $data->subject_id === $subject->id;
                })->first();
                $subject->class_subject = (object)$class_subject->toArray();
                if ($class_marks_details != null) {
                    $subject->class_details = (object)$class_marks_details->toArray();
                }

            }
        }
        $studentClassPerformance = AnnualClassDetails::where([
            'annual_report_id' => $id,
            'student_id'     => $student->id
        ])->first();

        $classPerformance = AnnualClassDetails::select(DB::raw('count(avg) as class_size, MAX(avg) as max_avg,MIN(avg) as min_avg,AVG(avg) as class_avg'))->where(['annual_report_id' => $id])->first();
        $low_subject_average = getSettings('report_low_subject_average', null, getCurrentMedium()->id);

        $low_subject_average = $low_subject_average['report_low_subject_average'] ?? 0;

        $grades = Grade::owner()->currentMedium()->orderBy('ending_range', 'DESC')->get();

        $effective_domain = EffectiveDomain::owner()->currentMedium()->orderBy('name', 'ASC')->get();
        
        $attendances = StudentAttendance::where(['student_id'=> $studentID, 
                                'class_section_id' => $student->class_section->id, 
                                'session_year_id' => $sessionYearData->id])
                                ->get();
        $attendance = (object)[
            'total_absences' => 0, 
            'justified_absences' => 0
        ];

        foreach($attendances as $item){
            $attendance->total_absences += $item->total_absences;
            $attendance->justified_absences += $item->justified_absences;
        }

        return SnappyPdf::loadView('exams.annual-result-report', 
        // return view('exams.annual-result-report', 
                        compact(
                                'sessionYearData',
                                'student', 
                                'examResultGroups',
                                'settings', 
                                'grades', 
                                'effective_domain', 
                                'annual_subject_report', 
                                'studentClassPerformance', 
                                'classPerformance', 
                                'low_subject_average', 
                                'reportHeaderLogo', 
                                'attendance', 
                                'term_reports'
                            )
                        );
    }

    public function downloadExamReport($annual_report_id, $student_id){
        // return $this->buildExamReport($annual_report_id, $student_id);
        $pdf = $this->buildExamReport($annual_report_id, $student_id);
        $student = Students::find($student_id);
        return $pdf->inline($student->user->first_name . '\'s-report.pdf');
    }

    public function bulkDownloadReports($class_section_id){
        $currentSessionYear = getSettings('session_year');
        $sessionYearData = SessionYear::where('id', $currentSessionYear['session_year'])->firstOrFail();
        // $report = AnnualReport::findOrFail($annual_report_id);
        $report = AnnualReport::where('class_section_id', $class_section_id)
                            ->where('session_year_id', $sessionYearData->id)
                            ->firstOrFail();
        $annual_report_id = $report->id;
        $term_report_ids = $report->term_report_ids;
        $class_section = $report->class_section;
        $student_list = $class_section->student;
        
        $term_reports = ExamReport::whereIn('id', $term_report_ids)
                                ->with('exam_term')
                                // ->orderBy('id', 'asc')
                                ->orderBy('exam_term_id', 'asc')
                                ->get();
        $settings1 = getSettings([
            'report_color'
        ]);
        $settings = getSettings([
            'report_left_header',
            'report_right_header',
            'report_color',
            'report_low_subject_average',
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
            'report_low_subject_average'
        ], null, getCurrentMedium()->id);

        $settings = array_merge($settings, $settings1);
        $reportHeaderLogo = Settings::where('type', 'report_header_logo')
                                ->where('center_id', get_center_id())
                                ->currentMedium()->first();

        $classPerformance = AnnualClassDetails::select(DB::raw('count(avg) as class_size, MAX(avg) as max_avg,MIN(avg) as min_avg,AVG(avg) as class_avg'))->where(['annual_report_id' => $annual_report_id])->first();
        $low_subject_average = getSettings('report_low_subject_average', null, getCurrentMedium()->id);
        $low_subject_average = $low_subject_average['report_low_subject_average'] ?? 0;
        $grades = Grade::owner()->currentMedium()->orderBy('ending_range', 'DESC')->get();
        $effective_domain = EffectiveDomain::owner()->currentMedium()->orderBy('name', 'ASC')->get();

        $classSubject = ClassSubject::where('class_id', $class_section->class->id)->get();
        $examResultGroups = ExamResultGroup::owner()
            ->with([
                'subjects' => function ($q) use ($class_section) {
                    $q->where('class_id', $class_section->class->id)->where('exam_result_group_subjects.center_id', Auth::user()->center->id);
                }
                , 'subjects.teacher.user'
            ])->whereHas('subjects.teacher', function ($q) use ($class_section) {
                $q->where('class_section_id', $class_section->id);
            })->whereHas('examResultGroupSubject', function ($q) use ($class_section) {
                $q->where('class_id', $class_section->class->id);
            })->orderBy('position', 'asc')->get();
        $annual_class_subject_report = AnnualClassSubjectReport::where('class_section_id', $class_section->id)->get();
        foreach ($examResultGroups as $group) {
            foreach ($group->subjects as $subject) {
                $class_subject = $classSubject->filter(function ($q) use ($subject) {
                    return $q->subject_id === $subject->id;
                })->first();

                if($class_subject == null) continue;

                $class_marks_details = $annual_class_subject_report->filter(function ($data) use ($subject) {
                    return $data->subject_id === $subject->id;
                })->first();
                $subject->class_subject = (object)$class_subject->toArray();
                if ($class_marks_details != null) {
                    $subject->class_details = (object)$class_marks_details->toArray();
                }

            }
        }


        $files = array();
        $folder = 'ann-'.$annual_report_id . '_' . $class_section->id;
        if(!directoryExists(public_path($folder))){
            mkdir(public_path($folder));
        }

        $zipname = $folder. '_' .$class_section->full_name . '.zip';
        $zipname = remove_accents($zipname);
        $zip = new ZipArchive();
        if($zip->open($zipname, ZipArchive::CREATE) != true){
            return response('', 500);
        }

        foreach($student_list as $stud){
            $student = Students::with([
                'user',
                'class_section.class.medium',
                'class_section.section',
                'class_section.teacher'
            ])->where('id', $stud->id)->first();
        
            $annual_subject_report = AnnualSubjectReport::where([
                'annual_report_id' => $annual_report_id,
                'student_id'     => $student->id
            ])->get();
            $studentClassPerformance = AnnualClassDetails::where([
                'annual_report_id' => $annual_report_id,
                'student_id'     => $student->id
            ])->first();
            
            $attendances = StudentAttendance::where(['student_id'=> $stud->id, 
                                    'class_section_id' => $student->class_section->id, 
                                    'session_year_id' => $currentSessionYear['session_year']])
                                    // ->whereIn('exam_term_id', )
                                    ->get();
            $attendance = (object)[
                'total_absences' => 0, 
                'justified_absences' => 0
            ];
            foreach($attendances as $item){
                $attendance->total_absences += $item->total_absences;
                $attendance->justified_absences += $item->justified_absences;
            }
            try{
            $pdf = SnappyPdf::loadView('exams.annual-result-report', 
                            compact(
                                    'sessionYearData',
                                    'student', 
                                    'examResultGroups',
                                    'settings', 
                                    'grades', 
                                    'effective_domain', 
                                    'annual_subject_report', 
                                    'studentClassPerformance', 
                                    'classPerformance', 
                                    'low_subject_average', 
                                    'reportHeaderLogo', 
                                    'attendance', 
                                    'term_reports'
                                )
                            );
            }catch(Exception $e){continue;}
            $filename = str_replace(" ", "_", $student->user->first_name);
            $filename = str_replace("/", "", $filename);
            $filename = $folder. '/'. $filename . '-report.pdf';
            $filename = remove_accents($filename);
            $pdf->save($filename, true);

            array_push($files, $filename);
            $zip->addFile($filename);
        }

        $zip->close();
        
        foreach($files as $filename){
            unlink($filename);
        }
        rmdir($folder);
        return response()->download($zipname)->deleteFileAfterSend(true);
    }
}
