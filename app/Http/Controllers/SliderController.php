<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Slider;
use App\Models\SliderAccess;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (!Auth::user()->can('slider-list')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $centers = Center::get();
        $roles = Role::where('name', 'Teacher')->orWhere('name', 'Parent')->orWhere('name', 'Student')->get();
        return response(view('sliders.index', compact('centers', 'roles')));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('slider-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $validator = Validator::make($request->all(), [
            'image'     => 'required|mimes:jpeg,png,jpg|image|max:2048',
            'url'       => 'nullable|url',
            'center_id' => 'required|array',
            'role_id'   => 'required|array'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        try {
            DB::beginTransaction();
            $slider = new Slider();
            $slider->image = $request->file('image')->store('sliders', 'public');
            $slider->url = $request->url;
            $slider->save();

            $sliderAccess = [];
            foreach ($request->center_id as $center_id) {
                foreach ($request->role_id as $role_id) {
                    $sliderAccess[] = array(
                        'slider_id' => $slider->id,
                        'center_id' => $center_id,
                        'role_id'   => $role_id,
                    );
                }
            }
            SliderAccess::upsert($sliderAccess, ['slider_id', 'center_id', 'role_id'], ['slider_id', 'center_id', 'role_id']);
            DB::commit();
            $response = array(
                'error'   => false,
                'message' => trans('data_store_successfully'),
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
            );
        }
        return response()->json($response);
    }

    public function show(Request $request)
    {
        if (!Auth::user()->can('slider-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'DESC';
        $sql = Slider::with('center_access:id,name', 'role_access:id,name');
        if (!empty($request->search)) {
            $search = $request->search;
            $sql->whereHas('center_access', function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%");
            });
            $sql->orWhereHas('role_access', function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%");
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
            $tempRow['image'] = $row->image;
            $tempRow['url'] = $row->url;
            $tempRow['centers'] = $row->center_access;
            $tempRow['roles'] = $row->role_access;
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function update(Request $request, $id)
    {

        if (!Auth::user()->can('slider-edit')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $validator = Validator::make($request->all(), [
            'image'     => 'mimes:jpeg,png,jpg|image|max:2048',
            'url'       => 'nullable|url',
            'center_id' => 'required|array',
            'role_id'   => 'required|array'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        try {
            DB::beginTransaction();
            $slider = Slider::find($id);
            $slider->url = $request->url;
            if ($request->hasFile('image')) {
                if (Storage::disk('public')->exists($slider->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($slider->getRawOriginal('image'));
                }
                $slider->image = $request->file('image')->store('sliders', 'public');;
            }
            $slider->save();

            $sliderAccess = [];
            foreach ($request->center_id as $center_id) {
                foreach ($request->role_id as $role_id) {
                    $sliderAccess[] = array(
                        'slider_id' => $slider->id,
                        'center_id' => $center_id,
                        'role_id'   => $role_id,
                    );
                }
            }
            SliderAccess::upsert($sliderAccess, ['slider_id', 'center_id', 'role_id'], ['slider_id', 'center_id', 'role_id']);
            if ($request->delete_center_id) {
                SliderAccess::whereIn('center_id', $request->delete_center_id)->where('slider_id', $slider->id)->delete();
            }
            if ($request->delete_role_id) {
                SliderAccess::whereIn('role_id', $request->delete_role_id)->where('slider_id', $slider->id)->delete();
            }
            DB::commit();
            $response = array(
                'error'   => false,
                'message' => trans('data_update_successfully'),
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
            );
        }
        return response()->json($response);
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('slider-delete')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        try {
            $slider = Slider::find($id);
            if (Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }
            $slider->delete();
            $response = array(
                'error'   => false,
                'message' => trans('data_delete_successfully')
            );
        } catch (\Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }
}
