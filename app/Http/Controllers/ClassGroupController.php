<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Models\ClassSchool;
use App\Models\Group;
use App\Printing\AcademicPrints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ClassGroupController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('class-group-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $classes = ClassSchool::owner()->with('stream')->activeMediumOnly()->get();
        $groups = Group::owner()->with('classes.stream')->with('classes', function ($q) {
            $q->activeMediumOnly();
        })->get();
        return view('class.group', compact('classes', 'groups'));
    }


    public function store(Request $request)
    {
        if (!Auth::user()->can('class-group-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        try {
            Group::create([
                "name" => $request->name,
                "center_id" => Auth::user()->center->id,
            ]);
            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }


    public function show(Request $request)
    {
        if (!Auth::user()->can('class-group-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'DESC';
        $print = $request->get('print') ?? false;

        $sql = Group::owner();
        if (!empty($request->search)) {
            $search = $request->search;
            $search_columns = array('id', 'name');
            $sql->where(function ($query) use ($search_columns, $search) {
                foreach ($search_columns as $search_column) {
                    $query->orWhere($search_column, 'LIKE', "%$search%");
                }
            });
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
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            
            if($print){
                $tempRow['classes'] = $row->classes;
            }

            $rows[] = $tempRow;
        }

        if($print){
            $pdf = AcademicPrints::getInstance(get_center_id());
            $pdf->printClassGroupsList($rows);


            return response(
                $pdf->Output('', 'CLASS GROUPS LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('class-group-edit')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        try {
            $group = Group::findOrFail($id);
            $group->name = $request->name;
            $group->save();
            $response = [
                'error' => false,
                'message' => trans('data_update_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }


    public function destroy($id)
    {
        if (!Auth::user()->can('class-group-delete')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        try {
            Group::findOrFail($id)->delete();
            $response = [
                'error' => false,
                'message' => trans('data_delete_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }

    public function edit(Request $request, $id)
    {
        if (!Auth::user()->can('class-group-edit')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $classes = ClassSchool::owner()->activeMediumOnly()->get();
        $groups = Group::owner()->with('classes', function ($q) {
            $q->activeMediumOnly();
        })->get();

        return view('class.assign-class-group', compact('classes', 'id', 'groups'));
    }

    public function assignedClassGroupList(): \Illuminate\Http\JsonResponse
    {
        if (!Auth::user()->can('exam-result-subject-group-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'DESC';

        $sql = ClassSchool::owner()->activeMediumOnly()->with(['examResultSubjectGroups.subject', 'examResultSubjectGroups.group']);

        if (!empty($request->search)) {
            $search = $request->search;
            $search_columns = array('id', 'user_id');
            $sql->where(function ($query) use ($search_columns, $search) {
                $query->orWhere($search_columns, 'LIKE', "%$search%");
            });
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
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $tempRow['exam_result_subject_group'] = [];
            foreach ($row->examResultSubjectGroups->toArray() as $subjectGroup) {
                if (!empty($subjectGroup['exam_result_group_id'])) {
                    $groupId = $subjectGroup['exam_result_group_id'];
                    $group = $subjectGroup['group'];
                    $subject = $subjectGroup['subject'];
                    $tempRow['exam_result_subject_group'][$groupId] = $group;
                    $tempRow['exam_result_subject_group'][$groupId]['subjects'][] = $subject;
                }
            }
            $tempRow['exam_result_subject_group'] = array_values($tempRow['exam_result_subject_group']);
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function addClassInGroup(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|numeric',
            'group_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            ClassGroup::query()->updateOrCreate(
                ['group_id' => $request->group_id, 'class_id' => $request->class_id, 'center_id' => Auth::user()->center->id],
                ['group_id' => $request->group_id],
            );
            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }

    public function removeClassFromGroup(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|numeric',
            'class_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $classGroup = ClassGroup::where([
                'group_id' => $request->group_id,
                'class_id' => $request->class_id,
                'center_id' => Auth::user()->center->id
            ])->firstOrFail();
            $classGroup->delete();
            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }
}
