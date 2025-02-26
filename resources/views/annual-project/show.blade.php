@extends('layout.master')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">@lang('class_annual_project')</h3>
                        <div>
                            @if (auth()->user()->hasRole('Center'))
                                <x-ls::button triggers_modal="annual_project_type_modal" :label_html="__('add_annual_project_type')" />
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <x-ls::accordion>
                                    <x-ls::accordionitem :label="__('annual_project_types_list')">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>@lang('name')</th>
                                                    <th>@lang('actions')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($annualProjetcTypes as $annualProjectType)
                                                    <tr>
                                                        <td>{{ $annualProjectType->name }}</td>
                                                        <td>
                                                            @if (auth()->user()->hasRole('Center'))
                                                                <div class="btn-group">
                                                                    <x-ls::button triggers_modal="annual_project_type_modal"
                                                                        label_html="<i class='fa fa-edit'></i>"
                                                                        classes="btn btn-sm btn-primary edit-annual-project-type"
                                                                        :attributes="[
                                                                            'data-id' => $annualProjectType->id,
                                                                        ]" />
                                                                    <x-ls::button label_html="<i class='fa fa-trash'></i>"
                                                                        classes="btn btn-sm btn-danger delete-annual-project-type"
                                                                        :attributes="[
                                                                            'data-id' => $annualProjectType->id,
                                                                        ]" />
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </x-ls::accordionitem>
                                </x-ls::accordion>
                            </div>
                        </div>
                        <div class="row">
                            <x-ls::form formview="inline" :buttons="[]">
                                <div class="col-md-6">
                                    <x-ls::select-model name="class_id" id="class_id" :options="$classes->pluck('name', 'id')->prepend(__('select_class'), 0)" label="Class"
                                        classes="d-block" placeholder="Select Class" />
                                </div>
                                <div class="col-md-6">
                                    <x-ls::select-model name="class_subject_id" id="class_subject_id" :options="$class_subjects->prepend(__('select_subject'), 0)"
                                        label="ClassSubject" classes="d-block" placeholder="Select Subject" />
                                </div>
                            </x-ls::form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Loader -->
                        <div id="loader" style="display: none;" class="text-center my-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading data...</p>
                        </div>
                        <!-- Form and Table -->
                        <div id="form-content" style="display: none;">
                            <x-ls::form action="" id="class_annual_project_form">
                                <table class="table table-striped table-bordered" id="annual_project_table">
                                    <thead></thead>
                                    <tbody></tbody>
                                </table>
                            </x-ls::form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-ls::modal id="annual_project_type_modal" :title="__('add_annual_project_type')">
        <x-ls::form id="annual_project_type_form" :action="route('annual-project.store-type')">
            <x-ls::text name="name" :label="__('name')" :placeholder="__('name')" />
        </x-ls::form>
    </x-ls::modal>
@endsection

@section('style')
    <style>
        #loader {
            position: relative;
            min-height: 100px;
        }

        #loader .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        #loader p {
            margin-top: 10px;
            color: #666;
        }
    </style>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const showLoader = () => {
                $('#loader').show();
                $('#form-content').hide();
            };

            const hideLoader = () => {
                $('#loader').hide();
                $('#form-content').show();
            };

            $('#class_id').change(function() {
                showLoader();
                var class_id = $(this).val();

                // Reset and hide form content when changing class
                $('#form-content').hide();
                $('#annual_project_table thead, #annual_project_table tbody').empty();

                $.ajax({
                    url: route('annual-project.show'),
                    type: 'GET',
                    data: {
                        class_id: class_id
                    },
                    success: function(data) {
                        let options = `<option value="0" selected>Select Subject</option>`;
                        data.class_subjects.forEach(function(item) {
                            options +=
                                `<option value="${item.id}">${item.subject.name}</option>`;
                        });
                        $('#class_subject_id').html(options);
                    },
                    error: function(data) {
                        console.log(data);
                        toastr.error('Error loading data');
                    },
                    complete: function() {
                        hideLoader();
                    }
                });
            });

            $('#class_subject_id').on('change', function() {
                let class_subject_id = $('#class_subject_id').val();

                if (class_subject_id == '0') {
                    $('#form-content').hide();
                    return;
                }

                showLoader();
                $('#class_annual_project_form').attr('action', route('annual-project.store-class-project', {
                    classSubject: class_subject_id
                }));

                $.ajax({
                    url: '',
                    type: 'GET',
                    data: {
                        class_subject_id: class_subject_id
                    },
                    success: function(data) {
                        const annual_project_types = data.annual_project_types;
                        const sequences = data.sequences;
                        const project_data = data.project_data;

                        let thead = '<tr><th>Sequences</th>';
                        annual_project_types.forEach(function(type) {
                            thead += `<th>${type.name}</th>`;
                        });
                        thead += '</tr>';
                        $('#annual_project_table thead').html(thead);

                        let tbody = '';
                        project_data.forEach(function(row) {
                            tbody += `<tr><td>${row.sequence_name}</td>`;
                            annual_project_types.forEach(function(type) {
                                const value = row.types[type.id] || '';
                                tbody += `<td>
                                <input 
                                    name="total[${row.sequence_id}][${type.id}]" 
                                    type="number" 
                                    class="form-control form-control-sm" 
                                    value="${value}"
                                >
                            </td>`;
                            });
                            tbody += '</tr>';
                        });

                        $('#annual_project_table tbody').html(tbody);
                    },
                    error: function(data) {
                        console.log(data);
                        toastr.error('Error loading data');
                    },
                    complete: function() {
                        hideLoader();
                    }
                });
            });

            $('#annual_project_type_form').on('submit', function(e) {
                e.preventDefault();
                showLoader();
                $.ajax({
                    url: route('annual-project.store-type'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(data) {
                        toastr.success(data.message);
                        $('#annual_project_type_form').trigger('reset');
                        $('#annual_project_type_modal').modal('hide');
                    },
                    error: function(data) {
                        console.log(data);
                        toastr.error(data.message);
                    },
                    complete: function() {
                        hideLoader();
                    }
                });
            });

            $('#class_annual_project_form').on('submit', function(e) {
                e.preventDefault();
                showLoader();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(data) {
                        toastr.success(data.message);
                    },
                    error: function(data) {
                        console.log(data);
                        toastr.error(data.message);
                    },
                    complete: function() {
                        hideLoader();
                    }
                });
            });

            $('.edit-annual-project-type').on('click', function() {
                let annualProjectTypeId = $(this).data('id');
                $.ajax({
                    url: route('annual-project.edit-annual-project-type', {
                        annualProjectType: annualProjectTypeId
                    }),
                    type: 'GET',
                    data: {
                        annualProjectTypeId: annualProjectTypeId
                    },
                    success: function(data) {
                        $('#annual_project_type_form').attr('action', route(
                            'annual-project.store-class-project', {
                                classSubject: class_subject_id
                            }));
                        $('#annual_project_type_form').trigger('reset');
                        $('#annual_project_type_modal').modal('hide');
                    },
                    error: function(data) {
                        console.log(data);
                        toastr.error(data.message);
                    },
                    complete: function() {
                        hideLoader();
                    }
                });
            });

            $('.delete-annual-project-type').on('click', function() {
                let annualProjectTypeId = $(this).data('id');
                const $btn = $(this);
                console.log($(this).data('id'));
                Swal.fire({
                    title: "@lang('delete_title')",
                    text: "@lang('delete_warning')",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '@lang('Yes')',
                    cancelButtonText: '@lang('No')',
                }).then(result => {
                    if (result) {
                        $.ajax({
                            url: route('annual-project.destroy-type', {
                                annualProjectType: annualProjectTypeId
                            }),
                            type: 'DELETE',
                            data: {
                                annualProjectTypeId: annualProjectTypeId
                            },
                            success: function(data) {

                                if (data.unable) {
                                    Swal.fire({
                                        title: "@lang('delete_title')",
                                        text: data.message,
                                        icon: 'error',
                                        showCancelButton: true,
                                        confirmButtonColor: '#d33',
                                        cancelButtonColor: '#3085d6',
                                        confirmButtonText: '@lang('Force delete')',
                                        cancelButtonText: '@lang('Abort deletion')',
                                    }).then(res => {
                                        if (res) {
                                            $.ajax({
                                                url: route(
                                                    'annual-project.force-destroy-type', {
                                                        annualProjectType: annualProjectTypeId
                                                    }),
                                                method: 'DELETE',
                                                success: function(
                                                data) {
                                                    toastr.success(
                                                        '@lang('alert_delete_annual_project_type_success')'
                                                        );
                                                    $btn.closest('tr').fadeOut();
                                                    return;
                                                },
                                                error: function(error) {
                                                    toastr.error(
                                                        error
                                                        .message
                                                        )
                                                },
                                                complete: function() {}
                                            });
                                        }
                                    });
                                }

                                toastr.error(data.message);
                            },
                            error: function(data) {
                                console.log(data);
                                toastr.error(data.message);
                            },
                            complete: function() {
                                hideLoader();
                                $(this).next('.force-delete-annual-project-type')
                                    .removeClass('d-none');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
