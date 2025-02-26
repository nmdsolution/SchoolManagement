<!DOCTYPE html>
<html>
<head>
    <title>{{__('Student Report')}}</title>
    {{--
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}">
    --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        body {
            position: relative; /* For pseudo-element positioning */

            /* Other styles */
            margin: 0;
            padding: 0;
            font-family: var(--bs-body-font-family);
            font-size: 60%;
            font-weight: var(--bs-body-font-weight);
            line-height: var(--bs-body-line-height);
            color: var(--bs-body-color);
            background-color: var(--bs-body-bg);
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        body::before {
            @if($reportWaterMark)
                content: '';
                background-image: url('{{ 'data:image/png;base64,' . base64_encode(@file_get_contents(public_path('storage/'.$reportWaterMark->getRawOriginal('message')))) }}');
            @else
                content: '';
                background-image: none;
            @endif

            background-repeat: no-repeat;
            background-position: center;
            background-size: 400px 400px;

            /* Add blur and transparency */
            filter: blur(100px); /* Adjust blur intensity (e.g., 5px, 10px) */
            opacity: 0.2; /* Adjust transparency (e.g., 0.1 to 0.3 for subtle visibility) */

            /* Positioning and sizing */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; /* Place it behind other content */
        }

        thead {
            background-color: {{$settings['report_color'] ?? ''}};
        }

        dd, legend {
            margin-bottom: .5rem;
        }

        progress, sub, sup {
            vertical-align: baseline;
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
            --bs-body-bg: #fff;
        }

        *, ::after, ::before {
            box-sizing: border-box;
        }

        hr {
            background-color: currentColor;
            border: 0;
            /*opacity: .25;*/
        }

        hr:not([size]) {
            height: 1px;
        }

        img, svg {
            vertical-align: middle;
        }

        table {
            caption-side: bottom;
            border-collapse: collapse;
            border-right: 1px solid #000000;
            font-size: 12px;
            width: 100%;
        }

        th {
            text-align: inherit;
            text-align: -webkit-match-parent;
            padding: 0.2rem;
            text-transform: uppercase; 
        }

        .student-table td {
            padding: 0.2rem;
            text-transform: uppercase;
        }

        td {
            padding: 0.2rem;
            text-transform: uppercase; 
        }


        tbody, td, tfoot, th, thead, tr {
            border: 0 solid;
            border-color: inherit;
        }

        .row {
            --bs-gutter-x: 1.5rem;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            margin-top: calc(-1 * var(--bs-gutter-y));
            margin-right: calc(-.5 * var(--bs-gutter-x));
            margin-left: calc(-.5 * var(--bs-gutter-x));
        }

        .row > * {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            padding-right: calc(var(--bs-gutter-x) * .5);
            padding-left: calc(var(--bs-gutter-x) * .5);
            margin-top: var(--bs-gutter-y);
        }

        .col {
            flex: 1 0 0;
        }

        .col-12, .row-cols-1 > * {
            flex: 0 0 auto;
            width: 100%;
        }

        .col-6, .row-cols-2 > * {
            flex: 0 0 auto;
            width: 50%;
        }

        .row-cols-3 > * {
            flex: 0 0 auto;
            width: 33.3333333333%;
        }

        .col-3, .row-cols-4 > * {
            flex: 0 0 auto;
            width: 25%;
        }

        .row-cols-5 > * {
            flex: 0 0 auto;
            width: 20%;
        }

        .row-cols-6 > * {
            flex: 0 0 auto;
            width: 16.6666666667%;
        }

        .col-2 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 16.66666667%;
        }

        .col-4, .col-5 {
            flex: 0 0 auto;
        }

        .col-4 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 33.33333333%;
        }

        .col-5 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 41.66666667%;
        }

        .col-7, .col-8 {
            flex: 0 0 auto;
        }

        .col-7 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 58.33333333%;
        }

        .col-8 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            width: 66.66666667%;
        }

        .col-9 {
            display: inline-block;
            padding-right: 15px;
            padding-left: 15px;
            flex: 0 0 auto;
            width: 75%;
        }

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
            border-color: #dee2e6;
        }

        .student-table {
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
            border-color: #dee2e6;
        }

         .student-table > tbody {
            vertical-align: inherit;
        }


        .table > :not(caption) > * > * {
            padding-left: .5rem;
            padding-right: .5rem;
            padding-top: .3rem;
            background-color: var(--bs-table-bg);
            border-bottom-width: 1px;
            box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
        }

        .table > tbody {
            vertical-align: inherit;
        }

        .table > thead {
            vertical-align: bottom;
        }

        .table > :not(:first-child) {
            border-top: 2px solid currentColor;
        }

        .table-sm > :not(caption) > * > * {
            padding: .25rem;
        }

        .table-bordered > :not(caption) > * {
            border-width: 1px 1px;
        }

        .table-bordered > :not(caption) > * > * {
            border-width: 1px 1px;
        }

        .align-middle {
            vertical-align: middle !important;
        }

        .border-bottom {
            border-bottom: 1px solid #dee2e6 !important;
        }

        .border-dark {
            border-color: #212529 !important;
        }

        .border-1 {
            border-width: 1px !important;
        }

        .w-100 {
            width: 100% !important;
        }

        .mt-2 {
            margin-top: .5rem !important;
        }

        .mt-3 {
            margin-top: 1rem !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .ms-3 {
            margin-left: 1rem !important;
        }

        .pe-0 {
            padding-right: 0 !important;
        }

        .text-start {
            text-align: left !important;
        }

        .text-center {
            text-align: center !important;
        }

        .bg-white {
            background-color: rgba(var(--bs-white-rgb), var(--bs-bg-opacity)) !important;
        }

        .my-1 {
            margin-top: 0.25rem !important;
            margin-bottom: 0.25rem !important;
        }

        .row {
            display: table;
            width: 100%;
        }

        .row [class^="col-"] {
            display: table-cell;
        }

        .ps-0 {
            padding-left: 0 !important;
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

        .student-image {
            width: 100%; /* Full width of container */
            height: 100%; /* Full height of container */
            object-fit: cover; /* Ensure image fits within cell without cropping */
            display: block;
        }

        .competence {
            font-size: {{ $settings['competence_font_size'] ?? 9 }}px;
        }

        .marks {
            font-size: {{ $settings['marks_font_size'] ?? 14 }}px;
            font-weight: {{ strpos($settings['marks_font_style'] ?? 'bold', 'bold') !== false ? 'bold' : 'normal' }};
            font-style: {{ strpos($settings['marks_font_style'] ?? 'bold', 'italic') !== false ? 'italic' : 'normal' }};
        }

        .teacher-name {
            font-size: {{ $settings['teacher_name_font_size'] ?? 12 }}px;
            font-weight: {{ strpos($settings['teacher_name_font_style'] ?? 'bold', 'bold') !== false ? 'bold' : 'normal' }};
            font-style: {{ strpos($settings['teacher_name_font_style'] ?? 'bold', 'italic') !== false ? 'italic' : 'normal' }};
        }

        .subject {
            font-size: {{ $settings['subject_font_size'] ?? 12 }}px;
            font-weight: {{ strpos($settings['subject_font_style'] ?? 'normal', 'bold') !== false ? 'bold' : 'normal' }};
            font-style: {{ strpos($settings['subject_font_style'] ?? 'normal', 'italic') !== false ? 'italic' : 'normal' }};
        }

        .subject-group {
            font-weight: {{ strpos($settings['subject_group_style'] ?? 'bold', 'bold') !== false ? 'bold' : 'normal' }};
            font-style: {{ strpos($settings['subject_group_style'] ?? 'bold', 'italic') !== false ? 'italic' : 'normal' }};
        }


    </style>
</head>
<body>
    <div class="watermark"></div>

    <div class="content">
    
        {{-- Main header starts --}}
        <div class="row text-center" style="display: flex;">
            <div class="col-5 report_left_header" style="position: relative;top: -50px;">
                <span style="font-size:12px">{!! $settings['report_left_header'] ?? '' !!}</span>
            </div>
            <div class="col-2">
                @if($reportHeaderLogo && file_exists(public_path('storage/'.$reportHeaderLogo->getRawOriginal('message'))))
                    <img src="{{'data:image/png;base64,'.base64_encode(@file_get_contents(public_path('storage/'.$reportHeaderLogo->getRawOriginal('message'))))}}" alt="Report Header" style="width: 120px; height: auto;"/>
                @else
                    <img src="" alt="Center Logo"/>                
                @endif
            </div>
            <div class="col-5 report_right_header" style="position: relative;top:-50px;">
                <span style="font-size:12px">{!! $settings['report_right_header'] ?? '' !!}</span>
            </div>
        </div>
        {{-- Main header ends --}}
    
        {{-- Session Year name --}}
        <div class="row text-center" style="position: relative;top:-40px; font-size: 15px;margin-bottom: 4px">
            <h3>{{__("Report")." ". $sessionYear->name }}</h3>
        </div>
        {{-- Session Year ends --}}
    
        {{-- Term name starts --}}
        <div class="row text-center" style="position: relative;top:-75px; font-size: 15px">
            <h3>{{$term->name}}</h3>
        </div>
        {{-- Term name ends --}}
    
    
    {{-- Student header starts --}}
    
        <div class="mt-3 row" style="position: relative;top: -100px;bottom: ">
            <div class="col-12 ps-0">
                <table class="table-bordered student-table border-dark" style="font-size: 15px">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <label>{{__('name')}}: </label>
                                <span><b>{{ $student->full_name ?? ''}}</b></span>
                            </td>
                            <td>
                                <label>{{__('Class')}}: </label>
                                <span><b>{{ $student->class_section->class->name . ' ' . $student->class_section->section->name  ?? '' }}</b></span>
                            </td>
                            <td rowspan="4" style="width: 110px; height: 110px; padding: 0px 0px 0px 5px; border-bottom-style: hidden; border-right-style: hidden; border-top-style: hidden;">
                                @if(file_exists(public_path('storage/'.$student->user->getRawOriginal('image'))) && is_file(public_path('storage/'.$student->user->getRawOriginal('image'))))
                                    <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('storage/'.$student->user->getRawOriginal('image'))))}}"
                                        alt="Student Image" class="student-image"/>
                                @else
                                    <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('storage/dummy_logo.png')))}}"
                                        alt="Student Image" class="student-image"/>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label>{{__("DOB")}}: </label>
                                <b>
                                    <span style="text-transform: none">
                                        {{$student->user->dob}}
                                        @php
                                            $place = $student->user->born_at;
                                        @endphp
                                        @if ($place)
                                            {{ __('in') }} {{ $place }}
                                        @endif
                                    </span>
                                </b>
                            </td>
                            <td>
                                <label>{{__("gender")}}: </label>
                                <span><b>{{strtoupper(substr($student->user->gender, 0, 1))}}</b></span>
                            </td>
                            <td>
                                <label>{{ __('class_enrollment') }}: </label>
                                <span><b>{{number_format($classPerformance->class_size,0)}}</b></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="1">
                                <label>{{ __('unique_identification_number') }}: </label>
                                <span><b>{{ $student->minisec_matricule ?? $student->admission_no }}</b></span>
                            </td>
                            <td>
                                <label>{{ __('repeater') }}: </label>
                                <span><b>{{ $student->is_repeater ? 'Yes' : 'No' }}</b></span>
                            </td>
                            <td>
                                <label>{{ __('number_of_subjects') }}: </label>
                                <span>
                                    <b>
                                        {{
                                            $examResultGroups->reduce(function ($count, $group) use ($examReportDetails) {
                                                return $count + $group->subjects->filter(function ($subject) use ($examReportDetails) {
                                                    $fetchSubjectData = $examReportDetails->firstWhere('subject_id', $subject->id);
                                                    return !empty($subject->class_subject) && $fetchSubjectData !== null && $fetchSubjectData->subject_avg>=0;
                                                })->count();
                                            }, 0)
                                        }}
                                    </b>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label>{{ __('Parent Info') }}: </label>
                                <span><b>{{ $student->parents->fatherRelationChild->name ?? ''}}</b></span>
                                <br>
    
                            </td>
                            <td>
                                <label>{{ __('number_passed') }}: </label>
                                <span>
                                    <b>
                                        {{
                                            $examResultGroups->reduce(function ($count, $group) use ($examReportDetails) {
                                                return $count + $group->subjects->filter(function ($subject) use ($examReportDetails) {
                                                    $fetchSubjectData = $examReportDetails->firstWhere('subject_id', $subject->id);
                                                    return !empty($subject->class_subject) && $fetchSubjectData !== null 
                                                        && $fetchSubjectData->subject_avg >= 10;
                                                })->count();
                                            }, 0)
                                        }}
                                    </b>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    
    {{-- Student header ends --}}
    
    {{-- Subject results table starts --}}
    
    <div class="mt-3 row" style="position: relative;top: -120px;">
        <div class="col-10 ps-0">
            <table class="table table-bordered border-dark" style="margin-right: 450px">
                <thead class="text-center align-middle">
                    <tr style="height: 15px">
                        <th rowspan="2" style="width: 180px">{{ __('subject_teacher_name') }}</th>
                        <th rowspan="2" style="width: 350px" >{{ __('competencies_evaluated') }}</th>
                        <th rowspan="2" style="width: 40px" class="verticalTableHeader">MK/20</th>
                        <th rowspan="2" style="width: 40px" class="verticalTableHeader">AV/20</th>
                        <th rowspan="2" style="width: 40px" class="verticalTableHeader">{{ __('Coef') }}</th>
                        <th rowspan="2" style="width: 40px" class="verticalTableHeader">{{ __('N X C') }}</th>
                        <th rowspan="2" style="width: 40px" class="verticalTableHeader">{{ __('grade') }}</th>
                        <th colspan="2">[Min - Max]</th>
                        <th rowspan="2" style="width: 100px">{{__("Remarks") .' '. __('and') .' '. __('signature') }}</th>
                    </tr>
                    <tr style="height: 10px">
                        <th style="width: 40px">{{__("Min")}}</th>
                        <th style="width: 40px">{{__("Max")}}</th>
                    </tr>
                </thead>
                <tbody>
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
                            @php
                            if(empty($subject->class_subject)) {
                                continue;
                            }

                            $fetchSubjectData = $examReportDetails->filter(function($data) use ($subject) {
                                return $data->subject_id === $subject->id;
                            })->first();

                            $subject_teachers = $subject->subject_teachers->filter(function($data) use ($student) {
                                return $data->class_section_id === $student->class_section_id;
                            });

                            if($fetchSubjectData == null) {
                                continue;
                            }

                            $weightage = $subject->class_subject->weightage ?? 0;
                            $nXc = $weightage * $fetchSubjectData->subject_avg;

                            if($nXc >= 0) {
                                $totalCoef += $weightage;
                                $overAllCoef += $weightage;
                                $totalNxC += $nXc;
                                $overAllNxC += $nXc;

                                if($fetchSubjectData->subject_avg < $low_subject_average) {
                                    $low_subjects[] = $subject->name;
                                }
                            }
                            @endphp

                            <tr class="text-center align-middle">
                                <!-- Subject and Teacher's Name -->
                                <td class="text-start">
                                    <span class="subject">{{strtoupper($subject->name)}}</span>
                                        @foreach($subject_teachers as $teacher)
                                                @php
                                                    $full_name = $teacher->teacher->user->full_name ?? '';
                                                    $nameParts = explode(' ', $full_name);
                                                    $abbreviatedName = count($nameParts) > 4
                                                        ? \Str::limit($nameParts[0], 1, '.') . ' ' . \Str::limit($nameParts[1], 1, '.') . ' ' . implode(' ', array_slice($nameParts, 2))
                                                        : $full_name;
                                                @endphp
                                            <br> <span class="ms-3 teacher-name">{{ $abbreviatedName   }}</span>
                                        @endforeach
                                </td>

                                <!-- Competencies Evaluated per Sequence -->
                                <td style="text-align: left; padding: 0px; text-transform:none;" class="competence">
                                    @php
                                        $sequenceCompetencies = [];
                                    @endphp
                                    @foreach($sequences as $sequence)
                                        @php
                                            // Generate a unique key for each sequence-subject pair
                                            $competencyKey = $sequence->id . '_' . $subject->id;

                                            // Retrieve the competency using the key
                                            $competency = $organized_competencies->get($competencyKey);

                                            // Add the sequence and competency to the list if it exists
                                            $sequenceCompetencies[] = $competency 
                                                ? $competency->competence 
                                                :''; // Use a slash if competency is not available
                                        @endphp
                                    @endforeach
                                    
                                    <!-- Display the competencies, separated by a line break -->
                                    {!! implode('<hr style="background-color: #000">', $sequenceCompetencies) !!}
                                </td>

                                <!-- Marks -->
                                <td>
                                    @php
                                        $sequenceMarks = [];
                                    @endphp
                                    @foreach($sequences as $sequence)
                                        @php
                                            // Check if marks exist for this sequence
                                            if (isset($fetchSubjectData->sequence_marks->{$sequence->id})) {
                                                    $formattedMark = number_format($fetchSubjectData->sequence_marks->{$sequence->id}, 2);
                                                    // Add marks with styling if needed
                                                    $sequenceMarks[] = $fetchSubjectData->sequence_marks->{$sequence->id} >= 10 
                                                        ? '<span class="marks">' . $formattedMark . '</span>' 
                                                        : '<span class="marks" style="color: #f00;">' . $formattedMark . '</span>';
                                            } else {
                                                // If no marks are found, display slash
                                                $sequenceMarks[] = '<span class="marks"> / </span>';
                                            }
                                        @endphp
                                    @endforeach

                                    <!-- Display marks for all sequences -->
                                    {!! implode('<br><br>', $sequenceMarks) !!}
                                </td>


                                <!-- AV/20 -->
                                <td class="marks">{{ $fetchSubjectData->subject_avg < 0 ? '/' : number_format($fetchSubjectData->subject_avg, 2) }}</td>

                                <!-- Coef -->
                                <td class="marks">{{ $weightage }}</td>

                                <!-- AV x coef -->
                                <td class="marks">{{ $nXc < 0 ? '/' : number_format($nXc, 2) }}</td>

                                <!-- Grade -->
                                <td class="marks">{{ strtoupper($fetchSubjectData->subject_grade) }}</td>

                                <!-- Min Marks -->
                                <td class="marks">{{ $subject->class_details ? number_format($subject->class_details->min, 2) : '/' }}</td>

                                <!-- Max Marks -->
                                <td class="marks">{{ $subject->class_details ? number_format($subject->class_details->max, 2) : '/' }}</td>

                                <!-- Remarks and Signature -->
                                <td>{{ strtoupper($fetchSubjectData->subject_remarks) }}</td>
                            </tr>
                        @endforeach

                        <tr class="text-center subject-group">
                            <td colspan="4">
                                <i>{{ $group->name }}</i>
                            </td>
                            <td >{{ $totalCoef }}</td>
                            <td>{{ number_format($totalNxC, 2) }}</td>
                            <td colspan="5" class="text-start">Moy / Av: {{ $totalCoef !== 0 ? number_format($totalNxC / $totalCoef, 2) : '0.00' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
        <div class="row g-0" style="position: relative;top: -135px;">
            {{-- Council Decision --}}
            <div class="col-5" style="padding: 0">
                <table class="table text-center border-1 border-dark w-100">
                    <thead class="text-center">
                        <tr>
                            <th colspan="4">{{ __("council_decision") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Row 1 -->
                        <tr>
                            <td>{{ __('justified_absence') }}</td>
                            <td>{{$attendance != null ? $attendance->justified_absences : '/'}}</td>
                            <td>{{ __('unjustified_absence') }}</td>
                            <td>{{$attendance != null ? $attendance->unjustified_absences : '/'}}</td>
                        </tr>
                        <!-- Row 2 -->
                        <tr>
                            <td>{{ __('Blame') }}</td>
                            <td>
                                @if ($settings['report_blame_min'] <= $attendance->total_absences
                                    && $settings['report_blame_max'] > $attendance->total_absences)
                                    {{ __("yes") }}
                                @else
                                    {{ __("no") }}
                                @endif
                            </td>
                            <td>{{ __('warning') }}</td>
                            <td>
                                @if ($settings['report_warning_min'] <= $attendance->total_absences
                                    && $settings['report_warning_max'] > $attendance->total_absences)
                                    {{ __("yes") }}
                                @else
                                    {{ __("no") }}
                                @endif
                            </td>
                        </tr>
                        <!-- Row 3 -->
                        <tr>
                            <td>{{ __('lateness') }}</td>
                            <td>/</td>
                        <td>{{__("Honor")}}</td>
                        <td>
                            @if ($studentClassPerformance->avg >= $settings['report_honor_roll']
                            && $attendance->total_absences < $settings['report_honor_roll_absences'] )
                                {{ __("yes") }}
                            @else
                                {{__("no")}}
                            @endif
                        </td>                          
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Class Report --}}
            <div class="col-3" style="padding: 0 15px">
                <table class="table table-bordered border-dark w-100" style="margin-top: 20px">
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

                    </tbody>
                </table>
            </div>

            {{-- Student Performance --}}
            <div class="col-4" style="padding: 0">
                <table class="table table-bordered border-dark w-100">
                @php
                    $term_sequences = array();
                    $cols = 1;
                    foreach ($terms as $term) {
                        $term_seqs = $all_sequences->filter(function($data) use($term){
                            return $data->exam_term_id == $term->id;
                        });
                        foreach ($term_seqs as $sequence) {
                            if(!isset($term_sequences[$term->id])) $term_sequences[$term->id] = array();

                            $cols +=1;
                            $termSequence = $examReportStudentSequence->filter(function($data) use($term,$sequence,$student){
                                return $data->exam_term_id===$term->id
                                    && $data->exam_sequence_id===$sequence->id
                                    && $data->class_section_id==$student->class_section_id;
                            })->first();

                            $term_sequences[$term->id][$sequence->id] = (object)[
                                'name' => $sequence->name,
                                'avg' => $termSequence && isset($termSequence->avg) ? number_format($termSequence->avg, 2) : null,
                                'rank' => $termSequence && isset($termSequence->rank) ? $termSequence->rank : null
                            ];
                        }
                    }
                    foreach ($terms as $term) {
                        $termPerformance = $studentTermPerformance->filter(function($data) use($term){
                            return $data->exam_report->exam_term_id===$term->id;
                        })->first();
                        $term_sequences[$term->id]['term'] = (object)array(
                            'name' => __('Term'),
                            'avg' => $termPerformance ? number_format($termPerformance->avg,2) : '//',
                            'rank' => $termPerformance ? $termPerformance->rank : '//'
                        );
                        $cols +=1;
                    }
                @endphp
                    <thead class="text-center">
                        <th colspan="{{$cols}}">{{__("Student's Performance")}}</th>
                    </thead>
                    <tbody>
                        {{-- <tr class="text-center">
                            <td rowspan="2"></td>
                            @foreach($terms as $term)
                                <td colspan="{{ count($term_sequences[$term->id])}}">{{$term->name}}</td>
                            @endforeach
                        </tr> --}}
                        <tr class="text-center">
                            <td></td>
                            @foreach($terms as $term)
                                @foreach ($term_sequences[$term->id] as $sequence)
                                    <td>{{$sequence->name}}</td>
                                @endforeach
                            @endforeach
                        </tr>                        
                        <tr>
                            <td><b>{{__("Avg")}}</b></td>
                            @foreach($terms as $term)
                                @foreach ($term_sequences[$term->id] as $sequence)
                                    <td class="marks">{{ $sequence->avg !== null ? $sequence->avg : '//' }}</td>
                                @endforeach
                            @endforeach                        
                        </tr>
                        <tr>
                            <td><b>{{__("Rank")}}</b></td>
                            @foreach($terms as $term)
                                @foreach ($term_sequences[$term->id] as $sequence)
                                    <td><b>{{$sequence->rank < 0 ? "NA" : $sequence->rank}}</b></td>
                                @endforeach
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


    {{-- Subject results table ends --}}
    

     {{--Signatures table  --}}
    <div class="col-12 row" style="position: relative;top: -130px;">
            <table class="table table-bordered border-dark" style="min-height: 30px;">
                <thead class="text-center">
                    <tr>
                        <th style="padding: 5px;">{{ __("parent signature") }}</th>
                        <th style="padding: 5px;">{{ __("teacher signature") }}</th>
                        @if (!empty($settings['discipline_master_signature']) && $settings['discipline_master_signature'] == 1)
                            <th style="padding: 5px;">{{  __("Signature") . " " . __("discipline_master")}}</th>
                        @endif
                        @if (!empty($settings['council_decision']) && $settings['council_decision'] == 1)
                            <th style="padding: 5px;">{{ __("council_decision") }}</th>
                        @endif
                        <th style="padding: 5px;">{{ __("principal signature") }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="min-height: 30px; padding: 20px;">&nbsp;</td>
                        <td style="min-height: 30px; font-size: 1.2em;">
                            {{ $student->class_section->teacher->user->full_name ?? '' }}
                        </td>
                        @if (!empty($settings['discipline_master_signature']) && $settings['discipline_master_signature'] == 1)
                            <td style="min-height: 30px; padding: 20px;">&nbsp;</td>
                        @endif
                        @if (!empty($settings['council_decision']) && $settings['council_decision'] == 1)
                            <td style="min-height: 30px; padding: 20px;">&nbsp;</td>
                        @endif
                        <td style="min-height: 30px; font-size: 1.2em;">

                            {{ getSettings('school_address')['school_address'] }}
                        
                            {{-- @if ($term->end_date != null)
                                {{ getSettings('school_address')['school_address'] . ', ' . __('on the') . ' ' . date('d-m-Y', strtotime($term->end_date)) }}
                            @else
                                {{ getSettings('school_address')['school_address'] }}
                            @endif --}}
                        </td>
                    </tr>
                </tbody>
            </table>
    </div>

    
    {{-- Footer with report date --}}
    {{-- @if($settings['report_date_generated'] ?? false) --}}
    <div style="font-style: italic; margin-top: -140px; font-size: 12px">
        <hr>    
        <div>
            <b>
                {{ __('Report printed on') . " " . now()->format('d-m-Y') . " " . strtolower(__('at')) . " " . now()->format('H:i') . " " . __('with') . " YADIKO - " . __('School Management System') }}           
            </b>
            <b style="float: right; display: flex;margin: 0px 2px">
                <span><a href="https://www.yadiko.com" style="text-decoration: none;color: #000" target="_blank">www.yadiko.com</a></span> 
                <span>| +237 691158469</span>
                <span>| +237 620311776</span>
            </b>
        </div>
    </div>
    {{-- @endif --}}

</div>

</body>
</html>
