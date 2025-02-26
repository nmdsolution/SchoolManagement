<?php

namespace App\Http\Controllers;

use App\Domain\SessionYear\Services\SessionYearService;
use App\Http\Requests\SessionYear\ShowSessionYearRequest;
use App\Http\Requests\SessionYear\StoreSessionYearRequest;
use App\Http\Requests\SessionYear\UpdateSessionYearRequest;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\ExamMarks;
use App\Models\ExamResult;
use App\Models\ExamTimetable;
use App\Models\FeesChoiceable;
use App\Models\FeesPaid;
use App\Models\InstallmentFee;
use App\Models\OnlineExam;
use App\Models\PaidInstallmentFee;
use App\Models\PaymentTransaction;
use App\Models\SessionYear;
use App\Models\StudentSessions;
use App\Models\StudentSubject;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Throwable;

class SessionYearController extends Controller 
{
    public function __construct(private SessionYearService $sessionYearService)
    {
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (!Auth::user()->can('session-year-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        return view('session_years.index');
    }


    public function store(StoreSessionYearRequest $request)
    {
        try {
            $this->sessionYearService->createSessionYear($request->validated());

            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully')
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage(),
                'data' => $e
            ], 500);
        }
    }


    public function update(UpdateSessionYearRequest $request)
    {
        try {
            $this->sessionYearService->updateSessionYear(
                $request->validated('id'),
                $request->validated()
            );

            return response()->json([
                'error' => false,
                'message' => trans('data_update_successfully')
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => trans('record_not_found')
            ], 404);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage(),
                'data' => $e
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShowSessionYearRequest $request)
    {
        try {
            $result = $this->sessionYearService->getSessionsList(
                $request->validated()
            );

            if ($request->filled('print')) {
                return $result;
            }

            return response()->json($result);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $session_year = SessionYear::find($id);
        return response($session_year);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (!Auth::user()->can('session-year-delete')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {

            //check wheather session year id is associated with other table..
            $announcements = Announcement::where('session_year_id', $id)->count();
            $assignment_submissions = AssignmentSubmission::where('session_year_id', $id)->count();
            $assignments = Assignment::where('session_year_id', $id)->count();
            $attendances = Attendance::where('session_year_id', $id)->count();
            $exam_marks = ExamMarks::where('session_year_id', $id)->count();
            $exam_results = ExamResult::where('session_year_id', $id)->count();
            $exam_timetables = ExamTimetable::where('session_year_id', $id)->count();
            $exams = Exam::where('session_year_id', $id)->count();
            $fees_choiceables = FeesChoiceable::where('session_year_id', $id)->count();
            $fees_paids = FeesPaid::where('session_year_id', $id)->count();
            $online_exams = OnlineExam::where('session_year_id', $id)->count();
            $payment_transactions = PaymentTransaction::where('session_year_id', $id)->count();
            $student_sessions = StudentSessions::where('session_year_id', $id)->count();
            $student_subjects = StudentSubject::where('session_year_id', $id)->count();
            $fees_installments = InstallmentFee::where('session_year_id',$id)->count();

            if ($announcements || $assignment_submissions || $assignments || $attendances || $exam_marks || $exam_results || $exam_timetables || $exams || $fees_choiceables || $fees_paids || $online_exams || $payment_transactions || $student_sessions || $student_subjects || $fees_installments) {
                $response = array(
                    'error'   => true,
                    'message' => trans('cannot_delete_because_data_is_associated_with_other_data')
                );
            } else {
                $year = SessionYear::find($id);
                if ($year->default == 1) {
                    $response = array(
                        'error'   => true,
                        'message' => trans('default_session_year_cannot_delete')
                    );
                } else {
                    $year->delete();
                    $response = [
                        'error'   => false,
                        'message' => trans('data_delete_successfully')
                    ];
                }
            }
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function deleteInstallmentData($id){
        if (!Auth::user()->can('session-year-delete')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {
            $installment_data_exists = PaidInstallmentFee::where('installment_fee_id',$id)->count();
            if($installment_data_exists){
                $response = array(
                    'error' => true,
                    'message' => trans('cannot_delete_because_data_is_associated_with_other_data')
                );
            }else{
                InstallmentFee::where('id',$id)->forceDelete();
                $response = [
                    'error' => false,
                    'message' => trans('data_delete_successfully')
                ];
            }
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }
}
