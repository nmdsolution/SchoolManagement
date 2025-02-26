<?php


namespace App\Domain\Teacher\Services;

use App\Domain\Course\Repositories\CourseRepository;

class SuperTeacherCourseService
{
    public function __construct(
        private CourseRepository $courseRepository
    ) {}

    public function getTeacherCourses(array $params): array
    {
        return $this->courseRepository->getSuperTeacherCourses(
            auth()->id(),
            $params
        );
    }
}