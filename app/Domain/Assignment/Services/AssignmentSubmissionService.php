<?php

namespace App\Domain\Assignment\Services;

use App\Domain\Assignment\Repositories\AssignmentRepository;
use App\Domain\Assignment\Repositories\AssignmentSubmissionRepository;
use App\Domain\Student\Repositories\StudentsRepository;
use App\Http\Requests\Assignment\UpdateAssignmentSubmissionRequest;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Printing\StudentPrints;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AssignmentSubmissionService
{
    public function __construct(
        private AssignmentSubmissionRepository $submissionRepository,
        private AssignmentRepository $assignmentRepository,
        private StudentsRepository $studentsRepository
        )
    {
        $this->submissionRepository = $submissionRepository;
    }

    public function getSubmissionsList(Request $request): array|Response
    {
        $pagination = $this->getPaginationParams($request);
        $filters = $this->getFilterParams($request);

        $total = $this->submissionRepository->getTotalCount($filters);
        $submissions = $this->submissionRepository->getPaginatedSubmissions($filters, $pagination);

        $rows = $this->formatSubmissionData($submissions);

        if ($request->get('print')) {
            return $this->generatePDF($request, $rows);
        }

        return [
            'total' => $total,
            'rows' => $rows
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
            'class_section_id' => $request->class_section_id,
            'subject_id' => $request->subject_id,
            'isTeacher' => auth()->user()->teacher ? true : false
        ];
    }

    private function formatSubmissionData(Collection $submissions): array
    {
        $rows = [];
        $no = 1;

        foreach ($submissions as $submission) {
            $rows[] = [
                'id' => $submission->id,
                'no' => $no++,
                'assignment_id' => $submission->assignment_id,
                'assignment_name' => $submission->assignment->name,
                'assignment_points' => $submission->assignment->points,
                'subject' => $submission->assignment->subject->name . ' - ' . $submission->assignment->subject->type,
                'student_id' => $submission->student_id,
                'student_name' => $submission->student->user->full_name,
                'file' => $submission->file,
                'points' => $submission->points,
                'session_year_id' => $submission->session_year_id,
                'feedback' => $submission->feedback,
                'status' => $submission->status,
                'created_at' => $submission->created_at,
                'updated_at' => $submission->updated_at,
                'operate' => $this->generateOperateButtons($submission)
            ];
        }

        return $rows;
    }

    private function generateOperateButtons(AssignmentSubmission $submission): string
    {
        return "<a href='" . route('class.edit', $submission->id) . "' 
                   class='btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data' 
                   data-id='{$submission->id}' title='Edit' data-toggle='modal' data-target='#editModal'>
                   <i class='fa fa-edit'></i>
                </a>&nbsp;&nbsp;";
    }

    private function generatePDF(Request $request, array $rows): Response
    {
        $classSection = $request->class_section_id ? 
            ClassSection::find($request->class_section_id) : null;
        $subject = $request->subject_id ? 
            Subject::find($request->subject_id) : null;

        $pdf = StudentPrints::getInstance(get_center_id(), 'L');
        $pdf->printStudentAssignmentList($rows, $classSection, $subject);

        return response(
            $pdf->Output('', 'STUDENT LIST.pdf'),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function updateSubmission(int $id, UpdateAssignmentSubmissionRequest $request): array
    {
        try {
            $submission = $this->submissionRepository->findById($id);
            
            $updateData = $this->prepareUpdateData($request);
            $this->submissionRepository->update($submission, $updateData);

            $this->sendSubmissionNotification($submission, $request->status);

            return [
                'error' => false,
                'message' => trans('data_update_successfully')
            ];

        } catch (\Throwable $e) {
            return [
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            ];
        }
    }

    private function prepareUpdateData(UpdateAssignmentSubmissionRequest $request): array
    {
        return [
            'feedback' => $request->feedback,
            'points' => $request->status == 1 ? $request->points : null,
            'status' => $request->status
        ];
    }

    private function sendSubmissionNotification(AssignmentSubmission $submission, int $status): void
    {
        $assignment = $this->assignmentRepository->getAssignmentWithSubject($submission->assignment_id);
        $userIds = $this->studentsRepository->getStudentUserIds($submission->student_id);

        $notificationData = $this->prepareNotificationData($status, $assignment);

        send_notification(
            $userIds,
            $notificationData['title'],
            $notificationData['body'],
            'assignment'
        );
    }

    private function prepareNotificationData(int $status, Assignment $assignment): array
    {
        if ($status == 2) {
            return [
                'title' => 'Assignment rejected',
                'body' => "{$assignment->name} rejected in {$assignment->subject->name} subject"
            ];
        }

        return [
            'title' => 'Assignment accepted',
            'body' => "{$assignment->name} accepted in {$assignment->subject->name} subject"
        ];
    }
}