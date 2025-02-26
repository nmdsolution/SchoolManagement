<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Mediums;
use App\Printing\MiscPrints;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $incomes = Income::owner()->get();

        $categories = IncomeCategory::owner()->get()->pluck('title', 'id');

        $paymentMethods = ['Cash', 'Mobile money', 'Check'];

        $mediums = Mediums::all()->pluck('name', 'id')->toArray();

        $mediums[0] = "All";

        return view('income.index', compact('categories', 'paymentMethods', 'incomes', 'mediums'));
    }

    public function store(Request $request)
    {
        // TODO Need to replace this with the permission for income create
        if (!Auth::user()->can('expense-create')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $center_id = Auth::user()->center->id;
        $session_year = getSettings('session_year');
        $session_year_id = $session_year['session_year'];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|integer|min:1',
            'quantity' => 'required|numeric|min:1',
            'amount' => 'required|numeric|min:1',
            'purchased_by' => 'required|string|max:255',
            'purchased_from' => 'required|string|max:255',
            'date' => 'required|date_format:d-m-Y',
            'payment_method' => 'required|integer',
            'note' => 'nullable|string|max:500',
            'attach' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'medium' => 'required|integer|min:0',
        ]);


        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        $total_amount = $request->amount * $request->quantity;

        $income = new Income();
        $income->center_id = $center_id;
        $income->session_year_id = $session_year_id;
        $income->name = $request->name;
        $income->quantity = $request->quantity;
        $income->amount = $request->amount;
        $income->category_id = $request->category;
        $income->purchased_by = $request->purchased_by;
        $income->purchased_from = $request->purchased_from;
        $income->date = date('Y-m-d', strtotime($request->date));
        $income->total_amount = $total_amount;
        $income->medium_id = $request->input('medium_id', 0); // 0 means represents it's for all the mediums
        $income->save();

//        TODO Need to add the logic to store the attach image to the database.

        $response = [
            'error' => false,
            'message' => trans('data_store_successfully')
        ];
        return response()->json($response);
    }


    public function show(Request $request)
    {
        if (!Auth::user()->can('expense-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');

        $center_id = Auth::user()->center->id;

        $sql = Income::query()->where('center_id', $center_id)->whereIn('medium_id', [
            '0', getCurrentMedium()->id
        ]);

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);

        $rows = $tempRow = $bulkData  = array();
        $bulkData['total'] = $sql->count();
        $no = 1;
        $data = getSettings('date_formate');
        foreach ($sql->get() as $row) {
            $operate = '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" data-id=' . $row->id . ' data-url=' . url('expense', $row->id) . ' title="Delete"><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $tempRow['quantity'] = $row->quantity;
            $tempRow['category'] = $row->category->title;
            $tempRow['amount'] = $row->amount;
            $tempRow['purchase_by'] = $row->purchased_by;
            $tempRow['purchase_from'] = $row->purchased_from;
            $tempRow['date'] = date($data['date_formate'], strtotime($row->date));
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        if(request()->get('print')){
            $pdf = MiscPrints::getInstance(get_center_id(), 'L');

            $pdf->printIncomeList($rows);

            return response(
                $pdf->Output('', trans('income_list') . '.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }
}
