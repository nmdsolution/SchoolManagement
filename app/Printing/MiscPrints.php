<?php

namespace App\Printing;

class MiscPrints extends PDFBase 
{
    public function printAnnouncementList($list){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('announcement').' '.trans('list'))), 0, 1, 'C');


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('title'))), 1, 0, 'C', true);
        $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('description'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('assign_to'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['title'])), 1, 0, 'L');
            $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['description'])), 1, 0, 'L');
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['assign_to']), 1, 1, 'C');
        }
    }

    public function printExpenseList($list){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('online').' '.trans('fees').' '.trans('transactions'))), 0, 1, 'C');

        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(7.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Item Name'))), 1, 0, 'C', true);
        $this->Cell(6, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Purchase By'))), 1, 0, 'C', true);
        $this->Cell(6, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Purchase Source'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('date'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Total Amount'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 8);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(7.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['item_name'])), 1, 0, 'L');
            $this->Cell(6, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['purchase_by'])), 1, 0, 'L');
            $this->Cell(6, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['purchase_from'])), 1, 0, 'C');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['date'])), 1, 0, 'C');
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['total_amount'])), 1, 1, 'C');
        }
    }
}
