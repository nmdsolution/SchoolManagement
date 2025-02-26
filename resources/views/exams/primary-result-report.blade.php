<!DOCTYPE html>
<html>
<head>
    <title>{{__('Student Report')}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        th {
            background-color: {{$settings['report_color'] ?? 'blue'}} !important;
        }

        thead {
            background-color: {{$settings['report_color'] ?? 'blue'}} !important;
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

        hr {
            margin: 1rem 0;
            border: 0;
            opacity: .25
        }

        hr:not([size]) {
            height: 1px
        }

        img, svg {
            vertical-align: middle
        }

        .report_left_header p, .report_right_header p {
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;/
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            font-size: 75%;
            background-color: var(--bs-body-bg);
            -webkit-text-size-adjust: 100%;
            font-weight: var(--bs-body-font-weight);
            color: var(--bs-body-color);
            -webkit-tap-highlight-color: transparent;
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .result-table td {
            text-align: center;
        }

        .text-start {
            text-align: left !important
        }

        .text-center {
            text-align: center !important
        }

        .row-cols-3 > * {
            flex: 0 0 auto;
            width: 33.3333333333%
        }

        .row-cols-5 > * {
            flex: 0 0 auto;
            width: 20%
        }

        .row-cols-6 > * {
            flex: 0 0 auto;
            width: 16.6666666667%
        }

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

        .report_left_header p, .report_right_header p {
            margin: 0;
            padding: 0;
        }

        .align-center {
            display: flex;
            align-items: center;
        }

        .justify-content-start{justify-content:flex-start!important}

        .center-titles {
            text-align: center;
            white-space: nowrap;
            padding: 1.5rem 0 !important;
        }

        .align-middle {
            vertical-align: middle !important;
        }

        .ps-0 {
            padding-left: 0 !important;
        }

        .row [class^="col-"] {
            display: table-cell;
        }

        .d-flex {
            display: flex !important;
        }

        .w-100 {
            width: 100% !important;
        }

        .table-row {
            display: table-row;
        }

        .table-cell {
            display: table-cell;
            padding: 10px;
            vertical-align: top;
        }

        .border-0 {
            border: 0 !important;
        }

        .report-header {
            font-size: 20px;
        }

        .info-header {
            margin-top: -20px;
        }

        .special-row td {
            font-weight: bold;
        }

        .result-header {
            font-weight: 700;
            width: 51%;
        }

        .no-space-right {
            padding-right: 0 !important;
            margin-right: 0 !important;
        }
    </style>
</head>
<body class="bg-white" style="height: 100px;">

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

    <div style="margin-top: -10px;">
        <div class="row text-center report-header" >
            <h3>{{__("report_card")." ". $sessionYear->name }}  {{$term->name}}</h3>
        </div>
    </div>

    @php
        $parentGuardianInfo = trans("no_name_provided");

       if ($student->father_id) {
           $parentGuardianInfo = \App\Models\Parents::query()->find($student->father_id)?->first_name;
       } else if ($student->mother_id) {
           $parentGuardianInfo = \App\Models\Parents::query()->find($student->mother_id)?->first_name;
       } else if ($student->guardian_id) {
           $parentGuardianInfo = \App\Models\Parents::query()->find($student->guardian_id)?->first_name;
       }

       $repeaterStatus = $student->repeater ? "Yes" : "No";

       $totalSubjects = 0;
    @endphp

    <div class="table pt-0 border-0 info-header" style="position: relative;">
        <div class="d-flex table-row">
            <div class="table-cell border-0 ps-0">
                <table>
                    <tr>
                        <th colspan="2">NAME OF STUDENT: {{ strtoupper($student->user->full_name )}}</th>
                        <th>CLASS: {{$student->class_section->class->name.' '.$student->class_section->section->name}}</th>
                    </tr>
                    <tr>
                        <td> {{ __("date_and_pob") }}: {{$student->user->dob}} {{ $student->born_at }}</td>
                        <td>{{ __('gender') }}: {{strtoupper(substr($student->user->gender, 0, 1)) }}</td>
                        <td>{{ __('class size') }}: {{ 50 }}</td>
                    </tr>
                    <tr>
                        <td>{{ __("unique_identification_number") }} : {{ $student->admission_no  }} </td>
                        <td>{{ __('repeater') }}: {{ $repeaterStatus }}</td>
                        <td rowspan="2" style="font-weight: bold;">{{ __("class_master") }}: {{  $student->studentSessions()->currentSessionYear()->class_section->teacher->user->first_name }} </td>
                    </tr>

                    <tr>
                        <td colspan="2">{{ __("parent_guardian_info") }}:  {{ $parentGuardianInfo }}</td>
                    </tr>
                </table>
            </div>

            <div class="table-cell border-0" style="position: relative; width: 16%;">
                <div class="row justify-content-start">
                    @if(file_exists(public_path('storage/'.$student->user->getRawOriginal('image'))) && is_file(public_path('storage/'.$student->user->getRawOriginal('image'))))
                        <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('storage/'.$student->user->getRawOriginal('image'))))}}"
                             alt="Report Header" class="w-100 no-space-right"/>
                    @else
                        <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('storage/dummy_logo.png')))}}"
                             alt="Report Header" class="w-100 no-space-right"/>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <table style="position: relative; top: -15px;">
        <tr>
            <th>Disciplines</th>
            <th>Evaluation</th>
            <th class="center-titles">Bareme</th>
            @foreach($sequences as $row)
                <th class="center-titles">{{$row->name}}</th>
            @endforeach
        </tr>

        @php
            $low_subjects = [];
            $overAllCoef = 0;
            $overAllNxC = 0;
        @endphp

        @foreach($examResultGroups as $group)
            @php
                $totalCoef = 0;
                $totalNxC = 0;

                $subjects = $group->subjects;
            @endphp

            @if (count($subjects))

                <tr class="text-center align-middle">

                    @php

                        $activeSubjects = $subjects->filter(function ($subject) use ($examReportDetails) {
                            $fetchSubjectData = $examReportDetails->filter(function($data)use($subject){
                                return $data->subject_id===$subject->id;
                            })->first();

                            if(!$fetchSubjectData==null){
                                return $fetchSubjectData->subject_id;
                            }
                            return '';
                        });

                        $activeSubjectCounts = count($activeSubjects);

                        $totalSubjects += $activeSubjectCounts;
                    @endphp

                    <td class="text-start" style="width: 20%" rowspan={{ $activeSubjectCounts }}> {{strtoupper($group->name)}} </td>

                    @foreach($subjects as $subject)

                        @php
                            // fetch the examReportDetails that belong to this subject for this student.
                            $fetchSubjectData = $examReportDetails->filter(function($data)use($subject){
                                return $data->subject_id===$subject->id;
                            })->first();

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

                        <td style="width: 37%;">{{ $subject->name }}</td>

                        <td class="text-center">20</td>

                        @php
                            $sequenceCount = 0;
                        @endphp

                        @foreach($sequences as $sequence)

                            @php $sequenceCount++; @endphp

                            @if (isset($fetchSubjectData->sequence_marks->{$sequence->id}))
                                @if ($fetchSubjectData->sequence_marks->{$sequence->id}>=10)
                                    <td class="text-center">{{ number_format($fetchSubjectData->sequence_marks->{$sequence->id},2)}}</td>
                                @else
                                    <td class="text-center" style="color: #f00;">{{ number_format($fetchSubjectData->sequence_marks->{$sequence->id},2)}}</td>
                                @endif
                            @else
                                <td class="text-center">/</td>
                            @endif


                        @endforeach
                </tr>
                    @endforeach
            @endif
        @endforeach

        <tr class="special-row">
            <td class="text-center">Total</td>
            <td class="text-center">{{ $totalSubjects }} {{ __('subjects') }}</td>
            <td class="text-center">10</td>
            <td class="text-center">10</td>
            <td class="text-center">10</td>
        </tr>
    </table>

    <table style="position: relative; top: -10px;">
        <tr>
            <th rowspan="4" style="width: 20%;">Result</th>

            <td colspan="3" class="result-header" > {{ __("sequence_average") }} </td>
            @foreach ($sequences as $sequence)
                <td class="text-center">10</td>
            @endforeach
        </tr>

        <tr>
            <td colspan="3" class="result-header" >{{ __('sequence_rank') }}</td>
            @foreach ($sequences as $sequence)
                <td class="text-center">10</td>
            @endforeach
        </tr>

        <tr>
            <td colspan="3" class="result-header" > {{ __('term_average') }}</td>
            <td class="text-center" colspan="{{ $sequenceCount }}">1</td>
        </tr>

        <tr>
            <td colspan="3" class="result-header" > {{ __("term_rank") }}</td>
            <td class="text-center" colspan="{{ $sequenceCount }}">0</td>
        </tr>
    </table>

    <table>
        <thead class="text-center">
            <th style="padding: 4px; text-align: center;">
                {{__("parent signature")}}
            </th>
            <th style="padding: 4px; text-align: center;">
                {{__("teacher signature")}}
            </th>
            <th style="padding: 4px; text-align: center;">
                {{__("principal signature")}}
            </th>
        </thead>
            <tr>
                <td style="min-height: 30px; padding: 30px;">&nbsp;</td>
                <td style="min-height: 30px; padding: 30px;">&nbsp;</td>
                <td style="min-height: 30px; text-align: center; vertical-align: top; font-weight: bold; font-size: 1.5em;">
                    @if ($term->end_date != null)
                        {{ getSettings('school_address')['school_address'].', '.__('on the').' '.date('d-m-Y', strtotime($term->end_date))}}
                    @else
                        {{ getSettings('school_address')['school_address'] }}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
