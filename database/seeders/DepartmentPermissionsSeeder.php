<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'department-list',
            'department-create',
            'department-edit',
            'department-delete',
        ];

        $center_teacher_permissions = [
            'department-list'
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
