<!DOCTYPE html>
<html>
<head>
    <title>Student Report</title>
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        thead {
            background-color: {{$settings['report_color'] ?? ''}};
        }

        .report-box {
            border: 1px solid black;
            padding: 50px 100px;
            margin-top: 50px;
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
            padding: 0.5rem 0 !important;
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

        /*table {*/
        /*    text-align:center;*/
        /*    table-layout : fixed;*/
        /*    width:150px*/
        /*}*/

    </style>
</head>
<body class="bg-white container report-box">

<div class="row text-center" style="display: flex;">
    <div class="col-5">
        {!! $settings['report_left_header'] !!}
    </div>
    <div class="col-2">
        @if($reportHeaderLogo)
            <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('storage/'.$reportHeaderLogo->getRawOriginal('message'))))}}" alt="Report Header" class="w-100"/>
        @endif
    </div>
    <div class="col-5">
        {!! $settings['report_right_header'] !!}
    </div>
</div>

<div class="col-12 row mt-4">
    <div class="col-5">
        <div class="row my-2">
            <div class="col-4 pe-0">Student Name :</div>
            <div class="col border-bottom border-dark border-1"><b>{{$student->user->full_name}}</b></div>
        </div>
        <div class="row my-2">
            <div class="col-4 pe-0">DOB :</div>
            <div class="col border-bottom border-dark border-1"><b><b>{{$student->user->dob}}</b></b></div>
        </div>
        <div class="row my-2">
            <div class="col-4 pe-0">Sex :</div>
            <div class="col border-bottom border-dark border-1"><b>{{strtoupper($student->user->gender)}}</b></div>
        </div>
        <div class="row my-2">
            <div class="col-4 pe-0">Matricule :</div>
            <div class="col border-bottom border-dark border-1"><b>{{$student->admission_no}}</b></div>
        </div>
    </div>
    <div class="col-5">
        <div class="row my-2">
            <div class="col-5 pe-0">CLASS :</div>
            <div class="col border-bottom border-dark border-1"><b>{{$student->class_section->full_name}}</b></div>
        </div>
        <div class="row my-2">
            <div class="col-5 pe-0">CLASS TEACHER :</div>
            <div class="col border-bottom border-dark border-1"><b>{{$student->class_section->teacher->user->full_name}}</b></div>
        </div>
        <div class="row my-2">
            <div class="col-5 pe-0">PARENTS :</div>
            <div class="col border-bottom border-dark border-1"></div>
        </div>
        <div class="row my-2">
            <div class="col-5 pe-0">PARENTS CONTACT:</div>
            <div class="col border-bottom border-dark border-1"></div>
        </div>
    </div>
    <div class="col-2">
        <div class="row my-2">
            @if(file_exists(public_path('storage/'.$student->user->getRawOriginal('image'))))
                <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('storage/'.$student->user->getRawOriginal('image'))))}}" alt="Report Header" class="w-100"/>
            @else
                <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('storage/dummy_logo.png')))}}" alt="Report Header" class="w-100"/>
            @endif
        </div>
    </div>
    {{--
    <div class="col-1 text-end">--}}
    {{-- <img src="https://i.ibb.co/3Yk8w95/To-Text-03-27-2023-09-14-1-html-fbacb45a6f4fc932.gif" alt="To-Text-03-27-2023-09-14-1-html-fbacb45a6f4fc932" border="0">--}}
    {{----}}{{-- <img src="{{$student->user->image}}" alt="Student Image" class="w-25 img-fluid">--}}
    {{--    </div>
--}}
</div>
<div class="mt-3 row">
    <div class="col-9 ps-0">
        <table class="table table-bordered border-dark">
            <thead class="text-center align-middle">
            <tr>
                <th rowspan="2">SUBJECTS</th>
                @foreach($term->sequence as $row)
                    <th rowspan="2" class="verticalTableHeader">{{$row->name}}</th>
                @endforeach
                <th rowspan="2" class="verticalTableHeader">Total</th>
                <th rowspan="2" class="verticalTableHeader">Coef</th>
                <th rowspan="2" class="verticalTableHeader">N X C</th>
                <th rowspan="2" class="verticalTableHeader">Rank</th>
                <th colspan="2">Classe</th>
                <th rowspan="2" class="verticalTableHeader">Grades</th>
                <th rowspan="2" class="verticalTableHeader">Remarks</th>
            </tr>
            <tr>
                <th class="verticalTableHeader">Min</th>
                <th class="verticalTableHeader">Max</th>
            </tr>
            </thead>
            <tbody>
            @php
                $low_subjects = [];
            @endphp

            @foreach($examResultGroups as $group)
                @php
                    $totalCoef = 0;
                    $totalNxC = 0;
                @endphp
                @foreach($group->subjects as $subject)
                    <tr class="text-center align-middle">
                        @php

                            $fetchSubjectData = $examReportDetails->filter(function($data)use($subject){
                            return $data->subject_id===$subject->id;
                            })->first();

                            if($fetchSubjectData==null){
                                continue;
                            }
                            $weightage = $subject->class_subject->weightage??0;
                            $totalCoef+=$weightage;
                            $nXc = $weightage * $fetchSubjectData->subject_avg;
                            $totalNxC +=$nXc;
                            if($fetchSubjectData->subject_avg < $low_subject_average){
                            $low_subjects[] = $subject->name;
                            }

                        @endphp

                                <!--Subject & Teacher Name-->
                        <td class="text-start">
                            {{$subject->name}}
                            @foreach($subject->teacher as $teacher)
                                <br> <span class="ms-3">{{$teacher->user->full_name}}</span>
                            @endforeach
                        </td>


                        @foreach($term->sequence as $sequence)
                            <!--Sequenece Wise Marks-->
                            <th>{{$fetchSubjectData->sequence_marks-> {
                            $sequence->id
                        }??0 }}
                            </th>
                        @endforeach

                        <!--Total Subject Average -->
                        <td>{{$fetchSubjectData->subject_avg }}</td>


                        <!--Subject Weightage-->
                        <td>{{$subject->class_subject->weightage ?? 0}}</td>

                        <!--N x C-->
                        <td><b>{{$nXc}}</b></td>

                        <!--Rank-->
                        <td>{{$fetchSubjectData->subject_rank }}</td>

                        <!--Class Min marks-->
                        <td>{{$subject->class_details->min}}</td>

                        <!--Class Max marks-->
                        <td>{{$subject->class_details->max}}</td>

                        <!--Grades-->
                        <td>{{$fetchSubjectData->subject_grade}}</td>
                        <td>{{$fetchSubjectData->subject_remarks}}</td>
                    </tr>
                @endforeach
                <tr class="text-center">
                    <th colspan="4">
                        <i>{{$group->name}} </i>
                    </th>
                    <td><b>{{$totalCoef}}</b></td>
                    <td><b>{{$totalNxC}}</b></td>
                    <td colspan="5" class="text-start"><b>Moy / Av : {{$totalNxC / $totalCoef}}</b></td>
                </tr>
            @endforeach

            {{--            @if(count($examResultGroups)<4)--}}
            {{--                @for($i=1;$i<=8;$i++)--}}
            {{--                    <tr class="text-center align-middle" style="height: 40px;">--}}
            {{--                        <td class="text-start"></td>--}}
            {{--                        @foreach($term->sequence as $sequence)--}}
            {{--                            <th></th>--}}
            {{--                        @endforeach--}}
            {{--                        <td></td>--}}
            {{--                        <td></td>--}}
            {{--                        <td></td>--}}
            {{--                        <td></td>--}}
            {{--                        <td></td>--}}
            {{--                        <td></td>--}}
            {{--                        <td></td>--}}
            {{--                        <td></td>--}}
            {{--                    </tr>--}}
            {{--            @endfor--}}
            {{--            @endif--}}
        </table>
        <div class="row">
            <div class="col-8">
                <table class="mt-2 table table-bordered border-dark">
                    <thead class="text-center">
                    <th colspan="{{count($terms)*2+1}}">Premium Performamce</th>
                    </thead>
                    <tbody>

                    <tr class="text-center">
                        <td rowspan="2"></td>
                        @foreach($terms as $term)
                            <td colspan="2">{{$term->name}}</td>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($terms as $term)
                            <td>Avg</td>
                            <td>Rank</td>
                        @endforeach
                    </tr>
                    @php
                        $examSequence = [];
                    @endphp
                    @foreach($sequences as $sequence)
                        <tr>
                            <td>{{$sequence->name}}</td>
                            @foreach($terms as $term)
                                @php
                                    $termSequence = $examReportStudentSequence->filter(function($data) use($term,$sequence){
                                    return $data->exam_term_id===$term->id && $data->exam_sequence_id===$sequence->id;
                                    })->first();
                                    if(!isset($examSequence[$term->id][$sequence->id])){
                                    if(!isset($examSequence[$term->id])){
                                    $examSequence[$term->id]['counter'] = 0;
                                    $examSequence[$term->id]['avg_sum'] = 0;
                                    $examSequence[$term->id]['rank_sum'] = 0;
                                    }
                                    $examSequence[$term->id][$sequence->id] = [
                                    'avg'=>null,'rank'=>null
                                    ];

                                    }
                                    $examSequence[$term->id][$sequence->id]['avg'] += $termSequence->avg??0;
                                    $examSequence[$term->id][$sequence->id]['rank'] += $termSequence->rank??0;
                                    if(isset($termSequence->avg)){
                                    $examSequence[$term->id]['counter']++;
                                    $examSequence[$term->id]['avg_sum'] += $termSequence->avg;
                                    $examSequence[$term->id]['rank_sum'] += $termSequence->rank;
                                    }
                                @endphp
                                <td>{{$termSequence->avg ?? "//"}}</td>
                                <td>{{$termSequence->rank ?? "//"}}</td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tr>
                        <td>Trim / Term</td>
                        {{--@dd($examSequence);--}}
                        @foreach($terms as $term)
                            <td>{{!empty($examSequence[$term->id]['counter']) ? $examSequence[$term->id]['avg_sum'] / $examSequence[$term->id]['counter'] : "//"}}</td>
                            <td>{{!empty($examSequence[$term->id]['counter']) ? $examSequence[$term->id]['rank_sum'] / $examSequence[$term->id]['counter'] : "//"}}</td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-4">
                <table class="mt-2 table table-bordered border-dark mb-0">
                    <thead class="text-center">
                    <th colspan="2">Performamce Summary</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Avg</td>
                        <td>{{$studentClassPerformance->avg}}</td>
                    </tr>
                    <tr>
                        <td>Rank</td>
                        <td>{{$studentClassPerformance->rank}}</td>
                    </tr>
                    <tr>
                        <td>Honor</td>
                        <td>{{$studentClassPerformance->avg >$settings['report_honor_roll']  ? "Yes" : "No"}}</td>
                    </tr>

                    </tbody>
                </table>
                <table class="mt-auto table table-bordered border-dark">
                    <thead class="text-center">
                    <th colspan="2">Class Summary</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Min Avg</td>
                        <td>{{$classPerformance->min_avg}}</td>
                    </tr>
                    <tr>
                        <td>Class Avg</td>
                        <td>{{$classPerformance->class_avg}}</td>
                    </tr>
                    <tr>
                        <td>Max Avg</td>
                        <td>{{$classPerformance->max_avg}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-3">
        <table class="table col-12">
            <thead class="text-center align-middle border-1 border-dark">
            <tr>
                <th>Conduct</th>
            </tr>
            </thead>
            <tbody class="border-1 border-dark">
            <tr>
                <td class="border-0">Absences NJ (h)</td>
            </tr>
            <tr>
                <td class="border-0">Absences NJ (h)</td>
            </tr>
            <tr>
                <td class="border-0">Absences NJ (h)</td>
            </tr>
        </table>
        @if(count($effective_domain))
            <table class="table table-bordered" style="">
                <thead class="text-center align-middle border-1 border-dark">
                <tr>
                    <th>Effective Domain</th>
                    <th>5</th>
                    <th>4</th>
                    <th>3</th>
                    <th>2</th>
                    <th>1</th>
                </tr>
                </thead>
                <tbody class="border-1 border-dark">
                @foreach($effective_domain as $row)
                    <tr>
                        <td>{{$row->name}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endforeach
            </table>
        @endif

        <table class="table table-bordered" style="">
            <thead class="text-center align-middle border-1 border-dark">
            <tr>
                <th colspan="3">Grade Scale</th>
            </tr>
            </thead>
            <tbody class="border-1 border-dark">
            @foreach($grades as $grade)
                <tr>
                    <td>{{$grade['grade']}}</td>
                    <td>{{$grade['starting_range']}}-{{$grade['ending_range']}}%</td>
                    <td>{{$grade['remarks']}}</td>
                </tr>
            @endforeach
        </table>


        <table class="table table-bordered" style="">
            <thead class="text-center align-middle border-1 border-dark">
            <tr>
                <th colspan="3">Low Subject</th>
            </tr>
            </thead>
            <tbody class="border-1 border-dark">
            <tr class="text-center">
                <td>{{implode(' , ', $low_subjects)}}</td>
            </tr>
        </table>
        <div class="border border-dark" style="height: 280px;">
            <h2 class="ms-2 mt-2">Signature :</h2>
        </div>
    </div>
</div>
<div class="col-12 border border-dark mt-3" style="height: 100px">
    <h2 class="ms-2 mt-2">Comments :</h2>
</div>
</body>
</html>