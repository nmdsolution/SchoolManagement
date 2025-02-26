<?php

namespace App\Http\Controllers;

use App\Domain\Announcement\Services\AnnouncementListService;
use App\Domain\Announcement\Services\AnnouncementService;
use App\Http\Requests\Announcement\StoreAnnouncementRequest;
use App\Http\Requests\Announcement\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AnnouncementController extends Controller
{
    public function __construct(
        private AnnouncementService $announcementService,
        private AnnouncementListService $announcementListService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('announcement-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class_section = ClassSection::owner()->SubjectTeacher()->with('class', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        return view('announcement.index', compact('class_section'));
    }

    public function getAssignData(Request $request)
    {
        $data = $request->data;
        $class_id = $request->class_id;
        if ($data == 'class_section' && $class_id != '') {
            $info = ClassSubject::where('class_id', $class_id)->with('subject')->get();
        } elseif ($data == 'class') {
            $info = ClassSchool::get();
        } else {
            $info = '';
        }
        return response()->json($info);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws Throwable
     */
    public function store(StoreAnnouncementRequest $request)
    {
        $result = $this->announcementService->createAnnouncement($request);
        return response()->json($result);
    }

    public function update(UpdateAnnouncementRequest $request)
    {
        $result = $this->announcementService->updateAnnouncement($request);
        return response()->json($result);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if (!Auth::user()->can('announcement-list')) {
            return response()->json([
                'message' => trans('no_permission_message')
            ]);
        }

        $result = $this->announcementListService->getAnnouncementsList($request);
        return response()->json($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('announcement-delete')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {
            Announcement::find($id)->delete();
            $response = array(
                'error'   => false,
                'message' => trans('data_delete_successfully')
            );
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }
}
