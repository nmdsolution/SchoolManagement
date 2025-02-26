<?php

namespace App\Http\Controllers;

use App\Models\ClassSchool;
use App\Models\Mediums;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rawilk\Settings\Support\Context;
use Throwable;

class MediumController extends Controller {
    public function index() {
        if (!Auth::user()->can('medium-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);

        }
        return view('medium.index');
    }

    public function store(Request $request) {
        if (!Auth::user()->can('medium-create')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $request->validate([
            'name' => 'required'
        ]);
        try {
            $medium = new Mediums();
            $medium->name = $request->name;
            $medium->center_id = Auth::user()->center->id;
            $medium->save();
            $response = [
                'error'   => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (\Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function edit($id) {
        $medium = Mediums::find($id);
        return response($medium);
    }

    public function update(Request $request) {
        if (!Auth::user()->can('medium-edit')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $request->validate([
            'name' => 'required'
        ]);
        try {
            $medium = Mediums::find($request->id);
            $medium->name = $request->name;
            $medium->save();
            $response = [
                'error'   => false,
                'message' => trans('data_update_successfully'),
            ];
        } catch (Throwable $e) {
            $response = [
                'error'   => true,
                'message' => trans('error_occurred'),
            ];
        }
        return response()->json($response);
    }

    public function destroy($id) {
        if (!Auth::user()->can('medium-delete')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {
            //check wheather the class exists in other table
            $class = ClassSchool::where('medium_id', $id)->count();
            $subject = Subject::where('medium_id', $id)->count();

            if ($class || $subject) {
                $response = array(
                    'error'   => true,
                    'message' => trans('cannot_delete_because_data_is_associated_with_other_data')
                );
            } else {
                Mediums::find($id)->delete();
                $response = [
                    'error'   => false,
                    'message' => trans('data_delete_successfully')
                ];
            }
        } catch (\Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
            );
        }
        return response()->json($response);
    }

    public function show() {
        if (!Auth::user()->can('medium-list')) {
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

        $sql = Mediums::where('center_id', Auth::user()->center->id);
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")->orwhere('name', 'LIKE', "%$search%");
            })->where('center_id', Auth::user()->center->id);
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
//            $operate = '<a href=' . route('medium.edit', $row->id) . ' class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
//            $operate .= '<a href=' . route('medium.destroy', $row->id) . ' class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';

//            $operate = '<div class="actions"><a href="#" class="bg-success-light btn btn-sm me-2 edit-data" data-id=' . $row->id . ' title="Edit" data-bs-toggle="modal" data-bs-target="#editModal"><i class="feather-edit"></i></a>&nbsp;&nbsp;';
//            $operate .= '<a href=' . route('medium.destroy', $row->id) . ' class="btn btn-sm bg-danger-light delete-form" data-id=' . $row->id . '><i class="feather-trash"></i></a></div>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;
//            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function setActiveMedium(Request $request, $id = null) {
        $medium = Mediums::findOrFail($id);
        set_active_medium($medium->id);

        return redirect()->back();
    }
}
