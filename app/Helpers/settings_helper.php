<?php

use App\Models\Center;
use App\Models\CenterTeacher;
use App\Models\ExamMarks;
use App\Models\ExamReport;
use App\Models\ExamSequence;
use App\Models\ExamTerm;
use App\Models\Grade;
use App\Models\Language;
use App\Models\Mediums;
use App\Models\SessionYear;
use App\Models\Settings;
use App\Models\Students;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\Permission\Models\Role;

function getSettings($type = null, $center_id = null, $mediumId = null): array
{

    if (Auth::user()) {
        $settingList = array();
        $center_id = $center_id ?? get_center_id();
        if (!is_array($center_id)) {
            $center_id = (array)$center_id;
        }

        if ($type != '') {
            if (is_array($type)) {
                $setting = Settings::whereIn('type', $type)->whereIn('center_id', $center_id)->where('medium_id', $mediumId)->get();
            } else {
                $setting = Settings::where('type', $type)->whereIn('center_id', $center_id)->where('medium_id', $mediumId)->get();
            }
        } else {
            if (Auth::user()->hasRole('Super Admin')) {
                $setting = Settings::where('center_id', null)->get();
            } else {
                $setting = Settings::query()->whereIn('center_id', $center_id)->where('medium_id', $mediumId)->get();
            }
        }
    } else {
        $settingList = array();
        $setting = Settings::where('center_id', null)->where('medium_id', $mediumId);
        if ($type != '') {
            $setting = is_array($type) ? $setting->whereIn('type', $type) : $setting->where('type', $type);
        }
        $setting = $setting->get();
    }
    foreach ($setting as $row) {
        $settingList[$row->type] = $row->message;
    }
    return $settingList;
}

function assign_roll_number($class_section): void
{
    if ($class_section) {
        $students = User::select('id')->whereHas('student', function ($q) use ($class_section) {
            $q->where('class_section_id', $class_section);
        })->orderBy('first_name')->get()->makeHidden('full_name');
        $roll_number = 1;
        foreach ($students as $key => $student) {
            $student = Students::where('user_id', $student->id)->first();
            $student->roll_number = $roll_number++;
            $student->save();
        }
    }
}

function current_language(): void
{
    if (Session::get('language') == 'en' || Session::get('language') == 'fr') {
        $lang = Session::get('language');
        Session::put('language', $lang);
        Session::put('locale', $lang);
        app()->setLocale(Session::get('locale'));
    } else {
        $lang = 'en';
        Session::put('language', $lang);
        Session::put('locale', $lang);
        app()->setLocale(Session::get('locale'));
    }
}

function get_language() {
    return Language::get();
}

function active_center($type) {

    if (Session()->get('center_id') != -1) {
        // $center_id = session()->get('center_id');
        $center_id = get_center_id();
        if ($center_id) {
            if ($type == 'logo') {
                $setting = Settings::where('center_id', $center_id)->where('type', 'favicon')->first();
                if ($setting) {
                    return url(Storage::url($setting->message));
                }
            } else {
                return $center = Center::select('name', 'logo')->find($center_id)->{$type};
            }
        } else {
            return '';
        }
    } else {
        return 'Super Admin Panel';
    }
}

function user_active_center($type) {
    if (Session()->get('center_id') != -1) {
        $center_id = session()->get('center_id');
        if (isset($center_id)) {
            return $center = Center::select('name', 'logo')->find($center_id)->{$type};
        }
        return '';
    }
    return 'Super Admin Panel';
}

function get_teacher_center() {
    return CenterTeacher::with('center:id,name,logo')->where('teacher_id', Auth::user()->teacher->id)->has('center')->get();
}

function get_user_center() {
    return Center::select('id', 'name', 'logo')->whereIn('id', Auth::user()->staff->pluck('center_id'))->get();
}

function getTimeFormat() {
    $timeFormat = array();
    $timeFormat['h:i a'] = 'h:i a - ' . date('h:i a');
    $timeFormat['h:i A'] = 'h:i A - ' . date('h:i A');
    $timeFormat['H:i'] = 'H:i - ' . date('H:i');
    return $timeFormat;
}

function getDateFormat() {
    $dateFormat = array();
    $dateFormat['d/m/Y'] = 'd/m/Y - ' . date('d/m/Y');
    $dateFormat['m/d/Y'] = 'm/d/Y - ' . date('m/d/Y');
    $dateFormat['Y/m/d'] = 'Y/m/d - ' . date('Y/m/d');
    $dateFormat['Y/d/m'] = 'Y/d/m - ' . date('Y/d/m');
    $dateFormat['m-d-Y'] = 'm-d-Y - ' . date('m-d-Y');
    $dateFormat['d-m-Y'] = 'd-m-Y - ' . date('d-m-Y');
    $dateFormat['Y-m-d'] = 'Y-m-d - ' . date('Y-m-d');
    $dateFormat['Y-d-m'] = 'Y-d-m - ' . date('Y-d-m');
    $dateFormat['F j, Y'] = 'F j, Y - ' . date('F j, Y');
    $dateFormat['jS F Y'] = 'jS F Y - ' . date('jS F Y');
    $dateFormat['l jS F'] = 'l jS F - ' . date('l jS F');
    $dateFormat['d M, y'] = 'd M, y - ' . date('d M, y');
    return $dateFormat;
}

function getTimezoneList() {
    static $timezones = null;

    if ($timezones === null) {
        $list = DateTimeZone::listAbbreviations();
        $idents = DateTimeZone::listIdentifiers();

        $data = $offset = $added = array();
        foreach ($list as $abbr => $info) {
            foreach ($info as $zone) {
                if (!empty($zone['timezone_id']) and !in_array($zone['timezone_id'], $added) and in_array($zone['timezone_id'], $idents)) {
                    $z = new DateTimeZone($zone['timezone_id']);
                    $c = new DateTime('', $z);
                    $zone['time'] = $c->format('H:i a');
                    $offset[] = $zone['offset'] = $z->getOffset($c);
                    $data[] = $zone;
                    $added[] = $zone['timezone_id'];
                }
            }
        }

        array_multisort($offset, SORT_ASC, $data);
        $i = 0;
        $temp = array();
        foreach ($data as $key => $row) {
            $temp[0] = $row['time'];
            $temp[1] = formatOffset($row['offset']);
            $temp[2] = $row['timezone_id'];
            $timezones[$i++] = $temp;
        }
    }
    return $timezones;
}

function formatOffset($offset) {
    $hours = $offset / 3600;
    $remainder = $offset % 3600;
    $sign = $hours > 0 ? '+' : '-';
    $hour = (int)abs($hours);
    $minutes = (int)abs($remainder / 60);

    if ($hour == 0 and $minutes == 0) {
        $sign = ' ';
    }
    return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0');
}

function flattenMyModel($model) {
    $modelArr = $model->toArray();
    $data = [];
    array_walk_recursive($modelArr, function ($item, $key) use (&$data) {
        $data[$key] = $item;
    });
    return $data;
}

function changeEnv($data = array()) {
    if (count($data) > 0) {

        // Read .env-file
        $env = file_get_contents(base_path() . '/.env');
        // Split string on every " " and write into array
        $env = explode(PHP_EOL, $env);
        // $env = preg_split('/\s+/', $env);
        foreach ($env as $env_key => $env_value) {
            $entry = explode("=", $env_value);
            $temp_env_keys[] = $entry[0];
        }
        // Loop through given data
        foreach ((array)$data as $key => $value) {
            $key_value = $key . "=" . $value;

            if (in_array($key, $temp_env_keys)) {
                // Loop through .env-data
                foreach ($env as $env_key => $env_value) {
                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value);
                    // // Check, if new key fits the actual .env-key
                    if ($entry[0] == $key) {
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . str_replace('"', '', $value);
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            } else {
                $env[] = $key_value;
            }
        }
        // Turn the array back to an String
        $env = implode("\n", $env);

        // And overwrite the .env with the new data
        file_put_contents(base_path() . '/.env', $env);

        return true;
    } else {
        return false;
    }
}

function findExamGrade($percentage, $getAllData = false) {
    $grades = Grade::owner()->currentMedium()->get();

    if (sizeof($grades)) {
        foreach ($grades as $row) {
            if (floor($percentage) >= $row['starting_range'] && floor($percentage) <= $row['ending_range']) {
                if ($getAllData) {
                    return $row;
                }

                return $row->grade;
            }
        }
    } else {
        return '';
    }
}

function student_present($exam_report_id, $student_id) {
    // return $exam_report_id .' - '. $student_id;
    $exam_report = ExamReport::find($exam_report_id);
    // $attendance = Attendance::where('class_section_id', $exam_report->class_section_id)->where('student_id', $student_id);
    $total_days = 0;
    $presents = 0;
    $absents = 0;

    $student = Students::withCount([
        'attendance as presents' => function ($q) use ($exam_report) {
            $q->where('class_section_id', $exam_report->class_section_id)
                ->where('type', 1);
        }
    ])
        ->withCount([
            'attendance as absents' => function ($q) use ($exam_report) {
                $q->where('class_section_id', $exam_report->class_section_id)
                    ->where('type', 0);
            }
        ])
        ->find($student_id);

    $presents = $student->presents;
    $absents = $student->absents;
    $total_days = $presents + $absents;

    if ($total_days) {
        $presents = ($presents * 100) / $total_days;
    } else {
        $presents = 0;
    }

    return number_format($presents, 2);
}

function resizeImage($image): void
{
    Image::make($image)->save(null, 50);
}

function getMediums() {
    return Mediums::get();
}

function getCurrentMedium() {
    if (Auth::user()->hasRole('Super Admin') || get_center_id() == -1) {
        return (object)[
            'id'   => null,
            'name' => null
        ];
    }
    $currentMediumID = session()->get('current_medium_id');
    if ($currentMediumID) {
        return (object)[
            'id'   => session()->get('current_medium_id'),
            'name' => session()->get('current_medium_name')
        ];
    }

    $medium = Mediums::first();
    session()->put('current_medium_id', $medium->id);
    session()->put('current_medium_name', $medium->name);
    return $medium;
}

function get_center_id($student_id = null) {
    if (Auth::user()->hasRole('Super Admin')) {
        return null;
    }

    if (Auth::user()->hasRole('Center')) {
        return Auth::user()->center->id;
    } else if (Auth::user()->hasRole('Teacher')) {
        if (!empty(Session()->get('center_id'))) {
            return Session()->get('center_id');
        }

        return CenterTeacher::where('user_id', Auth::user()->id)->pluck('center_id')->toArray();
    } else if (Auth::user()->staff->first()) {
        return Session()->get('center_id');
    } else if (Auth::user()->hasRole('Student')) {
        return Auth::user()->student->center_id;
    } else if (Auth::user()->hasRole('Parent')) {
        if (!empty($student_id)) {
            return Students::find($student_id)->center->id;
        }
        return Auth::user()->parent->children->pluck('center_id')->toArray();
    }
    return null;
}

function get_role_name($id) {
    $role = Role::find($id);
    $data = $role->name;
    $data = trim(str_replace(get_center_id() . "#", "", $data));
    $data = trim(str_replace("#", "", $data));
    return $data;
}

function produce_role_name($from_center_id, $id): string
{
    $role = Role::find($id);
    $data = $role->name;
    $data = trim(str_replace($from_center_id . "#", "", $data));
    $data = trim(str_replace("#", "", $data));
    return $data;
}

function get_pluck_role_name($ids) {
    $roles = Role::select('id', 'name')->whereIn('id', $ids)->get();
    $names = array();
    foreach ($roles as $key => $role) {
        $names[] = get_role_name($role->id);
    }
    return $names;
}

function get_active_user_role() {
    if (Auth::user()->hasRole('Center')) {
        return 'Center';
    }
    if (Auth::user()->hasRole('Super Teacher')) {
        return 'Super Teacher';
    }
    $roles_id = Auth::user()->roles->pluck('id');
    if (getCurrentMedium()) {
        $role = Role::whereIn('id', $roles_id)->where('medium_id', getCurrentMedium()->id)->get()->first();
        if (!$role) {
            $role = Role::whereIn('id', $roles_id)->get()->first();
        }
    } else {
        $role = Role::whereIn('id', $roles_id)->get()->first();
    }

    return get_role_name($role->id);
}

function getSessionName() {
    return SessionYear::owner()->where('default', 1)->first()->name;
}

function system_installation($center_id) {
    $center = Center::find($center_id);
    $session_year = new SessionYear();
    $session_year->name = Carbon::now()->format('Y');
    $session_year->default = 1;
    $session_year->start_date = Carbon::now()->startOfYear()->format('Y-m-d');
    $session_year->end_date = Carbon::now()->endOfYear()->format('Y-m-d');
    $session_year->center_id = $center->id;
    $session_year->save();
    $data = [
        ['type' => 'school_name', 'message' => $center->name, 'center_id' => $center->id],
        ['type' => 'school_email', 'message' => $center->support_email, 'center_id' => $center->id],
        ['type' => 'school_phone', 'message' => $center->support_contact, 'center_id' => $center->id],
        ['type' => 'school_address', 'message' => $center->address, 'center_id' => $center->id],
        ['type' => 'school_tagline', 'message' => $center->tagline, 'center_id' => $center->id],
        ['type' => 'student_id_fields', 'message' => 'full_name,class_name,roll_number,admission_no,session_year', 'center_id' => $center->id],
        ['type' => 'initial_code', 'message' => strtoupper(substr($center->name, 0, 2)), 'center_id' => $center->id],
        ['type' => 'header_color', 'message' => '#015b89', 'center_id' => $center->id],
        ['type' => 'footer_color', 'message' => '#00b4db', 'center_id' => $center->id],
        ['type' => 'text_color', 'message' => '#ffffff', 'center_id' => $center->id],
        ['type' => 'logo', 'message' => $center->getRawOriginal('logo'), 'center_id' => $center->id],
        ['type' => 'theme_color', 'message' => '#4C5EA6', 'center_id' => $center->id],
        ['type' => 'time_zone', 'message' => 'Asia/Kolkata', 'center_id' => $center->id],
        ['type' => 'date_formate', 'message' => 'd-m-Y', 'center_id' => $center->id],
        ['type' => 'session_year', 'message' => $session_year->id, 'center_id' => $center->id],
        ['type' => 'print_per_page', 'message' => 3, 'center_id' => $center->id],
        ['type' => 'auto_publish_exams', 'message' => 0, 'center_id' => $center->id],
    ];

    Settings::upsert($data, ['type', 'center_id'], ['message']);

    $term = ExamTerm::create([
        'name'            => 'Term 1',
        'session_year_id' => $session_year->id,
        'center_id'       => $center->id,
        'medium_id'       => 1
    ]);
    ExamSequence::insert([
        [
            'name'         => 'Seq 1',
            'exam_term_id' => $term->id,
            'center_id'    => $center->id,
        ],
        [
            'name'         => 'Seq 2',
            'exam_term_id' => $term->id,
            'center_id'    => $center->id,
        ]
    ]);
    return 1;
}

function getExamSubjecMarksSequenceWise($student_id, $subject_id, $sequence_id) {
    //    DB::enableQueryLog();
    $exam_marks = ExamMarks::where(['student_id' => $student_id, 'subject_id' => $subject_id])->with('timetable')->whereHas('timetable.exam', function ($q) use ($sequence_id) {
        $q->where(['type' => 1, 'exam_sequence_id' => $sequence_id]);
    })->get();
    if (count($exam_marks) > 0) {

        $total_obtained_marks = $total_marks = 0;
        foreach ($exam_marks as $row) {
            $total_marks += $row->timetable->total_marks;
            $total_obtained_marks += $row->obtained_marks;
        }
        $total_marks = ($total_marks === 0) ? 1 : $total_marks;
        $average = ($total_obtained_marks * 100) / $total_marks;
    } else {
        $average = "//";
    }

    return $average;
}
