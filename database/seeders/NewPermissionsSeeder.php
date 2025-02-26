<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class NewPermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'exam-term-documents',
            'statistics',
            'timetable-settings',
            'income-list',
            'income-category',
            'best-report',
            'annual-report-card',
            'annual-master-sheet',
            'generate-id-card',
            'assign-class-subject',
            'fees-discount-list',
            'fees-discount-create',
            'fees-discount-edit',
            'fees-discount-delete'
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission], ['name' => $permission]);
        }

        $center_role = Role::where('name', 'Center')->first();
        $teacher_role = Role::where('name', 'Teacher')->first();

        $center_role->givePermissionTo($permissions);
        
        $teacher_permissions = [
            'best-report',
            'annual-report-card',
            'annual-master-sheet',
        ];
        $teacher_role->givePermissionTo($teacher_permissions);
    }
}
