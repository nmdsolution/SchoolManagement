@extends('layout.master')

@section('title')
    {{ __('section') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage').' '.__('section') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create').' '.__('section') }}
                        </h4>
                        <form class="pt-3 section-create-form" id="create-form" action="{{ route('section.store') }}" method="POST" novalidate="novalidate">
                            <div class="col-12 col-sm-12 row">
                                <div class="form-group local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" type="text" placeholder="{{ __('name') }}" class="form-control" required/>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="student-submit">
                                    <input class="btn btn-primary" id="create-btn" type="submit" value={{ __('submit') }}>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list').' '.__('section') }}
                        </h4>
                        @php
                            $url = route('section.show', [1]);
                            $columns = [
                                trans('no')=>['data-field'=>'no'],
                                trans('id')=>['data-field'=>'id','data-visible'=>false],
                                trans('name')=>['data-field'=>'name'],
                                trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                            ];
                            $actionColumn = [
                                'editButton'=>['url'=>url('section')],
                                'deleteButton'=>['url'=>url('section')],
                                'data-events'=>'sectionEvents'
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn toolbar="false"></x-bootstrap-table>
                    </div>
                </div>
            </div>

            <div class="modal fade" tabindex="-1" id="editModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{__('edit').' '.__('section')}}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form class="pt-3 section-edit-form" id="edit-form" action="{{ url('section') }}" novalidate="novalidate">
                            <input type="hidden" name="edit_id" id="edit_id" value=""/>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-sm-6 col-md-12">
                                        <label>{{__('name')}} <span class="text-danger">*</span></label>
                                        <input name="name" id="edit_name" type="text" placeholder="{{__('name')}}" class="form-control" required/>
                                    </div>
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
    </div>
@endsection
