<?php

namespace App\Domain\Assignment\Repositories;

use App\Models\AssignmentSubmission;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AssignmentSubmissionRepository extends BaseRepository
{
    public function __construct(AssignmentSubmission $assignmentSubmission)
    {
        parent::__construct($assignmentSubmission);
    }

    public function getPaginatedSubmissions(array $filters, array $pagination): Collection
    {
        $query = $this->buildSubmissionQuery($filters);

        return $query->orderBy($pagination['sort'], $pagination['order'])
            ->skip($pagination['offset'])
            ->take($pagination['limit'])
            ->get();
    }

    public function getTotalCount(array $filters): int
    {
        return $this->buildSubmissionQuery($filters)->count();
    }

    private function buildSubmissionQuery(array $filters): Builder
    {
        $query = AssignmentSubmission::assignmentsubmissionteachers()
            ->with(['assignment.subject', 'student.user:first_name,last_name,id']);

        if ($filters['isTeacher']) {
            $this->addTeacherFilters($query);
        }

        if (isset($filters['search'])) {
            $this->addSearchFilters($query, $filters['search']);
        }

        $this->addSpecificFilters($query, $filters);

        return $query;
    }

    private function addTeacherFilters($query): void
    {
        $query->whereHas('assignment.subject', function ($q) {
            $q->where('center_id', session()->get('center_id'));
        });
    }

    private function addSearchFilters($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('id', 'LIKE', "%$search%")
                ->orWhere('session_year_id', 'LIKE', "%$search%")
                ->orWhere('created_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                ->orWhere('updated_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                ->orWhereHas('assignment.subject', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('assignment', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('student.user', function ($q) use ($search) {
                    $q->orWhereRaw("concat(users.first_name,' ',users.last_name) LIKE ?", ["%$search%"]);
                });
        })->whereHas('assignment.class_section.class', function ($q) {
            $q->where('center_id', session()->get('center_id'));
        });
    }

    private function addSpecificFilters($query, array $filters): void
    {
        if (!empty($filters['center_id'])) {
            $query->whereHas('assignment.class_section.class', function ($q) use ($filters) {
                $q->where('center_id', $filters['center_id']);
            });
        }

        if (!empty($filters['class_section_id'])) {
            $query->whereHas('assignment', function ($q) use ($filters) {
                $q->where('class_section_id', $filters['class_section_id']);
            });
        }

        if (!empty($filters['subject_id'])) {
            $query->whereHas('assignment', function ($q) use ($filters) {
                $q->where('subject_id', $filters['subject_id']);
            });
        }
    }
}
