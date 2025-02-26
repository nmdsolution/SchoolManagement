<?php

namespace App\Domain\Exam\Services;

use App\Domain\Class\Repositories\ClassSectionRepository;
use App\Domain\Exam\Exporters\ExamResultPdfExporter;
use App\Domain\Exam\Repositories\ExamMarksRepository;
use App\Domain\Exam\Repositories\ExamRepository;
use App\Domain\Exam\Repositories\ExamResultRepository;
use App\Models\ExamMarks;
use Illuminate\Http\Response;

class ExamResultService
{
    public function __construct(
        private ExamResultRepository $resultRepository,
        private ExamRepository $examRepository,
        private ClassSectionRepository $classSectionRepository,
        private ExamResultPdfExporter $pdfExporter,
        private ExamMarksRepository $examMarksRepository
    ) {}

    public function getExamResults(array $params): array|Response
    {
        $data = $this->resultRepository->getExamResults($params);

        if (!empty($params['print'])) {
            $exam = $this->examRepository->getByIdOrFail($params['exam_id']);
            $classSection = !empty($params['class_section_id']) 
                ? $this->classSectionRepository->getById($params['class_section_id'])
                : null;

            return $this->pdfExporter->exportResults($data['rows'], $exam, $classSection);
        }

        return $data;
    }

    public function updateResultMarks(array $marksData): void
    {
        DB::transaction(function () use ($marksData) {
            foreach ($marksData['edit'] as $data) {
                $examMark = $this->updateExamMark($data);
                $this->updateExamResult($data, $examMark);
            }
        });
    }

    private function updateExamMark(array $data): ExamMarks
    {
        $percentage = ($data['obtained_marks'] / $data['total_marks']) * 100;
        $grade = $this->findGradeOrFail($percentage);
        
        return $this->examMarksRepository->updateMark(
            $data['marks_id'],
            $data['obtained_marks'],
            $data['passing_marks'],
            $grade
        );
    }

    private function updateExamResult(array $data, ExamMarks $mark): void
    {
        $exam = $this->examRepository->getExamWithMarksAndTimetable(
            $data['exam_id'],
            $data['student_id'],
            $mark->timetable->class_section_id
        );

        foreach ($exam->marks as $examMark) {
            $percentage = $this->calculatePercentage(
                $examMark['total_obtained_marks'],
                $exam->timetable[0]['total_marks']
            );

            $grade = $this->findGradeOrFail($percentage);

            $this->resultRepository->updateResult(
                $data['exam_id'],
                $data['student_id'],
                $examMark['total_obtained_marks'],
                $percentage,
                $grade
            );
        }
    }

    private function calculatePercentage(float $obtainedMarks, float $totalMarks): float
    {
        if ($totalMarks === 0) {
            throw new DivisionByZeroException('Le total des notes ne peut pas être zéro');
        }
        return ($obtainedMarks * 100) / $totalMarks;
    }

    private function findGradeOrFail(float $percentage): string
    {
        $grade = findExamGrade($percentage);
        if (!$grade) {
            throw new GradeNotFoundException(trans('grades_data_does_not_exists'));
        }
        return $grade;
    }
}