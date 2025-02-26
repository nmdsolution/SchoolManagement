<?php

namespace App\Yadiko\Student\UserInterface\Htttp;

use Throwable;
use TypeError;
use App\Models\Students;
use App\Models\SessionYear;
use Illuminate\Support\Str;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use App\Printing\StudentPrints;
use App\Yadiko\Student\Application\DTO\BaseStudentHelper;
use Illuminate\Support\Facades\Auth;


class ListStudentController extends BaseStudentHelper
{

    public function show(Request $request)
    {
        if (!Auth::user()->can('student-list')) {
            $response = array('message' => trans('no_permission_message'));
            return response()->json($response);
        }
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = 'users.first_name';
        $order = $request->input('order', 'ASC');
        $studentStatus = $request->input('student_status', 1);
        $class_section_id = $request->input('class_id', '');

        $sessionYearId = SessionYear::owner()->select('id', 'name')->where('default', 1)->get()->first()->id;

        $sql = Students::with('user', 'class_section', 'father', 'mother', 'guardian', 'studentSessions')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->select('students.*', 'users.first_name', 'users.last_name')
            ->Owner()->ofTeacher()->whereHas('class_section.class', function ($q) {
                $q->activeMediumOnly();
            })->whereHas('studentSessions', function ($query) use ($class_section_id, $studentStatus, $sessionYearId) {
                $query->where('session_year_id', $sessionYearId)
                    ->where('active', $studentStatus)
                    ->when(Str::length($class_section_id) > 0, function ($query) use ($class_section_id) {
                        $query->where('class_section_id', $class_section_id);
                    });
            });


        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($query) use ($search) {
                $query->where('students.id', 'LIKE', "%$search%")
                    ->orWhere('user_id', 'LIKE', "%$search%")
                    ->orWhere('class_section_id', 'LIKE', "%$search%")
                    ->orWhere('admission_no', 'LIKE', "%$search%")
                    ->orWhere('roll_number', 'LIKE', "%$search%")
                    ->orWhere('admission_date', 'LIKE', date('Y-m-d', strtotime("%$search%")))
                    ->orWhere('is_new_admission', 'LIKE', "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%$search%")->orwhere('last_name', 'LIKE', "%$search%")->orwhere('email', 'LIKE', "%$search%")->orwhere('dob', 'LIKE', "%$search%");
                    })->orWhereHas('father', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%$search%")->orwhere('last_name', 'LIKE', "%$search%")->orwhere('email', 'LIKE', "%$search%")->orwhere('mobile', 'LIKE', "%$search%")->orwhere('occupation', 'LIKE', "%$search%")->orwhere('dob', 'LIKE', "%$search%");
                    })->orWhereHas('mother', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%$search%")->orwhere('last_name', 'LIKE', "%$search%")->orwhere('email', 'LIKE', "%$search%")->orwhere('mobile', 'LIKE', "%$search%")->orwhere('occupation', 'LIKE', "%$search%")->orwhere('dob', 'LIKE', "%$search%");
                    });
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
            $operate = '';
            if (Auth::user()->can('student-edit')) {
                $operate .= '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" data-id=' . $row->id . ' data-url=' . url('students') . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            }

            if (Auth::user()->can('student-delete')) {
                $operate .= '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" data-id=' . $row->id . ' data-user_id=' . $row->user_id . ' data-url=' . url('students', $row->user_id) . ' title="Delete"><i class="fa fa-trash"></i></a>';
            }

            $currentClassSection = $row->studentSessions()->where('session_year_id', $sessionYearId)->first()->class_section;

            $values = json_decode($row->dynamic_field_values, true);

            $tempRow['born_at'] = $row->user->born_at;
            $tempRow['minisec_matricule'] = $row->minisec_matricule;
            $tempRow['status'] = implode(', ', json_decode($row->status, true) ?? ['Not applicable']);


//            $selected_student = '<input type="checkbox" class="selected_student"  name="selected_students" value=' . $row->id . '>';
//            $tempRow['chk'] = $selected_student;
            $tempRow['id'] = $row->user->id;
            $tempRow['no'] = $no++;
            $tempRow['user_id'] = $row->user_id;
            $tempRow['first_name'] = $row->user->first_name;
            $tempRow['last_name'] = $row->user->last_name;
            $tempRow['email'] = $row->user->email;
            $tempRow['gender'] = $row->user->gender;
            $tempRow['dob'] = date($data['date_formate'], strtotime($row->user->dob));
            $tempRow['image'] = $row->user->image;
            $tempRow['image_link'] = $row->user->image;
            $tempRow['class_section_id'] = $currentClassSection->id;
            $tempRow['class_section_name'] = $currentClassSection->name;
            $tempRow['admission_no'] = $row->admission_no;
            $tempRow['roll_number'] = $row->roll_number;
            $tempRow['nationality'] = $row->nationality;
            $tempRow['repeater'] = $row->repeater;
            $tempRow['admission_date'] = date($data['date_formate'], strtotime($row->admission_date));
            $tempRow['is_new_admission'] = $row->is_new_admission;
            $tempRow['dynamic_data_field'] = json_decode($row->dynamic_field_values);


            if (!empty($row->father)) {
                //Father Data
                $tempRow['father_id'] = $row->father->id;
                $tempRow['father_email'] = $row->father->email;
                $tempRow['father_first_name'] = $row->father->first_name;
                $tempRow['father_last_name'] = $row->father->last_name;
                $tempRow['father_mobile'] = $row->father->mobile;
                $tempRow['father_dob'] = $row->father->dob;
                $tempRow['father_occupation'] = $row->father->occupation;
                $tempRow['father_image'] = $row->father->image;
                $tempRow['father_image_link'] = $row->father->image;
            } else {
                $tempRow['father_id'] = '';
                $tempRow['father_email'] = '';
                $tempRow['father_first_name'] = '';
                $tempRow['father_last_name'] = '';
                $tempRow['father_mobile'] = '';
                $tempRow['father_dob'] = '';
                $tempRow['father_occupation'] = '';
                $tempRow['father_image'] = '';
                $tempRow['father_image_link'] = '';
            }


            if (!empty($row->mother)) {
                //Mother Data
                $tempRow['mother_id'] = $row->mother->id;
                $tempRow['mother_email'] = $row->mother->email;
                $tempRow['mother_first_name'] = $row->mother->first_name;
                $tempRow['mother_last_name'] = $row->mother->last_name;
                $tempRow['mother_mobile'] = $row->mother->mobile;
                $tempRow['mother_dob'] = $row->mother->dob;
                $tempRow['mother_occupation'] = $row->mother->occupation;
                $tempRow['mother_image'] = $row->mother->image;
                $tempRow['mother_image_link'] = $row->mother->image;
            } else {
                $tempRow['mother_id'] = '';
                $tempRow['mother_email'] = '';
                $tempRow['mother_first_name'] = '';
                $tempRow['mother_last_name'] = '';
                $tempRow['mother_mobile'] = '';
                $tempRow['mother_dob'] = '';
                $tempRow['mother_occupation'] = '';
                $tempRow['mother_image'] = '';
                $tempRow['mother_image_link'] = '';
            }


            if (!empty($row->guardian)) {
                //Father Data
                $tempRow['guardian_id'] = $row->guardian->id;
                $tempRow['guardian_email'] = $row->guardian->email;
                $tempRow['guardian_first_name'] = $row->guardian->first_name;
                $tempRow['guardian_last_name'] = $row->guardian->last_name;
                $tempRow['guardian_mobile'] = $row->guardian->mobile;
                $tempRow['guardian_dob'] = $row->guardian->dob;
                $tempRow['guardian_occupation'] = $row->guardian->occupation;
                $tempRow['guardian_image'] = $row->guardian->image;
                $tempRow['guardian_image_link'] = $row->guardian->image;
            } else {
                $tempRow['guardian_id'] = '';
                $tempRow['guardian_email'] = '';
                $tempRow['guardian_first_name'] = '';
                $tempRow['guardian_last_name'] = '';
                $tempRow['guardian_mobile'] = '';
                $tempRow['guardian_dob'] = '';
                $tempRow['guardian_occupation'] = '';
                $tempRow['guardian_image'] = '';
                $tempRow['guardian_image_link'] = '';
            }

            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        if (request()->get('print')) {
            $class_section = ClassSection::query()->find(request()->get('class_id'));

            $pdf = StudentPrints::getInstance(get_center_id());

            return $pdf->printStudentList($rows, $class_section);
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

}