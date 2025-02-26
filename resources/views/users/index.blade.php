@extends('layout.master')

@section('title')
    {{ __('User') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage User') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Create User') }}
                        </h4>
                        <form class="pt-3 user-create-form" id="create-form" method="POST" action="{{ route('users.store') }}"
                            novalidate="novalidate" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="edit_id" id="edit_id" value="" />

                                {{-- <div class="form-group col-sm-12 col-md-12 search-user">
                                    <label>{{ __('mobile') }} <span class="text-danger">*</span></label>
                                    <select required class="user-search w-100" id="user_mobile" name="mobile">
                                    </select>
                                </div> --}}

                                <div class="form-group col-sm-12 col-md-4 col-md- local-forms">
                                    <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('first_name', null, [
                                        'required',
                                        'class' => 'form-control',
                                        'placeholder' => __('first_name'),
                                        'id' => 'first_name',
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('last_name', null, [
                                        'required',
                                        'class' => 'form-control',
                                        'placeholder' => __('last_name'),
                                        'id' => 'last_name',
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('email') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('email', null, [
                                        'required',
                                        'class' => 'form-control',
                                        'placeholder' => __('email'),
                                        'id' => 'email',
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    <div class="mobile-div">
                                        <label for="">{{ __('mobile') }} <span class="text-danger">*</span></label>
                                        {!! Form::number('mobile', null, ['required','class' => 'form-control', 'id' => 'mobile']) !!}
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-3">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" required class="form-check-input edit" name="gender"
                                                    id="male" value="male">
                                                {{ __('male') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" required class="form-check-input edit" name="gender"
                                                    id="female" value="female">
                                                {{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('image') }} </label>

                                    {!! Form::file('image', ['class' => 'form-control', 'id' => 'image', 'placeholder' => __('image')]) !!}
                                    <div class="col-md-9 col-sm-6 image-div d-none">
                                        <img src="#" class="img-thumbnail w-25" id="user-image" alt="User" />
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label for="">{{ __('current_address') }} <span
                                            class="text-danger">*</span></label>
                                    {!! Form::textarea('current_address', null, [
                                        'required',
                                        'class' => 'form-control',
                                        'placeholder' => __('current_address'),
                                        'id' => 'current_address',
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label for="">{{ __('permanent_address') }} <span
                                            class="text-danger">*</span></label>
                                    {!! Form::textarea('permanent_address', null, [
                                        'required',
                                        'class' => 'form-control',
                                        'placeholder' => __('permanent_address'),
                                        'id' => 'permanent_address',
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label for="">{{ __('dob') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('dob', null, [
                                        'required',
                                        'class' => 'form-control datetimepicker',
                                        'placeholder' => __('dob'),
                                        'id' => 'dob',
                                    ]) !!}
                                </div>

                                {{-- <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label for="">{{ __('password') }} <span class="text-danger">*</span></label>
                                    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => __('password')]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label for="">{{ __('confirm_password') }} <span
                                            class="text-danger">*</span></label>
                                    {!! Form::password('con_password', [
                                        'class' => 'form-control',
                                        'placeholder' => __('confirm_password'),
                                    ]) !!}
                                </div> --}}

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label for="">{{ __('role') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('role_id[]', $roles, null, [
                                        'required',
                                        'multiple',
                                        'class' => 'form-control js-example-basic-single select2-hidden-accessible',
                                        'tabindex' => '-1',
                                        'id' => 'roles',
                                        'style' => 'width:100%',
                                    ]) !!}
                                </div>
                            </div>
                            <input class="btn btn-secondary" id="reset-button" type="reset" value={{ __('Reset') }}>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('List User') }}
                        </h4>

                        @php
                            $url = url('user_list');
                            $columns = [
                                trans('no') => ['data-field' => 'no'],
                                trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                trans('name') => ['data-field' => 'name'],
                                trans('email') => ['data-field' => 'email'],
                                trans('mobile') => ['data-field' => 'mobile'],
                                trans('gender') => ['data-field' => 'gender'],
                                trans('current_address') => ['data-field' => 'current_address'],
                                trans('role') => ['data-field' => 'role'],
                                trans('image') => ['data-field' => 'image', 'data-formatter' => 'imageFormatter'],
                            ];
                            $actionColumn = [
                                'editButton' => ['url' => url('users')],
                                'deleteButton' => ['url' => url('users')],
                                'data-events' => 'userEvents',
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn
                            queryParams="UserQueryParams"></x-bootstrap-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection


@section('js')
    <script>
        
    </script>
@endsection
