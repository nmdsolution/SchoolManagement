<?php

namespace App\Domain\Event\Exporter;

use App\Printing\DashboardPrints;
use Illuminate\Http\Response;

class EventPdfExporter
{
    public function export(array $events): Response
    {
        $pdf = DashboardPrints::getInstance(get_center_id(), 'P');
        $pdf->printEvents($events);

        return new Response(
            $pdf->Output('S'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="UPCOMING EVENTS.pdf"'
            ]
        );
    }
}