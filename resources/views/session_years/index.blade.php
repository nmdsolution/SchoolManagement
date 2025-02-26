@extends('layout.master')

@section('title')
    {{ __('session_years') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage').' '.__('session_years') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create').' '.__('session_years') }}
                        </h4>
                        <form action="{{ url('session-years') }}" class="create-form pt-3" id="formdata" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('start_date') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('start_date', null, ['required', 'placeholder' => __('start_date'), 'class' => 'datetimepicker form-control']) !!}
                                    <span class="input-group-addon input-group-append">
                                </span>
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('end_date') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('end_date', null, ['required', 'placeholder' => __('end_date'), 'class' => 'datetimepicker form-control']) !!}
                                    <span class="input-group-addon input-group-append">
                                </span>
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <label>{{ __('fees') }} {{ __('due_date')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="fees_due_date" class="datetimepicker form-control" placeholder="{{ __('fees') }} {{ __('due_date')}}" required>
                                    </span>
                                </div>
                                <div class="form-group col-sm-12 col-md-3">
                                    <label>{{ __('fees') }} {{ __('due_charges')}} <span class="text-danger">*</span> <span class="text-info small">( {{__('in_percentage_%')}} )</span></label>
                                    <input type="number" min="1" name="fees_due_charges" class="form-control" placeholder="{{ __('fees') }} {{ __('due_charges')}}" required>
                                    </span>
                                </div>
                            </div>
                            <h4 class="card-title">
                                {{ __('fees').' '.__('installment') }}
                            </h4>
                            <div class="row mb-4 mt-4">
                                <div class="form-inline col-md-4">
                                    <label>{{__('include')}} {{__('fees')}} {{__('installment')}}</label> <span class="ml-1 text-danger">*</span>
                                    <div class="ml-4 d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="fees_installment" class="fees_installment_toggle" value="1">
                                                {{ __('enable') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="fees_installment" class="fees_installment_toggle" value="0" checked>
                                                {{ __('disable') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="fees_installment_content" style="display: none">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="installmentName_1">{{ __('installment') }} {{__('name')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="installment_data[1][name]" id="installmentName_1" class="form-control" placeholder="{{ __('installment') }} {{__('name')}}" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="installmentDueDate_1">{{ __('due_date') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="installment_data[1][due_date]" id="installmentDueDate_1" class="datepicker form-control" placeholder="{{ __('due_date') }}" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="installmentDueCharges_1">{{ __('due_charges') }} <span class="text-danger">*</span><span class="text-info small">( {{__('in_percentage_%')}} )</span></label>
                                        <input type="number" name="installment_data[1][due_charges]" id="installmentDueCharges_1" class="form-control" placeholder="{{ __('due_charges') }}" required>
                                    </div>
                                    <div class="form-group col-md-1 pl-0 mt-4">
                                        <button type="button" class="btn btn-primary btn-icon add-fee-installment-content">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="extra-fee-installment-content"></div>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list').' '.__('session_years') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = url('session_years_list');
                                    $columns = [
                                        trans('no')=>['data-field'=>'no'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                        trans('name')=>['data-field'=>"name", 'data-sortable'=>true],
                                        trans('start_date')=>['data-field'=>"start_date", 'data-sortable'=>true],
                                        trans('end_date')=>['data-field'=>"end_date" ,'data-sortable'=>true],
                                        trans('default')=>['data-field'=>"default" ,'data-sortable'=>true, 'data-formatter'=>"defaultYearFormatter"],
                                        trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                        trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                                    ];
                                    $actionColumn = [
                                        'editButton'=>['url'=>url('session-years')],
                                        'deleteButton'=>['url'=>url('session-years')],
                                        'data-events'=>'sessionYearEvents'
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
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{ __('edit').' '.__('session_years') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="editform" action="{{ url('session-years') }}" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="row form-group">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control','id'=>'name']) !!}

                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('start_date') }} <span class="text-danger">*</span></label>
                                {!! Form::text('start_date', null, ['required', 'placeholder' => __('start_date'), 'class' => 'datetimepicker form-control','id'=>'start_date']) !!}
                                <span class="input-group-addon input-group-append">
                            </span>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('end_date') }} <span class="text-danger">*</span></label>
                                {!! Form::text('end_date', null, ['required', 'placeholder' => __('end_date'), 'class' => 'datetimepicker form-control','id'=>'end_date']) !!}
                                <span class="input-group-addon input-group-append">
                            </span>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="fees_due_date">{{ __('fees') }} {{ __('due_date')}} <span class="text-danger">*</span></label>
                            <input type="text" name="fees_due_date" class="datepicker form-control" id="fees_due_date" placeholder="{{ __('fees') }} {{ __('due_date')}}" required>

                        </div>
                        <div class="form-group col-md-12">
                            <label for="fees_due_charges">{{ __('fees') }} {{ __('due_charges')}} <span class="text-danger">*</span> <span class="text-info small">( {{__('in_percentage_%')}} )</span></label>
                            <input type="number" name="fees_due_charges" class="form-control" id="fees_due_charges" placeholder="{{ __('fees') }} {{ __('due_charges')}}" min="1" max="100" required>
                        </div>
                        <div class="row mb-4 mt-12">
                            <div class="form-inline col-md-12">
                                <label>{{__('include')}} {{__('fees')}} {{__('installment')}}</label> <span class="ml-1 text-danger">*</span>
                                <div class="ml-4 d-flex">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="fees_installment" class="edit_fees_installment_toggle" value="1">
                                            {{ __('enable') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="fees_installment" class="edit_fees_installment_toggle" value="0">
                                            {{ __('disable') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group installment-div edit_fees_installment_content" style="display:none">
                            <hr class="edit-installment-hr" style='width:100%;margin-top: 1rem;margin-bottom: 1rem;border: 0;border-top: 1px solid rgba(0, 0, 0, 0.1);'>
                            <h5 class="card-title edit-installment-heading ml-3">{{ __('edit').' '.__('fees').' '.__('installment') }}</h5>
                            <div class="edit-installment-container col-md-12 mt-4"></div>
                            <div class="form-group col-md-12 mt-4">
                                <button type="button" class="btn btn-primary add-extra-fee-installment-data">
                                    <i class="fa fa-plus"></i> {{__('add_new_data')}} </button>
                            </div>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="edit-installment-content-template" style="display: none">
        <input type="hidden" name="installment_data[0][id]" id="editInstallmentId_0" class="form-control">
        <div class="row">
                <div class="form-group col-md-12">
                    <label>{{ __('installment') }} {{__('name')}} <span class="text-danger">*</span></label>
                    <input type="text" name="installment_data[0][name]" id="editInstallmentName_0" class="form-control" placeholder="{{ __('installment') }} {{__('name')}}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>{{ __('due_date') }} <span class="text-danger">*</span></label>
                    <input type="text" name="installment_data[0][due_date]" id="editInstallmentDueDate_0" class="datepicker form-control" placeholder="{{ __('due_date') }}" required>
                </div>
                <div class="form-group col-md-5">
                    <label>{{ __('due_charges') }} <span class="text-danger">*</span><span class="text-info small">( {{__('in_percentage_%')}} )</span></label>
                    <input type="number" name="installment_data[0][due_charges]" id="editInstallmentDueCharges_0" class="form-control" placeholder="{{ __('due_charges') }}" min="1" required>
                </div>
                <div class="form-group col-md-1 pl-0 mt-4">
                    <button type="button" class="btn btn-primary btn-icon add-edit-fee-installment-content">
                        <i class="fa fa-plus"></i></button>
                </div>
        </div>
    </div>
@endsection
