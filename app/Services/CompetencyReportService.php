<?php

namespace App\Services;

use App\Models\ClassSection;
use App\Models\ExamTerm;
use App\Models\Students;
use App\Models\Competency\CompetencyMark;
use App\Models\Competency\ClassCompetency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompetencyReportService
{
    public function generateCompetencyReport($request)
    {
        $classSection = ClassSection::findOrFail($request->class_section_id);
        $sessionYearId = getSettings('session_year')['session_year'];

        // Récupérer les compétences associées à la classe
        $competencies = ClassCompetency::with(['competency', 'competencyTypes'])
            ->where('class_id', $classSection->class_id)
            ->get();

        // Récupérer les étudiants de la classe
        $students = Students::where('class_section_id', $classSection->id)
            ->whereHas('studentSessions', function ($query) use ($sessionYearId) {
                $query->where('session_year_id', $sessionYearId);
            })
            ->get();

        // Récupérer les notes des étudiants
        $marks = CompetencyMark::with(['competencyType', 'competency'])
            ->whereIn('student_id', $students->pluck('id'))
            ->where('session_year_id', $sessionYearId)
            ->get();

        // Calculer les moyennes et autres statistiques
        $reportData = $this->calculateReportData($competencies, $marks, $students);

        // Créer ou mettre à jour le rapport
        $examReport = $this->createOrUpdateReport($classSection, $reportData);

        return $examReport;
    }

    private function calculateReportData($competencies, $marks, $students)
    {
        $reportData = [];
        foreach ($students as $student) {
            $studentMarks = $marks->where('student_id', $student->id);
            $totalMarks = 0;
            $totalCompetencies = 0;

            foreach ($competencies as $competency) {
                $competencyMarks = $studentMarks->where('competency_id', $competency->competency_id);
                $competencyTotal = $competencyMarks->sum('obtained_marks');
                $totalMarks += $competencyTotal;
                $totalCompetencies += $competencyMarks->count();
            }

            $average = $totalCompetencies > 0 ? $totalMarks / $totalCompetencies : 0;
            $reportData[$student->id] = [
                'student' => $student,
                'total_marks' => $totalMarks,
                'average' => $average,
            ];
        }

        return $reportData;
    }

    private function createOrUpdateReport($classSection, $reportData)
    {
        // Logique pour créer ou mettre à jour le rapport de compétences
        // Cela peut inclure la création d'un rapport dans la base de données
        // et l'enregistrement des détails des étudiants.

        // Exemple de code pour créer un rapport
        // $examReport = ExamReport::updateOrCreate([...]);

        return $examReport; // Retourner l'objet du rapport créé ou mis à jour
    }
} 