<?php

namespace App\Domain\Exam\Repositories;

use App\Models\ExamMarks;
use App\Models\ExamTimetable;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ExamMarksRepository extends BaseRepository
{
    public function __construct(ExamMarks $examMarks)
    {
        parent::__construct($examMarks);
    }

    public function getTotalAttemptStudents(array $timetableIds, int $sessionYearId): int
    {
        return $this->model->whereIn('exam_timetable_id', $timetableIds)
            ->where('session_year_id', $sessionYearId)
            ->groupBy('student_id')
            ->count();
    }

    public function getTotalFailStudents(array $timetableIds, int $sessionYearId): int
    {
        return $this->model->whereIn('exam_timetable_id', $timetableIds)
            ->where('session_year_id', $sessionYearId)
            ->where('passing_status', 0)
            ->groupBy('student_id')
            ->count();
    }

    public function updateMark(
        int $markId, 
        float $obtainedMarks, 
        float $passingMarks,
        string $grade
    ): ExamMarks {
        $mark = $this->getByIdOrFail($markId);
        
        $this->update([
            'obtained_marks' => $obtainedMarks,
            'passing_status' => $obtainedMarks >= $passingMarks ? 1 : 0,
            'grade' => $grade
        ], $markId);

        return $mark->fresh();
    }

    public function updateOrCreateMarks(
        ExamTimetable $timetable,
        array $markData,
        int $status,
        ?string $grade,
        int $subjectId
    ): ExamMarks {
        $conditions = [
            'exam_timetable_id' => $timetable->id,
            'student_id' => $markData['student_id'],
            'subject_id' => $subjectId
        ];

        $values = [
            'obtained_marks' => $markData['obtained_marks'] === '/' ? -1 : $markData['obtained_marks'],
            'passing_status' => $status,
            'session_year_id' => $timetable->session_year_id,
            'grade' => $grade
        ];

        return $this->model->updateOrCreate($conditions, $values);
    }

    /**
     * Met à jour ou crée plusieurs notes d'examen en une seule fois
     */
    public function upsertMany(array $marksData): void
    {
        $uniqueKeys = ['exam_timetable_id', 'student_id', 'subject_id'];
        $updateColumns = ['obtained_marks', 'passing_status', 'session_year_id', 'grade'];

        $this->model->upsert($marksData, $uniqueKeys, $updateColumns);
    }

    /**
     * Récupère les notes d'un étudiant pour un examen spécifique
     */
    public function getStudentMarks(int $studentId, int $examTimetableId): ?ExamMarks
    {
        return $this->model
            ->where('student_id', $studentId)
            ->where('exam_timetable_id', $examTimetableId)
            ->first();
    }

    /**
     * Récupère toutes les notes pour un examen spécifique
     */
    public function getExamMarks(int $examTimetableId): Collection
    {
        return $this->model
            ->where('exam_timetable_id', $examTimetableId)
            ->with(['student', 'examTimetable'])
            ->get();
    }
}