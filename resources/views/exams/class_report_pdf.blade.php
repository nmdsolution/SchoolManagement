<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Class : {{ $exam_report->class_section->full_name }} -
        {{ $exam_report->class_section->class->medium->name }}</title>
</head>
<style>
    body {
        margin: 0%;
    }

    .full-width-table {
        width: 100%;
    }

    .full-width-table {
        font-size: 14px;
    }

    .header-image {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 90%;
    }

    .class-heading {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
    }

    .remove-cell-border {
        border-collapse: collapse;

    }

    .remove-cell-border tr td,
    .remove-cell-border tr th {
        padding: 8px;
    }

    .text-left {
        text-align: left;

    }

    .overall-report {
        margin-top: 2%;
    }

    .top-students,
    .last-students {
        margin-top: 5%;
    }

    .overall-report table tr td {
        text-align: center;
    }

    .text-center {
        text-align: center;
    }

    .page-break {
        page-break-after: always;
    }

    .mb-3 {
        margin-bottom: 1rem;
    }

    .text-sm {
        font-size: 10px;
    }

    .text-right {
        text-align: right;
    }

    .table-heading {
        background-color: #03c6fc;
    }

    .green-text {
        color: green;
    }

    .verticalTableHeader {
        text-align: center;
        white-space: nowrap;
        g-origin: 50% 50%;
        -webkit-transform: rotate(270deg);
        -moz-transform: rotate(270deg);
        -ms-transform: rotate(270deg);
        -o-transform: rotate(270deg);
        transform: rotate(270deg);
        padding: 4rem 0 !important;
    }

    .verticalTableHeader p {
        margin: 0 -100%;
        display: inline-block;
    }

    .verticalTableHeader p:before {
        content: '';
        width: 0;
        padding-top: 110%; /* takes width as reference, + 10% for faking some extra padding */
        display: inline-block;
        vertical-align: middle;
    }


</style>

<body>
<table class="full-width-table table-header">
    <tr>
        <td>{!! $data['header_left'] !!}</td>
        <td><img class="header-image"
                 src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('storage/' . $data['header_logo']->getRawOriginal('message')))) }}"
                 style="width: 55%;"
                 alt=""></td>
        <td>{!! $data['header_right'] !!}</td>
    </tr>
</table>
<div class="class-heading">
    {{__("class")}} : {{ $exam_report->class_section->full_name }} -- {{ $exam_report->exam_term->name }} --
    {{ $exam_report->session_year->name }}
</div>

<div class="overall-report">
    <table class="full-width-table remove-cell-border" border="1">
        <tr class="table-heading">
            <th></th>
            <th> {{__('boys')}} </th>
            <th> {{__('girls')}} </th>
            <th> {{__('eff_t')}} </th>
        </tr>
        <tr>
            <th class="text-left">
                {{__('total_students')}}
            </th>
            <td>{{ $exam_report->male_students }}</td>
            <td>{{ $exam_report->female_students }}</td>
            <td>{{ $exam_report->total_students }}</td>
        </tr>
        <tr>
            <th class="text-left">
                {{__('total').' '.__('present')}}
            </th>
            <td>{{ $exam_report->male_students }}</td>
            <td>{{ $exam_report->female_students }}</td>
            <td>{{ $exam_report->total_students }}</td>
        </tr>
    </table>
</div>

<div class="overall-report">
    <table class="full-width-table remove-cell-border" border="1">
        <tr class="table-heading">
            <th></th>
            <th> {{__('boys')}} </th>
            <th> {{__('girls')}} </th>
            <th> {{__('class')}} </th>
        </tr>
        <tr>
            <th colspan="4">
                {{__('Average')}}
            </th>
        </tr>
        <tr>
            <th class="text-left">
                {{__('Max Avg')}}
            </th>
            <td>{{ number_format($exam_report->male_highest_avg, 2) }}</td>
            <td>{{ number_format($exam_report->female_highest_avg, 2) }}</td>
            <td>{{ number_format(max($exam_report->male_highest_avg, $exam_report->female_highest_avg), 2) }}</td>
        </tr>
        <tr>
            <th class="text-left">
                {{__('Min Avg')}}
            </th>
            <td>{{ number_format($exam_report->male_lowest_avg, 2) }}</td>
            <td>{{ number_format($exam_report->female_lowest_avg, 2) }}</td>
            <td>{{ number_format(min($exam_report->male_lowest_avg , $exam_report->female_lowest_avg), 2) }}</td>
        </tr>
        <tr>
            <th class="text-left">
                {{__('Average')}} &ge; 10
            </th>
            <td>{{ $exam_report->male_more_than_ten }}</td>
            <td>{{ $exam_report->female_more_than_ten }}</td>
            <td>{{ ($exam_report->male_more_than_ten + $exam_report->female_more_than_ten) }}</td>
        </tr>
        <tr>
            <th class="text-left">
                {{__('Average')}} &le; 10
            </th>
            <td>{{ $exam_report->male_less_than_ten }}</td>
            <td>{{ $exam_report->female_less_than_ten }}</td>
            <td>{{ ($exam_report->male_less_than_ten + $exam_report->female_less_than_ten)}}</td>
        </tr>
        <tr>
            <th colspan="4" class="">{{__('attendance')}} (%)</th>
        </tr>
        <tr>
            <th class="text-left">
                {{__('present')}}
            </th>
            <td>{{ number_format($exam_report->attendance->male_presents, 2) }} %</td>
            <td>{{ number_format($exam_report->attendance->female_presents, 2) }} %</td>
            <td>{{ number_format($exam_report->attendance->overall_attendance, 2) }} %</td>
        </tr>
        <tr>
            <th class="text-left">
                {{__('absent')}}
            </th>
            <td>{{ number_format(100 - $exam_report->attendance->male_presents, 2) }} %</td>
            <td>{{ number_format(100 - $exam_report->attendance->female_presents, 2) }} %</td>
            <td>{{ number_format(100 - $exam_report->attendance->overall_attendance, 2) }} %</td>
        </tr>
    </table>
</div>

{{-- Top 5 students --}}
<div class="top-students">
    <table class="full-width-table remove-cell-border" border="1">
        <tr class="table-heading">
            <th colspan="4">{{__('top_students')}}</th>
        </tr>
        <tr>
            <th>#</th>
            <th>{{__('full_name')}}</th>
            <th>{{__('gender')}}</th>
            <th>{{__('Average')}}</th>
        </tr>
        @foreach ($exam_report->top_student as $top_student)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $top_student->student->user->full_name }}</td>
                <td>{{ trans($top_student->student->user->gender) }}</td>
                <td class="text-center">{{ number_format($top_student->avg, 2) }}</td>
            </tr>
        @endforeach
    </table>
</div>

{{-- Last 5 students --}}
<div class="last-students">
    <table class="full-width-table remove-cell-border" border="1">
        <tr class="table-heading">
            <th colspan="4">{{__('last_students')}}</th>
        </tr>
        <tr>
            <th>#</th>
            <th>{{__('full_name')}}</th>
            <th>{{__('gender')}}</th>
            <th>{{__('Average')}}</th>
        </tr>
        @foreach ($exam_report->last_student as $last_student)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $last_student->student->user->full_name }}</td>
                <td>{{ trans($last_student->student->user->gender) }}</td>
                <td class="text-center">{{ number_format($last_student->avg, 2) }}</td>
            </tr>
        @endforeach
    </table>
</div>

<div class="page-break">

</div>
<div class="mb-3">
    <div class="class-heading">

        Class : {{ $exam_report->class_section->full_name }} -
        {{ $exam_report->class_section->class->medium->name }} -- {{ $exam_report->exam_term->name }} --
        {{ $exam_report->session_year->name }}
    </div>
    <div class="text-center text-sm">
        {{__('students')}}
    </div>
</div>
<div class="class-list">
    <table class="full-width-table remove-cell-border" border="1">
        <thead style="min-height: 100px !important;">
            <tr class="table-heading">
                <th rowspan="2">#</th>
                <th rowspan="2">{{__('student_name')}}</th>
                <th rowspan="2">{{__('gender')}}</th>
                <th colspan="3">{{__('Result')}}</th>
                <th colspan="2">{{__('discipline')}}</th>
                <th rowspan="2">{{__('observation')}}</th>
            </tr>
            <tr class="table-heading">
                <th class="verticalTableHeader">{{__('Average')}}</th>
                <th class="verticalTableHeader">{{__('Rank')}}</th>
                <th class="verticalTableHeader">{{__('Honor Roll')}}</th>
                <th class="verticalTableHeader">{{__('total_absence')}}</th>
                <th class="verticalTableHeader">{{__('justified_absence')}}</th>
            </tr>
        </thead>
        <tbody>
        @php
            $exam_term_id = $exam_report->exam_term_id;
        @endphp
        @foreach ($exam_report->exam_report_class_detail as $class_list)
            @php
                $total_absences = 0;
                $justified_absences = 0;

                $attendance = $class_list->student->student_attendance
                        ->filter(function ($item) use($exam_term_id){
                            return $item->exam_term_id == $exam_term_id;
                        })->first();
                if($attendance){
                    $total_absences = $attendance->total_absences;
                    $justified_absences = $attendance->justified_absences;
                }
            @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $class_list->student->user->full_name }}</td>
                <td>{{ trans($class_list->student->user->gender) }}</td>
                <td class="text-center">{{ number_format($class_list->avg, 2) }}</td>
                <td class="text-center">{{ $class_list->rank }}</td>
                <td class="text-center">
                    @if ($class_list->avg >= $data['report_honor_roll']
                     && $total_absences < $data['report_honor_roll_absences'])
                        &#x2714;
                    @endif
                </td>
                <td class="text-right">{{ $total_absences > 0 ? $total_absences : '' }}</td>
                <td class="text-right">{{ $total_absences > 0 ? ($justified_absences>0 ? $justified_absences : 0) : '' }}</td>
                <td>{{ $class_list->observation }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<!-- <div class="class-list">
    <table class="full-width-table remove-cell-border" border="1">
        <thead style="min-height: 100px !important;">
            <tr class="table-heading">
                <th rowspan="2">#</th>
                <th rowspan="2">{{__('student_name')}}</th>
                <th rowspan="2">{{__('gender')}}</th>
                <th colspan="7">{{__('Result')}}</th>
                <th colspan="4">{{__('discipline')}}</th>
                <th rowspan="2">{{__('observation')}}</th>
            </tr>
            <tr class="table-heading">
                <th class="verticalTableHeader">{{__('Average')}}</th>
                <th class="verticalTableHeader">{{__('Rank')}}</th>
                <th class="verticalTableHeader">{{__('Honor Roll')}}</th>
                <th class="verticalTableHeader">{{__('Congratulations (FEL)')}}</th>
                <th class="verticalTableHeader">{{__('Encouragement (ENR)')}}</th>
                <th class="verticalTableHeader">{{__('warning')}}</th>
                <th class="verticalTableHeader">{{__('Blame')}}</th>
                <th class="verticalTableHeader">{{__('total_absence')}}</th>
                <th class="verticalTableHeader">{{__('justified_absence')}}</th>
                <th class="verticalTableHeader">{{__('warning')}}</th>
                <th class="verticalTableHeader">{{__('Blame')}}</th>
            </tr>
        </thead>
        <tbody>
        @php
            $exam_term_id = $exam_report->exam_term_id;
        @endphp
        @foreach ($exam_report->exam_report_class_detail as $class_list)
            @php
                $total_absences = 0;
                $justified_absences = 0;

                $attendance = $class_list->student->student_attendance
                        ->filter(function ($item) use($exam_term_id){
                            return $item->exam_term_id == $exam_term_id;
                        })->first();
                if($attendance){
                    $total_absences = $attendance->total_absences;
                    $justified_absences = $attendance->justified_absences;
                }
            @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $class_list->student->user->full_name }}</td>
                <td>{{ trans($class_list->student->user->gender) }}</td>
                <td class="text-center">{{ number_format($class_list->avg, 2) }}</td>
                <td class="text-center">{{ $class_list->rank }}</td>
                <td class="text-center">
                    @if ($class_list->avg >= $data['report_honor_roll']
                     && $total_absences < $data['report_honor_roll_absences'])
                        &#x2714;
                    @endif
                </td>
                <td class="text-center">
                    @if ($class_list->avg >= $data['congratulations_min']
                        && $class_list->avg <= $data['congratulations_max'])
                        &#x2714;
                    @endif
                </td>
                <td class="text-center">
                    @if ($class_list->avg >= $data['encouragement_min']
                        && $class_list->avg < $data['encouragement_max'])
                        &#x2714;
                    @endif
                </td>
                <td class="text-center">
                    @if ($class_list->avg >= $data['average_warning_min']
                        && $class_list->avg < $data['average_warning_max'])
                        &#x2714;
                    @endif
                </td>
                <td class="text-center">
                    @if ($class_list->avg >= $data['average_blame_min']
                        && $class_list->avg < $data['average_blame_max'])
                        &#x2714;
                    @endif
                </td>

                <td class="text-right">{{ $total_absences > 0 ? $total_absences : '' }}</td>
                <td class="text-right">{{ $total_absences > 0 ? ($justified_absences>0 ? $justified_absences : 0) : '' }}</td>
                <td class="text-center">
                    @if ($total_absences >= $data['report_warning_min']
                        && $total_absences < $data['report_warning_max'])
                        &#x2714;
                    @endif
                </td>
                <td class="text-center">
                    @if ($total_absences >= $data['report_blame_min']
                        && $total_absences < $data['report_blame_max'])
                        &#x2714;
                    @endif
                <td></td>
            </tr>
        @endforeach
        </tbody>

    </table>
</div> -->
<div style="margin-top: 25px;">
    <table class="full-width-table remove-cell-border" border="1">
        <thead class="table-heading">
            <th style="padding: 5px;">
                {{__('class_delegate')}}
            </th>
            <th style="padding: 5px;">
                {{__("class_teacher")}}
            </th>
            <th style="padding: 5px;">
                {{__("class_counselor")}}
            </th>
            <th style="padding: 5px;">
                {{__("discipline_master")}}
            </th>
            <th style="padding: 5px;">
                {{__("council_president")}}
            </th>
        </thead>
        <tbody>
            <tr>
                <td style="min-height: 40px; padding: 30px;">&nbsp;</td>
                <td style="min-height: 40px; padding: 30px;">&nbsp;</td>
                <td style="min-height: 40px; padding: 30px;">&nbsp;</td>
                <td style="min-height: 40px; padding: 30px;">&nbsp;</td>
                <td style="min-height: 40px; padding: 30px;">&nbsp;</td>
            </tr>
        </tbody>
    </table>
</div>
</body>

</html>
