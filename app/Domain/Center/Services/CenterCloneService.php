<?php

namespace App\Domain\Center\Services;

use App\Models\ClassGroup;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\EffectiveDomain;
use App\Models\ElectiveSubjectGroup;
use App\Models\ExamResultGroup;
use App\Models\ExamResultGroupSubject;
use App\Models\Grade;
use App\Models\Group;
use App\Models\RoleHasPermission;
use App\Models\Section;
use App\Models\Settings;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\TimetableTemplate;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CenterCloneService
{
    public function cloneSectionClasses($from_center_id, $to_center_id, $medium_id): void
    {
        // finding all the sections that belong to the user in the from session year
        $sections = Section::with('classes.stream')->where('center_id', $from_center_id)->get();

        foreach ($sections as $section) {

            $toCenterSection = Section::query()->create([
                'name' => $section->name,
                'center_id' => $to_center_id
            ]);

            foreach ($section->classes as $class) {

                $streamId = !is_null($class->stream_id) ? Stream::query()->firstOrCreate([
                    'name' => $class->stream->name,
                    'center_id' => $to_center_id,
                ])->id : null;

                $newClass = ClassSchool::query()->firstOrCreate([
                    'name' => $class->name,
                    'center_id' => $to_center_id,
                    'medium_id' => $medium_id,
                    'stream_id' => $streamId,
                ]);

                ClassSection::query()->firstOrCreate([
                    'class_id' => $newClass->id,
                    'section_id' => $toCenterSection->id
                ]);
            }
        }
    }

    public function cloneClasses($from_center_id, $to_center_id, $medium_id): void
    {
        // fetching all the class schools from the center i am cloning from
        $classSchools = ClassSchool::query()->where([
            'center_id' => $from_center_id,
            'medium_id' => $medium_id
        ])->get();

        foreach ($classSchools as $class) {
            ClassSchool::query()->firstOrCreate([
                'name' => $class->name,
                'center_id' => $to_center_id,
                'medium_id' => $medium_id
            ]);
        }
    }

    public function cloneSubjects($from_center_id, $to_center_id, $medium_id): void
    {
        $subjects = Subject::query()->where([
            'center_id' => $from_center_id,
            'medium_id' => $medium_id
        ])->get();

        // creating all the subjects for the new center
        foreach ($subjects as $subject) {

            // this is going to either return the instance of the found subject, or it is going to create one.

            $created = Subject::query()->firstOrCreate([
                'center_id' => $to_center_id,
                'medium_id' => $medium_id,
                'name' => $subject->name,
                'type' => $subject->type,
            ], [
                'code' => $subject->code,
                'bg_color' => $subject->bg_color,
                'image' => $subject->image,
            ]);

            if (is_null($created)) {
                throw new \Exception('The Subject was not created');
            }
        }
    }


    public function cloneClassGroups($from_center_id, $to_center_id, $medium_id): void
    {
        // inserting the groups

        $groups = Group::query()->where('center_id', $from_center_id)->get();

        $groupsToInsert = $groups->map(function ($group) use ($to_center_id) {
            return [
                'name' => $group->name,
                'center_id' => $to_center_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        Group::query()->insert($groupsToInsert);


        // cloning all the class group relationships

        $classGroups = ClassGroup::query()
            ->with(['class', 'group'])
            ->where('center_id', $from_center_id)->get();

        $classGroupsToCreate = $classGroups->map(function ($classGroup) use (
            $from_center_id,
            $to_center_id,
            $medium_id
        ) {

            // looking for the class in the new center

            $class_id = ClassSchool::query()->where([
                'center_id' => $to_center_id,
                'medium_id' => $medium_id,
                'name' => $classGroup->class->name,
            ])->firstOrFail()->id;

            // looking for the group in the new center

            $group_id = Group::query()->where([
                'center_id' => $to_center_id,
                'name' => $classGroup->group->name,
            ])->firstOrFail()->id;

            // creating the instance for creation
            return [
                'class_id' => $class_id,
                'group_id' => $group_id,
                'center_id' => $to_center_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        ClassGroup::query()->insert($classGroupsToCreate);
    }


    public function cloneTimetableTemplate($from_center_id, $to_center_id, $medium_id): void
    {
        $timetableTemplate = TimetableTemplate::query()->where('center_id', $from_center_id)->first();

        if ($timetableTemplate?->exists) {
            TimetableTemplate::query()->create([
                'center_id' => $to_center_id,
                'periods' => $timetableTemplate->periods
            ]);
        }
    }

    public function cloneClassSubjectRelation($from_center_id, $to_center_id,  $medium_id): void
    {
        $classes = ClassSchool::query()->where([
            'center_id' => $from_center_id,
            'medium_id' => $medium_id,
        ])->get();

        foreach ($classes as $class) {
            $classSubjects = $class->allSubjects()->get();

            foreach ($classSubjects as $classSubject) {

                $new_class = ClassSchool::query()->firstOrCreate([
                    'name' => $class->name,
                    'center_id' => $to_center_id,
                    'medium_id' => $medium_id,
                ]);

                $new_class_id = $new_class->id;

                // getting the new Subject equivalent
                $new_subject = Subject::query()->where([
                    'name' => $classSubject->subject->name,
                    'center_id' => $to_center_id,
                    'medium_id' => $medium_id,
                    'type' => $classSubject->subject->type,
                ])->firstOrFail();

                $new_subject_id  = $new_subject->id;

                if (!$new_class_id || !$new_subject_id) {
                    throw new \Exception('Class or subject not found');
                }

                $electiveSubjectId = null;

                if (!is_null($classSubject->elective_subject_group_id)) {
                    $electiveSubjectGroup = ElectiveSubjectGroup::query()->find($classSubject->elective_subject_group_id);

                    $theClassId = ClassSchool::query()->where([
                        'center_id' => $to_center_id,
                        'medium_id' => $medium_id,
                        'name' => ClassSchool::findOrFail($electiveSubjectGroup->class_id)->name,
                    ])->firstOrFail()->id;

                    $electiveSubjectId = ElectiveSubjectGroup::query()->create([
                        'total_subjects' => $electiveSubjectGroup->total_subjects,
                        'total_selectable_subjects' => $electiveSubjectGroup->total_selectable_subjects,
                        'class_id' => $theClassId
                    ]);
                }

                // ClassSubject type can either be Compulsory or Elective.
                ClassSubject::query()->firstOrCreate([
                    'class_id' => $new_class_id,
                    'subject_id' => $new_subject_id,
                    'type' => $classSubject->type,
                    'weightage' => $classSubject->weightage,
                    'elective_subject_group_id' => $electiveSubjectId ?? null,
                ]);
            }
        }
    }


    public function cloneGrades($from_center_id, $to_center_id, $medium_id): void
    {
        $grades = Grade::query()->where([
            'center_id' => $from_center_id,
            'medium_id' => $medium_id
        ])->get();

        foreach ($grades as $grade) {

            Grade::query()->create([
                'center_id' => $to_center_id,
                'medium_id' => $medium_id,
                'starting_range' => $grade->starting_range,
                'ending_range' => $grade->ending_range,
                'grade' => $grade->grade,
                'remarks' => $grade->remarks,
            ]);
        }
    }

    public function cloneExamResultGroup($from_center_id, $to_center_id, $medium_id): void
    {
        $examResultGroups = ExamResultGroup::query()->where('center_id', $from_center_id)->get();

        foreach ($examResultGroups as $examResultGroup) {
            ExamResultGroup::query()->create([
                'name' => $examResultGroup->name,
                'position' => $examResultGroup->position,
                'center_id' => $to_center_id,
            ]);
        }
    }

    public function cloneExamResultSubjectGroup($from_center_id, $to_center_id, $medium_id): void
    {
        // looking for all the relation.
        $groupSubjects = ExamResultGroupSubject::query()
            ->where('center_id', $from_center_id)
            ->with(['subject', 'class', 'group'])
            ->get();

        $groupSubjectsToCreate = $groupSubjects->map(function ($groupSubject) use ($to_center_id, $medium_id) {
            $class = ClassSchool::query()
                ->where('center_id', $to_center_id)
                ->where('medium_id', $medium_id)
                ->where('name', $groupSubject->class->name)
                ->firstOrFail();


            $subject = Subject::query()
                ->where('center_id', $to_center_id)
                ->where('medium_id', $medium_id)
                ->where('name', $groupSubject->subject->name)
                ->where('type', $groupSubject->subject->type)
                ->firstOrFail();


            $group = ExamResultGroup::query()
                ->where('center_id', $to_center_id)
                ->where('name', $groupSubject->group->name)
                ->where('position', $groupSubject->group->position)
                ->firstOrFail();


            return [
                'subject_id' => $subject->id,
                'class_id' => $class->id,
                'center_id' => $to_center_id,
                'exam_result_group_id' => $group->id,
            ];
        })->toArray();

        ExamResultGroupSubject::query()->insert($groupSubjectsToCreate);
    }

    public function cloneReportCardSettings($from_center_id, $to_center_id, $medium_id): void
    {
        $settings = Settings::query()->where([
            'center_id' => $from_center_id,
            'medium_id' => $medium_id
        ])->get();

        $settingsToCreate = $settings->map(function ($setting) use ($to_center_id, $medium_id) {
            return [
                'center_id' => $to_center_id,
                'medium_id' => $medium_id,
                'type' => $setting->type,
                'message' => $setting->message,
                'data_type' => $setting->data_type,
            ];
        })->toArray();

        // taking advantage of batch processing
        Settings::query()->insert($settingsToCreate);
    }

    public function cloneEffectiveDomains($from_center_id, $to_center_id, $medium_id): void
    {
        $effectiveDomains = EffectiveDomain::query()->where([
            'center_id' => $from_center_id,
            'medium_id' => $medium_id
        ])->get();

        $domainsToCreate = $effectiveDomains->map(function ($domain) use ($to_center_id, $medium_id) {
            return [
                'center_id' => $to_center_id,
                'medium_id' => $medium_id,
                'name' => $domain->name,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        EffectiveDomain::query()->insert($domainsToCreate);
    }

    public function cloneRoles($from_center_id, $to_center_id, $medium_id): void
    {
        // Fetch all roles from the source center
        $roles = Role::query()->where([
            'center_id' => $from_center_id,
            'medium_id' => $medium_id
        ])->get();

        // going through all the roles to create an array for each record to be used for bulk upload.
        $rolesToCreate = $roles->map(function ($role) use ($from_center_id, $to_center_id, $medium_id) {

            $roleName = $to_center_id . "#" . produce_role_name($from_center_id, $role->id);

            return [
                'center_id' => $to_center_id,
                'medium_id' => $medium_id,
                'name' => $roleName,
                'guard_name' => $role->guard_name,
                'is_default' => $role->is_default,
                'created_at' => now(),
                'updated_at' => now()
            ];
        })->toArray();

        Role::query()->insert($rolesToCreate);
    }


    public function cloneRolePermissions($from_center_id, $to_center_id, $medium_id): void
    {
        $rolePermissions = RoleHasPermission::query()->where('medium_id', $medium_id)
            ->whereHas('role', function ($query) use ($from_center_id) {
                $query->where('center_id', $from_center_id);
            })->with('role')->get();

        $roles = Role::query()->where([
            'center_id' => $to_center_id,
            'medium_id' => $medium_id,
        ])->get()->keyBy('name');

        // Prepare data for bulk insert
        $relationsToClone = $rolePermissions->map(function ($rolePermission) use ($roles, $medium_id) {
            $roleName = $rolePermission->role->name;
            $role = $roles->get($roleName);

            if ($role) {
                return [
                    'medium_id' => $medium_id,
                    'permission_id' => $rolePermission->permission_id,
                    'role_id' => $role->id,
                ];
            }

            return null;
        })->filter()->values()->toArray();

        DB::transaction(function () use ($relationsToClone) {
            RoleHasPermission::query()->insert($relationsToClone);
        });
    }

    public function cloneCenter($from_center_id, $to_center_id, $medium_id)
    {
            $this->cloneClasses($from_center_id, $to_center_id, $medium_id);

            $this->cloneSubjects($from_center_id, $to_center_id, $medium_id);

            $this->cloneSectionClasses($from_center_id, $to_center_id, $medium_id);

            $this->cloneClassSubjectRelation($from_center_id, $to_center_id, $medium_id);

            // clone their timetable template too
            $this->cloneTimetableTemplate($from_center_id, $to_center_id, $medium_id);

            // clone the different groups that they created
            $this->cloneClassGroups($from_center_id, $to_center_id, $medium_id);

            // cloning the grades
            $this->cloneGrades($from_center_id, $to_center_id, $medium_id);

            // clone exam result group
            $this->cloneExamResultGroup($from_center_id, $to_center_id, $medium_id);

            $this->cloneExamResultSubjectGroup($from_center_id, $to_center_id, $medium_id);

            // clone the report card settings from the previous center to the current center
            $this->cloneReportCardSettings($from_center_id, $to_center_id, $medium_id);

            $this->cloneEffectiveDomains($from_center_id, $to_center_id, $medium_id);

            $this->cloneRoles($from_center_id, $to_center_id, $medium_id);

            $this->cloneRolePermissions($from_center_id, $to_center_id, $medium_id);

    }
}