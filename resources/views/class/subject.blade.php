@extends('layout.master')

@section('title')
    {{ __('class') . ' ' . __('subject') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('class') . ' ' . __('subject') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        @php
                            $url = route('class.subject.list');
                            $columns = [
                                trans('no')=>['data-field'=>'no'],
                                trans('id')=>['data-field'=>'id','data-visible'=>false],
                                trans('name')=>['data-field'=>'name'],
                                trans('section')=>['data-field'=>'section_names'],
                                trans('core_subject')=>['data-field'=>'core_subject','data-formatter'=>"coreSubjectFormatter"],
                                trans('elective_subject')=>['data-field'=>'elective_subject','data-formatter'=>'electiveSubjectFormatter'],
                                trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                            ];
                            $actionColumn = [
                                'editButton'=>false,
                                'deleteButton'=>false,
                                'data-events'=>'classSubjectEvents',
                                'customButton'=>[
                                        ['iconClass'=>'feather-edit','url'=>url('class/subject'),'title'=>'Edit Class Subjects','customClass'=>'edit-class-subject'],
                                ],
                            ];
                        @endphp
                        <div id="toolbar"></div>
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="AssignclassQueryParams" toolbar="false"></x-bootstrap-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
