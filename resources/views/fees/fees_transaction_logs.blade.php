@extends('layout.master')

@section('title')
    {{__('online')}} {{__('fees')}} {{ __('transactions') }} {{__('logs')}}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{__('online')}} {{__('fees')}} {{ __('transactions') }} {{__('logs')}}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="toolbar" class="row">
                            <div class="col">
                                <label for="filter_class_id" style="font-size: 0.89rem">
                                    {{ __('class') }}
                                </label>
                                <select name="filter_class_id" id="filter_class_id" class="form-control">
                                    <option value="">{{ __('all') }}</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">
                                            {{ $class->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label for="filter_session_year_id" style="font-size: 0.89rem">
                                    {{ __('session_years') }}
                                </label>
                                <select name="filter_session_year_id" id="filter_session_year_id" class="form-control">
                                    <option value="">{{__('all')}}</option>
                                    @foreach ($session_year_all as $session_year)
                                        <option value="{{ $session_year->id}}">
                                            {{ $session_year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col">
                                <label for="filter_payment_status" style="font-size: 0.86rem">
                                    {{ __('payment_status') }}
                                </label>
                                <select name="filter_payment_status" id="filter_payment_status" class="form-control">
                                    <option value="">{{__('all')}}</option>
                                    <option value="0">{{__('success')}}</option>
                                    <option value="1">{{__('failed')}}</option>
                                    <option value="2">{{__('pending')}}</option>
                                </select>
                            </div>
                        </div>
                        @php
                            $url = route('fees.transactions.log.list', 1);
                            $columns = [
                                trans('no')=>['data-field'=>'no'],
                                trans('id')=>['data-field'=>'id','data-visible'=>false],
                                trans('student_id')=>['data-field'=>"student_id",'data-sortable'=>true, 'data-visible'=>false],
                                trans('students').' '.trans('name')=>['data-field'=>"student_name"],
                                trans('total').' '.trans('fees')=>['data-field'=>"total_fees", 'data-align'=>"center"],
                                trans('fees').' '.trans('paid')=>['data-field'=>"amount_paid", 'data-align'=>"center"],
                                trans('fees').' '.trans('left')=>['data-field'=>"fees_left", 'data-align'=>"center"],
                                trans('payment_gateway')=>['data-field'=>"payment_gateway", 'data-align'=>"center",'data-visible'=>false,  'data-formatter'=>"feesOnlineTransactionLogParentGateway"],
                                trans('payment_status')=>['data-field'=>"payment_status", 'data-align'=>"center",'data-formatter'=>"feesOnlineTransactionLogPaymentStatus"],
                                trans('order_id').' / '.trans('payment_intent_id')=>['data-field'=>"order_id", 'data-align'=>"center",'data-visible'=>false],
                                trans('payment_id')=>['data-field'=>"payment_id", 'data-align'=>"center",'data-visible'=>false],
                                trans('payment_signature')=>['data-field'=>"payment_signature",'data-align'=>"center",'data-visible'=>false],
                                trans('session_years')=>[ 'data-field'=>"session_year_name", 'data-align'=>"center"],
                                trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                            ];
                            $actionColumn = [
                                'editButton'=>['url'=>url('section')],
                                'deleteButton'=>['url'=>url('section')],
                                'data-events'=>'sectionEvents'
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="feesPaymentTransactionQueryParams"></x-bootstrap-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
