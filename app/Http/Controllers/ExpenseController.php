<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Expense;
use App\Models\Teacher;
use App\Printing\MiscPrints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('expense-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        return response(view('expenses.index'));
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
            'item_name' => 'required',
            'qty' => 'required',
            'amount' => 'required',
            'purchase_by' => 'required',
            'purchase_from' => 'required',
            'date' => 'required',
        ]);

        if ($validator->fails()) {

            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        $expense = new Expense();
        $expense->center_id = $center_id;
        $expense->session_year_id = $session_year_id;
        $expense->item_name = $request->item_name;
        $expense->qty = $request->qty;
        $expense->amount = $request->amount;
        $expense->purchase_by = $request->purchase_by;
        $expense->purchase_from = $request->purchase_from;
        $expense->date = date('Y-m-d', strtotime($request->date));
        $total_amount = $request->amount * $request->qty;
        $expense->total_amount = $total_amount;
        $expense->save();
        $response = [
            'error' => false,
            'message' => trans('data_store_successfully')
        ];
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if (!Auth::user()->can('expense-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
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

        $center_id = Auth::user()->center->id;
        $sql = Expense::where('center_id', $center_id);

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orwhere('item_name', 'LIKE', "%$search%")
                    ->orwhere('qty', 'LIKE', "%$search%")
                    ->orwhere('amount', 'LIKE', "%$search%")
                    ->orwhere('purchase_by', 'LIKE', "%$search%")
                    ->orwhere('purchase_from', 'LIKE', "%$search%")
                    ->orwhere('date', 'LIKE', "%$search%")
                    ->orwhere('total_amount', 'LIKE', "%$search%");
            });
        }
        if (isset($_GET['filter_daterange']) && !empty($_GET['filter_daterange'])) {
            $daterange = $_GET['filter_daterange'];
            $dates = explode('-', $daterange);
            $startdate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
            $enddate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
            $sql->whereBetween('date', [$startdate, $enddate]);
        }

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        $data = getSettings('date_formate');
        foreach ($res as $row) {
            $operate = '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" data-id=' . $row->id . ' data-url=' . url('expense', $row->id) . ' title="Delete"><i class="fa fa-trash"></i></a>';



            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['item_name'] = $row->item_name;
            $tempRow['qty'] = $row->qty;
            $tempRow['amount'] = $row->amount;
            $tempRow['purchase_by'] = $row->purchase_by;
            $tempRow['purchase_from'] = $row->purchase_from;
            $tempRow['date'] = date($data['date_formate'], strtotime($row->date));
            $tempRow['total_amount'] = $row->total_amount;
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        if(request()->get('print')){
            $pdf = MiscPrints::getInstance(get_center_id(), 'L');
            
            $pdf->printExpenseList($rows);

            return response(
                $pdf->Output('', 'EXPENSE LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        //    dd($rows);
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('expense-edit')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $expense = Expense::find($id);
        $validator = Validator::make($request->all(), [
            'item_name' => 'required',
            'amount' => 'required',
            'date' => 'required',
        ]);

        if ($validator->fails()) {

            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        $expense->item_name = $request->item_name;
        $expense->qty = $request->qty;
        $expense->amount = $request->amount;

        $expense->purchase_by = $request->purchase_by;
        $expense->purchase_from = $request->purchase_from;
        $expense->date = date('Y-m-d', strtotime($request->date));
        $total_amount = $request->amount * $request->qty;
        $expense->total_amount = $total_amount;
        $expense->save();

        $response = [
            'error' => false,
            'message' => trans('data_update_successfully')
        ];
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if (!Auth::user()->can('expense-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {
            $expense = Expense::find($id);
            $expense->delete();
            $response = [
                'error' => false,
                'message' => trans('data_deleted_successfully')
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
    public function salarypaid()
    {
        if (!Auth::user()->can('salary-paid')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $teachers = Teacher::with('user')->Owner()->get()->pluck('user.full_name', 'id');

        return view('salary.index', compact(['teachers']));
    }
    public function getsalary($id)
    {
        $teacher = Teacher::findorfail($id);
        $salary = $teacher->salary;
        return response()->json(['salary' => $salary]);
    }
    public function addsalaryexpense(Request $request)
    {
        if (!Auth::user()->can(['expense-create', 'salary-paid'])) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $center_id = Auth::user()->center->id;
        $session_year = getSettings('session_year');
        $session_year_id = $session_year['session_year'];
        $id = $request->teachers;
        $teachername = Teacher::with('user')->Owner()->find($id)->user->full_name;
        $validator = Validator::make($request->all(), [
            'teachers' => 'required',
            'salary' => 'required',
            'date' => 'required',
        ]);

        if ($validator->fails()) {

            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        $expense = new Expense();
        $expense->center_id = $center_id;
        $expense->session_year_id = $session_year_id;
        $expense->item_name = $teachername . ' ' . "Teacher's Salary";
        $expense->qty = 1;
        $expense->amount = $request->salary;
        $expense->date = date('Y-m-d', strtotime($request->date));
        $total_amount = $request->salary * 1;
        $expense->total_amount = $total_amount;
        $expense->save();
        $response = [
            'error' => false,
            'message' => trans('data_store_successfully')
        ];
        return response()->json($response);
    }
}
