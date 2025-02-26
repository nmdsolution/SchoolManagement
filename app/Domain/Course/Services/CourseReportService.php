<?php

namespace App\Domain\Course\Services;

use App\Domain\Course\Repositories\CourseStudentRepository;

class CourseReportService
{
    public function __construct(
        private CourseStudentRepository $courseStudentRepository
    ) {}

    public function generateReport(array $params): array
    {
        return $this->courseStudentRepository->getReportData($params);
    }
}