<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Subject;
use App\Models\Department;
use FontLib\Table\Type\name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subjects = Subject::owner()
            ->orderBy('id', 'DESC')
            ->where('medium_id', getCurrentMedium()->id)
            ->get();
        $departments = Department::owner()->currentMediumOnly()->get();
        $users = User::whereHas('teacher', function ($q) {
            return $q->whereHas('center_teacher', function ($q2) {
                return $q2->where('center_id', get_center_id());
            });
        })->get()->pluck('full_name', 'id'); // Charger tous les utilisateurs
        return view('department.index', compact('departments', 'subjects', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('department-create')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'responsible_id' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        try {
            Department::updateOrCreate(['name' => $request->name],
                [
                    'responsible_id' => $request->responsible_id,
                    'name' => $request->name,
                    'center_id' => get_center_id(),
                    'medium_id' => getCurrentMedium()->id,
                    'session_year_id' => getSessionYearData()->id,
                ]
            );

            Subject::whereIn('id', $request->subjects)->update(
                ['department_id' => Department::where('name', $request->name)->first()->id]
            );

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully')
            );
        } catch (\Throwable $th) {
            $response = array(
                'error' => true,
                'message' => $th->getMessage()
            );
        }

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        if (!Auth::user()->can('department-update')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        try {
            $department->name = $request->name;
            $department->responsible_id = $request->responsible_id;
            $department->subjects()->sync($request->subjects);
            $department->save();

            $response = array(
                'error' => false,
                'message' => trans('data_update_successfully')
            );
        } catch (\Throwable $th) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);

        # code...
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        if (!Auth::user()->can('department-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        try {
            DB::transaction(function () use($department) {
//                $department->subjects()->update(['department_id' => null]);
                DB::table('subjects')
                    ->where('department_id', $department->id)
                    ->update(['department_id' => null]);

                $department->delete();
            });
            $response = array(
                'error' => false,
                'message' => trans('data_delete_successfully')
            );
        } catch (\Throwable $th) {
            $response = array(
                'error' => true,
                'message' => $th->getMessage()
            );
        }
        return response()->json($response);
    }

    public function list()
    {
        if (!Auth::user()->can('department-list')) {
            $response = array(
                'error' => true,
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

        $sql = Department::owner()->currentMediumOnly()->with('responsible');
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%");
            })->Owner();
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
            $operate = '<a href=' . route('department.edit', $row->id) . ' class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a href=' . route('department.destroy', $row->id) . ' class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $tempRow['responsible'] = $row?->responsible?->full_name;
            $tempRow['subjects'] = $row->subjects->pluck('name', 'id');
            $tempRow['operate'] = $operate;
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }
}
