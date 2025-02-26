@extends('layout.master')

@section('title')
    {{ __('Student Progress Report') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Student Progress Report') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ $student->user->full_name }}</h4>
                        <p>Class: {{ $student->class_section->class->name }} {{ $student->class_section->section->name }}</p>
                        <p>Admission No: {{ $student->admission_no }}</p>

                        <h5>Exam Results</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Exam</th>
                                    <th>Total Marks</th>
                                    <th>Obtained Marks</th>
                                    <th>Percentage</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($examResults as $result)
                                    <tr>
                                        <td>{{ $result->exam->name }}</td>
                                        <td>{{ $result->total_marks }}</td>
                                        <td>{{ $result->obtained_marks }}</td>
                                        <td>{{ $result->percentage }}%</td>
                                        <td>{{ $result->grade }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <h5>Attendance</h5>
                        <p>Total Present: {{ $attendance->where('type', 1)->count() }}</p>
                        <p>Total Absent: {{ $attendance->where('type', 0)->count() }}</p>
                        <p>Attendance Percentage: {{ ($attendance->where('type', 1)->count() / $attendance->count()) * 100 }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
