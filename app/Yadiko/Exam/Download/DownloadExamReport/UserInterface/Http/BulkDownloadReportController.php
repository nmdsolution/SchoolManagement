<?php

namespace App\Yadiko\Exam\Download\DownloadExamReport\UserInterface\Http;

use App\Http\Controllers\Controller;
use Exception;
use ZipArchive;
use App\Models\Grade;
use App\Models\ExamTerm;
use App\Models\Students;
use App\Models\ExamReport;
use App\Models\SessionYear;
use Illuminate\Support\Str;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\ExamSequence;
use App\Models\EffectiveDomain;
use App\Models\ExamResultGroup;
use App\Models\StudentAttendance;
use App\Models\SubjectCompetency;
use Illuminate\Support\Facades\DB;
use App\Services\ExamReportService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamReportClassDetails;
use App\Models\ExamReportClassSubject;
use Barryvdh\Snappy\Facades\SnappyPdf;
use App\Models\ExamReportStudentSubject;
use App\Models\ExamReportStudentSequence;


class BulkDownloadReportController extends Controller
{
    private ExamReportService $examReportService;

    public function __construct(ExamReportService $examReportService) {
        $this->examReportService = $examReportService;
    }


    public function bulkDownloadExamReports($termID, $classSectionID, $paymentStatus = null)
    {
        try {
            $currentSessionYear = getSettings('session_year')['session_year'];
            $sessionYear = SessionYear::where('id', $currentSessionYear)->firstOrFail();
            $examReport = ExamReport::where(['exam_term_id' => $termID, 'class_section_id' => $classSectionID])->firstOrFail();
            $class_section = ClassSection::findOrFail($classSectionID);
            $report_id = $examReport->id;

            $term = ExamTerm::with('sequence')->where('id', $termID)->firstOrFail();

            // Get current medium ID
            $classMediumId = $class_section->class->medium_id;

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
                'report_header_logo',
                'report_water_mark',
                'report_layout_type',
                'subject_group_style',
                'competence_font_size',
                'discipline_master_signature',
                'council_decision'
            ], null, $classMediumId);

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
            $currentMedium = getCurrentMedium()->id;
            $classPerformance = ExamReportClassDetails::select(DB::raw('count(avg) as class_size, MAX(avg) as max_avg, MIN(avg) as min_avg, AVG(avg) as class_avg'))
                ->where(['exam_report_id' => $report_id])
                ->whereHas('student.class_section.class', function ($query) use ($classMediumId) {
                    $query->where('medium_id', $classMediumId);
                })
                ->first();

            // Base student query with medium-specific filtering
            $studentsQuery = ExamReportClassDetails::with(['student.user', 'student.payment_transactions' => function($query) {
                $query->whereHas('student', function ($q) {
                    $q->whereHas('class_section', function ($query) {
                        $query->Owner();
                    });
                });
            }])
                ->whereHas('student', function($q) use ($currentMedium) {
                    $q->whereHas('class_section', function($query) use ($currentMedium) {
                        $query->whereHas('class', function($q) use ($currentMedium) {
                            $q->where('medium_id', $currentMedium);
                        });
                    });
                })
                ->where('exam_report_id', $report_id);

            // Payment status filtering with improved logic
            if (!empty($paymentStatus)) {
                $status = (int)$paymentStatus;
                if (!in_array($status, [0, 1, 2])) {
                    throw new \Exception('Invalid payment status');
                }

                $studentsQuery->where(function ($query) use ($status) {
                    switch ($status) {
                        case 2: // Unpaid
                            $query->whereHas('student', function($q) {
                                $q->whereDoesntHave('payment_transactions')
                                    ->orWhereHas('payment_transactions', function($subQ) {
                                        $subQ->selectRaw('student_id, SUM(CASE WHEN amount_paid = 0 THEN total_amount ELSE amount_paid END) as total_paid')
                                            ->groupBy('student_id')
                                            ->havingRaw('total_paid = 0');
                                    });
                            });
                            break;
                        case 1: // Fully Paid
                            $query->whereHas('student.payment_transactions', function ($subQuery) {
                                $subQuery->where('amount_paid', '>=', 'total_amount');
                            });
                            break;
                        // Uncomment if you need partially paid students
                        /* case 0: // Partially Paid
                            $query->whereHas('student.payment_transactions', function ($subQuery) {
                                $subQuery->selectRaw('student_id, SUM(total_amount) as total_amount, SUM(CASE WHEN amount_paid = 0 THEN total_amount ELSE amount_paid END) as total_paid')
                                    ->groupBy('student_id')
                                    ->havingRaw('total_paid < total_amount');
                            });
                            break; */
                    }
                });
            }

            $students = $studentsQuery->get()->pluck('student.id');
            $low_subject_average = getSettings('report_low_subject_average', null, $classMediumId);
            $low_subject_average = $low_subject_average['report_low_subject_average'] ?? 0;

            $grades = Grade::where('center_id', Auth::user()->center->id)
                ->where('medium_id', $classMediumId) // Use class medium ID
                ->orderBy('ending_range', 'DESC')
                ->get();

            $effective_domain = EffectiveDomain::where('center_id', Auth::user()->center->id)
                ->where('medium_id', $classMediumId) // Use class medium ID
                ->orderBy('name', 'ASC')
                ->get();

            $terms = ExamTerm::owner()->where('session_year_id', $currentSessionYear)
                ->where('id', '<=', $term->id)
                ->with('sequence')
                ->currentMedium()
                ->get();

            $all_sequences = ExamSequence::whereHas('term', function ($query) use ($currentSessionYear) {
                $query->where('session_year_id', $currentSessionYear);
            })->get();

            // Temporary folder
            $zipPath = Str::slug($class_section->full_name) . '.zip';
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

                  //  $results = $this->buildExamReport($report_id, $termID, $studentID);

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

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } catch (Exception $e) {
            Log::error('Failed to generate bulk exam reports', ['error' => $e->getMessage()]);
            return response('Failed to generate reports. Please try again.', 500);
        }
    }

}