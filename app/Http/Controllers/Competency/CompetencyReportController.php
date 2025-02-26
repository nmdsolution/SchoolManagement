<?php

namespace App\Http\Controllers\Competency;

use Exception;
use ZipArchive;
use App\Models\ExamTerm;
use App\Models\Settings;
use App\Models\Students;
use Illuminate\View\View;
use App\Models\SessionYear;
use App\Models\ClassSection;
use App\Models\ExamSequence;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Snappy\Facades\SnappyPdf;
use App\Models\Competency\CompetencyMark;
use App\Models\Competency\ClassCompetency;
use App\Models\Competency\CompetencyObservation;
use App\Models\Competency\CouncilReview;

class CompetencyReportController extends Controller
{
    public function reportCardList()
    {
        if (!Auth::user()->can('exam-report')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class_sections = ClassSection::owner()
            ->with('class')
            ->whereHas('class', function ($q) {
                $q->activeMediumOnly()->where('center_id', get_center_id());
            })
            ->get();

        $terms = ExamTerm::owner()
            ->currentSessionYear()
            ->currentMedium()
            ->get();

        $students = Students::currentSessionYear()
            ->where('center_id', get_center_id())
            ->with('user')
            ->get();

        return view('competency.report.report-card-list', compact('class_sections', 'terms', 'students'));
    }

    public function reportCardListData(Request $request)
    {
        $class_section_id = $request->class_section_id;
        $term_id = $request->term_id;

        $students = Students::where('class_section_id', $class_section_id)
            ->currentSessionYear()
            ->where('center_id', get_center_id())
            ->get();

        return response()->json(['students' => $students]);
    }

    public function generateReport(Request $request, Students $student)
    {
        if (!Auth::user()->can('exam-report')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }



        $download = $request->download;
        $sessionYear = getSessionYearData();
        $sessionYear = SessionYear::whereId($sessionYear->id)->firstOrFail();

        $student = Students::with([
            'user',
            'class_section.class.medium',
            'class_section.section',
            'class_section.teacher'
        ])->where('id', $student->id)
            ->whereHas('studentSessions', function ($q) use ($sessionYear) {
                $q->where('session_year_id', $sessionYear->id);
            })
            ->firstOrFail();

        $settings = array_merge(
            getSettings([
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
                'report_layout_type'
            ], null, getCurrentMedium()->id),
            getSettings('report_color')
        );

        $reportHeaderLogo = Settings::where('type', 'report_header_logo')
            ->where('center_id', get_center_id())
            ->currentMedium()
            ->first();
        // Récupérer les notes de l'élève
        $marks = CompetencyMark::with(['competencyType', 'competency'])
            ->where('student_id', $student->id)
            ->get();

        // Récupérer les compétences associées à la classe de l'élève
        $classSection = $student->class_section; // Assurez-vous que la relation est définie
        $competencies = ClassCompetency::with(['competency', 'competencyTypes'])
            ->where('class_id', $classSection->class_id)
            ->get();

        if (!$classSection->teacher) {
            return redirect()->back()->with('error', 'No teacher found for the class section.');
        }

        $terms = ExamTerm::owner()
            ->currentSessionYear()
            ->currentMedium()
            ->orderBy('id', 'ASC')
            ->where('id', '<=', $request->term_id)
            ->with('sequence')
            ->whereHas('sequence', function ($q) {
                $q->where('status', 1);
            })
            ->get();

        // Récupérer les séquences
        $sequences = ExamSequence::owner()->where('status', 1)
            ->whereHas('term', function ($q) use($terms) {
                $q->currentSessionYear()
                    ->currentMedium()->whereIn('id', $terms->pluck('id'));
            })
            ->where('status', 1)
            ->get();

        $sequenceData = [];
        $sequencesAverage = [];
        $totalAverage = 0; // Initialiser la somme des moyennes
        $sequenceCount = 0; // Compteur de séquences

        foreach ($terms as $term) {
            foreach ($term->sequence as $sequence) {
                $r = clone $request;
                $r->merge(['sequence_id' => $sequence->id, 'class_id' => $classSection->class_id, 'student_id' => $student->id]);
                $jsonResponse = app()->make(CompetencyMarksController::class)->callAction('getClassCompetencies', ['request' => $r]);
                $sequenceData[$sequence->id] = json_decode($jsonResponse->getContent(), true);
    
                // Calculer la moyenne pour chaque séquence
                $totalMarks = 0;
                $totalCompetencies = 0;
                foreach ($sequenceData[$sequence->id]['marks'] as $competency) {
                    foreach ($competency['marks'] as $type) {
                        $totalMarks += $type['mark'] ?? 0;
                        $totalCompetencies++;
                    }
                }
                $sequencesAverage[$sequence->id] = $totalCompetencies > 0 ? $totalMarks / $totalCompetencies : 0;
    
                // Ajouter à la somme des moyennes et incrémenter le compteur
                $totalAverage += $sequencesAverage[$sequence->id];
                $sequenceCount++;
            }
        }

        // Calculer la moyenne trimestrielle
        $quarterlyAverage = $sequenceCount > 0 ? $totalAverage / $sequenceCount : 0;

        $observations = CompetencyObservation::query()
            ->where('student_id', $student->id)
            ->whereIn('exam_term_id', $terms->pluck('id'))
            ->get();

        // Calculating average


        $councilReview = CouncilReview::whereIn('competency_observation_id', $observations->plucK('id'))->first();

        $view = 'competency.report.term-report';
        $data = compact('student', 'marks', 'competencies', 'sequences', 'terms', 'classSection', 'settings', 'reportHeaderLogo', 'sessionYear', 'sequenceData', 'observations', 'councilReview', 'sequencesAverage', 'quarterlyAverage');

        if ($download) {
            $view = 'competency.report.term-report';
            $pdf = SnappyPdf::loadView($view, $data)
                ->setOption('margin-top', 5) // marge supérieure de 20mm
                ->setOption('margin-bottom', 5) // marge inférieure de 20mm
                ->setOption('margin-left', 3) // marge gauche de 15mm
                ->setOption('margin-right', 3);
            return $pdf->download($student->user->first_name . '\'s-report.pdf');
        }

        return view($view, $data);
    }

    public function bulkDownloadReport(Request $request)
    {
        try {
            $termID = $request->term_id;
            $classSectionID = $request->class_section_id;

            $currentSessionYear = getSettings('session_year')['session_year'];
            $sessionYear = SessionYear::where('id', $currentSessionYear)->firstOrFail();
            $class_section = ClassSection::findOrFail($classSectionID);

            $term = ExamTerm::with('sequence')->where('id', $termID)->firstOrFail();

            $settings = array_merge(
                getSettings([
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
                    'report_layout_type'
                ], null, getCurrentMedium()->id),
                getSettings('report_color')
            );

            $reportHeaderLogo = Settings::where('type', 'report_header_logo')
                ->where('center_id', get_center_id())
                ->currentMedium()
                ->first();

            $students = Students::currentSessionYear()->whereHas('studentSessions', function ($query) use ($class_section) {
                $query->where('class_section_id', $class_section->id);
            })->get()->pluck('id');

            $terms = ExamTerm::owner()->where('session_year_id', $currentSessionYear)
                ->where('id', '<=', $term->id)
                ->with('sequence')
                ->currentMedium()
                ->get();

            $sequences = ExamSequence::where('exam_term_id', $termID)->get();

            // Temporary folder
            $zipPath = str_replace(' ', '_', $class_section->full_name) . '.zip';
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

                    $marks = CompetencyMark::with(['competencyType', 'competency'])
                        ->where('student_id', $student->id)
                        ->get();

                    // Récupérer les compétences associées à la classe de l'élève
                    $classSection = $student->class_section; // Assurez-vous que la relation est définie
                    $competencies = ClassCompetency::with(['competency', 'competencyTypes'])
                        ->where('class_id', $classSection->class_id)
                        ->get();

                    if (!$student) {
                        throw new Exception('Student not found');
                    }

                    $pdf = SnappyPdf::loadView('competency-report.', compact(
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
                        'attendance',
                        'all_sequences'
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

    public function bulkDownload(Request $request)
    {
        $class_section_id = $request->class_section_id;
        $term_id = $request->term_id;
        $request->merge(['download' => true]);

        $class_section = ClassSection::with('class')->find($class_section_id);

        if(!$class_section) {
            return response()->json([
                'error' => true,
                'message' => __('Unale to find this class')
            ]);
        }

        
        // Récupérer tous les élèves de la classe
        $students = Students::where('class_section_id', $class_section_id)->get();
        
        // Logique pour générer les bulletins pour chaque élève
        $pdfFiles = [];
        foreach ($students as $student) {
            // Appeler la méthode pour générer le rapport pour chaque élève
            $report = $this->generateReport($request, $student);
            $pdfFiles[] = $report; // Ajouter le chemin du fichier PDF généré
        }
        
        dd($pdfFiles);

        // Logique pour créer un fichier ZIP avec tous les PDF
        $zip = new ZipArchive();
        // Temporary folder
        $zipPath = str_replace(' ', '_', $class_section->full_name) . '.zip';
        $tempFolder = storage_path('app/temp_reports/');


        if (!is_dir($tempFolder)) {
            mkdir($tempFolder, 0777, true);
        }
        if ($zip->open(public_path($zipPath), ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response('Failed to create ZIP archive', 500);
        }


        foreach ($pdfFiles as $file) {
            $zip->addFile($file, basename($file));
        }

        $zip->close();

        return response()->json([
            'error' => false,
            'download_url' => url($zipPath)
        ]);
    }
}
