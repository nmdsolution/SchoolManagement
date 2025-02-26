<?php

namespace App\Domain\Lesson\Repositories;

use App\Domain\File\Repositories\FileRepository;
use App\Models\LessonTopic;
use App\Repositories\BaseRepository;

class LessonTopicRepository extends BaseRepository
{
    public function __construct(
        LessonTopic $model,
        protected LessonTopicFileRepository $fileRepository
    ) {
        parent::__construct($model);
    }

    public function createTopic(array $data): LessonTopic
    {
        $topic = $this->create([
            'name' => $data['name'],
            'description' => $data['description'],
            'lesson_id' => $data['lesson_id']
        ]);

        if (!empty($data['file'])) {
            $this->createTopicFiles($topic, $data['file']);
        }

        return $topic;
    }

    private function createTopicFiles(LessonTopic $topic, array $files): void
    {
        foreach ($files as $fileData) {
            if (!empty($fileData['type'])) {
                $this->fileRepository->createTopicFile($topic, $fileData);
            }
        }
    }

    public function updateTopic(int $topicId, array $data): LessonTopic
    {
        $topic = $this->getByIdOrFail($topicId);
        
        $this->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'lesson_id' => $data['lesson_id']
        ], $topicId);

        $this->handleFiles($topic, $data);

        return $topic->fresh();
    }

    private function handleFiles(LessonTopic $topic, array $data): void
    {
        if (!empty($data['edit_file'])) {
            $this->fileRepository->updateFiles($data['edit_file']);
        }

        if (!empty($data['file'])) {
            $this->fileRepository->createFiles($topic, $data['file']);
        }
    }

    public function getTopicsList(array $params): array
    {
        $query = $this->buildListQuery($params);
        
        $total = $query->count();

        $topics = $query->orderBy($params['sort'], $params['order'])
            ->skip($params['offset'])
            ->take($params['limit'])
            ->get();

        return [
            'total' => $total,
            'rows' => $this->formatTopicsData($topics)
        ];
    }

    private function buildListQuery(array $params): Builder
    {
        $query = $this->model->lessontopicteachers()
            ->with(['lesson.class_section', 'lesson.subject', 'file'])
            ->whereHas('lesson.class_section.class', function($q) {
                $q->activeMediumOnly();
            })
            ->whereHas('lesson.subject', function ($q) {
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
            
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhere('name', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%")
                    ->orWhereHas('lesson.class_section.section', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('lesson.class_section.class', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('lesson.subject', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('lesson', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    });
            })->whereHas('lesson.class_section.class', function ($q) {
                $q->where('center_id', session()->get('center_id'));
            });
        }
    }

    private function applyAdditionalFilters(Builder $query, array $params): void
    {
        if (!empty($params['subject_id'])) {
            $query->whereHas('lesson', function ($q) use ($params) {
                $q->where('subject_id', $params['subject_id']);
            });
        }

        if (!empty($params['class_id'])) {
            $query->whereHas('lesson', function ($q) use ($params) {
                $q->where('class_section_id', $params['class_id']);
            });
        }

        if (!empty($params['lesson_id'])) {
            $query->where('lesson_id', $params['lesson_id']);
        }
    }

    private function formatTopicsData(Collection $topics): array
    {
        $rows = [];
        $no = 1;

        foreach ($topics as $topic) {
            $rows[] = [
                'id' => $topic->id,
                'no' => $no++,
                'name' => $topic->name,
                'description' => $topic->description,
                'lesson_id' => $topic->lesson_id,
                'lesson_name' => $topic->lesson->name,
                'class_section_id' => $topic->lesson->class_section->id,
                'class_section_name' => $topic->lesson->class_section->full_name,
                'subject_id' => $topic->lesson->subject->id,
                'subject_name' => $topic->lesson->subject->name . ' - ' . $topic->lesson->subject->type,
                'file' => $topic->file,
                'center_id' => $topic->lesson->class_section->class->center_id,
                'operate' => $this->generateOperateButtons($topic)
            ];
        }

        return $rows;
    }

    private function generateOperateButtons(LessonTopic $topic): string
    {
        $editButton = sprintf(
            '<a href="%s" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" 
                data-id="%d" title="Edit" data-toggle="modal" data-target="#editModal">
                <i class="fa fa-edit"></i>
            </a>',
            route('lesson-topic.edit', $topic->id),
            $topic->id
        );

        $deleteButton = sprintf(
            '<a href="%s" class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" 
                data-id="%d">
                <i class="fa fa-trash"></i>
            </a>',
            route('lesson-topic.destroy', $topic->id),
            $topic->id
        );

        return $editButton . '&nbsp;&nbsp;' . $deleteButton;
    }

    public function deleteTopic(int $topicId): void
    {
        $topic = $this->getByIdOrFail($topicId);
        
        $this->deleteTopicFiles($topic);
        $this->delete($topicId);
    }

    private function deleteTopicFiles(LessonTopic $topic): void
    {
        if ($topic->file) {
            foreach ($topic->file as $file) {
                $this->fileRepository->safeDelete($file->id);
            }
        }
    }
}