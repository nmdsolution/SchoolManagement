@extends('layout.master')

@section('title')
    {{ __('Report Card List') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header" style="display: flex;justify-content: space-between;">
                        <h4 class="card-title">
                            {{ __('Report Card List') }}
                        </h4>
                        {{-- <button id="generate-all-reports" class="btn btn-success">
                            {{ __('Generate All Reports') }}
                        </button> --}}
                        <x-ls::button triggers_modal="observation-modal" :label="__('Create Report Observation')" />
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <x-ls::select-model :options="$class_sections->prepend(__('Select Class'), null)" :label="false" id="class_section_id"
                                    name="class_section_id" class="select2" />
                            </div>
                            <div class="col-md-6">
                                <x-ls::select-model :options="$terms->prepend(__('Select Term'), null)" :label="false" id="term_id" name="term_id"
                                    class="select2" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table id="report-card-list" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Student Name') }}</th>
                                    <th>{{ __('Options') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-ls::modal id="observation-modal">
        <x-ls::form :action="route('competency-observation.store')" method="POST">
            @csrf
            <x-ls::select :options="$students->pluck('user.full_name', 'id')" :label="false" id="student_id" name="student_id" class="select2" />
            <x-ls::select-model :options="$terms->prepend(__('Select Term'), null)" :label="false" id="exam_term_id" name="exam_term_id"
                class="select2" />
            <x-ls::textarea name="observation" label="Observation" />
            <x-ls::file name="teacher_signature" label="Teacher Signature" />
            <x-ls::file name="director_signature" label="Director Signature" />
            <x-ls::file name="parent_signature" label="Parent Signature" />
        </x-ls::form>
    </x-ls::modal>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#class_section_id, #term_id').on('change', function() {
                const class_section_id = $('#class_section_id').val();
                const term_id = $('#term_id').val();

                if (!class_section_id || !term_id) {
                    return;
                }

                loadReportCardList(class_section_id, term_id);
            });

            function loadReportCardList(class_section_id, term_id) {
                $('#report-card-list tbody').html('');
                $.ajax({
                    url: "{{ route('competency-report.report-card-list-data') }}",
                    type: 'POST',
                    data: {
                        class_section_id: class_section_id,
                        term_id: term_id
                    },
                    success: function(response) {
                        $('#report-card-list tbody').html('');
                        response.students.forEach(function(student) {
                            $('#report-card-list tbody').append(`<tr>
                                <td>${student.full_name}</td>
                                <td>
                                    <input type="checkbox" class="term-checkbox" data-student-id="${student.id}">
                                </td>
                                <td class="d-flex gap-2">
                                   <form action="${route('competency-report.generate-report', {student: student.id})}" method="POST" class="download-report-form">
                                        @csrf
                                        <input type="hidden" name="class_section_id" value="${class_section_id}">
                                        <input type="hidden" name="term_id" value="${term_id}">
                                        <input type="hidden" name="download" value="1">
                                        <button type="submit" class="btn btn-sm btn-icon btn-primary">
                                            <i class="fa fa-download"></i>
                                        </button>
                                   </form>
                                   <form action="${route('competency-report.generate-report', {student: student.id})}" method="POST" class="view-report-form">
                                        @csrf
                                        <input type="hidden" name="class_section_id" value="${class_section_id}">
                                        <input type="hidden" name="term_id" value="${term_id}">
                                        <button type="submit" class="btn btn-sm btn-icon btn-primary">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                   </form>
                                </td>
                            </tr>`);
                        });

                        if (response.students.length === 0) {
                            $('#report-card-list tbody').append(`<tr>
                                <td colspan="2" class="text-center">{{ __('No students found') }}</td>
                            </tr>`);
                        }
                    },
                    error: function() {
                        toastr.error("{{ __('alert_error_loading_students') }}");
                    }
                });
            }

            // Gestion du bouton pour générer les bulletins de tous les élèves
            $('#generate-all-reports').on('click', function() {
                const class_section_id = $('#class_section_id').val();
                const term_id = $('#term_id').val();

                if (!class_section_id || !term_id) {
                    toastr.error("{{ __('Please select a class and term.') }}");
                    return;
                }

                $.ajax({
                    url: "{{ route('competency-report.bulk-download') }}",
                    type: 'POST',
                    data: {
                        class_section_id: class_section_id,
                        term_id: term_id
                    },
                    success: function(response) {
                        if (!response.error) {
                            window.location.href = response.download_url; // Rediriger vers l'URL de téléchargement
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error("{{ __('Error generating reports') }}");
                    }
                });
            });
        });
    </script>
@endsection
