<?php

namespace App\Services;

use App\Models\AnnualClassDetails;
use App\Models\AnnualClassSubjectReport;
use App\Models\Subject;
use App\Models\ExamTerm;
use App\Models\Students;
use App\Models\ExamMarks;
use App\Models\ExamReport;
use App\Models\AnnualReport;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\ExamSequence;
use App\Models\ExamTimetable;
use Illuminate\Support\Facades\DB;
use App\Models\AnnualSubjectReport;
use Illuminate\Support\Facades\Log;
use App\Models\ExamReportClassDetails;
use App\Models\ExamReportClassSubject;
use App\Models\ExamReportStudentSubject;
use App\Models\ExamReportStudentSequence;

class YearReportService {
    private const MIN_PERCENTAGE = 0;

    public function createExamReport($data) {
        $class_section = ClassSection::with('teacher.user')->findOrFail($data->class_section_id);
        $session_year = getSettings('session_year');

        $examTerms = ExamReport::where('class_section_id', $data->class_section_id)
                                ->where('session_year_id', $session_year['session_year'])
                                ->with('exam_term')
                                ->whereHas('exam_term', function($query){
                                    $query->where('medium_id', getCurrentMedium()->id);
                                });

        if($examTerms->count() < 1){
            return null;
        }

        $avg = $examTerms->avg('avg');
        $total_points = $examTerms->sum('total_points');
        
        $report_ids = $examTerms->pluck('id');

        $report_subjects = ExamReportClassSubject::whereIn('exam_report_id', $report_ids)->pluck('subject_id')->toArray();

        $subject_ids = array_unique($report_subjects);
        $all_subjects = ClassSubject::whereIn('subject_id', $subject_ids)->where('class_id', $class_section->class_id)->get();
        $total_ceof = $all_subjects->sum('weightage');

        //Create Exam report
        $annualReport = $this->createClassReport(
                    $session_year['session_year'],
                    $class_section,
                    $total_ceof, 
                    $avg, 
                    $total_points,
                    $report_ids
                );

        // Add Student wise Report Details
        $this->createStudentDetails( $annualReport->id, $data->class_section_id, $report_ids, $subject_ids);

        $this->createClassSectionSubjectDetails($annualReport->id, $data->class_section_id);

        $this->calculateTotalAvgOfEachStudent($annualReport->id, $data->class_section_id, $report_ids);


        return $annualReport;
    }

    public function createClassReport($session_year_id, $class_section, $total_coef, $avg, $total_points, $report_ids) {
        // Delete Old report 
        $report = AnnualReport::where('class_section_id', $class_section->id)
            ->where('session_year_id', $session_year_id)
            ->first();
        if ($report) $report->delete();


        //find male female students
        $male_count = Students::owner()->whereHas('user', function ($query) {
            $query->where('gender', 'male');
        })->whereHas('class_section', function ($query) use ($class_section) {
            $query->where('id', $class_section->id);
        })->count();
        $female_count = Students::owner()->whereHas('user', function ($query) {
            $query->where('gender', 'female');
        })->whereHas('class_section', function ($query) use ($class_section) {
            $query->where('id', $class_section->id);
        })->count();

        //Create parent Exam report
        return AnnualReport::updateOrCreate(
            ['class_section_id' => $class_section->id, 'session_year_id' => $session_year_id],
            [
                'class_teacher_id' => $class_section->teacher->user->id ?? null, 
                'male_students' => $male_count, 
                'female_students' => $female_count, 
                'total_students' => ($male_count + $female_count), 
                'avg' => $avg, 
                'total_coef' => $total_coef, 
                'total_points' => $total_points,
                'term_report_ids' => json_encode($report_ids)
            ]
        );
    }

    public function createStudentDetails($report_id, $class_section_id, $exam_report_ids, $subject_ids) {
        foreach($subject_ids as $subject){
            $infos = ExamReportStudentSubject::where('subject_id', $subject)
                                    ->whereIn('exam_report_id', $exam_report_ids)
                                    ->where('subject_total', '!=', '-1')
                                    ->selectRaw("student_id, subject_id, count(exam_report_id) as ex_counts, SUM(subject_avg) as subject_total, GROUP_CONCAT(CONCAT('\"', exam_report_id, '\":', subject_avg)) as term_mark")
                                    ->groupBy(['student_id', 'subject_id'])
                                    ->get();
            $subject_data = array();
            foreach($infos as $info){
                $avg = round($info->subject_total/$info->ex_counts, 2);
                
                $percentage = ($avg * 100) / 20;
                $grade = findExamGrade($percentage, true);
                $subject_data[] = [
                    'annual_report_id' => $report_id,
                    'class_section_id' => $class_section_id,
                    'subject_id' => $subject,
                    'student_id' => $info->student_id,
                    'subject_total' => $info->subject_total,
                    'subject_avg' => $avg,
                    'subject_rank' => 0,
                    'term_marks' => "{" . $info->term_mark ."}",
                    'subject_grade' => $grade ? $grade->grade : "",
                    'subject_remarks' => $grade ? $grade->remarks : " "
                ];
            }

            AnnualSubjectReport::upsert($subject_data, ['annual_report_id', 'class_section_id', 'subject_id', 'student_id']);

            // Now we have to create values for subjects that were not taken by student all year round. 
            $infos = ExamReportStudentSubject::where('subject_id', $subject)
                                    ->whereIn('exam_report_id', $exam_report_ids)
                                    ->where('subject_total',  '-1')
                                    ->selectRaw("student_id, subject_id, count(exam_report_id) as ex_counts, SUM(subject_avg) as subject_total, GROUP_CONCAT(CONCAT('\"', exam_report_id, '\":', subject_avg)) as term_mark")
                                    ->groupBy(['student_id', 'subject_id'])
                                    ->get();
            $subject_data = array();
            foreach($infos as $info){
                if($info->subject_total != (-1*count($exam_report_ids)))
                    continue;
                $subject_data[] = [
                    'annual_report_id' => $report_id,
                    'class_section_id' => $class_section_id,
                    'subject_id' => $subject,
                    'student_id' => $info->student_id,
                    'subject_total' => -1,
                    'subject_avg' => -1,
                    'subject_rank' => 0,
                    'term_marks' => "{}",
                    'subject_grade' => "",
                    'subject_remarks' => " "
                ];
            }

            AnnualSubjectReport::upsert($subject_data, ['annual_report_id', 'class_section_id', 'subject_id', 'student_id']);

            //Create subject ranking.
            $subject_order = AnnualSubjectReport::where([
                'annual_report_id' => $report_id,
                'class_section_id' => $class_section_id,
                'subject_id' => $subject,
            ])->orderBy('subject_avg', 'desc')->get();

            $rank=1;
            foreach ($subject_order as $subject_item) {
                $subject_item->subject_rank = $rank++;
                $subject_item->save();
            }
        }
    }

    public function createClassSectionSubjectDetails($report_id, $class_section_id) {
        $list = AnnualSubjectReport::where('annual_report_id', $report_id)
                                    ->where('class_section_id', $class_section_id)
                                    ->selectRaw('subject_id, min(subject_avg) as min_val, max(subject_avg) as max_val')
                                    ->groupBy('subject_id')
                                    ->get();
        $subject_data = array();
        foreach($list as $item){
            $subject_data[] = [
                'annual_report_id' => $report_id,
                'class_section_id' => $class_section_id,
                'subject_id' => $item->subject_id,
                'min' => $item->min_val,
                'max' => $item->max_val
            ];
        }

        AnnualClassSubjectReport::upsert($subject_data, ['annual_report_id', 'class_section_id', 'subject_id']);
    }


    public function calculateTotalAvgOfEachStudent($report_id, $class_section_id, $exam_report_ids) {
        $list = ExamReportClassDetails::whereIn('exam_report_id', $exam_report_ids)
                                ->selectRaw("student_id, count(exam_report_id) as ex_counts, SUM(avg) as total_avg, GROUP_CONCAT(CONCAT('\"', exam_report_id, '\":', avg)) as term_avgs, GROUP_CONCAT(CONCAT('\"', exam_report_id, '\":', exam_report_class_details.rank)) as term_ranks")
                                ->groupBy(['student_id'])
                                ->orderBy('total_avg', 'desc')
                                ->get();
        
        $rank = 1;
        $annual_results = array();
        foreach($list as $item){
            $avg = round($item->total_avg / $item->ex_counts,2);
            $annual_results[] = [
                'annual_report_id' => $report_id,
                'class_section_id' => $class_section_id,
                'student_id' => $item->student_id,
                'avg' => $avg,
                'rank' => $rank++,
                'term_avgs' => "{" . $item->term_avgs . "}",
                'term_ranks' => "{" . $item->term_ranks . "}"
            ];
        }

        AnnualClassDetails::upsert($annual_results, ['annual_report_id', 'class_section_id', 'student_id']);
    }
}