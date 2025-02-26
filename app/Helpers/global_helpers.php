<?php

use App\Models\Center;
use App\Models\Mediums;
use App\Models\Settings;
use App\Models\SessionYear;
use App\Models\DefaultTimetable;
use Illuminate\Support\Facades\Auth;
use Rawilk\Settings\Support\Context;

function remove_accents($string) {
    if ( !preg_match('/[\x80-\xff]/', $string) )
        return $string;

    $chars = array(
    // Decompositions for Latin-1 Supplement
    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
    chr(195).chr(191) => 'y',
    // Decompositions for Latin Extended-A
    chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
    chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
    chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
    chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
    chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
    chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
    chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
    chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
    chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
    chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
    chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
    chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
    chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
    chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
    chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
    chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
    chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
    chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
    chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
    chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
    chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
    chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
    chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
    chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
    chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
    chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
    chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
    chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
    chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
    chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
    chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
    chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
    chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
    chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
    chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
    chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
    chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
    chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
    chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
    chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
    chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
    chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
    chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
    chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
    chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
    chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
    chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
    chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
    chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
    chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
    chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
    chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
    chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
    chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
    chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
    chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
    chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
    chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
    chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
    chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
    chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
    chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
    chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
    chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
    );

    $string = strtr($string, $chars);

    return $string;
}

function get_nationalities(){
    return [
        'cameroon',
        'chad',
        'central_africa',
        'equatorial_guinea',
        'gabon', 
        'congo',
        'nigeria'
    ];
}

function all_groups() {
    return [
        "Form 1" => [],
        "Form 2" => [],
        "Form 3" => [],
        "Form 4" => [],
        "Form 5" => [],
        "Lower Sixth" => [],
        "Upper Sixth" => [],
        "SECONDE" => [],
        "TERMINAL" => [],
        "PREMIERE" => []
    ];
}

function groupName($name) {
    $grouped = all_groups();

    $lowerValue = strtolower($name);

    if (str_starts_with($lowerValue, 'form 1')) {
        return "Form 1";
    } elseif (str_starts_with($lowerValue, 'form 2')) {
        return "Form 2";
    } elseif (str_starts_with($lowerValue, 'form 3')) {
        return "Form 3";
    }
    elseif (str_starts_with($lowerValue, 'form 4')) {
        return "Form 4";
    }
    elseif (str_starts_with($lowerValue, 'form 5')) {
        return "Form 5";
    }
    elseif (str_starts_with($lowerValue, 'lower sixth')) {
       return "Lower Sixth";
    } elseif (str_starts_with($lowerValue, 'upper sixth')) {
        return "Upper Sixth";
    } elseif (str_starts_with($lowerValue, 'seconde')) {
        return "SECONDE";
    } elseif (str_starts_with($lowerValue, 'terminal')) {
        return "TERMINAL";
    } elseif (str_starts_with($lowerValue, 'premiere')) {
        return "PREMIERE";
    }

    return $name;
}


function filterClassData($class_names, $male_students, $female_students) {
    $groupes = all_groups();

    $new_class_names = [];
    $new_male_students = [];
    $new_female_students = [];

    $class_groups = [];

    foreach ($class_names as $index => $name) {
        $starting_name = groupName($name);
        if (!isset($class_groups[$starting_name])) {
            $class_groups[$starting_name] = [];
        }
        $class_groups[$starting_name][] = $index;
    }

    foreach ($class_groups as $starting_name => $class_indices) {
        if (count($class_indices) > 1) {
            $total_male = 0;
            $total_female = 0;
            foreach ($class_indices as $index) {
                $total_male += $male_students[$index];
                $total_female += $female_students[$index];
            }
            $new_class_names[] = $starting_name;
            $new_male_students[] = $total_male;
            $new_female_students[] = $total_female;
        } else {
            // If the group has only one class, keep it as is
            $index = $class_indices[0];
            $new_class_names[] = $class_names[$index];
            $new_male_students[] = $male_students[$index];
            $new_female_students[] = $female_students[$index];
        }
    }


    return [$new_class_names, $new_male_students, $new_female_students];
}

// needed only when adding
function fillDefaultTimetable() {
    $list = [[
        "start_time" => "07:30",
        "end_time" => "08:30",
    ], [
        "start_time" => "08:30",
        "end_time" => "09:30",
    ], [
        "start_time" => "09:30",
        "end_time" => "10:30",
    ], [
        "start_time" => "10:30",
        "end_time" => "11:00",
    ], [
        "start_time" => "11:00",
        "end_time" => "12:00",
    ], [
        "start_time" => "12:00",
        "end_time" => "13:00",
    ], [
        "start_time" => "13:00",
        "end_time" => "14:00",
    ], [
        "start_time" => "14:00",
        "end_time" => "15:00",
    ],];

    foreach ($list as $item) {
        DefaultTimetable::create([
            'start_time' => $item['start_time'],
            'end_time' => $item['end_time'],
        ]);
    }
}

function studentResultRows(&$tempRow, $row, $operate, &$rows): void
{
    $tempRow['id'] = $row->id;
    $tempRow['exam_report_id'] = $row->exam_report_id;
    $tempRow['student_id'] = $row->student_id;
    $tempRow['student_name'] = $row->student->full_name ?? '';
    $tempRow['total_obtained_coef'] = $row->total_obtained_coef;
    $tempRow['total_obtained_points'] = $row->total_obtained_points;
    $tempRow['avg'] = number_format($row->avg, 2);
    $tempRow['total_avg'] = $row->total_avg;
    $tempRow['rank'] = $row->rank < 0 ? 'NA' : $row->rank;
    $tempRow['created_at'] = $row->created_at;
    $tempRow['updated_at'] = $row->updated_at;
    $tempRow['operate'] = $operate;
    $rows[] = $tempRow;
}

// Retrieve current session year data
function getSessionYearData() {
    $sessionYearId = getSettings('session_year')['session_year'];
    return SessionYear::findOrFail($sessionYearId);
}

function getReportHeaderLogo()
{
    return Settings::where('type', 'report_header_logo')
        ->where('center_id', get_center_id())->currentMedium()->first()
        ?? Settings::where('message', 'settings/GE3YADj9VNU0tp2O7B52foQr3byiBLiPdYZdipnS.jpg')->first();
}

function getReportWaterMark() 
{
    return Settings::where('type', 'report_water_mark')
        ->where('center_id', get_center_id())->currentMedium()->first()
        ?? Settings::where('message', 'settings/GE3YADj9VNU0tp2O7B52foQr3byiBLiPdYZdipnS.jpg')->first();
}

function getCenterType()
{
    $center = Center::find(get_center_id());
    $type =  $center->type;
    // dd(Auth::user()->hasRole('Center'), $type);
    return $type;

}

function isPrimaryCenter()
{
    return getCenterType() == 'primary' || getCenterType() == 1 || getCenterType() == '1';
}

function isSecondaryCenter()
{
    return getCenterType() == 'secondary' || getCenterType() == 0;
}

function getCenterTypeName(): string
{
    return isPrimaryCenter() ? 'Primary' : 'Centre secondaire';
}

function set_active_locale(string $locale)
{
    $locale = strtolower($locale);
    if (in_array($locale, ['en', 'fr'])) {
        Session::put('locale', $locale);        
        Session::save();
        Session::put('language', $locale);
        app()->setLocale(Session::get('locale'));
        settings()->context(new Context(['user_id' => auth()->user()->id]))->set('prefered_lacale', $locale);
    }
}

function set_active_medium(int $medium_id)
{
    $medium = Mediums::find($medium_id);
    if ($medium) {
        Session::put('current_medium_id', $medium->id);
        Session::put('current_medium_name', $medium->name);
        settings()
            ->context(new Context(['user_id' => auth()->user()->id]))
            ->set('prefered_medium', $medium_id)
            ;
    }
}

