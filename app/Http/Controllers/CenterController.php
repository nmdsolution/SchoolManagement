<?php

/** @noinspection NullPointerExceptionInspection */

///** @noinspection ALL */

namespace App\Http\Controllers;

use App\Domain\Center\Services\CenterCloneService;
use App\Domain\Center\Services\CenterService;
use App\Domain\User\Services\UserCenterAssignmentService;
use App\Domain\User\Services\UserCenterService;
use App\Http\Requests\Center\CenterRequest;
use App\Http\Requests\Center\CloneCenterRequest;
use App\Models\Center;
use App\Models\ClassSchool;
use App\Models\Mediums;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class CenterController extends Controller
{
    private string $folder;

    public function __construct(
        private CenterService $centerService,
        private UserCenterService $userCenterService,
        private UserCenterAssignmentService $userCenterAssignmentService,
        private CenterCloneService $centerCloneService
    ) {
        $this->folder = 'centers';
    }

    public function index()
    {
        if (!Auth::user()->can('center-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        return view('center.index');
    }

    public function store(CenterRequest $request)
    {
        return response()->json(
            $this->centerService->createCenter($request)
        );
    }


    public function show(CenterRequest $request)
    {
        if (!Auth::user()->can('center-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'DESC';

        $sql = Center::with('user');
        if (!empty($request->search)) {
            $search = $request->search;
            $search_columns = array('id', 'user_id');
            $sql->where(function ($query) use ($search_columns, $search) {
                $query->orWhere($search_columns, 'LIKE', "%$search%");
            });
        }
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        foreach ($res as $row) {
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $tempRow['type'] = $row->type;
            $tempRow['contact'] = $row->support_contact;
            $tempRow['email'] = $row->support_email;
            $tempRow['logo'] = $row->logo;
            $tempRow['tagline'] = $row->tagline;
            $tempRow['address'] = $row->address;
            $tempRow['admin'] = $row->user;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function update(CenterRequest $request, $id)
    {
        return response()->json(
            $this->centerService->updateCenter($request, $id)
        );
    }


    public function destroy($id)
    {
        if (!Auth::user()->can('center-delete')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        try {
            $center = Center::findOrFail($id);
            $center->delete();
            $center->user()->delete();
            $response = [
                'error'   => false,
                'message' => trans('data_delete_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
            );
        }
        return response()->json($response);
    }

    public function statusChange(CenterRequest $request, $id)
    {
        try {
            $center = Center::findOrFail($id);
            $center->user->status = $request->status;
            $center->user->save();
            $response = [
                'error'   => false,
                'message' => trans('data_delete_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
            );
        }
        return response()->json($response);
    }

    public function set_center(Request $request, $id = null)
    {

        $this->userCenterService->setUserCenter(auth()->user(), $id);
        return redirect()->back();
    }

    public function set_user_center(Request $request, $id = null)
    {
        $this->userCenterAssignmentService->setUserCenter($request, $id);
        return redirect()->back();
    }

    public function centerStatistics()
    {
        return view('documents.school_documents');
    }

    public function centerClone()
    {
        if (!Auth::user()->can('center-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $centers = Center::all()->pluck('name', 'id');

        $mediums = Mediums::all()->pluck('name', 'id');

        $classes = ClassSchool::where([
            'center_id' => 29,
            'medium_id' => 2
        ])->get()->toArray();


        return view('center.clone', compact('centers', 'mediums'));
    }

    public function listClasses(Request $request)
    {
        if (!Auth::user()->can('center-list')) {
            $response = array(
                'message' => trans('no_permission_message'),
                'success' => false,
            );

            return redirect(route('home'))->withErrors($response);
        }

        $request->validate([
            'center_id' => 'required',
            'medium_id' => 'required'
        ]);

        $centerId = $request->input('center_id');
        $mediumId = $request->input('medium_id');

        $classes = ClassSchool::where([
            'center_id' => $centerId,
            'medium_id' => $mediumId
        ])->get();

        $temp = [];
        $row = [];

        $no = 1;

        foreach ($classes as $class) {
            $temp['no'] = $no++;
            $temp['name'] = $class->name;
        }

        return response()->json([
            'rows' => $classes
        ]);
    }

    public function performValidation($from_center_id, $to_center_id, $medium_id)
    {
        if ($to_center_id == $from_center_id) {
            return response()->json([
                'error' => true,
                'message' => "you can't clone from one school to the same school"
            ]);
        }

        $classExistent = ClassSchool::query()->where([
            'center_id' => $to_center_id,
            'medium_id' => $medium_id,
        ])->count();

        $sectionExistent = Section::query()->where([
            'center_id' => $to_center_id,
        ])->count();

        $subjectExist = Subject::query()->where([
            'center_id' => $to_center_id,
            'medium_id' => $medium_id,
        ])->count();

        if ($classExistent || $sectionExistent || $subjectExist) {
            return response()->json([
                'error' => true,
                'message' => "You can only clone to an empty school"
            ]);
        }
    }

    /*
     * This code here is going to make it possible for users to clone information from one center
     * to another one.
     * */
    public function cloneCenter(CloneCenterRequest $request): \Illuminate\Http\JsonResponse
    {
        if (!Auth::user()->can('center-list')) {
            return response()->json([
                'success' => false,
                'message' => trans('no_permission_message')
            ]);
        }

        try {
            DB::beginTransaction();

            $from_center_id = $request->input('from_center_id');
            $to_center_id = $request->input('to_center_id');
            $medium_id = $request->input('medium_id');

            $state = $this->performValidation($from_center_id, $to_center_id, $medium_id);

            if (isset($state)) {
                return $state;
            }

            $this->centerCloneService->cloneCenter($from_center_id, $to_center_id, $medium_id);

            DB::commit();

            return response()->json([
                'error' => false,
                'message' => trans('success_cloned_center')
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'trace' => $th->getTrace(),
            ]);
        }
    }
}
