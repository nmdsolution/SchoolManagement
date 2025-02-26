<?php

namespace App\Domain\Assignment\Repositories;

use App\Models\Assignment;
use App\Models\File;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AssignmentRepository extends BaseRepository
{
    public function __construct(Assignment $assignment)
    {
        parent::__construct($assignment);
    }

    public function getPaginatedAssignments(array $filters, array $pagination): Collection
    {
        $query = $this->buildAssignmentQuery($filters);

        return $query->orderBy($pagination['sort'], $pagination['order'])
            ->skip($pagination['offset'])
            ->take($pagination['limit'])
            ->get();
    }

    public function getTotalCount(array $filters): int
    {
        return $this->buildAssignmentQuery($filters)->count();
    }

    private function buildAssignmentQuery(array $filters): Builder
    {
        $query = Assignment::assignmentteachers()
            ->with('class_section', 'file', 'subject');

        if ($filters['isTeacher']) {
            $this->addTeacherCenterFilter($query, $filters['center_id']);
        }

        if (isset($filters['search'])) {
            $this->addSearchFilters($query, $filters['search']);
        }

        $this->addSpecificFilters($query, $filters);

        return $query;
    }

    private function addTeacherCenterFilter($query, $centerId): void
    {
        $query->whereHas('class_section.class', function ($q) use ($centerId) {
            $q->where('center_id', session()->get('center_id'));
        });
    }

    private function addSearchFilters($query, $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('id', 'LIKE', "%$search%")
                ->orWhere('name', 'LIKE', "%$search%")
                ->orWhere('instructions', 'LIKE', "%$search%")
                ->orWhere('points', 'LIKE', "%$search%")
                ->orWhere('session_year_id', 'LIKE', "%$search%")
                ->orWhere('extra_days_for_resubmission', 'LIKE', "%$search%")
                ->orWhere('due_date', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                ->orWhere('created_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                ->orWhere('updated_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                ->orWhereHas('class_section.class', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('class_section.section', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('subject', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                });
        });
    }

    private function addSpecificFilters($query, array $filters): void
    {
        if (!empty($filters['center_id'])) {
            $query->whereHas('class_section.class', function ($q) use ($filters) {
                $q->where('center_id', $filters['center_id']);
            });
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_section_id', $filters['class_id']);
        }

        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }
    }

    public function findById(int $id): ?Assignment
    {
        return Assignment::findOrFail($id);
    }

    public function attachFiles(Assignment $assignment, array $files): void
    {
        foreach ($files as $uploadedFile) {
            File::create([
                'file_name' => $uploadedFile->getClientOriginalName(),
                'type' => 1,
                'file_url' => $uploadedFile->store('assignment', 'public'),
                'modal_type' => Assignment::class,
                'modal_id' => $assignment->id
            ]);
        }
    }

    public function getAssignmentWithSubject(int $id): Assignment
    {
        return Assignment::where('id', $id)
            ->with('subject')
            ->firstOrFail();
    }
}