<?php

namespace App\Domain\Lesson\Services;

use App\Domain\Lesson\Repositories\LessonRepository;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

class LessonService
{
    public function __construct(private LessonRepository $lessonRepository)
    {
        
    }

    public function createLesson(array $data): Lesson
    {
        return DB::transaction(function () use ($data) {
            return $this->lessonRepository->createLesson($data);
        });
    }
    
    public function updateLesson(int $id, array $data): Lesson
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->lessonRepository->updateLesson($id, $data);
        });
    }

    public function deleteLesson(int $lessonId): void
    {
        DB::transaction(function () use ($lessonId) {
            $this->lessonRepository->deleteLesson($lessonId);
        });
    }

    public function getLessonsList(array $params): array
    {
        return $this->lessonRepository->getLessonsList($params);
    }
}