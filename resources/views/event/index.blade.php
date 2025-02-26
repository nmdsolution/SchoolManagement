@php use Illuminate\Support\Facades\Auth; @endphp@extends('layout.master')

@section('title')
    {{ __('Event') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Event') }}
            </h3>
        </div>

        <div class="row">
            @if (Auth::user()->can('event-create'))
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('Create Event') }}
                            </h4>
                            <form class="create-event-form-validate create-form pt-3" id="formdata" action="{{url('event')}}" method="POST" novalidate="novalidate">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-5 local-forms">
                                        <label for="">{{ __('Name') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('name', null, ['required', 'placeholder' => __('Name'), 'class' => 'form-control']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-2 local-forms">
                                        <label for="">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('start_date', null, ['required', 'class' => 'form-control datetimepicker','id'=>'start_date_check']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-2 local-forms">
                                        <label for="">{{ __('End Date') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('end_date', null, ['required', 'class' => 'form-control datetimepicker']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3 local-forms">
                                        <label for="">{{ __('Location') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('location', null, ['required', 'placeholder' => __('Location'), 'class' => 'form-control']) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12 local-forms">
                                        <label for="">{{ __('Description') }}</label>
                                        {!! Form::textarea('description', null, ['placeholder' => __('Description'), 'class' => 'form-control']) !!}
                                    </div>

                                </div>
                                <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            @if (Auth::user()->can('event-list'))
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('list') . ' ' . __('Event') }}
                            </h4>

                            <div class="row">
                                <div class="col-12">
                                    @php
                                        $actionColumn = '';
                                            $url = route('event.show',1);
                                            $columns = [
                                                trans('no')=>['data-field'=>'no'],
                                                trans('id')=>['data-field'=>'id','data-visible'=>false],
                                                trans('name')=>['data-field'=>'name','data-sortable'=>true],
                                                trans('Description')=>['data-field'=>'description','data-sortable'=>true],
                                                trans('Start Date')=>['data-field'=>'start_date','data-sortable'=>true],
                                                trans('End Date')=>['data-field'=>'end_date','data-sortable'=>true],
                                                trans('Location')=>['data-field'=>'location','data-sortable'=>true],
                                            ];
                                             if (Auth::user()->can('event-edit') || Auth::user()->can('event-delete')){
                                                $actionColumn = [
                                                    'editButton'=> ['url'=>url('event')],
                                                    'deleteButton'=> ['url'=>url('event')],
                                                    'data-events'=>'eventEvents'
                                                ];
                                             }
                                    @endphp
                                    <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="eventParam"></x-bootstrap-table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('Edit Event')  }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="update-event-form-validate editform" action="{{url('event')}}" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="row form-group">
                            <div class="col-sm-12 col-md-12 local-forms">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['required', 'placeholder' => __('Name'), 'class' => 'form-control', 'id' => 'name']) !!}
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                {!! Form::text('start_date', null, ['required', 'placeholder' => __('Start Date'), 'class' => 'form-control datetimepicker', 'id' => 'start_date']) !!}
                            </div>
                            <div class="col-sm-12 col-md-6 local-forms">
                                <label>{{ __('End Date') }} <span class="text-danger">*</span></label>
                                {!! Form::text('end_date', null, ['required', 'placeholder' => __('End Date'), 'class' => 'form-control datetimepicker', 'id' => 'end_date']) !!}
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12 col-md-12 local-forms">
                                <label>{{ __('Location') }} <span class="text-danger">*</span></label>
                                {!! Form::text('location', null, ['required', 'placeholder' => __('Location'), 'class' => 'form-control', 'id' => 'location']) !!}
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12 col-md-12 local-forms">
                                <label>{{ __('Description') }} </label>
                                {!! Form::textarea('description', null, ['placeholder' => __('Description'), 'class' => 'form-control', 'id' => 'description']) !!}
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
