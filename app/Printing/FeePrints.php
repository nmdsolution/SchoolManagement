<?php

namespace App\Printing;

use App\Models\ClassSchool;
use App\Models\File;
use App\Models\SessionYear;
use Illuminate\Support\Facades\Storage;

class FeePrints extends PDFBase
{
    public function printFeeTypes($list)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        if (config('app.locale') == 'en') {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('fees') . ' ' . trans('type'))), 0, 1, 'C');
        } else {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('type') . ' ' . trans('fees'))), 0, 1, 'C');
        }


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(10, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('description'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('choiceable'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0, 0, 0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(10, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['name'])), 1, 0, 'L');
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['description']), 1, 0, 'L');
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['choiceable'] == 1 ? trans('yes') : trans('no')), 1, 1, 'C');
        }
    }

    public function printClassFees($list)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class') . ' ' . trans('fees'))), 0, 1, 'C');


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(6, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('base'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Total Amount'))), 1, 0, 'C', true);
        $this->Cell(6, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('description'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0, 0, 0);

        $count = 1;
        foreach ($list as $item) {
            $h = 0.8 * count($item['fees_type']);
            if ($h == 0) $h = 0.8;

            $this->Cell(1, $h, $count++, 1, 0, 'C');
            $this->Cell(6, $h, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['class_name'])), 1, 0, 'L');
            $this->Cell(3, $h, iconv('UTF-8', 'ISO-8859-1', $item['base_amount']), 1, 0, 'L');
            $this->Cell(3, $h, iconv('UTF-8', 'ISO-8859-1', $item['total_amount']), 1, 0, 'L');

            $x = $this->GetX();
            $count = 1;
            foreach ($item['fees_type'] as $type) {
                $this->SetX($x);
                $this->Cell(6, 0.8, iconv('UTF-8', 'ISO-8859-1', ($count++) . '. ' . $type['fees_name'] . ' - ' . $type['amount']), 1, 1, 'L');
            }
        }
    }

    public function printPaidFeesList($list, ClassSchool|null $class, SessionYear|null $session)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(mb_convert_encoding(trans('manage') . ' ' . trans('fees') . ' ' . trans('paid'), 'ISO-8859-1', 'UTF-8')), 0, 1, 'C');
    
        $this->SetFont('Times', 'B', 10);
        $this->SetTextColor(0, 0, 0);
        if ($class != null) {
            $this->Cell(0, 0.8, strtoupper(trans('class_name') . ': ' . $class->name), 0, 1, 'C');
        }
        if ($session != null) {
            $this->Cell(0, 0.8, strtoupper(trans('session_year') . ': ' . $session->name), 0, 1, 'C');
        }
    
        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);
    
        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(5.5, 0.8, strtoupper(mb_convert_encoding(trans('student_name'), 'ISO-8859-1', 'UTF-8')), 1, 0, 'C', true);
        $this->Cell(5.5, 0.8, strtoupper(mb_convert_encoding(trans('class'), 'ISO-8859-1', 'UTF-8')), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(mb_convert_encoding(trans('session_year'), 'ISO-8859-1', 'UTF-8')), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(mb_convert_encoding(trans('date'), 'ISO-8859-1', 'UTF-8')), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(mb_convert_encoding(trans('total') . ' ' . trans('fees'), 'ISO-8859-1', 'UTF-8')), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(mb_convert_encoding(trans('mode'), 'ISO-8859-1', 'UTF-8')), 1, 0, 'C', true);
        // $this->Cell(3, 0.8, strtoupper(mb_convert_encoding(trans('mode'), 'ISO-8859-1', 'UTF-8'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(mb_convert_encoding(trans('cheque_no'), 'ISO-8859-1', 'UTF-8')), 1, 1, 'C', true);
    
        $this->SetFont('Times', '', 8);
        $this->SetTextColor(0, 0, 0);
    
        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.6, $count++, 1, 0, 'C');
            $this->Cell(5.5, 0.6, strtoupper(mb_convert_encoding($item['student_name'], 'ISO-8859-1', 'UTF-8')), 1, 0, 'L');
            $this->Cell(5.5, 0.6, strtoupper(mb_convert_encoding($item['class_name'], 'ISO-8859-1', 'UTF-8')), 1, 0, 'L');
            $this->Cell(3, 0.6, strtoupper(mb_convert_encoding($item['session_year_name'], 'ISO-8859-1', 'UTF-8')), 1, 0, 'C');
            $this->Cell(3, 0.6, strtoupper(mb_convert_encoding($item['date'], 'ISO-8859-1', 'UTF-8')), 1, 0, 'C');
            $this->Cell(3, 0.6, mb_convert_encoding($item['total_fees'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
            $this->Cell(3, 0.6, strtoupper(mb_convert_encoding($item['mode'], 'ISO-8859-1', 'UTF-8')), 1, 0, 'C');
            $this->Cell(3, 0.6, strtoupper(mb_convert_encoding($item['cheque_no'], 'ISO-8859-1', 'UTF-8')), 1, 1, 'C');
        }
    }
    

    public function printFeeLogsList($list, ClassSchool|null $class, SessionYear|null $session)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('online') . ' ' . trans('fees') . ' ' . trans('transactions'))), 0, 1, 'C');


        $this->SetFont('Times', 'B', 10);
        $this->SetTextColor(0, 0, 0);
        if ($class != null) {
            $this->Cell(0, 0.8, (strtoupper(trans('class_name') . ': ' . $class->name)), 0, 1, 'C');
        }
        if ($session != null) {
            $this->Cell(0, 0.8, (strtoupper(trans('session_year') . ': ' . $session->name)), 0, 1, 'C');
        }

        $this->SetFont('Times', 'B', 7);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(5.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('session_year'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total') . ' ' . trans('fees'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('payment_gateway'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('payment_status'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('order_id'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('payment_id'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('payment_signature'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 7);
        $this->SetTextColor(0, 0, 0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.5, $count++, 1, 0, 'C');
            $this->Cell(5.5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['student_name'])), 1, 0, 'L');
            $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['session_year_name'])), 1, 0, 'L');
            $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['total_fees'])), 1, 0, 'C');
            $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['payment_gateway'])), 1, 0, 'C');
            $this->Cell(3, 0.5, iconv('UTF-8', 'ISO-8859-1', $item['payment_status']), 1, 0, 'C');
            $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['order_id'])), 1, 0, 'C');
            $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['payment_id'])), 1, 0, 'C');
            $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['payment_signature'])), 1, 1, 'C');
        }
    }

    public function printFeeReceipts($logo, $school_name, $fees_paid, $paid_installment, $fees_choiceable,
                                     $currency_symbol, $school_address, $fees_class, $session_year, $center): void
    {
        // Set font and text color
        $this->SetFont('Times', 'B', 10);
        $this->SetTextColor(0, 0, 0);

        // Add school logo
        if ($logo) {
            $logoWidth = 3;
            $logoHeight = 3;
            $this->Image($logo, (21 - $logoWidth) / 2, $this->GetY() - 3.5, $logoWidth, $logoHeight);
        }

        $contactInfo = 'Phone: '. $center->support_contact.' | Email: '. $center->support_email;

        if ($center->domain) {
            $contactInfo .= "Website: www.school.edu";
        }

        // $this->Cell(0, 0.8, iconv('UTF-8', 'ISO-8859-1', $school_name), 0, 1, 'C');
        $this->Cell(0, 0.8, iconv('UTF-8', 'ISO-8859-1', $center->tagline), 0, 1, 'C');
        $this->Cell(0, 0.8, $contactInfo, 0, 1, 'C');


        // Receipt title
        $this->SetFont('Times', 'B', 12);
        $this->Cell(0, 1, iconv('UTF-8', 'ISO-8859-1', trans("Fee Receipt")), 0, 1, 'C');
        $this->Ln(0.5);

        // Invoice details
        $this->SetFont('Times', 'B', 10);
        $this->Cell(0, 0.8, iconv('UTF-8', 'ISO-8859-1', trans("Invoice") . ': ' . (isset($fees_paid) ? $fees_paid->id : '-')), 0, 1);
        $this->Cell(0, 0.8, iconv('UTF-8', 'ISO-8859-1', trans("Payment Date") . ': ' . (isset($fees_paid) ? date('d-m-Y', strtotime($fees_paid->date)) : '-')), 0, 1);
        $this->Ln(0.5);

        // Student details
        $this->Cell(0, 0.8, iconv('UTF-8', 'ISO-8859-1', trans('Student Details') . ':'), 0, 1);
        $this->SetFont('Times', '', 10);
        $this->Cell(0, 0.8, iconv('UTF-8', 'ISO-8859-1', trans('Name') . ': ' . (isset($fees_paid) ? $fees_paid->student->user->first_name . ' ' . $fees_paid->student->user->last_name : '-')), 0, 1);
        $this->Cell(0, 0.8, iconv('UTF-8', 'ISO-8859-1', 'Session: ' . (isset($fees_paid) ? $fees_paid->session_year->name : '-')), 0, 1);
        $this->Cell(0, 0.8, iconv('UTF-8', 'ISO-8859-1', trans('Class') . ': ' . (isset($fees_paid) ? $fees_paid->class->name . ' - ' . $fees_paid->class->medium->name : '-')), 0, 1);
        $this->Ln(0.5);

        // Table header
        $this->SetFillColor(220, 220, 220); // Header background color
        $this->SetFont('Times', 'B', 10); // Header font style
        $this->Cell(3, 1, 'Sr no.', 1, 0, 'C', true);
        $this->Cell(10, 1, 'Fee Type', 1, 0, 'C', true);
        $this->Cell(5, 1, 'Amount', 1, 1, 'C', true);

// Table body
        $this->SetFont('Times', '', 10); // Regular font for details
        $no = 1;
        $rowColor = 255; // Start with white

// Sample logic for filling the table (this is a repeat of your logic, but with border changes)
        if ($fees_paid->is_fully_paid) {
            if (isset($paid_installment) && !empty($paid_installment->toArray())) {
                foreach ($paid_installment as $data) {
                    $this->SetFillColor($rowColor, $rowColor, $rowColor); // Alternate row color
                    $this->Cell(3, 1, $no++, 0, 0, 'C', true);
                    $this->Cell(10, 1, $data->installment_fee->name . "\n(PAID ON: " . date('d-m-Y', strtotime
                        ($data->date)) . ")", 0, 0, 'L', true);
                    $this->Cell(5, 1, $data->amount . ' ' . $currency_symbol, 0, 1, 'C', true);
                    $rowColor = ($rowColor == 255) ? 240 : 255; // Toggle color
                    if ($data->due_charges) {
                        $this->Cell(3, 1, $no++, 0, 0, 'C', true);
                        $this->Cell(10, 1, 'Due Charges (' . $data->installment_fee->name . ')', 0, 0, 'L', true);
                        $this->Cell(5, 1, $data->due_charges . ' ' . $currency_symbol, 0, 1, 'C', true);
                        $rowColor = ($rowColor == 255) ? 240 : 255;
                    }
                }
            } else {
                if (isset($fees_class) && !empty($fees_class)) {
                    foreach ($fees_class as $data) {
                        $this->SetFillColor($rowColor, $rowColor, $rowColor);
                        $this->Cell(3, 1, $no++, 0, 0, 'C', true);
                        $this->Cell(10, 1, $data->fees_type->name . "\n(PAID ON: " . date('d-m-Y', strtotime
                            ($fees_paid->date)) . ")", 0, 0, 'L', true);
                        $this->Cell(5, 1, $data->amount . ' ' . $currency_symbol, 0, 1, 'C', true);
                        $rowColor = ($rowColor == 255) ? 240 : 255;
                    }
                }
            }
        } else {
            if (isset($paid_installment) && !empty($paid_installment->toArray())) {
                foreach ($paid_installment as $data) {
                    $this->SetFillColor($rowColor, $rowColor, $rowColor);
                    $this->Cell(3, 1, $no++, 0, 0, 'C', true);
                    $this->Cell(10, 1, $data->installment_fee->name . "\n(PAID ON: " . date('d-m-Y', strtotime
                        ($data->date)) . ")", 0, 0, 'L', true);
                    $this->Cell(5, 1, $data->amount . ' ' . $currency_symbol, 0, 1, 'C', true);
                    $rowColor = ($rowColor == 255) ? 240 : 255;
                    if ($data->due_charges) {
                        $this->Cell(3, 1, $no++, 0, 0, 'C', true);
                        $this->Cell(10, 1, 'Due Charges (' . $data->installment_fee->name . ')', 0, 0, 'L', true);
                        $this->Cell(5, 1, $data->due_charges . ' ' . $currency_symbol, 0, 1, 'C', true);
                        $rowColor = ($rowColor == 255) ? 240 : 255;
                    }
                }
            }
        }

        // Display choiceable fees
        foreach ($fees_choiceable as $data) {
            $this->SetFillColor($rowColor, $rowColor, $rowColor);
            $this->Cell(3, 1, $no++, 0, 0, 'C', true);
            $this->Cell(10, 1, $data->fees_type->name . "\n(PAID ON: " . ($data->date ? date('d-m-Y', strtotime
                ($data->date)) : '-') . ")", 0, 0, 'L', true);
            $this->Cell(5, 1, $data->total_amount . ' ' . $currency_symbol, 0, 1, 'C', true);
            $rowColor = ($rowColor == 255) ? 240 : 255;
        }

        // Total Amount
        $this->SetFont('Times', 'B', 10); // Bold for emphasis
        $this->Cell(3, 1, '', 0);
        $this->Cell(10, 1, 'Total Amount:', 0);
        $this->Cell(5, 1, $fees_paid->total_amount . ' ' . $currency_symbol, 0, 1, 'C');


        // Signatures
        $this->Ln(1);
        $this->Cell(0, 0.8, iconv('UTF-8', 'ISO-8859-1', 'School Administrator Signature: ___________________________'), 0, 0, 'L');
        $this->Cell(0, 0.8, iconv('UTF-8', 'ISO-8859-1', 'Parent Signature: ___________________________'), 0, 1, 'R');
    }
}
