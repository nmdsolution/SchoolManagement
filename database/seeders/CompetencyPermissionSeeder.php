<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CompetencyPermissionSeeder extends Seeder {

    public function run() {
        $permissions = [
            'competency-domain-list',
            'competency-domain-create',
            'competency-domain-edit',
            'competency-domain-delete',

            'competency-list',
            'competency-create',
            'competency-edit',
            'competency-delete',

            'competency-type-list',
            'competency-type-create',
            'competency-type-edit',
            'competency-type-delete',
            'competency-type-assign-class',

            'class-competency-list',
            'class-competency-create',
            'class-competency-edit',
            'class-competency-delete',
        ];

        $center_teacher_permissions = [
            'upload-marks-per-competency',
            'upload-marks-per-student',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission], ['name' => $permission]);
        }

        foreach ($center_teacher_permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission], ['name' => $permission]);
        }

        $center_role = Role::where('name', 'Center')->first();
        $teacher_role = Role::where('name', 'Teacher')->first();

        $center_role->givePermissionTo([...$permissions, ...$center_teacher_permissions]);
        
        $teacher_role->givePermissionTo($center_teacher_permissions);
    }
}
