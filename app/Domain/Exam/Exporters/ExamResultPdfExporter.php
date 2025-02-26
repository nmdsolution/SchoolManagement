<?php

namespace App\Domain\Exam\Exporters;

use App\Models\ClassSection;
use App\Models\Exam;
use App\Printing\ExamPrints;
use Illuminate\Http\Response;

class ExamResultPdfExporter
{
    public function exportResults(array $results, Exam $exam, ?ClassSection $classSection): Response
    {
        $pdf = ExamPrints::getInstance(get_center_id(), 'P');
        $pdf->printExamResultList($results, $exam, $classSection);

        return new Response(
            $pdf->Output('S'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="EXAM RESULTS.pdf"'
            ]
        );
    }
}