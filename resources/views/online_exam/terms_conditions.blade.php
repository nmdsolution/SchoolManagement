@extends('layout.master')

@section('title')
    {{__('online').' '.__('exam').' '.__('terms_condition')}}
@endsection


@section('content')

    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{__('online').' '.__('exam').' '.__('terms_condition')}}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formdata" class="setting-form" action="{{route('online-exam.store-terms-conditions')}}" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="type" id="type" value="{{$type}}">

                                <div class="form-group col-md-12 col-sm-12">
                                    <textarea id="tinymce_message" name="setting_message" required placeholder="{{__('online').' '.__('exam').' '.__('terms_condition')}}">{{ isset($settings->message) && !empty($settings->message) ? htmlspecialchars_decode($settings->message) : ''}}</textarea>
                                </div>
                            </div>
                            <input class="btn btn-primary" type="submit" value="{{ __('submit') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
