<?php

namespace App\Domain\Course\Services;

use App\Domain\Course\Repositories\CourseCategoryRepository;
use App\Domain\Course\Repositories\CourseRepository;
use App\Domain\Course\Repositories\CourseTeacherRepository;
use App\Domain\User\Repositories\UserRepository;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CourseService
{
    public function __construct(
        private CourseRepository $courseRepository,
        private CourseTeacherRepository $courseTeacherRepository,
        private CourseCategoryRepository $courseCategoryRepository,
        private UserRepository $userRepository
        )
    {
        
    }

    public function createCourse(array $data)
    {

        // Traitement du fichier thumbnail
        $data['thumbnail'] = $data['thumbnail']->store('course_material', 'public');
        $data['course_category_id'] = $data['category_id'] != 0 ? $data['category_id'] : null;

        // Création du cours
        $course = $this->courseRepository->create([
            'name' => $data['name'],
            'price' => $data['price'],
            'duration' => $data['duration'],
            'description' => $data['description'],
            'course_category_id' => $data['course_category_id'],
            'thumbnail' => $data['thumbnail'],
            'tags' => $data['tags'],
        ]);

        // Ajout des enseignants au cours
        $this->courseRepository->attachTeachersToCourse($course->id, $data['super_teacher_ids']);

        return [
            'error' => false,
            'message' => trans('data_store_successfully')
        ];
    }


    public function updateCourse(Course $course, array $data): Course
    {
        return DB::transaction(function () use ($course, $data) {
            $updatedCourse = $this->courseRepository->updateCourse($course, $data);
            
            $this->courseRepository->updateSections(
                $updatedCourse,
                $data['old_files'] ?? [],
                $data['course_section'] ?? null
            );

            if (Auth::user()->hasRole('Super Admin')) {
                $this->courseTeacherRepository->updateTeachers($updatedCourse, $data['super_teacher_ids']);
            }

            return $updatedCourse;
        });
    }

    public function deleteCourse(int $courseId): void
    {
        DB::transaction(function () use ($courseId) {
            $this->courseRepository->deleteCourse($courseId);
        });
    }

    public function getMaterialData(int $courseId): array
    {
        $isSuperAdmin = Auth::user()->hasRole('Super Admin');
        
        $course = $this->courseRepository->getCourseWithMaterials($courseId, $isSuperAdmin);
        
        if (!$course && !$isSuperAdmin) {
            throw new AccessDeniedHttpException(trans('no_permission_message'));
        }

        $data = [
            'course' => $course,
            'categories' => $this->courseCategoryRepository->getCategoriesForDropdown(),
            'super_teachers' => '',
            'course_teacher' => ''
        ];

        if ($isSuperAdmin) {
            $data['super_teachers'] = $this->userRepository->getSuperTeachers();
            $data['course_teacher'] = $this->courseTeacherRepository
                ->getByCourseId($courseId)
                ->pluck('user_id');
        }

        if ($course->course_category_id === null) {
            $course->course_category_id = 0;
        }

        return $data;
    }

    public function deleteMaterial(int $sectionId): void
    {
        $this->courseRepository->deleteSectionWithMaterial($sectionId);
    }

    public function getCoursesData(array $params): array
    {
        return $this->courseRepository->getCoursesList($params);
    }
}