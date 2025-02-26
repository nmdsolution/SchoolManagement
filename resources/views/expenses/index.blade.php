@extends('layout.master')

@section('title')
    {{ __('Expenses') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Expenses') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Create New Expense') }}
                        </h4>
                        <form class="create-form pt-3" id="formdata" action="{{ url('expense') }}" method="POST"
                            novalidate="novalidate">
                            @csrf
                            <div class="separator mb-5"><span class="h5">{{ __('Expenses') }}</span></div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Item Name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('item_name', null, ['required', 'placeholder' => __('Item Name'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Quantity') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('qty', null, ['required', 'placeholder' => __('Qty'), 'class' => 'form-control', 'min' => '1']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Amount') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('amount', null, [
                                        'required',
                                        'placeholder' => __('Amount'),
                                        'class' => 'form-control',
                                        'min' => '0',
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Purchase By') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('purchase_by', null, ['required', 'placeholder' => __('Purchase By'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Purchase Source') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('purchase_from', null, [
                                        'required',
                                        'placeholder' => __('Purchase Source'),
                                        'class' => 'form-control',
                                    ]) !!}
                                    <span class="input-group-addon input-group-append">
                                    </span>
                                </div>
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Purchase Date') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('date', null, [
                                        'required',
                                        'placeholder' => __('dd/mm/yyyy'),
                                        'class' => 'datetimepicker form-control',
                                    ]) !!}
                                    <span class="input-group-addon input-group-append">
                                    </span>
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
                            {{ __('List Expenses') }}
                        </h4>
                        <div id="toolbar">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-3 mb-4 local-forms">
                                    <label for="">{{ __('Date Range') }}</label>
                                    <input type="text" name="filter_daterange" id="filter_daterange" class="form-control"
                                        placeholder="mm/dd/yyyy">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = url('expense/show');
                                    $columns = [
                                        trans('no') => ['data-field' => 'no'],
                                        trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                        trans('Item Name') => ['data-field' => 'item_name'],
                                        trans('Qty') => ['data-field' => 'qty', 'data-visible' => false],
                                        trans('Amount') => ['data-field' => 'amount', 'data-visible' => false],
                                        trans('Purchase By') => ['data-field' => 'purchase_by'],
                                        trans('Purchase From') => ['data-field' => 'purchase_from'],
                                        trans('date') => ['data-field' => 'date'],
                                        trans('Total Amount') => ['data-field' => 'total_amount'],
                                        trans('Created at') => ['data-field' => 'created_at', 'data-visible' => false],
                                        trans('Updated at') => ['data-field' => 'updated_at', 'data-visible' => false],
                                    ];
                                    $actionColumn = [
                                        'editButton' => ['url' => url('/expense')],
                                        'deleteButton' => ['url' => url('/expense')],
                                        'data-events' => 'expenseEvents',
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn
                                    queryParams="expenseQueryParams">
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
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Edit Expenses') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form class="editform" action="" novalidate="novalidate" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="separator mb-5"><span class="h5">{{ __('Expenses Details') }}</span></div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Item Name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('item_name', null, [
                                    'id' => 'item_name',
                                    'required',
                                    'placeholder' => __('Item Name'),
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Quantity') }} <span class="text-danger">*</span></label>
                                {!! Form::number('qty', null, ['id' => 'qty', 'placeholder' => __('Qty'), 'class' => 'form-control','min' => '0']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Amount') }} <span class="text-danger">*</span></label>
                                {!! Form::number('amount', null, [
                                    'id' => 'amount',
                                    'required',
                                    'placeholder' => __('Amount'),
                                    'class' => 'form-control','min' => '0'
                                ]) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Purchase By') }} <span class="text-danger">*</span></label>
                                {!! Form::text('purchase_by', null, [
                                    'id' => 'purchase_by',
                                    'placeholder' => __('Purchase By'),
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Purchase Source') }} <span class="text-danger">*</span></label>
                                {!! Form::text('purchase_from', null, [
                                    'id' => 'purchase_from',
                                    'placeholder' => __('Purchase Source'),
                                    'class' => 'form-control',
                                ]) !!}
                                <span class="input-group-addon input-group-append">
                                </span>
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Purchase Date') }} <span class="text-danger">*</span></label>
                                {!! Form::text('date', null, [
                                    'id' => 'date',
                                    'required',
                                    'placeholder' => __('dd/mm/yyyy'),
                                    'class' => 'datetimepicker form-control',
                                ]) !!}
                                <span class="input-group-addon input-group-append">
                                </span>
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
