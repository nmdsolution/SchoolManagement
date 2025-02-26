<?php

namespace App\Domain\Announcement\Services;

use App\Domain\Announcement\Repositories\AnnouncementRepository;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AnnouncementListService
{

    public function __construct(private AnnouncementRepository $announcementRepository)
    {
    }

    public function getAnnouncementsList(Request $request): array
    {
        $pagination = $this->getPaginationParams($request);
        $filters = $this->getFilterParams($request);

        $total = $this->announcementRepository->getTotalCount($filters);
        $announcements = $this->announcementRepository->getPaginatedAnnouncements($filters, $pagination);

        $rows = $this->formatAnnouncementData($announcements);

        if ($request->get('print')) {
            return $this->generatePDF($rows);
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
            'isTeacher' => auth()->user()->hasRole('Teacher')
        ];
    }

    private function formatAnnouncementData(Collection $announcements): array
    {
        $rows = [];
        $no = 1;

        foreach ($announcements as $announcement) {
            $assignmentData = $this->getAssignmentData($announcement);
            
            $rows[] = [
                'id' => $announcement->id,
                'no' => $no++,
                'title' => $announcement->title,
                'description' => $announcement->description,
                'type' => $announcement->table_type,
                'assign' => $assignmentData['assign'],
                'assign_to' => $assignmentData['assign_to'],
                'assignto' => $assignmentData['assignto'],
                'get_data' => $announcement->table_id,
                'file' => $announcement->file,
                'operate' => $this->generateOperateButtons($announcement)
            ];
        }

        return $rows;
    }

    private function getAssignmentData(Announcement $announcement): array
    {
        switch ($announcement->table_type) {
            case 'App\\Models\\ClassSection':
                return [
                    'assign' => 'class_section',
                    'assign_to' => $announcement->table->full_name,
                    'assignto' => $announcement->table->full_name
                ];

            case 'App\\Models\\ClassSchool':
                return [
                    'assign' => 'class',
                    'assign_to' => $announcement->table->name,
                    'assignto' => $announcement->table->name
                ];

            case 'App\\Models\\SubjectTeacher':
                return [
                    'assign' => 'Subject',
                    'assign_to' => $announcement->table,
                    'assignto' => $announcement->table->class_section->full_name . ' ' . $announcement->table->subject->name
                ];

            default:
                return [
                    'assign' => 'noticeboard',
                    'assign_to' => trans('noticeboard'),
                    'assignto' => trans('noticeboard')
                ];
        }
    }

    private function generateOperateButtons(Announcement $announcement): string
    {
        $user = auth()->user();
        $buttons = '<div class="actions">';

        if ($this->canEditAnnouncement($user, $announcement)) {
            $buttons .= '<a class="btn btn-sm bg-success-light edit-data set-form-url" href="' . route('announcement.update', $announcement->id) . '"  title="Edit" data-bs-toggle="modal" data-bs-target="#editModal"><i class="feather-edit"></i></a>&nbsp;&nbsp;';
            $buttons .= '<a class="btn btn-sm bg-success-light delete-form" data-id="' . $announcement->id . '" href="' . route('announcement.destroy', $announcement->id) . '" title="Delete"><i class="feather-trash"></i></a>';
        }

        return $buttons . '</div>';
    }

    private function canEditAnnouncement(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole('Center') && $announcement->table_type == "") {
            return true;
        }

        if ($user->hasRole('Teacher') && in_array($announcement->table_type, [
            "App\\Models\\ClassSection",
            "App\\Models\\ClassSchool",
            "App\\Models\\SubjectTeacher"
        ])) {
            return true;
        }

        return false;
    }

    private function generatePDF(array $rows): Response
    {
        $pdf = MiscPrints::getInstance(get_center_id(), 'P');
        $pdf->printAnnouncementList($rows);

        return response(
            $pdf->Output('', 'ANNOUNCEMENT LIST.pdf'),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }
}