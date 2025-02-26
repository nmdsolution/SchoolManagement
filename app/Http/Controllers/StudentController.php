<?php
namespace App\Http\Controllers;

use App\Exports\StudentDummayFile;
use App\Http\Forms\StudentAdmissionForm;
use App\Http\Requests\User\StoreStudentAdmissionRequest;
use App\Imports\StudentsImport;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\ExamMarks;
use App\Models\ExamReportClassDetails;
use App\Models\ExamReportStudentSequence;
use App\Models\ExamResult;
use App\Models\FeesChoiceable;
use App\Models\FeesPaid;
use App\Models\FormField;
use App\Models\Group;
use App\Models\Guardian;
use App\Models\OnlineExamStudentAnswer;
use App\Models\PaidInstallmentFee;
use App\Models\Parents;
use App\Models\PaymentTransaction;
use App\Models\SessionYear;
use App\Models\StudentAttendance;
use App\Models\StudentOnlineExamStatus;
use App\Models\Students;
use App\Models\StudentSessions;
use App\Models\StudentSubject;
use App\Models\User;
use App\Printing\StudentPrints;
use App\Services\StudentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use PhpOffice\PhpWord\TemplateProcessor;
use Spatie\Permission\Models\Role;
use Throwable;
use TypeError;

class StudentController extends Controller
{
    use FormBuilderTrait;

    public function __construct(protected StudentService $studentService)
    {
    }

    public function index()
    {
        if (!Auth::user()->can('student-list')) {
            $response = array('message' => trans('no_permission_message'));
            return redirect(route('home'))->withErrors($response);
        }

        $class_section = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        $formFields = FormField::orderBy('rank', 'ASC')->Owner()->get();

        $initial_code = getSettings('initial_code');

        $initial_code = $initial_code['initial_code'] ?? "";

        return view('students.details', compact('class_section', 'formFields', 'initial_code'));
    }

    public function create()
    {
        if (!Auth::user()->can('student-create')) {
            $response = array('message' => trans('no_permission_message'));
            return redirect(route('home'))->withErrors($response);
        }

        $class_section = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        do {
            $admission_no = mt_rand(100000, 999999); // Change the range as per your requirements
            $student = Students::currentSessionYear()->where('admission_no', $admission_no)->get()->first();
        } while ($student);

        $form = $this->form(StudentAdmissionForm::class, [
            'enctype' =>"multipart/form-data",
            'method' => 'POST',
            'novalidate' => 'novalidate',
            'url' => route('students.store'),
        ], ['admission_no' => $admission_no]);

        $formFields = FormField::owner()->orderBy('rank', 'ASC')->get();

        $initial_code = getSettings('initial_code');

        $initial_code = $initial_code['initial_code'] ?? "";

        return view('students.index', compact('class_section', 'admission_no', 'formFields', 'initial_code', 'form'));
    }

    public function createBulkData(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        if (!Auth::user()->can('student-create')) {
            $response = array('message' => trans('no_permission_message'));
            return redirect(route('home'))->withErrors($response);
        }

        $class_section = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        $fixed_fields = ['Full Name', 'Date of Birth', 'Gender', 'Admission Date'];
        $form_fields = FormField::where('center_id', get_center_id())->where('is_required', 1)->get();

        return view('students.add_bulk_data', compact('class_section', 'fixed_fields', 'form_fields'));
    }

    public function storeBulkData(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!Auth::user()->can('student-create') || !Auth::user()->can('student-edit')) {
            $response = array('message' => trans('no_permission_message'));
            return response()->json($response);
        }

        $validator = Validator::make($request->all(), [
            'class_section_id' => 'required',
            'file' => 'required|mimes:csv,txt'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            DB::beginTransaction();

            $studentImports = new StudentsImport($request->class_section_id);

            Excel::import($studentImports, $request->file);

            $modifiedRecords = $studentImports->getModifiedRecords();

            DB::commit();

            $response = [
                'error' => false,
                'message' => $studentImports->studentCount . " " . trans('students_stored_successfully'),
                'modifications' => count($modifiedRecords) > 0 ? [
                    'warning' => 'Some data was automatically corrected during import. Please review the following modifications:',
                    'records' => $modifiedRecords
                ] : null
            ];
        } catch (Exception $e) {
            if ($e instanceof TypeError && Str::contains($e->getMessage(), [
                    'Mail',
                    'Mailer',
                    'MailManager'
                ])) {
                $response = array(
                    'warning' => true,
                    'error' => false,
                    'message' => "Student Registered successfully. But Email not sent.",
                );
            } else {
                DB::rollBack();
                $response = array(
                    'error' => true,
                    'message' => $e->getMessage(),
                );
            }

        }
        return response()->json($response);
    }


    public function store(StoreStudentAdmissionRequest $request)
    {
        if (!Auth::user()->can('student-create') || !Auth::user()->can('student-edit')) {
            $response = array('message' => trans('no_permission_message'));
            return response()->json($response);
        }

        $form = $this->form(StudentAdmissionForm::class);

        $formData = $form->getData();

        if (!empty($request->father_first_name) && !is_numeric($request->father_first_name)) {
            $request->validate([
                'father_email' => 'nullable|email|unique:users,email|unique:parents,email',
                'father_image' => 'nullable|mimes:jpeg,png,jpg|image|max:2048',
                'father_mobile' => 'required',
            ]);
        }

        if (!empty($request->mother_first_name) && !is_numeric($request->mother_first_name)) {
            $request->validate([
                'mother_email' => 'nullable|email|unique:users,email|unique:parents,email',
                'mother_image' => 'nullable|mimes:jpeg,png,jpg|image|max:2048',
                'mother_mobile' => 'required',
            ]);
        }

        if (isset($request->guardian_first_name) && !is_numeric($request->guardian_first_name)) {
            $request->validate([
                'guardian_email' => 'nullable|email|unique:parents,email',
                'guardian_image' => 'nullable|mimes:jpeg,png,jpg|image|max:2048',
                'guardian_mobile' => 'required',
            ]);
        }

        $check_matricule = Students::currentSessionYear()->where('center_id', get_center_id())->where('admission_no', $request->admission_no)->first();

        if ($check_matricule) {
            $response = [
                'error' => true,
                'message' => 'Matricule number already exists'
            ];
            return response($response);
        }

        try {
            DB::beginTransaction();

            $parentRole = Role::where('name', 'Parent')->first();
            $studentRole = Role::where('name', 'Student')->first();

            if (!empty($request->father_first_name)) {
                //Add Father in User and Parent table data
                $father_plaintext_password = str_replace('-', '', date('d-m-Y', strtotime($request->father_dob)));
                if (!is_numeric($request->father_first_name)) {
                    $father_email = $request->father_email;
                    $father_user = new User();

                    $father_user->image = $request->hasFile('father_image') ? $request->file('father_image')->store('parents') : "";
                    $father_user->password = Hash::make($father_plaintext_password);
                    $father_user->first_name = $request->father_first_name;
                    $father_user->last_name = $request->father_last_name;
                    $father_user->email = $father_email;
                    $father_user->mobile = $request->father_mobile;
                    $father_user->dob = date('Y-m-d', strtotime($request->father_dob));
                    $father_user->gender = 'Male';
                    $father_user->save();
                    $father_user->assignRole($parentRole);

                    $father_parent = new Parents();
                    $father_parent->user_id = $father_user->id;
                    $father_parent->first_name = $request->father_first_name;
                    $father_parent->last_name = $request->father_last_name;
                    $father_parent->image = $father_user->getRawOriginal('image');
                    $father_parent->occupation = $request->father_occupation;
                    $father_parent->mobile = $request->father_mobile;
                    $father_parent->email = $request->father_email;
                    $father_parent->dob = date('Y-m-d', strtotime($request->father_dob));
                    $father_parent->gender = 'Male';
                    $father_parent->save();
                    $father_parent_id = $father_parent->id;
                    $father_email = $request->father_email;
                    $father_name = $request->father_first_name;
                } else {
                    $father_parent_id = $request->father_first_name;
                    $parent = Parents::where('id', $request->father_first_name)->first();
                    $father_email = $parent->email;
                    $father_name = $parent->first_name;
                }
            }

            if (!empty($request->mother_first_name)) {
                //Add Mother in User and Parent table data
                $mother_plaintext_password = str_replace('-', '', date('d-m-Y', strtotime($request->mother_dob)));
                if (!is_numeric($request->mother_first_name)) {
                    $mother_email = $request->mother_email;
                    $mother_user = new User();

                    $mother_user->image = $request->hasFile('mother_image') ? $request->file('mother_image')->store('parents') : "";
                    $mother_user->password = Hash::make($mother_plaintext_password);
                    $mother_user->first_name = $request->mother_first_name;
                    $mother_user->last_name = $request->mother_last_name;
                    $mother_user->email = $mother_email;
                    $mother_user->mobile = $request->mother_mobile;
                    $mother_user->dob = date('Y-m-d', strtotime($request->mother_dob));
                    $mother_user->gender = 'Female';
                    $mother_user->save();
                    $mother_user->assignRole($parentRole);

                    $mother_parent = new Parents();
                    $mother_parent->user_id = $mother_user->id;
                    $mother_parent->first_name = $request->mother_first_name;
                    $mother_parent->last_name = $request->mother_last_name;
                    $mother_parent->image = $mother_user->getRawOriginal('image');
                    $mother_parent->occupation = $request->mother_occupation;
                    $mother_parent->mobile = $request->mother_mobile;
                    $mother_parent->email = $request->mother_email;
                    $mother_parent->dob = date('Y-m-d', strtotime($request->mother_dob));
                    $mother_parent->gender = 'Female';
                    $mother_parent->save();
                    $mother_parent_id = $mother_parent->id;
                    $mother_email = $request->mother_email;
                    $mother_name = $request->mother_first_name;
                } else {
                    $mother_parent_id = $request->mother_first_name;
                    $parent = Parents::where('id', $request->mother_first_name)->first();
                    $mother_email = $parent->email;
                    $mother_name = $parent->first_name;
                }
            }

            if (isset($request->guardian_first_name)) {
                if (!is_numeric($request->guardian_first_name)) {
                    $plaintext_password = str_replace('-', '', date('d-m-Y', strtotime($request->guardian_dob)));
                    $user = new User();
                    $user->first_name = $request->guardian_first_name;
                    $user->last_name = $request->guardian_last_name;
                    $user->mobile = $request->guardian_mobile_no;
                    $user->email = $request->guardian_email;
                    $user->password = Hash::make($plaintext_password);
                    $user->gender = $request->guardian_gender;
                    $user->dob = date('Y-m-d', strtotime($request->guardian_dob));
                    $user->image = $request->file('guardian_image')->store('guardians', 'public');
                    $user->save();
                    $parentRole = Role::where('name', 'Parent')->first();
                    $user->assignRole($parentRole);

                    $parent = new Parents();
                    $parent->user_id = $user->id;
                    $parent->first_name = $request->guardian_first_name;
                    $parent->last_name = $request->guardian_last_name;
                    $parent->gender = $request->guardian_gender;
                    $parent->email = $request->guardian_email;
                    $parent->mobile = $request->guardian_mobile;
                    $parent->occupation = $request->guardian_occupation;
                    $parent->dob = date('Y-m-d', strtotime($request->guardian_dob));
                    $parent->image = $request->file('guardian_image')->store('guardians', 'public');
                    $parent->save();

                    $guardian = new Guardian();
                    $guardian->student_id = $request->child_id;
                    $guardian->user_id = $user->id;
                    $guardian->save();

                    $guardian_email = $request->guardian_email;
                    $guardian_parent = new Parents();
                    $guardian_parent->user_id = null;
                    $guardian_parent->first_name = $request->guardian_first_name;
                    $guardian_parent->last_name = $request->guardian_last_name;
                    $guardian_parent->image = $request->hasFile('guardian_image') ? $request->file('guardian_image')->store('parents') : "";
                    $guardian_parent->occupation = $request->guardian_occupation;
                    $guardian_parent->mobile = $request->guardian_mobile;
                    $guardian_parent->email = $guardian_email;
                    $guardian_parent->dob = date('Y-m-d', strtotime($request->guardian_dob));
                    $guardian_parent->gender = $request->guardian_gender;
                    $guardian_parent->save();
                    $guardian_parent_id = $guardian_parent->id;
                } else {
                    $guardian_parent_id = $request->guardian_first_name;
                }
            } else {
                $guardian_parent_id = NULL;
            }

            //Create Student User First
            // $user = User::find($request->edit_id);
            $student_plaintext_password = str_replace('-', '', date('d-m-Y', strtotime($request->dob)));
            $user = new User();

            $user->password = Hash::make($student_plaintext_password);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->admission_no;
            $user->mobile = $request->mobile ?? "";
            $user->dob = date('Y-m-d', strtotime($request->dob));
            $user->gender = $request->gender;
            $user->born_at = $request->born_at;

            //If Image exists then upload new image and delete the old image
            if ($request->hasFile('image')) {
                $user->image = $request->file('image')->store('students', 'public');
            }
            $user->save();
            $user->assignRole($studentRole);


            $student = new Students();
            // Student dynamic fields
            $formFields = FormField::owner()->orderBy('rank', 'ASC')->get();
            $data = array();
            $status = 0;

            $dynamic_data = [];

            // Initialize dynamic_data only if there's a valid JSON string
            if (!empty($student->dynamic_field_values)) {
                $dynamic_data = json_decode($student->dynamic_field_values, true) ?? [];
            }

            foreach ($formFields as $form_field) {
                // INPUT TYPE CHECKBOX
                if ($form_field->type == 'checkbox') {
                    if ($status == 0) {
                        $data[] = $request->checkbox;
                        $status = 1;
                    }
                } else if ($form_field->type == 'file') {
                    // INPUT TYPE FILE
                    $get_file = '';
                    $field = str_replace(" ", "_", $form_field->name);
                    if ($dynamic_data && count($dynamic_data) > 0) {
                        foreach ($dynamic_data as $field_data) {
                            if (isset($field_data[$field])) { // GET OLD FILE IF EXISTS
                                $get_file = $field_data[$field];
                            }
                        }
                    }
                    $hidden_file_name = 'file-' . $field;

                    if ($request->hasFile($field)) {
                        if ($get_file) {
                            Storage::disk('public')->delete($get_file); // DELETE OLD FILE IF NEW FILE IS SELECT
                        }
                        $data[] = [str_replace(" ", "_", $form_field->name) => $request->file($field)->store('student', 'public')];
                    } else {
                        if ($request->$hidden_file_name) {
                            $data[] = [str_replace(" ", "_", $form_field->name) => $request->$hidden_file_name];
                        }
                    }
                } else {
                    $field = str_replace(" ", "_", $form_field->name);
                    $data[] = [str_replace(" ", "_", $form_field->name) => $request->$field];
                }
            }
            //            $status = 0;
            // End student dynamic field

            $sessionYearId = getSettings('session_year')['session_year'];

            $student->user_id = $user->id;
            $student->center_id = get_center_id();
            $student->class_section_id = $request->class_section_id;
            $student->admission_no = $request->admission_no;
            $student->roll_number = $request->roll_number;
            $student->admission_date = date('Y-m-d', strtotime($request->admission_date));
            $student->father_id = $father_parent_id ?? null;
            $student->mother_id = $mother_parent_id ?? null;
            $student->guardian_id = $guardian_parent_id ?? null;
            $student->dynamic_field_values = json_encode($data);
            $student->nationality = $request->nationality;
            $student->repeater = $request->input('repeater', 0);

            $student->minisec_matricule = $request->input('minisec_matricule', '');

            $student->status = json_encode($request->status);

            $student->session_year_id = $sessionYearId;

            $student->save();


            if (!empty($request->father_first_name) && !empty($request->father_email)) {
                //Send User Credentials via Email
                $school_name = getSettings('school_name');
                $father_data = [
                    'subject' => 'Welcome to ' . $school_name['school_name'],
                    'email' => $father_email,
                    'name' => ' ' . $father_name,
                    'username' => ' ' . $father_email,
                    'password' => ' ' . $father_plaintext_password,
                    'child_name' => ' ' . $request->first_name,
                    'child_grnumber' => ' ' . $request->admission_no,
                    'child_password' => ' ' . $student_plaintext_password,
                ];

                Mail::send('students.email', $father_data, function ($message) use ($father_data) {
                    $message->to($father_data['email'])->subject($father_data['subject']);
                });
            }

            if (!empty($request->mother_first_name) && !empty($request->mother_email)) {
                $mother_data = [
                    'subject' => 'Welcome to ' . $school_name['school_name'],
                    'email' => $mother_email,
                    'name' => ' ' . $mother_name,
                    'username' => ' ' . $mother_email,
                    'password' => ' ' . $mother_plaintext_password,
                    'child_name' => ' ' . $request->first_name,
                    'child_grnumber' => ' ' . $request->admission_no,
                    'child_password' => ' ' . $student_plaintext_password,
                ];

                Mail::send('students.email', $mother_data, function ($message) use ($mother_data) {
                    $message->to($mother_data['email'])->subject($mother_data['subject']);
                });
            }

            // creating the StudentSession
            $studentSession = new StudentSessions();
            $studentSession->student_id = $student->id;
            $studentSession->class_section_id = $request->class_section_id;
            $studentSession->session_year_id = getSettings('session_year')['session_year'];
            $studentSession->promoted = false;
            $studentSession->save();

            DB::commit();

            assign_roll_number($request->class_section_id);

            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (Throwable $e) {
            // IF Exception is TypeError and message containes Mail keywords then email is not sent successfully
            if ($e instanceof TypeError && Str::contains($e->getMessage(), [
                    'Mail',
                    'Mailer',
                    'MailManager'
                ])) {
                $response = array(
                    'warning' => true,
                    'error' => false,
                    'message' => "Student Registered successfully. But Email not sent.",
                );
                DB::commit();
            } else {
                DB::rollBack();
                $response = array(
                    'error' => true,
                    'message' => trans('error_occurred'),
                    'data' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTrace()
                );
            }
        }

        return response()->json($response);
    }

    public function update(Request $request)
    {
        // logger($request);
        if (!Auth::user()->can('student-create') || !Auth::user()->can('student-edit')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $request->validate([
            'edit_id' => 'required',
            'first_name' => 'required',
            'image' => 'mimes:jpeg,png,jpg|image|max:2048|nullable',
            'dob' => 'required',
            'class_section_id' => 'required',
            'admission_no' => 'required|unique:users,email,' . $request->edit_id,
            'roll_number' => 'required',
            'admission_date' => 'required',
            'father_email' => 'nullable',
            'nationality' => 'required',
            'repeater' => 'nullable|boolean',
            'minisec_matricule' => 'nullable|string',
            'born_at' => 'nullable|string',
            'status' => 'nullable|array',
        ], [
            'admission_no.unique' => trans('Matricule number already exists'),
        ]);

        // TODO: Do some checks to make sure that no data like the marks was entered for this student.

        if (!empty($request->father_first_name) && !is_numeric($request->father_first_name)) {
            $request->validate([
                'father_email' => 'nullable|email|unique:users,email,' . $request->father_email,
                'father_image' => 'mimes:jpeg,png,jpg|image|max:2048|nullable',
                'father_mobile' => 'required',
            ]);
        }

        if (!empty($request->mother_first_name) && !is_numeric($request->mother_first_name)) {
            $request->validate([
                'mother_email' => 'nullable|email|unique:users,email,' . $request->mother_email . '|unique:parents,email,' . $request->mother_email,
                'mother_image' => 'mimes:jpeg,png,jpg|image|max:2048|nullable',
                'mother_mobile' => 'required',
            ]);
        }

        if (!empty($request->guardian_first_name) && !is_numeric($request->guardian_first_name)) {
            $request->validate([
                'guardian_email' => 'nullable|email|unique:parents,email,' . $request->guardian_email,
                'guardian_image' => 'nullable|mimes:jpeg,png,jpg|image|max:2048',
                'guardian_mobile' => 'required',
            ]);
        }

        $student_roll_number = Students::where('class_section_id', $request->class_section_id)->where('roll_number', $request->roll_number)->whereNot('user_id', $request->edit_id)->get()->first();
        if ($student_roll_number) {
            $response = array(
                'error' => true,
                'message' => 'Roll number already exists',
            );
            return response()->json($response);
        }

        $student_matricule = Students::Owner()
            ->currentSessionYear()
            ->where('admission_no', $request->admission_no)
            ->whereNot('user_id', $request->edit_id)->get()->first();

        if ($student_matricule) {
            $response = array(
                'error' => true,
                'message' => 'Matricule already exists',
            );
            return response()->json($response);
        }

        try {
            //Add Father in User and Parent table data
            if (!empty($request->father_first_name) && !is_numeric($request->father_first_name)) {
                $father_user = new User();
                $father_user->image = $request->hasFile('father_image') ?? $request->file('father_image')->store('parents', 'public');
                $father_user->password = Hash::make(str_replace('/', '', $request->father_dob));
                $father_user->first_name = $request->father_first_name;
                $father_user->last_name = $request->father_last_name;
                $father_user->email = $request->father_email;
                $father_user->mobile = $request->father_mobile;
                $father_user->dob = date('Y-m-d', strtotime($request->father_dob));
                $father_user->gender = 'Male';
                $father_user->save();

                $father_parent = new Parents();
                $father_parent->user_id = $father_user->id;
                $father_parent->first_name = $request->father_first_name;
                $father_parent->last_name = $request->father_last_name;
                $father_parent->image = $father_user->getRawOriginal('image');
                $father_parent->occupation = $request->father_occupation;
                $father_parent->mobile = $request->father_mobile;
                $father_parent->email = $request->father_email;
                $father_parent->dob = date('Y-m-d', strtotime($request->father_dob));
                $father_parent->gender = 'Male';
                $father_parent->save();
                $father_parent_id = $father_parent->id;
            } else {
                $father_parent_id = $request->father_first_name;
            }

            //Add Mother in User and Parent table data
            if (!empty($request->mother_first_name) && !is_numeric($request->mother_first_name)) {
                $mother_user = new User();
                $mother_user->image = $request->hasFile('mother_image') ?? $request->file('mother_image')->store('parents', 'public');
                $mother_user->password = Hash::make(str_replace('/', '', $request->mother_dob));
                $mother_user->first_name = $request->mother_first_name;
                $mother_user->last_name = $request->mother_last_name;
                $mother_user->email = $request->mother_email;
                $mother_user->mobile = $request->mother_mobile;
                $mother_user->dob = date('Y-m-d', strtotime($request->mother_dob));
                $mother_user->gender = 'Female';
                $mother_user->save();

                $mother_parent = new Parents();
                $mother_parent->user_id = 0;
                $mother_parent->first_name = $request->mother_first_name;
                $mother_parent->last_name = $request->mother_last_name;
                $mother_parent->image = $mother_user->getRawOriginal('image');
                $mother_parent->occupation = $request->mother_occupation;
                $mother_parent->mobile = $request->mother_mobile;
                $mother_parent->email = $request->mother_email;
                $mother_parent->dob = date('Y-m-d', strtotime($request->mother_dob));
                $mother_parent->gender = 'Female';
                $mother_parent->save();
                $mother_parent_id = $mother_parent->id;
            } else {
                $mother_parent_id = $request->mother_first_name;
            }

            if (isset($request->guardian_first_name)) {
                if (!is_numeric($request->mother_first_name)) {
                    $guardian_parent = new Parents();
                    $guardian_parent->user_id = 0;
                    $guardian_parent->first_name = $request->guardian_first_name;
                    $guardian_parent->last_name = $request->guardian_last_name;
                    $guardian_parent->image = $request->hasFile('guardian_image') ?? $request->file('guardian_image')->store('parents', 'public');;
                    $guardian_parent->occupation = $request->guardian_occupation;
                    $guardian_parent->mobile = $request->guardian_mobile;
                    $guardian_parent->email = $request->guardian_email;
                    $guardian_parent->dob = date('Y-m-d', strtotime($request->guardian_dob));
                    $guardian_parent->gender = $request->guardian_gender;
                    $guardian_parent->save();
                    $guardian_parent_id = $guardian_parent->id;
                } else {
                    $guardian_parent_id = $request->guardian_first_name;
                }
            } else {
                $guardian_parent_id = NULL;
            }

            //Create Student User First
            $user = User::find($request->edit_id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->mobile = $request->mobile ?? "";
            $user->email = $request->admission_no;
            $user->dob = date('Y-m-d', strtotime($request->dob));
            $user->gender = $request->gender;
            $user->born_at = $request->input('born_at', '');
            if ($request->hasFile('image')) {
                if ($user->getRawOriginal('image') && Storage::disk('public')->exists($user->getRawOriginal('image'))) {
                    Storage::disk('public')->delete($user->getRawOriginal('image'));
                }
                $user->image = $request->file('image')->store('student', 'public');
            }

            $user->save();

            $student = Students::where('user_id', $user->id)->firstOrFail();
            // Student dynamic fields
            $formFields = FormField::owner()->orderBy('rank', 'ASC')->get();
            $data = array();
            $status = 0;
            $i = 0;
            $dynamic_data = [];

            // Initialize dynamic_data only if there's a valid JSON string
            if (!empty($student->dynamic_field_values)) {
                $dynamic_data = json_decode($student->dynamic_field_values, true) ?? [];
            }

            foreach ($formFields as $form_field) {
                // INPUT TYPE CHECKBOX
                if ($form_field->type == 'checkbox') {
                    if ($status == 0) {
                        $data[] = $request->checkbox;
                        $status = 1;
                    }
                } else if ($form_field->type == 'file') {
                    // INPUT TYPE FILE
                    $get_file = '';
                    $field = str_replace(" ", "_", $form_field->name);
                    foreach ($dynamic_data as $field_data) {
                        if (isset($field_data[$field])) { // GET OLD FILE IF EXISTS
                            $get_file = $field_data[$field];
                        }
                    }
                    $hidden_file_name = 'file-' . $field;

                    if ($request->hasFile($field)) {
                        if ($get_file) {
                            Storage::disk('public')->delete($get_file); // DELETE OLD FILE IF NEW FILE IS SELECT
                        }
                        $data[] = [
                            str_replace(" ", "_", $form_field->name) => $request->file($field)->store('student', 'public')
                        ];
                    } else {
                        if ($request->$hidden_file_name) {
                            $data[] = [
                                str_replace(" ", "_", $form_field->name) => $request->$hidden_file_name
                            ];
                        }
                    }
                } else {
                    $field = str_replace(" ", "_", $form_field->name);
                    $data[] = [
                        str_replace(" ", "_", $form_field->name) => $request->$field
                    ];
                }
            }

            $status = 0;
            // End student dynamic field

            // update the student class section for both the current session year and the students table
            $student->class_section_id = $request->class_section_id;

            $studentSession = $student->studentSessions()->where('session_year_id', getSettings('session_year')['session_year'])->firstOrFail();
            $studentSession->class_section_id = $request->class_section_id;
            $studentSession->save();

            $student->admission_no = $request->admission_no;
            $student->roll_number = $request->roll_number;
            $student->admission_date = date('Y-m-d', strtotime($request->admission_date));
            $student->status = json_encode($request->edit_status);
            $student->father_id = $father_parent_id;
            $student->mother_id = $mother_parent_id;
            $student->guardian_id = $guardian_parent_id;
            $student->dynamic_field_values = json_encode($data);
            $student->nationality = $request->nationality;
            $student->minisec_matricule = $request->minisec_matricule;
            $student->repeater = $request->repeater ? true : false;
            $student->save();

            assign_roll_number($request->class_section_id);

            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
        } catch (Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            );
            logger($e->getMessage());
        }
        return response()->json($response);
    }

    public function listByGroup(Request $request)
    {

        $groupId = $request->groupId;

        // provide more actions to take if the groupId is not provided.

        if (!Auth::user()->can('student-list')) {
            $response = array('message' => trans('no_permission_message'));
            return response()->json($response);
        }

        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', 10);
        $sort = $request->input('sort', 'users.first_name');
        $order = $request->input('order', 'ASC');

        $groupQuery = Group::with('classes')->owner();

        if ($groupId) {
            $groups = $groupQuery->where('id', $groupId)->get();
        } else {
            $groups = $groupQuery->get();
        }

        $groupStudentData = array();

        foreach ($groups as $group) {
            $classGroupIds = $group->classes->pluck('id')->toArray();

            $stats = array();
            $bulkData = array();
            $rows = array();
            $tempRow = array();

            $data = getSettings('date_formate');

            $no = 1;

            $sessionYearId = getSettings('session_year')['session_year'];

            $studentQuery = Students::with('user', 'class_section')
                ->join('users', 'students.user_id', '=', 'users.id')
                ->select('students.*', 'users.first_name', 'users.last_name', 'users.gender')
                ->Owner()->ofTeacher()->whereHas('class_section.class', function ($q) use ($classGroupIds) {
                    $q->whereIn('id', $classGroupIds);
                    $q->activeMediumOnly();
                })->whereHas('studentSessions', function ($query) use ($sessionYearId) {
                    $query->where('session_year_id', $sessionYearId);
                });

            $result = $studentQuery->get();
            $bulkData['total'] = $result->count();

            $new_query = $studentQuery->clone()->where('is_new_admission', 1);
            $new_males = $new_query->clone()->whereIn('gender', ["Male", 'M'])->get()->count();
            $new_females = $new_query->clone()->whereIn('gender', ["Female", 'female', 'F'])->get()->count();

            $old_query = $studentQuery->clone()->where('is_new_admission', 0);
            $old_males = $old_query->clone()->whereIn('gender', ["Male", 'M'])->get()->count();
            $old_females = $old_query->clone()->whereIn('gender', ["Female", 'F'])->get()->count();


            $stats = array(
                'N' => array(
                    'male' => $new_males,
                    'female' => $new_females
                ),
                'O' => array(
                    'male' => $old_males,
                    'female' => $old_females
                ),
                'total' => $result->count()
            );

            foreach ($result as $student) {
                // modifing the different attributes of the row.
                $tempRow['id'] = $student->user->id;
                $tempRow['no'] = $no++;
                $tempRow['first_name'] = $student->user->first_name;
                $tempRow['last_name'] = $student->user->last_name;
                $tempRow['gender'] = $student->user->gender;
                $tempRow['class_section_name'] = $student->class_section->full_name;
                $tempRow['dob'] = date($data['date_formate'], strtotime($student->user->dob));

                $rows[] = $tempRow;
            }
            $groupStudentData[$group->name]['items'] = $rows;
            $groupStudentData[$group->name]['stats'] = $stats;
        }


        if (request()->get('print')) {

            $pdf = StudentPrints::getInstance(get_center_id(), 'P');

            $pdf->printStudentsOfGroup($groupStudentData);

            return response(
                $pdf->Output('', 'GROUPED STUDENT LIST For ' . $group->name . ' .pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);

    }

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

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        if (!Auth::user()->can('student-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        try {
            DB::beginTransaction();

            $student_id = Students::query()->select('id')->where('user_id', $id)->pluck('id')->first();

            $this->deleteStudentDataForCurrentSession($student_id);

            $this->cleanStudentIfNotExist($student_id);

            // implement some background logic which is going to recalculate the student marks
            DB::commit();

            $response = [ 'error' => false, 'message' => trans('data_delete_successfully')];

        } catch (Throwable $e) {
            DB::rollBack();
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
            );
        }

        return response()->json($response);
    }

    public function deleteStudentDataForCurrentSession($student_id): void
    {
        AssignmentSubmission::currentSessionYear()->where('student_id', $student_id)->delete();
        Attendance::currentSessionYear()->where('student_id', $student_id)->delete();
        ExamMarks::currentSessionYear()->where('student_id', $student_id)->delete();
        ExamResult::currentSessionYear()->where('student_id', $student_id)->delete();
        FeesChoiceable::currentSessionYear()->where('student_id', $student_id)->delete();
        FeesPaid::currentSessionYear()->where('student_id', $student_id)->delete();
        PaymentTransaction::currentSessionYear()->where('student_id', $student_id)->delete();
        StudentSessions::currentSessionYear()->where('student_id', $student_id)->delete();
        StudentSubject::currentSessionYear()->where('student_id', $student_id)->delete();
        StudentAttendance::currentSessionYear()->where('student_id', $student_id)->delete();
        PaidInstallmentFee::currentSessionYear()->where('student_id', $student_id)->delete();
    }

    public function cleanStudentIfNotExist($student_id): void
    {
        $studentSessionsExist = StudentSessions::where('student_id', $student_id)->count();

        if (!$studentSessionsExist) {

            // deleting the models that are dependent on only the student
            OnlineExamStudentAnswer::where('student_id', $student_id)->delete();
            StudentOnlineExamStatus::where('student_id', $student_id)->delete();
            ExamReportClassDetails::where('student_id', $student_id)->delete();
            ExamReportStudentSequence::where('student_id', $student_id)->delete();
            Guardian::where('student_id', $student_id)->delete();

            $student = Students::find($student_id);
            $userId = $student->user_id;

            if ($student->father_image != "" && Storage::disk('public')->exists($student->father_image)) {
                Storage::disk('public')->delete($student->father_image);
            }
            if ($student->mother_image != "" && Storage::disk('public')->exists($student->mother_image)) {
                Storage::disk('public')->delete($student->mother_image);
            }

            // done deletign everything about the student.
            $student->delete();

            $user = User::findOrFail($userId);
            if ($user->image != "" && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $user->delete();
        }
    }


    public function reset_password()
    {
        if (!Auth::user()->can('reset-password-list')) {
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

        $sql = Students::owner()->with('user')->whereHas('user', function ($q) {
            $q->where('reset_request', 1);
        });
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where('id', 'LIKE', "%$search%")->orwhere('users.email', 'LIKE', "%$search%")->orwhere('user.first_name', 'LIKE', "%$search%")->orwhere('user.last_name', 'LIKE', "%$search%")->orWhereRaw("concat(users.first_name,' ',users.last_name) LIKE '%" . $search . "%'");
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

    public function change_password(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!Auth::user()->can('student-change-password')) {
            $response = array('message' => trans('no_permission_message'));
            return response()->json($response);
        }
        try {
            $dob = date('dmY', strtotime($request->dob));
            $user = User::find($request->id);
            $user->reset_request = 0;
            $user->password = Hash::make($dob);
            $user->save();

            $response = [
                'error' => false,
                'message' => trans('data_update_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function assignClass()
    {
        $class_sections = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        $class = ClassSchool::activeMediumOnly()->where('center_id', Auth::user()->center->id)->get();
        return view('students.assign-class', compact('class_sections', 'class'));
    }

    public function transferStudentList(Request $request)
    {
        $sort = 'id';
        $order = 'DESC';
        $sessionYearId = getSettings('session_year')['session_year'];

        $from_class_section_id = $request->from_class_section_id;

        $sql = Students::owner()
            ->join('users', 'students.user_id', '=', 'users.id')
            ->select('students.*', 'users.id as user_id', 'users.first_name', 'users.last_name', 'users.image')
            ->with('user:id,first_name,last_name,image', 'class_section')
            ->whereHas('studentSessions', function ($query) use ($from_class_section_id, $sessionYearId) {
                $query->where('session_year_id', $sessionYearId);
                $query->where('class_section_id', $from_class_section_id);
            });

        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhere('user_id', 'LIKE', "%$search%")
                    ->orWhere('class_section_id', 'LIKE', "%$search%")
                    ->orWhere('is_new_admission', 'LIKE', "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%$search%")->orwhere('last_name', 'LIKE', "%$search%");
                    });
            });
        }

        $total = $sql->count();
        $res = $sql->orderBy($sort, $order)->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        $data = getSettings('date_formate');
        foreach ($res as $row) {
            $classSection = $row->studentSessions()->where('session_year_id', getSettings('session_year')['session_year'])->get()
                                ->first()->class_section;

            $assign_student = '<input type="checkbox" class="assign_student"  name="assign_student" value=' . $row->id . '>';

            $tempRow['chk'] = $assign_student;
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['user_id'] = $row->user_id;
            $tempRow['first_name'] = $row->user->first_name;
            $tempRow['last_name'] = $row->user->last_name;
            $tempRow['image'] = $row->user->image;
            $tempRow['class_section_id'] = $classSection->id;
            $tempRow['class_section_name'] = $classSection->full_name;
            $tempRow['admission_no'] = $row->admission_no;
            $tempRow['roll_number'] = $row->roll_number;
            $tempRow['admission_date'] = date($data['date_formate'], strtotime($row->admission_date));
            $rows[] = $tempRow;
        }

        if ($request->get('print')) {
            $class_section = ClassSection::find($from_class_section_id);

            $pdf = StudentPrints::getInstance(get_center_id(), 'L');

            $pdf->printNewStudentList($rows, $class_section);

            return response(
                $pdf->Output('', 'STUDENT LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function groupNewStudents(Request $request)
    {

        $groupId = $request->groupId ?? null;

        if (!$groupId) {
            return response()->json([
                'error' => "Group Id not provided",
            ]);
        }

        $sort = 'id';
        $order = 'DESC';

        $group = Group::with('classes')->where('id', $groupId)->firstOrFail();
        $classGroupIds = $group->classes->pluck('id')->toArray();

        $sql = Students::with('user:id,first_name,last_name,image,dob', 'class_section')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->select('students.*', 'users.first_name', 'users.last_name')
            ->Owner()->ofTeacher()->whereHas('class_section.class', function ($q) use ($classGroupIds) {
                $q->whereIn('id', $classGroupIds);
                $q->activeMediumOnly();
            })->where('is_new_admission', 1);

        $total = $sql->count();
        $res = $sql->orderBy($sort, $order)->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        $data = getSettings('date_formate');
        foreach ($res as $row) {
            $assign_student = '<input type="checkbox" class="assign_student"  name="assign_student" value=' . $row->id . '>';

            $tempRow['chk'] = $assign_student;
            $tempRow['id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['user_id'] = $row->user_id;
            $tempRow['first_name'] = $row->user->first_name;
            $tempRow['dob'] = $row->user->dob;
            $tempRow['image'] = $row->user->image;
            $tempRow['class_section_id'] = $row->class_section_id;
            $tempRow['class_section_name'] = $row->class_section->full_name;
            $tempRow['admission_no'] = $row->admission_no;
            $tempRow['roll_number'] = $row->roll_number;
            $tempRow['admission_date'] = date($data['date_formate'], strtotime($row->admission_date));
            $rows[] = $tempRow;
        }

        if ($request->get('print')) {

            $pdf = StudentPrints::getInstance(get_center_id(), 'L');

            $pdf->printNewStudentList($rows, null);

            return response(
                $pdf->Output('', 'STUDENT LIST.pdf'),
                200,
                [
                    'Content-Type' => 'application/pdf'
                ]
            );
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function assignClass_store(Request $request)
    {
//        if (Auth::user()->can('student-list')) {
//            $response = array(
//                'message' => trans('no_permission_message')
//            );
//            return redirect(route('home'))->withErrors($response);
//        }
        $validator = Validator::make($request->all(), [
            'from_class_section_id' => 'required|different:to_class_section_id',
            'to_class_section_id' => 'required',
            'selected_id' => 'required',
        ], [
            'from_class_section_id.different' => 'The from class section ID must be different from the to class section ID.',
        ]);

        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        $to_class_section_id = $request->to_class_section_id;
        $from_class_section_id = $request->from_class_section_id;


        try {
            DB::beginTransaction();

            $selected_student = explode(',', $request->selected_id);
            $sessionYearId = getSettings('session_year')['session_year'];

            $success = 0;
            $failure = 0;

            for ($i = 0, $iMax = count($selected_student); $i < $iMax; $i++) {

                $student_id = $selected_student[$i];

                $assignments = AssignmentSubmission::currentSessionYear()->where('student_id', $student_id)->count();
                $attendance = Attendance::currentSessionYear()->where('student_id', $student_id)->count();
                $examMarks = ExamMarks::currentSessionYear()->where('student_id', $student_id)->count();
                $examResults =ExamResult::currentSessionYear()->where('student_id', $student_id)->count();
                $feesChoiceable = FeesChoiceable::currentSessionYear()->where('student_id', $student_id)->count();
                $feesPaid = FeesPaid::currentSessionYear()->where('student_id', $student_id)->count();
                $paymentTransactions =PaymentTransaction::currentSessionYear()->where('student_id', $student_id)->count();
                $studentSubjects =StudentSubject::currentSessionYear()->where('student_id', $student_id)->count();
                $studentAttendance = StudentAttendance::currentSessionYear()->where('student_id', $student_id)->count();
                $paidInstallements = PaidInstallmentFee::currentSessionYear()->where('student_id', $student_id)->count();

                $counts = $assignments || $attendance || $examMarks || $examResults || $feesChoiceable || $feesPaid || $paymentTransactions || $studentSubjects || $studentAttendance || $paidInstallements;

                if (!$counts) {
                    $success++;

                    $studentSession = StudentSessions::where('student_id', $selected_student[$i])
                        ->where('session_year_id', $sessionYearId)
                        ->first();

                    if ($studentSession) {
                        $studentSession->class_section_id = $to_class_section_id;
                        $studentSession->save();
                    }
                } else {
                    $failure++;
                }

            }

            DB::commit();

            $response = [
                'error' => false,
                'message' => $success . " " .trans('data_store_successfully') . " and ". $failure . ' failed'
            ];
        } catch (Exception $e) {

            DB::rollBack();

            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            );
        }
        return response()->json($response);
    }

    public function indexStudentRollNumber()
    {
        if (!Auth::user()->can('student-create')) {
            $response = array('message' => trans('no_permission_message'));
            return redirect(route('home'))->withErrors($response);
        }
        $class_section = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        return view('students.assign_roll_no', compact('class_section'));
    }

    public function listStudentRollNumber(Request $request)
    {
        if (!Auth::user()->can('student-create')) {
            $response = array('message' => trans('no_permission_message'));
            return redirect(route('home'))->withErrors($response);
        }
        try {
            if (!Auth::user()->can('student-list')) {
                $response = array('message' => trans('no_permission_message'));
                return response()->json($response);
            }
            $class_section_id = $request->class_section_id;
            $sql = User::with('student')->orderBy('first_name', 'ASC');
            $sql = $sql->whereHas('student', function ($q) use ($class_section_id) {
                $q->whereHas('studentSessions', function ($query) use ($class_section_id) {
                    $query->where('session_year_id', getSettings('session_year')['session_year'])
                        ->where('class_section_id', $class_section_id);
                });
            });

            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = $_GET['search'];
                $sql->where('first_name', 'LIKE', "%$search%")->orwhere('last_name', 'LIKE', "%$search%")->orwhere('email', 'LIKE', "%$search%")->orwhere('dob', 'LIKE', "%$search%")->orWhereHas('student', function ($q) use ($search) {
                    $q->where('id', 'LIKE', "%$search%")->orWhere('user_id', 'LIKE', "%$search%")->orWhere('class_section_id', 'LIKE', "%$search%")->orWhere('admission_no', 'LIKE', "%$search%")->orWhere('admission_date', 'LIKE', date('Y-m-d', strtotime("%$search%")))->orWhereHas('user', function ($q) use ($search) {
                    });
                });
            }
            if ($request->sort_by == 'first_name') {
                $sql = $sql->orderBy('first_name', 'ASC');
            }
            if ($request->sort_by == 'last_name') {
                $sql = $sql->orderBy('last_name', 'ASC');
            }
            $total = $sql->count();

            $res = $sql->get();


            $bulkData = array();
            $bulkData['total'] = $total;
            $rows = array();
            $tempRow = array();
            $no = 1;
            $data = getSettings('date_formate');
            $roll = 1;
            $index = 0;
            foreach ($res as $row) {
                $tempRow['no'] = $no++;
                $tempRow['student_id'] = $row->student->id;
                $tempRow['old_roll_number'] = $row->student->roll_number;
                $tempRow['new_roll_number'] = "<input type='hidden' name='roll_number_data[" . $index . "][student_id]' class='form-control' readonly value=" . $row->student->id . "> <input type='hidden' name='roll_number_data[" . $index . "][roll_number]' class='form-control' value=" . $roll . ">" . $roll;
                $tempRow['user_id'] = $row->id;
                $tempRow['first_name'] = $row->first_name;
                $tempRow['last_name'] = $row->last_name;
                $tempRow['dob'] = date($data['date_formate'], strtotime($row->dob));
                $tempRow['image'] = $row->image;
                $tempRow['admission_no'] = $row->student->admission_no;
                $tempRow['admission_date'] = date($data['date_formate'], strtotime($row->student->admission_date));
                $rows[] = $tempRow;
                $index++;
                $roll++;
            }

            if ($request->get('print')) {
                $class_section = ClassSection::find($request->class_section_id);

                $pdf = StudentPrints::getInstance(get_center_id(), 'L');

                $pdf->printStudentRollNoList($rows, $class_section);

                return response(
                    $pdf->Output('', 'STUDENT ROLL NO LIST.pdf'),
                    200,
                    [
                        'Content-Type' => 'application/pdf'
                    ]
                );
            }


            $bulkData['rows'] = $rows;
            return response()->json($bulkData);
        } catch (Exception $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            );
            return response()->json($response);
        }
    }

    public function storeStudentRollNumber(Request $request)
    {
        if (!Auth::user()->can('student-create')) {
            $response = array('message' => trans('no_permission_message'));
            return redirect(route('home'))->withErrors($response);
        }
        $request->validate([
            'roll_number_data' => 'required',
        ],
            [
                'roll_number_data.required' => 'No students found',
            ]
        );

        $validator = Validator::make($request->all(), ['roll_number_data.*.roll_number' => 'required',], ['roll_number_data.*.roll_number.required' => trans('please_fill_all_roll_numbers_data')]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first()
            );
            return response()->json($response);
        }

        if (empty($request->roll_number_data)) {
            $response = array(
                'error' => true,
                'message' => "No Data Found"
            );
            return response()->json($response);
        }
        $i = 1;
        foreach ($request->roll_number_data as $data) {
            $student = Students::find($data['student_id']);

            // validation required when the edit of roll number is enabled

            // $class_roll_number_data = Students::where(['class_section_id' => $student->class_section_id,'roll_number' => $data['roll_number']])->whereNot('id',$data['student_id'])->count();
            // if(isset($class_roll_number_data) && !empty($class_roll_number_data)){
            //     $response = array(
            //         'error' => true,
            //         'message' => trans('roll_number_already_exists_of_number').' - '.$i
            //     );
            //     return response()->json($response);
            // }


            $student->roll_number = $data['roll_number'];
            $student->save();
            $i++;
        }
        $response = [
            'error' => false,
            'message' => trans('data_store_successfully')
        ];
        return response()->json($response);
    }

    public function generate_id_card()
    {
        if (!Auth::user()->can('student-id-card')) {
            $response = array('message' => trans('no_permission_message'));
            return redirect(route('home'))->withErrors($response);
        }

        // $student_id_fields = getSettings('student_id_fields');
        // $student_id_fields = explode(",",$student_id_fields['student_id_fields']);


        // return $students = Students::where('class_section_id',14)->get()->first()->full_name;


        // return $students = Students::where('class_section_id',14)->get()->first()->full_name;

        $class_section = ClassSection::owner()->with('class.stream', 'section')->whereHas('class', function ($q) {
            $q->activeMediumOnly();
        })->get();

        return view('students.generate_id_card', compact('class_section'));
    }

    public function view_generate_id_card(Request $request)
    {

        $request->validate(['user_id' => 'required'], ['user_id.required' => trans('please_select_row')]);
        $user_id = $request->user_id;
        $user_ids = explode(',', $user_id);
        $logo = url('storage/logo.png');
        $session_year = getSettings('session_year');
        $session_year = SessionYear::select('name', 'end_date')->find($session_year['session_year']);
        $students = Students::whereIn('user_id', $user_ids)->get();
        $logo = getSettings('logo1');
        if ($logo) {
            $logo = 'storage/' . $logo['logo1'];
        } else {
            $logo = 'storage/school_logo.png';
        }

        $school_name = getSettings('school_name');
        if ($school_name) {
            $school_name = $school_name['school_name'];
        } else {
            $school_name = 'YOUR SCHOOL NAME';
        }
        $water_mark = getSettings('water_mark');
        if (count($water_mark)) {
            $water_mark = 'storage/' . $water_mark['water_mark'];
        } else {
            $water_mark = '';
        }

        $school_detail = [
            'name' => strtoupper($school_name),
            'logo' => $logo,
            'session_year' => $session_year->name,
            'valid' => date('F d, Y', strtotime($session_year->end_date)),
            'water_mark' => $water_mark
        ];

        $header_color = getSettings('header_color');
        if ($header_color) {
            $header_color = $header_color['header_color'];
        } else {
            $header_color = '#015b89';
        }

        $footer_color = getSettings('footer_color');
        if ($footer_color) {
            $footer_color = $footer_color['footer_color'];
        } else {
            $footer_color = '#00b4db';
        }

        $text_color = getSettings('text_color');
        if ($text_color) {
            $font_color = $text_color['text_color'];
        } else {
            $font_color = '#ffffff';
        }

        $student_id_fields = getSettings('student_id_fields');
        if ($student_id_fields) {
            $student_id_fields = explode(",", $student_id_fields['student_id_fields']);
        } else {
            $student_id_fields = explode(",", "full_name,class_name,roll_number,admission_no,session_year");
        }

        $initial_code = getSettings('initial_code');
        $initial_code = $initial_code['initial_code'] ?? "";

        $counter = 0;
        $pdf = PDF::loadView('students.id_card_pdf', compact('students', 'school_detail', 'logo', 'counter', 'header_color', 'footer_color', 'font_color', 'student_id_fields', 'initial_code'));
        // $pdf->setPaper('B7','landscape');
        $pdf->setOptions(['page-width' => 89, 'page-height' => 51]);
        $pdf->setOptions(['margin-top' => 0, 'margin-left' => 0, 'margin-bottom' => 0, 'margin-right' => 0]);
        return $pdf->stream();

        return view('students.id_card_pdf', compact('students', 'school_detail', 'logo', 'counter', 'header_color', 'footer_color', 'font_color', 'student_id_fields', 'initial_code'));
    }

    public function student_sample_file()
    {
        return Excel::download(new StudentDummayFile(), 'student_sample_file.xlsx');
    }

    public function school_certificate($id)
    {
        try {
            $certificate_file = getSettings('certificate_file');
            if ($certificate_file) {
                $student_session = StudentSessions::find($id);
                $student = Students::find($student_session->student_id);
                $date_formate = getSettings('date_formate');

                $class_section = $student_session->class_section->full_name;

                $dob = date($date_formate['date_formate'], strtotime($student->user->dob));
                $admission_date = date($date_formate['date_formate'], strtotime($student->admission_date));
                $gr_numner = $student->admission_no;
                $session_year = SessionYear::find($student_session->session_year_id)->name;

                $issue_date = date($date_formate['date_formate'], strtotime(Carbon::now()));

                $certificate_file = $certificate_file['certificate_file'];

                // $certificate_file = str_replace('public','',$certificate_file);
                // $certificate_file_url = public_path('storage/'.$certificate_file);


                $school_certificate = new TemplateProcessor($certificate_file);
                $school_certificate->setValue('student_name', $student->user->full_name);
                $school_certificate->setValue('dob', $dob);
                $school_certificate->setValue('session_year', $session_year);
                $school_certificate->setValue('class_section', $class_section);
                $school_certificate->setValue('gr_number', $gr_numner);
                $school_certificate->setValue('admission_date', $admission_date);
                $school_certificate->setValue('issue_date', $issue_date);

                // $school_certificate->saveAs('student-certificate.docx');


                $school_certificate->saveAs('student-certificate.docx');
                return response()->download('student-certificate.docx');
            } else {
                return redirect()->back()->withErrors('No certificate found');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors('No certificate found');
        }
    }

    public function get_student(Request $request)
    {
        $student = Students::query()->select('id', 'user_id')->with('user:id,first_name,last_name')
            ->where('class_section_id', $request->class_section_id)->Owner()->get()->makeHidden([
                'class_name',
                'session_year'
        ]);

        $response = [
            'error' => false,
            'message' => 'Data fetch successfully',
            'data' => $student
        ];
        return response()->json($response);
    }

    public function boys_girls_counter()
    {
        $sessionYearId = getSettings('session_year')['session_year'];

        $class_school = ClassSchool::Owner()->activeMediumOnly()->withCount(['male_student' => function ($q) use ($sessionYearId) {

            $q->whereHas('studentSessions', function ($query) use ($sessionYearId) {
                $query->where('session_year_id', $sessionYearId);
                $query->where('active', true);
            });

            $q->Owner()->whereHas('user', function ($q) {
                $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
            });
        }])
            ->withCount(['female_student' => function ($q) use ($sessionYearId) {

                $q->whereHas('studentSessions', function ($query) use ($sessionYearId) {
                    $query->where('session_year_id', $sessionYearId);
                    $query->where('active', 1);
                });

                $q->Owner()->whereHas('user', function ($q) {
                    $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                });
            }])->get();

        $class_name = $class_school->pluck('name');
        $male_students = $class_school->pluck('male_student_count');
        $female_students = $class_school->pluck('female_student_count');

        $filter_by_group_number = filterClassData($class_name, $male_students, $female_students);
        $class_name = $filter_by_group_number[0];
        $male_students = $filter_by_group_number[1];
        $female_students = $filter_by_group_number[2];

        // --------------------Attendance------------------------//
        $class_school = ClassSchool::Owner()->activeMediumOnly()->get()->append(['attendance']);

        $boys_attendance = $class_school->pluck('attendance.boys_attendance');
        $girls_attendance = $class_school->pluck('attendance.girls_attendance');
        $attendance_class_name = $class_school->pluck('name');

        $data = [
            'error' => false,
            'message' => 'Data fetch successfully',
            'class_name' => $class_name,
            'male_student_count' => $male_students,
            'female_student_count' => $female_students,
            'attendance_class' => $attendance_class_name,
            'boys_attendance' => $boys_attendance,
            'girls_attendance' => $girls_attendance
        ];

        return response()->json($data);
    }


    public function boys_girls_class_group_wise(Request $request): \Illuminate\Http\JsonResponse
    {
        $class_school = ClassSchool::Owner()->activeMediumOnly()->withCount(['male_student' => function ($q) {
            $q->Owner()->whereHas('user', function ($q) {
                $q->whereIn('gender', ['Male', 'M'])->where('status', 1);
            });
        }])
            ->withCount(['female_student' => function ($q) {
                $q->Owner()->whereHas('user', function ($q) {
                    $q->whereIn('gender', ['Female', 'F'])->where('status', 1);
                });
            }])->whereHas('class_group', function ($q) use ($request) {
                $q->where('group_id', $request->class_group_id);
            })->get();

        $class_name = $class_school->pluck('name');
        $male_students = $class_school->pluck('male_student_count');
        $female_students = $class_school->pluck('female_student_count');

        $data = [
            'error' => false,
            'message' => 'Data fetch successfully',
            'class_name' => $class_name,
            'male_student_count' => $male_students,
            'female_student_count' => $female_students,
        ];

        return response()->json($data);
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $student = Students::find($id);
        $formFields = FormField::Owner()->get();
        $class_section = ClassSection::Owner()->get();

        $initial_code = getSettings('initial_code');

        $initial_code = $initial_code['initial_code'] ?? "";

        return view('students.edit', compact('student', 'formFields', 'class_section', 'initial_code'));
    }

    public function printStudentStats(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $pdf = StudentPrints::getInstance(get_center_id(), 'L', "STUDENT SEX AGE STATS");

        $pdf->printSexAgeStats();

        return response(
            $pdf->Output('', 'STUDENT SEX AGE STATISTICS.pdf'),
            200,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }


    public function printGroupedStudentStats(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $groupId = isset($request->groupId) ? $request->groupId : null;

        $pdf = StudentPrints::getInstance(get_center_id(), 'L', "STUDENT SEX AGE STATS");

        $pdf->printGroupedSexAgeStats($groupId);

        return response(
            $pdf->Output('', 'STUDENT SEX AGE STATISTICS.pdf'),
            200,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }

    public function printStudentSexStats(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $pdf = StudentPrints::getInstance(get_center_id(), 'L', "STUDENT SEX STATS");

        $pdf->printSexStats();

        return response(
            $pdf->Output('', 'STUDENT SEX STATISTICS.pdf'),
            200,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }

    public function printGroupedStudentSexStats(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $groupId = $request->groupId;

        $pdf = StudentPrints::getInstance(get_center_id(), 'L', "STUDENT SEX STATS");

        $pdf->printGroupedSexStats($groupId);

        return response(
            $pdf->Output('', trans('student_sex_statistics') . '.pdf'),
            200,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }

    public function getStudentCounts(Request $request): \Illuminate\Http\JsonResponse
    {
        $sessionYearId = getSettings('session_year')['session_year'];
        $studentStatus = $request->student_status ?? 0;
        $classSectionId = $request->input('class_section_id');

        $students = Students::with('user', 'class_section')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->Owner()->ofTeacher()->whereHas('class_section.class', function ($q) {
                $q->activeMediumOnly();
            })
            ->whereHas('studentSessions', function ($query) use ($classSectionId, $studentStatus, $sessionYearId) {
                $query->where('session_year_id', $sessionYearId)
                    ->where('active', $studentStatus)
                    ->when(Str::length($classSectionId) > 0, function ($query) use ($classSectionId) {
                        $query->where('class_section_id', $classSectionId);
                    });
            });

        $boysCount = $students->clone()->whereHas('user', function ($query) {
            $query->whereIn('gender', ['Male', 'male', 'M'])->orWhere('gender', 'LIKE', 'M%');
        })->count();

        $girlsCount = $students->clone()->whereHas('user', function ($query) {
            $query->whereIn('gender', ['Female', 'female', 'F'])->orWhere('gender', 'LIKE', 'F%');
        })->count();

        $total = $students->count();

        // Return counts as JSON
        return response()->json([
            'girls' => $girlsCount,
            'boys' => $boysCount,
            'total' => $total,
        ]);
    }

    public function updateStudentNamesUpperCase(): \Illuminate\Http\JsonResponse
    {
        try {
            // Start transaction for data consistency
            DB::beginTransaction();

            // Process in chunks to handle large datasets efficiently
            Students::with('user')
                ->chunk(1000, function ($students) {
                    foreach ($students as $student) {
                        if ($student->user) {
                            $student->user->update([
                                'first_name' => strtoupper($student->user->first_name)
                            ]);
                        }
                    }
                });

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'All student names have been updated to uppercase successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating names: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request): \Illuminate\Http\JsonResponse {
        if (!Auth::user()->can('student-delete')) {
            return response()->json([
                'error' => true,
                'message' => trans('no_permission_message')
            ]);
        }

        $validator = Validator::make($request->all(), [
            'ids' => 'required|string'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => $validator->errors()
            ]);
        }

        $userIds = trim($request->ids);

        // Convert comma-separated string to array and remove any empty values
        $userIdArray = array_filter(explode(',', $userIds));

        if (empty($userIdArray)) {
            return response()->json([
                'error' => true,
                'message' => trans('no_valid_ids_provided')
            ]);
        }

        try {
            DB::beginTransaction();

            // Get all student IDs corresponding to the user IDs
            $studentIds = Students::query()
                ->select('id')
                ->whereIn('user_id', $userIdArray)
                ->pluck('id')
                ->toArray();

            // If no students found for the given user IDs
            if (empty($studentIds)) {
                DB::rollBack();
                return response()->json([
                    'error' => true,
                    'message' => trans('no_students_found')
                ]);
            }

            $failedDeletions = [];

            foreach ($studentIds as $studentId) {
                try {
                    // Delete student data for current session
                    $this->deleteStudentDataForCurrentSession($studentId);

                    // Clean student if not exist
                    $this->cleanStudentIfNotExist($studentId);

                } catch (Throwable $e) {
                    $failedDeletions[] = $studentId;
                    return response()->json([
                        'error' => false,
                        'message' => 'Failed to delete student ID: ' . $studentId . '. Error: ' . $e->getMessage()
                    ]);
                }
            }

            // If any deletions failed, rollback the entire transaction
            if (!empty($failedDeletions)) {
                DB::rollBack();
                return response()->json([
                    'error' => true,
                    'message' => trans('batch_delete_failed'),
                    'failed_deletions' => $failedDeletions
                ]);
            }

            // Implement background logic to recalculate student marks
            DB::commit();

            return response()->json([
                'error' => false,
                'message' => trans('batch_delete_successful'),
                'deleted_count' => count($studentIds)
            ]);

        } catch (Throwable $e) {
            DB::rollBack();
            \Log::error('Batch delete failed. Error: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => trans('error_occurred')
            ]);
        }
    }

}
