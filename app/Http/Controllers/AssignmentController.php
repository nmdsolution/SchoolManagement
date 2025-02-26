<?php

namespace App\Http\Controllers;

use App\Domain\Assignment\Services\AssignmentListService;
use App\Domain\Assignment\Services\AssignmentService;
use App\Domain\Assignment\Services\AssignmentSubmissionService;
use App\Http\Requests\Assignment\StoreAssignmentRequest;
use App\Http\Requests\Assignment\UpdateAssignmentRequest;
use App\Http\Requests\Assignment\UpdateAssignmentSubmissionRequest;
use App\Models\Assignment;
use App\Models\ClassSection;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{

    public function __construct(
        private AssignmentListService $assignmentListService,
        private AssignmentService $assignmentService,
        private AssignmentSubmissionService $assignmentSubmissionService
        )
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('assignment-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $class_section = ClassSection::owner()->SubjectTeacher()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();
        $subjects = Subject::SubjectTeacher()->orderBy('id', 'ASC')->where('center_id', session()->get('center_id'))->get();
        return response(view('assignment.index', compact('class_section', 'subjects')));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if (!auth()->user()->can('assignment-list')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        return response()->json($this->assignmentListService->getAssignmentsList($request));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAssignmentRequest $request, $id)
    {
        $result = $this->assignmentService->updateAssignment($id, $request);
        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAssignmentRequest $request)
    {
        $result = $this->assignmentService->createAssignment($request);
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
        if (!Auth::user()->can('assignment-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {
            $assignment = Assignment::find($id);
            //            //Delete all the Assignment Submissions first
            //            $assignment_submission = AssignmentSubmission::where('assignment_id', $id)->get();
            //            if ($assignment_submission) {
            //                foreach ($assignment_submission as $submission) {
            //                    if (isset($submission->file)) {
            //                        foreach ($submission->file as $file) {
            //                            if (Storage::disk('public')->exists($file->file_url)) {
            //                                Storage::disk('public')->delete($file->file_url);
            //                            }
            //                        }
            //                        $submission->delete();
            //                    }
            //                }
            //            }
            //
            //            //After that Delete Assignment and its files from the server
            //            if ($assignment->file) {
            //                foreach ($assignment->file as $file) {
            //                    if (Storage::disk('public')->exists($file->file_url)) {
            //                        Storage::disk('public')->delete($file->file_url);
            //                    }
            //                }
            //            }
            //            $assignment->file()->delete();
            $assignment->delete();
            $response = array(
                'error' => false,
                'message' => trans('data_delete_successfully')
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function viewAssignmentSubmission()
    {
        if (!Auth::user()->can('assignment-submission')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        if (Auth::user()->teacher) {
            $class_section = ClassSection::owner()->SubjectTeacher()->with('class.stream', 'section')->whereHas('class', function ($q) {
                $q->activeMediumOnly();
            })->get();
        } else {
            $class_section = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
                $q->activeMediumOnly();
            })->get();
        }

        return response(view('assignment.submission', compact('class_section')));
    }

    public function assignmentSubmissionList(Request $request)
    {
        if (!auth()->user()->can('assignment-submission')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        $result = $this->assignmentSubmissionService->getSubmissionsList($request);
        return response()->json($result);
    }


    public function updateAssignmentSubmission(UpdateAssignmentSubmissionRequest $request, $id)
    {
        $result = $this->assignmentSubmissionService->updateSubmission($id, $request);
        return response()->json($result);
    }

    public function edit($id)
    {

    }
}
