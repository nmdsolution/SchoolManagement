<?php

namespace App\Services;

use App\Domain\Competency\Repositories\SubjectCompetencyRepository;
use App\Domain\Exam\Repositories\ExamMarksRepository;
use App\Domain\Exam\Repositories\ExamResultRepository;
use App\Domain\Exam\Repositories\ExamTimetableRepository;
use App\Models\ExamMarks;
use App\Models\ExamTimetable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ExamMarksService
{
    public function __construct(
        private ExamTimetableRepository $timetableRepository,
        private SubjectCompetencyRepository $competencyRepository,
        private ExamMarksRepository $marksRepository,
        private ExamResultRepository $resultRepository
    ) {}

    /**
     * Get exam marks for a specific class section, session year, and term,
     * optionally filtering by sequence IDs.
     *
     * @param int $classSectionId
     * @param int $sessionYearId
     * @param int $termId
     * @param array|null $sequenceIds
     * @return Collection
     */
    public function getExamMarks(int $classSectionId, int $sessionYearId, int $termId, ?array $sequenceIds = null): Collection
    {
        return ExamMarks::query()
            ->where('session_year_id', $sessionYearId)
            ->where('obtained_marks', '>', '-1') // Assuming -1 indicates no mark entered
            ->whereHas('timetable', function ($q) use ($classSectionId, $termId, $sequenceIds) {
                $q->where(['marks_upload_status' => 1, 'class_section_id' => $classSectionId]);
                $q->whereHas('exam', function ($q) use ($termId, $sequenceIds) {
                    $q->owner()->where(['type' => 1, 'exam_term_id' => $termId]);
                    if ($sequenceIds) {
                        $q->whereIn('exam_sequence_id', $sequenceIds);
                    }
                });
            })
            ->get();
    }

    /**
     * Calculate the total obtained marks for a collection of exam marks.
     *
     * @param Collection $examMarks
     * @return int
     */
    public function calculateTotalObtainedMarks(Collection $examMarks): int
    {
        return $examMarks->sum('obtained_marks');
    }

    /**
     * Get the total marks for a specific class section, session year, term, and subject IDs.
     *
     * @param int $classSectionId
     * @param int $sessionYearId
     * @param int $termId
     * @param array $subjectIds
     * @return Collection
     */
    public function getTotalMarksBySubject(int $classSectionId, int $sessionYearId, int $termId, array $subjectIds): Collection
    {
        return ExamTimetable::query()
            ->where(['session_year_id' => $sessionYearId, 'marks_upload_status' => 1, 'class_section_id' => $classSectionId])
            ->whereHas('exam', function ($q) use ($termId) {
                $q->owner()->where(['type' => 1, 'exam_term_id' => $termId]);
            })
            ->whereIn('subject_id', $subjectIds)
            ->select(DB::raw('subject_id, count(id) as sequence_count, SUM(total_marks) as total_marks'))
            ->groupBy('subject_id')
            ->get();
    }

    public function submitMarks(array $data): void
    {
        DB::transaction(function () use ($data) {
            $timetable = $this->timetableRepository->findByExamAndSection(
                $data['exam_id'],
                $data['class_section_id'],
                $data['subject_id']
            );

            $this->handleSubjectCompetency($data);
            
            $examResults = [];
            foreach ($data['exam_marks'] as $examMark) {
                if ($examMark['obtained_marks'] === '/') {
                    $this->handleAbsentStudent($examMark, $timetable);
                    continue;
                }

                $status = $this->calculatePassingStatus($examMark, $timetable);
                $percentage = $this->calculatePercentage($examMark);
                $grade = $this->findGrade($percentage);

                $this->marksRepository->updateOrCreateMarks(
                    $timetable,
                    $examMark,
                    $status,
                    $grade,
                    $data['subject_id']
                );

                if ($this->shouldPublishResults($data, $timetable)) {
                    $examResults[] = $this->prepareExamResult(
                        $data,
                        $examMark,
                        $percentage,
                        $grade,
                        $timetable
                    );
                }
            }

            $this->updateTimetableStatus($timetable, $data['marks_upload_status']);
            
            if (!empty($examResults)) {
                $this->resultRepository->upsertResults($examResults);
            }

            event(new MarksUploadedEvent($data));
        });
    }

    private function handleSubjectCompetency(array $data): void
    {
        if (!empty($data['subject_competency'])) {
            $this->competencyRepository->updateOrCreateCompetency([
                'exam_sequence_id' => $data['sequence_id'],
                'exam_id' => $data['exam_id'],
                'subject_id' => $data['subject_id'],
                'class_section_id' => $data['class_section_id'],
                'competence' => $data['subject_competency']
            ]);
        }
    }

    private function handleAbsentStudent(array $mark, ExamTimetable $timetable): void
    {
        $this->marksRepository->updateOrCreateMarks(
            $timetable,
            $mark,
            0,
            null,
            -1 // Marks for absent student
        );
    }

    private function calculatePassingStatus(array $mark, ExamTimetable $timetable): int
    {
        return $mark['obtained_marks'] >= $timetable->passing_marks ? 1 : 0;
    }

    private function calculatePercentage(array $mark): float
    {
        if ($mark['total_marks'] == 0) return 0;
        return ($mark['obtained_marks'] / $mark['total_marks']) * 100;
    }

    private function findGrade(float $percentage): string
    {
        $grade = findExamGrade($percentage);
        if (!$grade) {
            throw new GradeNotFoundException(trans('grades_data_does_not_exists'));
        }
        return $grade;
    }

    private function shouldPublishResults(array $data, ExamTimetable $timetable): bool
    {
        if ($data['marks_upload_status'] != 1) return false;

        $autoPublish = getSettings('auto_publish_exams', get_center_id())['auto_publish_exams'] ?? 0;
        return $timetable->exam->type == 1 && $autoPublish;
    }

    private function prepareExamResult(
        array $data,
        array $mark,
        float $percentage,
        string $grade,
        ExamTimetable $timetable
    ): array {
        return [
            'exam_id' => $data['exam_id'],
            'class_section_id' => $data['class_section_id'],
            'student_id' => $mark['student_id'],
            'total_marks' => $mark['total_marks'],
            'obtained_marks' => $mark['obtained_marks'],
            'percentage' => round($percentage, 2),
            'grade' => $grade,
            'session_year_id' => $timetable->session_year_id
        ];
    }
}