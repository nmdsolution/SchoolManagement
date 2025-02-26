<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Teacher;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use App\Printing\AcademicPrints;
use Illuminate\Support\Facades\Auth;

class ClassTeacherController extends Controller {
    public function teacher() {
        if (!Auth::user()->can('class-teacher-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $class_section = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        $class_teacher_ids = ClassSection::owner()->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->whereNot('class_teacher_id', null)->pluck('class_teacher_id');
        $teachers = Teacher::owner()->with('user')->whereNotIn('id', $class_teacher_ids)->get();
        $classes = ClassSchool::owner()->with('stream')->activeMediumOnly()->orderBy('id', 'DESC')->get();
        return view('class.teacher', compact('class_section', 'teachers', 'classes'));
    }

    public function assign_teacher(Request $request) {
        if (!Auth::user()->can('class-teacher-edit')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $request->validate([
            'class_section_id' => 'required',
            'teacher_id'       => 'required',
        ]);
        try {
            $teacher = Teacher::findOrFail($request->teacher_id);
            $assign_teacher = ClassSection::find($request->class_section_id);
            if ($assign_teacher->class_teacher_id && $assign_teacher->class_teacher_id != $request->teacher_id) {
                //If Old teacher is removed and new teacher is assigned as class teacher then remove old teacher's permission
                $old_teacher = Teacher::find($request->teacher_id)->with('user')->first();
                $old_teacher->user->removeRole('Class Teacher');
                //                $old_teacher->user->revokePermissionTo('class-teacher');
            }
            $assign_teacher->class_teacher_id = $request->teacher_id;
            $assign_teacher->save();
            $teacher->user->assignRole('Class Teacher');
            //            $teacher->user->givePermissionTo('class-teacher');

            $response = [
                'error'   => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
            );
        }
        return response()->json($response);
    }

    public function show() {
        if (!Auth::user()->can('class-teacher-list')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            $sort = $_GET['sort'];
        if (isset($_GET['order']))
            $order = $_GET['order'];

        $sql = ClassSection::owner()->with('class.stream', 'section', 'teacher')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        });
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")
                ->orWhereHas('class', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('section', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })
                ->orWhereHas('teacher.user', function ($q) use ($search) {
                    $q->whereRaw("concat(users.first_name,' ',users.last_name) LIKE '%" . $search . "%'")
                        ->orwhere('users.first_name', 'LIKE', "%$search%")
                        ->orwhere('users.last_name', 'LIKE', "%$search%");
                });
        }

        // if (isset($_GET['class_id'])) {
        //     $sql = $sql->where('class_id', $_GET['class_id']);
        // }

        if (isset($_GET['class_id']) && $_GET['class_id']) {
            $sql = $sql->where('class_id', $_GET['class_id']);
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';

            $tempRow['id'] = $row->id;
            $tempRow['class_id'] = $row->class_id;
            $tempRow['section_id'] = $row->section_id;
            $tempRow['teacher_id'] = $row->class_teacher_id;
            $tempRow['no'] = $no++;
            $tempRow['class'] = $row->class->full_name;
            $tempRow['section'] = $row->section->name;
            $tempRow['teacher'] = ($row->teacher) ? ($row->teacher->user->first_name . ' ' . $row->teacher->user->last_name) : '';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        if(request()->get('print')){
            $pdf = AcademicPrints::getInstance(get_center_id());
            $pdf->printClassTeacherList($rows);


            return response(
                $pdf->Output('', 'CLASS TEACHERS LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function removeClassTeacher($id) {
        try {
            $class_section = ClassSection::find($id);

            $teacher_id = $class_section->class_teacher_id;
            $old_teacher = Teacher::where('id', $teacher_id)->with('user')->first();
            $old_teacher->user->removeRole('Class Teacher');
            //        $old_teacher->user->revokePermissionTo('class-teacher');

            $class_section->class_teacher_id = null;
            $class_section->save();

            $response = [
                'error'   => false,
                'message' => trans('data_delete_successfully')
            ];

        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
            );
        }
        return response()->json($response);
    }


}
