<?php

namespace App\Domain\Lesson\Services;

use App\Domain\Lesson\Repositories\LessonTopicRepository;
use App\Models\LessonTopic;
use Illuminate\Support\Facades\DB;

class LessonTopicService
{
    public function __construct(private LessonTopicRepository $lessonTopicRepository)
    {
        
    }

    public function updateTopic(int $topicId, array $data): LessonTopic
    {
        return DB::transaction(function () use ($topicId, $data) {
            return $this->lessonTopicRepository->updateTopic($topicId, $data);
        });
    }

    public function getTopicsList(array $params): array
    {
        return $this->lessonTopicRepository->getTopicsList($params);
    }

    public function deleteTopic(int $topicId): void
    {
        DB::transaction(function () use ($topicId) {
            $this->lessonTopicRepository->deleteTopic($topicId);
        });
    }
}