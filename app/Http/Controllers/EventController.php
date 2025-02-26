<?php

namespace App\Http\Controllers;

use App\Domain\Event\Services\EventService;
use App\Http\Requests\Event\ShowEventsRequest;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use App\Printing\DashboardPrints;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Throwable;

class EventController extends Controller
{
    public function __construct(private EventService $eventService)
    {
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('event-list')) {
            $response = [
                'error'   => true,
                'message' => trans('no_permission_message')
            ];
            return redirect(route('home'))->withErrors($response);
        }

        return view('event.index');
    }

    public function store(StoreEventRequest $request)
    {
        try {
            $this->eventService->createEvent($request->validated());

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

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShowEventsRequest $request)
    {
        try {
            $result = $this->eventService->getEventsList(
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEventRequest $request, int $id)
    {
        try {
            $this->eventService->updateEvent($id, $request->validated());

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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        if (!Auth::user()->can('event-delete')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        Event::find($id)->delete();
        $response = [
            'error'   => false,
            'message' => trans('data_delete_successfully')
        ];

        return response($response);
    }

    public function upcoming_events()
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

        $sql = Event::select('name', 'description', 'start_date', 'end_date', 'location')->Owner()->where('start_date', '>=', Carbon::now())->orderBy('start_date', 'ASC')->ActiveMediumOnly();

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
            $tempRow['name'] = $row->name;
            $tempRow['date'] = date($data['date_formate'], strtotime($row->start_date)) .' - '.date($data['date_formate'], strtotime($row->end_date));
            $tempRow['start_date'] = date($data['date_formate'], strtotime($row->start_date));
            $tempRow['end_date'] = date($data['date_formate'], strtotime($row->end_date));

            $rows[] = $tempRow;
        }

        if(request()->get('print')){
            $pdf = DashboardPrints::getInstance(get_center_id(), 'P');

            $pdf->printEvents($rows);

            return response(
                $pdf->Output('', 'UPCOMING EVENTS.pdf'),
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
