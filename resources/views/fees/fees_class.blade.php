@extends('layout.master')

@section('title')
    {{__('fees')}} {{__('classes')}}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') }} {{__('fees')}} {{__('classes')}}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        @php
                            $url = route('fees.class.list');
                            $columns = [
                                trans('no')=>['data-field'=>'no'],
                                trans('id')=>['data-field'=>'id','data-visible'=>false],
                                trans('class')=>['data-field'=>'class_name','data-sortable'=>true],
                                trans('fees').''.trans('type')=>['data-field'=>"fees_type",'data-align'=>"left",'data-formatter'=>"feesTypeFormatter"],
                                trans('base').' '.trans('amount')=>['data-field'=>"base_amount",'data-align'=>"center"],
                                trans('total').' '.trans('amount')=>['data-field'=>"total_amount", 'data-align'=>"center"],
                                trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                            ];
                            $actionColumn = [
                                'editButton'=>['url'=>url('class/fees-type')],
                                'deleteButton'=>false,
                                'data-events'=>'feesClassEvents'
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="AssignclassQueryParams"></x-bootstrap-table>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{ __('edit') . ' ' . __('fees') }}
                            </h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        {{-- Template for old fees type --}}
                        <div class="row edit-fees-type-div" style="display: none;">
                            <div class="row col-11">
                                <div class="form-group col-md-4">
                                    <input type="hidden" name="edit_fees_type[0][fees_class_id]" class="edit-fees-type-id form-control" disabled>
                                    <select name="edit_fees_type[0][fees_type_id]" class="edit-fees-type form-control" required="required">
                                        <option value="">{{ __('select') }} {{__('fees')}} {{__('type')}}</option>
                                        @foreach ($fees_type_data as $fees_type)
                                            <option value="{{ $fees_type->id }}">{{ $fees_type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    {!! Form::number('edit_fees_type[0][amount]', null, ['class' => 'form-control edit_amount','placeholder' => __('enter').' '.__('fees').' '.__('amount'),'id' => 'edit_amount','min' => '0']) !!}
                                </div>
                                <div class="form-group col-sm-2 col-md-2">
                                    <label>{{ __('choiceable') }} <span class="text-danger">*</span></label>
                                    <div class="row form-check">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('edit_fees_type[0][choiceable]', 1, true, [
                                                'class' => 'form-check-input',
                                                'id' => 'editChoiceableYes_0'
                                            ]) !!}
                                            {{ __('yes') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('edit_fees_type[0][choiceable]', 0, false, [
                                                'class' => 'form-check-input',
                                                'id' => 'editChoiceableNo_0'
                                            ]) !!}
                                            {{ __('no') }}
                                        </label>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1 pl-0">
                                <button type="button" class="btn btn-icon btn-danger remove-fees-type" title="Remove Core Subject">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Template for New fees type --}}
                        <div class="row template_fees_type" style="display: none;">
                            <div class="row col-11">
                                <div class="form-group col-md-4">
                                    <select name="fees_type[0][fees_type_id]" class="form-control" required="required">
                                        <option value="">{{ __('select') }} {{__('fees')}} {{__('type')}}</option>
                                        @foreach ($fees_type_data as $fees_type)
                                            <option value="{{ $fees_type->id }}">{{ $fees_type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    {!! Form::number('fees_type[0][amount]', null, ['class' => 'form-control amount-text','placeholder' => __('enter').' '.__('fees').' '.__('amount'),'id' => 'amount-text','min' => '0']) !!}
                                </div>
                                <div class="form-group col-sm-2 col-md-2">
                                    <label>{{ __('choiceable') }} <span class="text-danger">*</span></label>
                                    <div class="row form-check">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            {!! Form::radio('fees_type[0][choiceable]', 1, true, [
                                                'class' => 'form-check-input',
                                                'id' => 'choiceableYes_0'
                                            ]) !!}
                                            {{ __('yes') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            {!! Form::radio('fees_type[0][choiceable]', 0, false, [
                                                'class' => 'form-check-input',
                                                'id' => 'choiceableNo_0'
                                            ]) !!}
                                            {{ __('no') }}
                                        </label>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1 pl-0">
                                <button type="button" class="btn btn-primary btn-icon add-fees-type remove_field" title="" id="remove_field">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <form class="pt-3" id="fees-class-create-form" action="{{ url('class/fees-type') }}" novalidate="novalidate">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>{{ __('class') }} <span class="text-danger">*</span></label>
                                    <select name="class_id" id="edit_class_id" class="form-control" disabled>
                                        <option value="">{{ __('select_class') }}</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id}}" data-medium="{{$class->medium_id}}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="class_id" id="class_id" value=""/>
                                </div>

                                <h4 class="mb-3">
                                    {{ __('fees') }} {{__('type')}}
                                </h4>
                                {{-- Dynamic New fees type will be added in this DIV --}}
                                <div class="mt-3 edit-extra-fees-types"></div>

                                <div>
                                    <div class="form-group pl-0 mt-4">
                                        <button type="button"
                                                class="col-md-3 btn btn-primary add-new-fees-type amount choiceable"
                                                id="amount">
                                            {{ __('fees') }} {{ __('type') }} <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                                <input class="btn btn-primary" type="submit" value={{ __('save') }} />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
