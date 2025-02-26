<?php

namespace App\Http\Controllers;

use App\Models\ClassSchool;
use App\Models\ExamResultGroup;
use App\Models\ExamResultGroupSubject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ExamResultGroupController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('exam-result-subject-group-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $classes = ClassSchool::owner()->activeMediumOnly()->get();
        return view('exams.subject-group', compact('classes'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => 'required']);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $examResultGroup = new ExamResultGroup();
            $examResultGroup->name = $request->name;
            $examResultGroup->position = $request->position;
            $examResultGroup->center_id = Auth::user()->center->id;
            $examResultGroup->save();
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

    public function show()
    {
        if (!Auth::user()->can('exam-result-subject-group-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;

        $sql = ExamResultGroup::owner();
        if (!empty($request->search)) {
            $search = $request->search;
            $search_columns = array('id', 'name');
            $sql->where(function ($query) use ($search_columns, $search) {
                $query->orWhere($search_columns, 'LIKE', "%$search%");
            });
        }
        $total = $sql->count();

        $sql->orderBy('position', 'asc')->skip($offset)->take($limit);
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
            $tempRow['position'] = $row->position;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        if (!Auth::user()->can('exam-result-subject-group-edit')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class = ClassSchool::owner()->findOrFail($id);
        $subjectGroups = ExamResultGroup::owner()->with('subjects', function ($q) use ($class) {
            $q->withPivot('class_id')->wherePivot('class_id', $class->id);
        })->get();
        $alreadyAddedSubjectIDs = $subjectGroups->pluck('subjects.*.id')->flatten();
        $subjects = Subject::query()->whereNotIn('id', $alreadyAddedSubjectIDs)->whereHas('classSubject', static function ($q) use ($class) {
            $q->where('class_id', $class->id);
        })->get();

        return view('exams.edit-subject-group', compact('class', 'subjects', 'subjectGroups', 'id'));
    }


    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), ['name' => 'required']);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        try {
            $examResultGroup = ExamResultGroup::findOrFail($id);
            $examResultGroup->name = $request->name;
            $examResultGroup->position = $request->position;
            $examResultGroup->save();
            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            );
        }
        return response()->json($response);
    }

    public function destroy($id)
    {
        try {
            ExamResultGroup::findOrFail($id)->delete();
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

    public function assignClassSubjectGroupList(Request $request): \Illuminate\Http\JsonResponse
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

        $sql = ClassSchool::owner()->activeMediumOnly()->with(['stream', 'examResultSubjectGroups.subject', 'examResultSubjectGroups.group']);

        $search_columns = ['id', 'user_id'];
        if (!empty($request->search)) {
            $search = $request->search;
            $sql->where(function ($query) use ($search_columns, $search) {
                foreach ($search_columns as $column) {
                    $query->orWhere($column, 'LIKE', "%$search%");
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
            $tempRow['name'] = $row->full_name;
            $tempRow['exam_result_subject_group'] = [];

            foreach ($row->examResultSubjectGroups->toArray() as $subjectGroup) {
                if (!empty($subjectGroup['exam_result_group_id'])) {
                    $groupId = $subjectGroup['exam_result_group_id'];
                    $group = $subjectGroup['group'];
                    $subject = $subjectGroup['subject'];
                    if (!isset($tempRow['exam_result_subject_group'][$groupId])) {
                        $tempRow['exam_result_subject_group'][$groupId] = $group;
                    }
                    $tempRow['exam_result_subject_group'][$groupId]['subjects'][] = $subject;
                }
            }
            $tempRow['exam_result_subject_group'] = array_values($tempRow['exam_result_subject_group']);
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function assignClassSubjectToGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|numeric',
            'class_id' => 'required|numeric',
            'exam_result_group_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            ExamResultGroupSubject::updateOrCreate(
                ['subject_id' => $request->subject_id, 'class_id' => $request->class_id, 'center_id' => Auth::user()->center->id],
                ['exam_result_group_id' => $request->exam_result_group_id],
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

    public function deleteClassSubjectFromGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|numeric',
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
            $examResultGroupSubject = ExamResultGroupSubject::where([
                'subject_id' => $request->subject_id,
                'class_id' => $request->class_id,
                'center_id' => Auth::user()->center->id
            ])->firstOrFail();
            $examResultGroupSubject->delete();
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
