@extends('layout.master')

@section('title')
    {{ __('Exam Sequences') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Exam Sequences') }}
            </h3>
        </div>

        <div class="row">
{{--            <div class="col-lg-12 grid-margin stretch-card">--}}
{{--                <div class="card">--}}
{{--                    <div class="card-body">--}}
{{--                        <h4 class="card-title">--}}
{{--                            {{ __('Create Exam Sequences') }}--}}
{{--                        </h4>--}}
{{--                        <form class="create-form pt-3" id="formdata" data-success-function="customTermSuccess" action="{{ route('exam-sequences.store') }}" enctype="multipart/form-data" method="POST" novalidate="novalidate">--}}
{{--                            @csrf--}}
{{--                            <div class="row">--}}
{{--                                <div class="form-group col-sm-12 col-md-6 local-forms">--}}
{{--                                    <label>{{ __('Term') }} <span class="text-danger">*</span></label>--}}
{{--                                    <select name="exam_term_id" id="exam_term_id" class="form-control">--}}
{{--                                        <option value="">{{ __('Select Term') }}</option>--}}
{{--                                        @foreach($exam_term as $term)--}}
{{--                                            <option value="{{$term->id}}">{{$term->name}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                                <div class="form-group col-sm-12 col-md-6 local-forms">--}}
{{--                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>--}}
{{--                                    {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}--}}
{{--                                </div>--}}
{{--                                <div class="form-group col-sm-12 col-md-6 local-forms">--}}
{{--                                    <label>{{ __('Start Date') }} <span class="text-danger">*</span></label>--}}
{{--                                    {!! Form::text('start_date', null, ['required', 'placeholder' => __('Start Date'), 'class' => 'form-control datepicker start_date','autocomplete'=>'off']) !!}--}}
{{--                                </div>--}}
{{--                                <div class="form-group col-sm-12 col-md-6 local-forms">--}}
{{--                                    <label>{{ __('End Date') }} <span class="text-danger">*</span></label>--}}
{{--                                    {!! Form::text('end_date', null, ['required', 'placeholder' => __('End Date'), 'class' => 'form-control datepicker end_date','autocomplete'=>'off']) !!}--}}
{{--                                </div>--}}
{{--                                <div class="form-group col-sm-12 col-md-4">--}}
{{--                                    <label>{{ __('status') }} <span class="text-danger">*</span></label>--}}
{{--                                    <br>--}}
{{--                                    <div class="d-flex">--}}
{{--                                        <div class="form-check form-check-inline">--}}
{{--                                            <label class="form-check-label">--}}
{{--                                                {!! Form::radio('status', '1',false,['id' => 'active','required'=>true]) !!}--}}
{{--                                                {{ __('Active') }}--}}
{{--                                            </label>--}}
{{--                                        </div>--}}
{{--                                        <div class="form-check form-check-inline">--}}
{{--                                            <label class="form-check-label">--}}
{{--                                                {!! Form::radio('status', '0',false ,['id' => 'inactive','required'=>true]) !!}--}}
{{--                                                {{ __('Inactive') }}--}}
{{--                                            </label>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <input class="btn btn-primary" type="submit" value={{ __('submit') }} />--}}
{{--                        </form>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Exam Sequence List') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = route('exam-sequences.show',[1]);
                                    $columns = [
                                        trans('no')=>['data-field'=>'no'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                        trans('name ')=>['data-field'=>'name'],
                                        trans('Term')=>['data-field'=>'term'],
                                        trans('start_date')=>['data-field'=>'start_date'],
                                        trans('end_date')=>['data-field'=>'end_date'],
                                        trans('status')=>['data-field'=>'status','data-formatter'=>'statusFormatter'],
                                        trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                        trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                                    ];
                                    $actionColumn = [
                                        'editButton'=> ['url'=>url('exam-sequences')],
                                        'deleteButton'=> ['url'=>url('exam-sequences')],
                                        'data-events'=>'ExamSequenceEvents'
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

    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Edit Exam Sequence') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="edit-form edit-sequence-form-validate" action="{{ url('exam-sequences') }}" novalidate="novalidate" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <span>{{ __('Exam Term') }} : <b id="edit_exam_term_id"></b></span>
                        <div class="row mt-3">

                            <div class="form-group col-sm-12 col-md-12 local-forms">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['id'=>'edit_name','required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                {!! Form::text('start_date', null, ['required', 'placeholder' => __('Start Date'), 'class' => 'form-control datepicker start_date','autocomplete'=>'off','id'=>'edit-start-date']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('End Date') }} <span class="text-danger">*</span></label>
                                {!! Form::text('end_date', null, ['required', 'placeholder' => __('End Date'), 'class' => 'form-control datepicker end_date','autocomplete'=>'off','id'=>'edit-end-date']) !!}
                            </div>


                            <div class="form-group col-sm-12 col-md-12" id="class_section_div">
                                <label>{{ __('class') }}<span class="text-danger">*</span></label>
                                <br>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="select-all-class-section"/>
                                        {{ __('Select All') }}
                                    </label>
                                </div>
                                <select name="class_section_id[]" id="class_section_id" class="class-section-in-sequence form-control select" multiple required>
                                    @if (isset($class_sections))
                                        @foreach ($class_sections as $class_section)
                                            <option value="{{$class_section['id']}}">
                                                {{$class_section['class']['full_name']}}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>

                            </div>

                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('status') }} <span class="text-danger">*</span></label>
                                <br>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('status', '1',false,['id' => 'edit-active','required'=>true]) !!}
                                            {{ __('Active') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('status', '0',false ,['id' => 'edit-inactive','required'=>true]) !!}
                                            {{ __('Inactive') }}
                                        </label>
                                    </div>
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
@section('script')
    <script>
        function customTermSuccess() {
            $('#exam_term_id option:selected').remove();
        }
    </script>
@endsection