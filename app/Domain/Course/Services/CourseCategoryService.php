<?php


namespace App\Domain\Course\Services;

use App\Domain\Course\Repositories\CourseCategoryRepository;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\DB;

class CourseCategoryService
{
    public function __construct(private CourseCategoryRepository $courseCategoryRepository)
    {
        
    }

    public function createCategory(array $data): CourseCategory
    {
        return DB::transaction(function () use ($data) {
            return $this->courseCategoryRepository->createCategory($data);
        });
    }

    public function getCategoriesList(array $params): array
    {
        return $this->courseCategoryRepository->getCategoriesList($params);
    }

    public function updateCategory(int $id, array $data): CourseCategory
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->courseCategoryRepository->updateCategory($id, $data);
        });
    }

    public function deleteCategory(int $id): void
    {
        DB::transaction(function () use ($id) {
            $this->courseCategoryRepository->deleteCategory($id);
        });
    }
}