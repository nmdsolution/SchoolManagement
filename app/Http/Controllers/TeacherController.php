<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use TypeError;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Str;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use App\Models\CenterTeacher;
use Illuminate\Http\Response;
use App\Models\SubjectTeacher;
use App\Imports\TeachersImport;
use App\Printing\TeacherPrints;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct() {
        $this->folder = 'teachers';
    }

    /**
     * @throws Exception
     */
    public function index() {
        if (!Auth::user()->can('teacher-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $bytes = random_bytes(3);
        $email = bin2hex($bytes) . "@yadiko.com";
        return view('teacher.index', compact('email'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) {
        if (!Auth::user()->can('teacher-create') || !Auth::user()->can('teacher-edit')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'first_name'                => 'required',
            // 'last_name'                 => 'required',
            'gender'                    => 'required',
            'email'                     => 'required|unique:users,email',
            // 'mobile'                    => 'required',
            'dob'                       => 'required',
            // 'qualification'             => 'required',
            'qualification_certificate' => 'mimes:jpeg,png,jpg,doc,pdf|nullable',
            'image'                     => 'mimes:jpeg,png,jpg',
            // 'current_address'           => 'required',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        try {
            DB::beginTransaction();
            if (is_numeric($request->email)) {
                // If Teacher already exists in center then Update Otherwise Assign it to the new center
                $teacher = Teacher::find($request->email);
                CenterTeacher::updateOrCreate([
                    'center_id'  => Auth::user()->center->id,
                    'teacher_id' => $teacher->id,
                    'user_id'    => $teacher->user_id,
                ], [
                    'center_id'  => Auth::user()->center->id,
                    'teacher_id' => $request->email,
                    'user_id'    => $teacher->user_id,
                ]);
            } else {
                //Create Entry in User Table
                $teacher_plain_text_password = str_replace('-', '', date('d-m-Y', strtotime($request->dob)));
                $user = new User();
                $user->image = $request->hasFile('image') ? $request->file('image')->store($this->folder, 'public') : "";
                $user->password = Hash::make($teacher_plain_text_password);
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->gender = $request->gender;
                $user->current_address = $request->current_address;
                $user->permanent_address = $request->permanent_address;
                $user->email = $request->email;
                $user->mobile = $request->mobile;
                $user->dob = date('Y-m-d', strtotime($request->dob));
                $user->save();
                $TeacherRole = Role::where('name', 'Teacher')->first();
                $user->assignRole([$TeacherRole->id]);

                // Create Entry in Teacher Table
                $teacher = new Teacher();
                $teacher->user_id = $user->id;
                $teacher->qualification = $request->qualification;
                $teacher->qualification_certificate = $request->hasFile('qualification_certificate') ? $request->file('qualification_certificate')->store($this->folder, 'public') : "";
                $teacher->salary = $request->salary;
                $teacher->save();

                // Create Entry in Center Teachers table
                $center_teacher = new CenterTeacher();
                // $center_teacher->center_id = Auth::user()->center->id;
                $center_teacher->center_id = get_center_id();
                $center_teacher->teacher_id = $teacher->id;
                $center_teacher->user_id = $user->id;
                if ($request->grant_permission) {
                    $center_teacher->manage_student_parent = 1;
                }
                $center_teacher->save();

                $permissions = [
                    'student-create',
                    'student-list',
                    'student-edit',
                    'student-delete',
                    'parents-create',
                    'parents-list',
                    'parents-edit'
                ];
                if ($request->grant_permission) {
                    // $user->givePermissionTo($permissions);
                    $TeacherRole = Role::where('name', 'Manage Student & Parent')->first();
                    $user->assignRole([$TeacherRole->id]);
                } else {
                    $user->revokePermissionTo($permissions);
                }

//                $school_name = getSettings('school_name');
//                $data = [
//                    'subject'     => 'Welcome to ' . $school_name['school_name'],
//                    'name'        => $request->first_name,
//                    'email'       => $request->email,
//                    'password'    => $teacher_plain_text_password,
//                    'school_name' => $school_name['school_name']
//                ];

//                Mail::send('teacher.email', $data, static function ($message) use ($data) {
//                    $message->to($data['email'])->subject($data['subject']);
//                });
            }

            DB::commit();
            $response = [
                'error'   => false,
                'message' => trans('data_store_successfully')
            ];

        } catch (Throwable $e) {
            DB::rollback();
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred'),
                'data'    => $e
            );
//            }
        }
        return response()->json($response);
    }

    public function show(Request $request) {
        if (!Auth::user()->can('teacher-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $sql = Teacher::with('user')->Owner();

        if($request->get('print')){
            $list = $sql->get();

            $pdf = TeacherPrints::getInstance(Auth::user()->center->id, 'L');

            $pdf->teacherList($list);

            return response(
                $pdf->Output('', 'TEACHER_LIST.pdf'),
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

        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orwhere('user_id', 'LIKE', "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%$search%")
                            ->orwhere('last_name', 'LIKE', "%$search%")
                            ->orwhere('gender', 'LIKE', "%$search%")
                            ->orwhere('email', 'LIKE', "%$search%")
                            // ->orwhere('dob', 'LIKE', "%" . date('Y-m-d', strtotime($search)) . "%")
                            ->orwhere('qualification', 'LIKE', "%$search%")
                            ->orwhere('current_address', 'LIKE', "%$search%")
                            ->orwhere('permanent_address', 'LIKE', "%$search%");
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
        foreach ($res as $row) {
            $operate = '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" data-id=' . $row->id . ' data-url=' . url('teachers') . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" data-id=' . $row->id . ' data-user_id=' . $row->user_id . ' data-url=' . url('teachers', $row->user_id) . ' title="Delete"><i class="fa fa-trash"></i></a>';

            $data = getSettings('date_formate');

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['user_id'] = $row->user_id;
            $tempRow['first_name'] = $row->user->first_name;
            $tempRow['last_name'] = $row->user->last_name;
            $tempRow['gender'] = $row->user->gender;
            $tempRow['current_address'] = $row->user->current_address;
            $tempRow['permanent_address'] = $row->user->permanent_address;
            $tempRow['email'] = $row->user->email;
            $tempRow['dob'] = date($data['date_formate'], strtotime($row->user->dob));
            $tempRow['mobile'] = $row->user->mobile;
            $tempRow['image'] = $row->user->image;
            $tempRow['salary'] = $row->salary;
            $tempRow['manage_student_parent'] = $row->center_teacher->first()->manage_student_parent;
            $tempRow['qualification'] = $row->qualification;
            $tempRow['qualification_certificate'] = $row->qualification_certificate;
            // if ($row->user->can('student-create', 'student-list', 'student-edit', 'parents-create', 'parents-list', 'parents-edit')) {
            //     $tempRow['has_student_permissions'] = 1;
            // } else {
            //     $tempRow['has_student_permissions'] = 0;
            // }

            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function update(Request $request) {
        if (!Auth::user()->can('teacher-edit')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'first_name'                => 'required',
            // 'last_name'                 => 'required',
            'gender'                    => 'required',
            //            'email' => 'required|unique:users,email,' . $request->user_id,
            // 'mobile'                    => 'required',
            'dob'                       => 'required',
            // 'qualification'             => 'required',
            'qualification_certificate' => 'mimes:jpeg,png,jpg,doc,pdf|nullable',
            'image'                     => 'mimes:jpeg,png,jpg|nullable',
            // 'current_address'           => 'required',
            // 'permanent_address'         => 'required',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $user = User::find($request->user_id);
            if ($request->hasFile('image')) {
                if ($user->getRawOriginal('image') !== null && Storage::disk('public')->exists($user->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($user->getRawOriginal('image'));
                }
                $user->image = $request->hasFile('image') ? $request->file('image')->store($this->folder, 'public') : "";
            }


            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->gender = $request->gender;
            $user->current_address = $request->current_address;
            $user->permanent_address = $request->permanent_address;
            //            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->dob = date('Y-m-d', strtotime($request->dob));
            $user->save();

            $teacher = Teacher::find($request->id);
            //            dd($teacher->toArray());
            $teacher->user_id = $user->id;
            $teacher->qualification = $request->qualification;
            if ($request->hasFile('qualification_certificate')) {
                if ($teacher->getRawOriginal('qualification_certificate') !== null && Storage::disk('public')->exists($teacher->getRawOriginal('qualification_certificate'))) {
                    Storage::disk('public')->delete($teacher->getRawOriginal('qualification_certificate'));
                }
                $teacher->qualification_certificate = $request->hasFile('qualification_certificate') ? $request->file('qualification_certificate')->store($this->folder, 'public') : "";
            }
            $teacher->salary = $request->salary;
            $teacher->save();


            // Create Entry in Center Teachers table


            if ($request->grant_permission) {
                $center_teacher = CenterTeacher::where('teacher_id', $teacher->id)->where('center_id', get_center_id())->get()->first();
                $center_teacher->manage_student_parent = 1;
                $center_teacher->save();

                $TeacherRole = Role::where('name', 'Manage Student & Parent')->first();
                $teacher->user->assignRole([$TeacherRole->id]);

            } else {
                $center_teacher = CenterTeacher::where('teacher_id', $teacher->id)->where('center_id', get_center_id())->get()->first();
                $center_teacher->manage_student_parent = 0;
                $center_teacher->save();

                Auth::user()->removeRole('Manage Student & Parent');
            }


            $permissions = [
                'student-create',
                'student-list',
                'student-edit',
                'student-delete',
                'parents-create',
                'parents-list',
                'parents-edit'
            ];
            if ($request->edit_grant_permission) {
                // $user->givePermissionTo($permissions);
                $TeacherRole = Role::where('name', 'Manage Student & Parent')->first();
                $user->assignRole([$TeacherRole->id]);
            } else {
                $user->revokePermissionTo($permissions);
            }

            $response = [
                'error'   => false,
                'message' => trans('data_update_successfully')
            ];
        } catch (Throwable $e) {
            if ($e instanceof TypeError && Str::contains($e->getMessage(), [
                    'Mail',
                    'Mailer',
                    'MailManager'
                ])) {
                $response = array(
                    'warning' => true,
                    'error'   => false,
                    'message' => "Teacher Updated successfully. But Email not sent.",
                );
            } else {
                $response = array(
                    'error'   => true,
                    'message' => trans('error_occurred'),
                    'data'    => $e
                );
            }

        }
        return response()->json($response);
    }

    public function destroy($id) {
        if (!Auth::user()->can('teacher-delete')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {
            $teacher_id = Teacher::with('user')->find($id);

            //check whether the teacher exists in other table
            $subject_teacher = SubjectTeacher::owner()->where('teacher_id', $teacher_id->id)->count();
            $class_section = ClassSection::owner()->where('class_teacher_id', $teacher_id->id)->count();
            if ($subject_teacher || $class_section) {
                $response = array(
                    'error'   => true,
                    'message' => trans('cannot_delete_because_data_is_associated_with_other_data')
                );

            } else {
                $class_section_id = ClassSection::where('class_teacher_id', $teacher_id->id)->first();
                if ($class_section_id) {
                    $class_teacher = ClassSection::find($class_section_id);
                    $class_teacher->class_teacher_id = null;
                    $class_teacher->save();
                    //                    $teacher_id->user->revokePermissionTo('class-teacher');
                    $teacher_id->user->removeRole('Class Teacher');
                }
                CenterTeacher::where('center_id', Auth::user()->center->id)->where('teacher_id', $teacher_id->id)->delete();

                $response = [
                    'error'   => false,
                    'message' => trans('data_delete_successfully')
                ];
            }
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function search(Request $request): JsonResponse {
        $data = Teacher::whereHas('user', function ($query) use ($request) {
            $query->Where(function ($query) use ($request) {
                $query->orWhere('email', 'like', '%' . $request->search . '%');
                $query->orWhere('mobile', 'like', '%' . $request->search . '%');
                $query->orWhere('first_name', 'like', '%' . $request->search . '%');
                $query->orWhere('last_name', 'like', '%' . $request->search . '%');
            });
        })->with('user')->get();
        if (!empty($data)) {
            $response = [
                'error' => false,
                'data'  => $data
            ];
        } else {
            $response = [
                'error'   => true,
                'message' => trans('no_data_found')
            ];
        }
        return response()->json($response);
    }

    public function bulk_data() {

        return view('teacher.upload_bulk_data');
    }

    public function store_bulk_data(Request $request): Response|JsonResponse|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt'
        ]);

        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        $teacherImport = new TeachersImport();

        Excel::import($teacherImport, $request->file);

        $errors = $teacherImport->getMessage();

        if (!empty($errors)) {
            return response()->json([
                'error' => true,
                'message' => $errors
            ]);
        }

        return response([
            'error'   => false,
            'message' => trans('data_store_successfully')
        ]);
    }


    public function generateEmail() {
        $response = [
            'error'   => false,
            'message' => trans('Email Generated Successfully'),
            'email'   => bin2hex(random_bytes(3)) . "@yadiko.com"
        ];
        return response($response);
    }

    public function resetPasswordIndex() {
        if (!Auth::user()->can('teacher-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        return view('teacher.reset_password');
    }

    public function resetPasswordShow() {
        if (!Auth::user()->can('teacher-list')) {
            $response = array('message' => trans('no_permission_message'));
            return response()->json($response);
        }
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'DESC';

        if (isset($_GET['offset'])) $offset = $_GET['offset'];
        if (isset($_GET['limit'])) $limit = $_GET['limit'];

        if (isset($_GET['sort'])) $sort = $_GET['sort'];
        if (isset($_GET['order'])) $order = $_GET['order'];

        $sql = Teacher::owner()->with('user')->whereHas('user', function ($q) {
            $q->where('reset_request', 1);
        });
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")
                ->orwhere('users.email', 'LIKE', "%$search%")->orwhere('users.first_name', 'LIKE', "%$search%")->orwhere('users.last_name', 'LIKE', "%$search%")->orWhereRaw("concat(users.first_name,' ',users.last_name) LIKE '%" . $search . "%'");
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
            $operate = '<button class="btn btn-xs btn-gradient-primary btn-action btn-rounded btn-icon reset_password" data-id=' . $row->id . ' title="Reset-Password"><i class="fa fa-edit"></i></button>&nbsp;&nbsp;';

            $tempRow['id'] = $row->user->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->user->first_name . ' ' . $row->user->last_name;
            $tempRow['dob'] = $row->user->dob;
            $tempRow['email'] = $row->user->email;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

//    public function change_password(Request $request) {
//        if (!Auth::user()->can('teacher-list')) {
//            $response = array('message' => trans('no_permission_message'));
//            return response()->json($response);
//        }
//        try {
//            $dob = date('dmY', strtotime($request->dob));
//            $user = Teacher::owner()->with('user')->find($request->id);
//            $user->reset_request = 0;
//            $user->password = Hash::make($dob);
//            $user->save();
//
//            $response = [
//                'error'   => false,
//                'message' => trans('data_update_successfully')
//            ];
//        } catch (Throwable $e) {
//            $response = array(
//                'error'   => true,
//                'message' => trans('error_occurred')
//            );
//        }
//        return response()->json($response);
//    }
}
