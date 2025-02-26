<?php

namespace App\Http\Controllers;

use App\Models\ClassSchool;
use Throwable;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\SubjectTeacher;
use App\Printing\AcademicPrints;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SubjectTeacherController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index() {
        if (!Auth::user()->can('subject-teacher-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $subjects = Subject::owner()->activeMediumOnly()->orderBy('id', 'DESC')->get();

        $class_section = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        $teachers = Teacher::owner()->with('user')->get();

        return view('subject.teacher', compact('class_section', 'teachers', 'subjects'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) {

        if (!Auth::user()->can('subject-teacher-create') || !Auth::user()->can('subject-teacher-edit')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $request->validate([
            'class_section_id' => 'required|numeric',
            'subject_id'       => 'required|numeric',
            'teacher_id'       => 'required',
        ]);

        try {

            foreach ($request->teacher_id as $teacher_id) {
                if (isset($request->id) && $request->id != '') {
                    $subject_teacher = SubjectTeacher::find($request->id);
                } else {
                    $subject_teacher = new SubjectTeacher();
                }
                $subject_teacher->class_section_id = $request->class_section_id;
                $subject_teacher->subject_id = $request->subject_id;
                $subject_teacher->teacher_id = $teacher_id;
                $subject_teacher->save();
            }
            $response = [
                'error'   => false,
                'message' => trans('data_store_successfully')
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

    public function update(Request $request) {

        if (!Auth::user()->can('subject-teacher-edit')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $request->validate([
            'class_section_id' => 'required|numeric',
            'subject_id'       => 'required|numeric',
            'teacher_id'       => 'required',
        ]);

        try {
            $subject_teacher = SubjectTeacher::find($request->id);
            $subject_teacher->class_section_id = $request->class_section_id;
            $subject_teacher->subject_id = $request->subject_id;
            $subject_teacher->teacher_id = $request->teacher_id;
            $subject_teacher->save();
            $response = [
                'error'   => false,
                'message' => trans('data_update_successfully')
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

    /**
     * Display the specified resource.
     *
     * @return JsonResponse
     */
    public function show(Request $request) {
        if (!Auth::user()->can('subject-teacher-list')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        if(request()->get('print') && empty($_GET['class_id']) && empty($_GET['teacher_id']) && empty($_GET['subject_id'])){
            $classes = ClassSchool::where('center_id', get_center_id())->get();

            $pdf = AcademicPrints::getInstance(get_center_id());
            $pdf->printSubjectTeacherList($classes);


            return response(
                $pdf->Output('', 'SUBJECT TEACHERS LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'DESC';

        $sql = SubjectTeacher::owner()->with('class_section.class.stream', 'subject', 'teacher')->whereHas('class_section.class', function ($q) {
            $q->Owner()->activeMediumOnly();
        });
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];

            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhereHas('class_section.class', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('class_section.section', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('subject', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('teacher.user', function ($q) use ($search) {
                        $q->whereRaw("concat(users.first_name,' ',users.last_name) LIKE '%" . $search . "%'")->orwhere('users.first_name', 'LIKE', "%$search%")->orwhere('users.last_name', 'LIKE', "%$search%");
                    });
            })->whereHas('teacher', function ($q) {
                $q->whereHas('center_teacher', function ($q) {
                    $q->where('center_id', Auth::user()->center->id);
                });
            });
        }

        if (isset($_GET['class_id']) && $_GET['class_id']) {
            $sql = $sql->where('class_section_id', $_GET['class_id']);
        }
        if (isset($_GET['teacher_id']) && $_GET['teacher_id']) {
            $sql = $sql->where('teacher_id', $_GET['teacher_id']);
        }
        if (isset($_GET['subject_id']) && $_GET['subject_id']) {
            $sql = $sql->where('subject_id', $_GET['subject_id']);
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

            $operate = '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" data-id=' . $row->id . ' data-url=' . url('subject-teachers') . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" data-id=' . $row->id . ' data-url=' . url('subject-teachers', $row->id) . ' title="Delete"><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['class_section_id'] = $row->class_section_id;
            $tempRow['class_section_name'] = $row->class_section->full_name;
            $tempRow['subject_id'] = $row->subject_id;
            $tempRow['subject_name'] = $row->subject->name;
            $tempRow['teacher_id'] = $row->teacher_id;
            $tempRow['teacher_name'] = ($row->teacher) ? ($row->teacher->user->first_name . ' ' . $row->teacher->user->last_name) : '';
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        if(request()->get('print')){
            $class = null;
            $subject = null;
            $teacher = null;

            $pdf = AcademicPrints::getInstance(get_center_id());

            if(!empty($_GET['class_id'])){
                $class = ClassSection::find($_GET['class_id']);
            }
            if(!empty($_GET['teacher_id'])){
                $teacher = Teacher::find($_GET['teacher_id']);
            }
            if(!empty($_GET['subject_id'])){
                $subject = Subject::find($_GET['subject_id']);
            }

            $pdf->printSpecificSubjectTeacherList($rows, $class, $teacher, $subject);


            return response(
                $pdf->Output('', 'SUBJECT TEACHERS LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id) {
        $subject_teacher = SubjectTeacher::find($id);
        return response($subject_teacher);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        if (!Auth::user()->can('subject-teacher-delete')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {
            Timetable::where('subject_teacher_id', $id)->delete();

            SubjectTeacher::find($id)->delete();

            $response = [
                'error'   => false,
                'message' => trans('data_delete_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }
}
