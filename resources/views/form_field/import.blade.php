@extends('layout.master')

@section('title')
    {{ __('Form Fields') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Import Form Fields') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Admin Form Fields') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = route('form-fields.import.show');
                                    $columns = [
                                        trans('Rank')=>['data-field'=>'rank'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                        trans('name')=>['data-field'=>'name'],
                                        trans('Type')=>["data-field"=>"type", "data-sortable"=>true],
                                        trans('Required')=>["data-field"=>"is_required", "data-sortable"=>true,"data-formatter"=>"badgeFormatter"],
                                        trans('Default Values')=>["data-field"=>"default_values", "data-sortable"=>true],
                                        trans('Other')=>["data-field"=>"other", "data-sortable"=>true, "data-visible"=>false],
                                        trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                        trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                                    ];
                                    $actionColumn = [
                                        'editButton'=>false,
                                        'deleteButton'=>false,
                                        'customButton'=>[
                                            ['iconClass'=>'fa fa-download','title'=>'Import Form Field','customClass'=>'import'],
                                        ],
                                        'data-events'=>'importFormFieldEvents'
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn pageList="[All]" sortName="rank" sortOrder="ASC"></x-bootstrap-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
