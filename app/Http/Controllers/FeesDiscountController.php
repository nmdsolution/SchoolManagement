<?php

namespace App\Http\Controllers;

use App\Models\FeesDiscount;
use App\Models\Students;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class FeesDiscountController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('fees-discount-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $statusOptions = collect(["Not applicable", "Handicap", "Refugee", "Orphan"])
            ->mapWithKeys(function ($status) {
                return [$status => __($status)];
            })->toArray();

        return view('fees.discounts.index', compact('statusOptions'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('fees-discount-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        try {
            $request->validate([
                'name' => 'required|string',
                'amount' => 'required|numeric|min:0|max:100',
                'applicable_status' => 'required|array',
                'description' => 'nullable|string'
            ]);

            DB::beginTransaction();
            
            $center_id = get_center_id();

            FeesDiscount::create([
                'name' => $request->name,
                'amount' => $request->amount,
                'applicable_status' => json_encode($request->applicable_status),
                'description' => $request->description,
                'center_id' => $center_id
            ]);

            DB::commit();

            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            );
            logger($e->getMessage());
        }
        return response()->json($response);
    }

    public function show(Request $request)
    {
        if (!Auth::user()->can('fees-discount-list')) {
            return response()->json(['message' => trans('no_permission_message')], 403);
        }
    
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');
        $search = $request->input('search');
    
        $query = FeesDiscount::owner();
    
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%")
                      ->orWhere('name', 'LIKE', "%$search%")
                      ->orWhere('description', 'LIKE', "%$search%");
            });
        }
    
        $total = $query->count();
        $feeDiscounts = $query->orderBy($sort, $order)
                              ->skip($offset)
                              ->take($limit)
                              ->get();
    
        $bulkData = [
            'total' => $total,
            'rows' => [],
        ];
    
        $no = 1;
        foreach ($feeDiscounts as $feeDiscount) {
            $operate = '<a href="#" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" 
                        data-id="' . $feeDiscount->id . '" 
                        title="Edit" 
                        data-toggle="modal" 
                        data-target="#editModal"><i class="fa fa-edit"></i></a>';
            $operate .= '<a href="#" class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-data" 
                        data-id="' . $feeDiscount->id . '" 
                        data-url="' . route('fees.discounts.destroy', $feeDiscount->id) . '" 
                        title="Delete"><i class="fa fa-trash"></i></a>';
    
            $bulkData['rows'][] = [
                'no' => $no++,
                'id' => $feeDiscount->id,
                'name' => $feeDiscount->name,
                'amount' => $feeDiscount->amount,
                'applicable_status' => implode(', ', json_decode($feeDiscount->applicable_status) ?? []),
                'description' => $feeDiscount->description,
                'active' => $feeDiscount->active 
                    ? '<span class="badge badge-success">Active</span>' 
                    : '<span class="badge badge-danger">Inactive</span>',
                'checkActive' => $feeDiscount->active,
                'created_at' => $feeDiscount->created_at->toDateString(),
                'updated_at' => $feeDiscount->updated_at->toDateString(),
                'operate' => $operate,
            ];
        }
    
        return response()->json($bulkData);
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('fees-discount-edit')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        try {
            $request->validate([
                'edit_name' => 'sometimes|string',
                'edit_amount' => 'sometimes|numeric|min:0',
                'edit_applicable_status' => 'sometimes|array',
                'edit_description' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $discount = FeesDiscount::findOrFail($id);
            $discount->update([
                'name' => $request->edit_name,
                'amount' => $request->edit_amount,
                'applicable_status' => json_encode($request->edit_applicable_status),
                'description' => $request->edit_description,
            ]);

            DB::commit();

            $response = [
                'error' => false,
                'message' => trans('data_update_successfully')
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            );
            logger($e->getMessage());
        }
        return response()->json($response);
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('fees-discount-delete')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        try {
            FeesDiscount::findOrFail($id)->delete();
            $response = [
                'error' => false,
                'message' => trans('data_delete_successfully')
            ];
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            );
        }
        return response()->json($response);
    }

    public function toggleStatus($id)
    {
        DB::beginTransaction();

        try {
            $discount = FeesDiscount::findOrFail($id);
    
            // Toggle the active status
            $discount->active = !$discount->active;
            $discount->save();

            DB::commit();
    
            return response()->json([
                'error' => false,
                'message' => trans('data_update_successfully')
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error('Error toggling Fees Discount status: ' . $e->getMessage());
    
            return response()->json([
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage() 
            ]);
        }
    }
     

}