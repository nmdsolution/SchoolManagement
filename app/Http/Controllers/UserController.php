<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\ModelHasRole;
use App\Models\PasswordReset;
use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth::user()->can('user-list')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $roles = Role::where('center_id', get_center_id())->whereNotIn('name', ['Super Admin', 'Center', 'Teacher', 'Parent', 'Student', 'Class Teacher', 'Super Teacher'])->get()->pluck('name', 'id');

        foreach ($roles as $key => $role) {
            $roles[$key] = get_role_name($key);
        }

        $data = User::doesnthave('student')->doesnthave('parent')->doesnthave('teacher')->orderBy('id', 'DESC')->get()->first();

        return view('users.index', compact('data', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('user-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $roles = Role::pluck('name', 'name')->all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('user-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'gender' => 'required',
            'current_address' => 'required',
            'permanent_address' => 'required',
            'dob' => 'required',
            'role_id' => 'required'
        ]);

        if ($request->edit_id) {
            $staff = Staff::find($request->edit_id);
            $user = User::find($staff->user_id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->gender = $request->gender;

            if ($request->hasFile('image')) {
                if (Storage::disk('public')->exists($user->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($user->getRawOriginal('image'));
                }
                $user->image = $request->file('image')->store('users', 'public');
            }

            $user->current_address = $request->current_address;
            $user->permanent_address = $request->permanent_address;
            $user->dob = date('Y-m-d', strtotime($request->dob));
            $user_plaintext_password = str_replace('-', '', date('d-m-Y', strtotime($request->dob)));

            $user->password = Hash::make($user_plaintext_password);

            $user->save();

            $total_roles = $user->getRoleNames();
            foreach ($total_roles as $key => $roles) {
                $user->removeRole($roles);
            }

            StaffRole::where('staff_id', $staff->id)->delete();
            foreach ($request->role_id as $key => $role) {
                $staff_role = new StaffRole();
                $staff_role->user_id = $user->id;
                $staff_role->staff_id = $staff->id;
                $staff_role->role_id = $role;
                $staff_role->save();

                $role = Role::find($role);
                $user->assignRole($role->name);
            }
            $response = [
                'error' => false,
                'message' => trans('data_update_successfully')
            ];
        } else {
            // New User
            // Create user first
            $user = User::where('mobile', $request->mobile)->get()->first();
            // $user = User::find($request->mobile);
            if (!$user) {
                $user = new User();
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                $user->mobile = $request->mobile;
                $user->gender = $request->gender;
                if ($request->hasFile('image')) {
                    $user->image = $request->file('image')->store('users', 'public');
                }
                $user->current_address = $request->current_address;
                $user->permanent_address = $request->permanent_address;
                $user->dob = date('Y-m-d', strtotime($request->dob));
                $user_plaintext_password = str_replace('-', '', date('d-m-Y', strtotime($request->dob)));
                $user->password = Hash::make($user_plaintext_password);
                $user->save();
            }

            // Create staff
            if (Auth::user()->hasRole('Super Admin')) {
                $staff = new Staff();
                $staff->user_id = $user->id;
                $staff->save();
            } else if (Auth::user()->hasRole('Center')) {
                $staff = new Staff();
                $staff->user_id = $user->id;
                $staff->center_id = Auth::user()->center->id;
                $staff->save();
            } else if (Auth::user()->staff->first()) {
                if (Session()->get('center_id') != -1) {
                    $staff = new Staff();
                    $staff->user_id = $user->id;
                    $staff->center_id = Session()->get('center_id');
                    $staff->save();
                } else {
                    $staff = new Staff();
                    $staff->user_id = $user->id;
                    $staff->center_id = Session()->get('center_id');
                    $staff->save();
                }
            }

            // Create user roles
            foreach ($request->role_id as $key => $role) {
                $staff_role = new StaffRole();
                $staff_role->user_id = $user->id;
                $staff_role->staff_id = $staff->id;
                $staff_role->role_id = $role;
                $staff_role->save();

                $role = Role::find($role);
                $user->assignRole($role->name);
            }
            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        }



        // $user = User::create($input);
        // $user->assignRole($request->input('roles'));


        // return redirect()->back()->with('success',$response['message']);

        return response()->json($response);
        // return redirect()->route('users.index') ->with('success',trans('data_store_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->can('user-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('user-edit')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $user = User::find($id)->makeHidden(['last_name']);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if (!Auth::user()->can('user-edit')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $user = User::find($id);
        $user->first_name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        // $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        $user->assignRole($request->roles);
        // $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', trans('data_update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('user-delete')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }


        $staff = Staff::find($id);

        if ($staff->user_id == Auth::user()->id) {
            $response = [
                'error' => true,
                'message' => 'Not allowed'
            ];
        } else {
            $user = User::find($staff->user_id);
            $total_roles = $user->getRoleNames();
            foreach ($total_roles as $key => $roles) {
                $user->removeRole($roles);
            }
            // $staff->delete();


            $user_id = $staff->user_id;


            $teacher_user = User::find($staff->user_id)->teacher()->exists();
            $parent_user = User::find($staff->user_id)->parent()->exists();
            $guardian_user = User::find($staff->user_id)->guardian()->exists();
            $staff->delete();
            $staff_user = User::find($user_id)->staff()->exists();

            if ($staff_user != 1 and $teacher_user != 1 and $parent_user != 1 and $guardian_user != 1) {
                ModelHasRole::where('model_id', $user_id)->delete();
                User::find($user_id)->delete();
            }

            $response = [
                'error' => false,
                'message' => trans('data_delete_successfully')
            ];
        }

        return response($response);
        // return redirect()->route('users.index')
        //     ->with('success', trans('data_delete_successfully'));
    }

    public function user_list()
    {
        if (!Auth::user()->can('user-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
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

        $sql = Staff::Owner()->with('user');

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%$search%")
                        ->orWhere('last_name', 'LIKE', "%$search%")
                        ->orWhere('mobile', 'LIKE', "%$search%")
                        ->orWhere('email', 'LIKE', "%$search%")
                        ->orWhere('gender', 'LIKE', "%$search%")
                        ->orWhere('current_address', 'LIKE', "%$search%");
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
            $operate = '<a href=' . route('users.edit', $row->id) . ' class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id=' . $row->id . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a href=' . route('users.destroy', $row->id) . ' class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata delete-form" data-id=' . $row->id . '><i class="fa fa-trash"></i></a>';

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['name'] = $row->user->full_name;
            $tempRow['first_name'] = $row->user->first_name;
            $tempRow['last_name'] = $row->user->getRawOriginal('last_name');
            $tempRow['permanent_address'] = $row->user->permanent_address;
            $tempRow['dob'] = date('d-m-Y', strtotime($row->user->dob));

            $tempRow['email'] = $row->user->email;
            $tempRow['mobile'] = $row->user->mobile;
            $tempRow['gender'] = $row->user->gender;
            $tempRow['current_address'] = $row->user->current_address;
            $tempRow['image'] = $row->user->image;
            $tempRow['role'] = get_pluck_role_name($row->staff_role->pluck('role.id'));
            $tempRow['role_id'] = $row->staff_role->pluck('role.id');
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function search(Request $request)
    {
        if ($request->type == "user") {
            $user = User::with('staff_role')->where(function ($query) use ($request) {
                $query->orWhere('mobile', 'like', '%' . $request->search . '%');
            })->get();
        }

        if (!empty($user)) {
            $response = [
                'error' => false,
                'data' => $user
            ];
        } else {
            $response = [
                'error' => true,
                'message' => trans('no_data_found')
            ];
        }
        return response()->json($response);
    }

    public function profile()
    {
        $user = Auth::user()->append('birth_date');

        // Center Admin

        if (Auth::user()->hasRole('Center')) {

            $user = Auth::user()->load('center');
            return view('users.center_profile', compact('user'));
        }

        if (Auth::user()->teacher) {

            $user = Auth::user()->load('teacher');
        }

        // Super Admin, Teacher, Staff user


        return view('users.profile', compact('user'));
    }

    public function update_profile(Request $request, $id)
    {

        // $validator = Validator::make($request->all(), [
        //     'first_name' => 'required',
        //     'last_name' => 'required',
        //     'email' => 'required',
        //     'mobile' => 'required',
        //     'birth_date' => 'required',
        // ]);

        // if (Auth::user()->hasRole('Center')) {
        //     $validator = Validator::make($request->all(), [
        //          'center.*' => 'required',
        //     ]);
        // }

        // if ($validator->fails()) {
        //     $response = array(
        //         'error'   => true,
        //         'message' => $validator->errors()->first()
        //     );
        //     return response()->json($response);
        // }

        $user = User::find($id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->dob = date('Y-m-d', strtotime($request->birth_date));
        if ($request->hasFile('image')) {
            if ($user->getRawOriginal('image')) {
                if (Storage::disk('public')->exists($user->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($user->getRawOriginal('image'));
                }
            }
            $user->image = $request->file('image')->store('user', 'public');
        }
        $user->save();

        if (Auth::user()->hasRole('Center')) {
            $center = Center::find(Auth::user()->center->id);
            $center->name = $request->center['name'];
            $center->support_email = $request->center['support_email'];
            $center->support_contact = $request->center['support_contact'];
            $center->tagline = $request->center['tagline'];
            $center->address = $request->center['address'];

            if ($request->hasFile('logo')) {
                if ($center->getRawOriginal('logo')) {
                    if (Storage::disk('public')->exists($center->getRawOriginal('logo'))) {
                        Storage::disk('public')->delete($center->getRawOriginal('logo'));
                    }
                }
                $center->logo = $request->file('logo')->store('logo', 'public');
            }
            $center->save();
        }

        if (Auth::user()->teacher) {
            $teacher = Teacher::find(Auth::user()->teacher->id);
            $teacher->contact_status = $request->teacher ? $request->teacher['contact_status'] : 0;
            $teacher->save();
        }

        $response = [
            'error' => false,
            'message' => 'Data saved successfully'
        ];

        return response($response);
    }

    public function change_password()
    {

        return view('users.change_password');
    }

    public function update_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        $user = User::find(Auth::user()->id);

        if (Hash::check($request->current_password, $user->password)) {

            $user->password = Hash::make($request->password);
            $user->save();
            $response = [
                'error' => false,
                'message' => 'Password update successfully'
            ];
        } else {
            $response = [
                'error' => true,
                'message' => 'Invalid current password'
            ];
        }

        return response($response);
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        try {
            $user = User::where('email', $request->email)->first();

            // Update reset request status
            $user->reset_request = 1;
            $user->save();

            $response = [
                'error' => false,
                'message' => 'Password Reset Request Sent Successfully'
            ];
            return response()->json($response);
        } catch (\Throwable $e) {
            $response = [
                'error' => true,
                'message' => 'Invalid email'
            ];
            logger($e->getMessage());
            return response()->json($response);
        }
    }

    public function set_new_password(Request $request, $token)
    {

        return view('auth.passwords.set_new_password', compact('token'));
    }

    public function store_new_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            $response = [
                'error' => true,
                'message' => $validator->errors()->first()
            ];

            return response()->json($response);
        }


        $password_reset = PasswordReset::where('email', $request->email)->where('token', $request->token)->first();
        if (!$password_reset) {
            $response = [
                'error' => true,
                'message' => 'Invalid token or email!'
            ];
            return response($response);
        }

        $user = User::where('email', $request->email)->get()->first();
        $user->password = Hash::make($request->password);
        $user->save();

        $password_reset = PasswordReset::where('email', $request->email)->where('token', $request->token)->delete();

        $response = [
            'error' => false,
            'message' => 'Your password has been reset successfully'
        ];

        return response($response);
    }
}
