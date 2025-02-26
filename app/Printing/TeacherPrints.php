<?php

namespace App\Printing;


class TeacherPrints extends PDFBase 
{
    public function teacherList(iterable $list){
        try {
            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);
            $this->Cell(0, 1, strtoupper( trans('Teachers')), 0, 1, 'C');


            $this->SetFont('Times', 'B', 10);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
            $this->Cell(8, 0.8, strtoupper(trans('full_name')), 1, 0, 'C', true);
            $this->Cell(1.5, 0.8, strtoupper(trans('gender')), 1, 0, 'C', true);
            $this->Cell(3, 0.8, strtoupper( trans('mobile')), 1, 0, 'C', true);
            $this->Cell(4.5, 0.8, strtoupper( trans('email')), 1, 0, 'C', true);
            $this->Cell(4.5, 0.8, strtoupper( trans('qualification')), 1, 0, 'C', true);
            $this->Cell(5, 0.8, strtoupper( trans('Address')), 1, 1, 'C', true);

            $this->SetFont('Times', '', 9);
            $this->SetTextColor(0,0,0);

            $count = 1;
            foreach ($list as $teacher) {
                $user = $teacher->user;
                $this->Cell(1, 0.8, $count++, 1, 0, 'C');
                $this->Cell(8, 0.8, strtoupper($user->first_name.' '.$user->last_name), 1, 0, 'L');
                $this->Cell(1.5, 0.8, strtoupper($user->gender), 1, 0, 'L');
                $this->Cell(3, 0.8, strtoupper($user->mobile), 1, 0, 'L');
                $this->Cell(4.5, 0.8, $user->email, 1, 0, 'L');
                $this->Cell(4.5, 0.8, strtoupper($teacher->qualification), 1, 0, 'L');
                $this->Cell(5, 0.8, strtoupper($user->address), 1, 1, 'L');
            }
        } catch (\Throwable $throwable) {
            return response()->json([
                'error' => true,
                'message' => trans('contact_info_error')
            ]);
        }
    }
}
