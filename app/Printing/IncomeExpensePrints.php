<?php

namespace App\Printing;


class IncomeExpensePrints extends PDFBase
{
    public function printIncomeCategories($list)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);

        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Income Categories'))), 0, 1, 'C');


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('title'))), 1, 0, 'C', true);
        $this->Cell(10, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('description'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('slug'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0, 0, 0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['title'])), 1, 0, 'L');
            $this->Cell(10, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['description']), 1, 0, 'L');
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['slug']), 1, 1, 'C');
        }
    }
}