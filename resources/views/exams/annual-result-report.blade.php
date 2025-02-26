<!DOCTYPE html>
<html>
<head>
    <title>{{__('Student Report')}}</title>
    {{--
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}">
    --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        thead {
            background-color: {{$settings['report_color'] ?? ''}};
        }

        dd, legend {
            margin-bottom: .5rem
        }

        progress, sub, sup {
            vertical-align: baseline
        }

        :root {
            --bs-blue: #0d6efd;
            --bs-indigo: #6610f2;
            --bs-purple: #6f42c1;
            --bs-pink: #d63384;
            --bs-red: #dc3545;
            --bs-orange: #fd7e14;
            --bs-yellow: #ffc107;
            --bs-green: #198754;
            --bs-teal: #20c997;
            --bs-cyan: #0dcaf0;
            --bs-white: #fff;
            --bs-gray: #6c757d;
            --bs-gray-dark: #343a40;
            --bs-gray-100: #f8f9fa;
            --bs-gray-200: #e9ecef;
            --bs-gray-300: #dee2e6;
            --bs-gray-400: #ced4da;
            --bs-gray-500: #adb5bd;
            --bs-gray-600: #6c757d;
            --bs-gray-700: #495057;
            --bs-gray-800: #343a40;
            --bs-gray-900: #212529;
            --bs-primary: #0d6efd;
            --bs-secondary: #6c757d;
            --bs-success: #198754;
            --bs-info: #0dcaf0;
            --bs-warning: #ffc107;
            --bs-danger: #dc3545;
            --bs-light: #f8f9fa;
            --bs-dark: #212529;
            --bs-primary-rgb: 13, 110, 253;
            --bs-secondary-rgb: 108, 117, 125;
            --bs-success-rgb: 25, 135, 84;
            --bs-info-rgb: 13, 202, 240;
            --bs-warning-rgb: 255, 193, 7;
            --bs-danger-rgb: 220, 53, 69;
            --bs-light-rgb: 248, 249, 250;
            --bs-dark-rgb: 33, 37, 41;
            --bs-white-rgb: 255, 255, 255;
            --bs-black-rgb: 0, 0, 0;
            --bs-body-color-rgb: 33, 37, 41;
            --bs-body-bg-rgb: 255, 255, 255;
            --bs-font-sans-serif: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            --bs-font-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
            --bs-body-font-family: var(--bs-font-sans-serif);
            --bs-body-font-size: 0.1rem;
            --bs-body-font-weight: 400;
            --bs-body-line-height: 1.5;
            --bs-body-color: #212529;
            --bs-body-bg: #fff
        }

        *, ::after, ::before {
            box-sizing: border-box
        }


        body {
            margin: 0;
            font-family: var(--bs-body-font-family);
            font-size: 65%;
            font-weight: var(--bs-body-font-weight);
            line-height: var(--bs-body-line-height);
            color: var(--bs-body-color);
            /*text-align: var(--bs-body-text-align);*/
            background-color: var(--bs-body-bg);
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
        }

        hr {
            margin: 1rem 0;
            background-color: currentColor;
            border: 0;
            opacity: .25
        }

        hr:not([size]) {
            height: 1px
        }

        img, svg {
            vertical-align: middle
        }

        table {
            caption-side: bottom;
            border-collapse: collapse
        }


        th {
            text-align: inherit;
            text-align: -webkit-match-parent
        }

        tbody, td, tfoot, th, thead, tr {
            border: 0 solid;
            border-color: inherit
        }

        .row {
            --bs-gutter-x: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            margin-top: calc(-1 * var(--bs-gutter-y));
            margin-right: calc(-.5 * var(--bs-gutter-x));
            margin-left: calc(-.5 * var(--bs-gutter-x))
        }

        .row > * {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            padding-right: calc(var(--bs-gutter-x) * .5);
            padding-left: calc(var(--bs-gutter-x) * .5);
            margin-top: var(--bs-gutter-y)
        }

        .col {
            flex: 1 0 0
        }

        /*.col-auto, .row-cols-auto > * {*/
        /*    flex: 0 0 auto;*/
        /*    width: auto*/
        /*}*/

        .col-12, .row-cols-1 > * {
            flex: 0 0 auto;
            width: 100%
        }

        .col-6, .row-cols-2 > * {
            flex: 0 0 auto;
            width: 50%
        }

        .row-cols-3 > * {
            flex: 0 0 auto;
            width: 33.3333333333%
        }

        .col-3, .row-cols-4 > * {
            flex: 0 0 auto;
            width: 25%
        }

        .row-cols-5 > * {
            flex: 0 0 auto;
            width: 20%
        }

        .row-cols-6 > * {
            flex: 0 0 auto;
            width: 16.6666666667%
        }

        /*.col-1, .col-2 {*/
        /*    flex: 0 0 auto*/
        /*}*/

        /*.col-1 {*/
        /*    display: inline-block;*/
        /*    padding-right: 15px;*/
        /*    padding-left: 15px;*/
        /*    width: 8.33333333%*/
        /*}*/

        .col-2 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 16.66666667%
        }

        .col-4, .col-5 {
            flex: 0 0 auto
        }

        .col-4 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 33.33333333%
        }

        .col-5 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 41.66666667%
        }

        .col-7, .col-8 {
            flex: 0 0 auto
        }

        .col-7 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 58.33333333%
        }

        .col-8 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 66.66666667%
        }

        .col-9 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            flex: 0 0 auto;
            width: 75%
        }

        /*.col-10 {*/
        /*    display: inline-block;*/
        /*    padding-right: 15px;*/
        /*    padding-left: 15px;*/
        /*    flex: 0 0 auto;*/
        /*    width: 83.33333333%*/
        /*}*/

        /*.col-11 {*/
        /*    display: inline-block;*/
        /*    padding-right: 15px;*/
        /*    padding-left: 15px;*/
        /*    flex: 0 0 auto;*/
        /*    width: 91.66666667%*/
        /*}*/


        .table {
            --bs-table-bg: transparent;
            --bs-table-accent-bg: transparent;
            --bs-table-striped-color: #212529;
            --bs-table-striped-bg: rgba(0, 0, 0, 0.05);
            --bs-table-active-color: #212529;
            --bs-table-active-bg: rgba(0, 0, 0, 0.1);
            --bs-table-hover-color: #212529;
            --bs-table-hover-bg: rgba(0, 0, 0, 0.075);
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            vertical-align: top;
            border-color: #dee2e6
        }

        .table > :not(caption) > * > * {
            padding-left: .5rem;
            padding-right: .5rem;
            padding-top: .3rem;
            background-color: var(--bs-table-bg);
            border-bottom-width: 1px;
            box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg)
        }

        .table > tbody {
            vertical-align: inherit
        }

        .table > thead {
            vertical-align: bottom
        }

        .table > :not(:first-child) {
            border-top: 2px solid currentColor
        }

        .table-sm > :not(caption) > * > * {
            padding: .25rem
        }

        .table-bordered > :not(caption) > * {
            border-width: 1px 0
        }

        .table-bordered > :not(caption) > * > * {
            border-width: 0 1px
        }


        .align-middle {
            vertical-align: middle !important
        }


        .border {
            border: 1px solid #dee2e6 !important
        }

        .border-0 {
            border: 0 !important
        }

        .border-bottom {
            border-bottom: 1px solid #dee2e6 !important
        }

        .border-dark {
            border-color: #212529 !important
        }


        .border-1 {
            border-width: 1px !important
        }

        .w-100 {
            width: 100% !important
        }


        .mt-2 {
            margin-top: .5rem !important
        }

        .mt-3 {
            margin-top: 1rem !important
        }

        .mt-4 {
            margin-top: 1.5rem !important
        }


        .mt-auto {
            margin-top: auto !important
        }

        .mb-0 {
            margin-bottom: 0 !important
        }


        .ms-2 {
            margin-left: .5rem !important
        }

        .ms-3 {
            margin-left: 1rem !important
        }

        .pe-0 {
            padding-right: 0 !important
        }

        .text-start {
            text-align: left !important
        }

        /*.text-end {*/
        /*    text-align: right !important*/
        /*}*/

        .text-center {
            text-align: center !important
        }

        .bg-white {
            background-color: rgba(var(--bs-white-rgb), var(--bs-bg-opacity)) !important
        }


        /*.my-0 {*/
        /*    margin-top: 0 !important;*/
        /*    margin-bottom: 0 !important;*/
        /*}*/

        .my-1 {
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }

        /*.my-2 {*/
        /*    margin-top: 0.5rem !important;*/
        /*    margin-bottom: 0.5rem !important;*/
        /*}*/

        /*.my-3 {*/
        /*    margin-top: 1rem !important;*/
        /*    margin-bottom: 1rem !important;*/
        /*}*/

        /*.my-4 {*/
        /*    margin-top: 1.5rem !important;*/
        /*    margin-bottom: 1.5rem !important;*/
        /*}*/

        /*.my-5 {*/
        /*    margin-top: 3rem !important;*/
        /*    margin-bottom: 3rem !important;*/
        /*}*/

        /*.my-auto {*/
        /*    margin-top: auto !important;*/
        /*    margin-bottom: auto !important;*/
        /*}*/

        .row {
            display: table;
            width: 100%;
            /*margin-right: -15px;*/
            /*margin-left: -15px;*/
        }

        .row [class^="col-"] {
            /*display: inline-block;*/
            /*width: 100%;*/
            /*padding-right: 15px;*/
            /*padding-left: 15px;*/
            display: table-cell;
        }

        .ps-0 {
            padding-left: 0 !important;
        }

        /*.me-0 {*/
        /*    margin-right: 0 !important;*/
        /*}*/

        .verticalTableHeader {
            text-align: center;
            white-space: nowrap;
            g-origin: 50% 50%;
            -webkit-transform: rotate(270deg);
            -moz-transform: rotate(270deg);
            -ms-transform: rotate(270deg);
            -o-transform: rotate(270deg);
            transform: rotate(270deg);
            padding: 1.5rem 0 !important;
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

        .report_left_header p, .report_right_header p {
            margin: 0;
        }
    </style>
</head>
<body class="bg-white" style="margin: 0;height:100px;">
<div class="row text-center" style="display: flex;">
    <div class="col-5 report_left_header" style="position: relative;top: -45px;">
        {!! $settings['report_left_header']??'' !!}
    </div>
    <div class="col-2">
        @if($reportHeaderLogo)
            <img src="{{'data:image/png;base64,'.base64_encode(@file_get_contents(public_path('storage/'.$reportHeaderLogo->getRawOriginal('message'))))}}" alt="Report Header" class="w-100"/>
        @endif
    </div>
    <div class="col-5 report_right_header" style="position: relative;top: -45px;">
        {!! $settings['report_right_header']??'' !!}
    </div>
</div>
<div class="row text-center" style="position: relative;top:-30px">
    <h3>{{__('annual_report')." ". $sessionYearData->name }}</h3>
</div>
<div class="col-12 row" style="position: relative;top: -65px;left: -30px;">
    <div class="col-4" style="position: relative;top: -10px;">
        <div class="row my-1">
            <div style="left:15px;position:relative;width: 70px; display: inline-block;">{{__('name')}}</div>
            <div style="position:relative;width: 10px; display: inline-block;">:</div>
            <div class="border-bottom border-dark border-1" style="width: 200px;display: inline-block;"><b>{{$student->user->full_name}}</b></div>
        </div>
        <div class="row my-1">
            <div style="left:15px;position:relative;width: 70px; display: inline-block;">{{__("DOB")}}</div>
            <div style="position:relative;width: 10px; display: inline-block;">:</div>
            <div class="border-bottom border-dark border-1" style="width: 200px;display: inline-block;">
                <b>
                    {{$student->user->dob}}
                    @php
                        $place = $student->get_dynamic_field('Place_of_birth');
                        if($place != "") $place = " , " . $place;
                    @endphp
                    {{$place}}
                </b>
            </div>
        </div>
        <div class="row my-1">
            <div style="left:15px;position:relative;width: 70px; display: inline-block;">{{__("Sex")}}</div>
            <div style="position:relative;width: 10px; display: inline-block;">:</div>
            <div class="border-bottom border-dark border-1" style="width: 200px;display: inline-block;"><b>{{strtoupper(substr($student->user->gender, 0, 1))}}</b></div>
        </div>
        <div class="row my-1">
            <div style="left:15px;position:relative;width: 70px; display: inline-block;">{{__("Matricule")}}</div>
            <div style="position:relative;width: 10px; display: inline-block;">:</div>
            <div class="border-bottom border-dark border-1" style="width: 200px;display: inline-block;"><b>{{$student->admission_no}}</b></div>
        </div>
    </div>
    <div class="col-5" style="position: relative;top: -5px;">
        <div class="row my-1">
            <div style="left:15px;position:relative;width: 145px; display: inline-block;">{{__('Class')}}</div>
            <div style="position:relative;width: 10px; display: inline-block;">:</div>
            <div class="border-bottom border-dark border-1" style="width: 200px;display: inline-block;">
                <b>{{$student->class_section->class->name.' '.$student->class_section->section->name}}</b>
            </div>
        </div>
        <div class="row my-1">
            <div style="left:15px;position:relative;width: 145px; display: inline-block;">{{__("Class Teacher")}}</div>
            <div style="position:relative;width: 10px; display: inline-block;">:</div>
            <div class="border-bottom border-dark border-1" style="width: 200px;display: inline-block;">
                <b>{{$student->class_section->teacher->user->full_name ?? ''}}</b>
            </div>
        </div>
        <div class="row my-1">
            <div style="left:15px;position:relative;width: 145px; display: inline-block;">{{__("parents")}}</div>
            <div style="position:relative;width: 10px; display: inline-block;">:</div>
            <div class="border-bottom border-dark border-1" style="width: 200px;display: inline-block;"></div>
        </div>
        <div class="row my-1">
            <div style="left:15px;position:relative;width: 145px; display: inline-block;">{{__("Parent Contact")}}</div>
            <div style="position:relative;width: 10px; display: inline-block;">:</div>
            <div class="border-bottom border-dark border-1" style="width: 200px;display: inline-block;"></div>
        </div>
    </div>
    <div class="col-2" style="position: relative;right:-50px">
        <div class="row my-1">
            @if(file_exists(public_path('storage/'.$student->user->getRawOriginal('image'))) && is_file(public_path('storage/'.$student->user->getRawOriginal('image'))))
                <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('storage/'.$student->user->getRawOriginal('image'))))}}"
                     alt="Report Header" class="w-100"/>
            @else
                <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('storage/dummy_logo.png')))}}"
                     alt="Report Header" class="w-100" height="90"/>
            @endif
        </div>
    </div>
</div>
<div class="mt-3 row" style="position: relative;top: -90px">
    <div class="col-12 ps-0">
        <table class="table table-bordered border-dark">
            <thead class="text-center align-middle">
            <tr>
                <th rowspan="2">{{__("SUBJECTS")}}</th>
                @foreach($term_reports as $row)
                    <th rowspan="2" class="verticalTableHeader">{{$row->exam_term->name}}</th>
                @endforeach
                <th rowspan="2" class="verticalTableHeader">{{__("Total")}}</th>
                <th rowspan="2" class="verticalTableHeader">{{__("Coef")}}</th>
                <th rowspan="2" class="verticalTableHeader">{{__("N X C")}}</th>
                <th rowspan="2" class="verticalTableHeader">{{__("Rank")}}</th>
                <th colspan="2">{{__("class")}}</th>
                <th rowspan="2" class="verticalTableHeader">{{__("Grades")}}</th>
                <th rowspan="2" class="verticalTableHeader">{{__("Remarks")}}</th>
                <th rowspan="2" class="verticalTableHeader">{{__("Signature")}}</th>
            </tr>
            <tr>
                <th class="verticalTableHeader">{{__("Min")}}</th>
                <th class="verticalTableHeader">{{__("Max")}}</th>
            </tr>
            </thead>
            <tbody style="font-weight: bold;">
            @php
                $low_subjects = [];
                $overAllCoef = 0;
                $overAllNxC = 0;
            @endphp

            @foreach($examResultGroups as $group)
                @php
                    $totalCoef = 0;
                    $totalNxC = 0;
                @endphp
                @foreach($group->subjects as $subject)
                    <tr class="text-center align-middle">

                        @php
                        // ClassSubject does exits anymore
                        if(empty($subject->class_subject)){
                            continue;
                        }
						$fetchSubjectData = $annual_subject_report->filter(function($data)use($subject){
						    return $data->subject_id===$subject->id;
						})->first();
                        if($fetchSubjectData){
                            // dd($fetchSubjectData);
                        }
						$subject_teachers = $subject->subject_teachers->filter(function($data)use($student){
							return $data->class_section_id===$student->class_section_id;
						});

						if($fetchSubjectData==null){
							continue;
						}
						$weightage = $subject->class_subject->weightage??0;
						$nXc = $weightage * $fetchSubjectData->subject_avg;
						if($nXc >=0){
							$totalCoef+=$weightage;
							$overAllCoef +=$weightage;
							$totalNxC +=$nXc;
							$overAllNxC+=$nXc;
							if($fetchSubjectData->subject_avg < $low_subject_average){
							$low_subjects[] = $subject->name;
							}
						}


                        @endphp

                                <!--Subject & Teacher Name-->
                        <td class="text-start">
                            {{strtoupper($subject->name)}}
                            @foreach($subject_teachers as $teacher)
                                <br> <span class="ms-3">{{$teacher->teacher->user->full_name}}</span>
                            @endforeach
                        </td>


                        @foreach($term_reports as $report)
                            @if (isset($fetchSubjectData->term_marks[$report->id]) && $fetchSubjectData->term_marks[$report->id] >=0)
                                @if ($fetchSubjectData->term_marks[$report->id]>=10)
                                    <td>{{ number_format($fetchSubjectData->term_marks[$report->id],2)}}</td>                                
                                @else
                                    <td style="color: #f00;">{{ number_format($fetchSubjectData->term_marks[$report->id],2)}}</td>
                                @endif
                            @else
                                <td>/</td>
                            @endif
                        @endforeach

                        <!--Total Subject Average -->
                        <th>{{ $fetchSubjectData->subject_avg<0 ? "/" : number_format($fetchSubjectData->subject_avg,2) }}</th>

                        <!--Subject Weightage-->

                        <td>{{$subject->class_subject->weightage}}</td>

                        <!--N x C-->
                        <td><b>{{ $nXc<0 ? "/" : number_format($nXc,2) }}</b></td>

                        <!--Rank-->
                        <td>{{ $fetchSubjectData->subject_rank<0 ? "/" : $fetchSubjectData->subject_rank }}</td>

                        <!--Class Min marks-->
                        <td>{{number_format($subject->class_details->min,2)}}</td>

                        <!--Class Max marks-->
                        <td>{{number_format($subject->class_details->max,2)}}</td>

                        <!--Grades-->
                        <th>{{strtoupper($fetchSubjectData->subject_grade)}}</th>
                        <!-- Remarks -->
                        <th>{{strtoupper($fetchSubjectData->subject_remarks)}}</th>
                        <!-- Signature -->
                        <th></th>
                    </tr>
                @endforeach
                
                <tr class="text-center">
                    <th colspan="{{count($term_reports)+2}}">
                        <i>{{$group->name}} </i>
                    </th>
                    <td><b>{{$totalCoef}}</b></td>
                    <td><b>{{number_format($totalNxC,2)}}</b></td>

                    @if($totalCoef!==0)
                    <td colspan="6" class="text-start"><b>{{__("Moy / Av")}} : {{number_format($totalNxC / $totalCoef,2)}}</b></td>
                    @else
                    <td colspan="6" class="text-start"><b>{{__("Moy / Av")}} : 0.00</b></td>
                    @endif
                </tr>
            @endforeach
        </table>
    </div>
</div>
<div class="row col-12" style="position: relative;top: -95px;margin-bottom: 0; font-weight: bold;">
    <div class="col-3" style="position:relative; padding-right: 15px;">
        <table class="table col-12" style="height: 181px; position: relative; border-radius: 10px;">
            <thead class="text-center align-middle border-1 border-dark">
            <tr>
                <th colspan="2">{{__("Conduct")}}</th>
            </tr>
            </thead>
            <tbody class="border-1 border-dark">
                <tr>
                    <td>
                        {{__('total_absence')}}</td>
                    <td>
                        <span >{{$attendance != null? $attendance->total_absences : '/'}}</span>
                    </td>
                </tr>
                <tr>
                    <td>{{__('justified_absence')}}</td>
                    <td>
                        <span>{{$attendance != null? $attendance->justified_absences : '/' }}</span>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('warning') }}</td>
                    <td>
                        @if ($settings['report_warning_min'] <= $attendance->total_absences 
                            && $settings['report_warning_max'] > $attendance->total_absences )
                            {{ __("yes") }}
                        @else
                            {{__("no")}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>{{ __('Blame') }}</td>
                    <td>
                        @if ($settings['report_blame_min'] <= $attendance->total_absences 
                            && $settings['report_blame_max'] > $attendance->total_absences )
                            {{ __("yes") }}
                        @else
                            {{__("no")}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>{{ __('lateness') }}</td>
                    <td> / </td>
                </tr>
                <tr>
                    <td>{{ __('others') }}</td>
                    <td> / </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-5" style="padding: 0">
        <table class="mt-2 table table-bordered border-dark" style="height: 75px;align-items: center">
            <thead class="text-center">
                <th colspan="{{count($term_reports)+1}}">{{__('term_performance')}}</th>
            </thead>
            <tbody>
                <tr class="text-center">
                    <td></td>
                    @foreach($term_reports as $report)
                        <td >{{$report->exam_term->name}}</td>
                    @endforeach
                </tr>

                <tr>
                    <td>{{__("Avg")}}</td>
                    @foreach($term_reports as $report)
                        <td>{{isset($studentClassPerformance->term_avgs[$report->id]) ? $studentClassPerformance->term_avgs[$report->id] : '/'}}</td>
                    @endforeach
                </tr>
                <tr>
                    <td>{{__("Rank")}}</td>
                    @foreach($term_reports as $report)
                        <td>{{isset($studentClassPerformance->term_ranks[$report->id]) ? $studentClassPerformance->term_ranks[$report->id] : '/'}}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        <div class="row" style="position: relative; top: -25px;">
            <div class="col-4" style="padding-right: 5px; padding-left: 0px; margin-left: 0px !important;">
                <table class="mt-3 table table-bordered border-dark" style=" height: 100px;">
                    <thead class="text-center">
                    <th colspan="2">{{__("Class Summary")}}</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{__("Class Avg")}}</td>
                            <td>{{number_format($classPerformance->class_avg,2)}}</td>
                        </tr>
                    <tr>
                        <td>{{__("Min Avg")}}</td>
                        <td>{{number_format($classPerformance->min_avg,2)}}</td>
                    </tr>
                    <tr>
                        <td>{{__("Max Avg")}}</td>
                        <td>{{number_format($classPerformance->max_avg,2)}}</td>
                    </tr>
                    <tr>
                        <td>{{__("class size")}}</td>
                        <td>{{number_format($classPerformance->class_size,0)}}</td>
                    </tr>
        
                    </tbody>
                </table>
            </div>
            <div class="col-8" style="padding-left: 5px;  padding-right: 0px; margin-right: 0px !important;">
                <table class="mt-2 table table-bordered border-dark mb-0" style="height: 100px;">
                    <thead class="text-center">
                        <th colspan="2">{{__("annual_performance")}}</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{__("Avg")}}</td>
                            <td style="font-weight: bold;">{{number_format($studentClassPerformance->avg,2)}}</td>
                        </tr>
                        <tr>
                            <td>{{__("Rank")}}</td>
                            <td style="font-weight: bold;">{{ $studentClassPerformance->rank < 0 ? "NA" : $studentClassPerformance->rank . "  /  ". number_format($classPerformance->class_size,0)}}</td>
                        </tr>
                        <tr>
                            <td>{{__("Honor")}}</td>
                            <td style="font-weight: bold;">
                                @if ($studentClassPerformance->avg >= $settings['report_honor_roll'] 
                                    && $attendance->total_absences < $settings['report_honor_roll_absences'] )
                                    {{ __("yes") }}
                                @else
                                    {{__("no")}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{__('average_comment')}}</td>
                            <td style="font-weight: bold;">
                                @if ($settings['average_blame_min'] <= $studentClassPerformance->avg 
                                    && $settings['average_blame_max'] > $studentClassPerformance->avg )
                                    {{ __('Blame') }}
                                @elseif($settings['average_warning_min'] <= $studentClassPerformance->avg 
                                    && $settings['average_warning_max'] > $studentClassPerformance->avg )
                                    {{ __('Warning (AV)') }}
                                @elseif($settings['encouragement_min'] <= $studentClassPerformance->avg 
                                    && $settings['encouragement_max'] > $studentClassPerformance->avg )
                                    {{ __('Encouragement (ENR)') }}
                                @elseif($settings['congratulations_min'] <= $studentClassPerformance->avg 
                                    && $settings['congratulations_max'] > $studentClassPerformance->avg )
                                    {{ __('Congratulations (FEL)') }}
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-3" style="padding: 0 15px;">
        <table class="table table-bordered border-dark" style="height: 181px; position: relative; width: 100%;">
            <thead class="text-center">
                <th style="padding: 3px;">
                    {{ __('council_decision')}}
                </th>
            </thead>
            <tbody>
                <tr>
                    <td style="min-height: 25px; padding: 15px;">&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="col-12 row" style="height: 100px;position: relative;top: -115px;margin-bottom: 0">
    <table class=" table table-bordered border-dark" style="min-height: 40px; width: 100%;">
        <thead class="text-center">
            <th style="padding: 5px;">
                {{__("parent signature")}}
            </th>
            <th style="padding: 5px;">
                {{__("teacher signature")}}
            </th>
            <th style="padding: 5px;">
                {{__("principal signature")}}
            </th>
        </thead>
        <tbody>
            <tr>
                <td style="min-height: 40px; padding: 30px;">&nbsp;</td>
                <td style="min-height: 40px; padding: 30px;">&nbsp;</td>
                <td style="min-height: 40px; text-align: center; font-weight: bold; font-size: 1.5em;">
                    @if ($sessionYearData->end_date != null)
                        {{ getSettings('school_address')['school_address'].', '.__('on the').' '.date('d-m-Y', strtotime($sessionYearData->end_date))}}
                    @else
                        {{ getSettings('school_address')['school_address'] }}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
