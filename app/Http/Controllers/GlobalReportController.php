<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\ExamReport;
use App\Models\ClassSection;
use App\Models\ExamSequence;
use Illuminate\Http\Request;
use App\Printing\GlobalReportPDF;
use Barryvdh\Snappy\Facades\SnappyPdf;
use App\Models\ExamReportStudentSubject;
use App\Models\ExamReportStudentSequence;
use App\Models\ExamTerm;

class GlobalReportController extends Controller
{
    public function index()
    {
        $sequences = ExamSequence::whereCenterId(get_center_id())
            ->whereHas('term', function($q) {
                $q->currentSessionYear();
            })
            ->whereStatus(true)
            ->get();

        $terms = ExamTerm::owner()->currentSessionYear()->currentMedium()
            ->with(['sequence'])->get();
        
        $groups = Group::whereCenterId(get_center_id())->get();

        return view('global-report.index', compact('sequences', 'groups', 'terms'));
    }

    public function sequence(Request $request, ExamSequence $sequence)
    {
        $pdf = GlobalReportPDF::getInstance(get_center_id(), 'L');
        
        $pdf->generateReport($sequence, $request->compact);

        return response($pdf->Output('S', 'global_report.pdf'), 200, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    public function term(Request $request, ExamTerm $term)
    {
        $pdf = GlobalReportPDF::getInstance(get_center_id(), 'L');
        
        $pdf->generateTermReport($term, $request->compact);
        
        return response($pdf->Output('S', 'global_report.pdf'), 200, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    public function classStats(ExamSequence $sequence)
    {
        $class_sections = ClassSection::owner()
            ->with('class', 'section')
            ->whereHas('class', function($q) {
                $q->where('medium_id', getCurrentMedium()->id);
            })->get();

        $class_stats = [];
        $seq_id = $sequence->id;

        foreach ($class_sections as $class_section) {
            $report = ExamReport::whereClassSectionId($class_section->id)
                ->whereSessionYearId(getSessionYearData()->id)->first();
            if (!$report) continue;

            $data = [
                'EFF' => ['male' => 0, 'female' => 0],
                'EVA' => ['male' => 0, 'female' => 0],
                'MS10' => ['male' => 0, 'female' => 0],
                'MI10' => ['male' => 0, 'female' => 0],
                'MIN' => ['male' => 20, 'female' => 20],
                'MAX' => ['male' => 0, 'female' => 0],
                'SUM' => ['male' => 0, 'female' => 0]
            ];

            $subjects = $class_section->subjects;
            $subject_ids = $subjects->pluck('id');
            $student_ids = $class_section->student->pluck('id');

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

                $data['MAX'][$gender] = max($data['MAX'][$gender], $line->avg);
                $data['MIN'][$gender] = min($data['MIN'][$gender], $line->avg);
            }

            $class_size = $data['EFF']['male'] + $data['EFF']['female'];
            $male_part = $data['EFF']['male'] < 1 ? 0 : ($data['EVA']['male'] / $data['EFF']['male']) * 100;
            $female_part = $data['EFF']['female'] < 1 ? 0 : ($data['EVA']['female'] / $data['EFF']['female']) * 100;
            $part = $class_size > 0 ? ($data['EVA']['male'] + $data['EVA']['female']) / $class_size * 100 : 0;

            $male_rate = $data['EFF']['male'] < 1 ? 0 : ($data['MS10']['male'] / $data['EFF']['male']) * 100;
            $female_rate = $data['EFF']['female'] < 1 ? 0 : ($data['MS10']['female'] / $data['EFF']['female']) * 100;
            $total_rate = $class_size > 0 ? ($data['MS10']['male'] + $data['MS10']['female']) / $class_size * 100 : 0;

            $male_avg = $data['EFF']['male'] < 1 ? 0 : $data['SUM']['male'] / $data['EFF']['male'];
            $female_avg = $data['EFF']['female'] < 1 ? 0 : $data['SUM']['female'] / $data['EFF']['female'];
            $total_avg = $class_size > 0 ? ($data['SUM']['male'] + $data['SUM']['female']) / $class_size : 0;

            $class_stats[] = [
                'class_section' => $class_section->full_name,
                'class_size' => $class_size,
                'EFF' => $data['EFF'],
                'male_part' => number_format($male_part, 2),
                'female_part' => number_format($female_part, 2),
                'part' => number_format($part, 2),
                'male_rate' => number_format($male_rate, 2),
                'female_rate' => number_format($female_rate, 2),
                'total_rate' => number_format($total_rate, 2),
                'male_avg' => number_format($male_avg, 2),
                'female_avg' => number_format($female_avg, 2),
                'total_avg' => number_format($total_avg, 2),
                'min' => min($data['MIN']['male'], $data['MIN']['female']),
                'max' => max($data['MAX']['male'], $data['MAX']['female']),
                'MIN' => $data['MIN'],
                'MAX' => $data['MAX']
            ];
        }

        return $class_stats;
    }

    public function group(Request $request)
    {
        if (!is_array($request->groups)) {
            $groups = [$request->group];
        }

        $pdf = new GlobalReportPDF(get_center_id(), 'L');
        $pdf->generateGroupReport($groups, $request->compact);
        return response($pdf->Output('S', 'global_report.pdf'), 200, [
            'Content-Type' => 'application/pdf'
        ]);
    }
}
