<?php

namespace App\Domain\Lesson\Repositories;

use App\Domain\File\Repositories\FileRepository;
use App\Exceptions\LessonHasTopicsException;
use App\Models\Lesson;
use App\Models\LessonTopic;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LessonRepository extends BaseRepository
{
    protected string $uploadPath = 'lessons';

    public function __construct(
        Lesson $model,
        protected FileRepository $fileRepository
    ) {
        parent::__construct($model);
    }

    public function createLesson(array $data): Lesson
    {
        $lesson = $this->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'class_section_id' => $data['class_section_id'],
            'subject_id' => $data['subject_id']
        ]);

        if (isset($data['file'])) {
            $this->createLessonFiles($lesson, $data['file']);
        }

        return $lesson;
    }

    private function createLessonFiles(Lesson $lesson, array $files): void
    {
        foreach ($files as $file) {
            if (!empty($file['type'])) {
                $this->fileRepository->createLessonFile($lesson, $file);
            }
        }
    }

    public function updateLesson(int $id, array $data): Lesson
    {
        $lesson = $this->getByIdOrFail($id);
        
        $this->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'class_section_id' => $data['class_section_id'],
            'subject_id' => $data['subject_id']
        ], $id);

        if (!empty($data['edit_file'])) {
            $this->updateExistingFiles($lesson, $data['edit_file']);
        }

        if (!empty($data['file'])) {
            $this->createNewFiles($lesson, $data['file']);
        }

        return $lesson->fresh();
    }

    private function updateExistingFiles(Lesson $lesson, array $files): void
    {
        foreach ($files as $file) {
            if (isset($file['type'])) {
                $this->fileRepository->updateLessonFile($file);
            }
        }
    }

    private function createNewFiles(Lesson $lesson, array $files): void
    {
        foreach ($files as $file) {
            if ($file['type']) {
                $this->fileRepository->createLessonFile($lesson, $file);
            }
        }
    }

    public function deleteLesson(int $lessonId): void
    {
        $lesson = $this->getByIdOrFail($lessonId);

        if ($this->hasTopics($lessonId)) {
            throw new LessonHasTopicsException();
        }

        $this->deleteRelatedFiles($lesson);
        $this->delete($lessonId);
    }

    private function hasTopics(int $lessonId): bool
    {
        return LessonTopic::where('lesson_id', $lessonId)->exists();
    }

    private function deleteRelatedFiles(Lesson $lesson): void
    {
        foreach ($lesson->file as $file) {
            $this->fileRepository->safeDelete($file->id);
        }
    }

    public function getLessonsList(array $params): array
    {
        $query = $this->buildListQuery($params);
        
        $total = $query->count();

        $lessons = $query->orderBy($params['sort'], $params['order'])
            ->skip($params['offset'])
            ->take($params['limit'])
            ->get();

        return [
            'total' => $total,
            'rows' => $this->formatLessonsData($lessons)
        ];
    }

    private function buildListQuery(array $params): Builder
    {
        $query = $this->model->lessonteachers()
            ->with(['subject', 'class_section', 'topic', 'file'])
            ->whereHas('class_section.class', function ($q) {
                $q->activeMediumOnly();
            })
            ->whereHas('subject', function ($q) {
                $q->where('center_id', session()->get('center_id'));
            });

        $this->applySearchFilters($query, $params);
        $this->applyAdditionalFilters($query, $params);

        return $query;
    }

    private function applySearchFilters(Builder $query, array $params): void
    {
        if (!empty($params['search'])) {
            $search = $params['search'];
            $searchDate = date('Y-m-d H:i:s', strtotime($search));

            $query->where(function ($q) use ($search, $searchDate) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhere('name', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%")
                    ->orWhere('created_at', 'LIKE', "%$searchDate%")
                    ->orWhere('updated_at', 'LIKE', "%$searchDate%")
                    ->orWhereHas('class_section.section', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('class_section.class', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('subject', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    });
            })->whereHas('class_section', function ($q) {
                $q->whereHas('class', function ($q) {
                    $q->where('center_id', session()->get('center_id'));
                });
            });
        }
    }

    private function applyAdditionalFilters(Builder $query, array $params): void
    {
        if (!empty($params['subject_id'])) {
            $query->where('subject_id', $params['subject_id']);
        }

        if (!empty($params['class_id'])) {
            $query->where('class_section_id', $params['class_id']);
        }
    }

    private function formatLessonsData(Collection $lessons): array
    {
        $rows = [];
        $no = 1;

        foreach ($lessons as $lesson) {
            $rows[] = [
                'id' => $lesson->id,
                'no' => $no++,
                'name' => $lesson->name,
                'description' => $lesson->description,
                'class_section_id' => $lesson->class_section_id,
                'class_section_name' => $lesson->class_section->full_name,
                'subject_id' => $lesson->subject_id,
                'subject_name' => $lesson->subject->name . ' - ' . $lesson->subject->type,
                'topic' => $lesson->topic,
                'file' => $lesson->file,
                'center_id' => $lesson->class_section->class->center->id,
                'operate' => $this->generateOperateButtons($lesson)
            ];
        }

        return $rows;
    }

    private function generateOperateButtons(Lesson $lesson): string
    {
        $editButton = sprintf(
            '<a href="%s" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" 
                data-id="%d" title="Edit" data-toggle="modal" data-target="#editModal">
                <i class="fa fa-edit"></i>
            </a>',
            route('lesson.edit', $lesson->id),
            $lesson->id
        );

        $deleteButton = sprintf(
            '<a href="%s" class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" 
                data-id="%d">
                <i class="fa fa-trash"></i>
            </a>',
            route('lesson.destroy', $lesson->id),
            $lesson->id
        );

        return $editButton . '&nbsp;&nbsp;' . $deleteButton;
    }
}