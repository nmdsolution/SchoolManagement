<?php

namespace App\Printing;

use App\Helpers\Number;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ExamReport;
use App\Models\ClassSchool;
use App\Models\AnnualReport;
use App\Models\ClassSection;
use App\Models\ExamSequence;
use App\Models\StudentSessions;
use App\Models\AnnualClassDetails;
use App\Models\AnnualSubjectReport;
use App\Models\ExamReportClassDetails;
use App\Models\ExamReportStudentSubject;
use App\Models\ExamReportStudentSequence;

class AcademicPrints extends PDFBase
{
    public function printSessionsList($list)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('session_years'))), 0, 1, 'C');


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(9, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Start Date'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('End Date'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Active'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 10);
        $this->SetTextColor(0, 0, 0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(9, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['name'])), 1, 0, 'L');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['start_date']), 1, 0, 'C');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['end_date']), 1, 0, 'C');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['default'] == 1 ? trans('yes') : trans('no')), 1, 1, 'C');
        }
    }

    public function printSubjectList($list)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        if (config('app.locale') == 'en') {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('subject') . ' ' . trans('list'))), 0, 1, 'C');
        } else {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('list') . ' ' . trans('subject'))), 0, 1, 'C');
        }


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('code'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('type'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('bg_color'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0, 0, 0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['name'])), 1, 0, 'L');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['code']), 1, 0, 'C');
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['type']), 1, 0, 'C');

            // TODO: Parse colour and use it as cell fill. 
            // $rgb = hex_to_rgb($item['bg_color']);
            // $this->SetFillColor();
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['bg_color']), 1, 1, 'C', true);
        }
    }

    public function printClassesList($list)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        if (config('app.locale') == 'en') {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('classes') . ' ' . trans('list'))), 0, 1, 'C');
        } else {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('list') . ' ' . trans('classes'))), 0, 1, 'C');
        }


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(12, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
        $this->Cell(6, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('sections'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0, 0, 0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(12, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $this->to_iso_8859_1($item['name']))), 1, 0, 'L');
            $this->Cell(6, 0.8, iconv('UTF-8', 'ISO-8859-1', $this->to_iso_8859_1(implode(' , ', $item['section_names']->toArray()))), 1, 1, 'C');
        }
    }

    public function printClassGroupsList($list)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        if (config('app.locale') == 'en') {
            $this->Cell(0, 1, strtoupper($this->to_iso_8859_1(trans('Class Group') . ' ' . trans('list'))), 0, 1, 'C');
        } else {
            $this->Cell(0, 1, strtoupper($this->to_iso_8859_1(trans('list') . ' ' . trans('Class Group'))), 0, 1, 'C');
        }

        $count1 = 1;
        foreach ($list as $group) {
            $this->SetFont('Times', 'BU', 11);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(0, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', ($count1++) . '. ' . $group['name'])), 0, 1, 'L');

            $this->SetFont('Times', 'B', 10);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
            $this->Cell(12, 0.8, strtoupper($this->to_iso_8859_1(trans('name'))), 1, 0, 'C', true);
            $this->Cell(6, 0.8, strtoupper($this->to_iso_8859_1(trans('sections'))), 1, 1, 'C', true);  

            $this->SetFont('Times', '', 9);
            $this->SetTextColor(0, 0, 0);

            $count = 1;
            foreach ($group['classes'] as $item) {
                $sections = $item->sections->pluck('name');
                $this->Cell(1, 0.8, $count++, 1, 0, 'C');
                $this->Cell(12, 0.8, strtoupper($this->to_iso_8859_1($item->name)), 1, 0, 'L');
                $this->Cell(6, 0.8, $this->to_iso_8859_1(implode(' , ', $sections->toArray())), 1, 1, 'C');
            }

            $this->Ln(1.5);
        }
    }

    public function printClassSubjectsList($list)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        if (config('app.locale') == 'en') {
            $this->Cell(0, 1, strtoupper($this->to_iso_8859_1(trans('Class Group') . ' ' . trans('list'))), 0, 1, 'C');
        } else {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('list') . ' ' . trans('Class Group'))), 0, 1, 'C');
        }

        $count1 = 1;
        foreach ($list as $class) {
            $this->SetFont('Times', 'BU', 11);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(0, 0.8, (strtoupper(iconv('UTF-8', 'ISO-8859-1', $count1++) . '. ' . $class['name'] . ' ' . implode(',', $class['section_names']->toArray()))), 0, 1, 'L');

            $this->SetFont('Times', 'BI', 10);
            $this->Cell(0, 0.8, (strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('core_subject')))), 0, 1, 'L');


            $this->SetFont('Times', 'B', 10);
            $this->SetFillColor(51, 74, 94);
            $this->SetTextColor(255, 255, 255);

            $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
            $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('code'))), 1, 0, 'C', true);
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Coef'))), 1, 0, 'C', true);
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('type'))), 1, 1, 'C', true);

            $this->SetFont('Times', '', 9);
            $this->SetTextColor(0, 0, 0);

            $count = 1;
            foreach ($class['core_subjects'] as $item) {
                $this->Cell(1, 0.8, $count++, 1, 0, 'C');
                $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item->subject->name)), 1, 0, 'L');
                $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item->subject->code), 1, 0, 'C');
                $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', Number::format($item->weightage, 2, null, app()->getLocale())), 1, 0, 'C');
                $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item->subject->type), 1, 1, 'C');
            }

            if (count($class['elective_subject_groups']) < 1) {
                $this->Ln(1);
                continue;
            }

            $this->SetFont('Times', 'BI', 10);
            $this->Cell(0, 0.8, (strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Elective Subject Group')))), 0, 1, 'L');

            $count2 = 1;
            foreach ($class['elective_subject_groups'] as $group) {
                $this->SetFont('Times', 'BI', 10);
                $this->Cell(0, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('subject_group') . ' ' . ($count2++))), 0, 1, 'L');


                $this->SetFont('Times', 'B', 10);
                $this->SetFillColor(51, 74, 94);
                $this->SetTextColor(255, 255, 255);

                $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
                $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('name'))), 1, 0, 'C', true);
                $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('code'))), 1, 0, 'C', true);
                $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Coef'))), 1, 0, 'C', true);
                $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('type'))), 1, 1, 'C', true);

                $this->SetFont('Times', '', 9);
                $this->SetTextColor(0, 0, 0);

                $count = 1;
                foreach ($group->electiveSubjects as $item) {
                    $this->Cell(1, 0.8, $count++, 1, 0, 'C');
                    $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item->subject->name)), 1, 0, 'L');
                    $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item->subject->code), 1, 0, 'C');
                    $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', Number::format($item->weightage, 2, null, app()->getLocale())), 1, 0, 'C');
                    $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item->subject->type), 1, 1, 'C');
                }
            }

            $this->Ln(1);
        }
    }

    public function printClassTeacherList($list)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        if (config('app.locale') == 'en') {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('assign_class_teacher') . ' ' . trans('list'))), 0, 1, 'C');
        } else {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('list') . ' ' . trans('assign_class_teacher'))), 0, 1, 'C');
        }


        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_name'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('section'))), 1, 0, 'C', true);
        $this->Cell(8, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('teacher'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0, 0, 0);

        $count = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count++, 1, 0, 'C');
            $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['class'])), 1, 0, 'L');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['section']), 1, 0, 'C');
            $this->Cell(8, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['teacher']), 1, 1, 'L');
        }
    }

    public function printSubjectTeacherList($classes)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        if (config('app.locale') == 'en') {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('subject') . ' ' . trans('teacher') . ' ' . trans('list'))), 0, 1, 'C');
        } else {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('list') . ' ' . trans('subject') . ' ' . trans('teacher'))), 0, 1, 'C');
        }

        $count = 1;
        foreach ($classes as $class) {
            $this->SetTextColor(0, 0, 0);
            $this->SetFont('Times', 'BU', 11);
            $this->Cell(0, 0.8, (strtoupper(iconv('UTF-8', 'ISO-8859-1', $count++) . '. ' . $class->name)), 0, 1, 'L');

            foreach ($class->class_section as $section) {
                $this->SetFont('Times', 'BU', 10);
                $this->Cell(0, 0.8, (strtoupper('> ' . $class->name . ' ' . $section->section->name)), 0, 1, 'L');

                $this->SetFont('Times', 'B', 10);
                $this->SetFillColor(51, 74, 94);
                $this->SetTextColor(255, 255, 255);

                $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
                $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('subject'))), 1, 0, 'C', true);
                $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('subject_code'))), 1, 0, 'C', true);
                $this->Cell(8, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('teacher'))), 1, 1, 'C', true);

                $this->SetFont('Times', '', 9);
                $this->SetTextColor(0, 0, 0);

                $count1 = 1;
                foreach ($section->subject_teachers as $item) {
                    $teacher = $item->teacher->user;
                    $this->Cell(1, 0.8, $count1++, 1, 0, 'C');
                    $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item->subject->name)), 1, 0, 'L');
                    $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $item->subject->code), 1, 0, 'C');
                    $this->Cell(8, 0.8, iconv('UTF-8', 'ISO-8859-1', $teacher->first_name . ' ' . $teacher->last_name), 1, 1, 'L');
                }
            }

            $this->Ln(1);
        }
    }

    public function printSpecificSubjectTeacherList($list, ClassSection|null $class, Teacher|null $teacher, Subject|null $subject)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        if (config('app.locale') == 'en') {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('subject') . ' ' . trans('teacher') . ' ' . trans('list'))), 0, 1, 'C');
        } else {
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('list') . ' ' . trans('subject') . ' ' . trans('teacher'))), 0, 1, 'C');
        }


        $this->SetFont('Times', 'B', 10);
        $this->SetTextColor(0, 0, 0);
        if ($class != null) {
            $class->section;
            $class->class;
            $this->Cell(0, 0.8, (strtoupper($this->to_iso_8859_1(trans('class_name') . ': ' . $class->full_name))), 0, 1, 'C');
        }
        if ($teacher != null) {
            $this->Cell(0, 0.8, (strtoupper($this->to_iso_8859_1(trans('teacher') . ': ' . $teacher->user->full_name))), 0, 1, 'C');
        }
        if ($subject != null) {
            $this->Cell(0, 0.8, (strtoupper($this->to_iso_8859_1(trans('subject') . ': ' . $subject->name))), 0, 1, 'C');
        }

        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class_section'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('subject'))), 1, 0, 'C', true);
        $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('teacher'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0, 0, 0);

        $count1 = 1;
        foreach ($list as $item) {
            $this->Cell(1, 0.8, $count1++, 1, 0, 'C');
            $this->Cell(7, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $item['class_section_name'])), 1, 0, 'L');
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['subject_name']), 1, 0, 'C');
            $this->Cell(7, 0.8, iconv('UTF-8', 'ISO-8859-1', $item['teacher_name']), 1, 1, 'L');
        }
    }

    public function printAttendanceReportList($list, ClassSection|null $class)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('attendance') . ' ' . trans('Report') . ' ' . trans('list'))), 0, 1, 'C');

        $this->SetFont('Times', 'B', 10);
        $this->SetTextColor(0, 0, 0);
        if ($class != null) {
            $class->section;
            $class->class;
            $this->Cell(0, 0.8, (strtoupper(trans('class_name') . ': ' . $class->full_name)), 0, 1, 'C');
        }

        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1.5, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('roll_no'))), 1, 0, 'C', true);
        $this->Cell(9, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total_days'))), 1, 0, 'C', true);
        $this->Cell(2.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('present'))), 1, 0, 'C', true);
        $this->Cell(2.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('present') . '%')), 1, 0, 'C', true);
        $this->Cell(2.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('absent'))), 1, 0, 'C', true);
        $this->Cell(2.5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('absent') . '%')), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0, 0, 0);

        $count = 1;
        foreach ($list as $student) {
            $this->Cell(1.5, 0.8, $count++, 1, 0, 'C');
            $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $student['roll_no'])), 1, 0, 'C');
            $this->Cell(9, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $student['name'])), 1, 0, 'L');
            $this->Cell(4, 0.8, iconv('UTF-8', 'ISO-8859-1', $student['total_days']), 1, 0, 'L');
            $this->Cell(2.5, 0.8, iconv('UTF-8', 'ISO-8859-1', Number::format($student['present'], 2, null, app()->getLocale())), 1, 0, 'L');
            $this->Cell(2.5, 0.8, iconv('UTF-8', 'ISO-8859-1', Number::format($student['present_per'], 2, null, app()->getLocale())), 1, 0, 'L');
            $this->Cell(2.5, 0.8, iconv('UTF-8', 'ISO-8859-1', Number::format($student['absent'], 2, null, app()->getLocale())), 1, 0, 'L');
            $this->Cell(2.5, 0.8, iconv('UTF-8', 'ISO-8859-1', Number::format($student['absent_per'], 2, null, app()->getLocale())), 1, 1, 'L');
        }
    }

    public function printClassAttendanceList($list, ClassSection|null $class)
    {
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('attendance') . ' ' . trans('list'))), 0, 1, 'C');

        $this->SetFont('Times', 'B', 10);
        $this->SetTextColor(0, 0, 0);
        if ($class != null) {
            $class->section;
            $class->class;
            $this->Cell(0, 0.8, (strtoupper(trans('class_name') . ': ' . $class->full_name)), 0, 1, 'C');
        }

        $this->SetFont('Times', 'B', 10);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1.5, 0.8, 'No', 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('roll_no'))), 1, 0, 'C', true);
        $this->Cell(11, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true);
        $this->Cell(5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('admission_no'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('date'))), 1, 0, 'C', true);
        $this->Cell(3, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('type'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 9);
        $this->SetTextColor(0, 0, 0);

        $count = 1;
        foreach ($list as $student) {
            $this->Cell(1.5, 0.8, $count++, 1, 0, 'C');
            $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $student['roll_no'])), 1, 0, 'C');
            $this->Cell(10, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $student['name'])), 1, 0, 'L');
            $this->Cell(5, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', $student['admission_no'])), 1, 0, 'L');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $student['date']), 1, 0, 'L');
            $this->Cell(3, 0.8, iconv('UTF-8', 'ISO-8859-1', $student['type_text']), 1, 1, 'L');
        }
    }

    public function printMasterSheet(ExamReport $report)
    {
        $class_section = $report->class_section;
        $class_section->class;
        $class_section->section;
        $subjects = $class_section->class_subjects;
        $subject_ids = $class_section->subjects->pluck('id');
        $student_ids = $class_section->student->pluck('id');
    
        $title = "";
        if (config('app.locale') == 'en') {
            $title = $class_section->full_name . ' ' . trans('master_sheet') . ' : ' . $report->exam_term->name;
        } else {
            $title = trans('master_sheet') . ' ' . $class_section->full_name . ' : ' . $report->exam_term->name;
        }
        $title = strtoupper(iconv('UTF-8', 'ISO-8859-1', remove_accents($title)));
    
        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, $title, 0, 1, 'C');
    
        $this->SetFont('Times', 'B', 8);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);
    
        $this->Cell(1, 0.8, mb_strtoupper(trans('Rank'), 'UTF-8'), 1, 0, 'C', true);
        $this->Cell(4, 0.8, mb_strtoupper(trans('student_name'), 'UTF-8'), 1, 0, 'C', true);
    
        $total_w = 20.7;
        $w = count($subjects) > 0 ? $total_w / count($subjects) : 1;
    
        foreach ($subjects as $subject) {
            $subjectName = mb_substr($subject->subject->name, 0, 4, 'UTF-8');
            $weightage = $subject->weightage;
        
            // Convert encoding to uppercase using iconv
            $formattedName = strtoupper(iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $subjectName . '(' . $weightage . ')'));
        
            $this->Cell(
                $w,
                0.8,
                $formattedName,
                1,
                0,
                'C',
                true
            );
        
            $subject->slashes = 0;
            $subject->fails = 0;
        }
        
    
        $this->Cell(1, 0.8, mb_strtoupper(trans('total'), 'UTF-8'), 1, 0, 'C', true);
        $this->Cell(1, 0.8, mb_strtoupper(trans('Avg'), 'UTF-8'), 1, 1, 'C', true);
    
        $this->SetFont('Times', '', 7);
        $this->SetTextColor(0, 0, 0);
    
        $lines = ExamReportClassDetails::where('exam_report_id', $report->id)
            ->whereIn('student_id', $student_ids)
            ->orderBy('rank', 'asc')->get();
    
        $stats = [
            'EFF' => ['male' => 0, 'female' => 0],
            'EVA' => ['male' => 0, 'female' => 0],
            'MS10' => ['male' => 0, 'female' => 0],
            'MI10' => ['male' => 0, 'female' => 0],
            'MIN' => ['male' => 20, 'female' => 20],
            'MAX' => ['male' => 0, 'female' => 0],
            'SUM' => ['male' => 0, 'female' => 0],
        ];
    
        foreach ($lines as $line) {
            $total = 0;
            $this->SetFont('Times', '', 7);
            $this->Cell(1, 0.5, $line->rank < 0 ? 'NA' : $line->rank, 1, 0, 'C');
    
            $this->SetFont('Times', '', 5.5);
            $this->Cell(4, 0.5, mb_strtoupper($line->student->full_name, 'UTF-8'), 1, 0, 'L');
    
            $this->SetFont('Times', '', 7);
    
            $marks = ExamReportStudentSubject::where('exam_report_id', $report->id)
                ->whereIn('subject_id', $subject_ids)
                ->where('student_id', $line->student_id)
                ->get()->pluck('subject_avg', 'subject_id');
    
            foreach ($subjects as $subject) {
                $mark = '/';
                $this->SetTextColor(0, 0, 0);
                if (isset($marks[$subject->subject->id]) && $marks[$subject->subject->id] >= 0) {
                    $mark = $marks[$subject->subject->id];
                    $total += $mark * $subject->weightage;
    
                    if ($mark < 10) {
                        $this->SetTextColor(255, 0, 0);
                        $subject->fails += 1;
                    }
                } else {
                    $subject->slashes += 1;
                }
                $this->Cell($w, 0.5, $mark, 1, 0, 'C');
            }
            $this->SetTextColor(0, 0, 0);
    
            $this->SetFont('Times', 'B', 7);
            $this->Cell(1, 0.5, $total, 1, 0, 'C');
            $this->Cell(1, 0.5, $line->avg, 1, 1, 'C');
    
            // Safely handle gender stats
            $gender = strtolower($line->student->user->gender ?? 'male');
            $gender = in_array($gender, ['male', 'female']) ? $gender : 'male';
    
            $stats['EFF'][$gender] += 1;
            $stats['SUM'][$gender] += $line->avg;
    
            if ($line->avg > 0) {
                $stats['EVA'][$gender] += 1;
            }
            if ($line->avg >= 10) {
                $stats['MS10'][$gender] += 1;
            } else {
                $stats['MI10'][$gender] += 1;
            }
    
            $stats['MAX'][$gender] = max($stats['MAX'][$gender], $line->avg);
            $stats['MIN'][$gender] = min($stats['MIN'][$gender], $line->avg);
        }
    
        $this->SetFont('Times', '', 7);
        $class_size = $stats['EFF']['male'] + $stats['EFF']['female'];
        $this->Cell(5, 0.5, mb_strtoupper(trans('participation_rate'), 'UTF-8'), 1, 0, 'L');
    
        foreach ($subjects as $subject) {
            $value = $class_size > 0 ? (($class_size - $subject->slashes) / $class_size) * 100 : 0;
            $this->SetTextColor($value < 10 ? 255 : 0, $value < 10 ? 0 : 0, 0);
            $this->Cell($w, 0.5, Number::format($value, 2, null, app()->getLocale()), 1, 0, 'C');
        }
    
        $this->Ln();
        $this->Cell(5, 0.5, mb_strtoupper(trans('success_rate'), 'UTF-8'), 1, 0, 'L');
    
        foreach ($subjects as $subject) {
            $participation = $class_size - $subject->slashes;
            $value = $participation > 0 ? (($participation - $subject->fails) / $participation) * 100 : 0;
            $this->SetTextColor($value < 10 ? 255 : 0, $value < 10 ? 0 : 0, 0);
            $this->Cell($w, 0.5, Number::format($value, 2, null, app()->getLocale()), 1, 0, 'C');
        }
    
        $this->Ln();
        $this->addStats($stats);
    }
    

    public function printSequenceMasterSheet(ExamReport $report, ExamSequence $examSequence)
    {
        list($subjects, $subject_ids, $student_ids, $w, $seq_id) = $this->prepareMasterSheet($report, $examSequence);

        $this->SetFont('Times', '', 7);
        $this->SetTextColor(0, 0, 0);

        $lines = ExamReportStudentSequence::where('exam_term_id', $report->exam_term->id)
            ->where('exam_sequence_id', $examSequence->id)
            ->whereIn('student_id', $student_ids)
            ->where('class_section_id', $report->class_section_id)
            ->orderBy('rank', 'asc')->get();
        $rank = 1;
        $stats = [
            'EFF' => ['male' => 0, 'female' => 0],
            'EVA' => ['male' => 0, 'female' => 0],
            'MS10' => ['male' => 0, 'female' => 0],
            'MI10' => ['male' => 0, 'female' => 0],
            'MIN' => ['male' => 20, 'female' => 20],
            'MAX' => ['male' => 0, 'female' => 0],
            'SUM' => ['male' => 0, 'female' => 0]
        ];

        foreach ($lines as $line) {
            $this->SetFont('Times', '', 7);
            $this->Cell(1, 0.5, $line->rank < 0 ? 'NA' : $rank++, 1, 0, 'C');

            $this->SetFont('Times', '', 5.5);
            $this->Cell(4, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $line->student->full_name)), 1, 0, 'L');

            $this->SetFont('Times', '', 7);

            $marks = ExamReportStudentSubject::where('exam_report_id', $report->id)
                ->whereIn('subject_id', $subject_ids)
                ->where('student_id', $line->student_id)
                ->get()->pluck('sequence_marks', 'subject_id');

            $total = 0;
            foreach ($subjects as $subject) {
                $mark = '/';
                $this->SetTextColor(0, 0, 0);
                if (isset($marks[$subject->subject->id]) && property_exists($marks[$subject->subject->id], $seq_id) && $marks[$subject->subject->id]->$seq_id >= 0) {
                    $mark = round($marks[$subject->subject->id]->$seq_id, 2);
                    $total += $mark * $subject->weightage;
                    if ($mark < 10) {
                        $this->SetTextColor(255, 0, 0);
                        $subject->fails += 1;
                    }
                } else {
                    $subject->slashes += 1;
                }
                $this->Cell($w, 0.5, $mark, 1, 0, 'C');
            }
            $this->SetTextColor(0, 0, 0);

            $this->SetFont('Times', 'B', 7);
            $this->Cell(1, 0.5, round((float) $total, 2), 1, 0, 'C');

            $this->SetFont('Times', 'B', 7);
            $this->SetTextColor($line->avg < 10 ? 255 : 0, $line->avg < 10 ? 0 : 0, $line->avg < 10 ? 0 : 255);
            $this->Cell(1, 0.5, $line->avg, 1, 1, 'C');
            $this->SetTextColor(0, 0, 0);

            $gender = strtolower($line->student->user->gender);
            $gender = in_array($gender, ['male', 'female']) ? $gender : 'male';

            $stats['EFF'][$gender] += 1;
            $stats['SUM'][$gender] += $line->avg;
            if ($line->avg > 0) {
                $stats['EVA'][$gender] += 1;
            }
            if ($line->avg >= 10) {
                $stats['MS10'][$gender] += 1;
            } else {
                $stats['MI10'][$gender] += 1;
            }

            $stats['MAX'][$gender] = max($stats['MAX'][$gender], $line->avg);
            $stats['MIN'][$gender] = min($stats['MIN'][$gender], $line->avg);
        }

        $this->SetFont('Times', '', 7);
        $this->SetTextColor(0, 0, 0);
        $class_size = $stats['EFF']['male'] + $stats['EFF']['female'];
        $this->Cell(5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('participation_rate'))), 1, 0, 'L');
        foreach ($subjects as $subject) {
            $value = $class_size > 0 ? (($class_size - $subject->slashes) / $class_size) * 100 : 0;
            $this->SetTextColor($value < 10 ? 255 : 0, $value < 10 ? 0 : 0, 0);
            $this->Cell($w, 0.5, round($value, 2) . ' %', 1, 0, 'C');
        }
        $this->Ln();

        $this->Cell(5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('success_rate'))), 1, 0, 'L');
        foreach ($subjects as $subject) {
            $participation = $class_size - $subject->slashes;
            $value = $participation > 0 ? (($participation - $subject->fails) / $participation) * 100 : 0;
            $this->SetTextColor($value < 10 ? 255 : 0, $value < 10 ? 0 : 0, 0);
            $this->Cell($w, 0.5, round($value, 2) . ' %', 1, 0, 'C');
        }
        $this->Ln();

        $this->addStats($stats);
    }

    private function prepareMasterSheet($report, $examSequence = null)
    {
        $class_section = $report->class_section;
        $class_section->class;
        $class_section->section;
        $subjects = $class_section->class_subjects;
        $subject_ids = $class_section->subjects->pluck('id');
        $student_ids = $class_section->student->pluck('id');
        $seq_id = $examSequence ? $examSequence->id : null;

        $title = config('app.locale') == 'en' 
            ? $class_section->full_name . ' ' . trans('master_sheet') . ' : ' . $report->exam_term->name 
            : trans('master_sheet') . ' ' . $class_section->full_name . ' : ' . $report->exam_term->name;
        $title = strtoupper(iconv('UTF-8', 'ISO-8859-1', remove_accents($title)));

        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, $title, 0, 1, 'C');

        if ($examSequence) {
            $this->SetFont('Times', 'BU', 10);
            $this->SetTextColor(51, 74, 94);
            $this->Cell(0, 1, strtoupper(iconv('UTF-8', 'ISO-8859-1', remove_accents($examSequence->name))), 0, 1, 'C');
        }

        $this->SetFont('Times', 'B', 8);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Rank'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true);

        $total_w = 20.7;
        $w = count($subjects) > 0 ? $total_w / count($subjects) : 1;

        foreach ($subjects as $subject) {
            $subjectName = mb_substr($subject->subject->name, 0, 4, 'UTF-8');
            $weightage = $subject->weightage;
            $formattedName = strtoupper(iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $subjectName . '(' . $weightage . ')'));
            $this->Cell($w, 0.8, $formattedName, 1, 0, 'C', true);
            $subject->slashes = 0;
            $subject->fails = 0;
        }

        $this->Cell(1, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total'))), 1, 0, 'C', true);
        $this->Cell(1, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Avg'))), 1, 1, 'C', true);

        return [$subjects, $subject_ids, $student_ids, $w, $seq_id];
    }

    private function addStats(array $stats)
    {
        if ($this->GetPageHeight() - $this->GetY() >= 4) {
            $this->Ln(1);
        } else {
            $this->AddPage();
        }
        $this->SetFont('Times', 'B', 8);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        // Header
        $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('class size'))), 1, 0, 'C', true);
        $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('tested'))), 1, 0, 'C', true);
        $this->Cell(3.7, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('participation_rate'))), 1, 0, 'C', true);
        $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('passed_average'))), 1, 0, 'C', true);
        $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('failed_average'))), 1, 0, 'C', true);
        $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('min'))), 1, 0, 'C', true);
        $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('max'))), 1, 0, 'C', true);
        $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('success_rate'))), 1, 0, 'C', true);
        $this->Cell(3, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('general_average'))), 1, 1, 'C', true);

        $this->SetFont('Times', 'B', 8);
        $this->SetTextColor(0, 0, 0);
        for ($i = 1; $i <= 9; $i++) {
            $this->Cell(1, 0.5, 'M', 1, 0, 'C');
            $this->Cell(1, 0.5, 'F', 1, 0, 'C');
            if ($i == 3) {
                $this->Cell(1.7, 0.5, 'T', 1, 0, 'C');
                continue;
            }
            $this->Cell(1, 0.5, 'T', 1, 0, 'C');
        }
        $this->Ln();
        $this->SetFont('Times', '', 8);

        // Content
        // Class Size
        $class_size = $stats['EFF']['male'] + $stats['EFF']['female'];
        $this->Cell(1, 0.5, $stats['EFF']['male'], 1, 0, 'C');
        $this->Cell(1, 0.5, $stats['EFF']['female'], 1, 0, 'C');
        $this->Cell(1, 0.5, $class_size, 1, 0, 'C');
        // tested
        $tested = $stats['EVA']['male'] + $stats['EVA']['female'];
        $this->Cell(1, 0.5, $stats['EVA']['male'], 1, 0, 'C');
        $this->Cell(1, 0.5, $stats['EVA']['female'], 1, 0, 'C');
        $this->Cell(1, 0.5, $tested, 1, 0, 'C');

        // Participation rate
        $male_part = $stats['EFF']['male'] < 1 ? 0 : ((float)$stats['EVA']['male'] / $stats['EFF']['male']) * 100;
        $female_part = $stats['EFF']['female'] < 1 ? 0 : ((float)$stats['EVA']['female'] / $stats['EFF']['female']) * 100;
        
        if ($class_size > 0) {
            $part = ((float)$tested / $class_size) * 100;
        } else {
            $part = 0;
        }
        
        $this->Cell(1, 0.5, Number::format($male_part, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($female_part, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1.7, 0.5, Number::format($part, 2, null, app()->getLocale()), 1, 0, 'C');

        // Passed Average
        $total_pass = $stats['MS10']['male'] + $stats['MS10']['female'];
        $this->Cell(1, 0.5, Number::format($stats['MS10']['male'], 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($stats['MS10']['female'], 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($total_pass, 2, null, app()->getLocale()), 1, 0, 'C');

        // Failed Average
        $total_fail = $stats['MI10']['male'] + $stats['MI10']['female'];
        $this->Cell(1, 0.5, Number::format($stats['MI10']['male'], 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($stats['MI10']['female'], 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($total_fail, 2, null, app()->getLocale()), 1, 0, 'C');

        // Min
        $min = $stats['MIN']['male'] < $stats['MIN']['female'] ? $stats['MIN']['male'] : $stats['MIN']['female'];
        $this->Cell(1, 0.5, Number::format($stats['MIN']['male'], 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($stats['MIN']['female'], 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($min, 2, null, app()->getLocale()), 1, 0, 'C');

        // Max
        $max = $stats['MAX']['male'] > $stats['MAX']['female'] ? $stats['MAX']['male'] : $stats['MAX']['female'];
        $this->Cell(1, 0.5, Number::format($stats['MAX']['male'], 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($stats['MAX']['female'], 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($max, 2, null, app()->getLocale()), 1, 0, 'C');

        // Success Rate
        $male_rate = $stats['EFF']['male'] < 1 ? 0 : ((float)$stats['MS10']['male'] / $stats['EFF']['male']) * 100;
        $female_rate = $stats['EFF']['female'] < 1 ? 0 : ((float)$stats['MS10']['female'] / $stats['EFF']['female']) * 100;
        
        if($class_size) {
            $total_rate = ((float)$total_pass / $class_size) * 100;
        } else {
            $total_rate = 0;
        }
        
        $this->Cell(1, 0.5, Number::format($male_rate, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($female_rate, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($total_rate, 2, null, app()->getLocale()), 1, 0, 'C');

        // General average
        $male_avg = $stats['EFF']['male'] < 1 ? 0 : (float)$stats['SUM']['male'] / $stats['EFF']['male'];
        $female_avg = $stats['EFF']['female'] < 1 ? 0 : (float)$stats['SUM']['female'] / $stats['EFF']['female'];

        if($class_size) {
            $total_avg = ((float)$stats['SUM']['male'] + (float)$stats['SUM']['female']) / $class_size;
        } else {
            $total_avg = 0;
        }

        $this->Cell(1, 0.5, Number::format($male_avg, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($female_avg, 2, null, app()->getLocale()), 1, 0, 'C');
        $this->Cell(1, 0.5, Number::format($total_avg, 2, null, app()->getLocale()), 1, 1, 'C');    

        // Footer
        $this->SetFont('Times', 'B', 8);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(3, 0.5, "OBSERVATION", 1, 0, 'C', true);
        $this->Cell(0, 0.5, '', 1, 1, 'C', false);

        // $this->addSignatureBoxes();
    }

    private function addSignatureBoxes()
    {
        if ($this->GetPageHeight() - $this->GetY() >= 5) {
            $this->Ln(1);
        } else {
            $this->AddPage();
        }

        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Times', 'B', 10);

        $this->Cell(5.55, 1, trans('class_delegate'), 1, 0, 'C', true);
        $this->Cell(5.55, 1, trans('class_teacher'), 1, 0, 'C', true);
        $this->Cell(5.55, 1, trans('class_counselor'), 1, 0, 'C', true);
        $this->Cell(5.55, 1, trans('discipline_master'), 1, 0, 'C', true);
        $this->Cell(5.55, 1, trans('council_president'), 1, 1, 'C', true);
        $this->Cell(5.55, 3, "", 1, 0, 'C');
        $this->Cell(5.55, 3, "", 1, 0, 'C');
        $this->Cell(5.55, 3, "", 1, 0, 'C');
        $this->Cell(5.55, 3, "", 1, 0, 'C');
        $this->Cell(5.55, 3, "", 1, 1, 'C');
    }


    public function printAnnualMasterSheet(AnnualReport $report)
    {
        $class_section = $report->class_section;
        $class_section->class;
        $class_section->section;
        $subjects = $class_section->class_subjects;
        $subject_ids = $class_section->subjects->pluck('id');

        $student_ids = AnnualClassDetails::whereHas('annual_report', function ($query) {
            $session_year_id = getSettings('session_year')['session_year'];
            $query->where('session_year_id', $session_year_id);
        })->where('class_section_id', $class_section->id)
            ->get()
            ->pluck('student_id')
            ->toArray();

        $title = "";
        if (config('app.locale') == 'en') {
            $title = $class_section->full_name . ' ' . trans('annual_master_sheet');
        } else {
            $title = trans('annual_master_sheet') . ' ' . $class_section->full_name;
        }
        $title = strtoupper(iconv('UTF-8', 'ISO-8859-1', remove_accents($title)));

        $this->SetFont('Times', 'BU', 12);
        $this->SetTextColor(51, 74, 94);
        $this->Cell(0, 1, $title, 0, 1, 'C');

        $this->SetFont('Times', 'B', 8);
        $this->SetFillColor(51, 74, 94);
        $this->SetTextColor(255, 255, 255);

        $this->Cell(1, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Rank'))), 1, 0, 'C', true);
        $this->Cell(4, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('student_name'))), 1, 0, 'C', true);

        $total_w = 20.7;
        $w = $total_w / count($subjects);
        foreach ($subjects as $subject) {
            $this->Cell($w, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1',
                substr($subject->subject->name, 0, 4) . '(' . Number::format($subject->weightage, 2, null, app()->getLocale()) . ')')), 1, 0, 'C', true);

            $subject->slashes = 0;
            $subject->fails = 0;
        }

        $this->Cell(1, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('total'))), 1, 0, 'C', true);
        $this->Cell(1, 0.8, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('Avg'))), 1, 1, 'C', true);

        $this->SetFont('Times', '', 7);
        $this->SetTextColor(0, 0, 0);

        $lines = AnnualClassDetails::where('annual_report_id', $report->id)
            ->whereIn('student_id', $student_ids)
            ->orderBy('rank', 'asc')->get();
        // $rank = 1;
        $stats = array(
            'EFF' => array(
                'male' => 0,
                'female' => 0
            ),
            'EVA' => array(
                'male' => 0,
                'female' => 0
            ),
            'MS10' => array(
                'male' => 0,
                'female' => 0
            ),
            'MI10' => array(
                'male' => 0,
                'female' => 0
            ),
            'MIN' => array(
                'male' => 20,
                'female' => 20
            ),
            'MAX' => array(
                'male' => 0,
                'female' => 0
            ),
            'SUM' => array(
                'male' => 0,
                'female' => 0
            )
        );
        foreach ($lines as $line) {
            $total = 0;
            $this->SetFont('Times', '', 7);
            if ($line->rank < 0) {
                $this->Cell(1, 0.5, 'NA', 1, 0, 'C');
            } else {
                $this->Cell(1, 0.5, $line->rank, 1, 0, 'C');
            }

            $this->SetFont('Times', '', 5.5);
            $this->Cell(4, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', $line->student->full_name)), 1, 0, 'L');

            $this->SetFont('Times', '', 7);

            $marks = AnnualSubjectReport::where('annual_report_id', $report->id)
                ->whereIn('subject_id', $subject_ids)
                ->where('student_id', $line->student_id)
                ->get()->pluck('subject_avg', 'subject_id');

            $total = 0;
            foreach ($subjects as $subject) {
                $mark = '/';
                $this->SetTextColor(0, 0, 0);
                if (isset($marks[$subject->subject->id]) && $marks[$subject->subject->id] >= 0) {
                    $mark = $marks[$subject->subject->id];
                    $total += $mark * $subject->weightage;
                    // Render failed marks in red
                    if ($mark < 10) {
                        $this->SetTextColor(255, 0, 0);
                        $subject->fails += 1;
                    }
                } else {
                    $subject->slashes += 1;
                }
                $this->Cell($w, 0.5, $mark, 1, 0, 'C');
            }
            $this->SetTextColor(0, 0, 0);

            $this->SetFont('Times', 'B', 7);
            $this->Cell(1, 0.5, Number::format($total, 2, null, app()->getLocale()), 1, 0, 'C');

            $this->SetFont('Times', 'B', 7);
            // Render failed averages in red and passed in blue
            if ($line->avg < 10) {
                $this->SetTextColor(255, 0, 0);
            } else {
                $this->SetTextColor(0, 0, 255);
            }
            $this->Cell(1, 0.5, Number::format($line->avg, 2, null, app()->getLocale()), 1, 1, 'C');
            $this->SetTextColor(0, 0, 0);

            $gender = strtolower($line->student->user->gender);
            $gender = in_array($gender, ['male', 'female']) ? $gender : 'male';

            if (!isset($stats['EFF'][$gender])) {
                $stats['EFF'][$gender] = 0;
            }

            // Stats 
            $stats['EFF'][$gender] += 1;
            $stats['SUM'][$gender] += $line->avg;
            if ($line->avg > 0) {
                $stats['EVA'][$gender] += 1;
            }
            if ($line->avg >= 10) {
                $stats['MS10'][$gender] += 1;
            } else {
                $stats['MI10'][$gender] += 1;
            }

            if ($line->avg > $stats['MAX'][$gender]) {
                $stats['MAX'][$gender] = $line->avg;
            }
            if ($line->avg < $stats['MIN'][$gender]) {
                $stats['MIN'][$gender] = $line->avg;
            }
        }

        $this->SetFont('Times', '', 7);
        $this->SetTextColor(0, 0, 0);
        $class_size = $stats['EFF']['male'] + $stats['EFF']['female'];
        $this->Cell(5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('participation_rate') . ' (%)')), 1, 0, 'L');
        foreach ($subjects as $subject) {
            if ($class_size > 0) {
                $value = (($class_size - $subject->slashes) / $class_size) * 100;
            } else {
                $value = 0;
            }

            // Render failed marks in red
            if ($value < 10) {
                $this->SetTextColor(255, 0, 0);
            } else {
                $this->SetTextColor(0, 0, 0);
            }
            $this->Cell($w, 0.5, $value > 0 ? Number::format($value, 2, null, app()->getLocale()) : '/', 1, 0, 'C');
        }
        $this->Ln();

        $this->Cell(5, 0.5, strtoupper(iconv('UTF-8', 'ISO-8859-1', trans('success_rate') . ' (%)')), 1, 0, 'L');
        foreach ($subjects as $subject) {
            $participation = $class_size - $subject->slashes;
            $value = $participation != 0 ? (($participation - $subject->fails) / $participation) * 100 : 0;

            // Render failed marks in red
            if ($value < 10) {
                $this->SetTextColor(255, 0, 0);
            } else {
                $this->SetTextColor(0, 0, 0);
            }
            $this->Cell($w, 0.5, $value > 0 ? Number::format($value, 2, null, app()->getLocale()) : '/', 1, 0, 'C');
        }
        $this->Ln();

        $this->addStats($stats);
    }

    public function printGlobalSequenceMasterSheet(ExamSequence $examSequence)
    {
        $class_sections = ClassSection::owner()
            ->with('class', 'section')->whereHas('class', function($q) {
                $q->currentMediumOnly();
            })->get();

        $data = null;
        $data['EFF']['male'] = 0;
        $data['EFF']['female'] = 0;
        $data['EVA']['TAUX_FEMALE'] = 0;
        $data['EVA']['TAUX_MALE'] = 0;
        $seq_id = $examSequence->id;

        foreach($class_sections as $class_section) {
            $report = ExamReport::whereClassSectionId($class_section->id)
                ->whereSessionYearId(getSessionYearData()->id)->first();
            $subjects = $class_section->subjects;
            $subject_ids = $subjects->pluck('id');
            $student_ids = $class_section->student->pluck('id');

            $data['EVA']['male'] += $report->male_students;
            $data['EVA']['female'] += $report->female_students;

            $lines = ExamReportStudentSequence::whereExamSequenceId($seq_id)
                ->whereIn('student_id', $student_ids)
                ->orderBy('rank', 'asc')
                ->get();
            
                foreach ($lines as $line) {
                    $marks = ExamReportStudentSubject::whereExamReportId($report->id)
                        ->whereStudentId($line->student_id)
                        ->whereIn('subject_id', $subject_ids)
                        ->get()
                        ->pluck('sequence_marks', 'subject_id');

                        $gender = strtolower($line->student->user->gender);
                        $gender = in_array($gender, ['male', 'female']) ? $gender : 'male';
            
                        if (!isset($data['EFF'][$gender])) {
                            $data['EFF'][$gender] = 0;
                        }
                
                        // Stats
                        $data['EFF'][$gender] += 1;
            
                        $data['SUM'][$gender] += $line->avg;
                        if ($line->avg > 0) {
                            $data['EVA'][$gender] += 1;
                        }
                        if ($line->avg >= 10) {
                            $data['MS10'][$gender] += 1;
                        } else {
                            $data['MI10'][$gender] += 1;
                        }
                
                        if ($line->avg > $data['MAX'][$gender]) {
                            $data['MAX'][$gender] = $line->avg;
                        }
                        if ($line->avg < $data['MIN'][$gender]) {
                            $data['MIN'][$gender] += $line->avg;
                        }
                }

        }

        dd($data);

    }
}
