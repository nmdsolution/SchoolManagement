<?php


namespace App\Yadiko\Exam\Download\DownloadExamReport\Applications;


trait PaymentFilterTrait {

    protected function applyPaymentFilter($query, $paymentStatus) {
        if ($paymentStatus !== null && $paymentStatus !== '') {
            $status = (int)$paymentStatus;

            $query->where(function ($query) use ($status) {
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
        return $query;
    }
}