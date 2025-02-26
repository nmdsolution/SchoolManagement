<?php


namespace App\Domain\Announcement\Services;

use App\Domain\Announcement\Repositories\AnnouncementRepository;
use App\Domain\Subject\Repositories\SubjectTeacherRepository;
use App\Http\Requests\Announcement\StoreAnnouncementRequest;
use App\Http\Requests\Announcement\UpdateAnnouncementRequest;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\Students;
use App\Models\SubjectTeacher;
use Illuminate\Http\Request;

class AnnouncementService
{
    public function __construct(
        private AnnouncementRepository $announcementRepository,
        private SubjectTeacherRepository $subjectTeacherRepository
        )
    {
        
    }

    public function createAnnouncement(StoreAnnouncementRequest $request): array
    {
        try {
            $getData = $request->get_data ?? [null];
            $sessionYear = getSettings('session_year');

            foreach ($getData as $data) {
                $announcementData = $this->prepareAnnouncementData($request, $data, $sessionYear['session_year']);
                $announcement = $this->announcementRepository->create($announcementData);

                if ($request->hasFile('file')) {
                    $this->announcementRepository->attachFiles($announcement, $request->file);
                }

                $notificationData = $this->prepareNotificationData($request, $data);
                $this->sendNotifications($notificationData);
            }

            return [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];

        } catch (\Throwable $e) {
            Log::error($e->getMessage() . '-->' . $e->getFile() . ' : ' . $e->getLine());
            return [
                'error' => true,
                'message' => trans('error_occurred')
            ];
        }
    }

    private function prepareAnnouncementData(StoreAnnouncementRequest $request, $data, $sessionYearId): array
    {
        $baseData = [
            'title' => $request->title,
            'description' => $request->description,
            'session_year_id' => $sessionYearId,
            'center_id' => get_center_id(),
            'table_id' => null,
            'table_type' => ''
        ];

        if ($request->set_data === 'class_section') {
            return $this->prepareClassSectionAnnouncement($request, $data, $baseData);
        }

        if ($request->set_data === 'class') {
            return $this->prepareClassAnnouncement($data, $baseData);
        }

        return $baseData;
    }

    private function prepareClassSectionAnnouncement(StoreAnnouncementRequest $request, $subjectId, array $baseData): array
    {
        $teacherId = auth()->user()->teacher->id;
        $subjects = $this->subjectTeacherRepository->findTeacherSubjects(
            $teacherId,
            $request->class_section_id,
            $subjectId
        );

        if ($subjects->isEmpty()) {
            throw new \Exception(trans('no_data_found'));
        }

        $baseData['table'] = $subjects->first();
        return $baseData;
    }

    private function prepareClassAnnouncement($classId, array $baseData): array
    {
        $baseData['table'] = ClassSchool::find($classId);
        return $baseData;
    }

    

    private function sendNotifications(array $data): void
    {
        send_notification($data['users'], $data['title'], $data['body'], $data['type']);
    }

    public function updateAnnouncement(UpdateAnnouncementRequest $request): array
    {
        try {
            $announcement = $this->announcementRepository->findById($request->id);
            
            $sessionYear = getSettings('session_year');
            $updateData = $this->prepareUpdateData($request, $sessionYear['session_year']);
            
            $this->announcementRepository->update($announcement, $updateData);

            if ($request->hasFile('file')) {
                $this->announcementRepository->attachFiles($announcement, $request->file);
            }

            $notificationData = $this->prepareNotificationData($request);
            $this->sendNotification($notificationData);

            return [
                'error' => false,
                'message' => trans('data_update_successfully')
            ];

        } catch (\Throwable $e) {
            return [
                'error' => true,
                'message' => trans('error_occurred')
            ];
        }
    }

    private function prepareUpdateData(UpdateAnnouncementRequest $request, $sessionYearId): array
    {
        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'session_year_id' => $sessionYearId
        ];

        if ($request->set_data === 'class_section') {
            $data['table'] = $this->getSubjectTeacherData($request);
        } elseif ($request->set_data === 'class') {
            $data['table'] = ClassSchool::find($request->get_data);
        }

        return $data;
    }

    private function getSubjectTeacherData(UpdateAnnouncementRequest $request)
    {
        $teacherId = auth()->user()->teacher->id;
        $subjects = $this->subjectTeacherRepository->findTeacherSubjects(
            $teacherId,
            $request->class_section_id,
            $request->get_data
        );

        if ($subjects->isEmpty()) {
            throw new \Exception(trans('no_data_found'));
        }

        return $subjects->first();
    }

    private function prepareNotificationData(Request $request): array
    {
        $notificationData = [
            'users' => [],
            'title' => '',
            'body' => '',
            'type' => $request->set_data
        ];

        switch ($request->set_data) {
            case 'class_section':
                $subjectName = SubjectTeacher::where([
                    'class_section_id' => $request->class_section_id,
                    'subject_id' => $request->get_data
                ])->with('subject')->first()->subject->name;

                $notificationData['users'] = Students::select('user_id')
                    ->where('class_section_id', $request->class_section_id)
                    ->pluck('user_id');
                $notificationData['title'] = "Update announcement in {$subjectName}";
                $notificationData['body'] = $request->title;
                break;

            case 'class':
                $classSections = ClassSection::where('class_id', $request->get_data)
                    ->pluck('id');
                $notificationData['users'] = Students::select('user_id')
                    ->whereIn('class_section_id', $classSections)
                    ->pluck('user_id');
                $notificationData['title'] = $request->title;
                $notificationData['body'] = $request->description;
                break;

            case 'noticeboard':
                $notificationData['users'] = Students::select('user_id')->pluck('user_id');
                $notificationData['title'] = 'Noticeboard updated';
                $notificationData['body'] = $request->title;
                break;
        }

        return $notificationData;
    }

    private function sendNotification(array $data): void
    {
        send_notification($data['users'], $data['title'], $data['body'], $data['type']);
    }
}