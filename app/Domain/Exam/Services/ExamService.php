<?php

namespace App\Domain\Exam\Services;

use App\Domain\Exam\Repositories\ExamRepository;
use App\Domain\Exam\Repositories\ExamTimetableRepository;
use App\Models\AutoSequenceExam;
use App\Models\ClassSection;
use App\Models\Exam;
use App\Models\ExamClassSection;
use App\Models\ExamSequence;
use App\Models\ExamTimetable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExamService
{
    public function __construct(
        private ExamRepository $examRepository,
        private ExamTimetableRepository $examTimetableRepository
        )
    {
        
    }

    /**
     * @param $examTermID
     * @param array|null $sequenceName
     * @return bool
     * @throws Throwable
     */
    public static function createDummyExamSequence($examTermID, array $sequenceName = null): bool
    {
        try {
            DB::beginTransaction();

            if (!empty($sequenceName)) {
                foreach ($sequenceName as $sequence) {
                    $data[] = [
                        'name'         => $sequence,
                        'exam_term_id' => $examTermID,
                        'center_id'    => Auth::user()->center->id,
                    ];
                }
            } else {
                $data = [
                    [
                        'name'         => 'Seq 1',
                        'exam_term_id' => $examTermID,
                        'center_id'    => Auth::user()->center->id,
                    ],
                    [
                        'name'         => 'Seq 2',
                        'exam_term_id' => $examTermID,
                        'center_id'    => Auth::user()->center->id,
                    ]
                ];
            }
            ExamSequence::query()->insert($data);
            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param array $TermSequence array [
     * <pre>
     *  [
     *      term_id=>1, Required
     *      sequence_id=>1 Required
     *  ],
     *  [
     *      term_id=>1, Required
     *      sequence_id=>1 Required
     *  ],
     * ]
     * @param array|null $classSectionID
     * @return bool
     * @throws Throwable
     */
    public static function createDummyExams(array $TermSequence, array $classSectionID = null) {
        try {
            DB::beginTransaction();
            $classSections = ClassSection::with('subjects:subjects.id,name');
            if (!empty($classSectionID)) {
                $classSections->whereIn('id', $classSectionID);
            }
            $autoSequenceExam = [];
            $classSections = $classSections->get();
            $examClassSection = $examTimetables = [];
            $setting = getSettings('session_year');
            foreach ($TermSequence as $data) {
                foreach ($classSections as $classSection) {
                    foreach ($classSection->subjects as $subject) {
                        $exam = Exam::create([
                            'name'             => $subject->name . " Exam",
                            'type'             => 1,
                            'session_year_id'  => $setting['session_year'],
                            'exam_term_id'     => $data['term_id'],
                            'exam_sequence_id' => $data['sequence_id'],
                            'center_id'        => Auth::user()->center->id
                        ]);

                        $examClassSection[] = [
                            'exam_id'          => $exam->id,
                            'class_section_id' => $classSection->id
                        ];

                        $examTimetables[] = [
                            'exam_id'          => $exam->id,
                            'class_section_id' => $classSection->id,
                            'subject_id'       => $subject->id,
                            'session_year_id'  => $setting['session_year'],
                            'total_marks'      => 20,
                            'passing_marks'    => 10,
                        ];

                    }

                    $autoSequenceExam[] = [
                        'class_section_id' => $classSection->id,
                        'exam_sequence_id' => $data['sequence_id']
                    ];
                }
            }
            ExamClassSection::insert($examClassSection);
            ExamTimetable::insert($examTimetables);
            AutoSequenceExam::insert($autoSequenceExam);
            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createExamTimetable(Request $request, Exam $exam): ?ExamTimetable
    {
        $data = [
            'exam_id'          => $exam->id,
            'class_section_id' => $request->class_section_id,
            'subject_id'       => $request->timetable_subject_id,
            'total_marks'      => 20,
            'passing_marks'    => 10,
            'session_year_id'  => getSessionYearData()->id,
        ];
        return $this->examTimetableRepository->create($data);
    }

    public function getCompletedExamsForMarksUpload()
    {
        $examData = $this->examRepository->getUnpublishedCompletedExams();
        $completedExams = [];

        foreach ($examData as $exam) {
            if ($this->isExamCompleted($exam)) {
                $completedExams[] = $exam;
            }
        }

        return $completedExams;
    }

    private function isExamCompleted($exam): bool
    {
        $dates = $this->examRepository->getExamDates(
            $exam->exam_id,
            $exam->class_section_id
        );

        $examStatus = $this->calculateExamStatus(
            $dates['start_date'],
            $dates['end_date']
        );

        return $examStatus === "2";
    }

    private function calculateExamStatus(string $startDate, string $endDate): string
    {
        $currentDate = Carbon::now()->toDateString();

        if ($currentDate > $startDate && $currentDate < $endDate) {
            return "1"; // On Going
        }
        return $currentDate < $startDate ? "0" : "2"; // Upcoming : Completed
    }

    public function getSequentialExamData(?Request $request = null): array
    {
        $filters = $this->prepareFilters($request);
        $exams = $this->examRepository->getSequentialExams($filters);

        if ($request?->ajax()) {
            return ['exams' => $exams];
        }

        return [
            'exams' => $exams,
            'class_sections' => $this->examRepository->getClassSections(),
            'sequences' => $this->examRepository->getActiveSequences()
        ];
    }

    private function prepareFilters(?Request $request): array
    {
        $filters = [];

        if (Auth::user()->hasRole('Teacher')) {
            $filters['teacher_id'] = Auth::user()->teacher->id;
        }

        if ($request) {
            if ($request->sequence_id) {
                $filters['sequence_id'] = $request->sequence_id;
            }
            if ($request->class_id) {
                $filters['class_id'] = $request->class_id;
            }
        }

        return $filters;
    }

}