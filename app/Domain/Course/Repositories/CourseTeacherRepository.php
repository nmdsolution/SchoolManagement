<?php

namespace App\Domain\Course\Repositories;

use App\Models\Course;
use App\Models\CourseTeacher;
use App\Repositories\BaseRepository;

// CourseTeacherRepository
class CourseTeacherRepository extends BaseRepository
{
    public function __construct(CourseTeacher $model)
    {
        parent::__construct($model);
    }

    public function updateTeachers(Course $course, array $teacherIds): void
    {
        $this->model->where('course_id', $course->id)->delete();
        
        $teachers = array_map(function($teacherId) use ($course) {
            return [
                'course_id' => $course->id,
                'user_id' => $teacherId
            ];
        }, $teacherIds);

        $this->insertMany($teachers);
    }

    public function getByCourseId(int $id): ?CourseTeacher
    {
        return $this->model->where('course_id', $id)->first();
    }
}