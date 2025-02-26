<?php

namespace Database\Seeders;

use App\Models\Mediums;
use App\Models\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InstallationSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        //Add Permissions
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'medium-list',
            'medium-create',
            'medium-edit',
            'medium-delete',

            'section-list',
            'section-create',
            'section-edit',
            'section-delete',

            'class-list',
            'class-create',
            'class-edit',
            'class-delete',

            'subject-list',
            'subject-create',
            'subject-edit',
            'subject-delete',

            'teacher-list',
            'teacher-create',
            'teacher-edit',
            'teacher-delete',

            'class-teacher-list',
            'class-teacher-create',
            'class-teacher-edit',
            'class-teacher-delete',

            'parents-list',
            'parents-create',
            'parents-edit',
            'parents-delete',

            'session-year-list',
            'session-year-create',
            'session-year-edit',
            'session-year-delete',

            'student-list',
            'student-create',
            'student-edit',
            'student-delete',
            'student-id-card',

            'subject-teacher-list',
            'subject-teacher-create',
            'subject-teacher-edit',
            'subject-teacher-delete',

            'timetable-list',
            'timetable-create',
            'timetable-edit',
            'timetable-delete',

            'attendance-list',
            'attendance-create',
            'attendance-edit',
            'attendance-delete',

            'holiday-list',
            'holiday-create',
            'holiday-edit',
            'holiday-delete',

            'announcement-list',
            'announcement-create',
            'announcement-edit',
            'announcement-delete',

            'class-timetable',
            'teacher-timetable',
            'student-assignment',
            'subject-lesson',
            'class-attendance',

            'create-specific-exam',
            'list-specific-exam',
            'exam-edit',
            'exam-delete',

            'list-sequential-exam',
            'exam-publish',
            'exam-upload-marks',
            'exam-report',
            'exam-timetable-list',
            'exam-timetable-edit',
            'exam-result-subject-group-create',
            'exam-result-subject-group-list',
            'exam-result-subject-group-edit',
            'exam-result-subject-group-delete',

            'setting-create',
            'fcm-setting-create',

            'assignment-create',
            'assignment-list',
            'assignment-edit',
            'assignment-delete',
            'assignment-submission',

            'email-setting-create',
            'privacy-policy',
            'contact-us',
            'about-us',

            'student-reset-password',
            'reset-password-list',
            'student-change-password',

            'promote-student-list',
            'promote-student-create',
            'promote-student-edit',
            'promote-student-delete',

            'language-list',
            'language-create',
            'language-edit',
            'language-delete',

            'lesson-list',
            'lesson-create',
            'lesson-edit',
            'lesson-delete',

            'topic-list',
            'topic-create',
            'topic-edit',
            'topic-delete',

            'terms-condition',

            'assign-class-to-new-student',
            'exam-timetable-create',
            'grade-create',
            'update-admin-profile',
            'exam-result',

            'fees-type',
            'fees-classes',
            'fees-paid',
            'fees-config',

            'manage-online-exam',

            'form-field-list',
            'form-field-create',
            'form-field-edit',
            'form-field-delete',

            'user-create',
            'user-list',
            'user-edit',
            'user-delete',

            'exam-term-create',
            'exam-term-list',
            'exam-term-edit',
            'exam-term-delete',

            'exam-sequence-create',
            'exam-sequence-list',
            'exam-sequence-edit',
            'exam-sequence-delete',

            'course-create',
            'course-list',
            'course-edit',
            'course-delete',
            'course-report',

            'expense-create',
            'expense-list',
            'expense-edit',
            'expense-delete',

            'event-create',
            'event-list',
            'event-edit',
            'event-delete',

            'salary-paid',

            'class-group-create',
            'class-group-list',
            'class-group-edit',
            'class-group-delete',

            'stream-list',
            'stream-create',
            'stream-edit',
            'stream-delete',

            'shift-list',
            'shift-create',
            'shift-edit',
            'shift-delete',
        ];
        foreach ($permissions as $key => $permission) {
            Permission::UpdateOrCreate(['name' => $permission], ['name' => $permission]);
        }


        // Add persmissions for Super Admin
        $super_admin_permissions = [
            'center-list',
            'center-create',
            'center-edit',
            'center-delete',

            'slider-list',
            'slider-create',
            'slider-edit',
            'slider-delete',

            'super-teacher-create',
            'super-teacher-list',
            'super-teacher-edit',
            'super-teacher-delete',

            'app-setting-create',

            'class-report',
        ];

        foreach ($super_admin_permissions as $key => $permission) {
            Permission::UpdateOrCreate(['name' => $permission,'type' => 1]);
        }

        $role = Role::updateOrCreate(['name' => 'Super Admin'], ['name' => 'Super Admin', 'is_default' => 1]);
        $superadmin_permission_list = [
            'slider-list',
            'slider-create',
            'slider-edit',
            'slider-delete',

            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            // 'setting-create',
            'fcm-setting-create',
            'email-setting-create',
            'privacy-policy',
            'terms-condition',
            'contact-us',
            'about-us',

            'language-list',
            'language-create',
            'language-edit',
            'language-delete',

            'update-admin-profile',
            'center-list',
            'center-create',
            'center-edit',
            'center-delete',

            'form-field-list',
            'form-field-create',
            'form-field-edit',
            'form-field-delete',

            'super-teacher-create',
            'super-teacher-list',
            'super-teacher-edit',
            'super-teacher-delete',

            'course-create',
            'course-list',
            'course-edit',
            'course-delete',
            'course-report',
            'app-setting-create'
        ];
        $role->syncPermissions($superadmin_permission_list);

        $role = Role::updateOrCreate(['name' => 'Super Teacher'], ['name' => 'Super Teacher', 'is_default' => 1]);
        $superteacher_list = [
            'course-list',
            'course-edit',
            'course-report',
        ];
        $role->syncPermissions($superteacher_list);


        $role = Role::updateOrCreate(['name' => 'Center'], ['name' => 'Center', 'is_default' => 1]);
        $center_list = [
            'section-list',
            'section-create',
            'section-edit',
            'section-delete',

            'class-list',
            'class-create',
            'class-edit',
            'class-delete',

            'subject-list',
            'subject-create',
            'subject-edit',
            'subject-delete',

            'teacher-list',
            'teacher-create',
            'teacher-edit',
            'teacher-delete',

            'class-teacher-list',
            'class-teacher-create',
            'class-teacher-edit',
            'class-teacher-delete',

            'parents-list',
            'parents-create',
            'parents-edit',
            'parents-delete',

            'session-year-list',
            'session-year-create',
            'session-year-edit',
            'session-year-delete',

            'student-list',
            'student-create',
            'student-edit',
            'student-delete',
            'student-id-card',

            'subject-teacher-list',
            'subject-teacher-create',
            'subject-teacher-edit',
            'subject-teacher-delete',

            'timetable-list',
            'timetable-create',
            'timetable-edit',
            'timetable-delete',

            'attendance-list',

            'holiday-list',
            'holiday-create',
            'holiday-edit',
            'holiday-delete',

            'announcement-list',
            'announcement-create',
            'announcement-edit',
            'announcement-delete',

            'class-timetable',
            'teacher-timetable',
            'student-assignment',
            'subject-lesson',
            'class-attendance',

            'create-specific-exam',
            'list-specific-exam',
            'list-sequential-exam',
            'exam-edit',
            'exam-delete',
            'exam-publish',
            'exam-timetable-create',
            'grade-create',

            'assignment-submission',

            'student-reset-password',
            'reset-password-list',
            'student-change-password',

            'promote-student-list',
            'promote-student-create',
            'promote-student-edit',
            'promote-student-delete',

            'assign-class-to-new-student',

            'fees-type',
            'fees-classes',
            'fees-paid',
            'fees-config',

            'form-field-list',
            'form-field-create',
            'form-field-edit',
            'form-field-delete',

            'fees-config',

            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'user-create',
            'user-list',
            'user-edit',
            'user-delete',

            'exam-term-create',
            'exam-term-list',
            'exam-term-edit',
            'exam-term-delete',

            'exam-sequence-create',
            'exam-sequence-list',
            'exam-sequence-edit',
            'exam-sequence-delete',
            'setting-create',

            'exam-timetable-list',
            'exam-timetable-edit',
            'exam-result',
            'exam-result-subject-group-create',
            'exam-result-subject-group-list',
            'exam-result-subject-group-edit',
            'exam-result-subject-group-delete',
            'exam-report',

            'expense-create',
            'expense-list',
            'expense-edit',
            'expense-delete',

            'event-create',
            'event-list',
            'event-edit',
            'event-delete',

            'salary-paid',
            'class-group-create',
            'class-group-list',
            'class-group-edit',
            'class-group-delete',

            'class-report',
            'exam-upload-marks',

            'stream-list',
            'stream-create',
            'stream-edit',
            'stream-delete',

            'shift-list',
            'shift-create',
            'shift-edit',
            'shift-delete',
        ];
        $role->syncPermissions($center_list);

        //Add Teacher Role
        $teacher_role = Role::updateOrCreate(['name' => 'Teacher'], ['name' => 'Teacher', 'is_default' => 1]);
        $teacher_permissions_list = [
            'student-list',
            // 'subject-teacher-list',
            'timetable-list',

            // 'attendance-list',
            // 'attendance-create',
            // 'attendance-edit',
            // 'attendance-delete',

            'holiday-list',

            'announcement-list',
            'announcement-create',
            'announcement-edit',
            'announcement-delete',

            'class-timetable',
            'teacher-timetable',
            'student-assignment',
            'subject-lesson',
            'class-attendance',

            'assignment-create',
            'assignment-list',
            'assignment-edit',
            'assignment-delete',
            'assignment-submission',

            'lesson-list',
            'lesson-create',
            'lesson-edit',
            'lesson-delete',

            'topic-list',
            'topic-create',
            'topic-edit',
            'topic-delete',

            'list-sequential-exam',
            'list-specific-exam',
            'exam-edit',
            'exam-delete',
            'exam-publish',
            'exam-upload-marks',
            'exam-result',
            'manage-online-exam',
            'event-list',
        ];
        $teacher_role->syncPermissions($teacher_permissions_list);

        // Add Parent and Student Role
        Role::updateOrCreate(['name' => 'Parent'], ['name' => 'Parent', 'is_default' => 1]);
        Role::updateOrCreate(['name' => 'Student'], ['name' => 'Student', 'is_default' => 1]);
        Role::updateOrCreate(['name' => 'Super Teacher'], ['name' => 'Super Teacher', 'is_default' => 1]);
        $classTeacherRole = Role::updateOrCreate(['name' => 'Class Teacher'], ['name' => 'Class Teacher', 'is_default' => 1]);
        $classTeacherRole->syncPermissions([
            'exam-publish',
            'attendance-list',
            'attendance-create',
            'attendance-edit',
            'attendance-delete',
        ]);

        $classTeacherRole = Role::updateOrCreate(['name' => 'Manage Student & Parent'], ['name' => 'Manage Student & Parent', 'is_default' => 1]);
        $classTeacherRole->syncPermissions([
            'student-create',
            'student-list',
            'student-edit',
            'student-delete',
            'parents-create',
            'parents-list',
            'parents-edit'
        ]);

        // Anglophone or Francophone
        // Create Medium
        Mediums::UpdateOrCreate(['id' => 1], ['name' => 'Anglophone']);
        Mediums::UpdateOrCreate(['id' => 2], ['name' => 'Francophone']);


        //Change system version here
        Settings::updateOrCreate(['type' => 'system_version'], ['message' => '1.0.0']);

        //clear cache
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
    }
}
