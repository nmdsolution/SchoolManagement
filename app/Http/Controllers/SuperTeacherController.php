<?php

namespace App\Http\Controllers;

use Throwable;
// use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SuperTeacherController extends Controller
{
    //
    public function index()
    {
        if(!Auth::user()->can('super-teacher-list')){
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        
        return response(view('super_teacher.index'));
    }

    public function create(Request $request)
    {
        if(!Auth::user()->can('super-teacher-create') || !Auth::user()->can('super-teacher-edit'))
        {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $validator=Validator::make($request->all(),[
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|unique:users|email',
            'mobile'=>'required',
            'dob'=>'required',
            'gender'=>'required',
            'image'=>'required',
            'current_address'=>'required',
            'permanent_address'=>'required',
        ]);
        
        if ($validator->fails()) {
          
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        $superteacher_plain_text_password = str_replace('-', '', date('d-m-Y', strtotime($request->dob)));
        $user= new User();
        $user->first_name=$request->first_name;
        $user->last_name=$request->last_name;
        $user->email=$request->email;
        $user->mobile=$request->mobile;
        $user->dob = date('Y-m-d', strtotime($request->dob));
        $user->gender=$request->gender;
        $user->image = $request->file('image')->store('superteacher', 'public');
        $user->password = Hash::make($superteacher_plain_text_password);
        $user->current_address=$request->current_address;
        $user->permanent_address=$request->permanent_address;
        $user->save();
        $SuperTeacherRole=Role::where('name','Super Teacher')->first();
        $user->assignRole([$SuperTeacherRole->id]);

        // $super_teacher=new SuperTeacher();
        // $super_teacher->user_id=$user->id;
        // $super_teacher->save();


        $response = [
            'error' => false,
            'message' => trans('data_store_successfully')
        ];
        return response()->json($response);
    }
    public function show()
    {
        if(!Auth::user()->can('super-teacher-list')){
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
        return response(view('super_teacher.index'));
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
        
        $sql=User::whereHas("roles", function($q){
            $q->where("name","Super Teacher");  
        });
        // dd($sql);
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orwhere('first_name', 'LIKE', "%$search%")
                    ->orwhere('last_name', 'LIKE', "%$search%")
                    ->orwhere('gender', 'LIKE', "%$search%")
                    ->orwhere('email', 'LIKE', "%$search%")
                    ->orwhere('dob', 'LIKE', "%" . date('Y-m-d', strtotime($search)) . "%")
                    ->orwhere('current_address', 'LIKE', "%$search%")
                    ->orwhere('permanent_address', 'LIKE', "%$search%");
            });
        }
        $total = $sql->count();
        // dd($total);
        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();
        
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = '<a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon editdata" data-id=' . $row->id .' data-user_id=' . $row->user_id . ' data-url=' . url('super-teacher') . ' title="Edit" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;';
            $operate .= '<a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon deletedata" data-id=' . $row->id . ' data-user_id=' . $row->user_id . ' data-url=' . url('super-teacher-delete', $row->user_id) . ' title="Delete"><i class="fa fa-trash"></i></a>';

            $data = getSettings('date_formate');

            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['first_name'] = $row->first_name;
            $tempRow['last_name'] = $row->getRawOriginal('last_name');
            $tempRow['gender'] = $row->gender;
            $tempRow['current_address'] = $row->current_address;
            $tempRow['permanent_address'] = $row->permanent_address;
            $tempRow['email'] = $row->email;
            $tempRow['dob'] = $row->dob;
            $tempRow['mobile'] = $row->mobile;
            $tempRow['image'] = $row->image;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function edit($id)
    {
       
        $super_teacher=User::find($id);

        // dd($super_teacher);
        return response()->json([$super_teacher, 200]);
    }

    public function update(Request $request)
    {
        // $id=$request->id;
        $id=$request->id;
        // dd($id);
        $super_teacher=User::find($id);
        // dd($super_teacher);
        if(!Auth::user()->can('super-teacher-edit') || !Auth::user()->can('super-teacher-create'))
        {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
       

        $validator=Validator::make($request->all(),[
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|unique:users,email,' . $request->id,
            'mobile'=>'required',
            'dob'=>'required',
            'gender'=>'required',
            'image'=>'mimes:png,jpg,jpeg',
            'current_address'=>'required',
            'permanent_address'=>'required',
        ]);
        
        if ($validator->fails()) {
          
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try{

            $user=User::find($id);
            if($request->hasFile('image'))
            {
                if (Storage::disk('public')->exists($user->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($user->getRawOriginal('image'));
                }
                $user->image = $request->file('image')->store('superteacher', 'public');
            }
            $plain_text_password = str_replace('-', '', date('d-m-Y', strtotime($request->dob)));
            $user->first_name=$request->first_name;
            $user->last_name=$request->last_name;
            $user->email=$request->email;
            $user->mobile=$request->mobile;
            $user->dob = date('Y-m-d', strtotime($request->dob));
            $user->gender=$request->gender;
            $user->password = Hash::make($plain_text_password);
            $user->current_address=$request->current_address;
            $user->permanent_address=$request->permanent_address;
            $user->save();

            $response = [
                'error' => false,
                'message' => trans('data_update_successfully')
                ];
           
        }
        catch(Throwable $e){
            $response = array(
                 'error' => true,
                 'message' => trans('error_occurred'),
                  'data' => $e
                );
        }
        return response()->json($response);
    }

    public function delete($id)
    {
        if(!Auth::user()->can('super-teacher-delete'))
        {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try{
            $user=User::find($id);
            if($user->image)
            {
                if (Storage::disk('public')->exists($user->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($user->getRawOriginal('image'));
                }
                
            }
                $user->delete();
                $response = [
                    'error' => false,
                    'message' => trans('data_deleted_successfully')
                    ];
            }
        catch(Throwable $e){
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                 'data' => $e
               );
        }    
        return response()->json($response);

    }
   
        
}
