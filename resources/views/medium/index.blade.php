@extends('layout.master')
@section('title')
    {{ __('medium') }}
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('title')
            {{__('create_medium')}}
        @endslot
        @slot('li_1')
            {{__('medium')}}
        @endslot
        @slot('li_2')
            {{__('create_medium')}}
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-sm-4">
            <div class="card">
                <div class="card-body">
                    <form id="create-form" class="medium-create-form" action="{{ url('medium') }}" method="POST"
                          novalidate="novalidate">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <h5 class="form-title"><span>{{__('create_medium')}}</span></h5>
                            </div>
                            <div class="col-12 col-sm-12">
                                <div class="form-group local-forms">
                                    <label>{{__('name')}} <span class="login-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="student-submit">
                                    <button type="submit" class="btn btn-primary">{{__('submit')}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-8">
            <div class="card card-table">
                <div class="card-body">
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="page-title">{{__('medium_list')}}</h5>
                            </div>
                        </div>
                    </div>
                    @php
                        $url = route('medium.show', [1]);
                        $columns = [
                            trans('No')=>['data-field'=>'no'],
                            trans('id')=>['data-field'=>'id','data-visible'=>false],
                            trans('name')=>['data-field'=>'name','data-sortable'=>true],
                            trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                            trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                        ];
                        $actionColumn = [
                            'editButton'=>['url'=>url('medium')],
                            'deleteButton'=>['url'=>url('medium')],
                            'data-events'=>'mediumEvents'
                        ];
                    @endphp
                    <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn></x-bootstrap-table>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" id="editModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{__('edit_medium')}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="edit-form" class="pt-3 medium-edit-form" action="{{ url('medium') }}">
                        <input type="hidden" name="id" id="id">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">{{__('name')}} <span class="text-danger">*</span></label>
                                {!! Form::text('name',null,array('required','class'=>'form-control','id'=>'name','placeholder'=>__('name'))) !!}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('close')}}</button>
                            <input class="btn btn-primary" type="submit" value={{ __('edit') }} />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
