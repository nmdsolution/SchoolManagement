@extends('layout.master')

@section('title')
    {{ __('fees') }} {{__('type')}}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') }} {{__('fees')}} {{__('type')}}
            </h3>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') }} {{__('fees')}} {{__('type')}}
                        </h4>
                        <form id="create-form" class="pt-3 create-form" url="{{ url('fees-type') }}" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-6 col-md-4 local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                                </div>

                                <div class="form-group col-sm-6 col-md-5 local-forms">
                                    <label>{{ __('description') }} </label>
                                    {!! Form::textarea('description', null, ['placeholder' => __('description'), 'class' => 'form-control']) !!}
                                </div>
{{--                                <div class="form-group col-sm-6 col-md-3">--}}
{{--                                    <label>{{ __('choiceable') }} <span class="text-danger">*</span></label>--}}
{{--                                    <div class="form-check">--}}
{{--                                        <label class="form-check-label">--}}
{{--                                            {!! Form::radio('choiceable', 1, true, ['class' => 'form-check-input']) !!}--}}
{{--                                            {{ __('yes') }}--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
{{--                                    <div class="form-check">--}}
{{--                                        <label class="form-check-label">--}}
{{--                                            {!! Form::radio('choiceable', 0, false, ['class' => 'form-check-input']) !!}--}}
{{--                                            {{ __('no') }}--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </div>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') }} {{__('fees')}}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = route('fees-type.show',1);
                                    $columns = [
                                        trans('no')=>['data-field'=>'no'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                        trans('name')=>['data-field'=>'name'],
                                        trans('description')=>['data-field'=>'description'],
//                                        trans('choiceable')=>['data-field'=>'choiceable','data-formatter'=>'feesTypeChoiceable'],
                                        trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                        trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                                    ];
                                    $actionColumn = [
                                        'editButton'=>['url'=>url('fees-type')],
                                        'deleteButton'=>['url'=>url('fees-type')],
                                        'data-events'=>'FeesTypeActionEvents'
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn></x-bootstrap-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{__('edit_fees')}}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa fa-close"></i></span>
                        </button>
                    </div>
                    <form id="edit-form" class="pt-3 edit-form" action="{{ url('fees-type') }}">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">{{__('name')}} <span class="text-danger">*</span></label>
                                {!! Form::text('edit_name',null,['required','class' => 'form-control edit_name','id' => 'edit_name','placeholder' => __('name')]) !!}
                            </div>

                            <div class="form-group">
                                <label for="name">{{__('description')}} </label>
                                {!! Form::textarea('edit_description',null,array('class'=>'form-control edit_description','id'=>'edit_description','placeholder'=>__('description'))) !!}
                            </div>
{{--                            <div class="form-group">--}}
{{--                                <label>{{ __('choiceable') }}</label>--}}
{{--                                <div class="form-check">--}}
{{--                                    <label class="form-check-label">--}}
{{--                                        --}}{{-- {!! Form::radio('edit_choiceable', 1, true, ['class' => 'form-check-input edit_choiceable']) !!} --}}
{{--                                        <input type="radio" name="edit_choiceable" value="1" id="edit_choiceable_true" class="form-check-input">--}}
{{--                                        {{ __('yes') }}--}}
{{--                                    </label>--}}
{{--                                </div>--}}
{{--                                <div class="form-check">--}}
{{--                                    <label class="form-check-label">--}}
{{--                                        <input type="radio" name="edit_choiceable" value="0" id="edit_choiceable_false" class="form-check-input">--}}
{{--                                        --}}{{-- {!! Form::radio('edit_choiceable', 0,['class' => 'form-check-input edit_choiceable_false']) !!} --}}
{{--                                        {{ __('no') }}--}}
{{--                                    </label>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                        </div>
                    </form>
                </div>
            </div>
        </div>

@endsection
