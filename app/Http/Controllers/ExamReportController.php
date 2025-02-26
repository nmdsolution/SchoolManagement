<?php

namespace App\Http\Controllers;

use Exception;
use ZipArchive;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\ExamTerm;
use App\Models\Settings;
use App\Models\Students;
use App\Models\ExamReport;
use App\Models\SessionYear;
use Illuminate\Support\Str;
use App\Models\AnnualReport;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\ExamSequence;
use App\Printing\ExamPrints;
use Illuminate\Http\Request;
use App\Models\EffectiveDomain;
use App\Models\ExamResultGroup;
use App\Printing\AcademicPrints;
use App\Models\StudentAttendance;
use App\Models\SubjectCompetency;
use App\Models\AnnualClassDetails;
use Illuminate\Support\Facades\DB;
use App\Services\ExamReportService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamReportClassDetails;
use App\Models\ExamReportClassSubject;
use Barryvdh\Snappy\Facades\SnappyPdf;
use App\Models\ExamReportStudentSubject;
use App\Models\ExamReportStudentSequence;
use Illuminate\Support\Facades\Validator;


class ExamReportController extends Controller {
    private ExamReportService $examReportService;

    public function __construct(ExamReportService $examReportService) {
        $this->examReportService = $examReportService;
    }

    public function index() {
        if (!Auth::user()->can('exam-report')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class_sections = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        $terms = ExamTerm::owner()->currentSessionYear()->currentMedium()->get();

        return view('exams.report-index', compact('class_sections', 'terms'));
    }

    public function store(Request $request) {

        if (!Auth::user()->can('exam-report')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $validator = Validator::make($request->all(), [
            'term_id'          => 'required|numeric',
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
            $data = $this->examReportService->generateTermReport($request);
            DB::commit();
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
        } catch (\Throwable $e) {
            DB::rollback();
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e->getMessage() . ' - File ' . $e->getFile() . ' At Line - ' . $e->getLine(),
                'trace' => $e->getTrace(),
            );
        }
        return response()->json($response);
    }


    public function show(Request $request): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        if (!Auth::user()->can('exam-report')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        // TODO: to remove after primary school report implementation
        if (isPrimaryCenter()) {
            return response()->json(['message' => trans('not_iplemented_for_primary_center')], 403);
        }

        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required|numeric',
            'term_id'          => 'required|numeric',
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

        $examReport = ExamReport::where([
            'class_section_id' => $request->class_section_id,
            'exam_term_id'     => $request->term_id
        ])->first();

        if (!$examReport) {
            $response = array(
                'error'   => true,
                'message' => "Exam Report Doesn't Exists.Please Generate First.",
                'rows'    => [],
                'total'   => 0
            );
            return response()->json($response);
        }

        $sql = ExamReportClassDetails::with(['student.user'])
            ->where('exam_report_id', $examReport->id);

        if (!empty($_GET['search'])) {
            $search = $_GET['search'];

            $sql->where(function ($q) use ($search) {
                $q->whereHas('class_timetable', function ($q) use ($search) {
                    $q->where('id', 'LIKE', "%$search%")
                        ->orWhere('total_marks', 'LIKE', "%$search%")
                        ->orWhere('passing_marks', 'LIKE', "%$search%")
                        ->orWhere('start_time', 'LIKE', "%$search%")
                        ->orWhere('end_time', 'LIKE', "%$search%")
                        ->orWhere('date', 'LIKE', "%$search%")
                        ->orWhere('created_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                        ->orWhere('updated_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%");
                })->orWhereHas('exam', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhereHas('session_year', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%$search%");
                        });
                })->orWhereHas('class_section.class', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })->orWhereHas('class_timetable.subject', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                });
            });
        }
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
            $operate = '<a href="' . route('exam-report-temp.index', [
                    $row->exam_report_id,
                    $request->term_id,
                    $row->student_id
                ]) . '" target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit" ><i class="feather-file"></i></a>&nbsp;&nbsp;';
            $operate .= '<a href="' . route('exam-report-view', [
                    $row->exam_report_id,
                    $request->term_id,
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

    private function buildExamReport($id, $termID, $studentID): array
    {
        $sessionYear = getSessionYearData();

        $sessionYear = SessionYear::query()->where('id', $sessionYear->id)->firstOrFail();


        ExamReport::query()->findOrFail($id);

        $examReportDetails = ExamReportStudentSubject::query()->where([
            'exam_report_id' => $id,
            'student_id'     => $studentID
        ])->get();

        $term = ExamTerm::with('sequence')->where('id', $termID)->firstOrFail();

        $exam = Exam::query()->where([
            'session_year_id' => $sessionYear->id,
            'exam_term_id' => $term->id
        ]);

        $student = Students::with([
            'user',
            'class_section.class.medium',
            'class_section.section',
            'class_section.teacher'
        ])->where('id', $studentID)
            ->whereHas('studentSessions', function ($q) use ($sessionYear) {
                $q->where('session_year_id', $sessionYear->id);
            })
            ->firstOrFail();

        $settings = getSettings([
            'report_left_header',
            'report_right_header',
            'report_color',
            'report_low_subject_average',
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
            'report_low_subject_average',
            'report_color',
            'marks_font_size',
            'teacher_name_font_size',
            'subject_font_size',
            'marks_font_style',
            'teacher_name_font_style',
            'subject_font_style',
            // 'report_date_generated',
            'report_header_logo',
            'report_water_mark',
            'report_layout_type',
            'subject_group_style',
            'competence_font_size',
            'discipline_master_signature',
            'council_decision'
        ], null, getCurrentMedium()->id);

        $reportHeaderLogo = getReportHeaderLogo();
        $reportWaterMark = getReportWaterMark();

        $classSection = $student->studentSessions()->currentSessionYear()->class_section;

        $examResultGroups = ExamResultGroup::owner()
            ->with([
                'subjects' => function ($q) use ($classSection) {
                    $q->where('class_id', $classSection->class->id)->where('exam_result_group_subjects.center_id', Auth::user()->center->id);
                }
                , 'subjects.teacher.user'
            ])->whereHas('subjects.teacher', function ($q) use ($classSection, $student) {
                $q->where('class_section_id', $classSection->id);
            })->whereHas('examResultGroupSubject', function ($q) use ($classSection, $student) {
                $q->where('class_id', $classSection->class->id);
            })->orderBy('position', 'asc')->get();

        $classSubject = ClassSubject::query()->where('class_id', $classSection->class->id)->get();

        $examReportClassSubjects = ExamReportClassSubject::where('class_section_id', $classSection->id)->get();

        // Fetch subject competencies with more detailed filtering
        $subjectCompetencies = SubjectCompetency::where([
            'class_section_id' => $classSection->id,
        ])->with(['sequence', 'subject'])->get();

        // Organize competencies by sequence and subject
        $organizedCompetencies = $subjectCompetencies->groupBy(function($competency) {
            return $competency->exam_sequence_id . '_' . $competency->subject_id;
        })->map(function($group) {
            return $group->first(); // Take the first competency if multiple exist
        });
        
        $sequences = ExamSequence::where('exam_term_id', $termID)->get();

        // Attach competencies to exam result groups
        foreach ($examResultGroups as $group) {
            foreach ($group->subjects as $subject) {

                $class_subject = $classSubject->filter(function ($q) use ($subject) {
                    return $q->subject_id === $subject->id;
                })->first();

                if($class_subject == null) continue;

                $class_marks_details = $examReportClassSubjects->filter(function ($data) use ($subject) {
                    return $data->subject_id === $subject->id;
                })->first();

                $subject->class_subject = (object)$class_subject->toArray();

                if ($class_marks_details != null) {
                    $subject->class_details = (object)$class_marks_details->toArray();
                }

                $competencyKey = $sequences->first()->id . '_' . $subject->id;
                $competency = $organizedCompetencies->get($competencyKey);
                $subject->competency = $competency ? $competency->competence : null;
            }
        }

        $studentClassPerformance = ExamReportClassDetails::where([
            'exam_report_id' => $id,
            'student_id'     => $student->id
        ])->first();


        $classPerformance = ExamReportClassDetails::select(DB::raw('count(avg) as class_size, MAX(avg) as max_avg,MIN(avg) as min_avg,AVG(avg) as class_avg'))->where(['exam_report_id' => $id])->first();
        $low_subject_average = getSettings('report_low_subject_average', null, getCurrentMedium()->id);
        $low_subject_average = $low_subject_average['report_low_subject_average'] ?? 0;
        $grades = Grade::owner()->currentMedium()->orderBy('ending_range', 'DESC')->get();
        $effective_domain = EffectiveDomain::owner()->currentMedium()->orderBy('name', 'ASC')->get();
        $terms = ExamTerm::owner()->where('session_year_id', $sessionYear->id)
                    ->where('id', '<=', $term->id)
                    ->with('sequence')->currentMedium()->get();


        $all_sequences = ExamSequence::whereHas('term', function($query) use($sessionYear){
            $query->where('session_year_id', $sessionYear->id);
        })->get();

        $examReportStudentSequence = ExamReportStudentSequence::where([
            'student_id'       => $student->id,
            'class_section_id' => $classSection->id
        ])->whereHas('examTerm', function ($q) use ($sessionYear) {
            $q->where('session_year_id', $sessionYear->id);
        })->get();

        $studentTermPerformance = ExamReportClassDetails::query()->where([
            'student_id' => $student->id
        ])->with('exam_report')->whereHas('exam_report', function ($q) use ($terms) {
            $q->whereIn('exam_term_id', $terms->pluck('id'));
        })->get();
        
        $attendance = StudentAttendance::query()->firstOrNew(
            ['student_id'=> $studentID, 'exam_term_id' => $term->id, 'class_section_id' => $classSection->id, 'session_year_id' => $sessionYear->id],
            ['total_absences' => 0, 'justified_absences' => 0, 'unjustified_absences' => 0]
        );


        // doing this for the primary report cards.
        $sequenceTotals = [];
        $sequenceKeys = $sequences->pluck('id')->toArray();

        // make sure that all the sequences have been initialized
        foreach ($sequenceKeys as $key) {
            $sequenceTotals[$key] = 0;
        }

        // Add organized competencies to the returned data
        $additionalData = [
            'organized_competencies' => $organizedCompetencies,
        ];

        return array_merge(
            compact(
                'sessionYear', 
                'student',
                'examResultGroups', 
                'term', 
                'settings', 
                'grades', 
                'effective_domain', 
                'examReportDetails', 
                'studentClassPerformance', 
                'classPerformance', 
                'low_subject_average', 
                'terms', 
                'sequences', 
                'examReportStudentSequence', 
                'reportHeaderLogo',
                'reportWaterMark', 
                'attendance', 
                'studentTermPerformance', 
                'all_sequences'
            ),
            $additionalData
        );
    }

    public function downloadExamReport($id, $termID, $studentID): \Illuminate\Http\Response
    {
        $results = $this->buildExamReport($id, $termID, $studentID);

        $student = Students::query()->find($studentID);


        $class = $student->studentSessions()->currentSessionYear()->class_section->class;
    
        $reportLayout = $class->report_layout ?? 0;

        if ($reportLayout == 0) {
            $view = 'exams.result-report';
        } else {
            $view = 'exams.result-report-with-competence';
        }
        
        $pdf = SnappyPdf::loadView($view, $results);

        $student = Students::query()->find($studentID);

        return $pdf->download($student->user->first_name . '\'s-report.pdf');
    }

    public function viewExamReport($id, $termID, $studentID)
    {
        try {
            $results = $this->buildExamReport($id, $termID, $studentID);
    
            $student = Students::query()->find($studentID);
    
            $class = $student->studentSessions()->currentSessionYear()->class_section->class;
    
            $reportCardId = $class->report_card_id;
            $reportLayout = $class->report_layout ?? 0; 
    
            if ($reportCardId == 2) {
                $pdf = SnappyPdf::loadView('exams.primary-result-report', $results);
            } else { 

                if ($reportLayout == 0) {
                    $view = 'exams.result-report';
                } else {
                    $view = 'exams.result-report-with-competence';
                }
                
                $pdf = SnappyPdf::loadView($view, $results);
            }
    
            return $pdf->inline($student->user->first_name . '\'s-report.pdf');
        } catch (\Throwable $throwable) {
            return response()->json([
                'error' => true,
                'message' => $throwable->getMessage()
            ]);
        }
    }
    

    public function bulkDownloadExamReports($termID, $classSectionID)
    {
        try {
            $currentSessionYear = getSettings('session_year')['session_year'];
            $sessionYear = SessionYear::where('id', $currentSessionYear)->firstOrFail();
            $examReport = ExamReport::where(['exam_term_id' => $termID, 'class_section_id' => $classSectionID])->firstOrFail();
            $class_section = ClassSection::findOrFail($classSectionID);
            $report_id = $examReport->id;
    
            $term = ExamTerm::with('sequence')->where('id', $termID)->firstOrFail();
    
            $settings = getSettings([
                'report_left_header',
                'report_right_header',
                'report_color',
                'report_low_subject_average',
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
                'report_low_subject_average',
                'report_color',
                'marks_font_size',
                'teacher_name_font_size',
                'subject_font_size',
                'marks_font_style',
                'teacher_name_font_style',
                'subject_font_style',
                // 'report_date_generated',
                'report_header_logo',
                'report_water_mark',
                'report_layout_type',
                'subject_group_style',
                'competence_font_size',
                'discipline_master_signature',
                'council_decision'
            ], null, getCurrentMedium()->id);
    
            $reportHeaderLogo = getReportHeaderLogo();
            $reportWaterMark = getReportWaterMark();
    
            $examResultGroups = ExamResultGroup::owner()->with([
                'subjects' => function ($q) use ($class_section) {
                    $q->where('class_id', $class_section->class->id)
                        ->where('exam_result_group_subjects.center_id', Auth::user()->center->id);
                }, 'subjects.teacher.user'
            ])->whereHas('subjects.teacher', function ($q) use ($class_section) {
                $q->where('class_section_id', $class_section->id);
            })->orderBy('position', 'asc')->get();
    
            $classSubject = ClassSubject::where('class_id', $class_section->class->id)->get();
    
            $examReportClassSubjects = ExamReportClassSubject::where('class_section_id', $class_section->id)->get();
    
            $subjectCompetencies = SubjectCompetency::where([
                'class_section_id' => $class_section->id,
            ])->with(['sequence', 'subject'])->get();
            
            $sequences = ExamSequence::where('exam_term_id', $termID)->get();

            // Organize competencies by sequence and subject
            $organizedCompetencies = $subjectCompetencies->groupBy(function($competency) {
                return $competency->exam_sequence_id . '_' . $competency->subject_id;
            })->map(function($group) {
                return $group->first(); // Take the first competency if multiple exist
            });

            foreach ($examResultGroups as $group) {
                foreach ($group->subjects as $subject) {
                    $class_subject = $classSubject->filter(fn($q) => $q->subject_id === $subject->id)->first();
                    if (!$class_subject) continue;
    
                    $class_marks_details = $examReportClassSubjects->filter(fn($data) => $data->subject_id === $subject->id)->first();
    
                    $subject->class_subject = (object)$class_subject->toArray();
    
                    if ($class_marks_details != null) {
                        $subject->class_details = (object)$class_marks_details->toArray();
                    }

                    $competencyKey = $sequences->first()->id . '_' . $subject->id;
                    $competency = $organizedCompetencies->get($competencyKey);
                    $subject->competency = $competency ? $competency->competence : null;

                }
            }
    
            $classPerformance = ExamReportClassDetails::select(DB::raw('count(avg) as class_size, MAX(avg) as max_avg, MIN(avg) as min_avg, AVG(avg) as class_avg'))
                ->where(['exam_report_id' => $report_id])
                ->first();
    
            $students = Students::currentSessionYear()->whereHas('studentSessions', function ($query) use ($examReport) {
                $query->where('class_section_id', $examReport->class_section_id);
            })->get()->pluck('id');
    
            $low_subject_average = getSettings('report_low_subject_average', null, getCurrentMedium()->id);
            $low_subject_average = $low_subject_average['report_low_subject_average'] ?? 0;
    
            $grades = Grade::owner()->currentMedium()->orderBy('ending_range', 'DESC')->get();
            $effective_domain = EffectiveDomain::owner()->currentMedium()->orderBy('name', 'ASC')->get();
            $terms = ExamTerm::owner()->where('session_year_id', $currentSessionYear)
                ->where('id', '<=', $term->id)
                ->with('sequence')
                ->currentMedium()
                ->get();
    
            $all_sequences = ExamSequence::whereHas('term', function ($query) use ($currentSessionYear) {
                $query->where('session_year_id', $currentSessionYear);
            })->get();
    
            // Temporary folder
            $zipPath = Str::slug($class_section->full_name). '.zip';
            $tempFolder = storage_path('app/temp_reports/');
            
            if (!is_dir($tempFolder)) {
                mkdir($tempFolder, 0777, true);
            }
    
            $zip = new ZipArchive();
    
            if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
                return response('Failed to create ZIP archive', 500);
            }
    
            $files = [];
            $generationErrors = [];
    
            foreach ($students as $studentID) {
                try {
                    $student = Students::with([
                        'user',
                        'class_section.class.medium',
                        'class_section.section',
                        'class_section.teacher'
                    ])->find($studentID);
    
                    if (!$student) {
                        throw new Exception('Student not found');
                    }

                    $studentTermPerformance = ExamReportClassDetails::where('student_id', $studentID)
                        ->whereHas('exam_report', function($query) use ($terms) {
                            $query->whereIn('exam_term_id', $terms->pluck('id'));
                        })->get();
    
                    $examReportDetails = ExamReportStudentSubject::where([
                        'exam_report_id' => $report_id,
                        'student_id' => $studentID,
                    ])->get();
    
                    $attendance = StudentAttendance::where([
                        'student_id' => $studentID,
                        'exam_term_id' => $term->id,
                        'class_section_id' => $class_section->id,
                        'session_year_id' => $currentSessionYear,
                    ])->first();
    
                    // If attendance is not found, initialize it with default values for total, justified, unjustified absences
                    $attendance = $attendance ?: (object)[
                        'total_absences' => 0,
                        'justified_absences' => 0,
                        'unjustified_absences' => 0
                    ];

                    $studentClassPerformance = ExamReportClassDetails::where([
                        'exam_report_id' => $report_id,
                        'student_id' => $studentID,
                    ])->first();

                    $examReportStudentSequence = ExamReportStudentSequence::where([
                        'student_id'       => $studentID,
                        'class_section_id' => $student->class_section_id, 
                    ])->whereHas('examTerm', function ($query) use ($sessionYear) {
                        $query->where('session_year_id', $sessionYear->id);
                    })->get();

                    $class = $student->studentSessions()->currentSessionYear()->class_section->class;
    
                    $additional_data = [
                        'organized_competencies' => $organizedCompetencies
                    ];                   

                    $results = $this->buildExamReport($report_id, $termID, $studentID);

                    $reportLayout = $class->report_layout ?? 0; 

                    if ($reportLayout == 0) {
                        $view = 'exams.result-report';
                    } else {
                        $view = 'exams.result-report-with-competence';
                    }

                    // Generate PDF
                    $pdf = SnappyPdf::loadView($view, 
                    array_merge( 
                        compact(
                            'sessionYear', 
                            'student', 
                            'examResultGroups', 
                            'term', 
                            'settings',
                            'grades', 
                            'effective_domain', 
                            'examReportDetails', 
                            'studentClassPerformance',
                            'studentTermPerformance',
                            'classPerformance', 
                            'low_subject_average', 
                            'terms', 
                            'sequences',
                            'examReportStudentSequence', 
                            'reportHeaderLogo', 
                            'reportWaterMark',
                            'attendance', 
                            'all_sequences',
                        ),
                        $additional_data,
                    ));
    
                    $filename = $tempFolder . remove_accents(
                        str_replace([' ', '/'], ['_', ''], $student->user->first_name)
                    ) . '_report.pdf';
    
                    $pdf->save($filename, true);
                    $zip->addFile($filename, basename($filename));

                    $files[] = $filename;

                } catch (Exception $e) {
                    $generationErrors[] = [
                        'student_id' => $studentID,
                        'error' => $e->getMessage(),
                    ];
                    Log::error('Failed to generate student report', [
                        'student_id' => $studentID,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }
    
            $zip->close();
    
            if (!empty($generationErrors)) {
                Log::warning('Some student reports could not be generated', $generationErrors);
            }
    
            // Cleanup
            foreach (glob("$tempFolder/*.pdf") as $file) {
                unlink($file);
            }
            rmdir($tempFolder);

            // dd("THIS IS THE PATH: ".$tempFolder);

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (Exception $e) {
            Log::error('Failed to generate bulk exam reports', ['error' => $e->getMessage()]);
            return response('Failed to generate reports. Please try again.', 500);
        }
    }

    public function viewMasterSheet($report_id, $sequence_id=null): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $report = ExamReport::findOrFail($report_id);
        $pdf = AcademicPrints::getInstance(get_center_id(), 'L');

        if($sequence_id==null){
            $pdf->printMasterSheet($report);
        }else{
            $examSequence = ExamSequence::findOrFail($sequence_id);
            $pdf->printSequenceMasterSheet($report, $examSequence);
        }

        $filename = remove_accents(trans('master_sheet') .'-'. $report->class_section->full_name);

        return response(
            $pdf->Output('', $filename.'.pdf'),
            200,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }

    public function viewAnnualMasterSheet($report_id, $sequence_id=null): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $report = AnnualReport::findOrFail($report_id);
        $pdf = AcademicPrints::getInstance(get_center_id(), 'L');

        $pdf->printAnnualMasterSheet($report);

        $filename = remove_accents(trans('master_sheet') .'-'. $report->class_section->full_name);

        return response(
            $pdf->Output('', $filename.'.pdf'),
            200,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }
}
