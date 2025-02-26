<?php

namespace App\Http\Controllers;

use App\Models\SessionYear;
use App\Models\Subject;
use Illuminate\Support\Str;
use PhpParser\Node\Scalar\String_;
use Throwable;
use Carbon\Carbon;
use App\Models\Exam;
use App\Models\User;
use App\Models\Event;
use App\Models\Group;
use App\Models\Staff;
use App\Models\Holiday;
use App\Models\Parents;
use App\Models\Teacher;
use App\Models\ExamTerm;
use App\Models\Students;
use App\Models\StaffRole;
use App\Models\ClassGroup;
use App\Models\ClassSchool;
use App\Models\Announcement;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\ExamSequence;
use Illuminate\Http\Request;
use App\Models\CenterTeacher;
use App\Models\SubjectTeacher;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        //        dd(Auth::user()->roles->toArray());
        // return 'asdf';
        $data = [
            'students' => 0,
            'teachers' => 0,
            'parents' => 0,
            'total_boys' => 0,
            'total_girls' => 0,
            'users' => 0,
            'roles' => 0

        ];
        $teacher = $student = $parent = $teachers = null;
        $boys = 0;
        $girls = 0;
        $exams = [];
        $class_groups_dropdown = [];

//        $sessionYearId = getSettings('session_year')['session_year'];
        $sessionYearId = SessionYear::owner()->where('default', 1)->first()->id;

        $teacherQuery = Teacher::owner();

        $studentQuery = Students::owner()->whereHas('studentSessions', function ($query) use ($sessionYearId) {
            $query->where('session_year_id', $sessionYearId);
            $query->where('active', true);
        });

        $parentQuery = Parents::owner();

        if (Auth::user()->hasRole('Super Admin')) {

            $teacher = $teacherQuery->count();
            $student = $studentQuery->get()->count();

            $parent = $parentQuery->count();

            $teachers = Teacher::with('user:id,first_name,last_name,image')->get();
            if ($student > 0) {
                $boys_count = $studentQuery->clone()->whereHas('user', function ($query) {
                    $query->where('gender', 'male');
                })->count();

                $girls_count = $studentQuery->clone()->whereHas('user', function ($query) {
                    $query->where('gender', 'female');
                })->count();

                $boys = round((($boys_count * 100) / $student), 2);
                $girls = round(($girls_count * 100) / $student, 2);
            }
        }



        if (Auth::user()->teacher) {

            if (!session()->has('center_id')) {
                $centers_id = CenterTeacher::with('center:id,name')->where('teacher_id', Auth::user()->teacher->id)->get()->first()->center->id;
                $request->Session()->put('center_id', $centers_id);
            }

            $center_teacher = CenterTeacher::where('center_id', get_center_id())->where('user_id', Auth::user()->id)->where('manage_student_parent', 1)->get()->first();

            if ($center_teacher) {
                $TeacherRole = Role::where('name', 'Manage Student & Parent')->first();
                Auth::user()->assignRole([$TeacherRole->id]);
            } else {
                // $TeacherRole = Role::where('name', 'Manage Student & Parent')->first();
                Auth::user()->removeRole('Manage Student & Parent');
            }

            if (Auth::user()->teacher) {
                $class_section = ClassSection::where('class_teacher_id', Auth::user()->teacher->id)->whereHas('class', function ($q) {
                    $q->where('center_id', get_center_id());
                })->get()->first();
                if ($class_section) {
                    $TeacherRole = Role::where('name', 'Class Teacher')->first();
                    Auth::user()->assignRole([$TeacherRole->id]);
                } else {
                    Auth::user()->removeRole('Class Teacher');
                }
            }
        }

        if (Auth::user()->staff->first()) {
            if (!session()->has('center_id')) {
                // getRawOriginal
                $staff = Auth::user()->staff->first();
                $staff_roles = StaffRole::where('staff_id', $staff->id)->with('role')->get();
                $total_roles = StaffRole::where('user_id', Auth::user()->id)->get();
                foreach ($total_roles as $key => $roles) {
                    Auth::user()->removeRole($roles->role->id);
                }

                foreach ($staff_roles as $key => $role) {

                    Auth::user()->assignRole([$role->role->id]);
                    // Auth::user()->syncPermissions($role->role->permissions->pluck('name'));
                }

                if ($staff->center_id) {
                    $request->Session()->put('center_id', $staff->center_id);
                } else {
                    $request->Session()->put('center_id', -1);
                }
            }

            $data = $this->user_counter();
        }

        $subjects = Subject::Owner()->activeMediumOnly()->get();

        if (!Auth::user()->hasRole('Super Admin') && !Auth::user()->staff->first()) {


            // Holiday
            $date_formate = getSettings('date_formate');

            $exams = Exam::where('session_year_id', $sessionYearId)->whereHas('timetable', function ($q) {
                $q->where('date', '<=', Carbon::now());
            })->Owner()->where('type', 2)->get()->pluck('name', 'id');

            // Class Group
            $class_groups = Group::with(['classes.class_section.exam_statistics' => function ($q) use ($sessionYearId) {
                $q->where('session_year_id', $sessionYearId);
            }])
                ->Owner()->get();

            // Class group section
            $class_groups_dropdown = Group::Owner()->get()->pluck('name', 'id');

            $data = $this->user_counter();

            $announcement = Announcement::Owner()->where('table_type', "")->limit(5)->get();

            $class_section = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
                $q->activeMediumOnly();
            })->get();
            $ids = ExamTerm::owner()->currentSessionYear()->currentMedium()->get()->pluck('id');
            $sequences = ExamSequence::owner()->whereIn('exam_term_id', $ids)->where('status', 1)->get();

            return view('dashboard', compact('class_section', 'sequences', 'teacher', 'parent', 'student', 'announcement', 'teachers', 'boys', 'girls', 'data', 'date_formate', 'exams', 'class_groups', 'class_groups_dropdown', 'subjects'));
        }

        $announcement = Announcement::where('table_type', "")->limit(5)->get();
        return view('dashboard', compact('teacher', 'parent', 'student', 'announcement', 'teachers', 'boys', 'girls', 'data', 'class_groups_dropdown','exams', 'subjects'));
    }

    public function user_counter()
    {
        $sessionSettings = getSettings('session_year');
        $sessionYearId = $sessionSettings['session_year'] ?? null;
    
        if (!$sessionYearId) {
            // Handle missing session year, log an error or throw an exception
            logger()->error("Session year is not defined in settings.");
            return response()->json(['error' => 'Session year is not configured.'], 500);
        }
    
        $studentsQuery = Students::Owner()
            ->whereHas('class_section.class', function ($q) {
                $q->activeMediumOnly();
            });
    
        $studentsQuery->whereHas('studentSessions', function ($query) use ($sessionYearId) {
            $query->where('session_year_id', $sessionYearId);
            $query->where('active', true);
        });
    
        $students = $studentsQuery->count();
    
        $teachers = Teacher::Owner()->whereHas('user', function ($q) {
            $q->where('status', 1);
        })->count();
    
        $parents = Parents::whereHas('children.class_section.class', function ($q) {
            $q->where('medium_id', getCurrentMedium()->id);
        })
            ->whereHas('user', function ($q) {
                $q->where('status', 1);
            })->count();
    
        $total_boys = $studentsQuery->clone()->whereHas('user', function ($q) {
            $q->whereIn('gender', ['Male', 'M']);
        })->count();
    
        $total_girls = $studentsQuery->clone()->whereHas('user', function ($q) {
            $q->whereIn('gender', ['Female', 'F']);
        })->count();
    
        return [
            'students' => $students,
            'teachers' => $teachers,
            'parents' => $parents,
            'total_boys' => $total_boys,
            'total_girls' => $total_girls,
            'users' => 0,
            'roles' => 0,
        ];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function login()
    {
        if (Auth::user()) {
            return redirect('/');
        } else {
            return view('auth.login');
        }
    }

    public function resetpassword()
    {
        return view('settings.reset_password');
    }

    public function checkPassword(Request $request)
    {
        $old_password = $request->old_password;
        $password = User::where('id', Auth::id())->first();
        if (Hash::check($old_password, $password->password)) {
            return response()->json(1);
        } else {
            return response()->json(0);
        }
    }

    public function changePassword(request $request)
    {
        $id = Auth::id();
        $request->validate([
            'old_password'     => 'required',
            'new_password'     => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);
        try {
            $data['password'] = Hash::make($request->new_password);
            User::where('id', $id)->update($data);
            $response = array(
                'error'   => false,
                'message' => trans('data_update_successfully')
            );
        } catch (\Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function logout(Request $request)
    {
        //        Auth::logout();
        Auth()->guard('web')->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect('/');
    }

    public function getSubjectByClassSection(Request $request)
    {
        $class_section = ClassSection::owner()->select('class_id')->where('id', $request->class_section_id)->first();
        if (get_center_id()) {
            $subjects = ClassSubject::SubjectTeacher($request->class_section_id)->where('class_id', $class_section->class_id)->with('subject')->whereHas('subject', function ($q) {
                $q->where('center_id', get_center_id());
            })->get();
        } else {
            $subjects = ClassSubject::SubjectTeacher($request->class_section_id)->where('class_id', $class_section->class_id)->with('subject')->get();
        }


        return response($subjects);
    }

    public function getTeacherByClassSubject(Request $request)
    {
        // find the teachers which exists in class_section with subject
        try {
            $teacher_exists = SubjectTeacher::where(['class_section_id' => $request->class_section_id, 'subject_id' => $request->subject_id])->pluck('teacher_id')->toArray();
            if (sizeof($teacher_exists)) {
                // if data is edited then find teachers according to it
                if (!empty($request->edit_id)) {
                    $teacher_id = SubjectTeacher::where('id', $request->edit_id)->pluck('teacher_id')->first();
                    unset($teacher_exists[array_search($teacher_id, $teacher_exists)]);
                    array_values($teacher_exists);
                }
                //remove the existsing teachers for class section with subject
                $teachers = Teacher::with('user')->whereNotIn('id', $teacher_exists)->whereHas('center_teacher', function ($q) {
                    $q->where('center_id', Auth::user()->center->id);
                })->get();
            } else {
                // get all teachers..
                $teachers = Teacher::with('user')->whereHas('center_teacher', function ($q) {
                    $q->where('center_id', Auth::user()->center->id);
                })->get();
            }
            return response($teachers);
        } catch (\Throwable $th) {
        }
    }

    function resetPasswordView()
    {
        $class_section = ClassSection::with('class', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();
        return view('students.reset_password', compact('class_section'));
    }


    public function editProfile()
    {
        $admin_data = Auth::user();
        return view('settings.update_profile', compact('admin_data'));
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'                => 'required',
            'first_name'        => 'required',
            'last_name'         => 'required',
            'mobile'            => 'required|digits:10',
            'gender'            => 'required',
            'dob'               => 'required',
            'email'             => 'required|email',
            'image'             => 'required|mimes:jpeg,png,jpg|image|max:5048',
            'current_address'   => 'required',
            'permanent_address' => 'required',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error'   => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }
        try {
            $user_db = User::find($request->id);
            $user_db->first_name = $request->first_name;
            $user_db->last_name = $request->last_name;
            $user_db->mobile = $request->mobile;
            $user_db->gender = $request->gender;
            $user_db->dob = date('Y-m-d', strtotime($request->dob));
            $user_db->email = $request->email;
            $user_db->current_address = $request->current_address;
            $user_db->permanent_address = $request->permanent_address;
            if (!empty($request->image)) {
                if (Storage::disk('public')->exists($user_db->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($user_db->getRawOriginal('image'));
                }

                $image = $request->image;
                // made file name with combination of current time
                $file_name = time() . '-' . $image->getClientOriginalName();
                //made file path to store in database
                $file_path = 'user/' . $file_name;
                //resized image
                resizeImage($image);
                //stored image to storage/public/user folder
                $destinationPath = storage_path('app/public/user');
                $image->move($destinationPath, $file_name);

                $user_db->image = $file_path;
            }
            $user_db->save();
            $response = array(
                'error'   => false,
                'message' => trans('data_update_successfully')
            );
        } catch (Throwable $e) {
            $response = array(
                'error'   => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function demo_email()
    {
        $token = 'asdf';


        $user = User::where('email', 'kevino@gmail.com')->first();
        $center_name = '';
        if ($user->hasRole('Center')) {
            $center_name = $user->center->name;
        }
        if ($user->hasRole('Teacher')) {
            $center_name = $user->center_teacher->first()->center->name;
        }
        if ($user->staff) {
            $center_name = $user->staff->first()->center->name;
        }

        return view('auth.passwords.send_email', compact('token'));
    }
}
