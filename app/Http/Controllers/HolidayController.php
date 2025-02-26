<?php

namespace App\Http\Controllers;

use App\Domain\Holiday\Services\HolidayService;
use App\Http\Requests\Holiday\ShowHolidaysRequest;
use App\Http\Requests\Holiday\StoreHolidayRequest;
use App\Http\Requests\Holiday\UpdateHolidayRequest;
use App\Models\Holiday;
use App\Printing\DashboardPrints;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Throwable;

class HolidayController extends Controller
{
    public function __construct(private HolidayService $holidayService)
    {
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('holiday-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }


        return view('holiday.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHolidayRequest $request)
    {
        try {
            $this->holidayService->createHoliday($request->validated());

            return response()->json([
                'error' => false,
                'message' => trans('data_store_successfully')
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => app()->environment('production') 
                    ? trans('error_occurred') 
                    : $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateHolidayRequest $request)
    {
        try {
            $this->holidayService->updateHoliday(
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
                    : $e->getMessage()
            ], 500);
        }
    }

    public function holiday_view()
    {
        return view('holiday.list');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShowHolidaysRequest $request)
    {
        try {
            $result = $this->holidayService->getHolidaysData(
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('holiday-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {
            Holiday::find($id)->delete();
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

    public function upcoming_holiday()
    {
        if (!Auth::user()->can('event-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
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

            $sql = Holiday::select('date', 'title', 'description')->Owner()->where('date', '>=', Carbon::now())->orderBy('date', 'ASC');

        if (!empty($_GET['search'])) {
            $search = $_GET['search'];

            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orwhere('name', 'LIKE', "%$search%")
                    ->orwhere('location', 'LIKE', "%$search%")
                    ->orwhere('end_date', 'LIKE', "%$search%")
                    ->orwhere('start_date', 'LIKE', "%$search%");
            })->Owner();
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
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->title;
            $tempRow['date'] = date($data['date_formate'], strtotime($row->date));

            $rows[] = $tempRow;
        }

        if(request()->get('print')){
            $pdf = DashboardPrints::getInstance(get_center_id(), 'P');

            $pdf->printHolidays($rows);

            return response(
                $pdf->Output('', 'UPCOMING HOLIDAYS.pdf'),
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
