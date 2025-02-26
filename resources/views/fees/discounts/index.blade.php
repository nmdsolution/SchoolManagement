@extends('layout.master')

@section('title')
    {{ __('fees') }} {{__('discount')}}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">{{ __('manage') }} {{__('fee_discounts')}} </h3>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ __('create') }} {{__('fee_discounts')}}</h4>
                        <form id="create-form" class="pt-3 create-form" url="{{ url('fees-discounts') }}" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-6 col-md-4 local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                                </div>

                                <div class="form-group col-sm-6 col-md-4 local-forms">
                                    <label>{{ __('percentage') . '(%)' }} <span class="text-danger">*</span></label>
                                    {!! Form::number('amount', null, [
                                        'required', 
                                        'step' => '0.01', 
                                        'min' => '0', 
                                        'max' => '100', 
                                        'class' => 'form-control',
                                        ]) 
                                    !!}
                                </div>

                                <div class="form-group col-sm-6 col-md-4 local-forms">
                                    <label>{{ __('status') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('applicable_status[]', $statusOptions, null, [
                                        'required',
                                        'class' => 'form-control select2',
                                        'multiple' => 'multiple',
                                        'data-placeholder' =>__('student_status'),
                                        'style' => 'width: 100%',
                                    ]) !!}
                                </div>
                                

                                <div class="form-group col-sm-6 col-md-4 local-forms">
                                    <label>{{ __('description') }}</label>
                                    {!! Form::textarea('description', null, ['placeholder' => __('description'), 'class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"> {{__('fee_discounts')}}</h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = route('fees.discounts.show', 1);
                                    $columns = [
                                        trans('no')=>['data-field'=>'no'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                        trans('name')=>['data-field'=>'name'],
                                        trans('percentage')=>['data-field'=>'amount'],
                                        trans('status')=>['data-field'=>'applicable_status'],
                                        trans('Active')=>['data-field'=>'active'],
                                        trans('description')=>['data-field'=>'description', 'data-visible'=>false],
                                        trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                        trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                                    ];
                                    $actionColumn = [
                                        'editButton'=>['url'=>url('fees-discounts')],
                                        'deleteButton'=>['url'=>url('fees-discounts')],
                                        'data-events'=>'FeesDiscountActionEvents'
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn></x-bootstrap-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Modal --}}
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{__('edit').' '. __('fee_discounts')}}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa fa-close"></i></span>
                        </button>
                    </div>
                    <form id="edit-form" class="pt-3 edit-form" action="{{ url('fees-discounts') }}">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('edit_name', null, ['required', 'class' => 'form-control edit_name', 'id' => 'edit_name', 'placeholder' => __('name')]) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ __('amount') }} <span class="text-danger">*</span></label>
                                {!! Form::number('edit_amount', null, ['required', 'step' => '0.01', 'min' => '0', 'max' => '100', 'class' => 'form-control edit_amount', 'id' => 'edit_amount']) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ __('status') }} <span class="text-danger">*</span></label>
                                {!! Form::select('edit_applicable_status[]', $statusOptions, null, [
                                    'required', 
                                    'class' => 'form-control edit_applicable_status select2', 
                                    'id' => 'edit_applicable_status', 
                                    'multiple' => 'multiple',

                                    ]) 
                                !!}
                            </div>

                            <div class="form-group">
                                <label>{{ __('description') }}</label>
                                {!! Form::textarea('edit_description', null, ['class' => 'form-control edit_description', 'id' => 'edit_description', 'placeholder' => __('description'), 'rows' => 3]) !!}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                dropdownParent: $('#create-form'),
                placeholder: "Select statuses",
                allowClear: true,
            });

        $('#editModal').on('shown.bs.modal', function () {
            $('.select2').select2({
                width: '100%',
                dropdownParent: $('#editModal')
                });
            });
        });
    </script>

@endsection