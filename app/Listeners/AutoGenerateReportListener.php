<?php

namespace App\Listeners;

use App\Events\MarksUploadedEvent;
use Illuminate\Support\Facades\DB;
use App\Services\ExamReportService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AutoGenerateReportListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(protected ExamReportService $examReportService)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MarksUploadedEvent $event)
    {
        $request = $event->request;

        try {

            Log::warning('Begin auto generating report');

            
            DB::beginTransaction();
            
            $data = $this->examReportService->generateTermReport($request);

            DB::commit();
            if (!$data) {
                session()->flash('success', "No Exam Data found to generate Report");
                
                Log::warning('Auto generating report finished!');
                
            } else {
                session()->flash('success', trans('data_store_successfully'));
            }
        } catch (\Throwable $e) {
            DB::rollback();
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e->getMessage() . ' - File ' . $e->getFile() . ' At Line - ' . $e->getLine(),
                'trace' => $e->getTrace(),
            );
        }
    }
}
