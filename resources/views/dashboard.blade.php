<?php $page = 'index'; ?>
@extends('layout.master')
@section('title')
    Dashboard
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('title')
            {{ __('Welcome Admin') }} !
        @endslot
        @slot('li_1')
            {{ __('Home') }}
        @endslot
        @slot('li_2')
            {{ __('Admin') }}
        @endslot
    @endcomponent

    <!-- Counter -->
    <div class="row">
        @if(auth()->user()->teacher && !isPrimaryCenter())   
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <a class="card-body" href="{{ route('exams.sequential.upload-marks') }}">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h3>{{ __('upload') . ' ' . __('Marks') }}</h3>
                        </div>
                        <div class="db-icon">
                            <i class="fa fa-book menu-icon text-black"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endif

        @if(auth()->user()->teacher && isPrimaryCenter())
        <div class="col-xl-4 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <a class="card-body" href="{{ route('competency.marks.index') }}">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h5>{{ __('upload') . ' ' . __('Competency') . ' ' . __('Marks') }}</h5>
                        </div>
                    </div>
                </a><a class="card-body" href="{{ route('competency.marks.upload-student') }}">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h5>{{ __('upload') . ' ' . __('Student') . ' ' . __('Marks') }}</h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endif

        @hasrole('Center')
        <div class="col-xl-4 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <div class="card-body">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h6>{{ __('students') }}</h6>
                            <h3>{{ $data['students'] }}</h3>
                            <div class="m-1">
                                <i class="fa fa-mars menu-icon"></i> <span>{{ $data['total_boys'] }}</span>
                                <i class="fa fa-venus menu-icon m-l-15"></i> <span>{{ $data['total_girls'] }}</span>
                            </div>
                        </div>
                        <div class="db-icon">
                            <img src="{{ URL::asset('/assets/img/icons/dash-icon-01.svg') }}" alt="Dashboard Icon">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <div class="card-body">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h6>{{ __('teacher') }}</h6>
                            <h3>{{ $data['teachers'] }}</h3>
                        </div>
                        <div class="db-icon">
                            <img src="{{ URL::asset('/assets/img/teacher.png') }}" alt="Dashboard Icon">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endhasrole
        
        @if(auth()->user()->teacher)
   {{--     <div class="col-xl-4 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <div class="card-body">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h5><a href="{{ url('lesson') }}">{{ __('create_lesson') }}</a></h5>
                            <hr />
                            <h5><a href="{{ url('lesson-topic') }}">{{ __('create_topic') }}</a></h5>
                            <hr />
                            <h5><a href="{{ url('competency') }}">{{ __('competency-create') }}</a></h5>
                        </div>
                        <div class="db-icon">
                            <img src="{{ URL::asset('/assets/img/teacher.png') }}" alt="Teacher">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <div class="card-body">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h5><a href="{{ route('exams.get-result') }}">{{ __('students') . ' ' . __('Result') }}</a></h5>
                        </div>
                        <div class="db-icon">
                            <img src="{{ URL::asset('/assets/img/teacher.png') }}" alt="Teacher">
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        @if (!isPrimaryCenter())
        <div class="col-xl-4 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <div class="card-body">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h5><a href="{{ route('annual-project.show') }}">{{ __('annual_project') }}</a></h5>
                        </div>
                        <div class="db-icon">
                            <img src="{{ URL::asset('/assets/img/teacher.png') }}" alt="Teacher">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif
        @if (!Auth::user()->hasRole('Super Admin') && !Auth::user()->hasRole('Teacher') && !Auth::user()->staff->first())
        <div class="col-xl-4 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <div class="card-body">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h6>{{ __('Parents') }}</h6>
                            <h3>{{ $data['parents'] }}</h3>
                        </div>
                        <div class="db-icon">
                            <img src="{{ URL::asset('/assets/img/parents.png') }}" alt="Parents">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <!-- /Counter -->
    @if (!Auth::user()->hasRole('Super Admin') && !Auth::user()->hasRole('Teacher') && !Auth::user()->staff->first())
        <div class="row">
            {{-- <div class="col-md-12 col-lg-6">
                <div class="card card-chart">
                    <div class="card-body">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h5 class="card-title">Overview</h5>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="nav nav-tabs">
                                            <li class="nav-item">
                                                <a href="#exam_overview" data-bs-toggle="tab" aria-expanded="false"
                                                    class="nav-link active">
                                                    Exam
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#class_group" data-bs-toggle="tab" aria-expanded="true"
                                                    class="nav-link">
                                                    Class Group
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#attendance" data-bs-toggle="tab" aria-expanded="true"
                                                    class="nav-link">
                                                    Attendance
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    @php
                                        $srno = 1;
                                    @endphp
                                    <div class="tab-content">
                                        <div class="tab-pane show active" id="exam_overview">
                                            <div class="col-sm-12 col-md-6">
                                                {!! Form::select('overview_exam_id', $exams, null, [
                                                    'class' => 'form-control select',
                                                    'id' => 'overview_exam_id',
                                                ]) !!}
                                            </div>

                                            <div class="row" id="exam_overview_data">

                                            </div>
                                        </div>
                                        
                                        <div class="tab-pane" id="class_group">
                                            <div class="row">
                                                @foreach ($class_groups as $class_group)
                                                    <div class="col-sm-12 col-md-6">
                                                        <div class="card flex-fill bg-transparent-color p-2">
                                                            <div class="card-body">
                                                                <h5 class="card-title mb-2">{{ $class_group->name }}</h5>
                                                                @php
                                                                    $class_name = [];
                                                                    $total_attempt_students = 0;
                                                                    $total_pass_students = 0;
                                                                @endphp
                                                                @foreach ($class_group->classes as $class)
                                                                    @php
                                                                        $class_name[] = $class->name;
                                                                    @endphp
                                                                    @foreach ($class->class_section as $class_section)
                                                                        @foreach ($class_section->exam_statistics as $exam_statistics)
                                                                            @php
                                                                                $total_attempt_students += $exam_statistics->total_attempt_student;
                                                                                
                                                                                $total_pass_students += $exam_statistics->pass;
                                                                            @endphp
                                                                        @endforeach
                                                                    @endforeach
                                                                @endforeach
                                                                <p class="card-text">{{ implode(', ', $class_name) }}</p>
                                                                @if ($total_attempt_students)
                                                                    <div class="progress progress-lg">
                                                                        <div class="progress-bar bg-success"
                                                                            role="progressbar"
                                                                            style="width: {{ ($total_pass_students * 100) / $total_attempt_students }}%;animation: progressAnimation 2s"
                                                                            aria-valuenow="25" aria-valuemin="0"
                                                                            aria-valuemax="100">
                                                                            <span
                                                                                class="text-end m-2">{{ number_format(($total_pass_students * 100) / $total_attempt_students, 2) }}
                                                                                %</span>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="progress progress-lg">
                                                                        <div class="progress-bar bg-no-data-found"
                                                                            role="progressbar"
                                                                            style="width: 100%;animation: progressAnimation 2s"
                                                                            aria-valuenow="25" aria-valuemin="0"
                                                                            aria-valuemax="100">
                                                                            <span class="text-center text-black"> No exams
                                                                                found </span>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        
                                        <div class="tab-pane" id="attendance">
                                            <div class="row">
                                                <div class="col-sm-12 col-md-6">
                                                    {!! Form::select('overview_class_group_id', $class_groups_dropdown, null, [
                                                        'class' => 'form-control select',
                                                        'id' => 'overview_attendance_class_group_id',
                                                    ]) !!}
                                                </div>

                                                <div class="row" id="attendance_overview_data">


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="col-sm-12 col-md-6">
                <!-- Overview Chart -->
                <div class="card card-chart">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h5 class="card-title">{{ __('attendance') }}</h5>
                            </div>
                            <div class="col-6">
                                <ul class="chart-list-out">
                                    <li><span class="circle-blue"></span>{{ __('Girls') }}</li>
                                    <li><span class="circle-green"></span>{{ __('Boys') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="apexcharts-overview"></div>
                    </div>
                </div>
                <!-- /Overview Chart -->
            </div>

            {{-- Gender Ratio --}}
            <div class="col-sm-12 col-md-6">
                <!-- Student Chart -->
                <div class="card card-chart">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h5 class="card-title">{{ __('Number of Students') }}</h5>
                            </div>
                            <div class="col-6">
                                <ul class="chart-list-out">
                                    <li><span class="circle-blue"></span>{{ __('Girls') }}</li>
                                    <li><span class="circle-green"></span>{{ __('Boys') }}</li>
                                    <li class="star-menus">
                                        <a href="javascript:" data-bs-toggle="dropdown"><i
                                                class="fas fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu sub-menu">
                                            <a class="dropdown-item" href="javascript:"
                                                onclick="class_wise()">{{ __('Class Wise') }}</a>
                                            <a class="dropdown-item" href="javascript:"
                                                onclick="class_group_wise()">{{ __('Class Group Wise') }}</a>
                                            <a class="dropdown-item" href="javascript:"
                                                onclick="overall_percentage()">{{ __('Overall Percentage') }}</a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-none" id="overall_percentage">
                            <div id="donut-boys-girls"></div>
                        </div>
                        <div class="" id="class_wise">
                            <div id="bar-chart-boys-girls"></div>
                        </div>
                        <div class="d-none" id="class_group_wise">
                            <div class="col-sm-12 col-md-6">
                                {!! Form::select('class_group_id', $class_groups_dropdown, null, [
                                    'class' => 'form-control select',
                                    'id' => 'class_group_id_boys_girls',
                                ]) !!}
                            </div>
                            <div id="bar-chart-boys-girls-class-group"></div>
                        </div>
                    </div>
                </div>
                <!-- /Student Chart -->
            </div>
        </div>

        {{-- Star students & Exam report --}}
        <div class="row">
            <div class="col-xl-6">
                {{-- Fee payment information --}}
                <div class="col-sm-12 col-md-12">
                    <!-- Fees Chart -->
                    <div class="card card-chart">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h5 class="card-title">{{ __('fees') }}</h5>
                                </div>
                                <div class="col-6">
                                    <ul class="chart-list-out">
                                        <li><span class="circle-blue"></span>{{ __('paid') }}</li>
                                        <li><span class="circle-fail"></span>{{ __('owing') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="fee-status-overview"></div>
                        </div>
                    </div>
                    <!-- /Fees Chart -->
                </div>
                <!-- Star Students -->
                <div class="card flex-fill student-space comman-shadow">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="card-title">{{ __('Star Students') }}</h5>
                    </div>
                    <div class="row m-3 mt-md-0 mb-0 gy-3">
                        <div class="col-sm-6 local-forms">
                            <label for="">{{ __('class_section') }}</label>
                            <select name="filter_class_id" id="filter_class_id" class="form-control">
                                @foreach ($class_section as $class)
                                    <option value={{ $class->id }}>
                                        {{ $class->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-6 local-forms">
                            <label for="">{{ __('Gender') }}</label>
                            <select name="filter_gender_id" id="filter_gender_id" class="form-control">
                                <option value="">{{ __('select') }}</option>
                                <option value="male">
                                    {{ __('Male') }}
                                </option>
                                <option value="female">
                                    {{ __('Female') }}
                                </option>
                            </select>
                        </div>

                        <div class="col-sm-6 d-none local-forms">
                            <select name="filter_limit_id" id="filter_limit_id" class="form-control">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                            </select>
                        </div>

                        <div class="col-sm-6 local-forms">
                            <label for="">{{ __('Subject') }}</label>
                            <select name="filter_subject_id" id="filter_subject_id" class="form-control">
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-6 local-forms">
                            <label for="">{{ __('Sequence') }}</label>
                            <select name="filter_sequence_best_id" id="filter_sequence_best_id" class="form-control">
                                @foreach ($sequences as $seq)
                                    <option value={{ $seq->id }}>
                                        {{ $seq->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-body">

                        @php
                            $url = url('top-student-list');
                            $table_id = 'top_student_list';
                            $data_search = false;
                            $actionColumn = false;
                            $columns = [
                                trans('No') => ['data-field' => 'no'],
                                trans('name') => ['data-field' => 'name'],
//                                trans('class') => ['data-field' => 'class'],
                                trans('Marks') => ['data-field' => 'marks'],
//                                trans('percentage') => ['data-field' => 'percentage'],
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :data_search=$data_search :actionColumn=$actionColumn
                            :table_id=$table_id queryParams="topStudentListqueryParams"></x-bootstrap-table>

                    </div>
                </div>
                <!-- /Star Students -->

                {{-- EVENTS --}}
                <div class="col-sm-12 col-md-12">
                    {{-- <div class="col-md-12 col-lg-6"> --}}
                    <div class="card card-chart">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h5 class="card-title">{{ __('Events') }}</h5>
                                </div>
                                <div class="card-body">
                                    {{-- <div class="table-responsive table-height">
                                        <table
                                            class="table star-student table-hover table-center table-borderless table-striped">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('title') }}</th>
                                                    <th {{ __('description') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $srno = 1;
                                                @endphp
                                                @foreach ($events as $event)
                                                    <tr class="text-wrap">
                                                        <td>{{ $srno++ }}</td>
                                                        <td>{{ $event->name }}</td>
                                                        <td>
                                                            {{ $event->description }}
                                                            <div class="row mt-3">
                                                                <div class="col-sm-12 col-md-6">
                                                                    <i class="fa fa-calendar-days menu-icon mx-1"></i>
                                                                    {{ date($date_formate['date_formate'], strtotime($event->start_date)) }}
                                                                    -
                                                                    {{ date($date_formate['date_formate'], strtotime($event->end_date)) }}
                                                                </div>
                                                                <div class="col-sm-12 col-md-6">
                                                                    <i class="fa fa-map-pin menu-icon  mx-1"></i>
                                                                    {{ $event->location }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div> --}}
                                    @php
                                        $url = url('upcoming-events');
                                        $data_search = false;
                                        $actionColumn = false;
                                        $columns = [
                                            trans('No') => ['data-field' => 'no'],
                                            trans('name') => ['data-field' => 'name'],
                                            trans('date') => ['data-field' => 'date'],
                                            
                                        ];
                                    @endphp
                                    <x-bootstrap-table :url=$url :columns=$columns :data_search=$data_search
                                        :actionColumn=$actionColumn 
                                        queryParams="queryParams"></x-bootstrap-table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- </div> --}}
                </div>

                <div class="col-sm-12 col-md-12">
                    {{-- <div class="col-md-12 col-lg-6"> --}}
                    <div class="card card-chart">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h5 class="card-title">{{ __('holiday') }}</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                {{-- <div class="table-responsive table-height">
                                    <table
                                        class="table star-student table-hover table-center table-borderless table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('title') }}</th>
                                                <th {{ __('description') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $srno = 1;
                                            @endphp
                                            @foreach ($holidays as $holiday)
                                                <tr>
                                                    <td>{{ $srno++ }}</td>
                                                    <td>{{ date($date_formate['date_formate'], strtotime($holiday->date)) }}
                                                    </td>
                                                    <td class="text-wrap">{{ $holiday->title }}</td>
                                                    <td class="text-wrap">{{ $holiday->description }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div> --}}
                                @php
                                        $url = url('upcoming-holiday');
                                        $data_search = false;
                                        $actionColumn = false;
                                        $columns = [
                                            trans('No') => ['data-field' => 'no'],
                                            trans('name') => ['data-field' => 'name'],
                                            trans('date') => ['data-field' => 'date'],
                                            
                                        ];
                                    @endphp
                                    <x-bootstrap-table :url=$url :columns=$columns :data_search=$data_search
                                        :actionColumn=$actionColumn 
                                        queryParams="queryParams"></x-bootstrap-table>
                            </div>
                        </div>
                    </div>
                    {{-- </div> --}}
                </div>

            </div>

            <div class="col-md-12 col-lg-6">
                <!-- Exam -->
                <div class="card card-chart">
                    <div class="card-body">
                        <div class="card-header">
                            <div class="align-items-center">
                                <div class="col-6">
                                    <h5 class="card-title">{{ __('exam') }}</h5>
                                </div>
                                {{-- Upcoming Exams --}}
                                <div class="row" style="display: none;">
                                    <div class="col-sm-12 col-md-12">
                                        <h6 class="mt-3">{{ __('Upcoming Exams') }}</h6>
                                        <div class="table-responsive">

                                            @php
                                                $url = url('upcoming-exam');
                                                $data_search = false;
                                                $actionColumn = false;
                                                $columns = [
                                                    trans('No') => ['data-field' => 'no'],
                                                    trans('name') => ['data-field' => 'name', 'data-sortable' => false],
                                                    trans('class') => ['data-field' => 'class'],
                                                    trans('start_date') => ['data-field' => 'start_date'],
                                                    trans('end_date') => ['data-field' => 'end_date'],
                                                ];
                                            @endphp
                                            <x-bootstrap-table :url=$url :columns=$columns :data_search=$data_search
                                                :actionColumn=$actionColumn queryParams="queryParams"></x-bootstrap-table>
                                        </div>

                                    </div>
                                </div>
                                {{-- End Upcoming exams --}}
                                {{-- <hr> --}}
                                {{-- Unpublish exam result --}}
                                <div class="row" style="display: none;">
                                    <div class="col-sm-12 col-md-12">
                                        <h6 class="mt-3">{{ __('Unpublish Exams Result') }}</h6>
                                        <div class="table-responsive">
                                            @php
                                                $url = url('unpublish-exam-result');
                                                $data_search = false;
                                                $actionColumn = false;
                                                $columns = [
                                                    trans('No') => ['data-field' => 'no'],
                                                    trans('name') => ['data-field' => 'name', 'data-sortable' => false],
                                                    trans('class') => ['data-field' => 'class'],
                                                    trans('start_date') => ['data-field' => 'start_date'],
                                                    trans('end_date') => ['data-field' => 'end_date'],
                                                ];
                                            @endphp
                                            <x-bootstrap-table :url=$url :columns=$columns :data_search=$data_search
                                                :actionColumn=$actionColumn queryParams="queryParams"></x-bootstrap-table>
                                        </div>

                                    </div>
                                </div>
                                {{-- End Unpublish exam result --}}
                                {{-- <hr> --}}
                                {{-- Pending or in review exam marks --}}
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <h6 class="mt-3">{{ __('Pending exam marks') }}</h6>
                                        <div id="toolbar">
                                            <div class="row">
                                                <div class="col-sm-6 local-forms">
                                                    @if (!Auth::user()->teacher)
                                                        <label for="">{{ __('class_section') }}</label>
                                                        <select name="filter_class_section_id" id="filter_class_section_id" class="form-control">
                                                            <option value="">{{ __('select_class_section') }}</option>
                                                            @foreach ($class_section as $class)
                                                                <option value={{ $class->id }}>
                                                                    {{ $class->full_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </div>
                                                <div class="col-sm-6 local-forms">
                                                    @if (!Auth::user()->teacher)
                                                        <label for="">{{ __('Sequence') }}</label>
                                                        <select name="filter_sequence_id" id="filter_sequence_id" class="form-control">
                                                            <option value="">{{ __('select') }}</option>
                                                            @foreach ($sequences as $seq)
                                                                <option value={{ $seq->id }}>
                                                                    {{ $seq->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">

                                            @php
                                                $url = url('pendding-exam-result');
                                                $data_search = false;
                                                $actionColumn = false;
                                                $columns = [
                                                    // trans('No') => ['data-field' => 'no', 'visible'=>false],
                                                    trans('class') => ['data-field' => 'class', 'data-sortable' => true],
                                                    trans('subject') => ['data-field' => 'subject', 'data-sortable' => true],
                                                    trans('Sequence') => ['data-field' => 'sequence', 'data-sortable' => true],
                                                    trans('teacher') => ['data-field' => 'teacher', 'data-sortable' => true],
                                                    // trans('start_date') => ['data-field' => 'start_date'],
                                                    // trans('end_date') => ['data-field' => 'end_date'],
                                                ];
                                            @endphp
                                            <x-bootstrap-table :url=$url :columns=$columns :data_search=$data_search sortName="class" sortOrder="ASC"
                                                :actionColumn=$actionColumn queryParams="dashboardExamQueryParams" showPagination="false"></x-bootstrap-table>

                                        </div>

                                    </div>
                                </div>
                                {{-- End Pending exam marks --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Exam -->
            </div>

        </div>
        {{-- End star student & Exam report --}}

        {{-- Events & Holiday --}}
        <div class="row">
            {{-- Event --}}
            {{-- <div class="col-md-12 col-lg-6">
                <div class="card card-chart">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h5 class="card-title">{{ _('Events') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-height">
                                    <table
                                        class="table star-student table-hover table-center table-borderless table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('title') }}</th>
                                                <th {{ __('description') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $srno = 1;
                                            @endphp
                                            @foreach ($events as $event)
                                                <tr class="text-wrap">
                                                    <td>{{ $srno++ }}</td>
                                                    <td>{{ $event->name }}</td>
                                                    <td>
                                                        {{ $event->description }}
                                                        <div class="row mt-3">
                                                            <div class="col-sm-12 col-md-6">
                                                                <i class="fa fa-calendar-days menu-icon mx-1"></i>
                                                                {{ date($date_formate['date_formate'], strtotime($event->start_date)) }}
                                                                -
                                                                {{ date($date_formate['date_formate'], strtotime($event->end_date)) }}
                                                            </div>
                                                            <div class="col-sm-12 col-md-6">
                                                                <i class="fa fa-map-pin menu-icon  mx-1"></i>
                                                                {{ $event->location }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            {{-- End Event --}}

            {{-- Holiday --}}
            {{-- <div class="col-md-12 col-lg-6">
                <div class="card card-chart">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h5 class="card-title">{{ __('holiday') }}</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-height">
                                <table class="table star-student table-hover table-center table-borderless table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('title') }}</th>
                                            <th {{ __('description') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $srno = 1;
                                        @endphp
                                        @foreach ($holidays as $holiday)
                                            <tr>
                                                <td>{{ $srno++ }}</td>
                                                <td>{{ date($date_formate['date_formate'], strtotime($holiday->date)) }}
                                                </td>
                                                <td class="text-wrap">{{ $holiday->title }}</td>
                                                <td class="text-wrap">{{ $holiday->description }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
            {{-- End holiday --}}

        </div>
        {{-- End events & holiday --}}
    @endif

@endsection

@section('js')
    <script>
        window.onload = setTimeout(() => {
            $('#overview_exam_id').trigger('change');
            $('#top_students').trigger('change');
            $('#overview_attendance_class_group_id').trigger('change');
            $('#class_group_id_boys_girls').trigger('change');

            $("filter_class_id").trigger('change');

        }, 500);

        function dashboardExamQueryParams(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                class_id: $('#filter_class_section_id').val(),
                sequence_id: $('#filter_sequence_id').val(),
            };
        }

        var chart = c3.generate({
            bindto: '#donut-boys-girls', // id of chart wrapper
            data: {
                columns: [
                    // each columns data
                    ['data1', {{ $data['total_boys'] }}],
                    ['data2', {{ $data['total_girls'] }}],
                ],
                type: 'donut', // default type of chart
                colors: {
                    data1: '#3D5EE1',
                    data2: '#70C4CF',
                },
                names: {
                    // name of each serie
                    'data1': 'Boys : ',
                    'data2': 'Girls : ',
                }
            },
            axis: {},
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });

        window.onload = setTimeout(() => {
            var class_name = '';
            var male_students = '';
            var female_students = '';

            var attendance_class_name = '';
            var boys_data = '';
            var girls_data = '';
            $.ajax({
                type: "get",
                url: "{{ url('get-gender-data') }}",
                success: function(response) {
                    class_name = response.class_name;
                    male_students = response.male_student_count;
                    female_students = response.female_student_count;
                    bar_chart_boys_girls(class_name, male_students, female_students)


                    attendance_class_name = response.attendance_class;
                    boys_data = response.boys_attendance;
                    girls_data = response.girls_attendance;
                    apexcharts_overview(attendance_class_name, boys_data, girls_data)
                }
            });
            $.ajax({
                type: "get",
                url: "{{ url('fees/summary') }}",
                success: function(response) {
                    fee_status_overview(response.class_names, response.paid_fee, response.unpaid_fee)
                }
            });
        }, 500);

        function class_wise() {
            $('#class_wise').removeClass('d-none');
            $('#overall_percentage').addClass('d-none');
            $('#class_group_wise').addClass('d-none');
        }

        function class_group_wise() {
            $('#class_group_wise').removeClass('d-none');
            $('#overall_percentage').addClass('d-none');
            $('#class_wise').addClass('d-none');
        }

        function overall_percentage() {
            $('#overall_percentage').removeClass('d-none');
            $('#class_wise').addClass('d-none');
            $('#class_group_wise').addClass('d-none');
        }
        $('#top_students').change(function(e) {
            $('#top_student_list').bootstrapTable('refresh');
        });

        $('#filter_limit_id, #filter_subject_id, #filter_gender_id, #filter_class_id, #filter_sequence_best_id').change(function(e) {
            $('#top_student_list').bootstrapTable('refresh');
        });

        function fee_status_overview(fee_class_name, paid_data, unpaid_data) {
            var options = {
                chart: {
                    type: "bar",
                    height: 350,
                    width: '100%',
                    stacked: false,
                    toolbar: {
                        show: true
                    },
                },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    bar: {
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: [{
                    name: "PAID",
                    color: '#3D5EE1',
                    // data: [45, 60, 75, 51, 42, 42, 30]
                    data: paid_data
                }, {
                    name: "OWING",
                    color: '#FE606F',
                    // data: [24, 48, 56, 32, 34, 52, 25]
                    data: unpaid_data
                }],
                xaxis: {
                    // categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    categories: fee_class_name,

                }
            }
            var chart = new ApexCharts(
                document.querySelector("#fee-status-overview"),
                options
            );
            chart.render();
        }


        function topStudentListqueryParams(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                class_id: $('#filter_class_id').val(),
                sequence_id: $('#filter_sequence_best_id').val(),
                gender: $('#filter_gender_id').val(),
                subject_id: $('#filter_subject_id').val(),
            };
        }
    </script>
@endsection
