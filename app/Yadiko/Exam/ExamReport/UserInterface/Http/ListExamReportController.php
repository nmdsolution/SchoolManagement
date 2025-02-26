<?php

namespace App\Yadiko\Exam\ExamReport\UserInterface\Http;

use App\Http\Controllers\Controller;
use App\Models\ExamTerm;
use App\Models\ExamReport;
use App\Models\ClassSection;
use App\Printing\ExamPrints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamReportClassDetails;
use Illuminate\Support\Facades\Validator;

class ListExamReportController extends Controller
{
    public function index() {
        if (!Auth::user()->can('exam-report')) {
            return redirect(route('home'))->withErrors(['message' => trans('no_permission_message')]);
        }

        // Modified to ensure medium filtering is applied consistently
        $currentMedium = getCurrentMedium()->id;

        $class_sections = ClassSection::owner()
            ->with(['class.stream', 'section'])
            ->whereHas('class', function ($q) use ($currentMedium) {
                $q->where('medium_id', $currentMedium);
            })
            ->get();

        $terms = ExamTerm::owner()
            ->currentSessionYear()
            ->where('medium_id', $currentMedium)
            ->get();

        return view('exams.report-index', compact('class_sections', 'terms'));
    }

    public function show(Request $request)
    {
        if (!Auth::user()->can('exam-report')) {
            return redirect(route('home'))->withErrors(['message' => trans('no_permission_message')]);
        }

        if (isPrimaryCenter()) {
            return response()->json(['message' => trans('not_iplemented_for_primary_center')], 403);
        }

        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required|numeric',
            'term_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()
            ]);
        }

        $currentMedium = getCurrentMedium()->id;

        // Verify the requested class section belongs to current medium
        $validClassSection = ClassSection::whereHas('class', function($q) use ($currentMedium) {
            $q->where('medium_id', $currentMedium);
        })->where('id', $request->class_section_id)->exists();

        if (!$validClassSection) {
            return response()->json([
                'error' => true,
                'message' => 'Invalid class section for current medium',
                'rows' => [],
                'total' => 0
            ]);
        }

        // Verify the requested term belongs to current medium
        $validTerm = ExamTerm::where([
            'id' => $request->term_id,
            'medium_id' => $currentMedium
        ])->exists();

        if (!$validTerm) {
            return response()->json([
                'error' => true,
                'message' => 'Invalid term for current medium',
                'rows' => [],
                'total' => 0
            ]);
        }

        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'rank';
        $order = $request->order ?? 'ASC';

        $examReport = ExamReport::where([
            'class_section_id' => $request->class_section_id,
            'exam_term_id' => $request->term_id
        ])->first();

        if (!$examReport) {
            return response()->json([
                'error' => true,
                'message' => "Exam Report Doesn't Exist. Please Generate First.",
                'rows' => [],
                'total' => 0
            ]);
        }

        // Base query with medium-specific filtering
        $sql = ExamReportClassDetails::with(['student.user', 'student.payment_transactions' => function($query) {
            $query->whereHas('student', function ($q) {
                $q->whereHas('class_section', function ($query) {
                    $query->Owner();
                });
            });
        }])
            ->whereHas('student', function($q) use ($currentMedium) {
                $q->whereHas('class_section', function($query) use ($currentMedium) {
                    $query->whereHas('class', function($q) use ($currentMedium) {
                        $q->where('medium_id', $currentMedium);
                    });
                });
            })
            ->where('exam_report_id', $examReport->id);

        // Payment status filtering

// Payment status filtering
        if (!empty($request->payment_status)) {
            $status = (int)$request->payment_status;
            if (!in_array($status, [0, 1, 2])) {
                return response()->json([
                    'error' => true,
                    'message' => 'Invalid payment status'
                ], 400);
            }

            $sql->where(function ($query) use ($status) {
                switch ($status) {
                    case 2: // Unpaid
                        $query->whereHas('student', function($q) {
                            $q->whereDoesntHave('payment_transactions')
                                ->orWhereHas('payment_transactions', function($subQ) {
                                    $subQ->selectRaw('student_id, SUM(CASE WHEN amount_paid = 0 THEN total_amount ELSE amount_paid END) as total_paid')
                                        ->groupBy('student_id')
                                        ->havingRaw('total_paid = 0');
                                });
                        });
                        break;
                    case 1: // Fully Paid
                        $query->whereHas('student.payment_transactions', function ($subQuery) {
                            $subQuery->where('amount_paid', '>=', 'total_amount');
                        });
                        break;
                  //  case 0: // Partially Paid
                  //      $query->whereHas('student.payment_transactions', function ($subQuery) {
                 //           $subQuery->selectRaw('student_id, SUM(total_amount) as total_amount, SUM(CASE WHEN amount_paid = 0 THEN total_amount ELSE amount_paid END) as total_paid')
                   //             ->groupBy('student_id')
                 //               ->havingRaw('total_paid < total_amount');
                   //     });
                  //      break;
                }
            });
        }
        $total = $sql->count();

        if ($sort == "student_name") {
            $sort = "users.first_name";
        }

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $rows = [];
        foreach ($res as $row) {
            $student = $row->student;
            $paymentTransactions = $student->payment_transactions;

            $tempRow = [
                'payment_details' => []
            ];

            foreach ($paymentTransactions as $transaction) {
                $tempRow['payment_details'][] = [
                    'id' => $transaction->id,
                    'mode' => $transaction->mode,
                    'type_of_fee' => $transaction->type_of_fee,
                    'payment_gateway' => $transaction->payment_gateway,
                    'total_amount' => number_format($transaction->total_amount, 2),
                    'amount_paid' => number_format($transaction->amount_paid == 0 ? $transaction->total_amount : $transaction->amount_paid, 2),
                    'fees_left' => number_format($transaction->fees_left, 2),
                    'payment_status' => $transaction->payment_status,
                    'date' => $transaction->date,
                    'cheque_no' => $transaction->cheque_no,
                    'order_id' => $transaction->order_id,
                    'payment_id' => $transaction->payment_id,
                    'payment_signature' => $transaction->payment_signature,
                ];
            }

            $totalFees = $paymentTransactions->sum('total_amount');
            $amountPaid = $paymentTransactions->sum(function($transaction) {
                return $transaction->amount_paid == 0 ? $transaction->total_amount : $transaction->amount_paid;
            });
            $feesLeft = $paymentTransactions->sum('fees_left');

            $tempRow['total_fees'] = number_format($totalFees, 2);
            $tempRow['amount_paid'] = number_format($amountPaid, 2);
            $tempRow['fees_left'] = number_format($feesLeft, 2);
            $tempRow['payment_status'] = $paymentTransactions->first()->payment_status ?? 0;

            $operate = '<a href="' . route('exam-report-temp.index', [
                    $row->exam_report_id,
                    $request->term_id,
                    $row->student_id
                ]) . '" target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit" ><i class="feather-file"></i></a>&nbsp;&nbsp;';

            $operate .= '<a href="' . route('exam-report-view', [
                    $row->exam_report_id,
                    $request->term_id,
                    $row->student_id
                ]) . '" target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="View" ><i class="feather-eye"></i></a>&nbsp;&nbsp;';

            studentResultRows($tempRow, $row, $operate, $rows);
        }

        if ($request->get('print')) {
            $classSection = !empty($request->class_section_id) ? ClassSection::find($request->class_section_id) : null;
            $pdf = ExamPrints::getInstance(get_center_id(), 'P');
            $pdf->printSpecificExamList($rows, $classSection);

            return response(
                $pdf->Output('', 'SPECIFIC EXAM LIST.pdf'),
                200,
                ['Content-Type' => 'application/pdf']
            );
        }

        return response()->json([
            'total' => $total,
            'rows' => $rows
        ]);
    }
}