@extends('layout.master')

@section('title')
    {{ __('parents') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('parents') }}
            </h3>
        </div>

        <div class="row">
            {{-- <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create').' '.__('parents') }}
                        </h4>
                        <form class="createform pt-3" id="formdata" enctype="multipart/form-data" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('first_name', null, ['required', 'placeholder' => __('first_name'), 'class' => 'form-control']) !!}

                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('last_name', null, ['required', 'placeholder' => __('last_name'), 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-3">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label><br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('gender', 'male', ['class' => 'form-check-input']) !!}
                                                {{ __('male') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('gender', 'female', ['class' => 'form-check-input']) !!}
                                                {{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('dob', null, ['required','readonly','placeholder' => __('dob'), 'class' => 'datetimepicker form-control']) !!}
                                    <span class="input-group-addon input-group-append">
                                    </span>
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('email') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('email', null, ['required', 'placeholder' => __('email'), 'class' => 'form-control']) !!}
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('mobile') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('mobile', null, ['required', 'placeholder' => __('mobile'), 'class' => 'form-control']) !!}

                                </div>
                                <div class="form-group col-sm-12 col-md-6">

                                    <label>{{ __('image') }}</label>
                                    <input type="file" name="image" class="file-upload-default"/>
                                    <div class="input-group col-xs-12">
                                        <input type="text" class="form-control file-upload-info" disabled="" placeholder="{{ __('image') }}"/>
                                        <span class="input-group-append">
                                            <button class="file-upload-browse btn btn-primary" type="button">{{__('upload')}}</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('current_address') }}</label>
                                    {!! Form::textarea('current_address', null, ['placeholder' => __('current_address'), 'class' => 'form-control','rows'=>3]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('permanent_address') }}</label>
                                    {!! Form::textarea('permanent_address', null, ['placeholder' => __('permanent_address'), 'class' => 'form-control','rows'=>3]) !!}
                                </div>
                            </div>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div> --}}
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('parents') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = url('parents_list');
                                    $columns = [
                                        trans('no') => ['data-field' => 'no'],
                                        trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                        trans('name') => ['data-field' => 'name', 'data-sortable' => true, 'data-visible' => false],
                                        trans('user_id') => ['data-field' => 'user_id'],
                                        trans('full_name') => ['data-field' => 'first_name', 'data-sortable' => true],
                                        // trans('last_name') => ['data-field' => 'last_name', 'data-sortable' => true],
                                        trans('gender') => ['data-field' => 'gender', 'data-sortable' => true],
                                        trans('email') => ['data-field' => 'email', 'data-sortable' => true],
                                        trans('dob') => ['data-field' => 'dob', 'data-sortable' => true],
                                        trans('image') => ['data-field' => 'image', 'data-formatter' => 'imageFormatter'],
                                        trans('created_at') => ['data-field' => 'created_at', 'data-visible' => false],
                                        trans('updated_at') => ['data-field' => 'updated_at', 'data-visible' => false],
                                    ];
                                    
                                    $actionColumn = [
                                        'editButton' => ['url' => url('parents')],
                                        'deleteButton' => false,
                                        'data-events' => 'parentEvents',
                                    ];

                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn>
                                </x-bootstrap-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('edit') . ' ' . __('parents') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="edit-form" class="edit-parent-form" action="{{ url('parents') }}" novalidate="novalidate"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="row form-group">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('first_name', null, [
                                    'required',
                                    'placeholder' => __('first_name'),
                                    'class' => 'form-control',
                                    'id' => 'first_name',
                                ]) !!}

                            </div>
                            {{-- <div class="form-group col-sm-12 col-md-6">
                                <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('last_name', null, [
                                    'required',
                                    'placeholder' => __('last_name'),
                                    'class' => 'form-control',
                                    'id' => 'last_name',
                                ]) !!}
                            </div> --}}
                        </div>
                        <div class="row form-group">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('gender', 'Male', null, ['class' => 'form-check-input edit', 'id' => 'gender']) !!}
                                            {{ __('male') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('gender', 'Female', null, ['class' => 'form-check-input edit', 'id' => 'gender']) !!}
                                            {{ __('female') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 col-md-6">
                                <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                {!! Form::text('dob', null, [
                                    'required',
                                    'placeholder' => __('dob'),
                                    'class' => 'datetimepicker form-control',
                                    'id' => 'dob',
                                ]) !!}
                                <span class="input-group-addon input-group-append">
                                </span>
                            </div>
                            <div class="form-group col-sm-12 col-md-6">
                                <label>{{ __('email') }} <span class="text-danger">*</span></label>
                                {!! Form::text('email', null, [
                                    'required',
                                    'placeholder' => __('email'),
                                    'class' => 'form-control',
                                    'id' => 'email',
                                ]) !!}
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="form-group col-sm-12 col-md-6">
                                <label>{{ __('mobile') }} <span class="text-danger">*</span></label>
                                {!! Form::text('mobile', null, [
                                    'required',
                                    'placeholder' => __('mobile'),
                                    'class' => 'form-control',
                                    'id' => 'mobile',
                                ]) !!}

                            </div>
                            <div class="form-group col-sm-12 col-md-6">
                                <label>{{ __('occupation') }} <span class="text-danger">*</span></label>
                                {!! Form::text('occupation', null, [
                                    'required',
                                    'placeholder' => __('occupation'),
                                    'class' => 'form-control',
                                    'id' => 'occupation',
                                ]) !!}

                            </div>
                            <div class="form-group col-sm-12 col-md-6">
                                <label>{{ __('image') }}</label>
                                <br>
                                <input type="file" name="image" class="form-control"/>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="form-group col-sm-12 col-md-6" id="current_address_div">
                                <label>{{ __('current_address') }}</label>
                                {!! Form::textarea('current_address', null, [
                                    'placeholder' => __('current_address'),
                                    'class' => 'form-control',
                                    'rows' => 3,
                                    'id' => 'current_address',
                                ]) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6" id="permanent_address_div">
                                <label>{{ __('permanent_address') }}</label>
                                {!! Form::textarea('permanent_address', null, [
                                    'placeholder' => __('permanent_address'),
                                    'class' => 'form-control',
                                    'rows' => 3,
                                    'id' => 'permanent_address',
                                ]) !!}
                            </div>
                        </div>
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
