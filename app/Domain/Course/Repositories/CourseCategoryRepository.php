<?php


namespace App\Domain\Course\Repositories;

use App\Models\CourseCategory;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CourseCategoryRepository  extends BaseRepository
{
    protected string $uploadPath = 'course_category';

    public function __construct(CourseCategory $courseCategory)
    {
        parent::__construct($courseCategory);
    }

    public function getCategoriesForDropdown(): array
    {
        $categories = $this->model->pluck('name', 'id')->toArray();
        return [0 => __('No Category')] + $categories;
    }

    public function createCategory(array $data): CourseCategory
    {
        $categoryData = [
            'name' => $data['name'],
            'description' => $data['description'],
            'thumbnail' => $this->storeThumbnail($data['thumbnail'])
        ];

        return $this->create($categoryData);
    }

    private function storeThumbnail(UploadedFile $thumbnail): string
    {
        return $thumbnail->store($this->uploadPath, 'public');
    }

    public function getCategoriesList(array $params): array
    {
        $query = $this->buildListQuery($params);
        
        $total = $query->count();

        $categories = $query->orderBy($params['sort'], $params['order'])
            ->skip($params['offset'])
            ->take($params['limit'])
            ->get();

        return [
            'total' => $total,
            'rows' => $this->formatCategoriesData($categories)
        ];
    }

    public function updateCategory(int $id, array $data): CourseCategory
    {
        $category = $this->getByIdOrFail($id);

        $updateData = [
            'name' => $data['name'],
            'description' => $data['description']
        ];

        if (isset($data['thumbnail'])) {
            $this->updateThumbnail($category, $data['thumbnail']);
            $updateData['thumbnail'] = $data['thumbnail']->store($this->uploadPath, 'public');
        }

        $this->update($updateData, $id);

        return $category->fresh();
    }

    private function updateThumbnail(CourseCategory $category, UploadedFile $newThumbnail): void
    {
        if ($category->getRawOriginal('thumbnail')) {
            $this->deleteExistingFile($category->getRawOriginal('thumbnail'));
        }
    }

    private function deleteExistingFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function buildListQuery(array $params): Builder
    {
        $query = $this->model->whereNull('deleted_at');

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('id', 'LIKE', "%{$params['search']}%")
                    ->orWhere('name', 'LIKE', "%{$params['search']}%");
            });
        }

        return $query;
    }

    private function formatCategoriesData(Collection $categories): array
    {
        $rows = [];
        $no = 1;

        foreach ($categories as $category) {
            $rows[] = [
                'id' => $category->id,
                'no' => $no++,
                'name' => $category->name,
                'description' => $category->description,
                'thumbnail' => $category->thumbnail,
                'operate' => $this->generateOperateButtons($category)
            ];
        }

        return $rows;
    }

    private function generateOperateButtons(CourseCategory $category): string
    {
        $editButton = sprintf(
            '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" 
                data-id="%d" title="Edit" data-toggle="modal" data-target="#editModal">
                <i class="fa fa-edit"></i>
            </a>',
            $category->id
        );

        $deleteButton = sprintf(
            '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" 
                data-id="%d" data-url="%s" title="Delete">
                <i class="fa fa-trash"></i>
            </a>',
            $category->id,
            url('course_category', $category->id)
        );

        return $editButton . '&nbsp;&nbsp;' . $deleteButton;
    }

    public function deleteCategory(int $id): void
    {
        $category = $this->getByIdOrFail($id);
        
        // Si vous avez besoin de supprimer des fichiers associés
        if ($category->thumbnail) {
            $this->deleteExistingFile($category->getRawOriginal('thumbnail'));
        }

        $this->delete($id);
    }

}