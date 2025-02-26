<?php

namespace App\Yadiko\Exam\sequenceWiseMarks\UserInterface\Http;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use App\Models\ExamSequence;
use App\Models\ExamTerm;
use App\Models\Students;
use App\Printing\ExamPrints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SequenceWiseMarksList extends Controller
{
    public function sequenceWiseMarksIndex() {
        $session_year = getSettings('session_year');

        $exam_terms = ExamTerm::owner()
            ->currentMedium()
            ->get()
            ->pluck('id');

        $sequences = ExamSequence::Owner()
            ->whereIn('exam_term_id', $exam_terms)
            ->whereHas('exam', function($query) use ($session_year) {
                $query->where('session_year_id', $session_year['session_year']);
            })
            ->get()
            ->pluck('name', 'id');

        $classSections = ClassSection::owner()
            ->with('class.stream', 'section')
            ->whereHas('class', function ($q) {
                $q->activeMediumOnly();
            })
            ->get();

        return view('exams.sequence_marks.index', compact('sequences', 'classSections'));
    }

    public function sequenceWiseMarksList(Request $request) {
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'erss.avg';
        $order = $request->order ?? 'DESC';
        $class_section_id = $request->class_section_id ?? 0;

        // Récupérer l'année scolaire
        $session_year = getSessionYearData(); // Assurez-vous que cette fonction existe et retourne l'année scolaire

        // Vérifiez si l'année scolaire a été récupérée avec succès
        if (!$session_year) {
            return response()->json(['error' => 'Session year not found.'], 400);
        }

        $sql = Students::select(['students.*', 'erss.avg as avg'])
            ->join('student_sessions', 'students.id', '=', 'student_sessions.student_id') // Jointure directe
            ->join('exam_report_student_sequences as erss', 'erss.student_id', 'students.id') // Jointure avec erss
            ->where('student_sessions.session_year_id', $session_year->id) // Utilisation de l'ID de l'année scolaire
            ->where('student_sessions.class_section_id', $class_section_id) // Utilisation de l'ID de la section de classe
            ->with(['user:id,first_name,last_name', 'exam_marks' => function ($q) use ($request, $session_year) {
                $q->whereHas('exam', function ($q) use ($request, $session_year) {
                    $q->where('exam_sequence_id', $request->sequence_id)
                        ->where('exams.session_year_id', $session_year->id); // Utilisation de l'ID de l'année scolaire
                });
            }, 'exam_marks.timetable:id,exam_id,total_marks', 'exam_marks.subject'])
            ->whereHas('exam_marks.exam', function ($q) use ($request) {
                $q->where('exam_sequence_id', $request->sequence_id);
            })
            ->orderBy('erss.avg', 'DESC');

        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql = $sql->where(function ($q) use ($search) {
                $q->where('students.id', 'LIKE', "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%$search%")
                            ->orWhere('last_name', 'LIKE', "%$search%");
                    });
            });
        }

        $total = $sql->count();
        $sql->skip($offset)->take($limit);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;

        foreach ($res as $row) {
            $total_marks = array_sum(array_column(array_column($row->exam_marks->toArray(), 'timetable'), 'total_marks'));
            $obtained_marks = $row->exam_marks->sum('obtained_marks');
            $total_coef = $row->exam_marks->sum('subject.weightage');
            $operate = '<div class="actions"><a class="btn btn-sm bg-success-light edit-data btn-rounded" title="Edit" data-bs-toggle="modal" data-bs-target="#editModal"><i class="feather-edit"></i></a></div>&nbsp;&nbsp;';
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['student_name'] = $row->user->first_name;
            $tempRow['total_marks'] = $total_marks;
            $tempRow['avg_marks'] = $row->avg;

            foreach ($row->exam_marks as $mark) {
                if ($mark->obtained_marks < 0) {
                    $mark->obtained_marks = '/';
                    $obtained_marks = $obtained_marks + 1;
                }
            }
            $tempRow['obtained_marks'] = $obtained_marks;
            $tempRow['exam_marks'] = $row->exam_marks;

            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        if (request()->get('print')) {
            $classSection = null;
            $examSequence = null;

            if (!empty($request->class_section_id)) {
                $classSection = ClassSection::find($request->class_section_id);
            }

            if (!empty($request->sequence_id)) {
                $examSequence = ExamSequence::find($request->sequence_id);
            }

            $pdf = ExamPrints::getInstance(get_center_id(), 'P');

            $pdf->printExamSequenceMarks($rows, $classSection, $examSequence);

            return response(
                $pdf->Output('', 'SEQUENTIAL EXAM MARKS LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }
}