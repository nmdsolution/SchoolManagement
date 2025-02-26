<?php

namespace App\Domain\Assignment\Services;

use App\Domain\Assignment\Repositories\AssignmentRepository;
use App\Domain\Exam\Repositories\StudentRepository;
use App\Domain\Subject\Repositories\SubjectRepository;
use App\Http\Requests\Assignment\StoreAssignmentRequest;
use App\Http\Requests\Assignment\UpdateAssignmentRequest;
use App\Models\Assignment;

class AssignmentService
{
    public function __construct(
        private AssignmentRepository $assignmentRepository,
        private StudentRepository $studentRepository,
        private SubjectRepository $subjectRepository
    ) {}


    public function updateAssignment(int $id, UpdateAssignmentRequest $request): array
    {
        try {
            $assignment = $this->assignmentRepository->findById($id);

            $updateData = $this->prepareUpdateData($request);
            $this->assignmentRepository->update($assignment, $updateData);

            if ($request->hasFile('file')) {
                $this->assignmentRepository->attachFiles($assignment, $request->file);
            }

            $this->sendAssignmentNotification($assignment, $request);

            return [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (\Throwable $e) {
            return [
                'error' => true,
                'message' => trans('error_occurred'),
                'exception' => $e
            ];
        }
    }

    private function prepareUpdateData(UpdateAssignmentRequest $request): array
    {
        $sessionYear = getSettings('session_year');

        return [
            'class_section_id' => $request->class_section_id,
            'subject_id' => $request->subject_id,
            'name' => $request->name,
            'instructions' => $request->instructions,
            'due_date' => $request->due_date,
            'points' => $request->points,
            'resubmission' => $request->resubmission ? 1 : 0,
            'extra_days_for_resubmission' => $request->resubmission ? $request->extra_days_for_resubmission : null,
            'session_year_id' => $sessionYear['session_year']
        ];
    }

    public function createAssignment(StoreAssignmentRequest $request): array
    {
        try {
            $assignmentData = $this->prepareAssignmentData($request);
            $assignment = $this->assignmentRepository->create($assignmentData);

            if ($request->hasFile('file')) {
                $this->assignmentRepository->attachFiles($assignment, $request->file);
            }

            $this->sendAssignmentNotification($assignment, $request);

            return [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];

        } catch (\Throwable $e) {
            return [
                'error' => true,
                'message' => trans('error_occurred'),
                'exception' => $e
            ];
        }
    }

    private function prepareAssignmentData(StoreAssignmentRequest $request): array
    {
        $sessionYear = getSettings('session_year');

        return [
            'class_section_id' => $request->class_section_id,
            'subject_id' => $request->subject_id,
            'name' => $request->name,
            'instructions' => $request->instructions,
            'due_date' => $request->due_date,
            'points' => $request->points,
            'resubmission' => $request->resubmission ? 1 : 0,
            'extra_days_for_resubmission' => $request->resubmission ? 
                $request->extra_days_for_resubmission : null,
            'session_year_id' => $sessionYear['session_year']
        ];
    }

    private function sendAssignmentNotification(Assignment $assignment, StoreAssignmentRequest|UpdateAssignmentRequest $request): void
    {
        $subjectName = $this->subjectRepository->getSubjectName($request->subject_id);
        $userIds = $this->studentRepository->getStudentUserIds($request->class_section_id);

        send_notification(
            $userIds,
            'New assignment added in ' . $subjectName,
            $request->name,
            'assignment'
        );
    }
}
