<?php

namespace App\Domain\Assignment\Services;

use App\Domain\Assignment\Repositories\AssignmentRepository;
use App\Models\Assignment;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class AssignmentListService
{

    public function __construct(private AssignmentRepository $assignmentRepository)
    {
    }

    public function getAssignmentsList(Request $request): array
    {
        $pagination = $this->getPaginationParams($request);
        $filters = $this->getFilterParams($request);

        $total = $this->assignmentRepository->getTotalCount($filters);
        $assignments = $this->assignmentRepository->getPaginatedAssignments($filters, $pagination);

        return [
            'total' => $total,
            'rows' => $this->formatAssignmentData($assignments)
        ];
    }

    private function getPaginationParams(Request $request): array
    {
        return [
            'offset' => $request->get('offset', 0),
            'limit' => $request->get('limit', 10),
            'sort' => $request->get('sort', 'id'),
            'order' => $request->get('order', 'DESC')
        ];
    }

    private function getFilterParams(Request $request): array
    {
        return [
            'search' => $request->get('search'),
            'center_id' => $request->center_id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'isTeacher' => auth()->user()->teacher ? true : false
        ];
    }

    private function formatAssignmentData(Collection $assignments): array
    {
        $rows = [];
        $no = 1;

        foreach ($assignments as $assignment) {
            $rows[] = [
                'id' => $assignment->id,
                'no' => $no++,
                'class_section_id' => $assignment->class_section_id,
                'class_section_name' => $assignment->class_section->full_name,
                'subject_id' => $assignment->subject_id,
                'subject_name' => $this->formatSubjectName($assignment->subject),
                'name' => $assignment->name,
                'instructions' => $assignment->instructions,
                'file' => $assignment->file,
                'due_date' => $assignment->due_date,
                'points' => $assignment->points,
                'resubmission' => $assignment->resubmission,
                'extra_days_for_resubmission' => $assignment->extra_days_for_resubmission,
                'session_year_id' => $assignment->session_year_id,
                'center_id' => $assignment->class_section->class->center_id,
                'operate' => $this->generateOperateButtons($assignment)
            ];
        }

        return $rows;
    }

    private function formatSubjectName(?Subject $subject): string
    {
        return $subject ? $subject->name . ' - ' . $subject->type : '-';
    }

    private function generateOperateButtons(Assignment $assignment): string
    {
        $editUrl = route('assignment.edit', $assignment->id);
        $deleteUrl = route('assignment.destroy', $assignment->id);

        return "<a href='{$editUrl}' class='btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data' 
                   data-id='{$assignment->id}' title='Edit' data-toggle='modal' data-target='#editModal'>
                   <i class='fa fa-edit'></i>
                </a>&nbsp;&nbsp;
                <a href='{$deleteUrl}' class='btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form' 
                   data-id='{$assignment->id}'>
                   <i class='fa fa-trash'></i>
                </a>";
    }
}