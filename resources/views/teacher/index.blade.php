@extends('layout.master')

@section('title')
    {{ __('teacher') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage_teacher') }}
            </h3>
        </div>

        <div class="row">
            @if (Auth::user()->can('teacher-create'))
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <h4 class="card-title col-sm-12 col-md-6">{{ __('create_teacher') }}</h4>
                                <div class="col-sm-12 col-md-6 d-flex flex-row-reverse">
                                    {{-- <a href="{{ url('teacher/upload-bulk-data') }}" class="btn btn-theme">{{ __('upload_bulk_data') }}</a> --}}
                                    <button data-bs-toggle="modal" data-bs-target="#standard-modal" class="btn btn-theme">{{ __('upload_bulk_data') }}</button>
                                </div>
                            </div>

                            <form class="teacher-validate-form create-form pt-3" id="formdata" action="{{ url('teachers') }}" data-success-function="successFunction" enctype="multipart/form-data" method="POST" novalidate="novalidate">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-12 local-forms">
                                        <label>{{ __('mobile') }} </label>
                                        <select class="teacher-search w-100 form-control" id="mobile" name="mobile"></select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('full_name') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('first_name', null, [
                                            'id' => 'first_name',
                                            'required',
                                            'placeholder' => __('full_name'),
                                            'class' => 'form-control',
                                        ]) !!}

                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                        <br>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    {!! Form::radio('gender', 'male', true, ['id' => 'male']) !!}
                                                    {{ __('male') }}
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    {!! Form::radio('gender', 'female', false, ['id' => 'female']) !!}
                                                    {{ __('female') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('last_name', null, [
                                            'id' => 'last_name',
                                            'required',
                                            'placeholder' => __('last_name'),
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div> --}}
                                    {{-- </div>
                                    <div class="row"> --}}
                                    {{-- <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                        <br>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    {!! Form::radio('gender', 'male', false, ['id' => 'male']) !!}
                                                    {{ __('male') }}
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    {!! Form::radio('gender', 'female', false, ['id' => 'female']) !!}
                                                    {{ __('female') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('dob', null, [
                                            'id' => 'dob',
                                            'required',
                                            'placeholder' => __('dob'),
                                            'class' => 'dob-date form-control',
                                        ]) !!}
                                        <span class="input-group-addon input-group-append"></span>
                                    </div>
                                    {{-- </div>
                                    <div class="row"> --}}
                                    {{--                                    <div class="form-group col-sm-12 col-md-6 local-forms">--}}
                                    {{--                                        <label>{{ __('mobile') }} </label>--}}
                                    {{--                                        {!! Form::number('mobile', null, [--}}
                                    {{--                                            'id' => 'mobile',--}}
                                    {{--                                            'placeholder' => __('mobile'),--}}
                                    {{--                                            'class' => 'form-control remove-number-increment',--}}
                                    {{--                                            'min'=>1--}}
                                    {{--                                        ]) !!}--}}

                                    {{--                                    </div>--}}
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('email') }} </label>
                                        {!! Form::email('email', $email, [
                                            'id' => 'email',
                                            'placeholder' => __('Email'),
                                            'class' => 'form-control',
                                            'readonly'=>true
                                        ]) !!}
                                        <a href="#" class="generate-new-email">{{__("Generate")}}</a>
                                    </div>
                                    <div class="form-group col-12 col-sm-12 col-md-6">
                                        <label for="">{{__("Image")}} </label>
                                        {!! Form::file('image', ['class' => 'form-control teacher-image']) !!}
                                        <br>
                                        <div class="col-md-9 col-sm-6">
                                            <img src="#" class="img-thumbnail w-25" id="image-tag" alt="Teacher"/>
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-sm-12 col-md-6">
                                        <label for="">{{__("Qualification Certificate / Degree")}}</label>
                                        {!! Form::file('qualification_certificate', ['class' => 'form-control']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('qualification') }} </label>
                                        {!! Form::textarea('qualification', null, [
                                            'id' => 'qualification',
                                            'placeholder' => __('qualification'),
                                            'class' => 'form-control',
                                            'rows' => 3,
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('Salary') }} <span class="text-primary">({{__("optional")}})</span></label>
                                        {!! Form::number('salary', null, [
                                            'id' => 'salary',
                                            'placeholder' => __('Amount'),
                                            'class' => 'form-control',
                                            'min'=>1
                                        ]) !!}
                                        <span class="input-group-addon input-group-append"></span>
                                    </div>

                                    {{-- </div>
                                    <div class="row"> --}}



                                    {{-- </div>
                                    <div class="row"> --}}
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('current_address') }} </label>
                                        {!! Form::textarea('current_address', null, [
                                            'id' => 'current_address',
                                            'placeholder' => __('current_address'),
                                            'class' => 'form-control',
                                            'rows' => 3,
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('permanent_address') }}</label>
                                        {!! Form::textarea('permanent_address', null, [
                                            'id' => 'permanent_address',
                                            'placeholder' => __('permanent_address'),
                                            'class' => 'form-control',
                                            'rows' => 3,
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group">
                                    <span class="input-group-text">
                                        <input type="checkbox" class="grant-permission" name="grant_permission">
                                    </span>
                                        <input type="text" disabled value="{{ __('grant_permission_to_manage_students_parents') }}" class="form-control permission">
                                    </div>
                                </div>
                                <div class="form-group text-info" style="font-size: 0.8rem;margin-top: -0.3rem">{{ __('note_for_permission_of_student_manage') }}</div>
                                <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('list_teacher') }}</h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = url('teacher_list');
                                    $columns = [
                                        trans('no') => ['data-field' => 'no'],
                                        trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                        trans('user_id') => ['data-field' => 'user_id', 'data-visible' => false],
                                        trans('full_name') => ['data-field' => 'first_name'],
                                        // trans('last_name') => ['data-field' => 'last_name'],
                                        trans('gender') => ['data-field' => 'gender'],
                                        trans('email') => ['data-field' => 'email'],
                                        trans('mobile') => ['data-field' => 'mobile'],
                                        trans('dob') => ['data-field' => 'dob','data-visible'=>false],
                                        trans('image') => ['data-field' => 'image', 'data-formatter' => 'imageFormatter'],
                                        trans('Salary') => ['data-field' => 'salary'],
                                        trans('qualification') => ['data-field' => 'qualification'],
                                        trans('Qualification Certificate') => ['data-field' => 'qualification_certificate','data-formatter'=>'QualifactionDegreeFormatter'],
                                        trans('current_address') => ['data-field' => 'current_address'],
                                        trans('permanent_address') => ['data-field' => 'permanent_address','data-visible'=>false],
                                    ];
                                    $actionColumn = [
                                        'editButton' => ['url' => url('teachers')],
                                        'deleteButton' => ['url' => url('teachers')],
                                        'data-events' => 'teacherActionEvents',
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn></x-bootstrap-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Standard modal content -->
    <div id="standard-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="create-form-upload-bulk pt-3" id="formdata-upload-bulk"
                      action="{{ url('teacher/upload-bulk-data') }}" enctype="multipart/form-data" method="POST"
                      novalidate="novalidate">
                    <div class="modal-header">
                        <h4 class="modal-title" id="standard-modalLabel">{{ __('upload_bulk_data') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12">
                                <label for="">{{ __('file') }} <span class="text-danger">*</span></label>
                                {!! Form::file('file', ['required', 'class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="mt-4 row">
                            <hr>
                            <div class="form-group col-sm-12 col-md-12">
                                {{-- <a href="{{ url('storage/teacher_sample_file.xlsx') }}" download="">{{ __('Download Sample File') }}</a> --}}
                                <a href="{{ asset('assets/file/teacher_sample_file.xlsx') }}" download="">{{ __('Download Sample File') }}</a>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <span style="font-size: 14px"> <b>{{ __('Note') }} :- </b>{{ __('First download sample file and convert to csv file then upload it') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__("Close")}}</button>
                        <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('edit_teacher') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="update-teacher-validate-form editform" action="{{ url('teachers') }}" novalidate="novalidate" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="user_id">
                        <input type="hidden" name="id" id="id">
                        <div class="row form-group">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('full_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('first_name', null, [ 'required', 'placeholder' => __('full_name'), 'class' => 'form-control', 'id' => 'edit_first_name', ]) !!}
                            </div>
                            {{-- <div class="form-group col-sm-12 col-md-6">
                                <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('last_name', null, [ 'required', 'placeholder' => __('last_name'), 'class' => 'form-control', 'id' => 'edit_last_name', ]) !!}
                            </div> --}}
                        </div>
                        <div class="row form-group">
                            <div class="form-group col-sm-12 col-md-6">
                                <div class="d-inline-flex">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label>&nbsp;&nbsp;
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('gender', 'male', null, ['class' => 'form-check-input edit', 'id' => 'edit_gender']) !!}
                                            {{ __('male') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('gender', 'female', null, ['class' => 'form-check-input edit', 'id' => 'edit_gender']) !!}
                                            {{ __('female') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Salary') }} <span class="text-primary">({{__("optional")}})</span></label>
                                {!! Form::number('salary', null, [
                                    'id' => 'edit_salary',
                                    'placeholder' => __('Amount'),
                                    'class' => 'form-control',
                                    'min'=>1
                                ]) !!}
                                <span class="input-group-addon input-group-append"></span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="form-group col-sm-12 col-md-6">
                                <label>{{ __('mobile') }} </label>
                                {!! Form::number('mobile', null, ['placeholder' => __('mobile'), 'class' => 'form-control remove-number-increment', 'id' => 'edit_mobile','min'=>1 ]) !!}
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 row">
                                <div class="form-group files col-md-12 col-sm-12">
                                    <label>{{ __('image') }} </label>
                                    <input name="image" id="edit_image" class="teacher-image form-control" type="file">
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="form-group col-sm-12 col-md-4">
                                <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                {!! Form::text('dob', null, [
                                    'required',
                                    'placeholder' => __('dob'),
                                    'class' => 'dob-date form-control',
                                    'id' => 'edit_dob',
                                ]) !!}
                                <span class="input-group-addon input-group-append"></span>
                            </div>
                            <div class="form-group col-sm-12 col-md-4">
                                <label>{{ __('qualification') }} </label>
                                {!! Form::textarea('qualification', null, [ 'placeholder' => __('qualification'), 'class' => 'form-control', 'rows' => 3, 'id' => 'edit_qualification', ]) !!}
                            </div>
                            <div class="form-group col-12 col-sm-12 col-md-4">
                                <label for="">{{__("Qualification Degree / Certificate")}}</label>
                                {!! Form::file('qualification_certificate', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="form-group col-sm-12 col-md-6">
                                <label>{{ __('current_address') }} </label>
                                {!! Form::textarea('current_address', null, [  'placeholder' => __('current_address'), 'class' => 'form-control', 'rows' => 3, 'id' => 'edit_current_address', ]) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6">
                                <label>{{ __('permanent_address') }}</label>
                                {!! Form::textarea('permanent_address', null, ['placeholder' => __('permanent_address'), 'class' => 'form-control', 'rows' => 3, 'id' => 'edit_permanent_address', ]) !!}
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group">
                            <span class="input-group-text">
                                <input type="checkbox" name="grant_permission" class="edit_permission_chk" id="manage_student_parent">
                            </span>
                                <input type="text" disabled value="{{ __('grant_permission_to_manage_students_parents') }}" class="form-control permission">
                            </div>
                        </div>
                        <div class="form-group text-info" style="font-size: 0.8rem;margin-top: -0.3rem">
                            {{ __('note_for_permission_of_student_manage') }}</div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function successFunction(response) {
            if (!response.error) {
                $('#email').val('').trigger('change');
            }
        }

        $(document).on('change', '.grant-permission', function () {
                if (this.checked) {
                    Swal.fire({
                        title: lang_delete_title,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: lang_yes_check
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).prop("checked", true);
                        } else {
                            $(this).prop("checked", false);
                        }
                    })
                }
            });
    </script>
@endsection
