@extends('layout.master')

@section('title')
    {{ __('holiday') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('holiday') }}
            </h3>
        </div>

        <div class="row">
            @if (Auth::user()->can('holiday-create'))
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('create') . ' ' . __('holiday') }}
                            </h4>
                            <form class="create-form pt-3" id="formdata" action="{{url('holiday')}}" method="POST" novalidate="novalidate">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('date') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('date', null, ['required', 'placeholder' => __('date'), 'class' => 'disable-past-date form-control']) !!}
                                        <span class="input-group-addon input-group-append">
                                    </span>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6 local-forms">
                                        <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('title', null, ['required', 'placeholder' => __('title'), 'class' => 'form-control']) !!}

                                    </div>
                                </div>
                                <div class="row">

                                    <div class="form-group col-sm-12 col-md-12 local-forms">
                                        <label>{{ __('description') }}</label>
                                        {!! Form::textarea('description', null, ['rows' => '2', 'placeholder' => __('description'), 'class' => 'form-control']) !!}

                                    </div>
                                </div>
                                <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
            @if (Auth::user()->can('holiday-list'))
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('list') . ' ' . __('holiday') }}
                            </h4>

                            <div class="row">
                                <div class="col-12">
                                    @php
                                        $actionColumn = '';
                                            $url = url('holiday-list');
                                            $columns = [
                                                trans('no')=>['data-field'=>'no'],
                                                trans('id')=>['data-field'=>'id','data-visible'=>false],
                                                trans('date')=>['data-field'=>'date','data-sortable'=>true],
                                                trans('title')=>['data-field'=>'title','data-sortable'=>true],
                                                trans('description')=>['data-field'=>'description'],
                                            ];
                                            // if (Auth::user()->can('holiday-edit') || Auth::user()->can('holiday-delete')){
                                                $actionColumn = [
                                                    'editButton'=> ['url'=>url('holiday')],
                                                    'deleteButton'=> ['url'=>url('holiday')],
                                                    'data-events'=>'holidayEvents'
                                                ];
                                            // }
                                    @endphp
                                    <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="HolidayParam"></x-bootstrap-table>
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
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('edit') . ' ' . __('holiday') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="editform" action="{{url('holiday')}}" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="row form-group">
                            <div class="col-sm-12 col-md-12">
                                <label>{{ __('date') }} <span class="text-danger">*</span></label>
                                {!! Form::text('date', null, ['required', 'placeholder' => __('date'), 'class' => 'datetimepicker form-control', 'id' => 'date']) !!}
                                <span class="input-group-addon input-group-append">
                                </span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12 col-md-12">
                                <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                {!! Form::text('title', null, ['required', 'placeholder' => __('title'), 'class' => 'form-control', 'id' => 'title']) !!}
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-12 col-md-12">
                                <label>{{ __('description') }}</label>
                                {!! Form::text('description', null, ['placeholder' => __('description'), 'class' => 'form-control', 'id' => 'description']) !!}
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
