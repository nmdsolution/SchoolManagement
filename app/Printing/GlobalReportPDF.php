<?php

namespace App\Printing;

use App\Models\Group;
use App\Helpers\Number;
use App\Models\ExamTerm;
use App\Models\ExamReport;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\ExamSequence;
use Illuminate\Support\Facades\DB;
use App\Models\ExamReportClassDetails;
use App\Models\ExamReportStudentSubject;
use App\Models\ExamReportStudentSequence;

class GlobalReportPDF extends PDFBase
{
    protected $sequence;
    protected $term;
    protected $data;
    protected $class_stats;
    protected $groups;
    protected $group_stats;
    protected $term_stats;
    protected $top_students = [];
    protected $last_students = [];

    // Ajout de propriétés pour gérer les dimensions
    protected $pageWidth;
    protected $pageHeight;
    protected $margins = 3;
    protected $colWidth = 13; // Réduit de 20 à 15
    protected $dataCellHeight = 0.7;

    // Colors
    protected $colors = [
        'blue_gray' => [203, 212, 194],
        'deep_blue' => [51, 74, 94],
    ];

    protected function initializeData()
    {
        return [
            'EFF' => ['male' => 0, 'female' => 0],
            'EVA' => ['male' => 0, 'female' => 0],
            'MS10' => ['male' => 0, 'female' => 0],
            'MI10' => ['male' => 0, 'female' => 0],
            'MIN' => ['male' => 20, 'female' => 20],
            'MAX' => ['male' => 0, 'female' => 0],
            'SUM' => ['male' => 0, 'female' => 0],
        ];
    }

    protected function calculateStats($sequence, ?int $group_id = null)
    {
        $minimum_coefficient_percentage  = settings()->get('global_report_minimum_coefficient_percentage', 80);

        // Initialiser les group_stats
        $this->group_stats = [];
        $class_sectionsQb = ClassSection::owner()
            ->with('class', 'section');

        // Filtrer par groupe si group_id est fourni
        if ($group_id) {
            $class_sectionsQb = $class_sectionsQb->whereHas('class', function ($q) use ($group_id) {
                $q->whereHas('class_group', function ($q2) use ($group_id) {
                    $q2->where('group_id', $group_id);
                });
            });
        }

        $class_sections = $class_sectionsQb
            ->whereHas('class', function ($q) {
                $q->where('medium_id', getCurrentMedium()->id);
            })
            ->whereHas('student', function ($q) {
                $q->whereHas('studentSessions', function ($q2) {
                    $q2
                        ->where('session_year_id', getSessionYearData()->id)
                        ->where('active', true);
                });
            })
            ->get();

        $this->class_stats = [];

        foreach ($class_sections as $class_section) {
            $section_data = $this->initializeData();

            $report = ExamReport::whereClassSectionId($class_section->id)
                ->whereSessionYearId(getSessionYearData()->id)
                ->first();

            if (!$report) {
                continue;
            }

            // Récupérer les matières avec leurs coefficients via class_subjects
            $class_subjects = ClassSubject::whereHas('class', function ($q) use ($class_section) {
                $q->where('id', $class_section->class_id);
            })->with('subject')->get();

            $subjects = $class_section->subjects;
            $total_class_coefficients = $class_subjects->sum('weightage');
            $subject_ids = $class_subjects->pluck('subject_id');

            // Utiliser le pourcentage dynamique
            $minimum_required_coefficients = $total_class_coefficients * ($minimum_coefficient_percentage / 100);


            $student_ids = $class_section->student->pluck('id');

            $lines = ExamReportStudentSequence::whereExamSequenceId($sequence->id)
                ->whereIn('student_id', $student_ids)
                ->orderBy('rank', 'asc')
                ->get();

            foreach ($lines as $line) {
                // Récupérer les notes avec la relation complète pour accéder aux coefficients
                $subject_marks = ExamReportStudentSubject::whereExamReportId($report->id)
                    ->whereStudentId($line->student_id)
                    ->whereIn('subject_id', $subject_ids)
                    ->get();

                // Calculer la somme des coefficients des matières notées
                $student_total_coefficients = 0;
                foreach ($subject_marks as $mark) {
                    $class_subject = $class_subjects->firstWhere('subject_id', $mark->subject_id);
                    // Vérifier que les notes de séquence existent et ne sont pas nulles
                    $sequence_marks = $mark->sequence_marks;
                    if ($class_subject && $sequence_marks && isset($sequence_marks->{$sequence->id})) {
                        $student_total_coefficients += $class_subject->weightage;
                    }
                }

                $coefficient_percentage = ($student_total_coefficients / $total_class_coefficients) * 100;

                // Ne mettre à jour les stats que si l'élève atteint le seuil minimum
                if ($coefficient_percentage >= $minimum_coefficient_percentage) {
                    $gender = strtolower($line->student->user->gender);
                    $gender = in_array($gender, ['male', 'female']) ? $gender : 'male';

                    // Update global stats
                    $this->updateStats($this->data, $gender, $line);
                    // Update class section stats
                    $this->updateStats($section_data, $gender, $line);
                }
            }

            // Calculate class section statistics
            $this->class_stats[] = array_merge([
                'class_section' => $class_section->name,
                'class_section_id' => $class_section->id,  // Ajout de l'ID ici
                'total_class_coefficients' => $total_class_coefficients,
                'minimum_required_coefficients' => $minimum_required_coefficients,
                'excluded_students' => $lines->where('coefficient_percentage', '<', $minimum_coefficient_percentage)->count(),
                'total_students' => $lines->count()
            ], $this->calculateMetrics($section_data));
        }
    }

    protected function calculateTermStats($term, ?int $group_id = null)
{
    // Initialiser les statistiques
    $this->data = [
        'EFF' => ['male' => 0, 'female' => 0],
        'EVA' => ['male' => 0, 'female' => 0],
        'MS10' => ['male' => 0, 'female' => 0],
        'MI10' => ['male' => 0, 'female' => 0],
        'MIN' => ['male' => 20, 'female' => 20],
        'MAX' => ['male' => 0, 'female' => 0],
        'SUM' => ['male' => 0, 'female' => 0],
    ];
    
    $this->class_stats = [];

    $class_sectionsQb = ClassSection::owner()
        ->with(['class', 'section', 'class_subjects.subject']);

    // Filtrer par groupe si fourni
    if ($group_id) {
        $class_sectionsQb = $class_sectionsQb->whereHas('class', function ($q) use ($group_id) {
            $q->whereHas('class_group', function ($q2) use ($group_id) {
                $q2->where('group_id', $group_id);
            });
        });
    }

    $class_sections = $class_sectionsQb
        ->whereHas('class', function ($q) {
            $q->where('medium_id', getCurrentMedium()->id);
        })
        ->whereHas('student', function ($q) {
            $q->whereHas('studentSessions', function ($q2) {
                $q2->where('session_year_id', getSessionYearData()->id)
                   ->where('active', true);
            });
        })
        ->get();

    foreach ($class_sections as $class_section) {
        // Initialiser les stats de la section
        $section_data = [
            'EFF' => ['male' => 0, 'female' => 0],
            'EVA' => ['male' => 0, 'female' => 0],
            'MS10' => ['male' => 0, 'female' => 0],
            'MI10' => ['male' => 0, 'female' => 0],
            'MIN' => ['male' => 20, 'female' => 20],
            'MAX' => ['male' => 0, 'female' => 0],
            'SUM' => ['male' => 0, 'female' => 0],
        ];

        $report = ExamReport::whereClassSectionId($class_section->id)
            ->whereSessionYearId(getSessionYearData()->id)
            ->whereExamTermId($term->id)
            ->first();

            
            if (!$report) {
                continue;
            }
            
        $this->top_students = [...$report->top_student, ...$this->top_students];
        $this->last_students = [...$report->last_student, ...$this->last_students];
        // Récupérer les matières avec leurs coefficients
        $class_subjects = ClassSubject::whereHas('class', function ($q) use ($class_section) {
            $q->where('id', $class_section->class_id);
        })->with('subject')->get();

        $total_class_coefficients = $class_subjects->sum('weightage');
        $subject_ids = $class_subjects->pluck('subject_id');

        $student_ids = $class_section->student->pluck('id');
        $lines = ExamReportClassDetails::where('exam_report_id', $report->id)
            ->whereIn('student_id', $student_ids)
            ->orderBy('rank', 'asc')
            ->get();

        foreach ($lines as $line) {
            // Récupérer le genre de l'étudiant
            $gender = strtolower($line->student->user->gender ?? 'male');
            $gender = in_array($gender, ['male', 'female']) ? $gender : 'male';

            // Incrémenter l'effectif total
            $section_data['EFF'][$gender]++;
            $this->data['EFF'][$gender]++;

            // Ajouter la moyenne
            $section_data['SUM'][$gender] += $line->avg;
            $this->data['SUM'][$gender] += $line->avg;

            // Si l'élève a une moyenne (évalué)
            if ($line->avg > 0) {
                $section_data['EVA'][$gender]++;
                $this->data['EVA'][$gender]++;

                // Vérifier si la moyenne est >= 10
                if ($line->avg >= 10) {
                    $section_data['MS10'][$gender]++;
                    $this->data['MS10'][$gender]++;
                } else {
                    $section_data['MI10'][$gender]++;
                    $this->data['MI10'][$gender]++;
                }
            }

            // Mettre à jour MIN et MAX
            if ($line->avg > 0) {
                $section_data['MIN'][$gender] = min($section_data['MIN'][$gender], $line->avg);
                $section_data['MAX'][$gender] = max($section_data['MAX'][$gender], $line->avg);
                
                $this->data['MIN'][$gender] = min($this->data['MIN'][$gender], $line->avg);
                $this->data['MAX'][$gender] = max($this->data['MAX'][$gender], $line->avg);
            }
        }

        // Réinitialiser MIN à 0 si aucune note
        foreach (['male', 'female'] as $gender) {
            if ($section_data['MIN'][$gender] === 20 && $section_data['MAX'][$gender] === 0) {
                $section_data['MIN'][$gender] = 0;
            }
            if ($this->data['MIN'][$gender] === 20 && $this->data['MAX'][$gender] === 0) {
                $this->data['MIN'][$gender] = 0;
            }
        }

        // Ajouter les stats de la section
        $this->class_stats[] = array_merge([
            'class_section' => $class_section->name,
            'class_section_id' => $class_section->id,
            'total_class_coefficients' => $total_class_coefficients,
        ], $this->calculateMetrics($section_data));
    }
}
    protected function createExcludedStudentsTable($compact = false)
    {
        $minimum_coefficient_percentage  = settings()->get('global_report_minimum_coefficient_percentage', 80);
        $dataCellHeight = $this->dataCellHeight;
        $total_w = 28;

        // Structure pour collecter les élèves exclus
        $excluded_students = [];

        foreach ($this->class_stats as $class_stat) {
            $class_section = ClassSection::find($class_stat['class_section_id']);
            if (!$class_section) {
                continue;
            }

            $report = ExamReport::whereClassSectionId($class_section->id)
                ->whereSessionYearId(getSessionYearData()->id)
                ->first();

            if (!$report) {
                continue;
            }

            // Récupérer les matières avec leurs coefficients
            $class_subjects = ClassSubject::whereHas('class', function ($q) use ($class_section) {
                $q->where('id', $class_section->class_id);
            })->with('subject')->get();

            $total_class_coefficients = $class_subjects->sum('weightage');
            $subject_ids = $class_subjects->pluck('subject_id');

            // Récupérer tous les élèves de la classe
            $lines = ExamReportStudentSequence::with(['student.user'])
                ->whereExamSequenceId($this->sequence->id)
                ->whereIn('student_id', $class_section->student->pluck('id'))
                ->get();

            foreach ($lines as $line) {
                // Vérifier que l'étudiant existe
                if (!$line->student || !$line->student->user) {
                    continue;
                }

                // Récupérer les notes de l'élève
                $subject_marks = ExamReportStudentSubject::whereExamReportId($report->id)
                    ->whereStudentId($line->student_id)
                    ->whereIn('subject_id', $subject_ids)
                    ->get();

                $student_coefficients = 0;
                $evaluated_subjects = 0;
                $total_subjects = $class_subjects->count();

                foreach ($subject_marks as $mark) {
                    $class_subject = $class_subjects->firstWhere('subject_id', $mark->subject_id);
                    $sequence_marks = $mark->sequence_marks;

                    if (
                        $class_subject &&
                        $sequence_marks &&
                        isset($sequence_marks->{$this->sequence->id}) &&
                        is_numeric($sequence_marks->{$this->sequence->id})
                    ) {
                        $student_coefficients += $class_subject->weightage;
                        $evaluated_subjects++;
                    }
                }

                // Calculer le pourcentage
                $coefficient_percentage = $total_class_coefficients > 0 ?
                    ($student_coefficients / $total_class_coefficients) * 100 : 0;

                // Si l'élève n'atteint pas 80%, l'ajouter à la liste des exclus
                if ($coefficient_percentage < 80) {
                    $excluded_students[] = [
                        'name' => $line->student->user->full_name,
                        'matricule' => $line->student->admission_no,
                        'class' => $class_section->name,
                        'coefficient_percentage' => $coefficient_percentage,
                        'evaluated_subjects' => $evaluated_subjects,
                        'total_subjects' => $total_subjects,
                        'total_coefficients' => $student_coefficients,
                        'class_total_coefficients' => $total_class_coefficients,
                    ];
                }
            }
        }

        // Si aucun élève exclu, ne rien afficher
        if (empty($excluded_students)) {
            return;
        }

        // Titre du tableau
        $this->SetFont('Times', 'B', 12);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(
            $total_w,
            $dataCellHeight,
            $this->to_iso_8859_1(__('exclude_students_statistics', ['minimum_coefficient_percentage' => Number::format($minimum_coefficient_percentage, 2, null, app()->getLocale())])),
            1,
            1,
            'C',
            true
        );

        // Définir les colonnes
        $col_widths = [
            'name' => $total_w * 0.25,
            'matricule' => $total_w * 0.15,
            'class' => $total_w * 0.15,
            'subjects' => $total_w * 0.15,
            'coef' => $total_w * 0.15,
            'percent' => $total_w * 0.15,
        ];

        // En-têtes
        $this->SetFont('Times', 'B', 8);
        $this->Cell($col_widths['name'], $dataCellHeight, $this->to_iso_8859_1('name'), 1, 0, 'C', true);
        $this->Cell($col_widths['matricule'], $dataCellHeight, $this->to_iso_8859_1(__('matricule')), 1, 0, 'C', true);
        $this->Cell($col_widths['class'], $dataCellHeight, __('class'), 1, 0, 'C', true);
        $this->Cell($col_widths['subjects'], $dataCellHeight, $this->to_iso_8859_1(__('evaluated_subjects')), 1, 0, 'C', true);
        $this->Cell($col_widths['coef'], $dataCellHeight, $this->to_iso_8859_1(__('evaluated_weightage')), 1, 0, 'C', true);
        $this->Cell($col_widths['percent'], $dataCellHeight, __('percent'), 1, 1, 'C', true);

        // Données
        $this->SetFont('Times', '', 8);
        foreach ($excluded_students as $index => $student) {
            $this->SetFillColor($index % 2 == 0 ? 245 : 255, $index % 2 == 0 ? 245 : 255, $index % 2 == 0 ? 245 : 255);
            $this->SetTextColor(0, 0, 0);

            $this->Cell($col_widths['name'], $dataCellHeight, $this->to_iso_8859_1($student['name']), 1, 0, 'L', true);
            $this->Cell($col_widths['matricule'], $dataCellHeight, $student['matricule'], 1, 0, 'C', true);
            $this->Cell($col_widths['class'], $dataCellHeight, $this->to_iso_8859_1($student['class']), 1, 0, 'C', true);
            $this->Cell(
                $col_widths['subjects'],
                $dataCellHeight,
                $student['evaluated_subjects'] . '/' . $student['total_subjects'],
                1,
                0,
                'C',
                true
            );
            $this->Cell(
                $col_widths['coef'],
                $dataCellHeight,
                $student['total_coefficients'] . '/' . $student['class_total_coefficients'],
                1,
                0,
                'C',
                true
            );

            // Pourcentage en rouge si < 80%
            $this->SetTextColor(255, 0, 0);
            $this->Cell(
                $col_widths['percent'],
                $dataCellHeight,
                Number::format($student['coefficient_percentage'], 2) . '%',
                1,
                1,
                'C',
                true
            );
        }

        // Total des exclus
        $this->SetFont('Times', 'B', 8);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(
            $total_w,
            $dataCellHeight,
            $this->to_iso_8859_1( trans('excluded_students_total') . ': ' . count($excluded_students)),
            1,
            1,
            'R',
            true
        );

        $this->Ln(10);
    }

    protected function createClassGroupsResults($compact = false)
    {
        $dataCellHeight = $this->dataCellHeight;
        $total_w = 28;

        // En-têtes principaux
        $headers = [
            'Class Size',
            'Participation Rate',
            'Success Rate',
            'Average Score',
            'Minimum Score',
            'Maximum Score',
        ];

        $w = $total_w / (count($headers) * 3 + 3); // +3 pour la colonne du nom de classe

        // Titre de la section
        $this->SetFont('Times', 'B', 12);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($total_w, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__('results_per_group'))), 1, 1, 'C', true);

        // En-têtes des colonnes
        
        $this->SetFillColor(
            $this->colors['blue_gray'][0],
            $this->colors['blue_gray'][1],
            $this->colors['blue_gray'][2],
        );

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', 'B', 8);
        $this->Cell($w * 3, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__('Class Section'))), 1, 0, 'C', true);

        foreach ($headers as $key => $header) {
            $percent = in_array($key, [1, 2]) ? ' (%)' : '';
            $this->Cell($w * 3, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__($header)) . $percent), 1, 0, 'C', true);
        }
        $this->Ln();


        // Sous-en-têtes (M, F, T) si non compact
        if (!$compact) {
            $this->Cell($w * 3, $dataCellHeight, '', 1, 0, 'C', true);
            foreach ($headers as $header) {
                $this->Cell($w, $dataCellHeight, 'M', 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, 'F', 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, 'T', 1, 0, 'C', true);
            }
            $this->Ln();
        }

        // Parcourir chaque groupe
        foreach ($this->groups as $group_id) {
            $group = Group::find($group_id);
            // Réinitialiser les données pour ce groupe
            $this->data = $this->initializeData();

            // Calculer les stats pour ce groupe spécifique
            $this->calculateTermStats($this->term, $group_id);

            // Vérifier si le groupe a des classes
            if (empty($this->class_stats)) {
                // Si pas de classes, afficher une ligne vide ou un message
                $this->SetFont('Times', 'B', 10);
                $this->SetFillColor(51, 74, 94);
                $this->SetTextColor(255, 255, 255);
                $this->Cell($total_w, $dataCellHeight, $this->to_iso_8859_1($group->name . ' - No classes'), 1, 1, 'C', true);
                continue; // Passer au groupe suivant
            }

            $group_stats = [
                'group_name' => $group->name,
                'class_stats' => $this->class_stats,
                'group_metrics' => $this->calculateMetrics($this->data),
            ];

            // Vérifier si on a besoin d'une nouvelle page
            if ($this->GetY() > 180) {
                $this->AddPage();
                // Répéter les en-têtes
                
                

                $this->SetFont('Times', 'B', 12);
                $this->Cell($total_w, $dataCellHeight, $this->to_iso_8859_1(__('results_per_group') . ' (suite)'), 1, 1, 'C', true);

                // Répéter les en-têtes des colonnes
                $this->SetFillColor(
                    $this->colors['blue_gray'][0],
                    $this->colors['blue_gray'][1],
                    $this->colors['blue_gray'][2],
                );

                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Times', 'B', 8);
                $this->Cell($w * 3, $dataCellHeight, $this->to_iso_8859_1(__('Class Section')), 1, 0, 'C', true);
                foreach ($headers as $key => $header) {
                    $percent = in_array($key, [1, 2]) ? ' (%)' : '';
                    $this->Cell($w * 3, $dataCellHeight, $this->to_iso_8859_1(__($header) . $percent), 1, 0, 'C', true);
                }
                $this->Ln();

                if (!$compact) {
                    $this->Cell($w * 3, $dataCellHeight, '', 1, 0, 'C', true);
                    foreach ($headers as $header) {
                        $this->Cell($w, $dataCellHeight, 'M', 1, 0, 'C', true);
                        $this->Cell($w, $dataCellHeight, 'F', 1, 0, 'C', true);
                        $this->Cell($w, $dataCellHeight, 'T', 1, 0, 'C', true);
                    }
                    $this->Ln();
                }
            }

            // En-tête du groupe
            $this->SetFont('Times', 'B', 10);
            $this->SetFillColor(200, 200, 200);
            $this->SetTextColor(0, 0, 0);
            // $this->Cell($total_w, $dataCellHeight, $this->to_iso_8859_1($group_stats['group_name']), 1, 1, 'C', true);

            // Stats des classes du groupe
            $this->SetFont('Times', '', 8);
            foreach ($group_stats['class_stats'] as $stats) {
                $this->Cell($w * 3, $dataCellHeight, $this->to_iso_8859_1($stats['class_section']), 1, 0, 'C');

                // Class Size
                if (!$compact) {
                    $this->Cell($w, $dataCellHeight, $stats['EFF']['male'], 1, 0, 'C');
                    $this->Cell($w, $dataCellHeight, $stats['EFF']['female'], 1, 0, 'C');
                    $this->Cell($w, $dataCellHeight, $stats['class_size'], 1, 0, 'C');
                } else {
                    $this->Cell($w * 3, $dataCellHeight, $stats['class_size'], 1, 0, 'C');
                }

                // Participation Rate
                if (!$compact) {
                    $this->Cell($w, $dataCellHeight, $stats['EFF']['male'] > 0 ? Number::format($stats['male_part'], 2) : '/', 1, 0, 'C');
                    $this->Cell($w, $dataCellHeight, $stats['EFF']['female'] > 0 ? Number::format($stats['female_part'], 2) : '/', 1, 0, 'C');
                    $this->Cell($w, $dataCellHeight, Number::format($stats['part'], 2), 1, 0, 'C');
                } else {
                    $this->Cell($w * 3, $dataCellHeight, Number::format($stats['part'], 2), 1, 0, 'C');
                }

                // Success Rate
                if (!$compact) {
                    $this->Cell($w, $dataCellHeight, $stats['EFF']['male'] > 0 ? Number::format($stats['male_rate'], 2) : '/', 1, 0, 'C');
                    $this->Cell($w, $dataCellHeight, $stats['EFF']['female'] > 0 ? Number::format($stats['female_rate'], 2) : '/', 1, 0, 'C');
                    $this->Cell($w, $dataCellHeight, Number::format($stats['total_rate'], 2), 1, 0, 'C');
                } else {
                    $this->Cell($w * 3, $dataCellHeight, Number::format($stats['total_rate'], 2), 1, 0, 'C');
                }

                // Average Score
                if (!$compact) {
                    $this->Cell($w, $dataCellHeight, $stats['EFF']['male'] > 0 ? Number::format($stats['male_avg'], 2) : '/', 1, 0, 'C');
                    $this->Cell($w, $dataCellHeight, $stats['EFF']['female'] > 0 ? Number::format($stats['female_avg'], 2) : '/', 1, 0, 'C');
                    $this->Cell($w, $dataCellHeight, Number::format($stats['total_avg'], 2), 1, 0, 'C');
                } else {
                    $this->Cell($w * 3, $dataCellHeight, Number::format($stats['total_avg'], 2), 1, 0, 'C');
                }

                // Minimum Score
                if (!$compact) {
                    $this->Cell($w, $dataCellHeight, $stats['EFF']['male'] > 0 ? $stats['MIN']['male'] : '/', 1, 0, 'C');
                    $this->Cell($w, $dataCellHeight, $stats['EFF']['female'] > 0 ? $stats['MIN']['female'] : '/', 1, 0, 'C');
                    $this->Cell($w, $dataCellHeight, min($stats['MIN']['male'], $stats['MIN']['female']), 1, 0, 'C');
                } else {
                    $this->Cell($w * 3, $dataCellHeight, min($stats['MIN']['male'], $stats['MIN']['female']), 1, 0, 'C');
                }

                // Maximum Score
                if (!$compact) {
                    $this->Cell($w, $dataCellHeight, $stats['EFF']['male'] > 0 ? $stats['MAX']['male'] : '/', 1, 0, 'C');
                    $this->Cell($w, $dataCellHeight, $stats['EFF']['female'] > 0 ? $stats['MAX']['female'] : '/', 1, 0, 'C');
                    $this->Cell($w, $dataCellHeight, max($stats['MAX']['male'], $stats['MAX']['female']), 1, 0, 'C');
                } else {
                    $this->Cell($w * 3, $dataCellHeight, max($stats['MAX']['male'], $stats['MAX']['female']), 1, 0, 'C');
                }

                $this->Ln();
            }

            // Total du groupe
            $this->SetFont('Times', 'B', 8);
            $this->SetFillColor(
                $this->colors['blue_gray'][0],
                $this->colors['blue_gray'][1],
                $this->colors['blue_gray'][2],
            );

            $this->SetTextColor(0, 0, 0);
            $metrics = $group_stats['group_metrics'];

            $this->Cell($w * 3, $dataCellHeight, $this->to_iso_8859_1("Total " . $group_stats['group_name']), 1, 0, 'C', true);

            // Afficher les totaux du groupe
            if (!$compact) {
                // Class Size
                $this->Cell($w, $dataCellHeight, $metrics['EFF']['male'], 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, $metrics['EFF']['female'], 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, $metrics['EFF']['male'] + $metrics['EFF']['female'], 1, 0, 'C', true);

                // Participation Rate
                $this->Cell($w, $dataCellHeight, Number::format($metrics['male_part'], 2), 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, Number::format($metrics['female_part'], 2), 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, Number::format($metrics['part'], 2), 1, 0, 'C', true);

                // Success Rate
                $this->Cell($w, $dataCellHeight, Number::format($metrics['male_rate'], 2), 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, Number::format($metrics['female_rate'], 2), 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, Number::format($metrics['total_rate'], 2), 1, 0, 'C', true);

                // Average Score
                $this->Cell($w, $dataCellHeight, Number::format($metrics['male_avg'], 2), 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, Number::format($metrics['female_avg'], 2), 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, Number::format($metrics['total_avg'], 2), 1, 0, 'C', true);

                // Minimum Score
                $this->Cell($w, $dataCellHeight, Number::format($metrics['MIN']['male'], 2), 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, Number::format($metrics['MIN']['female'], 2), 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, Number::format(min($metrics['MIN']['male'], $metrics['MIN']['female']), 2), 1, 0, 'C', true);

                // Maximum Score
                $this->Cell($w, $dataCellHeight, Number::format($metrics['MAX']['male'], 2), 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, Number::format($metrics['MAX']['female'], 2), 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, Number::format(max($metrics['MAX']['male'], $metrics['MAX']['female']), 2), 1, 0, 'C', true);
            } else {
                // Version compacte des totaux
                $this->Cell($w * 3, $dataCellHeight, $metrics['EFF']['male'] + $metrics['EFF']['female'], 1, 0, 'C', true);
                $this->Cell($w * 3, $dataCellHeight, Number::format($metrics['part'], 2), 1, 0, 'C', true);
                $this->Cell($w * 3, $dataCellHeight, Number::format($metrics['total_rate'], 2), 1, 0, 'C', true);
                $this->Cell($w * 3, $dataCellHeight, Number::format($metrics['total_avg'], 2), 1, 0, 'C', true);
                $this->Cell($w * 3, $dataCellHeight, Number::format(min($metrics['MIN']['male'], $metrics['MIN']['female']), 2), 1, 0, 'C', true);
                $this->Cell($w * 3, $dataCellHeight, Number::format(max($metrics['MAX']['male'], $metrics['MAX']['female']), 2), 1, 0, 'C', true);
            }

            $this->Ln();
        }
    }

    protected function updateStats(&$stats, $gender, $line)
    {
        $stats['EFF'][$gender] += 1;
        $stats['SUM'][$gender] += $line->avg;
        if ($line->avg > 0) {
            $stats['EVA'][$gender] += 1;
        }
        if ($line->avg >= 10) {
            $stats['MS10'][$gender] += 1;
        } else {
            $stats['MI10'][$gender] += 1;
        }
        $stats['MAX'][$gender] = max($stats['MAX'][$gender], $line->avg);
        $stats['MIN'][$gender] = min($stats['MIN'][$gender], $line->avg);
    }

    public function updateTermStats(&$stats, ExamReport $report)
    {
        $stats['EFF']['male'] = $report->male_students;
        $stats['EFF']['female'] = $report->female_students;

        $stats['EVA']['male'] = $report->total_male_present;
        $stats['EVA']['female'] = $report->total_female_present;

        $stats['MS10']['male'] = $report->male_more_than_ten;
        $stats['MS10']['female'] = $report->female_more_than_ten;

        $stats['MI10']['male'] = $report->male_less_than_ten;
        $this->data['MI10']['female'] = $report->female_less_than_ten;

        $stats['MAX']['male'] = $report->male_highest_avg;
        $this->data['MAX']['female'] = $report->female_highest_avg;

        $stats['MIN']['male'] = $report->male_lowest_avg;
        $stats['MIN']['female'] = $report->female_lowest_avg;
    }

    protected function calculateMetrics($stats)
    {
        $class_size = $stats['EFF']['male'] + $stats['EFF']['female'];

        return [
            'EFF' => $stats['EFF'],
            'class_size' => $class_size,
            'male_part' => $stats['EFF']['male'] < 1 ? 0 : ($stats['EVA']['male'] / $stats['EFF']['male']) * 100,
            'female_part' => $stats['EFF']['female'] < 1 ? 0 : ($stats['EVA']['female'] / $stats['EFF']['female']) * 100,
            'part' => $class_size > 0 ? ($stats['EVA']['male'] + $stats['EVA']['female']) / $class_size * 100 : 0,
            'male_rate' => $stats['EFF']['male'] < 1 ? 0 : ($stats['MS10']['male'] / $stats['EFF']['male']) * 100,
            'female_rate' => $stats['EFF']['female'] < 1 ? 0 : ($stats['MS10']['female'] / $stats['EFF']['female']) * 100,
            'total_rate' => $class_size > 0 ? ($stats['MS10']['male'] + $stats['MS10']['female']) / $class_size * 100 : 0,
            'male_avg' => $stats['EFF']['male'] < 1 ? 0 : $stats['SUM']['male'] / $stats['EFF']['male'],
            'female_avg' => $stats['EFF']['female'] < 1 ? 0 : $stats['SUM']['female'] / $stats['EFF']['female'],
            'total_avg' => $class_size > 0 ? ($stats['SUM']['male'] + $stats['SUM']['female']) / $class_size : 0,
            'MIN' => $stats['MIN'],
            'MAX' => $stats['MAX'],
        ];
    }

    // public function Header() {
    // $this->SetFont('Times', 'B', 16);
    // $this->Cell(0, 10, 'Global Report for ' . $this->sequence->name, 0, 1, 'C');
    // $this->Ln(10);
    // }

    public function generateReport(ExamSequence $sequence, ?bool $compact = false)
    {
        // Générer le rapport pour la séquence spécifiée
        $this->SetTextColor(51, 74, 94);
        $this->underline = true;
        $this->Cell(0, 2, $this->to_iso_8859_1(__('global_report_sequence') . $sequence->name), 1, 1, 'C');
        $this->underline = false;
        $this->AddPage();

        $this->sequence = $sequence;
        $this->data = $this->initializeData();
        $this->groups = Group::owner()->pluck('id')->toArray();
        $this->data = $this->initializeData();

        // Génération du rapport par groupe
        $this->createClassGroupsResults($compact);
        $this->AddPage();

        $this->calculateStats($sequence);
        $metrics = $this->calculateMetrics($this->data);
        $this->createGlobalResults($metrics, $compact);

        $this->AddPage();
        $this->createClassResults($this->class_stats, $compact);

        // Ajouter le classement des classes
        $this->AddPage();
        $this->createClassRanking($compact);

        // Ajouter la liste des élèves exclus
        $this->AddPage();
        $this->createExcludedStudentsTable($compact);
    }

    public function generateTermReport(ExamTerm $term, ?bool $compact = false)
    {
        $this->term = $term;
        $this->data = $this->initializeData();
        $this->groups = Group::owner()->pluck('id')->toArray();

        // Document header
        $this->SetFont('Times', 'B', 12);
        $this->SetTextColor(51, 74, 94);
        $this->underline = true;
        $this->SetFillColor(255, 255, 255);
        $this->Cell(0, 2, $this->to_iso_8859_1(mb_strtoupper(__('global_report_term') . ': ' . $term->name)), 0, 1, 'C', true);
        $this->underline = false;
        $this->Ln(0);

        // Génération du rapport par groupe
        $this->createClassGroupsResults($compact);
        $this->AddPage();

        $this->calculateTermStats($term);
        $metrics = $this->calculateMetrics($this->data);

        $this->createClassResults($this->class_stats, $compact);
        $this->AddPage();
        
        $this->createGlobalResults($metrics, $compact);
        $this->AddPage();
        // Ajouter le classement des classes
        $this->createClassRanking($compact);

        // Ajouter la liste des élèves exclus
        // $this->AddPage();
        // $this->createExcludedStudentsTable($compact);
    }

    protected function createGlobalResults($metrics, ?bool $compact = false)
    {
        $total_w = 28;
        $dataCellHeight = $this->dataCellHeight;
        // En-têtes principaux avec largeurs ajustées
        $headers = [
            ['Class Size', 3],
            ['Evaluated', 3],
            ['Participation rate', 3],
            ['Success rate', 3],
            ['Average Score', 3],
            ['Minimum Score', 3],
            ['Maximum Score', 3],
        ];

        $w = $total_w / (count($headers) * 3);

        $this->SetFont('Times', 'B', 12);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($total_w, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__('global_results'))), 1, 1, 'C', true);

        // Calcul de l'espace disponible
        $usableWidth = $this->pageWidth - (2 * $this->margins);

        $this->SetFont('Times', 'B', 8);

        $this->SetFillColor(203, 212, 194);
        $this->SetTextColor(0, 0, 0);
        foreach ($headers as $key => $header) {
            $percent = in_array($key, [2, 3]) ? ' (%)' : '';
            $this->Cell($w * 3, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__($header[0]))) . $percent, 1, 0, 'C', true);
        }
        $this->Ln();

        // Sous-en-têtes (M, F, T)
        
        if (!$compact) {
            foreach ($headers as $header) {
                for ($i = 0; $i < 3; $i++) {
                    $this->Cell($w, $dataCellHeight, ['M', 'F', 'T'][$i], 1, 0, 'C', true);
                }
            }
            $this->Ln();
        }

        // Données
        $this->SetFont('Times', '', 8);
        $this->SetTextColor(0, 0, 0);

        // Class Size
        if (!$compact) {
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['male'], 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['female'], 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, $metrics['class_size'], 1, 0, 'C');
        } else {
            $this->Cell($w * 3, $dataCellHeight, $metrics['class_size'], 1, 0, 'C');
        }

        // Evalues
        if (!$compact) {
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['male'] > 0 ? $this->data['EVA']['female'] : '/', 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['female'] > 0 ? $this->data['EVA']['female'] : '/', 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, $this->data['EVA']['male'] + $this->data['EVA']['female'], 1, 0, 'C');
        } else {
            $this->Cell($w * 3, $dataCellHeight, $this->data['EVA']['male'] + $this->data['EVA']['female'], 1, 0, 'C');
        }

        // Participation rate
        if (!$compact) {
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['male'] > 0 ? Number::format($metrics['male_part'], 2, null, app()->getLocale()) : '/', 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['female'] > 0 ? Number::format($metrics['female_part'], 2, null, app()->getLocale()) : '/', 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, Number::format($metrics['part'], 2, null, app()->getLocale()), 1, 0, 'C');
        } else {
            $this->Cell($w * 3, $dataCellHeight, Number::format($metrics['part'], 2, null, app()->getLocale()), 1, 0, 'C');
        }

        // Success rate
        if (!$compact) {
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['male'] > 0 ? Number::format($metrics['male_rate'], 2, null, app()->getLocale()) : '/', 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['female'] > 0 ? Number::format($metrics['female_rate'], 2, null, app()->getLocale()) : '/', 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, Number::format($metrics['total_rate'], 2), 1, 0, 'C');
        } else {
            $this->Cell($w * 3, $dataCellHeight, Number::format($metrics['total_rate'], 2), 1, 0, 'C');
        }

        // Average score
        if (!$compact) {
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['male'] > 0 ? Number::format($metrics['male_avg'], 2, null, app()->getLocale()) : '/', 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['female'] > 0 ? Number::format($metrics['female_avg'], 2, null, app()->getLocale()) : '/', 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, Number::format($metrics['total_avg'], 2), 1, 0, 'C');
        } else {
            $this->Cell($w * 3, $dataCellHeight, Number::format($metrics['total_avg'], 2, null, app()->getLocale()), 1, 0, 'C');
        }

        // Minimum score
        if (!$compact) {
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['male'] > 0 ? $metrics['MIN']['male'] : '/', 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['female'] > 0 ? $metrics['MIN']['female'] : '/', 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, min($metrics['MIN']['male'], $metrics['MIN']['female']), 1, 0, 'C');
        } else {
            $this->Cell($w * 3, $dataCellHeight, min($metrics['MIN']['male'], $metrics['MIN']['female']), 1, 0, 'C');
        }

        // Maximum score
        if (!$compact) {
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['male'] > 0 ? $metrics['MAX']['male'] : '/', 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, $metrics['EFF']['female'] > 0 ? $metrics['MAX']['female'] : '/', 1, 0, 'C');
            $this->Cell($w, $dataCellHeight, max($metrics['MAX']['male'], $metrics['MAX']['female']), 1, 0, 'C');
        } else {
            $this->Cell($w * 3, $dataCellHeight, max($metrics['MAX']['male'], $metrics['MAX']['female']), 1, 0, 'C');
        }

        $this->Ln(20);
    }

    protected function createClassResults($class_stats, ?bool $compact = false)
    {
        $dataCellHeight = $this->dataCellHeight;
        // En-têtes principaux
        $headers = [
            'Class Size',
            'Participation Rate',
            'Success Rate',
            'Average Score',
            'Minimum Score',
            'Maximum Score',
        ];

        $total_w = 28;
        $w = $total_w / (count($headers) * 3 + 3);

        // Titre de la section
        $this->SetFont('Times', 'B', 12);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($total_w, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__('results_per_class'))), 1, 0, 'C', true);

        $this->Ln();

        // Ajustement des largeurs
        $this->SetFont('Times', 'B', 8);

        $this->SetFillColor(203, 212, 194);
        $this->SetTextColor(0, 0, 0);
        
        $this->Cell($w * 3, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__('Class Section'))), 1, 0, 'C', true);
        foreach ($headers as $key => $header) {
            $percent = in_array($key, [1, 2]) ? '(%)' : '';
            $this->Cell($w * 3, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__($header)) . $percent), 1, 0, 'C', true);
        }
        $this->Ln();

        // Sous-en-têtes (M, F, T)
        if (!$compact) {
            $this->Cell($w * 3, $dataCellHeight, '', 1, 0, 'C', true);
            for ($i = 0; $i < count($headers); $i++) {
                $this->Cell($w, $dataCellHeight, 'M', 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, 'F', 1, 0, 'C', true);
                $this->Cell($w, $dataCellHeight, 'T', 1, 0, 'C', true);
            }
            $this->Ln();
        }

        // Données pour chaque classe
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 8);
        foreach ($class_stats as $stats) {
            if ($this->GetY() > 180) {
                $this->AddPage();
                // Répéter les en-têtes
                $this->SetFont('Times', 'B', 12);
                $this->Cell(0, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__('per_class_results')) . ' (suite)'), 1, 1, 'L', true);
                $this->SetFont('Times', 'B', 8);

                // Répéter les en-têtes du tableau
                $this->Cell($w * 3, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__('Class Section'))), 1, 0, 'C', true);
                foreach ($headers as $header) {
                    $this->Cell($w * 3, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__($header))), 1, 0, 'C', true);
                }
                $this->Ln();

                // Répéter les sous-en-têtes
                if (!$compact) {
                    $this->SetFillColor(203, 212, 194);
                    $this->SetTextColor(0, 0, 0);
                    $this->Cell($w * 3, $dataCellHeight, '', 1, 0, 'C', true);
                    for ($i = 0; $i < count($headers); $i++) {
                        $this->Cell($w, $dataCellHeight, 'M', 1, 0, 'C', true);
                        $this->Cell($w, $dataCellHeight, 'F', 1, 0, 'C', true);
                        $this->Cell($w, $dataCellHeight, 'T', 1, 0, 'C', true);
                    }
                    $this->Ln();
                }
                $this->SetFont('Times', '', 8);
            }

            $this->Cell($w * 3, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper($stats['class_section'])), 1, 0, 'C');

            // Class Size
            if (!$compact) {
                $this->Cell($w, $dataCellHeight, $stats['EFF']['male'], 1, 0, 'C');
                $this->Cell($w, $dataCellHeight, $stats['EFF']['female'], 1, 0, 'C');
                $this->Cell($w, $dataCellHeight, $stats['class_size'], 1, 0, 'C');
            } else {
                $this->Cell($w * 3, $dataCellHeight, $stats['class_size'], 1, 0, 'C');
            }

            // Participation Rate
            if (!$compact) {
                $this->Cell($w, $dataCellHeight, $stats['EFF']['male'] > 0 ? Number::format($stats['male_part'], 2, null, app()->getLocale()) : '/', 1, 0, 'C');
                $this->Cell($w, $dataCellHeight, $stats['EFF']['female'] > 0 ? Number::format($stats['female_part'], 2, null, app()->getLocale()) : '/', 1, 0, 'C');
                $this->Cell($w, $dataCellHeight, Number::format($stats['part'], 2), 1, 0, 'C');
            } else {
                $this->Cell($w * 3, $dataCellHeight, Number::format($stats['part'], 2), 1, 0, 'C');
            }

            // Success Rate
            if (!$compact) {
                $this->Cell($w, $dataCellHeight, $stats['EFF']['male'] > 0 ? Number::format($stats['male_rate'], 2, null, app()->getLocale()) : '/', 1, 0, 'C');
                $this->Cell($w, $dataCellHeight, $stats['EFF']['female'] > 0 ? Number::format($stats['female_rate'], 2, null, app()->getLocale()) : '/', 1, 0, 'C');
                $this->Cell($w, $dataCellHeight, Number::format($stats['total_rate'], 2), 1, 0, 'C');
            } else {
                $this->Cell($w * 3, $dataCellHeight, Number::format($stats['total_rate'], 2, null, app()->getLocale()), 1, 0, 'C');
            }

            // Average Score
            if (!$compact) {
                $this->Cell($w, $dataCellHeight, $stats['EFF']['male'] > 0 ? Number::format($stats['male_avg'], 2, null, app()->getLocale()) : '/', 1, 0, 'C');
                $this->Cell($w, $dataCellHeight, $stats['EFF']['female'] > 0 ? Number::format($stats['female_avg'], 2, null, app()->getLocale()) : '/', 1, 0, 'C');
                $this->Cell($w, $dataCellHeight, Number::format($stats['total_avg'], 2), 1, 0, 'C');
            } else {
                $this->Cell($w * 3, $dataCellHeight, Number::format($stats['total_avg'], 2, null, app()->getLocale()), 1, 0, 'C');
            }

            // Minimum Score
            if (!$compact) {
                $this->Cell($w, $dataCellHeight, $stats['EFF']['male'] > 0 ? $stats['MIN']['male'] : '/', 1, 0, 'C');
                $this->Cell($w, $dataCellHeight, $stats['EFF']['female'] > 0 ? $stats['MIN']['female'] : '/', 1, 0, 'C');
                $this->Cell($w, $dataCellHeight, min($stats['MIN']['male'], $stats['MIN']['female']), 1, 0, 'C');
            } else {
                $this->Cell($w * 3, $dataCellHeight, min($stats['MIN']['male'], $stats['MIN']['female']), 1, 0, 'C');
            }

            // Maximum Score
            if (!$compact) {
                $this->Cell($w, $dataCellHeight, $stats['EFF']['male'] > 0 ? $stats['MAX']['male'] : '/', 1, 0, 'C');
                $this->Cell($w, $dataCellHeight, $stats['EFF']['female'] > 0 ? $stats['MAX']['female'] : '/', 1, 0, 'C');
                $this->Cell($w, $dataCellHeight, max($stats['MAX']['male'], $stats['MAX']['female']), 1, 0, 'C');
            } else {
                $this->Cell($w * 3, $dataCellHeight, max($stats['MAX']['male'], $stats['MAX']['female']), 1, 0, 'C');
            }

            $this->Ln();
        }
    }

    protected function createClassRanking($compact = false)
    {
        $dataCellHeight = $this->dataCellHeight;
        $total_w = 28;

        // Structure de données pour le classement
        $rankings = [];
        foreach ($this->class_stats as $stats) {
            $rankings[] = [
                'class_name' => $stats['class_section'],
                'EFF' => $stats['EFF']['male'] + $stats['EFF']['female'],
                'success_rate' => $stats['total_rate'],
                'average' => $stats['total_avg'],
            ];
        }

        // Trier le tableau par moyenne décroissante
        usort($rankings, function ($a, $b) {
            return $b['average'] <=> $a['average'];
        });

        // Définir les largeurs des colonnes
        $col_widths = [
            'rank' => $total_w * 0.1, // 10% pour le rang
            'name' => $total_w * 0.3, // 40% pour le nom
            'EFF' => $total_w * 0.2, // 40% pour le EFF
            'success' => $total_w * 0.2, // 25% pour le taux de réussite
            'average' => $total_w * 0.2, // 25% pour la moyenne
        ];

        // Titre du tableau
        $this->SetFont('Times', 'B', 12);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);
        $this->Cell($total_w, $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__('classes_ranking'))), 1, 1, 'C', true);

        // En-têtes des colonnes
        $this->SetFont('Times', 'B', 8);
        $this->SetFillColor(203, 212, 194);
        $this->SetTextColor(0, 0, 0);
        $this->Cell($col_widths['rank'], $dataCellHeight, 'Rang', 1, 0, 'C', true);
        $this->Cell($col_widths['name'], $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__('Class'))), 1, 0, 'C', true);
        $this->Cell($col_widths['EFF'], $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__('Class Size'))), 1, 0, 'C', true);
        $this->Cell($col_widths['success'], $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__('Success Rate')) . ' (%)'), 1, 0, 'C', true);
        $this->Cell($col_widths['average'], $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper(__('Average Score')) . '/20'), 1, 1, 'C', true);

        // Données
        $this->SetFont('Times', '', 8);
        $this->SetTextColor(0, 0, 0);

        foreach ($rankings as $index => $ranking) {
            // Alterner les couleurs de fond pour une meilleure lisibilité
            $this->SetFillColor($index % 2 == 0 ? 245 : 255, $index % 2 == 0 ? 245 : 255, $index % 2 == 0 ? 245 : 255);
            $this->SetFillColor(255, 255, 255);
            $this->SetTextColor(0, 0, 0);

            $this->Cell($col_widths['rank'], $dataCellHeight, $index + 1, 1, 0, 'C', true);
            $this->Cell($col_widths['name'], $dataCellHeight, $this->to_iso_8859_1(mb_strtoupper($ranking['class_name'])), 1, 0, 'L', true);
            $this->Cell($col_widths['EFF'], $dataCellHeight, $ranking['EFF'], 1, 0, 'C', true);
            if ($ranking['success_rate'] < 50) {
                $this->SetTextColor(255, 0, 0);
            }

            $this->Cell($col_widths['success'], $dataCellHeight, Number::format($ranking['success_rate'], 2, null, app()->getLocale()), 1, 0, 'C', true);
            if ($ranking['average'] < 10) {
                $this->SetTextColor(255, 0, 0);
            }
            $this->Cell($col_widths['average'], $dataCellHeight, Number::format($ranking['average'], 2, null, app()->getLocale()), 1, 1, 'C', true);
        }

        $this->Ln(10);
    }

    public function generateGroupReport($sequence, $groups, $compact = false)
    {
        $this->sequence = $sequence;
        $this->groups = $groups;
        $this->data = $this->initializeData();

        // Génération du rapport par groupe
        $this->createClassGroupsResults($compact);

        // Réinitialiser les données et calculer les stats globales
        $this->data = $this->initializeData();
        $this->calculateStats($sequence); // Calcul des stats globales sans filtre de groupe
        $metrics = $this->calculateMetrics($this->data);

        // Génération des résultats globaux
        $this->AddPage();
        $this->createGlobalResults($metrics, $compact);
    }
}
