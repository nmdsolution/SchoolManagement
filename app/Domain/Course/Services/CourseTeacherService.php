<?php

namespace App\Domain\Course\Services;

use App\Models\Course;
use App\Models\CourseTeacher;
use Illuminate\Support\Facades\Auth;

class CourseTeacherService
{
    public function updateTeachers(Course $course, array $teacherIds): void
    {
        if (!Auth::user()->hasRole('Super Admin')) {
            return;
        }

        CourseTeacher::where('course_id', $course->id)->delete();
        
        $teachers = array_map(function($teacherId) use ($course) {
            return [
                'course_id' => $course->id,
                'user_id' => $teacherId
            ];
        }, $teacherIds);

        CourseTeacher::insert($teachers);
    }
}