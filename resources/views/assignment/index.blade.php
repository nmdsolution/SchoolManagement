@extends('layout.master')

@section('title')
    {{ __('manage') . ' ' . __('assignment') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('assignment') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('assignment') }}
                        </h4>
                        <form class="pt-3 add-assignment-form" id="create-form" action="{{ route('assignment.store') }}" method="POST" novalidate="novalidate">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-3 local-forms">
                                    <label>{{ __('class_section') }}
                                        <span class="text-danger">*</span></label>
                                    <select name="class_section_id" id="class_section_id" class="class_section_id form-control">
                                        <option value="">--{{ __('select_class_section') }}--</option>
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}" data-class="{{ $section->class->id }}">
                                                {{ $section->full_name  }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-sm-12 col-md-3 local-forms">
                                    <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                    <select name="subject_id" id="subject_id" class="subject_id form-control">

                                    </select>
                                </div>

                                <div class="form-group col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('assignment_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" placeholder="{{ __('assignment_name') }}" class="form-control"/>
                                </div>

                                <div class="form-group  col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('assignment_instructions') }}</label>
                                    <textarea id="instructions" name="instructions" placeholder="{{ __('assignment_instructions') }}" class="form-control"></textarea>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms local-forms-files">
                                    <label>{{ __('Reference Material') }} </label>
                                    <input type="file" name="file[]" class="form-control" multiple/>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('last_submission_date') }} <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="last-submission-date" name="due_date" placeholder="{{ __('last_submission_date') }}" class='form-control'>
                                    <span class="input-group-addon input-group-append">
                                    </span>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('points') }}</label>
                                    <input type="number" id="points" name="points" placeholder="{{ __('points') }}" class="form-control" min="1"/>
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="resubmission" id="resubmission_allowed" value="">{{ __('resubmission_allowed') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms" id="extra_days_for_resubmission_div" style="display: none;">
                                    <label>{{ __('extra_days_for_resubmission') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="text" id="extra_days_for_resubmission" name="extra_days_for_resubmission" placeholder="{{ __('extra_days_for_resubmission') }}" class="form-control"/>
                                </div>
                            </div>
                            <input class="btn btn-primary" id="create-btn" type="submit" value={{ __('submit') }} />
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('assignment') }}
                        </h4>

                        <div id="toolbar">
                            <div class="row">

                                <div class="col-sm-12 col-md-3 local-forms">
                                    <label for="">{{ __('class_section') }}</label>
                                    <select name="filter_class_section_id" id="filter_class_section_id" class="form-control">
                                        <option value="">{{ __('all') }}</option>
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}" data-class="{{ $section->class->id }}">
                                                {{ $section->full_name  }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-3 local-forms">
                                    <label for="">{{ __('subject') }}</label>
                                    <select name="filter_subject_id" id="filter_subject_id" class="form-control">
                                        <option value="">{{ __('all') }}</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>

                        @php
                            $url = route('assignment.show', 1);
                            $columns = [
                                trans('no') => ['data-field' => 'no'],
                                trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                trans('name') => ['data-field' => 'name', 'data-sortable' => true],
                                trans('title') => ['data-field' => 'title', 'data-sortable' => true],
                                trans('instructions') => ['data-field' => 'instructions'],
                                trans('file') => ['data-field' => 'file','data-formatter' => 'fileFormatter'],
                                trans('class_section') => ['data-field' => 'class_section_name'],
                                trans('subject') => ['data-field' => 'subject_name'],
                                trans('due_date') => ['data-field' => 'due_date'],
                                trans('points') => ['data-field' => 'points'],
                                trans('resubmission') => ['data-field' => 'resubmission'],
                                trans('extra_days_for_resubmission') => ['data-field' => 'extra_days_for_resubmission'],
                                trans('session_year_id') => ['data-visible' => false, 'data-field' => 'session_year_id'],
                            ];
                            if (Auth::user()->can('assignment-edit') || Auth::user()->can('assignment-delete')) {
                                $actionColumn = [
                                    'editButton' => ['url' => url('assignment')],
                                    'deleteButton' => ['url' => url('assignment')],
                                    'data-events' => 'assignmentEvents',
                                ];
                            }
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="CreateAssignmentSubmissionQueryParams"></x-bootstrap-table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{ __('edit') . ' ' . __('class') . ' ' . __('subject') }}
                            </h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 edit-assignment-form" id="edit-form" action="{{ url('assignment') }}" novalidate="novalidate">
                            <input type="hidden" name="edit_id" id="edit_id" value=""/>
                            <div class="modal-body">
                                <div class="row">

                                    <div class="form-group col-sm-12 col-md-3 local-forms">
                                        <label>{{ __('class') . ' ' . __('section') }} <span class="text-danger">*</span></label>
                                        <select name="class_section_id" id="edit_class_section_id" class="class_section_id form-control">
                                            <option value="">--{{ __('select_class_section') }}--</option>
                                            @foreach ($class_section as $section)
                                                <option value="{{ $section->id }}" data-class="{{ $section->class->id }}">
                                                    {{ $section->full_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3 local-forms">
                                        <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                        <select name="subject_id" id="edit_subject_id" class="subject_id form-control">
                                            <option value="">--{{ __('select_subject') }}--</option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12 local-forms">
                                        <label>{{ __('assignment_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_name" name="name" placeholder="{{ __('assignment_name') }}" class="form-control"/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12 local-forms">
                                        <label>{{ __('assignment_instructions') }}</label>
                                        <textarea id="edit_instructions" name="instructions" placeholder="{{ __('assignment_instructions') }}" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('old_files') }} </label>
                                        <div id="old_files"></div>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4 local-forms">
                                        <label>{{ __('upload_new_files') }} </label>
                                        <input type="file" name="file[]" id="new-files" class="form-control" multiple/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4 local-forms">
                                        <label>{{ __('last_submission_date') }} <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="due_date" id="edit_due_date" placeholder="{{ __('last_submission_date') }}" class='form-control'>
                                        <span class="input-group-addon input-group-append"></span>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4 local-forms">
                                        <label>{{ __('points') }}</label>
                                        <input type="number" id="edit_points" name="points" placeholder="{{ __('points') }}" class="form-control" min="1"/>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" name="resubmission" id="edit_resubmission_allowed" value="1">{{ __('resubmission_allowed') }}
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-4 local-forms" id="edit_extra_days_for_resubmission_div" style="display: none;">
                                        <label>{{ __('extra_days_for_resubmission') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_extra_days_for_resubmission" name="extra_days_for_resubmission" placeholder="{{ __('extra_days_for_resubmission') }}" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                                <input class="btn btn-primary" type="submit" value={{ __('edit') }} />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let today = new Date().toISOString().slice(0, 16);
        $("#last-submission-date").attr('min', today)
        window.onload = $('#center_id').trigger('change');
        window.onload = $('#filter_center_id').trigger('change');
        window.onload = setTimeout(() => {
            $('#class_section_id').trigger('change');
        }, 500);
    </script>
@endsection
