<?php

namespace App\Domain\SessionYear\Exporter;

use App\Printing\AcademicPrints;
use Illuminate\Http\Response;

class SessionPdfExporter
{
    public function export(array $sessions): Response
    {
        $pdf = AcademicPrints::getInstance(get_center_id());
        $pdf->printSessionsList($sessions);

        return new Response(
            $pdf->Output('S'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="SESSIONS LIST.pdf"'
            ]
        );
    }
}