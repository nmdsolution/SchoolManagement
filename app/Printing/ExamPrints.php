<?php

namespace App\Printing;

use App\Helpers\Number;
use App\Models\Exam;
use App\Models\ExamTerm;
use App\Models\ExamReport;
use App\Models\AnnualReport;
use App\Models\ClassSection;
use App\Models\ExamSequence;

class ExamPrints extends PDFBase
{
    public function printSpecificExamList($list, ClassSection|null $classSection){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        if($classSection){
            $title = $classSection->class->name. ' ' . $classSection->section->name . ' ' .trans('Specific Exam').' '.trans('list');
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');
        }else{
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Specific Exam').' '.trans('list'))), 0, 1, 'C');
        }


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_section'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('session_year'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Marks').' '.trans('status'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('publish'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['name'])), 1, 0, 'L');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['class_name'])), 1, 0, 'L');
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['session_year_name'])), 1, 0, 'C');
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', '')), 1, 0, 'C');
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['publish']? trans('yes'): trans('no'))), 1, 1, 'C');
        }
    }

    public function printSequentialExamList($list, ClassSection|null $classSection){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        if($classSection){
            $title = $classSection->class->name. ' ' . $classSection->section->name . ' ' .trans('Sequential Exam').' '.trans('list');
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');
        }else{
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Specific Exam').' '.trans('list'))), 0, 1, 'C');
        }


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(5.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_section'))), 1, 0, 'C', true);
        $this->Cell(5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('session_year'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Term'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Sequence'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Teacher Status'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_status'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(5.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['class_name'])), 1, 0, 'L');
            $this->Cell(5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['session_year_name'])), 1, 0, 'C');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['term_name'])), 1, 0, 'C');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['sequence_name'])), 1, 0, 'C');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['teacher_status']? trans('Active'):trans('Inactive'))), 1, 0, 'C');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['student_status']? trans('Active'): trans('Inactive'))), 1, 1, 'C');
        }
    }

    public function printExamResultList($list, Exam $exam, ClassSection|null $classSection){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $title = $exam->name . ' ' .trans('exam_result');
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');

        $this->SetFont('Times', 'B', 10);
        $this->SetTextColor(0,0,0);
        if($classSection){
            $title = $classSection->class->name. ' ' . $classSection->section->name;
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');
        }

        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(7.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('session_year'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total_marks'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('obtained_marks'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('percentage'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('grade'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(7.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['student_name'])), 1, 0, 'L');
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['session_year_name'])), 1, 0, 'C');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', Number::format($item['total_marks'], 2, null, app()->getLocale()))), 1, 0, 'C');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', Number::format($item['obtained_marks'], 2, null, app()->getLocale()))), 1, 0, 'C');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', Number::format($item['percentage'], 2, null, app()->getLocale()))), 1, 0, 'C');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', Number::format($item['grade'], 2, null, app()->getLocale()))), 1, 1, 'C');
        }
    }

    public function printExamReportList($list, ClassSection $classSection, ExamTerm $term){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);

        $title = $classSection->class->name. ' ' . $classSection->section->name . ' ' .$term->name.' '.trans('Report');
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');

        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(2, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Rank'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_id'))), 1, 0, 'C', true);
        $this->Cell(10, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Avg'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(2, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['rank'])), 1, 0, 'C');
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['student_id'])), 1, 0, 'C');
            $this->Cell(10, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['student_name'])), 1, 0, 'L');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', is_numeric($item['publish']) ? Number::format($item['publish'], 2, null, app()->getLocale()) : $item['publish'])), 1, 1, 'C');
        }
    }

    public function printExamTermReportList($list, ExamTerm $term){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);

        $title = $term->name.' '.trans('Report');
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');

        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(2, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Rank'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_id'))), 1, 0, 'C', true);
        $this->Cell(10, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Avg'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(2, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['rank'])), 1, 0, 'C');
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['student_id'])), 1, 0, 'C');
            $this->Cell(10, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['student_name'])), 1, 0, 'L');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['publish'])), 1, 1, 'C');
        }
    }

    public function printExamSequenceMarks($list, ClassSection|null $class, ExamSequence|null $examSequence){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Sequence Wise Marks'))), 0, 1, 'C');


        $this->SetFont('Times', 'B', 10);
        $this->SetTextColor(0,0,0);
        if($class!=null){
            $class->section;
            $class->class;
            $this->Cell(0, 0.8, (strtoupper(trans('class_name').': '.$class->full_name)), 0, 1, 'C');
        }
        if($examSequence!=null){
            $this->Cell(0, 0.8, (strtoupper(trans('Sequence').': '.$examSequence->name)), 0, 1, 'C');
        }

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1.5, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(9, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total_marks'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('obtained_marks'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Avg'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1.5, 0.8, $count++, 1, 0, 'C');
            $this->Cell(9, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['student_name'])), 1, 0, 'L');
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['total_marks'])), 1, 0, 'C');
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['obtained_marks'])), 1, 0, 'C');
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['avg_marks'])), 1, 1, 'C');
        }
    }

    public function printClassReport(ExamReport $exam_report, array $data){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);

        $title = $exam_report->class_section->full_name.' '. $exam_report->exam_term->name;
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Class Report').' '.$title)), 0, 1, 'C');

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(7, 0.7, "", 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('boys'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('girls'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total'))), 1, 1, 'C', true);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 8);
        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total_students'))), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->male_students, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->female_students, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->total_students, 2, null, app()->getLocale()), 1, 1, 'C');

        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total').' '.trans('present'))), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->male_students, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->female_students, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->total_students, 2, null, app()->getLocale()), 1, 1, 'C');

        $this->ln(1);

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(7, 0.7, "", 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('boys'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('girls'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 1, 'C', true);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', 'B', 8);
        $this->Cell(0, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average'))), 1, 1, 'C');

        $this->SetFont('Times', '', 8);
        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Max Avg'))), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->male_highest_avg, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->female_highest_avg, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format(max($exam_report->male_highest_avg, $exam_report->female_highest_avg), 2, null, app()->getLocale()), 1, 1, 'C');

        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Min Avg'))), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->male_lowest_avg, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->female_lowest_avg, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format(max($exam_report->male_lowest_avg, $exam_report->female_lowest_avg), 2, null, app()->getLocale()), 1, 1, 'C');

        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average').'>=10')), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->male_more_than_ten, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->female_more_than_ten, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format(($exam_report->male_more_than_ten + $exam_report->female_more_than_ten), 2, null, app()->getLocale()), 1, 1, 'C');

        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average'.'<10'))), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->male_less_than_ten, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->female_less_than_ten, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format(($exam_report->male_less_than_ten + $exam_report->female_less_than_ten), 2, null, app()->getLocale()), 1, 1, 'C');

        $this->SetFont('Times', 'B', 8);
        $this->Cell(0, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('attendance').'(%)')), 1, 1, 'C');

        $this->SetFont('Times', '', 8);
        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('present'))), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->total_male_present, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->total_female_present, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->total_male_present+$exam_report->total_female_present, 2, null, app()->getLocale()), 1, 1, 'C');

        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('absent'))), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format(100 - $exam_report->total_male_present, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format(100 - $exam_report->total_female_present, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format(100 - $exam_report->total_male_present - $exam_report->total_female_present, 2, null, app()->getLocale()), 1, 1, 'C');

        $this->Ln(1);

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(0, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('top_students'))), 1, 1, 'C', true);
        $this->Cell(1, 0.7, "#", 1, 0, 'C', true);
        $this->Cell(10, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('full_name'))), 1, 0, 'C', true);
        $this->Cell(5, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('gender'))), 1, 0, 'C', true);
        $this->Cell(3, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average'))), 1, 1, 'C', true);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 8);
        $count = 1;
        foreach ($exam_report->top_student as $student) {
            $this->Cell(1, 0.7, $count++, 1, 0, 'C');
            $this->Cell(10, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', $student->student->user->full_name)), 1, 0, 'L');
            $this->Cell(5, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans($student->student->user->gender))), 1, 0, 'C');
            $this->Cell(3, 0.7, Number::format($student->avg, 2, null, app()->getLocale()), 1, 1, 'C');
        }

        $this->Ln(1);

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(0, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('last_students'))), 1, 1, 'C', true);
        $this->Cell(1, 0.7, "#", 1, 0, 'C', true);
        $this->Cell(10, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('full_name'))), 1, 0, 'C', true);
        $this->Cell(5, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('gender'))), 1, 0, 'C', true);
        $this->Cell(3, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average'))), 1, 1, 'C', true);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 8);
        $count = 1;
        foreach ($exam_report->last_student as $student) {
            $this->Cell(1, 0.7, $count++, 1, 0, 'C');
            $this->Cell(10, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', $student->student->user->full_name)), 1, 0, 'L');
            $this->Cell(5, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans($student->student->user->gender))), 1, 0, 'C');
            $this->Cell(3, 0.7, Number::format($student->avg, 2, null, app()->getLocale()), 1, 1, 'C');
        }

        $this->Ln(1);

        $this->SetFont('Times', 'B', 8);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);
        
        $this->Cell(0, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('students'))), 1, 1, 'C', true);
        
        $this->SetFont('Times', 'B', 7);
        $this->Cell(0.4, 1.4, "#", 1, 0, 'C', true); // Numéro
        $this->Cell(6.5, 1.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true); // Augmenté davantage
        $this->SetFont('Times', 'B', 5);
        $this->Cell(0.6, 1.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('gender'))), 1, 0, 'C', true); // Réduit à 0.6 car un seul caractère
        $this->SetFont('Times', 'B', 7);
        $this->Cell(5.6, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Result'))), 1, 0, 'C', true);
        $this->Cell(2.5, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('discipline'))), 1, 0, 'C', true);
        $this->Cell(3.4, 1.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "OBS")), 1, 1, 'C', true); // Augmenté encore plus
        
        $this->SetXY(8.5, $this->GetY()-0.7); // Ajuster selon la nouvelle somme des largeurs précédentes
        $this->SetFont('Times', 'B', 5);
        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Avg'))), 1, 0, 'C', true);
        $this->SetFont('Times', 'B', 7);
        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Rank'))), 1, 0, 'C', true);
        $this->Cell(0.7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "HR")), 1, 0, 'C', true);
        $this->Cell(0.7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "CR")), 1, 0, 'C', true);
        $this->Cell(0.7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "ENC")), 1, 0, 'C', true);
        $this->Cell(0.7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "WA")), 1, 0, 'C', true);
        $this->Cell(0.8, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "BL")), 1, 0, 'C', true);
        
        $this->Cell(0.7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "TA")), 1, 0, 'C', true);
        $this->Cell(0.6, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "JA")), 1, 0, 'C', true);
        $this->Cell(0.6, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "WA")), 1, 0, 'C', true);
        $this->Cell(0.6, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "BL")), 1, 0, 'C', true);
        $this->Ln();
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 7);

        $exam_term_id = $exam_report->exam_term_id;
        $count = 1;
        foreach($exam_report->exam_report_class_detail as $class_list){
            $total_absences = 0;
            $justified_absences = 0;

            $attendance = $class_list->student->student_attendance
                ->filter(function ($item) use($exam_term_id){
                    return $item->exam_term_id == $exam_term_id;
                })->first();
            if($attendance){
                $total_absences = $attendance->total_absences;
                $justified_absences = $attendance->justified_absences;
            }

            $honor_roll = "";
            if ($class_list->avg >= $data['report_honor_roll']
                && $total_absences < $data['report_honor_roll_absences'])
                $honor_roll = 'yes';

            $congrats = "";
            if ($class_list->avg >= $data['congratulations_min']
                && $class_list->avg <= $data['congratulations_max'])
                $congrats = 'yes';

            $encorage = "";
            if ($class_list->avg >= $data['encouragement_min']
                && $class_list->avg < $data['encouragement_max'])
                $encorage = 'yes';

            $avg_warn = "";
            if ($class_list->avg >= $data['average_warning_min']
                && $class_list->avg < $data['average_warning_max'])
                $avg_warn = trans('yes');

            $avg_blame = "";
            if ($class_list->avg >= $data['average_blame_min']
                && $class_list->avg < $data['average_blame_max'])
                $avg_blame = 'yes';

            $rep_warn = "";
            if ($total_absences >= $data['report_warning_min']
                && $total_absences < $data['report_warning_max'])
                $rep_warn = 'yes';

            $rep_blame = "";
            if ($total_absences >= $data['report_blame_min']
                && $total_absences < $data['report_blame_max'])
                $rep_blame = 'yes';

                $this->Cell(0.4, 0.5, $count++, 1, 0, 'C');
    $this->Cell(6.5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $class_list->student->user->full_name)), 1, 0, 'L');
    $this->Cell(0.6, 0.5, strtoupper($this->short_gender($class_list->student->user->gender)), 1, 0, 'C');

    $this->Cell(1, 0.5, Number::format($class_list->avg, 2, null, app()->getLocale()), 1, 0, "L");
    $this->Cell(1, 0.5, $class_list->rank, 1, 0, "L");
    $this->Cell(0.7, 0.5, strtoupper($this->short_yes_no($honor_roll, true)), 1, 0, 'C');
    $this->Cell(0.7, 0.5, strtoupper($this->short_yes_no($congrats, true)), 1, 0, 'C');
    $this->Cell(0.7, 0.5, strtoupper($this->short_yes_no($encorage, true)), 1, 0, 'C');
    $this->Cell(0.7, 0.5, strtoupper($this->short_yes_no($avg_warn, true)), 1, 0, 'C');
    $this->Cell(0.8, 0.5, strtoupper($this->short_yes_no($avg_blame, true)), 1, 0, 'C');
    $this->Cell(0.7, 0.5, $total_absences > 0 ? $total_absences : '', 1, 0, "L");
    $this->Cell(0.6, 0.5, $justified_absences > 0 ? $justified_absences : '', 1, 0, "L");
    $this->Cell(0.6, 0.5, strtoupper($this->short_yes_no($rep_warn, true)), 1, 0, 'C');
    $this->Cell(0.6, 0.5, strtoupper($this->short_yes_no($rep_blame, true)), 1, 0, 'C');

    $this->Cell(3.4, 0.5, "", 1, 1, "L");
        }

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Ln(2);
        $this->Cell(3.8, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_delegate'))), 1, 0, 'C', true);
        $this->Cell(3.8, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_teacher'))), 1, 0, 'C', true);
        $this->Cell(3.8, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_counselor'))), 1, 0, 'C', true);
        $this->Cell(3.8, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('discipline_master'))), 1, 0, 'C', true);
        $this->Cell(3.8, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('council_president'))), 1, 1, 'C', true);

        $this->Cell(3.8, 2, "", 1, 0, 'C');
        $this->Cell(3.8, 2, "", 1, 0, 'C');
        $this->Cell(3.8, 2, "", 1, 0, 'C');
        $this->Cell(3.8, 2, "", 1, 0, 'C');
        $this->Cell(3.8, 2, "", 1, 1, 'C');

        $this->Ln(1.5);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', 'I', 6);
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- HR: ".trans('Honor Roll'))), 0, 0,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- CR: ".trans('Congratulations (FEL)'))), 0, 0,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- ENC: ".trans('Encouragement (ENR)'))), 0, 1,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- WA: ".trans('warning'))), 0, 0,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- BL: ".trans('Blame'))), 0, 0,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- TA: ".trans('total_absence'))), 0, 1,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- JA: ".trans('justified_absence'))), 0, 0,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- OBS: ".trans('observation'))), 0, 1,'L');
    }


    public function printAnnualClassReport(AnnualReport $exam_report, array $data){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);

        $title = trans('annual_class_report') .' '. $exam_report->class_section->full_name;
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(7, 0.7, "", 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('boys'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('girls'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total'))), 1, 1, 'C', true);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 8);
        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total_students'))), 1, 0, 'C');
        $this->Cell(4, 0.7, $exam_report->male_students, 1, 0, 'C');
        $this->Cell(4, 0.7, $exam_report->female_students, 1, 0, 'C');
        $this->Cell(4, 0.7, $exam_report->total_students, 1, 1, 'C');

        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total').' '.trans('present'))), 1, 0, 'C');
        $this->Cell(4, 0.7, $exam_report->male_students, 1, 0, 'C');
        $this->Cell(4, 0.7, $exam_report->female_students, 1, 0, 'C');
        $this->Cell(4, 0.7, $exam_report->total_students, 1, 1, 'C');

        $this->ln(1);

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(7, 0.7, "", 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('boys'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('girls'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 1, 'C', true);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', 'B', 8);
        $this->Cell(0, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average'))), 1, 1, 'C');

        $this->SetFont('Times', '', 8);
        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Max Avg'))), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->male_highest_avg, 2, null, app()->getLocale()), 2, null, app()->getLocale(), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->female_highest_avg, 2, null, app()->getLocale()), 2, null, app()->getLocale(), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format(max($exam_report->male_highest_avg, $exam_report->female_highest_avg), 2, null, app()->getLocale(), 2, null, app()->getLocale()), 1, 1, 'C');

        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Min Avg'))), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->male_lowest_avg, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->female_lowest_avg, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format(max($exam_report->male_lowest_avg, $exam_report->female_lowest_avg), 2, null, app()->getLocale()), 1, 1, 'C');

        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average').'>=10')), 1, 0, 'C');
        $this->Cell(4, 0.7, $exam_report->male_more_than_ten, 1, 0, 'C');
        $this->Cell(4, 0.7, $exam_report->female_more_than_ten, 1, 0, 'C');
        $this->Cell(4, 0.7, ($exam_report->male_more_than_ten + $exam_report->female_more_than_ten), 1, 1, 'C');

        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average'.'<10'))), 1, 0, 'C');
        $this->Cell(4, 0.7, $exam_report->male_less_than_ten, 1, 0, 'C');
        $this->Cell(4, 0.7, $exam_report->female_less_than_ten, 1, 0, 'C');
        $this->Cell(4, 0.7, ($exam_report->male_less_than_ten + $exam_report->female_less_than_ten), 1, 1, 'C');

        $this->SetFont('Times', 'B', 8);
        $this->Cell(0, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('attendance').'(%)')), 1, 1, 'C');

        $this->SetFont('Times', '', 8);
        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('present'))), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->total_male_present, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->total_female_present, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format($exam_report->total_male_present+$exam_report->total_female_present, 2, null, app()->getLocale()), 1, 1, 'C');

        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('absent'))), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format(100 - $exam_report->total_male_present, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format(100 - $exam_report->total_female_present, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(4, 0.7, Number::format(100 - $exam_report->total_male_present - $exam_report->total_female_present, 2, null, app()->getLocale()), 1, 1, 'C');

        $this->Ln(1);

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(0, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('top_students'))), 1, 1, 'C', true);
        $this->Cell(1, 0.7, "#", 1, 0, 'C', true);
        $this->Cell(10, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('full_name'))), 1, 0, 'C', true);
        $this->Cell(5, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('gender'))), 1, 0, 'C', true);
        $this->Cell(3, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average'))), 1, 1, 'C', true);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 8);
        $count = 1;
        foreach ($exam_report->top_student as $student) {
            $this->Cell(1, 0.7, $count++, 1, 0, 'C');
            $this->Cell(10, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', $student->student->user->full_name)), 1, 0, 'L');
            $this->Cell(5, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans($student->student->user->gender))), 1, 0, 'C');
            $this->Cell(3, 0.7, Number::format($student->avg, 2, null, app()->getLocale()), 1, 1, 'C');
        }

        $this->Ln(1);

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(0, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('last_students'))), 1, 1, 'C', true);
        $this->Cell(1, 0.7, "#", 1, 0, 'C', true);
        $this->Cell(10, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('full_name'))), 1, 0, 'C', true);
        $this->Cell(5, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('gender'))), 1, 0, 'C', true);
        $this->Cell(3, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average'))), 1, 1, 'C', true);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 8);
        $count = 1;
        foreach ($exam_report->last_student as $student) {
            $this->Cell(1, 0.7, $count++, 1, 0, 'C');
            $this->Cell(10, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', $student->student->user->full_name)), 1, 0, 'L');
            $this->Cell(5, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans($student->student->user->gender))), 1, 0, 'C');
            $this->Cell(3, 0.7, Number::format($student->avg, 2, null, app()->getLocale()), 1, 1, 'C');
        }

        $this->Ln(1);

        $this->SetFont('Times', 'B', 8);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(0, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('students'))), 1, 1, 'C', true);

        $this->SetFont('Times', 'B', 7);
        $this->Cell(0.5, 1.4, "#", 1, 0, 'C', true);
        $this->Cell(4, 1.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true);
        $this->Cell(2, 1.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('gender'))), 1, 0, 'C', true);
        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Result'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('discipline'))), 1, 0, 'C', true);
        $this->Cell(1.5, 1.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "OBS")), 1, 1, 'C', true);

        $this->SetXY(7.5, $this->GetY()-0.7);
        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Avg'))), 1, 0, 'C', true);
        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Rank'))), 1, 0, 'C', true);
        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "HR")), 1, 0, 'C', true);
        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "CR")), 1, 0, 'C', true);
        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "ENC")), 1, 0, 'C', true);
        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "WA")), 1, 0, 'C', true);
        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "BL")), 1, 0, 'C', true);

        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "TA")), 1, 0, 'C', true);
        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "JA")), 1, 0, 'C', true);
        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "WA")), 1, 0, 'C', true);
        $this->Cell(1, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', "BL")), 1, 0, 'C', true);
        $this->Ln();

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 7);

        $exam_term_id = ExamTerm::where('center_id', get_center_id())->currentSessionYear()->currentMedium()->pluck('id')->toArray();
        $count = 1;
        foreach($exam_report->annual_report_class_detail as $class_list){
            $total_absences = 0;
            $justified_absences = 0;

            $attendances = $class_list->student->student_attendance
                ->filter(function ($item) use($exam_term_id){
                    return in_array($item->exam_term_id, $exam_term_id);
                });
            foreach($attendances as $attendance){
                $total_absences += $attendance->total_absences;
                $justified_absences += $attendance->justified_absences;
            }

            $honor_roll = "";
            if ($class_list->avg >= $data['report_honor_roll']
                && $total_absences < $data['report_honor_roll_absences'])
                $honor_roll = trans('yes');

            $congrats = "";
            if ($class_list->avg >= $data['congratulations_min']
                && $class_list->avg <= $data['congratulations_max'])
                $congrats = trans('yes');

            $encorage = "";
            if ($class_list->avg >= $data['encouragement_min']
                && $class_list->avg < $data['encouragement_max'])
                $encorage = trans('yes');

            $avg_warn = "";
            if ($class_list->avg >= $data['average_warning_min']
                && $class_list->avg < $data['average_warning_max'])
                $avg_warn = trans('yes');

            $avg_blame = "";
            if ($class_list->avg >= $data['average_blame_min']
                && $class_list->avg < $data['average_blame_max'])
                $avg_blame = trans('yes');

            $rep_warn = "";
            if ($total_absences >= $data['report_warning_min']
                && $total_absences < $data['report_warning_max'])
                $rep_warn = trans('yes');

            $rep_blame = "";
            if ($total_absences >= $data['report_blame_min']
                && $total_absences < $data['report_blame_max'])
                $rep_blame = trans('yes');

            $this->Cell(0.5, 0.5, $count++, 1, 0, 'C');
            $this->Cell(4, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $class_list->student->user->full_name)), 1, 0, 'L');
            $this->Cell(2, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans($class_list->student->user->gender))), 1, 0, 'C');

            $this->Cell(1, 0.5,Number::format($class_list->avg, 2, null, app()->getLocale()), 1, 0, "L");
            $this->Cell(1, 0.5,$class_list->rank, 1, 0, "L");
            $this->Cell(1, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $honor_roll)), 1, 0, 'C');
            $this->Cell(1, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $congrats)), 1, 0, 'C');
            $this->Cell(1, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $encorage)), 1, 0, 'C');
            $this->Cell(1, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $avg_warn)), 1, 0, 'C');
            $this->Cell(1, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $avg_blame)), 1, 0, 'C');
            $this->Cell(1, 0.5,$total_absences > 0 ? $total_absences : '', 1, 0, "L");
            $this->Cell(1, 0.5,$justified_absences > 0 ? $justified_absences : '', 1, 0, "L");
            $this->Cell(1, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $rep_warn)), 1, 0, 'C');
            $this->Cell(1, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $rep_blame)), 1, 0, 'C');

            $this->Cell(1.5, 0.5,"", 1, 1, "L");
        }

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Ln(2);
        $this->Cell(3.8, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_delegate'))), 1, 0, 'C', true);
        $this->Cell(3.8, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_teacher'))), 1, 0, 'C', true);
        $this->Cell(3.8, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_counselor'))), 1, 0, 'C', true);
        $this->Cell(3.8, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('discipline_master'))), 1, 0, 'C', true);
        $this->Cell(3.8, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('council_president'))), 1, 1, 'C', true);

        $this->Cell(3.8, 2, "", 1, 0, 'C');
        $this->Cell(3.8, 2, "", 1, 0, 'C');
        $this->Cell(3.8, 2, "", 1, 0, 'C');
        $this->Cell(3.8, 2, "", 1, 0, 'C');
        $this->Cell(3.8, 2, "", 1, 1, 'C');

        $this->Ln(1.5);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', 'I', 6);
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- HR: ".trans('Honor Roll'))), 0, 0,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- CR: ".trans('Congratulations (FEL)'))), 0, 0,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- ENC: ".trans('Encouragement (ENR)'))), 0, 1,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- WA: ".trans('warning'))), 0, 0,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- BL: ".trans('Blame'))), 0, 0,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- TA: ".trans('total_absence'))), 0, 1,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- JA: ".trans('justified_absence'))), 0, 0,'L');
        $this->Cell(6, 0.4, strtoupper(iconv('UTF-8', 'ISO-8859-1', "- OBS: ".trans('observation'))), 0, 1,'L');
    }

    public function printBestStudentList($list, $className, $gender = null){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $title = trans("Best Student List");

        $this->SetFont('Times', 'B', 10);
        $this->SetTextColor(0,0,0);
        if($className){
            $title = $title. ' For ' . $className;
        }

        if ($gender) {
            $title = $title . " That are " . trans($gender);
        }

        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');

        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(10, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 0, 'C', true);
        $this->Cell(2, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('gender'))), 1, 0, 'C', true);
        $this->Cell(2, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('average'))), 1, 0, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0,0,0);

        $this->Ln(0.8);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(10, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['name'])), 1, 0, 'C');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['class'])), 1, 0, 'C');
            $this->Cell(2, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['gender'])), 1, 0, 'C');
            $this->Cell(2, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['average'])), 1, 0, 'C');
            $this->Ln(0.8);
        }
    }


    public function printBestStudentGroupList($name, $best_student, $best_boys = [], $best_girls = []){
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);

        $title = trans('annual_class_best_report') .' '. $name;
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(2, 0.7, "No", 1, 0, 'C', true);
        $this->Cell(2, 0.7, trans('gender'), 1, 0, 'C', true);
        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average'))), 1, 1, 'C', true);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 8);
        foreach($best_student as $index => $item) {
            $this->Cell(2, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', ++$index)), 1, 0, 'C');
            $this->Cell(2, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans($item->student->user->gender))), 1, 0, 'C');
            $this->Cell(7, 0.7, $item->student->full_name, 1, 0, 'C');
            $this->Cell(4, 0.7, $item->student->class_name, 1, 0, 'C');
            $this->Cell(4, 0.7, Number::format($item->avg, 2, null, app()->getLocale()), 1, 1, 'C');
        }

        $this->ln(1);

        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);

        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans("best_boy_students"))), 0, 1, 'C');

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(2, 0.7, "No", 1, 0, 'C', true);
        $this->Cell(2, 0.7, trans('gender'), 1, 0, 'C', true);
        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average'))), 1, 1, 'C', true);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 8);
        foreach($best_boys as $index => $item) {
            $this->Cell(2, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', ++$index)), 1, 0, 'C');
            $this->Cell(2, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans($item->student->user->gender))), 1, 0, 'C');
            $this->Cell(7, 0.7, $item->student->full_name, 1, 0, 'C');
            $this->Cell(4, 0.7, $item->student->class_name, 1, 0, 'C');
            $this->Cell(4, 0.7, Number::format($item->avg, 2, null, app()->getLocale()), 1, 1, 'C');
        }

        $this->ln(1);

//        Best Girls

        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);

        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('best_girl_students'))), 0, 1, 'C');

        $this->SetFont('Times', 'B', 9);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(2, 0.7, "No", 1, 0, 'C', true);
        $this->Cell(2, 0.7, trans('gender'), 1, 0, 'C', true);
        $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 0, 'C', true);
        $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average'))), 1, 1, 'C', true);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Times', '', 8);
        foreach($best_girls as $index => $item) {
            $this->Cell(2, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', ++$index)), 1, 0, 'C');
            $this->Cell(2, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans($item->student->user->gender))), 1, 0, 'C');
            $this->Cell(7, 0.7, $item->student->full_name, 1, 0, 'C');
            $this->Cell(4, 0.7, $item->student->class_name, 1, 0, 'C');
            $this->Cell(4, 0.7, Number::format($item->avg, 2, null, app()->getLocale()), 1, 1, 'C');
        }
    }

    public function printBestInSubjectList($group_name, $data) {

        foreach ($data as $name => $result) {
            $this->SetFont('Times', 'BU', 12);
            $this->SetTextColor(51, 74, 94);

            $title = trans('annual_class_best_report') . $name;
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', $title)), 0, 1, 'C');

            $this->SetFont('Times', 'B', 9);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(2, 0.7, "No", 1, 0, 'C', true);
            $this->Cell(2, 0.7, trans('gender'), 1, 0, 'C', true);
            $this->Cell(7, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
            $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class'))), 1, 0, 'C', true);
            $this->Cell(4, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Average'))), 1, 1, 'C', true);

            $this->SetTextColor(0, 0, 0);
            $this->SetFont('Times', '', 8);
            foreach($result as $index => $item) {
                $this->Cell(2, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', ++$index)), 1, 0, 'C');
                $this->Cell(2, 0.7, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans($item->student->user->gender))), 1, 0, 'C');
                $this->Cell(7, 0.7, $item->student->full_name, 1, 0, 'C');
                $this->Cell(4, 0.7, $item->student->class_name, 1, 0, 'C');
                $this->Cell(4, 0.7, Number::format($item->subject_avg, 2, null, app()->getLocale()), 1, 1, 'C');
            }

            $this->ln(1);
        }
    }
}

