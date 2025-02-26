@extends('layout.master')

@section('title')
    {{ __('Income') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage_incomes') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create_new_income') }}
                        </h4>
                        <form class="create-form pt-3" id="formdata" action="{{ url('accounting/income') }}"
                              method="POST"
                              novalidate="novalidate">
                            @csrf
                            <div class="separator mb-5"><span class="h5">{{ __('Incomes') }}</span></div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('Item Name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('Item Name'),
                                    'class' => 'form-control']) !!}
                                </div>

                                <div class="form-group  col-sm-12 col-md-4 local-forms">
                                    <label for="">{{__('category')}}</label>
                                    {!! Form::select('category', $categories , null, ['class' =>
                                    'form-control select', 'id' => 'category']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('Amount') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('amount', null, [
                                        'required',
                                        'placeholder' => __('Amount'),
                                        'class' => 'form-control',
                                        'min' => '0',
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('Quantity') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('quantity', 1, [
                                        'required',
                                        'class' => 'form-control',
                                        'min' => '1',
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('Purchase By') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('purchased_by', null, ['required', 'placeholder' => __('Purchased By'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('purchased_from') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('purchased_from', null, [
                                        'required',
                                        'placeholder' => __('purchased_from'),
                                        'class' => 'form-control',
                                    ]) !!}
                                    <span class="input-group-addon input-group-append">
                                    </span>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('purchased_date') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('date', null, [
                                        'required',
                                        'placeholder' => __('dd/mm/yyyy'),
                                        'class' => 'datetimepicker form-control',
                                    ]) !!}
                                    <span class="input-group-addon input-group-append">
                                    </span>
                                </div>

                                <div class="form-group  col-sm-12 col-md-4 local-forms">
                                    <label for="">{{__('Payment Method')}}</label>
                                    {!! Form::select('payment_method', $paymentMethods , ["All"], ['class' =>
                                    'form-control select', 'id' => 'payment_method']) !!}
                                </div>

                                <div class="form-group  col-sm-12 col-md-4 local-forms">
                                    <label for="">{{__('Attachment')}}</label>
                                    {!! Form::file('attach', ['class' =>
                                    'form-control', 'id' => 'attach']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('Note') }}</label>
                                    {!! Form::text('note', null, ['placeholder' => __('Note'),
                                    'class' => 'form-control']) !!}
                                </div>

                                <div class="form-group  col-sm-12 col-md-4 local-forms">
                                    <label for="">{{__('Medium')}}</label>
                                    {!! Form::select('medium', $mediums , ["All"], ['class' =>
                                    'form-control select', 'id' => 'category']) !!}
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
                            {{ __('List Incomes') }}
                        </h4>
                        <div id="toolbar">
                            <div class="row">
                                <div class="row" id="toolbar">
                                    <div class="col-md-3 col-sm-12">
                                        <label for="">{{__('From')}}</label>
                                        {!! Form::select('from_date', ['Today', 'Yesterday'], null, ['class' =>
                                        'form-control select', 'id' => 'from-date']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">

                                @php
                                    $url = route('income.show', 'good');
                                    $columns = [
                                         trans('no') => ['data-field' => 'no'],
                                        trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                        trans('Item Name') => ['data-field' => 'name'],
                                        trans('Category') => ['data-field' => 'category'],
                                        trans('Qty') => ['data-field' => 'quantity'],
                                        trans('Amount') => ['data-field' => 'amount'],
                                        trans('Purchase By') => ['data-field' => 'purchase_by'],
                                        trans('Purchase From') => ['data-field' => 'purchase_from'],
                                        trans('date') => ['data-field' => 'date'],
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
                                                   queryParams="incomeQueryParams">
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
                                {!! Form::text('name', null, [
                                    'id' => 'name',
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
                                {!! Form::text('purchased_by', null, [
                                    'id' => 'purchased_by',
                                    'placeholder' => __('Purchase By'),
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Purchase Source') }} <span class="text-danger">*</span></label>
                                {!! Form::text('purchased_from', null, [
                                    'id' => 'purchased_from',
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
