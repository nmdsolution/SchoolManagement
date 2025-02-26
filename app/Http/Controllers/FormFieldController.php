<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormFieldRequest;
use App\Models\FormField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Contracts\Foundation\Application;
use \Illuminate\Http\RedirectResponse;
use \Illuminate\Routing\Redirector;
use \Illuminate\View\View;
use \Illuminate\Contracts\View\Factory;

class FormFieldController extends Controller
{
    private string $folder;

    public function __construct()
    {
        $this->folder = 'form-fields';
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(): Factory|View|Redirector|Application|RedirectResponse
    {
        if (!Auth::user()->can('form-field-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        // $formFields = FormField::orderBy('rank', 'ASC')->get();
        $formFields = FormField::owner()->orderBy('rank', 'ASC')->get();
        return view('form_field.index', compact('formFields'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FormFieldRequest $request
     * @return JsonResponse
     */
    public function store(FormFieldRequest $request): JsonResponse
    {
        try {
            if (Auth::user()->hasRole('Super Admin')) {
                $maxRank = FormField::max('rank');
            } else {
                $center = Auth::user()->center;
                $maxRank = FormField::where('center_id', $center->id)->max('rank');
            }
            FormField::create([
                "name" => str_replace(" ","_",$request->name),
                "type" => $request->type,
                "is_required" => isset($request->is_required) ? 1 : 0,
                "default_values" => $request->default_values ? json_encode(str_replace(" ","_",$request->default_values)) : '',
                "other" => $request->other,
                "center_id" => Auth::user()->hasRole('Center') ? Auth::user()->center->id : null,
                "rank" => $maxRank + 1
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

    /**
     * Display the specified resource.
     *
     * @param FormFieldRequest $request
     * @return JsonResponse
     */
    public function show(FormFieldRequest $request): JsonResponse
    {
//        if (!Auth::user()->can('form-field-list')) {
//            $response = array(
//                'message' => trans('no_permission_message')
//            );
//            return response()->json($response);
//        }

        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'rank';
        $order = $request->order ?? 'ASC';

        $sql = new FormField;
        if (Auth::user()->hasRole('Super Admin')) {
            $sql = $sql->where('center_id', null);
        } elseif (Auth::user()->hasRole('Center')) {
            $sql = $sql->where('center_id', Auth::user()->center->id);
        }
        if (!empty($request->search)) {
            $search = $request->search;
            $search_columns = array('type', 'default_value', 'other');
            $sql->where(function ($query) use ($search_columns, $search) {
                $query->orWhere($search_columns, 'LIKE', "%$search%");
            });
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order);
//            ->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        foreach ($res as $row) {
            $tempRow['id'] = $row->id;
            $tempRow['rank'] = $row->rank;
            $tempRow['name'] = str_replace("_"," ",$row->name);
            $tempRow['type'] = $row->type;
            $tempRow['is_required'] = $row->is_required;
            $tempRow['default_values'] = json_decode($row->default_values, true);
            $tempRow['other'] = $row->other;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function update(FormFieldRequest $request, $id)
    {
        try {
            $data = FormField::findOrFail($id);
            $data->name = $request->name;
            $data->type = $request->type;
            $data->is_required = isset($request->is_required) ? 1 : 0;
            $data->default_values = $request->default_values ? json_encode($request->default_values) : '';
            $data->other = $request->other;
            $data->center_id = Auth::user()->hasRole('Center') ? Auth::user()->center->id : null;
            $data->save();
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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            FormField::findOrFail($id)->delete();
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

    public function changeRank(Request $request): JsonResponse
    {
        try {
            $ids = json_decode($request->ids);
            $update = [];
            foreach ($ids as $key => $id) {
                $update[] = [
                    'id' => $id,
                    'rank' => ($key + 1)
                ];
            }
            FormField::upsert($update, ['id'], ['rank']);
            $response = [
                'error' => false,
                'message' => trans('Rank Updated Successfully')
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

    public function importIndex(): Factory|View|Redirector|Application|RedirectResponse
    {
        if (!Auth::user()->can('form-field-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $formFields = FormField::where('center_id', null)->orderBy('rank', 'ASC')->get();
        return view('form_field.import', compact('formFields'));
    }

    public function importShow(FormFieldRequest $request): JsonResponse
    {
//        if (!Auth::user()->can('form-field-list')) {
//            $response = array(
//                'message' => trans('no_permission_message')
//            );
//            return response()->json($response);
//        }

        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'rank';
        $order = $request->order ?? 'ASC';
        $center_fields = FormField::where('center_id', Auth::user()->center->id)->select('name')->pluck('name');

        $sql = FormField::whereNotIn('name', $center_fields);
        if (!empty($request->search)) {
            $search = $request->search;
            $search_columns = array('type', 'default_value', 'other');
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
        foreach ($res as $row) {
            $tempRow['id'] = $row->id;
            $tempRow['rank'] = $row->rank;
            $tempRow['name'] = str_replace("_"," ",$row->name);
            $tempRow['type'] = $row->type;
            $tempRow['is_required'] = $row->is_required;
            $tempRow['default_values'] = json_decode($row->default_values, true);
            $tempRow['other'] = $row->other;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function importStore(Request $request, $id): JsonResponse
    {
        try {
            if (Auth::user()->hasRole('Super Admin')) {
                $maxRank = FormField::max('rank');
            } else {
                $center = Auth::user()->center;
                $maxRank = FormField::where('center_id', $center->id)->max('rank');
            }
            $data = FormField::findOrFail($id)->replicate();

            $data->center_id = Auth::user()->hasRole('Center') ? Auth::user()->center->id : null;
            $data->rank = $maxRank + 1;
            $data->save();
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
