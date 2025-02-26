<?php

namespace App\Printing;

use App\Helpers\Number;

class DashboardPrints extends PDFBase 
{
    public function printStartStudents($list){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Star Students'))), 0, 1, 'C');


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(8, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('full_name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Marks'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('percentage'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(8, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['name'])), 1, 0, 'L');
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['class']), 1, 0, 'L');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', Number::format($item['marks'], 2, null, app()->getLocale())), 1, 0, 'C');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', Number::format($item['percentage'], 2, null, app()->getLocale())), 1, 1, 'C');
        }
    }

    public function printUpcomingExams($list){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Upcoming Exams'))), 0, 1, 'C');


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(8, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('start_date'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('end_date'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(8, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['name'])), 1, 0, 'L');
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['class']), 1, 0, 'L');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['start_date']), 1, 0, 'C');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['end_date']), 1, 1, 'C');
        }
    }

    public function printUnpublishedExamResult($list){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Upcoming Exams'))), 0, 1, 'C');


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(8, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('start_date'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('end_date'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(8, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['name'])), 1, 0, 'L');
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['class']), 1, 0, 'L');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['start_date']), 1, 0, 'C');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['end_date']), 1, 1, 'C');
        }
    }

    public function printPendingExamMarks($list){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Upcoming Exams'))), 0, 1, 'C');


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 0, 'C', true);
        $this->Cell(5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('subject'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Sequence'))), 1, 0, 'C', true);
        $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('teacher'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['class']), 1, 0, 'L');
            $this->Cell(5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['subject'])), 1, 0, 'L');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['sequence']), 1, 0, 'C');
            $this->Cell(7, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['teacher']), 1, 1, 'L');
        }
    }

    public function printEvents($list){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Events'))), 0, 1, 'C');


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(14, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('date'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(14, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['name'])), 1, 0, 'L');
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['name']), 1, 1, 'C');
        }
    }

    public function printHolidays($list){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('holiday_list'))), 0, 1, 'C');


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(14, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('date'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(14, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['name'])), 1, 0, 'L');
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['date']), 1, 1, 'C');
        }
    }
}
