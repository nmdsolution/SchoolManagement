<?php /** @noinspection PhpUndefinedFieldInspection */
/** @noinspection PhpMissingReturnTypeInspection */

/** @noinspection ReturnTypeCanBeDeclaredInspection
 * @noinspection \Symfony\Component\ErrorHandler\Error\UndefinedMethodError
 * */

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\FeesChoiceable;
use App\Models\FeesClass;
use App\Models\FeesDiscount;
use App\Models\FeesPaid;
use App\Models\FeesType;
use App\Models\InstallmentFee;
use App\Models\PaidInstallmentFee;
use App\Models\PaymentTransaction;
use App\Models\SessionYear;
use App\Models\Students;
use App\Printing\FeePrints;
use App\Printing\ReceiptPrinting;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Matrix\Operators\Subtraction;
use Throwable;

class FeesTypeController extends Controller
{

    public function index(): Factory|View|Application
    {
        return view('fees.fees_types');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'nullable',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $fees_type = new FeesType();
            $fees_type->name = $request->name;
            $fees_type->description = $request->description;
            $fees_type->center_id = get_center_id();
            $fees_type->medium_id = getCurrentMedium()->id;

            $fees_type->save();
            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
            );
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
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'desc');
        $search = $request->input('search', '');

        // TODO Need to find a way to apply scout searching here
        $sql = FeesType::owner()->activeMediumOnly()->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        });

        $rows = $tempRow = $bulkData = array();
        $bulkData['total'] = $sql->count();

        $sql->orderBy($sort, $order)->offset($offset)->limit($limit);

        $no = 1;
        foreach ($sql->get() as $row) {
            $operate = '<a href="#" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data set-form-url" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a href=' . route('fees-type.destroy', $row->id) . ' class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $tempRow['description'] = $row->description;
            $tempRow['choiceable'] = $row->choiceable;
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        if (request()->get('print')) {
            $pdf = FeePrints::getInstance(get_center_id());
            $pdf->printFeeTypes($rows);
            return response(
                $pdf->Output('', 'FEE TYPES LIST.pdf'),
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
        $validator = Validator::make($request->all(), [
            'edit_name' => 'required',
            'edit_description' => 'nullable',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $fees_type = FeesType::Owner()->findOrFail($id);
            $fees_type->name = $request->edit_name;
            $fees_type->description = $request->edit_description;
            $fees_type->save();
            $response = array(
                'error' => false,
                'message' => trans('data_update_successfully'),
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
            );
        }
        return response()->json($response);
    }


    public function destroy($id)
    {
        try {
            // check whether fees type id is associate with other tables...
            $fees_choiceables = FeesChoiceable::Owner()->where('fees_type_id', $id)->count();
            $fees_classes = FeesClass::Owner()->where('fees_type_id', $id)->count();

            if ($fees_choiceables || $fees_classes) {
                $response = array(
                    'error' => true,
                    'message' => trans('cannot_delete_because_data_is_associated_with_other_data')
                );
            } else {
                FeesType::Owner()->findOrFail($id)->delete();
                FeesClass::Owner()->where('fees_type_id', $id)->delete();
                $response = array(
                    'error' => false,
                    'message' => trans('data_delete_successfully'),
                );
            }
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
            );
        }
        return response()->json($response);
    }

    public function feesClassListIndex()
    {
        $classes = ClassSchool::owner()->activeMediumOnly()->orderByRaw('CONVERT(name, SIGNED) asc')->with('sections')->get();
        $fees_type = FeesType::Owner()->where('center_id', get_center_id())->orderBy('id', 'ASC')->where('medium_id', getCurrentMedium()->id)->pluck('name', 'id');
        $fees_type_data = FeesType::Owner()->where('center_id', get_center_id())->where('medium_id', getCurrentMedium()->id)->get();
        return response(view('fees.fees_class', compact('classes', 'fees_type', 'fees_type_data')));
    }

    public function feesClassList()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        if (isset($_GET['limit']))
            $limit = $_GET['limit'];

        if (isset($_GET['sort']))
            $sort = $_GET['sort'];


        $sql = ClassSchool::Owner()->with('stream')->owner()->activeMediumOnly()->orderByRaw('CONVERT(name, SIGNED) asc')->with('fees_class');
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")
                ->orwhere('name', 'LIKE', "%$search%");
        }
        if (!empty($_GET['medium_id'])) {
            $sql = $sql->where('medium_id', $_GET['medium_id']);
        }
        $total = $sql->count();

        $sql->skip($offset)->take($limit);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;

        foreach ($res as $row) {

            $row = (object)$row;
            $operate = '<a href=' . route('class.edit', $row->id) . ' class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';

            $tempRow['no'] = $no++;
            $tempRow['class_id'] = $row->id;
            $tempRow['class_name'] = $row->full_name;
            if (count($row->fees_class)) {
                $total_amount = 0;
                $base_amount = 0;
                $fees_type_table = array();
                foreach ($row->fees_class as $fees_details) {
                    $fees_type_table[] = array(
                        'id' => $fees_details->id,
                        'fees_name' => $fees_details->fees_type->name,
                        'amount' => $fees_details->amount,
                        'choiceable' => $fees_details->choiceable,
                        'fees_type_id' => $fees_details->fees_type->id,
                    );
                    if ($fees_details->choiceable == 0) {
                        $base_amount += $fees_details->amount;
                    }
                    $total_amount += $fees_details->amount;
                }
                $tempRow['fees_type'] = $fees_type_table;
                $tempRow['base_amount'] = $base_amount;
                $tempRow['total_amount'] = $total_amount;
            } else {
                $tempRow['fees_type'] = [];
                $tempRow['base_amount'] = "-";
                $tempRow['total_amount'] = "-";
            }
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        if (request()->get('print')) {
            $pdf = FeePrints::getInstance(get_center_id());
            $pdf->printClassFees($rows);

            return response(
                $pdf->Output('', 'CLASS FEES LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function updateFeesClass(Request $request)
    {
        $validation_rules = array(
            'class_id' => 'required|numeric',
            'edit_fees_type.*.fees_type_id' => 'required',
            'edit_fees_type.*.amount' => 'required:edit_fees_type',
            'edit_fees_type.*.choiceable' => 'required|in:0,1'
        );
        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            // //Update Fees Type For Class first
            if ($request->edit_fees_type) {
                foreach ($request->edit_fees_type as $row) {
                    $edit_fees_type = FeesClass::Owner()->findOrFail($row['fees_class_id']);
                    $edit_fees_type->fees_type_id = $row['fees_type_id'];
                    $edit_fees_type->amount = $row['amount'];
                    $edit_fees_type->choiceable = $row['choiceable'];
                    $edit_fees_type->save();
                }
            }

            //Add New Fees Type For Class
            if ($request->fees_type) {
                $fees_type = array();
                foreach ($request->fees_type as $row) {
                    $fees_type[] = array(
                        'class_id' => $request->class_id,
                        'fees_type_id' => $row['fees_type_id'],
                        'amount' => $row['amount'],
                        'choiceable' => $row['choiceable'],
                        'center_id' => get_center_id()
                    );
                }
                FeesClass::Owner()->insert($fees_type);
            }
            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }

    public function removeFeesClass($id)
    {
        try {
            $fees_class = FeesClass::Owner()->where('id', $id)->first();

            //check whether the fees class is associated with other table.
            $fees_choiceable = FeesChoiceable::Owner()->where(['class_id' => $fees_class->class_id, 'fees_type_id' => $fees_class->fees_type_id])->count();
            if ($fees_choiceable) {
                $response = array(
                    'error' => true,
                    'message' => trans('cannot_delete_because_data_is_associated_with_other_data')
                );
            } else {
                $fees_type_class = FeesClass::Owner()->findOrFail($id);
                $fees_type_class->delete();
                $response = array(
                    'error' => false,
                    'message' => trans('data_delete_successfully')
                );
            }
        } catch (Throwable) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function feesPaidListIndex()
    {
        $classes = ClassSchool::activeMediumOnly()->Owner()->orderByRaw('CONVERT(name, SIGNED) asc')->with('stream')->get();
        $session_year_all = SessionYear::Owner()->select('id', 'name', 'default')->Owner()->get();
        return response(view('fees.fees_paid', compact('classes', 'session_year_all')));
    }

    public function feeStatusSummary()
    {
        if (!Auth::user()->can('fees-paid')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }


        $sql = ClassSection::Owner()
            ->with(['class', 'section', 'class.stream'])
            ->with('class', function ($query) {
                $query->with(['stream']);
                $query->withSum('fees_class', 'amount');
            })
            ->whereHas('class', function ($query) {
                $query->activeMediumOnly();
            })
            ->withCount('student')
            ->selectSub(function ($query) {
                $query->from('fees_paids')
                    ->join('students', 'fees_paids.student_id', '=', 'students.id')
                    ->whereColumn('students.class_section_id', 'class_sections.id')
                    ->selectRaw('sum(total_amount)');
            }, 'total_fees_paid');

        $res = $sql->get();
        $class_names = array();
        $paid_fee = array();
        $unpaid_fee = array();

        foreach ($res as $row) {

            $row = (object)$row;

            $total_amount = $row->class->fees_class_sum_amount * $row->student_count;

            $paid = $row->total_fees_paid;

            $class_names[] = $row->full_name;
            $paid_fee[] = $paid ?? 0;
            $unpaid_fee[] = $total_amount - $paid;
        }

        $data = array(
            'class_names' => $class_names,
            'paid_fee' => $paid_fee,
            'unpaid_fee' => $unpaid_fee
        );

        return response()->json($data);
    }

    private function generateOperateButtons($row, $payment_transaction, $request)
    {
        $operate = "";
        if (!empty($row->fees_paid) && isset($row->fees_paid[0]->id)) {
            // checks that fees paid's session year matches the current session year then allow to modify the fees payments or else show only clear and pdf option
            if (isset($row->fees_paid[0]->session_year_id) && $row->fees_paid[0]->session_year_id == $request->session_year_id) {

                $operate = '<div class="dropdown"><button class="btn btn-xs btn-gradient-success btn-rounded btn-icon dropdown-toggle btn-dark" type="button" data-bs-toggle="dropdown"><i class="fa fa-dollar"></i></button><div class="dropdown-menu">';
                $operate .= '<a href="#" class="compulsory-data dropdown-item" data-id=' . $row->id . ' title="' . trans('compulsory') . ' ' . trans('fees') . '" data-bs-toggle="modal" data-bs-target="#compulsoryModal"><i class="fa fa-dollar text-success mr-2"></i>' . trans('compulsory') . ' ' . trans('fees') . '</a><div class="dropdown-divider"></div>';
                $operate .= '<a href="#" class="optional-data dropdown-item" data-id=' . $row->id . ' title="' . trans('optional') . ' ' . trans('fees') . '" data-bs-toggle="modal" data-bs-target="#optionalModal"><i class="fa fa-dollar text-success mr-2"></i>' . trans('optional') . ' ' . trans('fees') . '</a>';
                $operate .= '</div></div>&nbsp;&nbsp;';

                $operate .= '<a href=' . route('fees.paid.clear.data', $row->fees_paid[0]->id) . ' class="btn btn-xs btn-danger btn-rounded btn-icon delete-form" title="' . trans('clear') . '" data-id=' . $row->fees_paid[0]->id . '><i class="feather-delete"></i></a>&nbsp;&nbsp;';
                $operate .= '<a href=' . route('fees.paid.receipt.pdf', $row->fees_paid[0]->id) . ' class="btn btn-xs btn-primary btn-rounded btn-icon generate-paid-fees-pdf" target="_blank" data-id=' . $row->fees_paid[0]->id . ' title="' . trans('generate_pdf') . ' ' . trans('fees') . '"><i class="feather-file"></i></a>&nbsp;&nbsp;';
            } else {
                $operate .= '<a href=' . route('fees.paid.clear.data', $row->fees_paid[0]->id) . ' class="btn btn-xs btn-danger btn-rounded btn-icon btn-icon delete-form" title="' . trans('clear') . '" data-id=' . $row->fees_paid->id . '><i class="feather-delete"></i></a>&nbsp;&nbsp;';
                $operate .= '<a href=' . route('fees.paid.receipt.pdf', $row->fees_paid[0]->id) . ' class="btn btn-xs btn-primary btn-rounded btn-icon btn-icon generate-paid-fees-pdf" target="_blank" data-id=' . $row->fees_paid->id . ' title="' . trans('generate_pdf') . ' ' . trans('fees') . '"><i class="feather-file"></i></a>&nbsp;&nbsp;';
            }
        } else {
            $operate = '<div class="dropdown"><button class="btn btn-xs btn-gradient-success btn-rounded btn-icon dropdown-toggle btn-dark" type="button" data-bs-toggle="dropdown"><i class="fa fa-dollar"></i></button><div class="dropdown-menu">';
            $operate .= '<a href="#" class="compulsory-data dropdown-item" data-id=' . $row->id . ' title="' . trans('compulsory') . ' ' . trans('fees') . '" data-bs-toggle="modal" data-bs-target="#compulsoryModal"><i class="fa fa-dollar text-success mr-2"></i>' . trans('compulsory') . ' ' . trans('fees') . '</a><div class="dropdown-divider"></div>';
            $operate .= '<a href="#" class="optional-data dropdown-item" data-id=' . $row->id . ' title="' . trans('optional') . ' ' . trans('fees') . '" data-bs-toggle="modal" data-bs-target="#optionalModal"><i class="fa fa-dollar text-success mr-2"></i>' . trans('optional') . ' ' . trans('fees') . '</a>';
            $operate .= '</div></div>&nbsp;&nbsp;';
        }

        return $operate;
    }


    private function formatRow($row, $session_year, $current_date, $due_date, $request, $key)
    {
        $base_amount = FeesClass::query()->where(['class_id' => $row->class_section->class_id, 'choiceable' => 0])->sum('amount');
        $charges = (strtotime($current_date) > strtotime($due_date)) ? ($session_year->fee_due_charges * $base_amount / 100) : 0;
        $base_amount_with_due_charges = $base_amount + $charges;

        $payment_transaction = $row->fees_paid ? PaymentTransaction::query()->where([
            'student_id' => $row->id,
            'class_id' => $row->class_section->class_id,
            'session_year_id' => $session_year->id
        ])->latest()->first() : null;

        $compulsory_fees = FeesClass::owner()->where(['class_id' => $row->class_section->class_id, 'choiceable' => 0])->with('fees_type')->get();
        $choiceable_fees = FeesClass::owner()->where(['class_id' => $row->class_section->class_id, 'choiceable' => 1])->with('fees_type')->get();
        $installment_data = InstallmentFee::owner()->where('session_year_id', $request->session_year_id)->get();

        $paid_installment_data = PaidInstallmentFee::owner()->where(['student_id' => $row->id, 'session_year_id' => $request->session_year_id])->first();

        $tempRow = [
            'id' => null,
            'student_id' => $row->id,
            'no' => $key + 1,
            'father_id' => $row->father_id,
            'mother_id' => $row->mother_id,
            'student_name' => $row->user->first_name . ' ' . $row->user->last_name,

            'is_installment_paid' => $paid_installment_data ? 1 : 0,
            'class_id' => $row->class_section->class_id,
            'stream_name' => $row->class_section->class->streams->name ?? '-',
            'compulsory_fees' => sizeof($compulsory_fees) ? $compulsory_fees : null,
            'class_name' => $row->class_section->class->name . ' ' . $row->class_section->class->medium->name,

            'base_amount' => $base_amount,
            'base_amount_with_due_charges' => $base_amount_with_due_charges,
            'due_charges' => ['date' => date('d-m-Y', strtotime($due_date)), 'charges' => $charges],

            'fees_status' => $row->fees_paid[0]->is_fully_paid ?? null,
            'total_fees' => $row->fees_paid[0]->total_amount ?? null,
            'current_date' => $current_date,
            'date' => $row->fees_paid[0]->date ?? null,
            'session_year_name' => $row->fees_paid[0]->session_year->name ?? null,

            'mode' => $payment_transaction->mode ?? null,
            'type_of_fee' => $payment_transaction->type_of_fee ?? null,
            'cheque_no' => $payment_transaction->cheque_no ?? null,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
            'operate' => $this->generateOperateButtons($row, $payment_transaction, $request),
        ];

        if (!empty($installment_data)) {
            $tempRow['installment_data'] = array();
            foreach ($installment_data as $data) {

                $paid_installment = PaidInstallmentFee::owner()->where(['student_id' => $row->id, 'installment_fee_id' => $data->id])->first();

                $tempInstallmentData = array(
                    'id' => $data->id,
                    'name' => $data->name,
                    'due_date' => $data->due_date,
                    'due_charges' => $data->due_charges,
                    'due_charges_applicable' => 0,
                    'paid' => $paid_installment ? 1 : 0,
                    'paid_id' => $paid_installment ? $paid_installment->id : '',
                    'paid_on' => $paid_installment ? $paid_installment->date : '',
                );

                if (strtotime($current_date) >= strtotime($data->due_date)) {
                    $tempInstallmentData = array(
                        ...$tempInstallmentData,
                        'due_charges_applicable' => 1,
                        'paid_on' => $paid_installment ? date('d-m-Y', strtotime($paid_installment->date)) : '',
                    );
                }

                $tempRow['installment_data'][] = $tempInstallmentData;
            }
        }
        if (!empty($choiceable_fees)) {
            $tempRow['choiceable_fees'] = array();
            $paid_choiceable_fees_query = FeesChoiceable::owner()->where(['class_id' => $row->class_section->class_id, 'student_id' => $row->id, 'session_year_id' => $request->session_year_id]);
            foreach ($choiceable_fees as $data) {
                $paid_choiceable_data = (clone $paid_choiceable_fees_query)->where('fees_type_id', $data->fees_type_id);

                $tempChoiceableFees = array(
                    'id' => $data->id,
                    'name' => $data->fees_type->name,
                    'class_id' => $data->class_id,
                    'fees_type_id' => $data->fees_type_id,
                    'choiceable' => $data->choiceable,
                    'amount' => $data->amount,
                    'is_paid' => 0,
                );
                if ($paid_choiceable_data->count()) {
                    $tempChoiceableFees = array(
                        ...$tempChoiceableFees,
                        'is_paid' => 1,
                        'paid_id' => $paid_choiceable_data->first()->id,
                        'date' => $paid_choiceable_data->first()->date,
                    );
                }
                $tempRow['choiceable_fees'][] = $tempChoiceableFees;
            }
        }

        return $tempRow;
    }

    private function printFeesList($rows, $class_id, $session_year)
    {
        $pdf = FeePrints::getInstance(get_center_id(), 'L');
        $class = ClassSchool::find($class_id);

        $pdf->printPaidFeesList($rows, $class, $session_year);

        return response(
            $pdf->Output('', 'PAID FEE LIST.pdf'),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function feesPaidList(Request $request)
    {
        if (!Auth::user()->can('fees-paid')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $offset = $request->input('offset', 0);
        $session_year_id = getSettings('session_year')['session_year'];

        $sql = Students::owner()->with(['user:id,first_name,last_name', 'fees_paid', 'class_section'])->whereHas('studentSessions', function ($query) use ($session_year_id) {
            $query->where('session_year_id', $session_year_id);
        });

        $sql->with('fees_paid', function ($q) {
            $q->with('class', 'session_year', 'payment_transaction')->where('session_year_id', $_GET['session_year_id']);
        });

        // limits download to all fees paid for each student 
        // for both general and a specific class
        $limit = $request->input('limit', count($sql->get()));

        // In case there is no class selected
        if (!empty($request->input('class_id'))) {
            $class_section_id = ClassSection::owner()
                ->where('class_id', $request->input('class_id'))
                ->pluck('id');
    
            $sql->whereIn('class_section_id', $class_section_id);
        }

        if (!empty($_GET['mode'])) {
            $sql->whereHas('fees_paid', function ($q) {
                $q->where('mode', $_GET['mode']);
            });
        }

        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->where('students.id', 'LIKE', "%$search%")
                    ->orWhere('user_id', 'LIKE', "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%$search%")
                            ->orWhere('last_name', 'LIKE', "%$search%");
                    });
            });
        }

        // TODO add the fees_paid and fees_left information to the fees_paid list

        $total = $sql->count();
        $sql->join('users', 'users.id', '=', 'students.user_id')
            ->orderBy('users.first_name', 'ASC')
            ->orderBy('users.last_name', 'ASC')
            ->select('students.*') // ensuring only students are selected
            ->skip($offset)
            ->take($limit);

        $res = $sql->get(); 

        $bulkData = $rows = $tempRow = array();
        $bulkData['total'] = $total;
        $no = 1;
        $session_year = SessionYear::owner()->where('id', $request->session_year_id)->first();
        $due_date = $session_year->fee_due_date;


        // going through all the students and their information.
        foreach ($res as $key => $row) {

            // Get the fees data
            $compulsory_fees = FeesClass::owner()->where(['class_id' => $row->class_section->class_id, 'choiceable' => 0])->with('fees_type')->get();
            $choiceable_fees = FeesClass::owner()->where(['class_id' => $row->class_section->class_id, 'choiceable' => 1])->with('fees_type')->get();
            $installment_data = InstallmentFee::owner()->where('session_year_id', $request->session_year_id)->get();

            // Base Amount
            $base_amount = FeesClass::where(['class_id' => $row->class_section->class_id, 'choiceable' => 0])->selectRaw('SUM(amount) as base_amount')->first();
            $base_amount = $base_amount['base_amount'];
            $base_amount_with_due_charges = $base_amount;
            $current_date = Carbon::now()->format('d-m-Y');

            // if due charges is applicable
            if (strtotime($current_date) > strtotime($due_date)) {
                $due_charges = $session_year->fee_due_charges;
                $charges = (($due_charges) * ($base_amount) / 100);
                $base_amount_with_due_charges = $base_amount + $charges;
            }

            $payment_transaction = null;

            $totalDiscount = 0;

            if ($row->fees_paid) {
                // Looking for hte payment information for that student in that class
                $payment_transaction = PaymentTransaction::where(['student_id' => $row->id, 'class_id' => $row->class_section->class_id, 'session_year_id' => $request->session_year_id])->latest()->first();
            }

            //Get Paid Fees

            $operate = "";
            // check that fees paid is not empty
            if (!empty($row->fees_paid) && isset($row->fees_paid[0]->id)) {
                // checks that fees paid's session year matches the current session year then allow to modify the fees payments or else show only clear and pdf option
                if (isset($row->fees_paid[0]->session_year_id) && $row->fees_paid[0]->session_year_id == $request->session_year_id) {

                    $operate = '<div class="dropdown"><button class="btn btn-xs btn-gradient-success btn-rounded btn-icon dropdown-toggle btn-dark" type="button" data-bs-toggle="dropdown"><i class="fa fa-dollar"></i></button><div class="dropdown-menu">';
                    $operate .= '<a href="#" class="compulsory-data dropdown-item" data-id=' . $row->id . ' title="' . trans('compulsory') . ' ' . trans('fees') . '" data-bs-toggle="modal" data-bs-target="#compulsoryModal"><i class="fa fa-dollar text-success mr-2"></i>' . trans('compulsory') . ' ' . trans('fees') . '</a><div class="dropdown-divider"></div>';
                    $operate .= '<a href="#" class="optional-data dropdown-item" data-id=' . $row->id . ' title="' . trans('optional') . ' ' . trans('fees') . '" data-bs-toggle="modal" data-bs-target="#optionalModal"><i class="fa fa-dollar text-success mr-2"></i>' . trans('optional') . ' ' . trans('fees') . '</a>';
                    $operate .= '</div></div>&nbsp;&nbsp;';

                    $operate .= '<a href=' . route('fees.paid.clear.data', $row->fees_paid[0]->id) . ' class="btn btn-xs btn-danger btn-rounded btn-icon delete-form" title="' . trans('clear') . '" data-id=' . $row->fees_paid[0]->id . '><i class="feather-delete"></i></a>&nbsp;&nbsp;';
                    $operate .= '<a href=' . route('fees.paid.receipt.pdf', $row->fees_paid[0]->id) . ' class="btn btn-xs btn-primary btn-rounded btn-icon generate-paid-fees-pdf" target="_blank" data-id=' . $row->fees_paid[0]->id . ' title="' . trans('generate_pdf') . ' ' . trans('fees') . '"><i class="feather-file"></i></a>&nbsp;&nbsp;';
                } else {
                    $operate .= '<a href=' . route('fees.paid.clear.data', $row->fees_paid[0]->id) . ' class="btn btn-xs btn-danger btn-rounded btn-icon btn-icon delete-form" title="' . trans('clear') . '" data-id=' . $row->fees_paid->id . '><i class="feather-delete"></i></a>&nbsp;&nbsp;';
                    $operate .= '<a href=' . route('fees.paid.receipt.pdf', $row->fees_paid[0]->id) . ' class="btn btn-xs btn-primary btn-rounded btn-icon btn-icon generate-paid-fees-pdf" target="_blank" data-id=' . $row->fees_paid->id . ' title="' . trans('generate_pdf') . ' ' . trans('fees') . '"><i class="feather-file"></i></a>&nbsp;&nbsp;';
                }
            } else {
                // just produce the normal buttons if there is no infomration of whether anything was paid.
                $operate = '<div class="dropdown"><button class="btn btn-xs btn-gradient-success btn-rounded btn-icon dropdown-toggle btn-dark" type="button" data-bs-toggle="dropdown"><i class="fa fa-dollar"></i></button><div class="dropdown-menu">';
                $operate .= '<a href="#" class="compulsory-data dropdown-item" data-id=' . $row->id . ' title="' . trans('compulsory') . ' ' . trans('fees') . '" data-bs-toggle="modal" data-bs-target="#compulsoryModal"><i class="fa fa-dollar text-success mr-2"></i>' . trans('compulsory') . ' ' . trans('fees') . '</a><div class="dropdown-divider"></div>';
                $operate .= '<a href="#" class="optional-data dropdown-item" data-id=' . $row->id . ' title="' . trans('optional') . ' ' . trans('fees') . '" data-bs-toggle="modal" data-bs-target="#optionalModal"><i class="fa fa-dollar text-success mr-2"></i>' . trans('optional') . ' ' . trans('fees') . '</a>';
                $operate .= '</div></div>&nbsp;&nbsp;';
            }

            $tempRow['id'] = null;
            $tempRow['student_id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['father_id'] = $row->father_id;
            $tempRow['mother_id'] = $row->mother_id;
            $tempRow['student_name'] = $row->user->first_name . ' ' . $row->user->last_name;
            $tempRow['class_id'] = $row->class_section->class_id;
            $tempRow['class_name'] = $row->class_section->class->name . ' ' . $row->class_section->class->medium->name;
            $tempRow['stream_name'] = $row->class_section->class->streams->name ?? '-';
            $tempRow['compulsory_fees'] = sizeof($compulsory_fees) ? $compulsory_fees : null;

            $paid_installment_data = PaidInstallmentFee::owner()->where(['student_id' => $row->id, 'session_year_id' => $request->session_year_id])->first();
            $tempRow['is_installment_paid'] = $paid_installment_data ? 1 : 0;

            if (!empty($installment_data)) {
                $tempRow['installment_data'] = array();
                foreach ($installment_data as $data) {
                    // Paid Installment Data
                    $paid_installment = PaidInstallmentFee::owner()->where(['student_id' => $row->id, 'installment_fee_id' => $data->id])->first();
                    $tempInstallmentData = array(
                        'id' => $data->id,
                        'name' => $data->name,
                        'due_date' => $data->due_date,
                        'due_charges' => $data->due_charges,
                        'due_charges_applicable' => 0,
                        'paid' => $paid_installment ? 1 : 0,
                        'paid_id' => $paid_installment ? $paid_installment->id : '',
                        'paid_on' => $paid_installment ? $paid_installment->date : '',
                    );
                    if (strtotime($current_date) >= strtotime($data->due_date)) {
                        $tempInstallmentData = array(
                            ...$tempInstallmentData,
                            'due_charges_applicable' => 1,
                            'paid_on' => $paid_installment ? date('d-m-Y', strtotime($paid_installment->date)) : '',
                        );
                    }
                    $tempRow['installment_data'][] = $tempInstallmentData;
                }
            }
            if (!empty($choiceable_fees)) {
                $tempRow['choiceable_fees'] = array();
                $paid_choiceable_fees_query = FeesChoiceable::owner()->where(['class_id' => $row->class_section->class_id, 'student_id' => $row->id, 'session_year_id' => $request->session_year_id]);
                foreach ($choiceable_fees as $data) {
                    //Clone the Query To Avoid Extra Addition of Where Fees Type ID
                    $paid_choiceable_data = (clone $paid_choiceable_fees_query)->where('fees_type_id', $data->fees_type_id);

                    $tempChoiceableFees = array(
                        'id' => $data->id,
                        'name' => $data->fees_type->name,
                        'class_id' => $data->class_id,
                        'fees_type_id' => $data->fees_type_id,
                        'choiceable' => $data->choiceable,
                        'amount' => $data->amount,
                        'is_paid' => 0,
                    );
                    if ($paid_choiceable_data->count()) {
                        $tempChoiceableFees = array(
                            ...$tempChoiceableFees,
                            'is_paid' => 1,
                            'paid_id' => $paid_choiceable_data->first()->id,
                            'date' => $paid_choiceable_data->first()->date,
                        );
                    }
                    $tempRow['choiceable_fees'][] = $tempChoiceableFees;
                }
            }
            $tempRow['base_amount'] = $base_amount;
            $tempRow['base_amount_with_due_charges'] = $base_amount_with_due_charges;
            $tempRow['due_charges'] = array(
                'date' => date('d-m-Y', strtotime($due_date)),
                'charges' => $charges ?? null,
            );

            $tempRow['fees_status'] = $row->fees_paid[0]->is_fully_paid ?? null;
            $tempRow['total_fees'] = $row->fees_paid[0]->total_amount ?? null;
            $tempRow['current_date'] = $current_date;
            $tempRow['date'] = $row->fees_paid[0]->date ?? null;
            $tempRow['session_year_name'] = $row->fees_paid[0]->session_year->name ?? null;
            $tempRow['mode'] = $payment_transaction->mode ?? null;
            $tempRow['type_of_fee'] = $payment_transaction->type_of_fee ?? null;
            $tempRow['fees_paid'] = $payment_transaction->amount_paid ?? null;
            $tempRow['fees_left'] = $payment_transaction->fees_left ?? null;
            $tempRow['cheque_no'] = $payment_transaction->cheque_no ?? null;
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;
            $tempRow['status'] =  implode(', ', json_decode($row->user->student->status) ?? ['Not Applicable']);

            if (!empty($row->user->student->status)) {
                $studentStatuses = json_decode($row->user->student->status, true) ?? [];
                $activeDiscounts = FeesDiscount::where('center_id', get_center_id())
                                                ->where('active', 1)
                                                ->get();
            
                foreach ($activeDiscounts as $discount) {
                    $applicableStatuses = json_decode($discount->applicable_status, true) ?? [];
            
                    if (!empty(array_intersect($studentStatuses, $applicableStatuses))) {
                        $totalDiscount += $discount->amount;
                    }
                }
            
                if (!empty($row->fees_paid) && isset($row->fees_paid[0])) {
                    $totalAmount = $row->fees_paid[0]->total_amount ?? 0;
                    $tempRow['total_fees'] = round($totalAmount - (($totalAmount * $totalDiscount) / 100), 2);
                } else {
                    $tempRow['total_fees'] = null;
                }
            }
            
            $tempRow['fee_discount'] = round($totalDiscount, 2);
            
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        if (request()->get('print')) {
            $class = null;
            $session_year = null;
            // logger($rows);

            $pdf = FeePrints::getInstance(get_center_id(), 'L');

            if (!empty($_GET['session_year_id'])) {
                $session_year = SessionYear::find($_GET['session_year_id']);
            }
            if (!empty($_GET['class_id'])) {
                $class = ClassSchool::find($_GET['class_id']);
            }
            $pdf->printPaidFeesList($rows, $class, $session_year);

            return response(
                $pdf->Output('', 'PAID FEE LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    //  Was writing this function below to make a more efficient version of the one above

//    public function feesPaidList(Request $request)
//    {
//        if (!Auth::user()->can('fees-paid')) {
//            return redirect(route('home'))->withErrors(['message' => trans('no_permission_message')]);
//        }
//
//        $offset = $request->get('offset', 0);
//        $limit = $request->get('limit', 10);
//        $sort = $request->get('sort', 'id');
//        $order = $request->get('order', 'DESC');
//
//        $class_id = $_GET['class_id'];
//
//        $class_section_id = ClassSection::owner()->where('class_id', $class_id)->pluck('id');
//
//        $session_year_id = $request->session_year_id;
//
//        $studentsQuery = Students::owner()
//            ->with(['user:id,first_name,last_name', 'fees_paid', 'class_section'])
//            ->with('fees_paid', function ($q) {
//                $q->with('class', 'session_year', 'payment_transaction')->where('session_year_id', $_GET['session_year_id']);
//            })
//            ->whereHas('studentSessions', function ($query) use ($session_year_id) {
//                $query->where('session_year_id', $session_year_id);
//                $query->where('active', true);
//            })->whereIn('class_section_id', $class_section_id)
//            ->when($request->filled('mode'), function ($query) use ($request) {
//                $query->whereHas('fees_paid', fn($q) => $q->where('mode', $request->mode));
//            })->when($request->filled('search'), function ($query) use ($request) {
//                $search = $request->search;
//                $query->where(function ($q) use ($search) {
//                    $q->where('id', 'LIKE', "%$search%")
//                        ->orWhereHas('user', fn($q) => $q->where('first_name', 'LIKE', "%$search%")
//                            ->orWhere('last_name', 'LIKE', "%$search%"));
//                });
//            })
//            ->orderBy($sort, $order)
//            ->skip($offset)
//            ->take($limit);
//
//        $res = $studentsQuery->get();
//
//        $session_year = SessionYear::owner()->where('id', $request->session_year_id)->first();
//        $due_date = $session_year->fee_due_date;
//        $current_date = Carbon::now()->format('d-m-Y');
//
//        $rows = $res->map(function ($row, $key) use ($request, $session_year, $current_date, $due_date) {
//            return $this->formatRow($row, $session_year, $current_date, $due_date, $request, $key);
//        });
//
//        if ($request->get('print')) {
//            return $this->printFeesList($rows, $class_id, $session_year);
//        }
//
//        return response()->json([
//            'rows' => $rows,
//            'total' => $studentsQuery->count()
//        ]);
//    }

    public function feesPaidStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'mode' => 'required|in:0,1,2',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $date = date('Y-m-d', strtotime($request->date));
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            // fetching the total amount for all of the fees to be paid by the student in that class.
            $class_fees = FeesClass::Owner()->where('class_id', $request->class_id)->selectRaw('SUM(amount) as total_amount')->groupby('class_id')->first();

            $due_date = getSettings('fees_due_date');
            $due_date = $due_date['fees_due_date'];
            $current_date = Carbon::now()->format('m/d/Y');
            if ($current_date > $due_date) {
                $due_charges = getSettings('fees_due_charges');
                $due_charges = $due_charges['fees_due_charges'];
                $class_fees = $class_fees['total_amount'] + $due_charges;
            } else {
                $class_fees = $class_fees['total_amount'];
            }

            //add data to fees paid
            // for every payment that the user is making, we are going to
            // create a new fees paid record
            $fees_paid = new FeesPaid();
            $fees_paid->student_id = $request->student_id;
            $fees_paid->class_id = $request->class_id;
            if ($request->mode) {
                $fees_paid->mode = $request->mode;
                $fees_paid->cheque_no = $request->cheque_no;
            } else {
                $fees_paid->mode = $request->mode;
            }
            $fees_paid->total_amount = $request->total_amount ?? $class_fees;
            $fees_paid->date = $date;
            $fees_paid->session_year_id = $session_year_id;
            $fees_paid->save();

            // add compulsory fees in fees choiced table
            $compulsory_fees = FeesClass::Owner()->where('class_id', $request->class_id)->whereHas('fees_type', function ($q) {
                $q->where('choiceable', 0);
            })->get();

            // Fetch all of the compulsory fees and create them for the this student.
            foreach ($compulsory_fees as $fees) {
                $fees_choiceable = new FeesChoiceable();
                $fees_choiceable->student_id = $request->student_id;
                $fees_choiceable->class_id = $request->class_id;
                $fees_choiceable->fees_type_id = $fees->fees_type_id;
                $fees_choiceable->is_due_charges = 0;
                $fees_choiceable->total_amount = $fees->amount;
                $fees_choiceable->session_year_id = $session_year_id;
                $fees_choiceable->save();
            }

            // add choiceable fees in fees choiceable table
            // this happens if the user parsed
            if (isset($request->choiceable_fees)) {
                foreach ($request->choiceable_fees as $fees_type_id) {
                    $amount = FeesClass::Owner()->where(['fees_type_id' => $fees_type_id, 'class_id' => $request->class_id])->pluck('amount')->first();
                    $fees_choiceable = new FeesChoiceable();
                    $fees_choiceable->student_id = $request->student_id;
                    $fees_choiceable->class_id = $request->class_id;
                    $fees_choiceable->fees_type_id = $fees_type_id;
                    $fees_choiceable->is_due_charges = 0;
                    $fees_choiceable->total_amount = $amount;
                    $fees_choiceable->session_year_id = $session_year_id;
                    $fees_choiceable->save();
                }
            }

            // if due charges applicable then add entry in fees choiced table
            if ($request->due_charges != null) {
                $fees_choiceable = new FeesChoiceable();
                $fees_choiceable->student_id = $request->student_id;
                $fees_choiceable->class_id = $request->class_id;
                $fees_choiceable->fees_type_id = null;
                $fees_choiceable->is_due_charges = 1;
                $fees_choiceable->total_amount = $request->due_charges;
                $fees_choiceable->session_year_id = $session_year_id;
                $fees_choiceable->save();
            }

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully')
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function feesPaidUpdate(Request $request)
    {
        $fees_paid_db = FeesPaid::Owner()->find($request->edit_id);
        //get session_year_id of particular fees paid .
        $session_year_id = $fees_paid_db->session_year_id;

        $date = date('Y-m-d', strtotime($request->edit_date));
        $fees_paid_db->date = $date;
        $fees_paid_db->total_amount = $request->edit_total_amount;
        if ($request->edit_mode) {
            $fees_paid_db->mode = $request->edit_mode;
            $fees_paid_db->cheque_no = $request->edit_cheque_no;
        } else {
            $fees_paid_db->mode = $request->edit_mode;
        }
        $fees_paid_db->save();
        if (isset($request->add_new_choiceable_fees)) {
            foreach ($request->add_new_choiceable_fees as $fees_type_id) {
                $amount = FeesClass::Owner()->where(['fees_type_id' => $fees_type_id, 'class_id' => $request->edit_class_id])->pluck('amount')->first();
                $fees_choiceable = new FeesChoiceable();
                $fees_choiceable->student_id = $request->edit_student_id;
                $fees_choiceable->class_id = $request->edit_class_id;
                $fees_choiceable->fees_type_id = $fees_type_id;
                $fees_choiceable->is_due_charge = 0;
                $fees_choiceable->total_amount = $amount;
                $fees_choiceable->session_year_id = $session_year_id;
                $fees_choiceable->save();
            }
        }

        $response = array(
            'error' => false,
            'message' => trans('data_update_successfully')
        );
        return response()->json($response);
    }

    public function feesPaidRemoveChoiceableFees($id)
    {
        try {
            $fees_choiceable = FeesChoiceable::Owner()->find($id);
            $student_id = $fees_choiceable->student_id;
            $class_id = $fees_choiceable->class_id;
            $session_year_id = $fees_choiceable->session_year_id;

            //get the amount of particular fees choiced
            $fees_choiceable_amount = $fees_choiceable->total_amount;
            $fees_choiceable->delete();

            $fees_paid_id = FeesPaid::Owner()->where(['student_id' => $student_id, 'class_id' => $class_id, 'session_year_id' => $session_year_id])->pluck('id')->first();
            $fees_paid_amount = FeesPaid::Owner()->where(['student_id' => $student_id, 'class_id' => $class_id, 'session_year_id' => $session_year_id])->pluck('total_amount')->first();
            $updated_fees = $fees_paid_amount - $fees_choiceable_amount;
            $fees_paid_update = FeesPaid::Owner()->find($fees_paid_id);
            $fees_paid_update->total_amount = $updated_fees;
            $fees_paid_update->save();
            $response = array(
                'error' => false,
                'message' => trans('data_delete_successfully')
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function clearFeesPaidData($id)
    {
        try {
            $fees_paid_data = FeesPaid::Owner()->find($id);

            // get the ids from fees paid to remove the fees choiced data
            $student_id = $fees_paid_data->student_id;
            $class_id = $fees_paid_data->class_id;
            $session_year_id = $fees_paid_data->session_year_id;

            $fees_paid_data->delete();

            FeesChoiceable::Owner()->where(['student_id' => $student_id, 'class_id' => $class_id, 'session_year_id' => $session_year_id])->delete();
            PaidInstallmentFee::where(['student_id' => $student_id, 'class_id' => $class_id, 'session_year_id' => $session_year_id])->delete();
            PaymentTransaction::where(['student_id' => $student_id, 'class_id' => $class_id, 'session_year_id' => $session_year_id])->delete();
            $response = array(
                'error' => false,
                'message' => trans('data_delete_successfully')
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function feesTransactionsLogsIndex(Request $request)
    {
        $session_year_all = SessionYear::Owner()->select('id', 'name', 'default')->Owner()->get();
        $classes = ClassSchool::activeMediumOnly()->orderByRaw('CONVERT(name, SIGNED) asc')->with('stream')->Owner()->get();
        return response(view('fees.fees_transaction_logs', compact('classes', 'session_year_all')));
    }

public function feesTransactionsLogsList(): JsonResponse|Response
{
    try {
        $sql = PaymentTransaction::with(['student', 'session_year'])
            ->whereHas('student', function ($q) {
                $q->whereHas('class_section', function ($query) {
                    $query->Owner()
                        ->whereHas('class', function($classQuery) {
                            $classQuery->activeMediumOnly();
                        });
                });
            });

        $params = request()->all();

        $isFilterEmpty = empty(array_filter([
            $params['search'] ?? null,
            $params['class_id'] ?? null,
            $params['session_year_id'] ?? null,
            $params['payment_status'] ?? null
        ]));

        if (!$isFilterEmpty) {
            if (!empty($params['search'])) {
                $search = $params['search'];
                $sql->where(function ($query) use ($search) {
                    $query->where('id', 'LIKE', "%$search%")
                        ->orWhere('order_id', 'LIKE', "%$search%")
                        ->orWhere('payment_id', 'LIKE', "%$search%")
                        ->orWhereHas('student.user', function ($q) use ($search) {
                            $q->where('first_name', 'LIKE', "%$search%")
                                ->orWhere('last_name', 'LIKE', "%$search%");
                        });
                });
            }

            if (!empty($params['session_year_id'])) {
                $sessionYearId = $params['session_year_id'];
                if (!SessionYear::find($sessionYearId)) {
                    throw new ModelNotFoundException("Session year not found with ID: {$sessionYearId}");
                }
                $sql->where('session_year_id', $sessionYearId);
            }

            if (!empty($params['class_id'])) {
                $classId = $params['class_id'];
                if (!ClassSchool::find($classId)) {
                    throw new ModelNotFoundException("Class not found with ID: {$classId}");
                }
                $sql->where('class_id', $classId);
            }

            if (!empty($params['payment_status'])) {
                $allowedStatuses = ['pending', 'success', 'failed'];
                $status = $params['payment_status'];
                if (!in_array($status, $allowedStatuses)) {
                    throw new InvalidArgumentException("Invalid payment status: {$status}");
                }
                $sql->where('payment_status', $status);
            }
        }

        $total = $sql->count();
        $res = $sql->get();
        $rows = [];
        $no = 1;
        foreach ($res as $row) {
            try {
                $rows[] = [
                    'id' => $row->id,
                    'no' => $no++,
                    'student_id' => $row->student_id,
                    'student_name' => $row->student->user->first_name . ' ' . $row->student->user->last_name,
                    'total_fees' => $row->total_amount,
                    'amount_paid' => $row->amount_paid == 0 ? $row->total_amount : $row->amount_paid,
                    'fees_left' => $row->fees_left,
                    'payment_gateway' => $row->payment_gateway,
                    'payment_status' => $row->payment_status,
                    'order_id' => $row->order_id,
                    'mode' => $row->mode,
                    'cheque_no' => $row->cheque_no,
                    'payment_id' => $row->payment_id,
                    'payment_signature' => $row->payment_signature,
                    'session_year_id' => $row->session_year_id,
                    'session_year_name' => $row->session_year->name
                ];
            } catch (Exception $e) {
                Log::error("Error processing payment transaction record ID {$row->id}: " . $e->getMessage(), [
                    'exception' => $e,
                    'record' => $row->toArray()
                ]);
            }
        }

        // Handle PDF generation if requested
        if (request()->get('print')) {
            try {
                $class = !empty($params['class_id']) ? ClassSchool::findOrFail($params['class_id']) : null;
                $session_year = !empty($params['session_year_id']) ? SessionYear::findOrFail($params['session_year_id']) : null;

                $pdf = FeePrints::getInstance(get_center_id(), 'L');
                $pdf->printFeeLogsList($rows, $class, $session_year);

                return response(
                    $pdf->Output('', 'FEE TRANSACTION LOGS.pdf'),
                    200,
                    ['Content-Type' => 'application/pdf']
                );
            } catch (Exception $e) {
                Log::error('PDF generation failed: ' . $e->getMessage(), [
                    'exception' => $e,
                    'params' => request()->all()
                ]);
                return response()->json([
                    'error' => 'Failed to generate PDF',
                    'message' => 'An error occurred while generating the PDF report'
                ], 500);
            }
        }

        // Return JSON response
        $response = [
            'total' => $total,
            'rows' => $rows
        ];

        logger($response);
        return response()->json($response);

    } catch (ModelNotFoundException $e) {
        Log::error('Resource not found: ' . $e->getMessage(), [
            'exception' => $e,
            'params' => request()->all()
        ]);
        return response()->json([
            'error' => 'Resource not found',
            'message' => $e->getMessage()
        ], 404);

    } catch (InvalidArgumentException $e) {
        Log::error('Invalid input parameter: ' . $e->getMessage(), [
            'exception' => $e,
            'params' => request()->all()
        ]);
        return response()->json([
            'error' => 'Invalid input',
            'message' => $e->getMessage()
        ], 400);

    } catch (Exception $e) {
        Log::error('Unexpected error in feesTransactionsLogsList: ' . $e->getMessage(), [
            'exception' => $e,
            'params' => request()->all(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'error' => 'Internal server error',
            'message' => 'An unexpected error occurred while processing your request'
        ], 500);
    }
}

    // : Response|JsonResponse
    public function feesPaidReceiptPDF($id): Response|JsonResponse
    {
        try {
            $center = Center::find(get_center_id());

            $settings = getSettings();

            $storage_url = Auth::user()->image;

            if (Storage::exists($settings['logo2'])) {
                $storage_url = Storage::url($settings['logo2']);
            } else if (Storage::exists($settings['logo1'])) {
                $storage_url = Storage::url($settings['logo1']);
            }

            $logo = url($storage_url);

            $school_name = Auth::user()->center->name;
            $school_address = getSettings('school_address', Auth::user()->center->id);
            $school_address = $school_address['school_address'] ?? '';
            $currency_symbol = getSettings('currency_symbol', Auth::user()->center->id);
            if (isset($currency_symbol) && count($currency_symbol)) {
                $currency_symbol = $currency_symbol['currency_symbol'];
            } else {
                $currency_symbol = null;
            }

            //Getting the Fees Paid Data
            $fees_paid = FeesPaid::where('id', $id)->with('student.user:id,first_name,last_name', 'class', 'session_year')->get()->first();

            // Variables
            $student_id = $fees_paid->student_id;
            $class_id = $fees_paid->class_id;
            $session_year_id = $fees_paid->session_year_id;

            $optional_fees_type_id = FeesClass::owner()->where(['class_id' => $class_id, 'choiceable' => 1])->pluck('fees_type_id');

            // Paid Installment Data
            $paid_installment = PaidInstallmentFee::owner()->where(['student_id' => $student_id, 'class_id' => $class_id, 'session_year_id' => $session_year_id])->with('installment_fee')->get();

            //Fees Choiceable Data
            $fees_choiceable = FeesChoiceable::owner()->whereIn('fees_type_id', $optional_fees_type_id)->where(['student_id' => $student_id, 'class_id' => $class_id, 'session_year_id' => $session_year_id])->with('fees_type')->orderby('id', 'asc')->get();

            //Fees Class Data
            $fees_class = FeesClass::owner()->where(['class_id' => $class_id, 'choiceable' => 0])->with('fees_type')->get();

            //Session Year Data
            $session_year = SessionYear::owner()->where('id', $session_year_id)->first();

            $pdf = FeePrints::getInstance(get_center_id(), 'P');

            $pdf->printFeeReceipts($logo, $school_name, $fees_paid, $paid_installment, $fees_choiceable,
                $currency_symbol, $school_address, $fees_class, $session_year, $center);

//            $pdf = Pdf::loadView('fees.fees_receipt', compact('logo', 'school_name', 'fees_paid', 'paid_installment', 'fees_choiceable', 'currency_symbol', 'school_address', 'fees_class', 'session_year'));

//            return $pdf->stream('fees-receipt.pdf');

            return response(
                $pdf->Output('', 'Student Receipt.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            );
            return response()->json($response);
        }
    }

    public function feesPaidRemoveInstallmentFees($id)
    {
        if (!Auth::user()->can('fees-paid')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        try {
            $paid_installment_fee_db = PaidInstallmentFee::find($id);

            //Get Detials
            $student_id = $paid_installment_fee_db->student_id;
            $class_id = $paid_installment_fee_db->class_id;
            $session_year_id = $paid_installment_fee_db->session_year_id;
            $installment_fee_amount = $paid_installment_fee_db->amount;
            $fees_payment_transaction_id = $paid_installment_fee_db->payment_transaction_id;

            // Delete Fees Installment Paid Entry
            $paid_installment_fee_db->delete();

            // Check the Payment Transaction ID Is Not Null
            if (!empty($fees_payment_transaction_id)) {
                // Check the Payment Transaction Entry
                $fees_payment_db = PaymentTransaction::where('id', $fees_payment_transaction_id);
                // Get the Payment Transaction Amount
                $fees_transaction_amount = $fees_payment_db->pluck('total_amount')->first();

                // Reduce the amount of Deleted Choiceable Fees in Payment Transaction
                $updated_transaction_fees_amount = $fees_transaction_amount - $installment_fee_amount;

                // If Updated Fees Amount is not Zero then update the Total Amount Else Delete the entry
                if ($updated_transaction_fees_amount != 0) {
                    $fees_transaction_update = PaymentTransaction::find($fees_payment_transaction_id);
                    $fees_transaction_update->total_amount = $updated_transaction_fees_amount;
                    $fees_transaction_update->save();
                } else {
                    $fees_transaction_update = PaymentTransaction::where('id', $fees_payment_transaction_id)->delete();
                }
            }


            // Check the Fees Paid Entry
            $fees_paid_db = FeesPaid::where(['student_id' => $student_id, 'class_id' => $class_id, 'session_year_id' => $session_year_id]);
            // Get the Fees Paid ID
            $fees_paid_id = $fees_paid_db->pluck('id')->first();
            // Get the Fees Paid Amount
            $fees_paid_amount = $fees_paid_db->pluck('total_amount')->first();

            // Reduce the amount of Deleted Choiceable Fees in Fees Paid
            $updated_fees_paid_amount = $fees_paid_amount - $installment_fee_amount;

            // If Updated Fees Amount is not Zero then update the Total Amount Else Delete the entry
            if ($updated_fees_paid_amount != 0) {
                $fees_paid_update = FeesPaid::find($fees_paid_id);
                $fees_paid_update->total_amount = $updated_fees_paid_amount;
                $fees_paid_update->is_fully_paid = 0;
                $fees_paid_update->save();
            } else {
                $fees_paid_update = FeesPaid::where('id', $fees_paid_id)->delete();
            }

            $response = array(
                'error' => false,
                'message' => trans('data_delete_successfully')
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function compulsoryFeesPaidStore(Request $request)
    {
        // things to do if a student wants to pay a compulsory fees.
        if (!Auth::user()->can('fees-paid')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'mode' => 'required|in:0,1,2',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $date = date('Y-m-d H:i:s', strtotime($request->date));
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            // Get the Father Id for Payment Transaction Table
            $father_id = Students::owner()->where('id', $request->student_id)->pluck('father_id')->first();

            // creating the payment transaction information for the payment the user is making
            $payment_transaction_store = new PaymentTransaction();
            $payment_transaction_store->student_id = $request->student_id;
            $payment_transaction_store->class_id = $request->class_id;
            $payment_transaction_store->parent_id = $father_id;
            $payment_transaction_store->mode = $request->mode;
            $payment_transaction_store->cheque_no = (!empty($request->cheque_no)) ? $request->cheque_no : null;
            $payment_transaction_store->type_of_fee = !empty($request->installment_fees) ? 1 : 0;
            $payment_transaction_store->payment_status = 1;
            $payment_transaction_store->date = $date;
            $payment_transaction_store->total_amount = $request->total_amount;
            $payment_transaction_store->session_year_id = $session_year_id;
            $payment_transaction_store->center_id = get_center_id();

            $payment_transaction_store->save();


            // Add Data in Array of Optional Fees
            $installment_fees_store = array();
            if (!empty($request->installment_fees)) {
                foreach ($request->installment_fees as $data) {
                    if (!empty($data['id'])) {
                        $installment_fees_store[] = array(
                            'student_id' => $request->student_id,
                            'parent_id' => $father_id,
                            'class_id' => $request->class_id,
                            'installment_fee_id' => $data['id'],
                            'amount' => $data['amount'],
                            'session_year_id' => $session_year_id,
                            'date' => $date,
                            'due_charges' => $data['due_charges'] ?? null,
                            'payment_transaction_id' => $payment_transaction_store->id,
                            'center_id' => get_center_id()
                        );
                        $is_fully_paid = $data['fully_paid'];
                    }
                }
            }

            // Add Data in Fees Choiceable Of Optional Payment
            PaidInstallmentFee::owner()->insert($installment_fees_store);


            if ($request->installment_mode == 0) {
                $is_fully_paid = 1;
            }
            // Add Data in Fees Paid Of Optional Payment Transaction
            $update_fees_paid_query = FeesPaid::owner()->where(['student_id' => $request->student_id, 'class_id' => $request->class_id, 'session_year_id' => $session_year_id]);

            $fees_paid = $update_fees_paid_query->firstOrNew();
            $fees_paid->total_amount += $request->total_amount;
            $fees_paid->date = $date;
            $fees_paid->is_fully_paid = $is_fully_paid;
            // $fees_paid->center_id = Auth::user()->center->id;
            $fees_paid->center_id = get_center_id();
            if (!$fees_paid->exists) {
                $fees_paid->student_id = $request->student_id;
                $fees_paid->class_id = $request->class_id;
                $fees_paid->session_year_id = $session_year_id;
            }
            $fees_paid->save();

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully')
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'more' => $e->getMessage(),
                'trace' => $e->getTrace()
            );
        }
        return response()->json($response);
    }


    public function optionalFeesPaidStore(Request $request)
    {
        if (!Auth::user()->can('fees-paid')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'mode' => 'required|in:0,1,2',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $date = date('Y-m-d H:i:s', strtotime($request->date));
            $session_year = getSettings('session_year');
            $session_year_id = $session_year['session_year'];

            // Get the Father Id for Payment Transaction Table
            $father_id = Students::where('id', $request->student_id)->pluck('father_id')->first();

            // Add Data in Payment Transaction Of Optional Payment Transaction
            $payment_transaction_store = new PaymentTransaction();
            $payment_transaction_store->student_id = $request->student_id;
            $payment_transaction_store->class_id = $request->class_id;
            $payment_transaction_store->parent_id = $father_id;
            $payment_transaction_store->mode = $request->mode;
            $payment_transaction_store->cheque_no = (!empty($request->cheque_no) && $request->mode == 1) ? $request->cheque_no : null;
            $payment_transaction_store->type_of_fee = 2;
            $payment_transaction_store->payment_status = 1;
            $payment_transaction_store->total_amount = $request->total_amount == 0 ? $request->optional_fees_type_data[0]['amount'] : $request->total_amount;
            $payment_transaction_store->amount_paid = $request->amount_paid ?? 0;
            $payment_transaction_store->fees_left = $request->total_amount == 0 ? $request->optional_fees_type_data[0]['amount'] - $request->amount_paid : 0;
            $payment_transaction_store->session_year_id = $session_year_id;
            $payment_transaction_store->center_id = get_center_id();
            $payment_transaction_store->save();

            // Add Data in Array of Optional Fees
            $optional_fees_store = array();
            foreach ($request->optional_fees_type_data as $fees_type_data) {
                if (!empty($fees_type_data['id'])) {
                    $optional_fees_store[] = array(
                        'student_id' => $request->student_id,
                        'class_id' => $request->class_id,
                        'fees_type_id' => $fees_type_data['id'],
                        'is_due_charges' => 0,
                        'total_amount' => $fees_type_data['amount'],
                        'session_year_id' => $session_year_id,
                        'date' => $date,
                        'payment_transaction_id' => $payment_transaction_store->id,
                        'center_id' => get_center_id()
                    );
                }
            }

            // Add Data in Fees Choiceable Of Optional Payment
            FeesChoiceable::insert($optional_fees_store);

            // Add Data in Fees Paid Of Optional Payment Transaction
            $update_fees_paid_query = FeesPaid::where(['student_id' => $request->student_id, 'class_id' => $request->class_id, 'session_year_id' => $session_year_id]);

            $fees_paid = $update_fees_paid_query->firstOrNew();
            $fees_paid->total_amount += $request->total_amount;
            $fees_paid->date = $date;
            if (!$fees_paid->exists) {
                $fees_paid->student_id = $request->student_id;
                $fees_paid->class_id = $request->class_id;
                $fees_paid->is_fully_paid = 0;
                $fees_paid->session_year_id = $session_year_id;
                $fees_paid->center_id = get_center_id();
            }
            $fees_paid->save();

            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully')
            );
        } catch (Throwable $e) {
            logger($e->getMessage());
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

}
