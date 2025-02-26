<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RoleHasPermission;
use App\Models\StaffRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // function __construct()
    // {
    //     $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
    //     $this->middleware('permission:role-create', ['only' => ['create','store']]);
    //     $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
    //     $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth::user()->can('role-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        // $roles = Role::orderBy('id','DESC')->paginate(5);
        // return view('roles.index',compact('roles'))
        // ->with('i', ($request->input('page', 1) - 1) * 5);



        if (Auth::user()->hasRole('Super Admin')) {
            $roles = Role::where('center_id', NULL)->get();
        } else if (Auth::user()->hasRole('Center')) {
            $roles = Role::where('center_id', Auth::user()->center->id)->orWhereIn('name', ['Center', 'Teacher', 'Student', 'Parent', 'Class Teacher','Manage Student and Parent'])->get();
        } else if (Auth::user()->staff->first()) {
            $center_id = session()->get('center_id');
            if ($center_id != -1) {
                $roles = Role::where('center_id', $center_id)->orWhereIn('name', ['Center', 'Teacher', 'Student', 'Parent', 'Class Teacher','Manage Student and Parent'])->whereNot('name', ['Center', 'Super Admin'])->get();
            } else {
                $roles = Role::where('center_id', NULL)->whereNotIn('name', ['Center', 'Super Admin'])->get();
            }
        }

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('role-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        if (Auth::user()->hasRole('Super Admin')) {
            $permission = Permission::where('type', 1)->get();
        } else if (Auth::user()->hasRole('Center')) {
            $permission = Permission::where('type', 0)->get();
        } else if (Auth::user()->staff->first()) {
            $permission = Auth::user()->getAllPermissions();
        }

        // return Auth::user()->getAllPermissions();


        // return 1;
        // return $permission = Permission::get();
        return view('roles.create', compact('permission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('role-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $get_center_id = get_center_id();

        // $validator = $this->validate($request, [
        //     'name' => 'required',
        //     'permission' => 'required',
        //     'name' => Rule::unique('roles')->where('name',$request->name)->where('guard_name','web')->where('center_id',$get_center_id)
        // ]);
        $role_name = get_center_id() . '#' . $request->name;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'permission' => 'required',

        ]);

        $role = Role::where('name', $role_name)->where('center_id', get_center_id())->where('medium_id', getCurrentMedium()->id)->where('guard_name', 'web')->get()->first();
        if ($role) {
            $response = array(
                'error' => true,
                'message' => 'The role name has already been taken.',
                'code' => 102,
            );
            return redirect()->back()->with('error', $response['message'])->withInput();
            return response()->json($response);
            
        }

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return redirect()->back()->with('error', $response['message']);
            return response()->json($response);
        }



        $role_name = get_center_id() . '#' . $request->name;
        if (Auth::user()->hasRole('Super Admin')) {
            $role = Role::create(['name' => $role_name, 'center_id' => NULL, 'is_default' => 0, 'guard_name' => 'web']);
        } else if (Auth::user()->hasRole('Center')) {
            $role = Role::create(['name' => $role_name, 'center_id' => Auth::user()->center->id, 'is_default' => 0, 'guard_name' => 'web', 'medium_id' => getCurrentMedium()->id]);
        } else if (Auth::user()->staff->first()) {
            if (Session()->get('center_id') != -1) {
                $role = Role::create(['name' => $role_name, 'center_id' => Session()->get('center_id'), 'is_default' => 0, 'guard_name' => 'web', 'medium_id' => getCurrentMedium()->id]);
            } else {
                $role = Role::create(['name' => $role_name, 'center_id' => NULL, 'is_default' => 0, 'guard_name' => 'web', 'medium_id' => getCurrentMedium()->id]);
            }
        } else if (Auth::user()->hasRole('Teacher')) {
            $role = Role::create(['name' => $role_name, 'center_id' => Session()->get('center_id'), 'is_default' => 0, 'guard_name' => 'web', 'medium_id' => getCurrentMedium()->id]);
        }
        // $role = Role::create(['name' => $request->input('name')]);
        // $role->syncPermissions($request->input('permission'));

        foreach ($request->permission as $key => $permission) {
            $role_has_permission = new RoleHasPermission();
            $role_has_permission->permission_id = $permission;
            $role_has_permission->role_id = $role->id;
            $role_has_permission->medium_id = getCurrentMedium()->id;
            $role_has_permission->save();
        }

        return redirect()->route('roles.index')
            ->with('success', trans('data_store_successfully'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->can('role-list')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get();

        return view('roles.show', compact('role', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->can('role-edit')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $role = Role::find($id);
        // $permission = Permission::get();
        if (Auth::user()->hasRole('Super Admin')) {
            $permission = Permission::where('type', 1)->get();
        } else if (Auth::user()->hasRole('Center')) {
            $permission = Permission::where('type', 0)->get();
        } else if (Auth::user()->staff->first()) {
            $permission = Auth::user()->getAllPermissions();
        }
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('roles.edit', compact('role', 'permission', 'rolePermissions'));
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
        if (!Auth::user()->can('role-edit')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        // $this->validate($request, [
        //     'name' => 'required',
        //     'permission' => 'required',
        // ]);

        $get_center_id = get_center_id();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'permission' => 'required',
            'name' => Rule::unique('roles')->whereNot('name', $request->name)->whereNot('guard_name', 'web')->whereNot('center_id', $get_center_id)->whereNot('medium_id', getCurrentMedium()->id)
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
                'code' => 102,
            );
            return redirect()->back()->with('error', $response['message']);
            return response()->json($response);
        }

        $role_name = get_center_id() . '#' . $request->name;
        $role = Role::find($id);
        // $role->name = $role_name;
        $role->save();
        // $role->syncPermissions($request->input('permission'));

        RoleHasPermission::where('role_id', $role->id)->delete();

        foreach ($request->permission as $key => $permission) {
            $role_has_permission = new RoleHasPermission();
            $role_has_permission->permission_id = $permission;
            $role_has_permission->role_id = $role->id;
            $role_has_permission->medium_id = getCurrentMedium()->id;
            $role_has_permission->save();
        }

        return redirect()->route('roles.index')
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
        if (!Auth::user()->can('role-delete')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $user_role = StaffRole::where('role_id', $id)->where('user_id', Auth::user()->id)->get()->first();
        if ($user_role) {
            return redirect()->route('roles.index')
                ->with('error', 'Not Allowed');
        } else {
            DB::table("roles")->where('id', $id)->delete();
            return redirect()->route('roles.index')
                ->with('success', trans('data_delete_successfully'));
        }
    }

    public function role_list()
    {
        if (!Auth::user()->can('role-list')) {
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
        $sql = Role::where('center_id',get_center_id())->where('medium_id', getCurrentMedium()->id)->orWhereIn('name', ['Center', 'Student', 'Parent', 'Teacher', 'Class Teacher','Manage Student & Parent']);
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orwhere('name', 'LIKE', "%$search%");
            })->where('center_id',get_center_id());
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
            $tempRow['name'] = __(get_role_name($row->id));
            $tempRow['is_default'] = $row->is_default;

            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }
}
