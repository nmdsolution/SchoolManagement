<?php

namespace App\Domain\Exam\Repositories;

use App\Models\ExamTimetable;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ExamTimetableRepository extends BaseRepository
{
    public function __construct(ExamTimetable $examTimetable)
    {
        parent::__construct($examTimetable);
    }

    public function getByExamId(int $examId)
    {
        return $this->model->where('exam_id', $examId)
            ->with('exam_marks')
            ->get();
    }

    public function getTimetableIds(int $examId, int $classSectionId, int $sessionYearId)
    {
        return $this->model->where([
            'exam_id' => $examId,
            'session_year_id' => $sessionYearId,
            'class_section_id' => $classSectionId
        ])->pluck('id');
    }

    public function findByExamAndSection(int $examId, int $classSectionId, int $subjectId): ?ExamTimetable
    {
        return $this->model
            ->where([
                'exam_id' => $examId,
                'class_section_id' => $classSectionId,
                'subject_id' => $subjectId
            ])
            ->first();
    }

    public function getExamDates(int $examId, int $classSectionId): array
    {
        $dates = $this->model
            ->where([
                'exam_id' => $examId,
                'class_section_id' => $classSectionId
            ])
            ->selectRaw('MIN(date) as start_date, MAX(date) as end_date')
            ->first();

        return [
            'start_date' => $dates->start_date,
            'end_date' => $dates->end_date
        ];
    }

    public function updateTimetableStatus(int $timetableId, int $status): void
    {
        $this->update([
            'marks_upload_status' => $status
        ], $timetableId);
    }

    public function getTimetablesByExam(int $examId): Collection
    {
        return $this->model
            ->where('exam_id', $examId)
            ->with(['exam', 'classSection', 'subject'])
            ->get();
    }
}