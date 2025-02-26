<?php

namespace App\Domain\Course\Repositories;

use App\Domain\File\Repositories\FileRepository;
use App\Models\Course;
use App\Models\CourseTeacher;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CourseRepository extends BaseRepository
{
    public function __construct(Course $course, private FileRepository $fileRepository)
    {
        parent::__construct($course);
    }

    public function attachTeachersToCourse(int $courseId, array $teacherIds)
    {
        foreach ($teacherIds as $teacherId) {
            CourseTeacher::create([
                'course_id' => $courseId,
                'user_id' => $teacherId,
            ]);
        }
    }

    public function updateCourse(Course $course, array $data): Course
    {
        $updateData = [
            'name' => $data['name'],
            'price' => $data['price'],
            'duration' => $data['duration'],
            'description' => $data['description'],
            'course_category_id' => $data['category_id'] != 0 ? $data['category_id'] : null,
            'tags' => $data['tags']
        ];

        $this->update($updateData, $course->id);
        
        if (isset($data['thumbnail'])) {
            $this->updateThumbnail($course, $data['thumbnail']);
        }

        return $course->fresh();
    }

    private function updateThumbnail(Course $course, UploadedFile $thumbnail): void
    {
        if (Storage::disk('public')->exists($course->getRawOriginal('thumbnail'))) {
            Storage::disk('public')->delete($course->getRawOriginal('thumbnail'));
        }
        $this->update([
            'thumbnail' => $thumbnail->store('course_material', 'public')
        ], $course->id);
    }

    public function updateSections(Course $course, array $oldFiles, ?array $newSections): void
    {
        $oldSectionIds = $course->course_section->pluck('id')->toArray();
        $newSectionIds = [];

        if ($oldFiles) {
            $newSectionIds = $this->processOldFiles($course, $oldFiles);
        }

        if ($newSections) {
            $this->createNewSections($course, $newSections);
        }

        $deletedSectionIds = array_diff($oldSectionIds, $newSectionIds);
        if (!empty($deletedSectionIds)) {
            $this->delete($deletedSectionIds);
            $this->fileRepository->deleteByModalIds($deletedSectionIds);
        }
    }

    private function createNewSections(Course $course, array $sections): void
    {
        foreach ($sections as $sectionData) {
            $section = $this->create([
                'course_id' => $course->id,
                'title' => $sectionData['title'],
                'description' => $sectionData['section_description']
            ]);

            if (isset($sectionData['course_files'])) {
                $this->fileRepository->handleSectionFiles($section, $sectionData['course_files']);
            }
        }
    }

    private function processOldFiles(Course $course, array $materials): array
    {
        $newSectionIds = [];
        foreach ($materials as $material) {
            if (!empty($material['section_id'])) {
                $newSectionIds[] = $material['section_id'];
                
                $section = $this->updateOrCreate(
                    ['id' => $material['section_id']],
                    [
                        'course_id' => $course->id,
                        'title' => $material['title'],
                        'description' => $material['section_description']
                    ]
                );

                if (isset($material['course_files'])) {
                    $this->fileRepository->handleSectionFiles($section, $material['course_files']);
                }
            }
        }
        return $newSectionIds;
    }

    public function deleteWithFiles(int $sectionId): void
    {
        $section = $this->getByIdOrFail($sectionId, ['file']);
        
        // Supprimer les fichiers
        foreach ($section->file as $file) {
            $this->fileRepository->safeDelete($file->id);
        }

        // Supprimer la section
        $this->delete($sectionId);
    }

    public function deleteCourse(int $courseId): void
    {
        $course = $this->getByIdOrFail($courseId);
        
        // Supprimer les sections et leurs fichiers associés
        $courseSections = $course->course_section;
        foreach ($courseSections as $section) {
            $this->deleteWithFiles($section->id);
        }

        // Supprimer le cours
        $this->delete($courseId);
    }

    public function getSuperTeacherCourses(int $teacherId, array $params): array
    {
        $query = $this->model->with(['course_category', 'course_section.file'])
            ->whereHas('course_teacher', function ($q) use ($teacherId) {
                $q->where('user_id', $teacherId);
            });

        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $search = $params['search'];
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhere('name', 'LIKE', "%$search%")
                    ->orWhere('price', 'LIKE', "%$search%")
                    ->orWhere('duration', 'LIKE', "%$search%")
                    ->orWhere('tags', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        $total = $query->count();
        $courses = $query->orderBy($params['sort'], $params['order'])
            ->skip($params['offset'])
            ->take($params['limit'])
            ->get();

        return [
            'total' => $total,
            'rows' => $this->formatCoursesData($courses)
        ];
    }

    private function formatCoursesData(Collection $courses): array
    {
        $rows = [];
        $no = 1;

        foreach ($courses as $course) {
            $rows[] = [
                'id' => $course->id,
                'no' => $no++,
                'name' => $course->name,
                'image' => $course->thumbnail,
                'price' => $course->price,
                'duration' => $course->duration,
                'description' => $course->description,
                'category' => $course->course_category?->name,
                'tags' => $course->tags,
                'file' => $course->course_section->load('file'),
                'operate' => $this->generateOperateButtons($course)
            ];
        }

        return $rows;
    }

    public function getCourseWithMaterials(int $courseId, bool $isSuperAdmin): ?Course
    {
        $query = $this->model->with('course_section.file');

        if (!$isSuperAdmin) {
            $query->whereHas('course_teacher', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        return $query->find($courseId);
    }

    public function deleteSectionWithMaterial(int $sectionId): void
    {
        $section = $this->getByIdOrFail($sectionId);
        
        $file = $this->fileRepository->getFirstBySectionId($section->id);
        if ($file) {
            $this->fileRepository->safeDelete($file->id);
        }

        $this->delete($sectionId);
    }

    public function getCoursesList(array $params): array
    {
        $query = $this->buildListQuery($params);
        
        $total = $query->count();
        $courses = $query->get();

        return [
            'total' => $total,
            'rows' => $this->formatCoursesDataWithSuperTeacher($courses)
        ];
    }

    private function buildListQuery(array $params): Builder
    {
        $query = $this->model->with(['users', 'course_category', 'course_section.file']);

        if (!empty($params['search'])) {
            $this->applySearchFilters($query, $params['search']);
        }

        return $query->orderBy('id', 'DESC');
    }

    private function applySearchFilters(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('id', 'LIKE', "%$search%")
                ->orWhere('name', 'LIKE', "%$search%")
                ->orWhere('price', 'LIKE', "%$search%")
                ->orWhere('duration', 'LIKE', "%$search%")
                ->orWhere('tags', 'LIKE', "%$search%")
                ->orWhere('description', 'LIKE', "%$search%")
                ->orWhereHas('users', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%$search%")
                        ->orWhere('last_name', 'LIKE', "%$search%");
                });
        });
    }

    private function formatCoursesDataWithSuperTeacher(Collection $courses): array
    {
        $rows = [];
        $no = 1;

        foreach ($courses as $course) {
            $rows[] = [
                'id' => $course->id,
                'no' => $no++,
                'name' => $course->name,
                'image' => $course->thumbnail,
                'price' => $course->price,
                'duration' => $course->duration,
                'description' => $course->description,
                'category' => $course->course_category?->name,
                'tags' => $course->tags,
                'file' => $course->course_section ? $course->course_section->load('file') : '',
                'operate' => $this->generateOperateButtons($course),
                'super_teacher_id' => $course->users->pluck('id'),
                'super_teachers_name' => $course->users->pluck('full_name')
            ];
        }

        return $rows;
    }

    private function generateOperateButtons(Course $course): string
    {
        $editButton = sprintf(
            '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" 
                data-id="%d" title="Edit" data-toggle="modal" data-target="#editModal">
                <i class="fa fa-edit"></i>
            </a>',
            $course->id
        );

        $deleteButton = sprintf(
            '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" 
                data-id="%d" data-url="%s" title="Delete">
                <i class="fa fa-trash"></i>
            </a>',
            $course->id,
            url('course', $course->id)
        );

        return $editButton . '&nbsp;&nbsp;' . $deleteButton;
    }
}