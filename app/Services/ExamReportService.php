<?php

namespace App\Services;

use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\ExamMarks;
use App\Models\ExamReport;
use App\Models\ExamReportClassDetails;
use App\Models\ExamReportClassSubject;
use App\Models\ExamReportStudentSequence;
use App\Models\ExamReportStudentSubject;
use App\Models\ExamSequence;
use App\Models\ExamTimetable;
use App\Models\Students;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamReportService
{
    private const MINIMUM_PASS_PERCENTAGE = 0;

    public function generateTermReport($request): \Illuminate\Database\Eloquent\Model|bool|\Illuminate\Database\Eloquent\Builder
    {
        $classSection = $this->getClassSection($request->class_section_id);

        $sessionYearId = getSettings('session_year')['session_year'];

        // returns all the timetables that belong to this particular term and for this session year.
        // timetables relate subjects, to particular sequences which are under particular terms and store information about the grading of those students in that particular subject

        $examTimetables = $this->getTermExamTimetables($request, $sessionYearId, $classSection->id);

        if (empty($examTimetables)) return false;

        // fetching all the exam_marks which belong to that particular list of timetables.
        $examMarks = $this->getExamMarks($examTimetables, $sessionYearId, $classSection);

        if (empty($examMarks)) return false;

        // this is going to pick all the students in that class who have a mark in a subject.
        // so people who didn't have a mark in that subject will not be included in that calculation.

        // this is not producing the correct number of students.

        // TODO : find out why this is not producing the correct value.
        $studentIds = array_unique(array_column($examMarks->toArray(), 'student_id'));

        $subjectIds = array_unique(array_column($examTimetables->toArray(), 'subject_id'));

        $classSubjects = $this->getClassSubjects($classSection->class_id, $subjectIds);

        $reportData = [
            'totalCoefficient' => $classSubjects->sum('weightage'), // getting the total possible weightage that a student can have.
            'totalPoints' => $examTimetables->sum('total_marks'),   // getting the possible total number of points that a student can ever have
            'classAverage' => $this->calculateClassAverage($examMarks, $studentIds, $examTimetables) // getting the class average
        ];

        // recreating the exam report information.
        // this report is created for the class.
        $examReport = $this->createOrUpdateReport($sessionYearId, $classSection, $request->term_id, $reportData);

        // filling the studentDetails for these results.
        $this->createStudentDetails($sessionYearId, $request->term_id, $examReport->id, $request->class_section_id, $subjectIds, $classSection);

        $this->createClassSectionSubjectDetails($examReport->id, $request->class_section_id, $sessionYearId);

        $this->calculateTotalAvgOfEachStudent($examReport->id, $request->class_section_id, $subjectIds, $reportData["totalCoefficient"], $sessionYearId);

        return $examReport;
    }

    // this is the function where we calculate the student average and perform the ranking of the student.
    public function rankStudentsByExamMarks($sessionYearId, $studentIDs, $classSectionId, $termID, $sequenceIds, $examReportID): void
    {
        // getting all the marks and grouping by the students.
        $orderByMarksStudentID = ExamMarks::query()->owner()
            ->whereIn('student_id', $studentIDs)
            ->where('obtained_marks', '>', -1)
            ->where('exam_marks.session_year_id', $sessionYearId)
            ->whereHas('timetable', fn($q) => $q->where([
                'marks_upload_status' => 1,
                'class_section_id' => $classSectionId,
                'session_year_id' => $sessionYearId
            ]))
            ->whereHas('student', function ($q) use ($sessionYearId, $classSectionId) {
                $q->whereHas('studentSessions', function ($query) use ($classSectionId, $sessionYearId) {
                    $query->where('session_year_id', $sessionYearId)
                        ->where('class_section_id', $classSectionId);
                });
            })
            ->whereHas('timetable.exam', fn($q) => $q->owner()
                ->where('type', 1)  // type 1 = sequential type of exam
                ->where('exam_term_id', $termID)
                ->whereIn('exam_sequence_id', $sequenceIds)
            )
            ->join('exam_timetables', 'exam_timetables.id', '=', 'exam_marks.exam_timetable_id')
            ->selectRaw(
                'exam_marks.subject_id, 
                 exam_marks.student_id,
                 exam_timetable_id, 
                 SUM(exam_timetables.total_marks) as total_marks, 
                 SUM(exam_marks.obtained_marks) as total_obtained_marks, 
                 COUNT(exam_marks.obtained_marks) as total_subjects'
            )
            ->groupBy('student_id')
            ->orderByDesc('total_obtained_marks')
            ->get()
            ->groupBy('total_obtained_marks');

        $orderByMarksStudentID = array_values($orderByMarksStudentID->toArray());

        foreach ($orderByMarksStudentID as $rank => $marksGroup) {

            foreach ($marksGroup as $key => $row) {

                $row = (object) $row;

                // TODO : Make sure the total for the subject is gotten and not assumed to be 20

                $studentAverage = ($row->total_obtained_marks * 20) / ($row->total_subjects * $row->total_marks);

                ExamReportClassDetails::query()->updateOrCreate(
                    ['exam_report_id' => $examReportID, 'student_id' => $row->student_id],
                    [
                        'total_obtained_points' => $row->total_obtained_marks,
                        'student_id' => $row->student_id,
                        'rank' => ($rank + 1),
                        'avg' => $studentAverage //Assuming the total mark per subject is 2
                    ]
                );
            }

        }
    }

    private function getStudentIDs($classSectionId, $sessionYearId)
    {
        // fetching all the students that belong to that class for the current session year.
        return Students::owner()
            ->whereHas('studentSessions', function ($query) use ($classSectionId, $sessionYearId) {
                $query->where('class_section_id', $classSectionId)
                    ->where('session_year_id', $sessionYearId);
            })
            ->get()
            ->pluck('id');
    }

    private function getExamSequenceIds($termID): \Illuminate\Support\Collection
    {
        return ExamSequence::owner()
            ->where('exam_term_id', $termID)
            ->pluck('id');
    }

    private function getExamMarksSubjectWise($session_year_id, $class_section_id, $termID, $sequences, $studentIDs)
    {
        return ExamMarks::owner()
            ->with(['student:id,user_id,class_section_id'])
            ->whereIn('student_id', $studentIDs)
            ->where(['session_year_id' => $session_year_id])
            ->where('obtained_marks', '>', -1)
            ->whereHas('timetable', function ($q) use ($class_section_id) {
                $q->where(['marks_upload_status' => 1, 'class_section_id' => $class_section_id]);
            })
            ->whereHas('timetable.exam', function ($q) use ($termID, $sequences) {
                $q->owner()
                    ->where(['type' => 1, 'exam_term_id' => $termID])
                    ->whereIn('exam_sequence_id', $sequences);
            })
            ->select(DB::raw('subject_id, student_id, exam_timetable_id, SUM(obtained_marks) as total_obtained_marks, count(id) as seqs'))
            ->groupBy('student_id', 'subject_id')
            ->get();
    }

    private function getExamMarksSubjectWiseForEmptyExams($session_year_id, $class_section_id, $termID, $sequences, $studentIDs)
    {
        return ExamMarks::owner()
            ->with(['student:id,user_id,class_section_id'])
            ->whereIn('student_id', $studentIDs)
            ->where(['session_year_id' => $session_year_id])
            ->where('obtained_marks', '-1')
            ->whereHas('timetable', function ($q) use ($class_section_id) {
                $q->where(['marks_upload_status' => 1, 'class_section_id' => $class_section_id]);
            })
            ->whereHas('timetable.exam', function ($q) use ($termID, $sequences) {
                $q->owner()
                    ->where(['type' => 1, 'exam_term_id' => $termID])
                    ->whereIn('exam_sequence_id', $sequences);
            })
            ->select(DB::raw('subject_id, student_id, exam_timetable_id, SUM(obtained_marks) as total_obtained_marks, count(id) as seqs'))
            ->groupBy('student_id', 'subject_id')
            ->get();
    }

    private function getSubjectData($session_year_id, $class_section_id, $termID, $sequences): \Illuminate\Database\Eloquent\Collection|array
    {
        return ExamTimetable::query()
            ->where(['session_year_id' => $session_year_id, 'marks_upload_status' => 1, 'class_section_id' => $class_section_id])
            ->whereHas('exam', function ($q) use ($termID, $sequences) {
                $q->owner()
                    ->where(['type' => 1, 'exam_term_id' => $termID])
                    ->whereIn('exam_sequence_id', $sequences);
            })
            ->select(DB::raw('subject_id, count(id) as sequence_count, SUM(total_marks) as total_marks'))
            ->groupBy('subject_id')
            ->get();
    }

    private function processExamMarksSubjectWise($examMarksSubjectWise, $subjectWiseTotalMarks, $subjectSequenceCount, $examReportID): void
    {
        foreach ($examMarksSubjectWise as $row) {
            $avg = $this->calculateSubjectAverage(
                $row->total_obtained_marks,
                $subjectWiseTotalMarks[$row->subject_id] ?? 0,
                $subjectSequenceCount[$row->subject_id] ?? 0,
                $row->seqs
            );

            ExamReportStudentSubject::query()->updateOrCreate(
                ['exam_report_id' => $examReportID, 'student_id' => $row->student_id, 'subject_id' => $row->subject_id],
                [
                    'subject_total' => $row->total_obtained_marks,
                    'subject_rank' => 0,
                    'subject_avg' => $avg
                ]
            );
        }
    }

    public function getExamMarksSubjectSequenceWise($sessionYearId, $classSectionID, $termID, $sequences, $studentIDs): \Illuminate\Support\Collection
    {
        return DB::table('exam_marks')
            ->selectRaw('exam_marks.subject_id, exam_marks.student_id,exams.exam_sequence_id,exams.exam_term_id, exam_timetable_id, SUM(obtained_marks) AS total_obtained_marks,SUM(total_marks) as total_marks')
            ->join('exam_timetables', 'exam_timetables.id', '=', 'exam_marks.exam_timetable_id')
            ->join('exams', 'exams.id', '=', 'exam_timetables.exam_id')
            ->where([
                'exam_marks.session_year_id' => $sessionYearId,
                'exams.type' => 1,
                'exams.exam_term_id' => $termID,
                'exam_timetables.marks_upload_status' => 1,
                'exam_timetables.class_section_id' => $classSectionID
            ])
            ->where('exam_marks.obtained_marks', '>', '-1')
            ->whereIn('student_id', $studentIDs)
            ->whereIn('exams.exam_sequence_id', $sequences)
            ->groupBy('exam_marks.student_id', 'exam_marks.subject_id', 'exams.exam_sequence_id')->get();
    }


    public function createStudentDetails($sessionYearId, $termID, $examReportID, $classSectionId, $subjectIds, $classSection): void
    {
        // getting all the students that belong to that class for that particular session year.
        $studentIDs = $this->getStudentIDs($classSectionId, $sessionYearId);

        // the ids of all the sequences.
        $sequenceIds = $this->getExamSequenceIds($termID);

        $this->rankStudentsByExamMarks($sessionYearId, $studentIDs, $classSectionId, $termID, $sequenceIds, $examReportID);

        $examMarksSubjectWise = $this->getExamMarksSubjectWise($sessionYearId, $classSectionId, $termID, $sequenceIds, $studentIDs);

        // find all the exam timetables and group them by the subject_ids
        $subjectData = $this->getSubjectData($sessionYearId, $classSectionId, $termID, $sequenceIds);

        $subjectWiseTotalMarks = $subjectData->pluck('total_marks', 'subject_id');

        $subjectSequenceCount = $subjectData->pluck('sequence_count', 'subject_id');

        $this->processExamMarksSubjectWise(
            $examMarksSubjectWise,
            $subjectWiseTotalMarks,
            $subjectSequenceCount,
            $examReportID
        );

        $examMarksSubjectWise = $this->getExamMarksSubjectWiseForEmptyExams($sessionYearId, $classSectionId, $termID, $sequenceIds, $studentIDs);

        $this->processEmptyExamMarks($examReportID, $examMarksSubjectWise, $subjectSequenceCount);

        $examMarksSubjectSequenceWise = $this->getExamMarksSubjectSequenceWise($sessionYearId, $classSection->id, $termID, $sequenceIds, $studentIDs);

        // grouping the examMarksSubjectSequences data by the student_id
        $exMarkSubjectSequenceStudentGrouped = $examMarksSubjectSequenceWise->groupBy('student_id');

        $totalCoefficient = ClassSubject::query()->whereIn('subject_id', $subjectIds)->where('class_id', $classSection->class_id)->sum('weightage');

        $examReportStudentSubject = [];
        $examSequenceReport = [];

        $minReq = (int)(ExamReportService::MINIMUM_PASS_PERCENTAGE * 0.01 * $totalCoefficient);

        $this->groupings($subjectIds , $exMarkSubjectSequenceStudentGrouped, $examReportID, $classSection, $examReportStudentSubject, $examSequenceReport);

        $this->updateStudentSequenceReport($classSection->id, $studentIDs, $examSequenceReport, $termID, $minReq, $sequenceIds, $sessionYearId);

        // exam report student subject update
        ExamReportStudentSubject::query()->upsert($examReportStudentSubject, ['exam_report_id', 'student_id', 'subject_id'], ['sequence_marks', 'subject_grade', 'subject_remarks']);
    }

    public function groupBySubject($studentMarks, $examReportID, &$examReportStudentSubject): void
    {
        $groupBySubject = $studentMarks->groupBy('subject_id');

        foreach ($groupBySubject as $subjectWise) {
            $sequenceMarks = [];
            $studentID = '';
            $subjectID = '';
            $percentage = 0;

            foreach ($subjectWise as $row) {
                $studentID = $row->student_id;
                $subjectID = $row->subject_id;
                $sequenceMarks[$row->exam_sequence_id] = ($row->total_obtained_marks * 20) / $row->total_marks;
                $percentage = ($row->total_obtained_marks * 100) / $row->total_marks;
            }

            $grade = findExamGrade($percentage, true);

            $examReportStudentSubject[] = array(
                'exam_report_id' => $examReportID,
                'student_id' => $studentID,
                'subject_id' => $subjectID,
                'subject_grade' => $grade->grade,
                'subject_remarks' => $grade->remarks,
                'sequence_marks' => json_encode($sequenceMarks)
            );
        }
    }

    public function groupBySequence($subjectIds, $studentMarks, $classSection, &$examSequenceReport): void
    {
        // Group marks by sequence
        $groupBySequence = $studentMarks->groupBy('exam_sequence_id');

        // Get subject coefficients
        $classSubjectCoefficients = ClassSubject::query()
            ->whereIn('subject_id', $subjectIds)
            ->where('class_id', $classSection->class_id)
            ->pluck('weightage', 'subject_id')
            ->toArray();

        foreach ($groupBySequence as $sequenceMarks) {
            $data = $sequenceMarks->first();
            
            $weightedTotal = 0;
            $totalCoefficients = 0;

            foreach ($sequenceMarks as $mark) {
                $subjectId = $mark->subject_id;
                
                // Skip if no coefficient is defined
                if (!isset($classSubjectCoefficients[$subjectId])) {
                    continue;
                }

                $coefficient = $classSubjectCoefficients[$subjectId];
                $obtainedMark = ($mark->total_obtained_marks * 20) / $mark->total_marks; // Convert to /20

                // Calculate weighted mark (mark × coefficient)
                $weightedTotal += $obtainedMark * $coefficient;
                $totalCoefficients += $coefficient;
            }

            // Calculate sequence average (total weighted marks / sum of coefficients)
            $sequenceAverage = $totalCoefficients > 0 ? $weightedTotal / $totalCoefficients : 0;

            $examSequenceReport[] = [
                'class_section_id' => $classSection->id,
                'student_id' => $data->student_id,
                'exam_term_id' => $data->exam_term_id,
                'exam_sequence_id' => $data->exam_sequence_id,
                'total' => round($weightedTotal, 2),
                'avg' => round($sequenceAverage, 2),
                'total_coef' => $totalCoefficients,
                'rank' => -1
            ];
        }
    }
    
    

    public function groupings($subjectIds ,$exMarkSubjectSequenceStudentGrouped, $examReportID, $classSection, &$examReportStudentSubject, &$examSequenceReport): void
    {
        foreach ($exMarkSubjectSequenceStudentGrouped as $studentMarks) {

            $this->groupBySubject($studentMarks, $examReportID, $examReportStudentSubject);

            $this->groupBySequence($subjectIds ,$studentMarks, $classSection, $examSequenceReport);
        }
    }

    public function updateStudentSequenceReport($classSectionId, $studentIDs, $examSequenceReport, $termID, $minReq, $sequences, $sessionYearId): void
    {
        // deleting the student sequence report for students all the students who don't belong to the list of students for this class section, in this year.

        // this is for deleting the students who don't have any sequence information for this class.
        // this line just performs basic clean up for that class.
        ExamReportStudentSequence::query()
            ->where('class_section_id', $classSectionId)
            ->whereHas('examTerm', function ($query) use ($sessionYearId) {
                $query->where('session_year_id', $sessionYearId); // just me ensuring that i'm doing this for the correct session year.
            })
            ->whereNotIn('student_id', $studentIDs)
            ->delete();

        ExamReportStudentSequence::query()->upsert($examSequenceReport, ['class_section_id', 'student_id', 'exam_term_id', 'exam_sequence_id'], ['total', 'total_coef', 'rank', 'avg']);

        foreach ($sequences as $sequence) {
            $examReportStudentSequence = ExamReportStudentSequence::query()->where([
                'class_section_id' => $classSectionId,
                'exam_term_id' => $termID,
                'exam_sequence_id' => $sequence
            ])->where('total_coef', '>=', $minReq)
                ->orderByDesc('avg')->orderByDesc('total') // Tie-break, in case we have two with the same logic
                ->get();

            $sequenceWiseRank = [];

            foreach ($examReportStudentSequence as $key => $studentSequence) {
                $sequenceWiseRank[] = [
                    'id' => $studentSequence->id,
                    'rank' => ($key + 1)
                ];
            }
            ExamReportStudentSequence::query()->upsert($sequenceWiseRank, ['id'], ['rank']);
        }
    }

    public function createClassSectionSubjectDetails($examReportID, $classSectionID, $sessionYearId): void
    {
        // fetching all the students that belong to that session year and that class.
        $studentIDs = Students::owner()
            ->where('class_section_id', $classSectionID)
            ->whereHas('studentSessions', function ($query) use ($sessionYearId, $classSectionID) {
                $query->where('class_section_id', $classSectionID);
                $query->where('session_year_id', $sessionYearId);
            })
            ->pluck('id');

        $subjectMinMax = ExamReportStudentSubject::query()
            ->where('exam_report_id', $examReportID)
            ->whereIn('student_id', $studentIDs)
            ->where('subject_total', '>', '-1')
            ->select(DB::raw('*,subject_id,MIN(subject_avg) as min,MAX(subject_avg) as max'))
            ->groupBy('subject_id')->get();

        $examReportClassSubject = [];
        $subjectRanks = [];

        foreach ($subjectMinMax as $row) {
            // creating the examReportClassSubject records.
            $examReportClassSubject[] = array(
                'exam_report_id' => $examReportID,
                'class_section_id' => $classSectionID,
                'subject_id' => $row->subject_id,
                'min' => $row->min,
                'max' => $row->max,
            );

            // -1 for the subject totals is going to mean that it's not a valid or set record.
            $studentSubjectWiseMarks = ExamReportStudentSubject::query()
                ->where(['exam_report_id' => $examReportID, 'subject_id' => $row->subject_id])
                ->whereIn('student_id', $studentIDs)
                ->where('subject_total', '>', '-1')
                ->orderBy('subject_avg', 'desc')->get();

            foreach ($studentSubjectWiseMarks as $key => $studentSubject) {
                $subjectRanks[] = [
                    'id' => $studentSubject->id,
                    'subject_rank' => ($key + 1)
                ];
            }
        }

        ExamReportStudentSubject::query()->upsert($subjectRanks, ['id'], ['subject_rank']);

        ExamReportClassSubject::query()->upsert($examReportClassSubject, ['exam_report_id', 'class_section_id', 'subject_id'], ['min', 'max']);
    }


    public function calculateTotalAvgOfEachStudent($examReportID, $classSectionID, $subjectIds, $total_class_coef, $sessionYearId): void
{
    $classSection = $this->getClassSection($classSectionID);

    $studentIds = Students::owner()
        ->whereHas('studentSessions', function ($query) use ($sessionYearId, $classSectionID) {
            $query->where('class_section_id', $classSectionID)
                ->where('session_year_id', $sessionYearId);
        })
        ->pluck('id');

    $minRequirement = (int) (ExamReportService::MINIMUM_PASS_PERCENTAGE * 0.01 * $total_class_coef * 2);

    $classSubjectsAndWeightages = ClassSubject::query()
        ->where(['class_id' => $classSection->class_id])
        ->pluck('weightage', 'subject_id')->toArray();

    $studentSubjectWiseMarks = ExamReportStudentSubject::query()
        ->where(['exam_report_id' => $examReportID])
        ->whereIn('subject_id', $subjectIds)
        ->whereIn('student_id', $studentIds)
        ->whereHas('examReport', function ($query) use ($sessionYearId) {
            $query->where("session_year_id", $sessionYearId);
        })
        ->where('subject_total', '>', '-1')
        ->get()->groupBy('student_id');

    foreach ($studentSubjectWiseMarks as $studentId => $student) {
        $totalSequenceAverage = 0;
        $totalSequences = 0;

        foreach ($student as $subject) {
            // Check if class subject weightage exists
            if (!isset($classSubjectsAndWeightages[$subject->subject_id])) {
                Log::warning("Missing subject coefficient", [
                    'subject_id' => $subject->subject_id,
                    'student_id' => $studentId
                ]);
                continue;
            }

            // Calculate sequence average
            $sequenceMarks = get_object_vars($subject->sequence_marks);
            $sequenceCount = count($sequenceMarks);
            
            if ($sequenceCount > 0) {
                $sequenceAverage = array_sum($sequenceMarks) / $sequenceCount;
                
                // Weighted by subject weightage and number of sequences
                $totalSequenceAverage += $sequenceAverage * $classSubjectsAndWeightages[$subject->subject_id];
                $totalSequences += $classSubjectsAndWeightages[$subject->subject_id];
            }
        }

        if ($totalSequences == 0) {
            ExamReportClassDetails::query()->updateOrCreate(
                ['exam_report_id' => $examReportID, 'student_id' => $studentId],
                ['avg' => 0, 'rank' => -1, 'total_coef' => 0]
            );
        } else {
            $finalAverage = round($totalSequenceAverage / $totalSequences, 2);            
            ExamReportClassDetails::query()->updateOrCreate(
                ['exam_report_id' => $examReportID, 'student_id' => $studentId],
                [
                    'avg' => $finalAverage,
                    'total_coef' => $totalSequences,
                    'rank' => -1
                ]
            );
        }
    }

    $examReportClass = ExamReportClassDetails::query()
        ->where('exam_report_id', $examReportID)
        ->whereHas('exam_report', function ($query) use ($sessionYearId) {
            $query->where("session_year_id", $sessionYearId);
        })
        ->whereIn('student_id', $studentIds)
        ->where('total_coef', '>=', $minRequirement)
        ->orderBy('avg', 'desc')->get();

    $updateRank = [];

    foreach ($examReportClass as $key2 => $row) {
        $updateRank[] = [
            'id' => $row->id,
            'rank' => ($key2 + 1)
        ];
    }

    ExamReportClassDetails::query()->upsert($updateRank, ['id'], ['rank']);
}

    

    private function getClassSection($classSectionId): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null
    {
        return ClassSection::with(['teacher.user'])->findOrFail($classSectionId);
    }

    private function getTermExamTimetables($request, $sessionYearId, $classSectionId): \Illuminate\Database\Eloquent\Collection|array
    {
        // get the exam timetables for a particular class and session year id
        return ExamTimetable::query()
            ->where([
                'class_section_id' => $classSectionId,
                'session_year_id' => $sessionYearId,
                'marks_upload_status' => 1
            ])
            ->whereHas('exam', function ($query) use ($sessionYearId, $request) {
                $query->owner()
                    ->where([
                        'exam_term_id' => $request->term_id,
                        'session_year_id' => $sessionYearId,
                        'type' => 1
                    ]);
            })->get();
    }

    private function getExamMarks($examTimetables, $sessionYearId, $classSection): \Illuminate\Database\Eloquent\Collection|array
    {
        // filter for just the students that belong to that particular class.
        // making sure that the exams belong to only who are in this class and for the particular session year which was specified.
        return ExamMarks::owner()
            ->where('obtained_marks', '>', -1)
            ->where('session_year_id', $sessionYearId)
            ->whereIn('exam_timetable_id', $examTimetables->pluck('id'))
            ->whereHas('student', function ($query) use ($sessionYearId, $classSection)  {
                $query->whereHas('studentSessions', function ($query) use ($sessionYearId, $classSection) {
                    $query->where('class_section_id', $classSection->id)
                        ->where('session_year_id', $sessionYearId);
                });
            })
            ->whereHas('timetable', function ($query) use ($sessionYearId, $classSection) {
                $query->where('class_section_id', $classSection->id)
                    ->where('session_year_id', $sessionYearId);
            })
            ->get();
    }

    private function getClassSubjects($classId, $subjectIds): \Illuminate\Database\Eloquent\Collection|array
    {
        return ClassSubject::query()
            ->whereIn('subject_id', $subjectIds)
            ->where('class_id', $classId)
            ->get();
    }

    private function calculateClassAverage($examMarks, $studentIds, $examTimetables): float|int
    {
        $totalObtainedMarks = $examMarks->sum('obtained_marks');
        $totalPossibleMarks = $examTimetables->sum('total_marks') * count($studentIds);
        return ($totalObtainedMarks * 20) / $totalPossibleMarks;
    }

    private function createOrUpdateReport($sessionYearId, $classSection, $termId, $reportData): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {
//         Remove an existing report if any
        $existingReport = ExamReport::query()
            ->where([
                'class_section_id' => $classSection->id,
                'exam_term_id' => $termId,
                'session_year_id' => $sessionYearId
            ])->first();

        $existingReport?->delete();

        $studentCounts = $this->getStudentGenderCounts($classSection, $sessionYearId);

        return ExamReport::query()->updateOrCreate([
            'class_section_id' => $classSection->id,
            'exam_term_id' => $termId,
            'session_year_id' => $sessionYearId,
            'class_teacher_id' => $classSection->teacher->user->id ?? null,
            'male_students' => $studentCounts['male'],
            'female_students' => $studentCounts['female'],
            'total_students' => $studentCounts['total'],
            'avg' => $reportData['classAverage'],
            'total_coef' => $reportData['totalCoefficient'],
            'total_points' => $reportData['totalPoints']
        ]);
    }

    private function getStudentGenderCounts($classSection, $sessionYearId): array
    {
        $students = Students::owner()->whereHas('studentSessions', function ($query) use ($sessionYearId, $classSection) {
                $query->where('class_section_id', $classSection->id)
                    ->where('session_year_id', $sessionYearId);
            });

        $maleCount = $students->clone()->whereHas('user', function ($query) {
                $query->whereIn('gender', ['male', 'M']);
            })->count();

        $femaleCount = $students->whereHas('user', function ($query) {
                $query->whereIn('gender', ['female', 'F']);
            })->count();

        return [
            'male' => $maleCount,
            'female' => $femaleCount,
            'total' => $maleCount + $femaleCount
        ];
    }

    // Calculate subject average
    private function calculateSubjectAverage($totalObtainedMarks, $totalMarks, $sequenceCount, $sequences): float|int
    {
        // subjectWiseTotal is like the totalMarks here
        //
        if (!$totalMarks || !$sequenceCount) {
            return 0;
        }

        if ($sequenceCount > $sequences) {
            return ($totalObtainedMarks * 20) / (  ($totalMarks / $sequenceCount) * $sequences);
        }

        return ($totalObtainedMarks * 20) / $totalMarks;
    }

    private function processEmptyExamMarks($examReportID, $examMarksSubjectWise, $subjectSequenceCount): void
    {
        foreach ($examMarksSubjectWise as $row) {

            if ($row->seqs < $subjectSequenceCount[$row->subject_id]) continue;

            ExamReportStudentSubject::query()->updateOrCreate(
                ['exam_report_id' => $examReportID, 'student_id' => $row->student_id, 'subject_id' => $row->subject_id],
                [
                    'subject_total' => -1,
                    'subject_rank' => -1,
                    'subject_avg' => -1,
                    'subject_grade' => '',
                    'subject_remarks' => '',
                    'sequence_marks' => json_encode((object)[]),
                ]
            );
        }
    }
}