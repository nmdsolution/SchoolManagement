<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Center;
use App\Models\Settings;
use App\Models\ReportCard;
use App\Models\SessionYear;
use App\Models\CenterReport;
use Illuminate\Http\Request;
use App\Models\EffectiveDomain;
use Rawilk\Settings\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Rawilk\Settings\Support\Context;

class SettingController extends Controller
{

    public function index()
    {

        if (!Auth::user()->can('setting-create')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }

        $settings = getSettings();
        $getDateFormat = getDateFormat();
        $getTimezoneList = getTimezoneList();
        $getTimeFormat = getTimeFormat();

        $settings['global_report_minimum_coefficient_percentage'] = settings()
            ->context(new Context(['user_id' => get_center_id()]))
            ->get('global_report_minimum_coefficient_percentage', 80)
            ;

        $session_year = SessionYear::where('center_id', get_center_id())->orderBy('id', 'desc')->get();
        $student_id_fields = '';

        try {
            if ($settings['student_id_fields']) {
                $student_id_fields = explode(",", $settings['student_id_fields']);
            }
        } catch (\Throwable $th) {
        }
        return view('settings.index', compact('settings', 'getDateFormat', 'getTimezoneList', 'getTimeFormat', 'session_year', 'student_id_fields'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'required|max:255',
            'school_email' => 'required|email',
            'school_phone' => 'required',
            'school_address' => 'required',
            'time_zone' => 'required',
            'theme_color' => 'required',
            'session_year' => 'required',
            'school_tagline' => 'required',
            'online_payment' => 'required|in:0,1',
            'certificate_file' => 'max:5120'
        ]);

        $settings = [
            'school_name',
            'school_email',
            'school_phone',
            'school_address',
            'time_zone',
            'date_formate',
            'time_formate',
            'theme_color',
            'session_year',
            'school_tagline',
            'online_payment',
            'header_color',
            'footer_color',
            'text_color',
            'student_id_fields',
            'initial_code',
            'student_honor_roll_text',
            // 'encouragement',
            // 'congratulations',
            // 'warning'
        ];
        try {
            foreach ($settings as $row) {
                if (!Auth::user()->teacher) {
                    if (Settings::where('type', $row)->where('center_id', Auth::user()->center->id)->exists()) {
                        if ($row == 'session_year') {
                            $get_id = Settings::select('message')->where('type', 'session_year')->where('center_id', Auth::user()->center->id)->pluck('message')->first();

                            $old_year = SessionYear::find($get_id);
                            $old_year->default = 0;
                            $old_year->save();

                            $session_year = SessionYear::find($request->$row);
                            $session_year->default = 1;
                            $session_year->save();
                        }

                        $data = [
                            'message' => $request->$row
                        ];
                        if ($row == 'student_id_fields') {
                            $data = [
                                'message' => implode(",", $request->$row)
                            ];
                        }

                        Settings::where('type', $row)->where('center_id', Auth::user()->center->id)->update($data);
                    } else {
                        if ($row == 'student_id_fields') {
                            $request->$row = implode(",", $request->$row);
                        }
                        $setting = new Settings();
                        $setting->type = $row;
                        $setting->message = $request->$row;
                        $setting->center_id = Auth::user()->center->id;
                        $setting->save();
                    }
                }
            }

            // for online payment data
            if (Settings::where('type', 'online_payment')->where('center_id', Auth::user()->center->id)->exists()) {
                $data = [
                    'message' => $request->online_payment
                ];
                Settings::where('type', 'online_payment')->where('center_id', Auth::user()->center->id)->update($data);
            } else {
                $setting = new Settings();
                $setting->type = 'online_payment';
                $setting->message = $request->online_payment;
                $setting->center_id = Auth::user()->center->id;
                $setting->save();
            }
            // end of online payment data

            if ($request->hasFile('logo1')) {
                if (Settings::where('type', 'logo1')->where('center_id', Auth::user()->center->id)->exists()) {
                    $get_id = Settings::select('message')->where('type', 'logo1')->pluck('message')->first();
                    if (Storage::disk('public')->exists($get_id)) {
                        Storage::disk('public')->delete($get_id);
                    }
                    $data = [
                        'message' => $request->file('logo1')->store('logo', 'public')
                    ];
                    Settings::where('type', 'logo1')->where('center_id', Auth::user()->center->id)->update($data);
                } else {
                    $setting = new Settings();
                    $setting->type = 'logo1';
                    $setting->message = $request->file('logo1')->store('logo', 'public');
                    $setting->center_id = get_center_id();
                    $setting->save();
                }
            }
            if ($request->hasFile('logo2')) {
                if (Settings::where('type', 'logo2')->where('center_id', Auth::user()->center->id)->exists()) {
                    $get_id = Settings::select('message')->where('type', 'logo2')->pluck('message')->first();
                    if (Storage::disk('public')->exists($get_id)) {
                        Storage::disk('public')->delete($get_id);
                    }
                    $data = [
                        'message' => $request->file('logo2')->store('logo', 'public')
                    ];
                    Settings::where('type', 'logo2')->where('center_id', Auth::user()->center->id)->update($data);
                } else {
                    $setting = new Settings();
                    $setting->type = 'logo2';
                    $setting->message = $request->file('logo2')->store('logo', 'public');
                    $setting->center_id = get_center_id();
                    $setting->save();
                }
            }
            if ($request->hasFile('favicon')) {
                if (Settings::where('type', 'favicon')->where('center_id', Auth::user()->center->id)->exists()) {
                    $get_id = Settings::select('message')->where('type', 'favicon')->pluck('message')->first();
                    if (Storage::disk('public')->exists($get_id)) {
                        Storage::disk('public')->delete($get_id);
                    }
                    $data = [
                        'message' => $request->file('favicon')->store('logo', 'public')
                    ];
                    Settings::where('type', 'favicon')->where('center_id', Auth::user()->center->id)->update($data);
                    $data = [
                        'logo' => $request->file('favicon')->store('logo', 'public')
                    ];
                    Center::where('id', get_center_id())->update($data);

                } else {
                    $setting = new Settings();
                    $setting->type = 'favicon';
                    $setting->message = $request->file('favicon')->store('logo', 'public');
                    $setting->center_id = get_center_id();
                    $setting->save();
                }
            }
            // Student ID card water mark
            if ($request->hasFile('water_mark')) {
                if (Settings::where('type', 'water_mark')->where('center_id', Auth::user()->center->id)->exists()) {
                    $get_id = Settings::select('message')->where('type', 'water_mark')->pluck('message')->first();
                    if (Storage::disk('public')->exists($get_id)) {
                        Storage::disk('public')->delete($get_id);
                    }
                    $data = [
                        'message' => $request->file('water_mark')->store('logo', 'public')
                    ];
                    Settings::where('type', 'water_mark')->where('center_id', Auth::user()->center->id)->update($data);
                } else {
                    $setting = new Settings();
                    $setting->type = 'water_mark';
                    $setting->message = $request->file('water_mark')->store('logo', 'public');
                    $setting->center_id = get_center_id();
                    $setting->save();
                }
            }

            // Certificate file
            if ($request->hasFile('certificate_file')) {
                if (Settings::where('type', 'certificate_file')->where('center_id', Auth::user()->center->id)->exists()) {
                    $get_id = Settings::select('message')->where('type', 'certificate_file')->pluck('message')->first();
                    if (Storage::disk('public')->exists($get_id)) {
                        Storage::disk('public')->delete($get_id);
                    }
                    File::delete($get_id);
                    $extension = $request->certificate_file->getClientOriginalExtension();
                    $filename = $request->certificate_file->getClientOriginalName();
                    
                    $file = $request->file('certificate_file');
                    $file->move(public_path('/'), $filename);
                    $data = [
                        'message' => $filename
                    ];
                    Settings::where('type', 'certificate_file')->where('center_id', Auth::user()->center->id)->update($data);
                } else {
                    $extension = $request->certificate_file->getClientOriginalExtension();
                    $filename = $request->certificate_file->getClientOriginalName();
                    $setting = new Settings();
                    $setting->type = 'certificate_file';
                    // $setting->message = $request->file('certificate_file')->storeAs('public/certificates',$filename);
                    // $setting->message = $request->file('certificate_file')->store('certificates','public');
                    $file = $request->file('certificate_file');
                    $file->move(public_path('/'), $filename);
                    $setting->message = $filename;
                    $setting->center_id = get_center_id();
                    $setting->save();
                }
            }
            // honor_roll_certificate_file
            if ($request->hasFile('honor_roll_certificate_file')) {
                if (Settings::where('type', 'honor_roll_certificate_file')->where('center_id', Auth::user()->center->id)->exists()) {
                    $get_id = Settings::select('message')->where('type', 'honor_roll_certificate_file')->pluck('message')->first();
                    if (Storage::disk('public')->exists($get_id)) {
                        Storage::disk('public')->delete($get_id);
                    }
                    File::delete($get_id);
                    $extension = $request->honor_roll_certificate_file->getClientOriginalExtension();
                    $filename = $request->honor_roll_certificate_file->getClientOriginalName();
                    // $data = [
                    //     'message' => $request->file('honor_roll_certificate_file')->storeAs('public/certificates',$filename)
                    // ];
                    // $data = [
                    //     'message' => $request->file('honor_roll_certificate_file')->store('certificates','public')
                    // ];
                    $file = $request->file('honor_roll_certificate_file');
                    $file->move(public_path('/'), $filename);
                    $data = [
                        'message' => $filename
                    ];
                    Settings::where('type', 'honor_roll_certificate_file')->where('center_id', Auth::user()->center->id)->update($data);
                } else {
                    $extension = $request->honor_roll_certificate_file->getClientOriginalExtension();
                    $filename = $request->honor_roll_certificate_file->getClientOriginalName();
                    $setting = new Settings();
                    $setting->type = 'honor_roll_certificate_file';
                    // $setting->message = $request->file('honor_roll_certificate_file')->storeAs('public/certificates',$filename);
                    // $setting->message = $request->file('honor_roll_certificate_file')->store('certificates','public');
                    $file = $request->file('honor_roll_certificate_file');
                    $file->move(public_path('/'), $filename);
                    $setting->message = $filename;
                    $setting->center_id = get_center_id();
                    $setting->save();
                }
            }

            if (Settings::where('type', 'auto_publish_exams')->where('center_id', Auth::user()->center->id)->exists()) {
                $data = [
                    'message' => isset($request->auto_publish_exams) ? 1 : 0
                ];
                Settings::where('type', 'auto_publish_exams')->where('center_id', Auth::user()->center->id)->update($data);
            } else {
                $extension = $request->certificate_file->getClientOriginalExtension();
                $filename = $request->certificate_file->getClientOriginalName();
                $setting = new Settings();
                $setting->type = 'auto_publish_exams';
                $setting->message = isset($request->auto_publish_exams) ? 1 : 0;
                $setting->center_id = Auth::user()->center->id;
                $setting->save();
            }

            
            if ($request->global_report_minimum_coefficient_percentage) {
                settings()->context(new Context(['user_id' => get_center_id()]))->set(
                    'global_report_minimum_coefficient_percentage', 
                    $request->global_report_minimum_coefficient_percentage
                );
            }

            //        $logo1 = Settings::select('message')->where('type', 'logo1')->pluck('message')->first();
            //        $logo2 = Settings::select('message')->where('type', 'logo2')->pluck('message')->first();
            //        $favicon = Settings::select('message')->where('type', 'favicon')->pluck('message')->first();
            //        $app_name = Settings::select('message')->where('type', 'school_name')->pluck('message')->first();
            //        $timezone = Settings::select('message')->where('type', 'time_zone')->pluck('message')->first();
            //            $env_update = changeEnv([
            //                'LOGO1' => $logo1,
            //                'LOGO2' => $logo2,
            //                'FAVICON' => $favicon,
            //                'APP_NAME' => "'" . $app_name . "'",
            //                'TIMEZONE' => "'" . $timezone . "'"
            //            ]);
            //        if ($env_update) {
            $response = array(
                'error' => false,
                'message' => trans('data_update_successfully'),
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
        // return redirect()->back()->with('success', trans('data_update_successfully'));
    }


    public function fcm_index()
    {

        $settings = Settings::where('type', 'fcm_server_key')->first();
        $type = 'fcm_server_key';
        return view('settings.fcm_key', compact('settings', 'type'));
    }

    public function email_index()
    {

        $settings = getSettings();
        return view('settings.email_configuration', compact('settings'));
    }

    public function email_update(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'required',
            'mail_host' => 'required',
            'mail_port' => 'required',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'mail_encryption' => 'required',
            'mail_send_from' => 'required|email',
        ]);

        $settings = [
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_send_from',
        ];

        try {
            foreach ($settings as $row) {
                if (Settings::where('type', $row)->exists()) {

                    $data = [
                        'message' => $request->$row
                    ];
                    Settings::where('type', $row)->update($data);
                } else {
                    $setting = new Settings();
                    $setting->type = $row;
                    $setting->message = $request->$row;
                    $setting->save();
                }
                Settings::updateOrInsert(
                    ['type' => 'email_configration_verification'],
                    ['type' => 'email_configration_verification', 'message' => 0]
                );
            }
            $env_update = changeEnv([
                'MAIL_MAILER' => $request->mail_mailer,
                'MAIL_HOST' => $request->mail_host,
                'MAIL_PORT' => $request->mail_port,
                'MAIL_USERNAME' => $request->mail_username,
                'MAIL_PASSWORD' => $request->mail_password,
                'MAIL_ENCRYPTION' => $request->mail_encryption,
                'MAIL_FROM_ADDRESS' => $request->mail_send_from

            ]);
            if ($env_update) {
                $response = array(
                    'error' => false,
                    'message' => trans('data_update_successfully'),
                );
            } else {
                $response = array(
                    'error' => false,
                    'message' => trans('error_occurred'),
                );
            }
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }

    public function verifyEmailConfigration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'verify_email' => 'required|email',
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
            );
            return response()->json($response);
        }
        try {
            $data = [
                'email' => $request->verify_email,
            ];
            $admin_mail = env('MAIL_FROM_ADDRESS');
            if (!filter_var($request->verify_email, FILTER_VALIDATE_EMAIL)) {
                $response = array(
                    'error' => true,
                    'message' => trans('invalid_email'),
                );
                return response()->json($response);
            }
            Mail::send('mail', $data, function ($message) use ($data, $admin_mail) {
                $message->to($data['email'])->subject('Connection Verified successfully');
                $message->from($admin_mail, 'Yadiko Admin');
            });

            Settings::where('type', 'email_configration_verification')->update(['message' => 1]);

            $response = array(
                'error' => false,
                'message' => trans('email_sent_successfully'),
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage()
            );
        }
        return response()->json($response);
    }

    public function privacy_policy_index()
    {

        $settings = Settings::where('type', 'privacy_policy')->first();
        $type = 'privacy_policy';
        return view('settings.privacy_policy', compact('settings', 'type'));
    }

    public function contact_us_index()
    {

        $settings = Settings::where('type', 'contact_us')->first();
        $type = 'contact_us';
        return view('settings.contact_us', compact('settings', 'type'));
    }

    public function about_us_index()
    {

        $settings = Settings::where('type', 'about_us')->first();
        $type = 'about_us';
        return view('settings.about_us', compact('settings', 'type'));
    }

    public function terms_condition_index()
    {

        $settings = Settings::where('type', 'terms_condition')->first();
        $type = 'terms_condition';
        return view('settings.terms_condition', compact('settings', 'type'));
    }

    public function setting_page_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'message' => 'required'
        ]);
        if ($validator->fails()) {
            $response = array(
                'error' => true,
                'message' => $validator->errors()->first(),
            );
            return response()->json($response);
        }
        $type = $request->type;
        $message = $request->message;
        $id = Settings::select('id')->where('type', $type)->pluck('id')->first();
        if (isset($id) && !empty($id)) {
            $setting = Settings::find($id);
            $setting->message = $message;
            $setting->save();
            $response = array(
                'error' => false,
                'message' => trans('data_update_successfully'),
            );
        } else {
            $setting = new Settings();
            $setting->type = $type;
            $setting->message = $message;
            $setting->save();
            $response = array(
                'error' => false,
                'message' => trans('data_store_successfully'),
            );
        }

        return response()->json($response);
    }

    public function app_index()
    {

        $settings = getSettings();
//        dd($settings);
        return view('settings.app_settings', compact('settings'));
    }

    public function app_update(Request $request)
    {
        $request->validate([
            'app_link' => 'nullable',
            'ios_app_link' => 'nullable',
            'app_version' => 'nullable',
            'ios_app_version' => 'nullable',
            'force_app_update' => 'nullable',
            'app_maintenance' => 'nullable',
            'teacher_app_link' => 'nullable',
            'teacher_ios_app_link' => 'nullable',
            'teacher_app_version' => 'nullable',
            'teacher_ios_app_version' => 'nullable',
            'teacher_force_app_update' => 'nullable',
            'teacher_app_maintenance' => 'nullable',
            'login_page_background' => 'nullable',
            'school_tagline' => 'nullable',
            'school_name' => 'nullable',
        ]);

        $settings = [
            'app_link',
            'ios_app_link',
            'app_version',
            'ios_app_version',
            'force_app_update',
            'app_maintenance',
            'teacher_app_link',
            'teacher_ios_app_link',
            'teacher_app_version',
            'teacher_ios_app_version',
            'teacher_force_app_update',
            'teacher_app_maintenance',
            'school_tagline',
            'school_name'
        ];

        try {

            foreach ($settings as $row) {

                if ($request->$row!="") {
                    if (Settings::where(['type'=> $row,'center_id'=>null])->exists()) {

                        $data = [
                            'message' => $request->$row
                        ];
                        Settings::where(['type'=> $row,'center_id'=>null])->update($data);
                    } else {
                        $setting = new Settings();
                        $setting->type = $row;
                        $setting->message = $request->$row;
                        $setting->save();
                    }
                }
                if ($request->hasFile('login_page_background')) {
                    $file = Settings::where(['type' => 'login_page_background','center_id'=>null])->first();
                    if (!empty($file)) {
                        if (Storage::disk('public')->exists($file->getRawOriginal('message'))) {
                            Storage::disk('public')->delete($file->getRawOriginal('message'));
                        }
                        $image = $request->file('login_page_background')->store('settings', 'public');
                        $file->message = $image;
                        $file->save();
                    } else {
                        $setting = new Settings();
                        $setting->type = 'login_page_background';
                        $setting->message = $request->file('login_page_background')->store('settings', 'public');
                        $setting->data_type = 'file';
                        $setting->save();
                    }
                }
            }

            $response = array(
                'error' => false,
                'message' => trans('data_update_successfully'),
            );
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e
            );
        }
        return response()->json($response);
    }

    public function reportSettingIndex()
    {
        $settings = getSettings(null, null, getCurrentMedium()->id);

        $effective_domain = EffectiveDomain::owner()->currentMedium()->orderBy('name', 'ASC')->get();

        return view('settings.report', compact('settings', 'effective_domain'));
    }

    public function reportSettingUpdate(Request $request)
    {

        // logger($request);
        $validator = Validator::make($request->all(), [
            'report_warning_min' => 'required|numeric',
            'report_warning_max' => 'required|numeric',
            'report_blame_min' => 'required|numeric',
            'report_blame_max' => 'required|numeric',
            'average_blame_min' => 'required|numeric',
            'average_blame_max' => 'required|numeric',
            'average_warning_min' => 'required|numeric',
            'average_warning_max' => 'required|numeric',
            'encouragement_min' => 'required|numeric',
            'encouragement_max' => 'required|numeric',
            'congratulations_min' => 'required|numeric',
            'congratulations_max' => 'required|numeric',
            'report_honor_roll' => 'required|numeric',
            'report_honor_roll_absences' => 'required|numeric',
            'report_low_subject_average' => 'required|numeric',
            'report_color' => 'required|string',
            'marks_font_size' => 'nullable|numeric',
            'teacher_name_font_size' => 'nullable|numeric',
            'subject_font_size' => 'nullable|numeric',
            'competence_font_size' => 'nullable|numeric',
            'marks_font_style' => 'nullable|string',
            'teacher_name_font_style' => 'nullable|string',
            'subject_font_style' => 'nullable|string',
            // 'report_date_generated' => 'required|boolean',
            'report_header_logo' => 'nullable|image',
            'report_water_mark' => 'nullable|image',
            'report_left_header' => 'nullable|string',
            'report_right_header' => 'nullable|string',
            'effective_domain' => 'array',
            'report_layout_type' => 'nullable|in:0,1',
            'subject_group_style' => 'nullable|string',
            'discipline_master_signature' => 'boolean',
            'council_decision' => 'nullable|boolean',
            'remove_water_mark' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            $response = [
                'error' => true,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response);
        }

        try {
            $settings = [];
            $centerId = Auth::user()->center->id;

            $disciplineMasterSignature = $request->has('discipline_master_signature') ? $request->discipline_master_signature : 0;
            $councilDecision = $request->has('council_decision') ? $request->council_decision : 0;
    
            $settings[] = [
                'type' => 'discipline_master_signature',
                'message' => $disciplineMasterSignature,
                'center_id' => $centerId,
                'data_type' => 'text',
                'medium_id' => getCurrentMedium()->id,
            ];
    
            $settings[] = [
                'type' => 'council_decision',
                'message' => $councilDecision,
                'center_id' => $centerId,
                'data_type' => 'text',
                'medium_id' => getCurrentMedium()->id,
            ];

            // Handle file inputs separately
            if ($request->hasFile('report_header_logo')) {
                $logoFile = Settings::where(['type' => 'report_header_logo', 'center_id' => $centerId])->first();
                
                if ($logoFile) {
                    if (Storage::disk('public')->exists($logoFile->getRawOriginal('message'))) {
                        Storage::disk('public')->delete($logoFile->getRawOriginal('message'));
                    }
            
                    $logoFile->delete();
                }
                $logoPath = $request->file('report_header_logo')->store('settings', 'public');
                $settings[] = [
                    'type' => 'report_header_logo',
                    'message' => $logoPath,
                    'center_id' => $centerId,
                    'data_type' => 'file',
                    'medium_id' => getCurrentMedium()->id,
                ];
            }

            if ($request->input('remove_water_mark') == 1) {
                $waterMarkFile = Settings::where(['type' => 'report_water_mark', 'center_id' => $centerId])->first();
            
                if ($waterMarkFile) {
                    if (Storage::disk('public')->exists($waterMarkFile->getRawOriginal('message'))) {
                        Storage::disk('public')->delete($waterMarkFile->getRawOriginal('message'));
                    }
                    $waterMarkFile->delete();
            
                    $settings[] = [
                        'type' => 'report_water_mark',
                        'message' => null,
                        'center_id' => $centerId,
                        'data_type' => 'file',
                        'medium_id' => getCurrentMedium()->id,
                    ];
                }
            }

            if ($request->hasFile('report_water_mark')) {
                $waterMarkFile = Settings::where(['type' => 'report_water_mark', 'center_id' => $centerId])->first();
            
                if ($waterMarkFile) {
                    if (Storage::disk('public')->exists($waterMarkFile->getRawOriginal('message'))) {
                        Storage::disk('public')->delete($waterMarkFile->getRawOriginal('message'));
                    }
            
                    $waterMarkFile->delete();
                }
            
                $waterMarkPath = $request->file('report_water_mark')->store('settings', 'public');
                $settings[] = [
                    'type' => 'report_water_mark',
                    'message' => $waterMarkPath,
                    'center_id' => $centerId,
                    'data_type' => 'file',
                    'medium_id' => getCurrentMedium()->id,
                ];
            }
            

            // Handle non-file inputs
            foreach ($validator->validated() as $type => $message) {
                if (in_array($type, ['report_water_mark', 'report_header_logo', 'effective_domain'])) {
                    continue;
                }
                $settings[] = [
                    'type' => $type,
                    'message' => $message,
                    'center_id' => $centerId,
                    'data_type' => 'text',
                    'medium_id' => getCurrentMedium()->id,
                ];
            }

            Settings::query()->upsert($settings, ['type', 'center_id', 'medium_id'], ['message']);

            // Handle effective domains
            if (isset($request->effective_domain)) {
                $effectiveDomains = [];
                $mediumId = getCurrentMedium()->id;
                foreach ($request->effective_domain as $row) {
                    $effectiveDomains[] = [
                        'name' => $row,
                        'center_id' => $centerId,
                        'medium_id' => $mediumId,
                    ];
                }
                EffectiveDomain::query()
                    ->where('center_id', $centerId)
                    ->where('medium_id', $mediumId)
                    ->delete();
                EffectiveDomain::query()->insert($effectiveDomains);
            }

            $response = [
                'error' => false,
                'message' => trans('data_update_successfully'),
            ];
        } catch (\Throwable $e) {
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
                'data' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }


    public function destroyEffectiveDomain($id)
    {
        try {
            $effective_domain = EffectiveDomain::where(['center_id' => Auth::user()->center->id, 'id' => $id])->firstOrFail();
            $effective_domain->delete();
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

    public function changeSessionYear(Request $request) {
        $request->validate([
            'session_year' => 'required|numeric',
        ]);

        $old_year = SessionYear::owner()->where('default', 1)->get()->first();

        if ($old_year) {
            $year = SessionYear::find($old_year->id);

            $year->default = 0;
            $year->save();
        }

        $session_year = SessionYear::find($request->session_year);
        $session_year->default = 1;
        $session_year->save();

        Settings::where('type', 'session_year')->where('center_id', Auth::user()->center->id)->update([
            'message' => $request->session_year
        ]);

        return response()->json([
            'status' => true,
        ]);
    }
}
