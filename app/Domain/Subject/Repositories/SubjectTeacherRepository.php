<?php


namespace App\Domain\Subject\Repositories;

use App\Models\SubjectTeacher;
use App\Repositories\BaseRepository;

class SubjectTeacherRepository extends BaseRepository
{
    public function __construct(SubjectTeacher $subjectTeacher)
    {
        parent::__construct($subjectTeacher);
    }

    public function findTeacherSubjects(int $teacherId, int $classSectionId, int $subjectId)
    {
        return SubjectTeacher::where([
            'class_section_id' => $classSectionId,
            'teacher_id' => $teacherId,
            'subject_id' => $subjectId
        ])->with('subject')->get();
    }
}