<?php

namespace App\Domain\Subject\Repositories;

use App\Exceptions\SubjectHasAssociationsException;
use App\Models\Assignment;
use App\Models\ClassSubject;
use App\Models\ExamMarks;
use App\Models\ExamTimetable;
use App\Models\StudentSubject;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class SubjectRepository extends BaseRepository
{
    protected $folder = 'subjects';

    public function __construct(Subject $subject)
    {
        parent::__construct($subject);
    }

    public function getSubjectName(int $subjectId): string
    {
        return Subject::where('id', $subjectId)->value('name');
    }

    public function createSubject(array $data): Subject
    {
        $subject = $this->model->newInstance();
        $subject->fill([
            'medium_id' => getCurrentMedium()->id,
            'name' => $data['name'],
            'bg_color' => $data['bg_color'],
            'code' => $data['code'],
            'type' => $data['type'],
            'center_id' => get_center_id()
        ]);

        if (isset($data['image'])) {
            $subject->image = $data['image']->store($this->folder, 'public');
        }

        $this->create($subject);

        return $subject;
    }

    public function updateSubject(int $id, array $data): Subject
    {
        $subject = $this->getByIdOrFail($id);

        $updateData = [
            'medium_id' => getCurrentMedium()->id,
            'name' => $data['name'],
            'bg_color' => $data['bg_color'],
            'code' => $data['code'],
            'type' => $data['type']
        ];

        if (isset($data['image'])) {
            $this->deleteOldImage($subject);
            $updateData['image'] = $data['image']->store($this->folder, 'public');
        }

        $this->update($updateData, $id);
        
        return $subject->fresh();
    }

    private function deleteOldImage(Subject $subject): void
    {
        if ($subject->getRawOriginal('image') && 
            Storage::disk('public')->exists($subject->getRawOriginal('image'))) {
            Storage::disk('public')->delete($subject->getRawOriginal('image'));
        }
    }

    public function checkSubjectAssociations(int $subjectId): array
    {
        return [
            'assignments' => Assignment::where('subject_id', $subjectId)->count(),
            'class_subjects' => ClassSubject::where('subject_id', $subjectId)->count(),
            'exam_marks' => ExamMarks::where('subject_id', $subjectId)->count(),
            'exam_timetables' => ExamTimetable::where('subject_id', $subjectId)->count(),
            'student_subjects' => StudentSubject::where('subject_id', $subjectId)->count(),
            'subject_teachers' => SubjectTeacher::where('subject_id', $subjectId)->count(),
        ];
    }

    public function hasAssociations(int $subjectId): bool
    {
        return array_sum($this->checkSubjectAssociations($subjectId)) > 0;
    }

    public function deleteSubject(int $subjectId): void
    {
        $subject = $this->getByIdOrFail($subjectId);
        
        if ($this->hasAssociations($subjectId)) {
            throw new SubjectHasAssociationsException(
                trans('cannot_delete_because_data_is_associated_with_other_data')
            );
        }

        $this->delete($subjectId);
    }

    public function getSubjectsList(array $params): array
    {
        $query = $this->buildListQuery($params);
        $total = $query->count();

        $subjects = $query->orderBy($params['sort'], $params['order'])
            ->skip($params['offset'])
            ->take($params['limit'])
            ->get();

        return [
            'total' => $total,
            'rows' => $this->formatSubjectsData($subjects)
        ];
    }

    private function buildListQuery(array $params): Builder
    {
        $query = $this->model->owner()
            ->where('medium_id', getCurrentMedium()->id);

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('id', 'LIKE', "%{$params['search']}%")
                    ->orWhere('name', 'LIKE', "%{$params['search']}%")
                    ->orWhere('code', 'LIKE', "%{$params['search']}%")
                    ->orWhere('type', 'LIKE', "%{$params['search']}%");
            });
        }

        return $query;
    }

    private function formatSubjectsData(Collection $subjects): array
    {
        $rows = [];
        $no = 1;

        foreach ($subjects as $subject) {
            $rows[] = [
                'id' => $subject->id,
                'no' => $no++,
                'name' => $subject->name,
                'code' => $subject->code,
                'bg_color' => $subject->bg_color,
                'image' => $subject->image,
                'type' => $subject->type,
                'created_at' => $subject->created_at,
                'updated_at' => $subject->updated_at,
                'operate' => $this->generateOperateButtons($subject)
            ];
        }

        return $rows;
    }

    private function generateOperateButtons(Subject $subject): string
    {
        $editButton = sprintf(
            '<a href="%s" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" 
                data-id="%d" title="Edit" data-toggle="modal" data-target="#editModal">
                <i class="fa fa-edit"></i>
            </a>',
            route('subject.edit', $subject->id),
            $subject->id
        );

        $deleteButton = sprintf(
            '<a href="%s" class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" 
                data-id="%d">
                <i class="fa fa-trash"></i>
            </a>',
            route('subject.destroy', $subject->id),
            $subject->id
        );

        return $editButton . '&nbsp;&nbsp;' . $deleteButton;
    }
}

