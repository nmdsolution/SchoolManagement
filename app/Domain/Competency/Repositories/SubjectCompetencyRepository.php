<?php

namespace App\Domain\Competency\Repositories;

use App\Models\SubjectCompetency;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class SubjectCompetencyRepository extends BaseRepository
{
    public function __construct(SubjectCompetency $subjectCompetency)
    {
        parent::__construct($subjectCompetency);
    }

    public function findByExamAndSubject(int $examId, int $subjectId): ?SubjectCompetency
    {
        return $this->model
            ->where([
                'exam_id' => $examId,
                'subject_id' => $subjectId
            ])
            ->first();
    }

    public function updateOrCreateCompetency(array $data): SubjectCompetency
    {
        $conditions = [
            'exam_sequence_id' => $data['exam_sequence_id'],
            'exam_id' => $data['exam_id'],
            'subject_id' => $data['subject_id'],
            'class_section_id' => $data['class_section_id']
        ];

        $values = [
            'competence' => $data['competence']
        ];

        return $this->model->updateOrCreate($conditions, $values);
    }

    public function getCompetencies(int $examId, int $subjectId): Collection
    {
        return $this->model
            ->where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->get();
    }
}