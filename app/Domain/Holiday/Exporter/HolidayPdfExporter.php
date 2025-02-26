<?php


namespace App\Domain\Holiday\Exporter;

use App\Printing\DashboardPrints;
use Illuminate\Http\Response;

class HolidayPdfExporter
{
    public function export(array $holidays): Response
    {
        $pdf = DashboardPrints::getInstance(get_center_id(), 'P');
        $pdf->printHolidays($holidays);

        return new Response(
            $pdf->Output('S'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="UPCOMING HOLIDAYS.pdf"'
            ]
        );
    }
}