<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClassSubjectCollection;
use App\Models\AnnualClassDetails;
use App\Models\AnnualReport;
use App\Models\AnnualSubjectReport;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\ElectiveSubjectGroup;
use App\Models\ExamClassSection;
use App\Models\ExamReport;
use App\Models\ExamResult;
use App\Models\ExamTerm;
use App\Models\FeesClass;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Mediums;
use App\Models\OnlineExam;
use App\Models\OnlineExamQuestion;
use App\Models\Section;
use App\Models\Shift;
use App\Models\Stream;
use App\Models\Students;
use App\Models\StudentSessions;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Timetable;
use App\Printing\AcademicPrints;
use App\Printing\ExamPrints;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClassSchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (!Auth::user()->can('class-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $sections = Section::Owner()->orderBy('id', 'ASC')->get();
        $mediums = Mediums::orderBy('name', 'ASC')->get();
        $streams = Stream::owner()->orderBy('id', 'ASC')->get();
        $shifts = Shift::owner()->where('status', 1)->get();
        return response(view('class.index', compact('sections', 'mediums', 'streams', 'shifts')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('class-create')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'section_id' => 'required|array',
            'section_id.*' => 'numeric',
            'medium_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            DB::beginTransaction();
            if (!$request->stream_id) {
                $class = new ClassSchool();
                $class->name = $request->name;
                $class->medium_id = $request->medium_id;
                $class->center_id = get_center_id();
                $class->shift_id = $request->shift_id;
                $class->save();
                $class_section = array();
                foreach ($request->section_id as $section_id) {
                    $class_section[] = array(
                        'class_id' => $class->id,
                        'section_id' => $section_id
                    );
                }
                ClassSection::insert($class_section);
            } else {
                $classes = [];
                foreach ($request->stream_id as $stream_id) {
                    $classes[] = [
                        'name' => $request->name,
                        'medium_id' => $request->medium_id,
                        'stream_id' => $stream_id,
                        'shift_id' => $request->shift_id,
                        'center_id' => get_center_id()
                    ];
                }

                $classIds = [];
                foreach ($classes as $class) {
                    $classIds[] = ClassSchool::insertGetId($class);
                }

                $class_sections = [];
                foreach ($classIds as $classId) {
                    foreach ($request->section_id as $section_id) {
                        $class_sections[] = [
                            'class_id' => $classId,
                            'section_id' => $section_id
                        ];
                    }
                }
                ClassSection::insert($class_sections);
            }

            DB::commit();
            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
            );
        } catch (\Throwable $e) {
            DB::rollback();
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        if (!Auth::user()->can('class-edit')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'edit_id' => 'required',
            'name' => 'required',
            'section_id' => 'required'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $id = $request->edit_id;
            $class = ClassSchool::owner()->findOrFail($id);
            $class->name = $request->name;
            $class->shift_id = $request->shift_id;
            if ($request->stream_id != null) {
                $existingrow = ClassSchool::owner()->where('name', $request->name)->where('medium_id', $request->medium_id)->where('shift_id', $request->shift_id)->where('stream_id', $request->stream_id)->first();
                if (!$existingrow) {
                    $class->stream_id = $request->stream_id;
                } else {
                    $response = array(
                        'error' => true,
                        'message' => trans('class_with_stream_already_exists'),
                    );
                    return response()->json($response);
                }
            }
            $class->save();
            $all_section_ids = ClassSection::whereIn('section_id', $request->section_id)->where('class_id', $id)->pluck('section_id')->toArray();
            $delete_class_section = $class->sections->pluck('id')->toArray();
            $class_section = array();
            foreach ($request->section_id as $key => $section_id) {
                if (!in_array($section_id, $all_section_ids)) {
                    $class_section[] = array(
                        'class_id' => $class->id,
                        'section_id' => $section_id
                    );
                } else {
                    unset($delete_class_section[array_search($section_id, $delete_class_section)]);
                }
            }

            // insert the once that where not already existing.

            ClassSection::insert($class_section);

            // check wheather the id in $delete_class_section is assosiated with other data ..
            $assignemnts = Assignment::whereIn('class_section_id', $delete_class_section)->count();
            $attendances = Attendance::whereIn('class_section_id', $delete_class_section)->count();
            $exam_result = ExamResult::whereIn('class_section_id', $delete_class_section)->count();
            $lessons = Lesson::whereIn('class_section_id', $delete_class_section)->count();
            $student_session = StudentSessions::whereIn('class_section_id', $delete_class_section)->count();
            $students = Students::whereIn('class_section_id', $delete_class_section)->count();
            $subject_teachers = SubjectTeacher::whereIn('class_section_id', $delete_class_section)->count();
            $timetables = Timetable::whereIn('class_section_id', $delete_class_section)->count();

            if ($assignemnts || $attendances || $exam_result || $lessons || $student_session || $students || $subject_teachers || $timetables) {
                $response = array(
                    'error' => true,
                    'message' => trans('cannot_delete_because_data_is_associated_with_other_data')
                );
                return response()->json($response);
            }

            //Remaining Data in $delete_class_section should be deleted
            ClassSection::whereIn('section_id', $delete_class_section)->where('class_id', $id)->delete();

            $response = array(
                'error' => false,
                'message' => trans('data_update_successfully'),
            );
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage() . $e->getLine()
            );
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ClassSchool $classSchool
     * @return JsonResponse
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('class-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {
            // check whether the class exists in other table
            $class_subject = ClassSubject::where('class_id', $id)->count();
            $class_exam = ExamClassSection::where('class_id', $id)->count();
            $class_fees = FeesClass::where('class_id', $id)->count();

            if ($class_subject || $class_exam || $class_fees) {
                $response = array(
                    'error' => true,
                    'message' => trans('cannot_delete_because_data_is_associated_with_other_data')
                );
            } else {
                $class = ClassSchool::find($id);
                $class_section = ClassSection::where('class_id', $class->id);

                // check the class section id exists with other table ...
                $class_section_id = $class_section->pluck('id');
                $assignemnts = Assignment::whereIn('class_section_id', $class_section_id)->count();
                $attendances = Attendance::whereIn('class_section_id', $class_section_id)->count();
                $exam_result = ExamResult::whereIn('class_section_id', $class_section_id)->count();
                $lessons = Lesson::whereIn('class_section_id', $class_section_id)->count();
                $student_session = StudentSessions::whereIn('class_section_id', $class_section_id)->count();

                $students = Students::query()->whereIn('class_section_id', $class_section_id)->count();


                $subject_teachers = SubjectTeacher::whereIn('class_section_id', $class_section_id)->count();
                $timetables = Timetable::whereIn('class_section_id', $class_section_id)->count();

                if ($assignemnts || $attendances || $exam_result || $lessons || $student_session || $students || $subject_teachers || $timetables) {
                    $response = array(
                        'error' => true,
                        'message' => trans('cannot_delete_because_data_is_associated_with_other_data')
                    );
                } else {
                    $class_section->delete();
                    $class->delete();
                    $response = array(
                        'error' => false,
                        'message' => trans('data_delete_successfully')
                    );
                }

            }
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

//    public function destroy($id): JsonResponse
//    {
//        if (!Auth::user()->can('class-delete')) {
//            $response = array(
//                'error' => true,
//                'message' => trans('no_permission_message')
//            );
//            return response()->json($response);
//        }
//        try {
//            ClassSubject::where('class_id', $id)->delete();
//            ExamClassSection::where('class_id', $id)->delete();
//            FeesClass::where('class_id', $id)->delete();
//
//            $class = ClassSchool::find($id);
//            $class_section = ClassSection::where('class_id', $class->id);
//
//            // check the class section id exists with other table ...
//            $class_section_id = $class_section->pluck('id');
//
//            Assignment::query()->whereIn('class_section_id', $class_section_id)->delete();
//            Attendance::query()->whereIn('class_section_id', $class_section_id)->delete();
//            ExamResult::whereIn('class_section_id', $class_section_id)->delete();
//            Lesson::whereIn('class_section_id', $class_section_id)->delete();
//            SubjectTeacher::whereIn('class_section_id', $class_section_id)->delete();
//            Timetable::whereIn('class_section_id', $class_section_id)->delete();
//
//            $studentController = new StudentController();
//
//            $students = Students::query()->whereHas('studentSessions', function ($query) use ($class_section_id) {
//                $query->whereIn('class_section_id', $class_section_id);
//            })->get();
//
//            StudentSessions::whereIn('class_section_id', $class_section_id)->delete();
//
//            $count = count($students);
//
//            foreach ($students as $student) {
//                $studentController->destroy($student->user_id);
//            }
//
//            $class_section->delete();
//            $class->delete();
//
//            $response = array(
//                'error' => false,
//                'message' => $count . ' students deleted.'
//            );
//        } catch (\Throwable $e) {
//            $response = array(
//                'error' => true,
//                'message' => trans('error_occurred')
//            );
//        }
//        return response()->json($response);
//    }


    public function show(Request $request)
    {
        if (!Auth::user()->can('class-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'DESC';
        $currentMedium = getCurrentMedium();
        $sql = ClassSchool::owner()->with('sections', 'stream', 'shift')->where('medium_id', $currentMedium->id);
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orwhere('name', 'LIKE', "%$search%")
                    ->orWhereHas('sections', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    });
            })->where('center_id', Auth::user()->center->id);
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
            $operate = '<a href=' . route('class.edit', $row->id) . ' class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a href=' . route('class.destroy', $row->id) . ' class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';

            $renameButton = '<a href=' . route('class.rename', $row->id) . ' class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#renameModel"><i class="fa fa-exchange"></i></a>&nbsp;&nbsp;';
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->name;
            $sections = $row->sections;
            $tempRow['sections'] = $sections;
            $tempRow['shift_id'] = $row->shift->id ?? '';
            $tempRow['shift_name'] = $row->shift->title ?? '-';
            $tempRow['section_id'] = $sections->pluck('id');
            $tempRow['section_name'] = $sections->pluck('name');
            $tempRow['section_names'] = $sections->pluck('name');
            $tempRow['stream_id'] = $row->stream->id ?? ' ';
            $tempRow['stream_name'] = $row->stream->name ?? '-';
            $tempRow['medium_id'] = $row->medium_id;
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;
            $tempRow['operate'] = $operate;
            $tempRow['renameButton'] = $renameButton;
            $rows[] = $tempRow;
        }

        if (request()->get('print')) {
            $pdf = AcademicPrints::getInstance(get_center_id());
            $pdf->printClassesList($rows);


            return response(
                $pdf->Output('', 'CLASSES LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function subject()
    {
        if (!Auth::user()->can('class-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        return response(view('class.subject'));
    }

    public function updateSubject(Request $request, $id)
    {
        if (!Auth::user()->can('class-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class = ClassSchool::owner()->where('id', $id)->orderBy('id', 'DESC')->with(['coreSubject', 'electiveSubjectGroup.electiveSubjects.subject', 'stream'])->firstOrFail();
        $subjects = Subject::Owner()->where('medium_id', $class->medium->id)->orderBy('id', 'ASC')->get();
        return response(view('class.edit-subject', compact('class', 'subjects')));
    }

    public function update_subjects(Request $request)
    {
        //        if (!Auth::user()->can('class-create')) {
        //            $response = array(
        //                'error' => true,
        //                'message' => trans('no_permission_message')
        //            );
        //            return response()->json($response);
        //        }
        $validation_rules = array(
            'class_id' => 'required|numeric',
            'core_subject' => 'nullable|array',
            'core_subject.*' => 'nullable|array|required_array_keys:subject_id,weightage',
            'elective_subject_id' => 'array',
            'elective_subjects' => 'nullable|array',
            'elective_subjects.*.subject_id' => 'required|array',
            'elective_subjects.*.total_selectable_subjects' => 'required|numeric',
        );
        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            DB::beginTransaction();
            //Update Core OR Create Subjects
            if ($request->core_subject) {
                $core_subjects = array();
                foreach ($request->core_subject as $row) {
                    $core_subjects[] = array(
                        'id' => $row['class_subject_id'] ?? 0,
                        'class_id' => $request->class_id,
                        'type' => "Compulsory",
                        'subject_id' => $row['subject_id'],
                        'weightage' => $row['weightage'] ?? 0,
                    );
                }

                ClassSubject::upsert($core_subjects, ['id'], ['subject_id', 'weightage']);
            }
            //Create OR Update Subject group for Elective Subjects
            if ($request->elective_subjects) {
                foreach ($request->elective_subjects as $subject_group) {
                    //Create Subject Group
                    if (!empty($subject_group['subject_group_id'])) {
                        $elective_subject_group = ElectiveSubjectGroup::findOrFail($subject_group['subject_group_id']);
                    } else {
                        $elective_subject_group = new ElectiveSubjectGroup();
                    }

                    $elective_subject_group->total_subjects = count($subject_group['subject_id']);
                    $elective_subject_group->total_selectable_subjects = $subject_group['total_selectable_subjects'];
                    $elective_subject_group->class_id = $request->class_id;
                    $elective_subject_group->save();

                    //Assign Elective Subjects to this Subject Group
                    $elective_subject = [];
                    foreach ($subject_group['subject_id'] as $key => $subject_id) {
                        $elective_subject = array(
                            'id' => $subject_group['class_subject_id'][$key] ?? 0,
                            'class_id' => $request->class_id,
                            'type' => "Elective",
                            'subject_id' => $subject_id,
                            'weightage' => $subject_group['weightage'][$key],
                            'elective_subject_group_id' => $elective_subject_group->id,
                        );
                    }
                    ClassSubject::upsert($elective_subject, ['id'], ['subject_id', 'weightage']);
                }
            }
            DB::commit();
            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }

    public function subject_list(Request $request)
    {
        if (!Auth::user()->can('class-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $limit = $request->limit ?? 10;

        $offset = (!empty($request->offset) && !empty($limit)) ? $request->offset : 0;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'DESC';
        $currentMedium = getCurrentMedium();
        $sql = ClassSchool::owner()->with('sections', 'coreSubject', 'electiveSubjectGroup.electiveSubjects.subject', 'stream')->where('center_id', Auth::user()->center->id)
            ->where('medium_id', $currentMedium->id);


        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orwhere('name', 'LIKE', "%$search%");
            })->where('center_id', Auth::user()->center->id);
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

            $row = (object)$row;
            $operate = '<a href=' . route('class.edit', $row->id) . ' class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->full_name;
            $tempRow['section_names'] = $row->sections->pluck('name');
            $tempRow['core_subjects'] = $row->coreSubject;
            $tempRow['elective_subject_groups'] = $row->electiveSubjectGroup;
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        if (request()->get('print')) {
            $pdf = AcademicPrints::getInstance(get_center_id());
            $pdf->printClassSubjectsList($rows);


            return response(
                $pdf->Output('', 'CLASS SUBJECTS LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function subject_destroy($id)
    {
        // if (!Auth::user()->can('class-delete')) {
        //     $response = array(
        //         'error' => true,
        //         'message' => trans('no_permission_message')
        //     );
        //     return response()->json($response);
        // }
        try {
            //check wheather the class subject exists in other table
            $online_exam_questions = OnlineExamQuestion::where('class_subject_id', $id)->count();
            $online_exams = OnlineExam::where('class_subject_id', $id)->count();
            if ($online_exam_questions || $online_exams) {
                $response = array(
                    'error' => true,
                    'message' => trans('cannot_delete_because_data_is_associated_with_other_data')
                );
            } else {
                $class_subject = ClassSubject::findOrFail($id);
                if ($class_subject->type == "Elective") {
                    $subject_group = ElectiveSubjectGroup::findOrFail($class_subject->elective_subject_group_id);
                    $subject_group->total_subjects = $subject_group->total_subjects - 1;
                    if ($subject_group->total_subjects > 0) {
                        $subject_group->save();
                    } else {
                        $subject_group->delete();
                    }
                }
                $class_subject->delete();
                $response = array(
                    'error' => false,
                    'message' => trans('data_delete_successfully')
                );
            }
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function subject_group_destroy($id)
    {
        try {
            $subject_group = ElectiveSubjectGroup::findOrFail($id);

            // check wheather the class subject exists in other table..
            $class_subject_id = ClassSubject::where('elective_subject_group_id', $id)->pluck('id');
            $online_exam_questions = OnlineExamQuestion::whereIn('class_subject_id', $class_subject_id)->count();
            $online_exams = OnlineExam::whereIn('class_subject_id', $class_subject_id)->count();
            if ($online_exam_questions || $online_exams) {
                $response = array(
                    'error' => true,
                    'message' => trans('cannot_delete_because_data_is_associated_with_other_data')
                );
            } else {
                $subject_group->electiveSubjects()->delete();
                $subject_group->delete();
                $response = array(
                    'error' => false,
                    'message' => trans('data_delete_successfully')
                );
            }
        } catch (\Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function getSubjectsByMediumId()
    {
        try {
            $currentMedium = getCurrentMedium();
            $subjects = Subject::where('medium_id', $currentMedium->id)->get();
            $response = array(
                'error' => false,
                'data' => $subjects,
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

    public function class_section_by_center(Request $request)
    {
        try {
            $class_section = ClassSection::owner()->SubjectTeacher()->with('class', 'section')
                ->whereHas('class', function ($q) use ($request) {
                    $q->activeMediumOnly();
                })->get();
            $response = [
                'error' => false,
                'message' => trans('data_fetch_successfully'),
                'data' => $class_section
            ];
        } catch (\Throwable $th) {
            $response = [
                'error' => true,
                'message' => $th
            ];
        }
        return response()->json($response);
    }

    public function class_by_center(Request $request)
    {
        try {
            $class_ids = ClassSchool::where('center_id', $request->center_id)->get();
            $response = [
                'error' => false,
                'message' => trans('data_fetch_successfully'),
                'data' => $class_ids
            ];

        } catch (\Throwable $th) {
            $response = [
                'error' => true,
                'message' => $th
            ];
        }
        return response()->json($response);
    }

    public function get_section($id = null)
    {
        $class_section = ClassSection::with('section')->where('class_id', $id)->get();
        if (count($class_section) > 0) {
            $response = [
                'error' => false,
                'message' => 'Data fetch Successfully',
                'data' => $class_section
            ];
        } else {
            $response = [
                'error' => true,
                'message' => 'No data found',
            ];
        }

        return response()->json($response);
    }

    public function class_report()
    {
        if (!Auth::user()->can('class-report')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $session_year = getSettings('session_year');

        $exam_terms = ExamTerm::query()->where('session_year_id', $session_year['session_year'])->Owner()->currentMedium()->get()->pluck('name', 'id');

        return view('exams.class_report', compact('exam_terms'));
    }

    public function global_data(Request $request)
    {
        $session_year = getSettings('session_year');
        $term = ExamTerm::query()
            ->with('sequence')
            ->where('session_year_id', $session_year['session_year'])
            ->Owner()
            ->currentMedium()
            ->find($request->exam_term_id);
        $groups = Group::owner()->get();
        return response()->json([
            'term' => $term,
            'groups' => $groups
        ]);
    }

    public function annual_report()
    {
        if (!Auth::user()->can('class-report')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        return view('exams.annual_report');
    }

    public function list_annual_reports()
    {
        if (!Auth::user()->can('class-report')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $data = AnnualReport::where('session_year_id', getSettings('session_year')['session_year'])->whereHas('class_section.class', function ($q) {
            $q->Owner()->activeMediumOnly();
        })->get();
        $reports = [];
        $bulkData = array();
        $bulkData['total'] = count($data);
        $rows = array();
        $tempRow = array();
        $no = 1;

        // Process each group of classes
        $class_names = ClassSchool::owner()->activeMediumOnly()->pluck('name', 'id')->toArray();

        $grouped = $this->class_grouper($class_names);
        $group_data = array();

        foreach ($grouped as $starting_name => $class_group) {
            $group_data[] = $starting_name;
        }

        foreach ($data as $row) {
            $operate = '<a href=' . url('annual-class-report', $row->id) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button" data-id=' . $row->id . ' title="Class Report" data-toggle="modal" data-target="#editModal">' . trans('annual_class_report') . '</a>&nbsp;&nbsp;';
            $operate .= '<a href=' . route('view-annual-master-sheet', $row->id) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button" data-id=' . $row->id . ' title="Class Report" data-toggle="modal" data-target="#editModal">' . trans('master_sheet') . '</a>&nbsp;&nbsp;';
            $class_n = $row->class_section->class->name;
            $class_name = collect($group_data)->filter(function($name) use($class_n) {
                return Str::startsWith($class_n, $name);
            })->first();
            $operate .= '<a href=' . url('annual_class_best_report', [$class_name]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button"  title="Best Report" data-toggle="modal" data-target="#editModal">' . trans('annual_best_report') . '</a>&nbsp;&nbsp;';
            $operate .= '<a href=' . url('annual_class_best_in_subject_report', [$class_name]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button"  title="Best Report" data-toggle="modal" data-target="#editModal">' . trans('best_in_subjects') . '</a>&nbsp;&nbsp;';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $row->class_section->class;
            $row->class_section->section;
            $row->class_section->class->stream;
            $tempRow['class_section'] = $row->class_section->full_name;
            $tempRow['action'] = $operate;

            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function class_section_list(Request $request)
    {
        if (!Auth::user()->can('class-report')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $offset = $request->offset ?? 0;
        $limit = $request->limit ?? 10;
        $sort = $request->sort ?? 'id';
        $order = $request->order ?? 'DESC';
        $currentMedium = getCurrentMedium();

        $session_year = getSettings('session_year');
        $sql = ExamReport::where('session_year_id', $session_year['session_year'])->with('class_section.class.stream', 'class_section.section')
            ->where('exam_term_id', $request->exam_term_id)->whereHas('class_section.class', function ($q) {
                $q->Owner()->activeMediumOnly();
            });

        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            // $sql->where(function ($q) use ($search) {
            //     $q->where('id', 'LIKE', "%$search%")
            //         ->orwhere('name', 'LIKE', "%$search%");
            // })->where('center_id', Auth::user()->center->id);
        }
        $total = $sql->count();

        // $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;

        foreach ($res as $row) {

            $row = (object)$row;
            $operate = '<a href=' . url('class-report', $row->id) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button" data-id=' . $row->id . ' title="Class Report" data-toggle="modal" data-target="#editModal">' . trans('Class Report') . '</a>&nbsp;&nbsp;';

            $operate .= '<a href=' . url('student-honor-roll', $row->id) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-sm btn-outline-primary  btn-rounded btn-icon honor-roll default-button edit-data" data-id=' . $row->id . ' title="Honor Roll" class="btn btn-sm bg-success-light edit-data set-form-url">' . trans('Honor Roll') . '</a>&nbsp;&nbsp;';

            $operate .= '<a href=' . route('view-master-sheet', $row->id) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button" data-id=' . $row->id . ' title="Class Report" data-toggle="modal" data-target="#editModal">' . trans('master_sheet') . ': ' . $row->exam_term->name . '</a>&nbsp;&nbsp;';

            foreach ($row->exam_term->sequence as $seq) {
                $operate .= '<a href=' . route('view-seq-master-sheet', [$row->id, $seq->id]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button" data-id=' . $row->id . ' title="Class Report" data-toggle="modal" data-target="#editModal">' . trans('master_sheet') . ': ' . $seq->name . '</a>&nbsp;&nbsp;';
            }

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['class_section'] = $row->class_section->full_name;
            $tempRow['action'] = $operate;
            // $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function annual_best_report()
    {
        if (!Auth::user()->can('class-report')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        return view('exams.annual_best_report');
    }

    public function list_class_groups(Request $request)
    {
        if (!Auth::user()->can('class-report')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        // Process each group of classes
        $class_names = ClassSchool::owner()->activeMediumOnly()->pluck('name', 'id')->toArray();

        $grouped = $this->class_grouper($class_names);
        $data = array();

        foreach ($grouped as $starting_name => $class_group) {
            $data[] = $starting_name;
        }

        $reports = [];
        $bulkData = array();
        $bulkData['total'] = count($data);
        $rows = array();
        $tempRow = array();
        $no = 1;

        foreach ($data as $name) {
            $operate = '<a href=' . url('annual_class_best_report', [$name]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button"  title="Best Report" data-toggle="modal" data-target="#editModal">' . trans('annual_best_report') . '</a>&nbsp;&nbsp;';
            $operate .= '<a href=' . url('annual_class_best_in_subject_report', [$name]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button"  title="Best Report" data-toggle="modal" data-target="#editModal">' . trans('best_in_subjects') . '</a>&nbsp;&nbsp;';

//            $tempRow['id'] = $no;
            $tempRow['no'] = $no++;
            $tempRow['class_group_name'] = $name;
            $tempRow['action'] = $operate;

            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);

    }

    public function list_class_groups_statistics()
    {
        if (!Auth::user()->can('class-report')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $groupsQuery = Group::owner()->with('classes.stream')->with('classes', function ($q) {
            $q->activeMediumOnly();
        });

        $groups = $groupsQuery->get()->pluck('name', 'id');

        $class_names = ClassSchool::owner()->activeMediumOnly()->pluck('name', 'id')->toArray();

        $data = array();

        $reports = [];
        $bulkData = array();
        $bulkData['total'] = count($groups) + 1;
        $rows = array();
        $tempRow = array();
        $no = 2;

        // create the first row
        $tempRow['no'] = 1;
        $tempRow['class_group_name'] = trans("overall_statistics");

        $actions = '<a href=' .route('students.list', ['print'=>'true', 'limit'=>1000000]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button" data-toggle="modal" data-target="#editModal">' . trans('list') . '</a>&nbsp;&nbsp;';
        $actions .= '<a href=' .  route('print_sex_stats', ['print'=>'true', 'limit'=>1000000]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button" data-toggle="modal" data-target="#editModal">' .  trans('print_sex_stats') . '</a>&nbsp;&nbsp;';
        $actions  .= '<a href=' . route('print_sex_age_stats', ['print'=>'true', 'limit'=>1000000]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button" data-toggle="modal" data-target="#editModal">' . trans('print_sex_age_stats') . '</a>&nbsp;&nbsp;';

        $tempRow['action'] = $actions;

        $rows[] = $tempRow;

        foreach ($groups as $id => $name) {
            $operate = '<a href=' . route('students.list_by_group', ['groupId' => $id, 'print' => 'true', 'limit' => 1000000]) . ' target="_blank" class="btn btn-xs my-1 btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button"  title="Best Report" data-toggle="modal" data-target="#editModal">' . trans('list') . '</a>&nbsp;&nbsp;';

            $operate .= '<a href=' . route('grouped_sex_stats', ['groupId' => $id, 'print' => 'true', 'limit' => 1000000]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button"  title="Best Report" data-toggle="modal" data-target="#editModal">' . trans('grouped_sex_stats') . '</a>&nbsp;&nbsp;';

            $operate .= '<a href=' . route('print_grouped_sex_age_stats', ['groupId' => $id, 'print' => 'true', 'limit' => 1000000]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button"  title="Best Report" data-toggle="modal" data-target="#editModal">' . trans('grouped_sex_age_stats') . '</a>&nbsp;&nbsp;';

            $operate .= '<a href=' . route('group-new-students', ['groupId' => $id, 'print' => true]) . ' target="_blank" class="btn btn-xs btn-gradient-primary btn-rounded btn-sm btn-outline-primary btn-icon edit-data default-button" data-toggle="modal" data-target="#editModal">' . trans('new_students') . '</a>&nbsp;&nbsp;';

//            $tempRow['id'] = $no;
            $tempRow['no'] = $no++;
            $tempRow['class_group_name'] = $name;
            $tempRow['action'] = $operate;

            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function list_annual_best_in_subject_reports($group_name)
    {
        if (!Auth::user()->can('class-report')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $class_names = ClassSchool::owner()->activeMediumOnly()->pluck('name', 'id')->toArray();

        $class_groups = $this->class_grouper($class_names);

        $subjectIds = Subject::Owner()->activeMediumOnly()->get()->pluck('id', 'name')->toArray();

        $data = array();

        foreach ($class_groups as $starting_name => $class_group) {

            $annual_report_ids = AnnualReport::query()->where('session_year_id', getSettings('session_year')['session_year'])
                ->whereHas('class_section.class', function ($q) use ($class_group) {
                    $q->Owner()->activeMediumOnly()->whereIn('id', $class_group);
                })->get()->pluck('id')->toArray();

            $temp = array();

            foreach ($subjectIds as $name => $subject_id) {

                $annualSubjectReport = AnnualSubjectReport::with(['student', 'subject', 'student.user'])
                    ->orderByDesc('subject_avg')
                    ->whereIn('annual_report_id', $annual_report_ids)
                    ->where('subject_id', $subject_id)
                    ->limit(5)
                    ->get();

                if (count($annualSubjectReport) > 0) {
                    $temp[$name] = $annualSubjectReport;
                }

            }

            $data[$starting_name] = $temp;
        }


        $pdf = ExamPrints::getInstance(get_center_id(), 'P');

        $pdf->printBestInSubjectList($group_name, $data[$group_name]);

        return response(
            $pdf->Output('', trans('annual_best_subject_report_for') . $group_name . ".pdf"),
            200,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }

    public function list_annual_best_reports($group_name)
    {
        if (!Auth::user()->can('class-report')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        // Process each group of classes
        $class_names = ClassSchool::owner()->activeMediumOnly()->pluck('name', 'id')->toArray();

        $grouped = $this->class_grouper($class_names);

        $best_students = array();
        $best_boys = array();
        $best_girls = array();

        foreach ($grouped as $starting_name => $class_group) {

            $annual_report_ids = AnnualReport::query()->where('session_year_id', getSettings('session_year')['session_year'])
                ->whereHas('class_section.class', function ($q) use ($class_group) {
                    $q->Owner()->activeMediumOnly()->whereIn('id', $class_group)->orderByDesc('avg')->limit(10);
                })->get()->pluck('id')->toArray();

            $annualClassDetails = AnnualClassDetails::query()->with(['student', 'class_section'])->whereIn('annual_report_id', $annual_report_ids)
                ->orderByDesc('avg');

            $annualClassDetailsBestStudent = $annualClassDetails->limit(5)->get();

            $annualClassDetailsBestBoys = $annualClassDetails->whereHas('student.user', function ($query) {
                $query->whereIn('gender', ['Male', 'male', 'M']);
            })->limit(5)->get();

            $annualClassDetailsBestGirls = AnnualClassDetails::query()->with(['student', 'class_section'])->whereIn('annual_report_id', $annual_report_ids)
                ->orderByDesc('avg')->whereHas('student.user', function ($query) {
                    $query->whereIn('gender', ['Female', 'female', 'F']);
                })->limit(5)->get();

            $best_students[$starting_name] = $annualClassDetailsBestStudent;
            $best_boys[$starting_name] = $annualClassDetailsBestBoys;
            $best_girls[$starting_name] = $annualClassDetailsBestGirls;
        }


        $best_students = $best_students[$group_name];
        $best_boys = $best_boys[$group_name];
        $best_girls = $best_girls[$group_name];

        $pdf = ExamPrints::getInstance(get_center_id(), 'P');

        $pdf->printBestStudentGroupList($group_name, $best_students, $best_boys, $best_girls);

        return response(
            $pdf->Output('', trans('annual_best_report_for') . $group_name . ".pdf"),
            200,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }

    public function renameClassSection($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $sections = Section::owner()->get();

        $class = ClassSchool::query()->findOrFail($id);

        $currentSections = $class->sections;

        return view('class.section-transfer', compact('sections', 'currentSections', 'class'));
    }

    public function transferClassSection(Request $request) {
        try {

            $fromSectionId = $request->from_section_id;
            $toSectionId = $request->to_section_id;
            $classId = $request->class_id;

            DB::beginTransaction();

            // rename the class section and put the class in the new class section.
            $classSection = ClassSection::where([
                'class_id' => $classId,
                'section_id' => $fromSectionId
            ])->firstOrFail();

            $classSection->update([
                'section_id' => $toSectionId
            ]);

            $classSection->save();

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => $throwable->getMessage(),
            ]);
        }
    }

    public function assignClassReport() {
        return view('class.assign-class-report');
    }

}
