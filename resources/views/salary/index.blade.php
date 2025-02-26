@extends('layout.master')
@section('title')
    {{ __('Salary') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Salary') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Salary Pay') }}
                        </h4>
                        <form class="create-form pt-3" id="formdata" action="{{route('salary.store')}}" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="separator mb-5"><span class="h5">{{__('Salary')}}</span></div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Teacher') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('teachers', $teachers, null, ['id'=>'teachers','required','placeholder' => __('Please Select'), 'class' => 'form-control']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Salary') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('salary', null, ['id'=>'salary','required','placeholder' => __('Amount'), 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Date') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('date', null, ['required', 'placeholder' => __('dd/mm/yyyy'), 'class' => 'datetimepicker form-control']) !!}
                                    <span class="input-group-addon input-group-append">
                                    </span>
                                </div>
                            </div>
                            <input class="btn btn-primary" type="submit" value={{ __('Pay Now') }} />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection        