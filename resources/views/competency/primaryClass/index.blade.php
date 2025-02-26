@extends('layout.master')
@section('title')
    {{ __('class') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('class') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('class') }}
                        </h4>
                        <form class="pt-3 class-create-form" id="create-form" action="{{ route('primary-class.store') }}" method="POST" novalidate="novalidate">
                            <div class="row">
                                <div class="form-group">
                                    <label>{{ __('Sector') }} <span class="text-danger">*</span></label>
                                    <div class="col-12 d-flex row">
                                        @foreach ($mediums as $medium)
                                            <div class="form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" name="medium_id" id="medium_{{ $medium->id }}" value="{{ $medium->id }}">
                                                    {{ $medium->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group">
                                    {{ html()->label(__('primary_level')) }}
                                    {{ html()->select('primary_level_id', $primaryLevels)->class('form-control') }}
                                </div>
                                <div class="form-group">
                                    {{ html()->label(__('teacher')) }}
                                    {{ html()->select('teacher_id', $teachers)->class('form-control') }}
                                </div>
                                <div class="form-group local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" type="text" placeholder="{{ __('name') }}" class="form-control"/>
                                </div>
                            </div>
                            <input class="btn btn-primary" id="create-btn" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('class') }}
                        </h4>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>@lang('name')</th>
                                <th>@lang('level')</th>
                                <th>@lang('teacher_name')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($primaryClasses as $class)
                                <tr>
                                    <td>{{ $class->name }}</td>
                                    <td>{{ $class->primaryLevel->name }}</td>
                                    <td>{{ $class->teacher->user->full_name }}</td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="customModal" role="dialog" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteStudentModalLabel">{{ trans('confirm_student_deletion') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3">{{ __("action_cannot_be_undone") }}</p>
                            <div class="mb-3">
                                <label for="confirmStudentName" class="form-label">Student Name: <span id="selected_student"></span></label>
                                <input type="text" class="form-control" id="confirmStudentName" placeholder="{{ __("copy_name") }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="deleteStudentBtn" disabled>Delete Student</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('edit') . ' ' . __('class') }}</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 class-edit-form" id="edit-form" action="{{ url('class') }}" novalidate="novalidate">
                            <div class="modal-body">
                                <input type="hidden" name="edit_id" id="edit_id" value=""/>
                                <div class="form-group">
                                    <label>{{ __('Sector') }} <span class="text-danger">*</span></label>
                                    <div class="ml-1">
                                        @foreach ($mediums as $medium)
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input edit" name="medium_id"
                                                           id="edit_medium_{{ $medium->id }}"
                                                           value="{{ $medium->id }}"> {{ $medium->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" id="edit_name" type="text" placeholder="{{ __('name') }}" class="form-control"/>
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

@section('js')
    <script type="text/javascript">

        $(document).ready(function () {

            $('.rename-class-section').on('click', function () {
                console.log('button clicked');
            })
        });

    </script>
@endsection