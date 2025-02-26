<?php

namespace App\Printing;

use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\ExamTerm;
use App\Models\Group;
use App\Models\Subject;

class StudentPrints extends PDFBase
{
    public function printNewStudentList($list, ClassSection|null $classSection){
        $currentIndex = 1;
        try {
            $this->classSectionHeader($classSection);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(1.5, 0.8, 'No', 1, 0, 'C', true);
            $this->Cell(8, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('first_name'))), 1, 0, 'C', true);
            $this->Cell(6, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('admission_no'))), 1, 0, 'C', true);
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('roll_no'))), 1, 0, 'C', true);
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('dob'))), 1, 1, 'C', true);

            $this->SetFont('Times', '', 9);
            $this->SetTextColor(0,0,0);

            $count = 1;
            foreach ($list as $index => $student) {
                $currentIndex = $index;
                $this->Cell(1.5, 0.8, $count++, 1, 0, 'C');
                $this->Cell(8, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['first_name'])), 1, 0, 'L');
                $this->Cell(6, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['admission_no'])), 1, 0, 'L');
                $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1',$student['roll_number']), 1, 0, 'L');
                $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1',$student['dob']), 1, 1, 'L');
            }
        } catch (\Throwable $throwable) {
            $student = $list[$currentIndex];
            return $this->wrongCharacterError($student);
        }
    }

    public function wrongCharacterError($student): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'error' => true,
            'message' => $student['no'] . " : " . $student['first_name'] . ' ' . trans('bad_attribute_message') ,
        ]);
    }

    public function printPromoteStudentList($list, ClassSection|null $classSection){
        $currentIndex = 1;
        try {
            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);
            if($classSection){
                $title = $classSection->class->name. ' ' . $classSection->section->name . ' ' .trans('promote_student');
                $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');
            }else{
                $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('promote_students'))), 0, 1, 'C');
            }

            $count = $this->studentListTitles1();
            foreach ($list as $index => $student) {
                $currentIndex = $index;
                $this->Cell(1.5, 0.8, $count++, 1, 0, 'C');
                $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['student_id'])), 1, 0, 'L');
                $this->Cell(12, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['name'])), 1, 0, 'L');
                $this->Cell(6, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['result'])), 1, 0, 'L');
                $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1',$student['status']), 1, 1, 'L');
            }
        } catch (\Throwable $throwable) {
            $student = $list[$currentIndex];
            return $this->wrongCharacterError($student);
        }
    }

    public function printPromotedStudentList($list, ClassSection|null $classSection){
        $currentIndex = 1;
        try {
            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);
            if($classSection){
                $title = $classSection->class->name. ' ' . $classSection->section->name . ' ' .trans('Promote Student List');
                $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');
            }else{
                $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Promote Student List'))), 0, 1, 'C');
            }

            $count = $this->studentListTitles1();
            foreach ($list as $index => $student) {
                $currentIndex = $index;
                $this->Cell(1.5, 0.8, $count++, 1, 0, 'C');
                $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['id'])), 1, 0, 'L');
                $this->Cell(12, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['student_name'])), 1, 0, 'L');
                $this->Cell(6, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['result'])), 1, 0, 'L');
                $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1',$student['status']), 1, 1, 'L');
            }
        } catch (\Throwable $throwable) {
            $student = $list[$currentIndex];
            return $this->wrongCharacterError($student);
        }
    }

    public function printStudentRollNoList($list, ClassSection|null $classSection){
        $currentIndex = 1;
        try {
            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);
            if($classSection){
                $title = $classSection->class->name. ' ' . $classSection->section->name . ' ' .trans('students'). ' ' .trans('roll_no');
                $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');
            }else{
                $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('students'). ' ' .trans('roll_no'))), 0, 1, 'C');
            }

            $this->SetFont('Times', 'B', 10);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(1.5, 0.8, 'No', 1, 0, 'C', true);
            $this->SetFont('Times','B',8);
            $this->Cell(2.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('roll_no'))), 1, 0, 'C', true);
            $this->SetFont('Times','B',10);
            $this->Cell(11.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Date of Birth'))), 1, 0, 'C', true);
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('admission_no'))), 1, 0, 'C', true);
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('admission_date'))), 1, 1, 'C', true);

            $this->SetFont('Times', '', 9);
            $this->SetTextColor(0,0,0);

            $count = 1;
            foreach ($list as $index => $student) {
                $currentIndex = $index;
                $this->Cell(1.5, 0.8, $count++, 1, 0, 'C');
                $this->Cell(2.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['old_roll_number'])), 1, 0, 'C');
                $this->Cell(11.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['first_name'].' '.$student['last_name'])), 1, 0, 'L');
                $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['dob'])), 1, 0, 'L');
                $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['admission_no'])), 1, 0, 'L');
                $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1',$student['admission_date']), 1, 1, 'L');
            }
        } catch (\Throwable $throwable) {
            $student = $list[$currentIndex];
            return $this->wrongCharacterError($student);
        }
    }

    public function addConfirmation(): void
    {
        $this->SetX(-9.3);

        $this->Cell(0, 1 , '________________________________________', 0, 0, 'L');
        $this->Ln(0.7);

        $this->SetX(-10);
        $this->SetFont('Times', 'B', 10);
        $this->Cell(8, 1, iconv('UTF-8', "ISO-8859-1" , trans('principal_signature')), 0, 0.5, 'R');

        $this->SetFont('Times', 'B', 10);
        $this->SetX(-9);
        $signedOn = trans('signed_on');
        $this->Cell(0, 1 ,  iconv('UTF-8', "ISO-8859-1" , $signedOn .'  _____________________________'), 0, 0.5, 'L');
    }

    public function printStudentList($list, ClassSection|null $classSection){
        $currentIndex = 0;
        try {
            $this->classSectionHeader($classSection);
            $x_point = $this->maleFemaleStatsHeader($list);

            $y_point = $this->GetY();
            $this->Ln(1);

            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Times', 'B', 8);

            $this->Cell(1, 0.5, 'No', 1, 0, 'C', true);
            $this->Cell(2, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('matricule'))), 1, 0, 'C', true);
            $this->Cell(6, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true);
            $this->Cell(1.5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('gender'))), 1, 0, 'C', true);
            $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Date of Birth'))), 1, 0, 'C', true);
            $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('at'))), 1, 0, 'C', true);
            $this->Cell(1.5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('status'))), 1, 1, 'C', true);

            $this->SetFont('Times', '', 8);
            $this->SetTextColor(0, 0, 0);
            $stats = array(
                'N' => array(
                    'male'  => 0,
                    'female' => 0
                ),
                'O'=> array(
                    'male'  => 0,
                    'female' => 0
                )
            );

            foreach ($list as $index => $item) {
                $currentIndex = $index;
                $name = trim($item['first_name'] . ' ' . $item['last_name']);
                $genderCharacter = $item['gender'][0];

                $this->Cell(1, 0.5, $item['no'], 1, 0, 'C');
                $this->Cell(2, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['admission_no'])), 1, 0, 'C');
                $this->Cell(6, 0.5, strtoupper(iconv('UTF-8', "ISO-8859-1" , $this->filterDatabaseEmptyCharacter($name))), 1, 0, 'L');
                $this->Cell(1.5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $genderCharacter)), 1, 0, 'C');
                $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['dob'])), 1, 0, 'C');
                $place = $item['dynamic_data_field']['Place_of_birth'] ?? $item['born_at'];

                $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', "ISO-8859-1" , $this->filterDatabaseEmptyCharacter($place))), 1, 0, 'L');

                $status = "O";
                if(isset($item['dynamic_data_field']['Status'])){
                    $status = $item['dynamic_data_field']['Status'];
                }
                $status = $status == 'N'? 'N' : 'O';

                $gender = strtolower($item['gender']);
                if($gender == 'm') $gender = 'male';
                else if ($gender == 'f') $gender = 'female';
                else $gender = $this->getGender($gender);

                $stats[$status][$gender] +=1;
                if($status == "O" && config('app.locale')=='fr')
                    $status = 'A';
                $this->Cell(1.5, 0.5, iconv('UTF-8', "ISO-8859-1" , $status), 1, 1, 'C');
            }

            $this->Ln(1);

            $this->SetFont('Times', '', 10);
            $this->SetX(-9.7);

            $this->addConfirmation();

            $last_page = $this->page;
            $this->page=1;
            $this->SetY($y_point);
            $this->SetX($x_point);
            $this->SetFont('Times', '', 10);
            $this->oldNewGenderStats($stats, $list);

            $this->page = $last_page;

            return response(
                $this->Output('', 'STUDENT LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        } catch (\Throwable $throwable) {
            $student = $list[$currentIndex];
            return $this->wrongCharacterError($student);
        }
    }

    public function printParentsList($list){
        $currentIndex = 0;
        try {
            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('parents').' '.trans('list'))), 0, 1, 'C');

            $this->SetFont('Times', 'B', 10);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(1.5, 0.8, 'No', 1, 0, 'C', true);
            $this->Cell(8, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('full_name'))), 1, 0, 'C', true);
            $this->Cell(2, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('gender'))), 1, 0, 'C', true);
            $this->Cell(3.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('email'))), 1, 0, 'C', true);
            $this->Cell(3.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('mobile'))), 1, 1, 'C', true);

            $this->SetFont('Times', '', 9);
            $this->SetTextColor(0,0,0);

            $count = 1;
            foreach ($list as $index => $parent) {
                $this->Cell(1.5, 0.8, $count++, 1, 0, 'C');
                $this->Cell(8, 0.8, iconv('UTF-8', 'ISO-8859-1',$parent['first_name'].' '.$parent['last_name']), 1, 0, 'L');
                $this->Cell(2, 0.8, iconv('UTF-8', 'ISO-8859-1',$parent['gender']), 1, 0, 'L');
                $this->Cell(3.5, 0.8, iconv('UTF-8', 'ISO-8859-1',$parent['email']), 1, 0, 'L');
                $this->Cell(3.5, 0.8, iconv('UTF-8', 'ISO-8859-1',$parent['mobile']), 1, 1, 'L');
            }
        } catch (\Throwable $throwable) {
            $parent = $list[$currentIndex];
            return $this->wrongCharacterError($parent);
        }
    }

    public function printStudentAssignmentList($list, ClassSection|null $class, Subject|null $subject){
        try {
            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('assignment_submission').' '.trans('list'))), 0, 1, 'C');


            $this->SetFont('Times', 'B', 10);
            $this->SetTextColor(0,0,0);
            if($class!=null){
                $class->section;
                $class->class;
                $this->Cell(0, 0.8, (strtoupper(trans('class_name').': '.$class->full_name)), 0, 1, 'C');
            }
            if($subject!=null){
                $this->Cell(0, 0.8, (strtoupper(trans('subject').': '.$subject->name)), 0, 1, 'C');
            }

            $this->SetFont('Times', 'B', 10);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(1.5, 0.8, 'No', 1, 0, 'C', true);
            $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('assignment_name'))), 1, 0, 'C', true);
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('subject'))), 1, 0, 'C', true);
            $this->Cell(9, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true);
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('points'))), 1, 0, 'C', true);
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('status'))), 1, 1, 'C', true);

            $this->SetFont('Times', '', 9);
            $this->SetTextColor(0,0,0);

            $count = 1;
            foreach ($list as $student) {
                $this->Cell(1.5, 0.8, $count++, 1, 0, 'C');
                $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['assignment_name'])), 1, 0, 'L');
                $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['subject'])), 1, 0, 'L');
                $this->Cell(9, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['student_name'])), 1, 0, 'L');
                $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1',$student['points']), 1, 0, 'L');
                $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1',$student['status']), 1, 1, 'L');
            }
        }  catch (\Throwable $throwable) {
            return response()->json([
                'error' => true,
                'message' => trans("contact_info_error")
            ]);
        }
    }

    public function printAttendanceList($list, ClassSection $classSection, ExamTerm $examTerm){
        try {
            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);
            $title = trans('daily_absences').': '.$classSection->full_name ;
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');

            $this->SetFont('Times', 'B', 10);
            $this->SetTextColor(0,0,0);

            $this->Cell($this->GetPageWidth()/3, 1, iconv('UTF-8', 'ISO-8859-1', trans('Exam Term').' : '. $examTerm->name), 0, 0);
            $this->Cell($this->GetPageWidth()/3, 1, iconv('UTF-8', 'ISO-8859-1', trans('week_of').' : '), 0, 0);
            $this->Cell(0, 1, iconv('UTF-8', 'ISO-8859-1', trans('Class Teacher').' : '), 0, 1);

            $this->SetFont('Times', 'B', 10);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $per_col_width = 0.4;
            $periods = 9;
            $day_col_width = $per_col_width*$periods;

            $this->Cell(1, 1.6, 'No', 1, 0, 'C', true);
            $this->Cell(7, 1.6, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('full_name'))), 1, 0, 'C', true);
            $this->Cell(1.5, 1.6, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('gender'))), 1, 0, 'C', true);
            $this->Cell($day_col_width, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('monday'))), 1, 0, 'C', true);
            $this->Cell($day_col_width, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('tuesday'))), 1, 0, 'C', true);
            $this->Cell($day_col_width, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('wednesday'))), 1, 0, 'C', true);
            $this->Cell($day_col_width, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('thursday'))), 1, 0, 'C', true);
            $this->Cell($day_col_width, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('friday'))), 1, 1, 'C', true);

            $this->Cell(9.5, 0.8, '');
            for($i=0; $i<5; $i++){
                for($j=0; $j<$periods; $j++){
                    $this->Cell($per_col_width, 0.8, $j+1, 1, 0, 'C',true);
                }
            }
            $this->Ln();


            $this->SetFont('Times', '', 8);
            $this->SetTextColor(0,0,0);

            $col_height = 0.5;
            foreach ($list as $student) {
                $this->Cell(1, $col_height, iconv('UTF-8', 'ISO-8859-1',$student['no']), 1, 0, 'L');
                $this->Cell(7, $col_height, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['first_name'].' '.$student['last_name'])), 1, 0, 'L');
                $this->Cell(1.5, $col_height, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['gender'])), 1, 0, 'L');
                for($i=0; $i<5; $i++){
                    for($j=0; $j<$periods; $j++){
                        $this->Cell($per_col_width, $col_height, '', 1, 0, 'C');
                    }
                }
                $this->Ln();
            }
        } catch (\Throwable $throwable) {
            return response()->json([
                'error' => true,
                'message' => trans("contact_info_error")
            ]);
        }
    }

    public function printClassMarkSheet($list, ClassSection $classSection, ExamTerm $examTerm){
        /**
         * Printing on Portrait
         */
        try {
            $classSection->class;
            $classSection->section;

            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);
            $title = $classSection->full_name . ' ' .trans('marks_sheet');
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');

            $this->SetFont('Times', 'B', 10);
            $this->SetTextColor(0,0,0);

            $this->Cell($this->GetPageWidth()/3, 1, iconv('UTF-8', 'ISO-8859-1', trans('Exam Term').' : '. $examTerm->name), 0, 0);
            $this->Cell($this->GetPageWidth()/3, 1, iconv('UTF-8', 'ISO-8859-1', trans('subject').' : '), 0, 0);
            $this->Cell(0, 1, iconv('UTF-8', 'ISO-8859-1', trans('teacher').' : '), 0, 1);

            $this->SetFont('Times', 'B', 9);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $cols = count($examTerm->sequence) * 3;
            $col_size = 9/$cols;

            $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
            $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('full_name'))), 1, 0, 'C', true);
            $this->Cell(2, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('matricule'))), 1, 0, 'C', true);
            foreach ($examTerm->sequence as $seq) {
                $this->Cell($col_size, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$seq->name)), 1, 0, 'C', true);
            }
            for ($i=count($examTerm->sequence); $i < $cols; $i++) {
                $this->Cell($col_size, 0.8, '', 1, 0, 'C', true);
            }
            $this->Ln();

            $this->SetFont('Times', '', 8);
            $this->SetTextColor(0,0,0);

            foreach ($list as $student) {
                $this->Cell(1, 0.5, iconv('UTF-8', 'ISO-8859-1',$student['no']), 1, 0, 'C');
                $this->Cell(7, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1',$student['first_name'].' '.$student['last_name'])), 1, 0, 'L');
                $this->Cell(2, 0.5, iconv('UTF-8', 'ISO-8859-1',$student['admission_no']), 1, 0, 'C');
                for ($i=0; $i < $cols; $i++) {
                    $this->Cell($col_size, 0.5, '', 1, 0);
                }
                $this->Ln();
            }

            $this->Ln(0.5);
            $this->SetFont('Times', 'BU', 9);
            $this->Cell($this->GetPageWidth()/2, 1, iconv('UTF-8', 'ISO-8859-1', trans('date').' : '), 0, 0);
            $this->Cell(0, 1, iconv('UTF-8', 'ISO-8859-1', trans('Signature').' : '), 0, 1);
        } catch (\Throwable $throwable) {
            return response()->json([
                'error' => true,
                'message' => trans("contact_info_error")
            ]);
        }
    }


    public function printSexAgeStats(){
        try {
            $class_sections = ClassSection::owner()
                ->with('class.stream', 'section')
                ->whereHas('class', function ($q) {
                    $q->activeMediumOnly();
                })->get();

            $ages = $this->initiateAgeArray();
            $total = $this->initiateAgeArray();

            $title = strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('sex_age_statistics')));
            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);
            $this->Cell(0, 1, $title, 0, 1, 'C');

            $this->SetFont('Times', 'B', 8);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(3, 1.6, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_section'))), 1, 0, 'C', true);

            $total_w = 24.7;
            $w = $total_w/count($ages);
            $x = $this->GetX();
            foreach(array_keys($ages) as $age){
                $txt = (string)$age.' '.trans('years_old');
                if($age==10){
                    $txt = '< 11 '.trans('years_old');
                }else if($age==22){
                    $txt = '> 21 '.trans('years_old') ;
                }
                $this->Cell($w, 0.8, iconv('UTF-8', 'ISO-8859-1', $txt), 1, 0, 'C', true);
                // $this->SetY($y);
            }
            $this->Ln();

            $this->SetX($x);
            foreach ($ages as $_) {
                $this->Cell($w/3, 0.8, 'M', 1, 0, 'C', true);
                $this->Cell($w/3, 0.8, 'F', 1, 0, 'C', true);
                $this->Cell($w/3, 0.8, 'T', 1, 0, 'C', true);
            }
            $this->Ln();

            $this->SetFont('Times', '', 7);
            $this->SetTextColor(0,0,0);

            $today = date("Y-m-d");
            foreach ($class_sections as $class_section) {
                $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $class_section->full_name)), 1, 0, 'L');

                $ages = $this->initiateAgeArray();
                $students = $class_section->student()->whereHas('studentSessions', function ($query) {
                    $query->where('session_year_id', getSettings('session_year')['session_year']);
                })->get();
                foreach ($students as $student) {
                    $gender = strtolower($student->user->gender);

                    if(str_starts_with($gender, 'm')) $gender = 'male';
                    else if (str_starts_with($gender, 'f')) $gender = 'female';
                    else $gender = "error";


                    $age = date_diff(date_create($student->user->dob), date_create($today));
                    if($age->y <=10){
                        $ages[10][$gender] +=1;
                        $total[10][$gender] +=1;
                    }else if($age->y >=22){
                        $ages[22][$gender] +=1;
                        $total[22][$gender] +=1;
                    }else{
                        $ages[$age->y][$gender] +=1;
                        $total[$age->y][$gender] +=1;
                    }
                }

                foreach ($ages as $value) {
                    $this->Cell($w/3, 0.5, $value['male'], 1, 0, 'C');
                    $this->Cell($w/3, 0.5, $value['female'], 1, 0, 'C');
                    $this->Cell($w/3, 0.5, $value['male'] + $value['female'], 1, 0, 'C');
                }
                $this->Ln();
            }

            $check = $this->GetPageHeight() - $this->GetY();
            if($check < 2.5){
                $this->AddPage();
            }else{
                $this->Ln(0.5);
            }

            $this->SetFont('Times', 'B', 6);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(3, 1.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total'))), 1, 0, 'L', true);

            foreach(array_keys($ages) as $age){
                $txt = (string)$age.' '.trans('years_old');
                if($age==10){
                    $txt = '< 11 '.trans('years_old');
                }else if($age==22){
                    $txt = '> 21 '.trans('years_old') ;
                }
                $this->Cell($w, 0.5, iconv('UTF-8', 'ISO-8859-1', $txt), 1, 0, 'C', true);
                // $this->SetY($y);
            }
            $this->Ln();

            $this->SetX($x);
            foreach ($ages as $_) {
                $this->Cell($w/3, 0.5, 'M', 1, 0, 'C', true);
                $this->Cell($w/3, 0.5, 'F', 1, 0, 'C', true);
                $this->Cell($w/3, 0.5, 'T', 1, 0, 'C', true);
            }
            $this->Ln();

            $this->SetX($x);
            foreach ($total as $value) {
                $this->Cell($w/3, 0.5, $value['male'], 1, 0, 'C', true);
                $this->Cell($w/3, 0.5, $value['female'], 1, 0, 'C', true);
                $this->Cell($w/3, 0.5, $value['male'] + $value['female'], 1, 0, 'C', true);
            }
            $this->Ln();
        } catch (\Throwable $throwable) {
            return response()->json([
                'error' => true,
                'message' => trans("contact_info_error")
            ]);
        }
    }

    public function printGroupedSexAgeStats($groupId){
        try {
            $ages = $this->initiateAgeArray();
            $total = $this->initiateAgeArray();

            $group = Group::owner()->where('id', $groupId)->get()->first();
            $groupName = $group->name;

            $title = strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('sex_age_statistics_for_group') . $groupName));
            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);
            $this->Cell(0, 1, $title, 0, 1, 'C');

            $this->SetFont('Times', 'B', 8);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(3, 1.6, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_section'))), 1, 0, 'C', true);

            $total_w = 24.7;
            $w = $total_w/count($ages);
            $x = $this->GetX();
            foreach(array_keys($ages) as $age){
                $txt = (string)$age.' '.trans('years_old');
                if($age==10){
                    $txt = '< 11 '.trans('years_old');
                }else if($age==22){
                    $txt = '> 21 '.trans('years_old') ;
                }
                $this->Cell($w, 0.8, iconv('UTF-8', 'ISO-8859-1', $txt), 1, 0, 'C', true);
                // $this->SetY($y);
            }
            $this->Ln();

            $this->SetX($x);
            foreach ($ages as $_) {
                $this->Cell($w/3, 0.8, 'M', 1, 0, 'C', true);
                $this->Cell($w/3, 0.8, 'F', 1, 0, 'C', true);
                $this->Cell($w/3, 0.8, 'T', 1, 0, 'C', true);
            }
            $this->Ln();

            $this->SetFont('Times', '', 7);
            $this->SetTextColor(0,0,0);

            $today = date("Y-m-d");

            $group = Group::with('classes')->where('id', $groupId)->firstOrFail();
            $classIds = $group->classes->pluck('id')->toArray();

            $class_sections = ClassSection::owner()
                ->with('class.stream', 'section', 'student')
                ->whereHas('class', function ($q) use ($classIds) {
                    $q->activeMediumOnly();
                    $q->whereIn('id', $classIds);
                })->get();

            foreach ($class_sections as $class_section) {
                $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $class_section->full_name)), 1, 0, 'L');

                $ages = $this->initiateAgeArray();
                foreach ($class_section->student as $student) {
                    $gender = strtolower($student->user->gender);
                    if($gender == 'm') $gender = 'male';
                    else if ($gender == 'f') $gender = 'female';

                    $age = date_diff(date_create($student->user->dob), date_create($today));
                    if($age->y <=10){
                        $ages[10][$gender] +=1;
                        $total[10][$gender] +=1;
                    }else if($age->y >=22){
                        $ages[22][$gender] +=1;
                        $total[22][$gender] +=1;
                    }else{
                        $ages[$age->y][$gender] +=1;
                        $total[$age->y][$gender] +=1;
                    }
                }

                foreach ($ages as $value) {
                    $this->Cell($w/3, 0.5, $value['male'], 1, 0, 'C');
                    $this->Cell($w/3, 0.5, $value['female'], 1, 0, 'C');
                    $this->Cell($w/3, 0.5, $value['male'] + $value['female'], 1, 0, 'C');
                }

                $this->Ln();
            }

            $check = $this->GetPageHeight() - $this->GetY();
            if($check < 2.5){
                $this->AddPage();
            }else{
                $this->Ln(0.5);
            }

            // putting the titles of the totals at the bottom

            $this->SetFont('Times', 'B', 6);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(3, 1.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total'))), 1, 0, 'L', true);

            foreach(array_keys($ages) as $age){
                $txt = $age .' '.trans('years_old');
                if($age==10){
                    $txt = '< 11 '.trans('years_old');
                }else if($age==22){
                    $txt = '> 21 '.trans('years_old') ;
                }
                $this->Cell($w, 0.5, iconv('UTF-8', 'ISO-8859-1', $txt), 1, 0, 'C', true);
                // $this->SetY($y);
            }

            $this->Ln();

            $this->SetX($x);
            foreach ($ages as $_) {
                $this->Cell($w/3, 0.5, 'M', 1, 0, 'C', true);
                $this->Cell($w/3, 0.5, 'F', 1, 0, 'C', true);
                $this->Cell($w/3, 0.5, 'T', 1, 0, 'C', true);
            }
            $this->Ln();

            $this->SetX($x);
            foreach ($total as $value) {
                $this->Cell($w/3, 0.5, $value['male'], 1, 0, 'C', true);
                $this->Cell($w/3, 0.5, $value['female'], 1, 0, 'C', true);
                $this->Cell($w/3, 0.5, $value['male'] + $value['female'], 1, 0, 'C', true);
            }
            $this->Ln();
        } catch (\Throwable $throwable) {
            return response()->json([
                'error' => true,
                'message' => trans("contact_info_error")
            ]);
        }
    }

    private function initiateAgeArray() : array{
        $ages = array();
        for($i=10; $i<=22; $i++){
            $ages[$i] = array(
                'male' => 0,
                'female' => 0,
            );
        }

        return $ages;
    }


    public function printSexStats(){
        try {
            $title = strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('sex_statistics')));
            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);
            $this->Cell(0, 1, $title, 0, 1, 'C');

            $this->SetFont('Times', 'B', 8);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_section'))), 1, 0, 'C', true);

            $w = 22.7/9;
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_m'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_f'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_t'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_a_m'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_a_f'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_a_t'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_n_m'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_n_f'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_n_t'))), 1, 1, 'C', true);

            $this->printSexStatsM2($w);
        } catch (\Throwable $throwable) {
            return response()->json([
                'error' => true,
                'message' => trans("contact_info_error")
            ]);
        }
    }

    public function printGroupedSexStats($groupId = null){
        try {
            $title = strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('sex_statistics')));
            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);
            $this->Cell(0, 1, $title, 0, 1, 'C');

            $this->SetFont('Times', 'B', 8);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_name'))), 1, 0, 'C', true);

            $w = 22.7/9;
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_m'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_f'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_t'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_a_m'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_a_f'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_a_t'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_n_m'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_n_f'))), 1, 0, 'C', true);
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('eff_n_t'))), 1, 1, 'C', true);


            $totals = array(
                'N' => array(
                    'male'  => 0,
                    'female' => 0
                ),
                'O'=> array(
                    'male'  => 0,
                    'female' => 0
                )
            );

            $class_names = ClassSchool::with('streams')->owner()->activeMediumOnly()->pluck('name', 'id')->toArray();

            $groupQuery = Group::with('classes')->owner();

            if ($groupId) {
                $groups = $groupQuery->where('id', $groupId)->get();
            } else {
                $groups = $groupQuery->get();
            }

            foreach ($groups as $group) {
                $classGroupIds = $group->classes->pluck('name', 'id')->toArray();

                // calculate the final group results to be displayed at the end of the list.

                $this->SetFillColor(0,0, 0);
                $this->Cell($w * 9 + 4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',$group->name)), 1, 1, 'C', true);

                foreach ($classGroupIds as $classId => $className) {

                    $class_totals = array(
                        'N' => array(
                            'male'  => 0,
                            'female' => 0
                        ),
                        'O'=> array(
                            'male'  => 0,
                            'female' => 0
                        )
                    );

                    $stats = array(
                        'N' => array(
                            'male'  => 0,
                            'female' => 0
                        ),
                        'O'=> array(
                            'male'  => 0,
                            'female' => 0
                        )
                    );

                    $this->SetFont('Times', '', 7);
                    $this->SetTextColor(0,0,0);

                    $this->Cell(4, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $className)), 1, 0, 'L');

                    $class_sections = ClassSection::with('student')->whereHas('class', function ($query) use ($classId) {
                        $query->where('id', $classId);
                    })->get();

                    foreach ($class_sections as $class_section) {

                        foreach ($class_section->student as $student) {

                            $gender = strtolower(trim($student->user->gender));

                            if($gender == 'm') $gender = 'male';
                            else if ($gender == 'f') $gender = 'female';

                            $status = "O";
                            if(isset($student->dynamic_field_values['Status'])){
                                $status = $student->dynamic_field_values['Status'];
                            }
                            $status = $status == 'N'? 'N' : 'O';

                            if($status == 'N'){
                                $stats['N'][$gender] += 1;
                                $class_totals['N'][$gender] +=1;
                                $totals['N'][$gender] +=1;
                            }else{
                                $stats['O'][$gender] +=1;
                                $class_totals['O'][$gender] +=1;
                                $totals['O'][$gender] +=1;
                            }

                        }
                    }

                    $total_m = $stats['N']['male'] + $stats['O']['male'];
                    $total_f = $stats['N']['female'] + $stats['O']['female'];
                    $total = $total_m + $total_f;

                    $this->Cell($w, 0.5, $total_m, 1, 0, 'C');
                    $this->Cell($w, 0.5, $total_f, 1, 0, 'C');
                    $this->Cell($w, 0.5, $total, 1, 0, 'C');
                    $this->Cell($w, 0.5, $stats['O']['male'], 1, 0, 'C');
                    $this->Cell($w, 0.5, $stats['O']['female'], 1, 0, 'C');
                    $this->Cell($w, 0.5, $stats['O']['male']+$stats['O']['female'], 1, 0, 'C');
                    $this->Cell($w, 0.5, $stats['N']['male'], 1, 0, 'C');
                    $this->Cell($w, 0.5, $stats['N']['female'], 1, 0, 'C');
                    $this->Cell($w, 0.5, $stats['N']['male']+$stats['N']['female'], 1, 1, 'C');
                }

                // Last row for the totals

                $this->SetFont('Times', 'B', 8);
                $this->SetFillColor(51, 74, 94);
                $this->SetTextColor(255, 255, 255);
                $this->Cell(4, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total'))), 1, 0, 'C', true);

                $total_m = $totals['N']['male'] + $totals['O']['male'];
                $total_f = $totals['N']['female'] + $totals['O']['female'];
                $total = $total_m + $total_f;

                $this->Cell($w, 0.5, $total_m, 1, 0, 'C', true);
                $this->Cell($w, 0.5, $total_f, 1, 0, 'C', true);
                $this->Cell($w, 0.5, $total, 1, 0, 'C', true);
                $this->Cell($w, 0.5, $totals['O']['male'], 1, 0, 'C', true);
                $this->Cell($w, 0.5, $totals['O']['female'], 1, 0, 'C', true);
                $this->Cell($w, 0.5, $totals['O']['male']+$totals['O']['female'], 1, 0, 'C', true);
                $this->Cell($w, 0.5, $totals['N']['male'], 1, 0, 'C', true);
                $this->Cell($w, 0.5, $totals['N']['female'], 1, 0, 'C', true);
                $this->Cell($w, 0.5, $totals['N']['male']+$totals['N']['female'], 1, 1, 'C', true);
            }
        } catch (\Throwable $throwable) {
            return response()->json([
                'error' => false,
                'message' => trans('contact_info_error')
            ]);
        }
    }

    /**
     * @param ClassSection|null $classSection
     * @return void
     */
    public function classSectionHeader(?ClassSection $classSection): void
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        if ($classSection) {
            $title = $classSection->class->name . ' ' . $classSection->section->name . ' ' . trans('students');
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');
        } else {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('students'))), 0, 1, 'C');
        }

        $this->SetFont('Times', 'B', 10);
    }

    /**
     * @return int
     */
    public function studentListTitles1(): int
    {
        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1.5, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_id'))), 1, 0, 'C', true);
        $this->Cell(12, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(6, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('result'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('status'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0, 0, 0);

        $count = 1;
        return $count;
    }

    /**
     * @param $list
     * @return mixed
     */
    public function maleFemaleStatsHeader($list)
    {
        $this->SetTextColor(0, 0, 0);
        $this->Cell(9, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class size') . ': ' . count($list))), 0, 0, 'L');
        $x_point = $this->GetX();
        $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('new'))), 1, 0, 'C');
        $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('old'))), 1, 0, 'C');
        $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total'))), 1, 1, 'C');
        $this->SetX($x_point);
        $this->SetFont('Times', '', 10);
        $this->Cell(1, 0.5, 'M', 1, 0, 'C');
        $this->Cell(1, 0.5, 'F', 1, 0, 'C');
        $this->Cell(1, 0.5, 'T', 1, 0, 'C');
        $this->Cell(1, 0.5, 'M', 1, 0, 'C');
        $this->Cell(1, 0.5, 'F', 1, 0, 'C');
        $this->Cell(1, 0.5, 'T', 1, 0, 'C');
        $this->Cell(1, 0.5, 'M', 1, 0, 'C');
        $this->Cell(1, 0.5, 'F', 1, 0, 'C');
        $this->Cell(1, 0.5, 'T', 1, 1, 'C');
        return $x_point;
    }

    /**
     * @param array $stats
     * @param $list
     * @return void
     */
    public function oldNewGenderStats(array $stats, $list): void
    {
        $this->Cell(1, 0.5, $stats['N']['male'], 1, 0, 'C');
        $this->Cell(1, 0.5, $stats['N']['female'], 1, 0, 'C');
        $this->Cell(1, 0.5, $stats['N']['male'] + $stats['N']['female'], 1, 0, 'C');
        $this->Cell(1, 0.5, $stats['O']['male'], 1, 0, 'C');
        $this->Cell(1, 0.5, $stats['O']['female'], 1, 0, 'C');
        $this->Cell(1, 0.5, $stats['O']['male'] + $stats['O']['female'], 1, 0, 'C');
        $this->Cell(1, 0.5, $stats['N']['male'] + $stats['O']['male'], 1, 0, 'C');
        $this->Cell(1, 0.5, $stats['N']['female'] + $stats['O']['female'], 1, 0, 'C');
        $this->Cell(1, 0.5, count($list), 1, 1, 'C');
    }

    private function printSexStatsM1($w){
        try {
            $class_sections = ClassSection::owner()
                ->with('class.stream', 'section')
                ->whereHas('class', function ($q) {
                    $q->activeMediumOnly();
                })->get();

            $totals = array(
                'N' => array(
                    'male'  => 0,
                    'female' => 0
                ),
                'O'=> array(
                    'male'  => 0,
                    'female' => 0
                )
            );

            $this->SetFont('Times', '', 7);
            $this->SetTextColor(0,0,0);
            foreach ($class_sections as $class_section) {
                $this->Cell(5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $class_section->full_name)), 1, 0, 'L');
                $stats = array(
                    'N' => array(
                        'male'  => 0,
                        'female' => 0
                    ),
                    'O'=> array(
                        'male'  => 0,
                        'female' => 0
                    )
                );

                foreach ($class_section->student as $student) {
                    $gender = strtolower($student->user->gender);
                    if($gender == 'm') $gender = 'male';
                    else if ($gender == 'f') $gender = 'female';

                    if($student->is_new_admission){
                        $stats['N'][$gender] +=1;
                        $totals['N'][$gender] +=1;
                    }else{
                        $stats['O'][$gender] +=1;
                        $totals['O'][$gender] +=1;
                    }
                }

                $total_m = $stats['N']['male'] + $stats['O']['male'];
                $total_f = $stats['N']['female'] + $stats['O']['female'];
                $total = $total_m + $total_f;

                $this->Cell($w, 0.5, $total_m, 1, 0, 'C');
                $this->Cell($w, 0.5, $total_f, 1, 0, 'C');
                $this->Cell($w, 0.5, $total, 1, 0, 'C');
                $this->Cell($w, 0.5, $stats['O']['male'], 1, 0, 'C');
                $this->Cell($w, 0.5, $stats['O']['female'], 1, 0, 'C');
                $this->Cell($w, 0.5, $stats['O']['male']+$stats['O']['female'], 1, 0, 'C');
                $this->Cell($w, 0.5, $stats['N']['male'], 1, 0, 'C');
                $this->Cell($w, 0.5, $stats['N']['female'], 1, 0, 'C');
                $this->Cell($w, 0.5, $stats['N']['male']+$stats['N']['female'], 1, 1, 'C');
            }

            $this->SetFont('Times', 'B', 8);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total'))), 1, 0, 'C', true);

            $total_m = $totals['N']['male'] + $totals['O']['male'];
            $total_f = $totals['N']['female'] + $totals['O']['female'];
            $total = $total_m + $total_f;

            $this->Cell($w, 0.5, $total_m, 1, 0, 'C', true);
            $this->Cell($w, 0.5, $total_f, 1, 0, 'C', true);
            $this->Cell($w, 0.5, $total, 1, 0, 'C', true);
            $this->Cell($w, 0.5, $totals['O']['male'], 1, 0, 'C', true);
            $this->Cell($w, 0.5, $totals['O']['female'], 1, 0, 'C', true);
            $this->Cell($w, 0.5, $totals['O']['male']+$totals['O']['female'], 1, 0, 'C', true);
            $this->Cell($w, 0.5, $totals['N']['male'], 1, 0, 'C', true);
            $this->Cell($w, 0.5, $totals['N']['female'], 1, 0, 'C', true);
            $this->Cell($w, 0.5, $totals['N']['male']+$totals['N']['female'], 1, 1, 'C', true);
        } catch (\Throwable $throwable) {
            return response()->json([
                'error' =>  false,
                'message' => trans('contact_info_error')
            ]);
        }
    }

    private function printSexStatsM2($w){
        try {
            $classes = ClassSchool::owner()->with('stream')->activeMediumOnly()->get();

            $totals = array(
                'N' => array(
                    'male'  => 0,
                    'female' => 0
                ),
                'O'=> array(
                    'male'  => 0,
                    'female' => 0
                )
            );

            foreach($classes as $class){
                $class_sections = $class->class_section;
                $class_totals = array(
                    'N' => array(
                        'male'  => 0,
                        'female' => 0
                    ),
                    'O'=> array(
                        'male'  => 0,
                        'female' => 0
                    )
                );
                $this->SetFont('Times', '', 7);
                $this->SetTextColor(0,0,0);
                foreach ($class_sections as $class_section) {
                    $class_section->class->stream;
                    $class_section->section;
                    $this->Cell(5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $class_section->full_name)), 1, 0, 'L');
                    $stats = array(
                        'N' => array(
                            'male'  => 0,
                            'female' => 0
                        ),
                        'O'=> array(
                            'male'  => 0,
                            'female' => 0
                        )
                    );

                    $students = $class_section->student()->whereHas('studentSessions', function ($query) {
                        $query->where('session_year_id', getSettings('session_year')['session_year']);
                    })->get();

                    foreach ($students as $student) {
                        $gender = strtolower(trim($student->user->gender));
                        if(str_starts_with($gender, 'm')) $gender = 'male';
                        else if (str_starts_with($gender, 'f')) $gender = 'female';
                        else $gender = 'wrong';

                        $status = "O";
                        if(isset($student->dynamic_field_values['Status'])){
                            $status = $student->dynamic_field_values['Status'];
                        }
                        $status = $status == 'N'? 'N' : 'O';

                        if($status == 'N'){
                            $stats['N'][$gender] +=1;
                            $class_totals['N'][$gender] +=1;
                            $totals['N'][$gender] +=1;
                        }else{
                            $stats['O'][$gender] +=1;
                            $class_totals['O'][$gender] +=1;
                            $totals['O'][$gender] +=1;
                        }
                    }

                    $total_m = $stats['N']['male'] + $stats['O']['male'];
                    $total_f = $stats['N']['female'] + $stats['O']['female'];
                    $total = $total_m + $total_f;

                    $this->Cell($w, 0.5, $total_m, 1, 0, 'C');
                    $this->Cell($w, 0.5, $total_f, 1, 0, 'C');
                    $this->Cell($w, 0.5, $total, 1, 0, 'C');
                    $this->Cell($w, 0.5, $stats['O']['male'], 1, 0, 'C');
                    $this->Cell($w, 0.5, $stats['O']['female'], 1, 0, 'C');
                    $this->Cell($w, 0.5, $stats['O']['male']+$stats['O']['female'], 1, 0, 'C');
                    $this->Cell($w, 0.5, $stats['N']['male'], 1, 0, 'C');
                    $this->Cell($w, 0.5, $stats['N']['female'], 1, 0, 'C');
                    $this->Cell($w, 0.5, $stats['N']['male']+$stats['N']['female'], 1, 1, 'C');
                }

                $this->SetFont('Times', 'B', 8);
                $this->SetFillColor(175, 188, 199);
                // $this->SetTextColor(255, 255, 255);
                $this->Cell(5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $class->name)), 1, 0, 'C', true);

                $total_m = $class_totals['N']['male'] + $class_totals['O']['male'];
                $total_f = $class_totals['N']['female'] + $class_totals['O']['female'];
                $total = $total_m + $total_f;

                $this->Cell($w, 0.5, $total_m, 1, 0, 'C', true);
                $this->Cell($w, 0.5, $total_f, 1, 0, 'C', true);
                $this->Cell($w, 0.5, $total, 1, 0, 'C', true);
                $this->Cell($w, 0.5, $class_totals['O']['male'], 1, 0, 'C', true);
                $this->Cell($w, 0.5, $class_totals['O']['female'], 1, 0, 'C', true);
                $this->Cell($w, 0.5, $class_totals['O']['male']+$class_totals['O']['female'], 1, 0, 'C', true);
                $this->Cell($w, 0.5, $class_totals['N']['male'], 1, 0, 'C', true);
                $this->Cell($w, 0.5, $class_totals['N']['female'], 1, 0, 'C', true);
                $this->Cell($w, 0.5, $class_totals['N']['male']+$class_totals['N']['female'], 1, 1, 'C', true);
            }

            $this->SetFont('Times', 'B', 8);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total'))), 1, 0, 'C', true);

            $total_m = $totals['N']['male'] + $totals['O']['male'];
            $total_f = $totals['N']['female'] + $totals['O']['female'];
            $total = $total_m + $total_f;

            $this->Cell($w, 0.5, $total_m, 1, 0, 'C', true);
            $this->Cell($w, 0.5, $total_f, 1, 0, 'C', true);
            $this->Cell($w, 0.5, $total, 1, 0, 'C', true);
            $this->Cell($w, 0.5, $totals['O']['male'], 1, 0, 'C', true);
            $this->Cell($w, 0.5, $totals['O']['female'], 1, 0, 'C', true);
            $this->Cell($w, 0.5, $totals['O']['male']+$totals['O']['female'], 1, 0, 'C', true);
            $this->Cell($w, 0.5, $totals['N']['male'], 1, 0, 'C', true);
            $this->Cell($w, 0.5, $totals['N']['female'], 1, 0, 'C', true);
            $this->Cell($w, 0.5, $totals['N']['male']+$totals['N']['female'], 1, 1, 'C', true);
        } catch (\Throwable $throwable) {
            return response()->json([
                'error' => false,
                'message' => trans('contact_info_error')
            ]);
        }
    }

    public function printStudentsOfGroup($groupsData){
        try {
            foreach ($groupsData as $name => $list) {
                $title = trans("list_of_all_students_in"). " " . $name;

                $stats = $list['stats'];

                $this->Ln();

                $this->SetFont('Times', 'BU', 12);
                $this->SetTextColor(51, 74, 94);
                $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');

                $this->SetFont('Times', 'B', 10);
                $x_point = $this->maleFemaleStatsHeader($list);
                $this->SetX($x_point);

                $this->oldNewGenderStats($stats, $list);

                $y_point = $this->GetY();
                $this->Ln(1);


                $this->SetFont('Times', 'B', 10);
                $this->SetFillColor(51, 74, 94);
                $this->SetTextColor(255, 255, 255);

                $this->Cell(1.5, 0.8, 'No', 1, 0, 'C', true);
                $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('full_name'))), 1, 0, 'C', true);
                $this->Cell(2, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('gender'))), 1, 0, 'C', true);
                $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 0, 'C', true);
                $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('dob'))), 1, 0, 'C', true);

                $this->SetFont('Times', '', 9);
                $this->SetTextColor(0,0,0);
                $this->Ln();

                $count = 1;
                foreach ($list['items'] as $student) {
                    $this->Cell(1.5, 0.8, $count++, 1, 0, 'C');
                    $this->Cell(7, 0.8, iconv('UTF-8', 'ISO-8859-1',$student['first_name'].' '.$student['last_name']), 1, 0, 'L');
                    $this->Cell(2, 0.8, iconv('UTF-8', 'ISO-8859-1',$student['gender']), 1, 0, 'L');
                    $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1',$student['class_section_name']), 1, 0, 'L');
                    $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1',$student['dob']), 1, 0, 'L');
                    $this->Ln();
                }
            }
        } catch (\Throwable $throwable) {
            return response()->json([
                'error' => false,
                'message' => trans('contact_info_error'),
            ]);
        }
    }

    private function getGender(string $gender): string
    {
        if ($gender) {
            $firstCharacter = trim($gender[0]);

            if ($firstCharacter == 'm') {
                return 'male';
            } else {
                return 'female';
            }
        } else {
            return 'male';
        }
    }

}
