@extends('layout.master')

@section('title')
    {{ __('Centers') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage_centers') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create_center') }}
                        </h4>
                        <form class="center-validate-form create-form pt-3" id="formdata" data-success-function="successFunction" action="{{ route('centers.store') }}" enctype="multipart/form-data" method="POST" novalidate="novalidate">
                            @csrf

                            <div class="separator mb-5"><span class="h5">{{ __('center_details') }}</span></div>
                            <div class="row">
                                <div class="form-group col">
                                    {{ html()->label(__('center_type')) }}
                                    {{ html()->select('type', [
                                        'primary' => __('center_type_secondary'),
                                        'secondary' => __('center_type_primary'),
                                    ])->class('form-control')  }}
                                    <span class="help-text text-danger">{{ __('alerts.center_type_cannot_change') }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Support Email') }} <span class="text-danger">*</span><span title="This Email is a Support Email. And it will not be used to login the Center" class="fa fa-info-circle"></label>
                                    {!! Form::text('email', null, ['required', 'placeholder' => __('email'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Contact') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('contact', null, ['required', 'placeholder' => __('contact'), 'class' => 'remove-number-increment form-control ','min'=>1]) !!}
                                </div>

                                <div class="col-12 col-sm-12 col-md-6">
                                    <div class="form-group files">
                                        <label>{{ __('logo') }}</label>
                                        <span class="text-danger">*</span>
                                        <input name="logo" type="file" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('tagline') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('tagline', null, [ 'required', 'placeholder' => __('tagline'), 'class' => 'form-control', 'rows' => 3, ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('address', null, [ 'required', 'placeholder' => __('Address'), 'class' => 'form-control', 'rows' => 3, ]) !!}
                                </div>
                            </div>
                            <div class="separator mb-5"><span class="h5">{{ __('center_admin_details') }}</span></div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('user_first_name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                                </div>
                                {{--                                <div class="form-group col-sm-12 col-md-6 local-forms">--}}
                                {{--                                    <label>{{ __('last_name') }} <span class="text-danger">*</span></label>--}}
                                {{--                                    {!! Form::text('user_last_name', null, ['required', 'placeholder' => __('last_name'), 'class' => 'form-control']) !!}--}}
                                {{--                                </div>--}}

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('email') }} <span class="text-danger">*</span> <span title="This Email will be used to Login For Center" class="fa fa-info-circle"></span></label>
                                    {!! Form::text('user_email', null, ['required', 'placeholder' => __('email'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Contact') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('user_contact', null, ['required', 'placeholder' => __('contact'), 'class' => 'remove-number-increment form-control','min'=>1]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('user_dob', null, ['required', 'placeholder' => __('dob'), 'class' => 'dob-date form-control']) !!}
                                    <span class="input-group-addon input-group-append">
                                    </span>
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('user_gender', 'male') !!}
                                                {{ __('male') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('user_gender', 'female') !!}
                                                {{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-6">
                                    <div class="form-group files">
                                        <label>{{ __('image') }}</label>
                                        <span class="text-danger">*</span>
                                        <input name="user_image" type="file" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('current_address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('user_current_address', null, ['required','placeholder' => __('current_address'),'class' => 'form-control','rows' => 3,]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('permanent_address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('user_permanent_address', null, [ 'required', 'placeholder' => __('permanent_address'), 'class' => 'form-control', 'rows' => 3, ]) !!}
                                </div>
                            </div>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('center_list') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = route('centers.show',[1]);
                                    $columns = [
                                        trans('no')=>['data-field'=>'no'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                        trans('name ')=>['data-field'=>'name'],
                                        trans('Type')=>['data-field'=>'type'],
                                        trans('Support Contact')=>['data-field'=>'contact'],
                                        trans('Support Email')=>['data-field'=>'email'],
                                        trans('logo')=>['data-field'=>'logo','data-formatter'=>'imageFormatter'],
                                        trans('tagline')=>['data-field'=>'tagline'],
                                        trans('address')=>['data-field'=>'address'],
                                        trans('address')=>['data-field'=>'address'],
                                        trans('center_admin_details')=>['data-field'=>'admin','data-formatter'=>'centerAdminFormatter'],
//                                        trans('status')=>['data-field'=>'status','data-formatter'=>'centerStatusFormatter','data-events'=>'centerActionEvents'],
                                    ];
                                    $actionColumn = [
                                        'editButton'=>['url'=>url('centers')],
                                        'deleteButton'=>['url'=>url('centers')],
                                        'data-events'=>'centerActionEvents'
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


    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('edit_center') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="editform" action="{{ url('centers') }}" novalidate="novalidate" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="user_id">
                        <input type="hidden" name="id" id="id">
                        <div class="separator mb-5"><span class="h5">{{__('center_details')}}</span></div>
                        <div class="row">
                            <div class="form-group col">
                                {{ html()->label(__('center_type')) }}
                                {{ html()->select('type', [
                                    'secondary' => __('center_type_secondary'),
                                    'primary' => __('center_type_primary'),
                                ])->class('form-control')  }}
                                <span class="help-text text-danger">{{ __('alerts.center_type_cannot_change') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['id'=>'edit_name','required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('email') }} <span class="text-danger">*</span></label>
                                {!! Form::text('email', null, ['id'=>'edit_email','required', 'placeholder' => __('email'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Contact') }} <span class="text-danger">*</span></label>
                                {!! Form::text('contact', null, ['id'=>'edit_contact','required', 'placeholder' => __('contact'), 'class' => 'form-control']) !!}
                            </div>

                            <div class="col-12 col-sm-12 col-md-6">
                                <div class="form-group files">
                                    <label>{{ __('logo') }}</label>
                                    <input name="logo" type="file" class="form-control">
                                </div>
                            </div>

                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('tagline') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('tagline', null, [ 'id'=>'edit_tagline','required', 'placeholder' => __('tagline'), 'class' => 'form-control', 'rows' => 3, ]) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Address') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('address', null, ['id'=>'edit_address','required', 'placeholder' => __('Address'), 'class' => 'form-control', 'rows' => 3, ]) !!}
                            </div>

                            <div class="separator mb-5"><span class="h5">{{ __('center_admin_details') }}</span></div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('user_first_name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control','id'=>'edit_user_first_name']) !!}
                                </div>
                                {{--                                <div class="form-group col-sm-12 col-md-6 local-forms">--}}
                                {{--                                    <label>{{ __('last_name') }} <span class="text-danger">*</span></label>--}}
                                {{--                                    {!! Form::text('user_last_name', null, ['required', 'placeholder' => __('last_name'), 'class' => 'form-control','id'=>'edit_user_last_name']) !!}--}}
                                {{--                                </div>--}}

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('email') }} <span class="text-danger">*</span> <span title="This Email will be used to Login For Center" class="fa fa-info-circle"></span></label>
                                    {!! Form::text('user_email', null, ['required', 'placeholder' => __('email'), 'class' => 'form-control','id'=>'edit_user_email']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Contact') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('user_contact', null, ['required', 'placeholder' => __('contact'), 'class' => 'remove-number-increment form-control','min'=>1,'id'=>'edit_user_contact']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('user_dob', null, ['required', 'placeholder' => __('dob'), 'class' => 'dob-date form-control','id'=>'edit_user_dob']) !!}
                                    <span class="input-group-addon input-group-append">
                                    </span>
                                </div>
                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('user_gender', 'male',null,['id'=>"edit_male"]) !!}
                                                {{ __('male') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('user_gender', 'female',null,['id'=>"edit_female"]) !!}
                                                {{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('current_address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('user_current_address', null, ['required','placeholder' => __('current_address'),'class' => 'form-control','rows' => 3,'id'=>'edit_user_current_address']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('permanent_address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('user_permanent_address', null, [ 'required', 'placeholder' => __('permanent_address'), 'class' => 'form-control', 'rows' => 3,'id'=>'edit_user_permanent_address']) !!}
                                </div>
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
@section('js')
    <script !src="">
        function successFunction(response) {
            Swal.fire(
                'Center Login Credentials!',
                'Email : ' + response.email + '<br />Password : ' + response.password,
                'success'
            )
        }
    </script>
@endsection