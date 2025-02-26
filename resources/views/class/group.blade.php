@extends('layout.master')

@section('title')
    {{ __('Class Group') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Class Group') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Create Group') }}
                        </h4>
                        <form class="create-form pt-3" id="formdata" data-success-function="customSuccess" action="{{ route('class-group.store') }}" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Group List') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $url = route('class-group.show',[1]);
                                    $columns = [
                                        trans('no')=>['data-field'=>'no'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                        trans('name ')=>['data-field'=>'name'],
                                        trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],
                                        trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],
                                    ];
                                    $actionColumn = [
                                        'editButton'=>['url'=>url('class-group')],
                                        'deleteButton'=>['url'=>url('class-group')],
                                        'data-events'=>'classGroupEvent'
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn toolbar="false"></x-bootstrap-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--            <div class="col-lg-12 grid-margin stretch-card">--}}
            {{--                <div class="card">--}}
            {{--                    <div class="card-body">--}}
            {{--                        <h4 class="card-title">--}}
            {{--                            {{ __('Assign Group to Class') }}--}}
            {{--                        </h4>--}}
            {{--                        <div class="row">--}}
            {{--                            <div class="col-12">--}}
            {{--                                @php--}}
            {{--                                    $url = route('exam.subject-group.assigned-list');--}}
            {{--                                    $columns = [--}}
            {{--                                        trans('no')=>['data-field'=>'no'],--}}
            {{--                                        trans('id')=>['data-field'=>'id','data-visible'=>false],--}}
            {{--                                        trans('name ')=>['data-field'=>'name'],--}}
            {{--                                        trans('created_at')=>['data-field'=>'created_at','data-visible'=>false],--}}
            {{--                                        trans('updated_at')=>['data-field'=>'updated_at','data-visible'=>false],--}}
            {{--                                    ];--}}
            {{--                                    $actionColumn = [--}}
            {{--                                        'editButton'=>['url'=>url('class-group'),'redirection'=>true],--}}
            {{--                                        'deleteButton'=>false,--}}
            {{--                                    ];--}}
            {{--                                @endphp--}}
            {{--                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn></x-bootstrap-table>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body h-100">
                            {{--                        <h5>Class : {{$class->name}}</h5>--}}
                            <small class="text-danger">* {{ __('Drag and Drop Class to Add / Remove in Group') }}</small>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h5>{{ __('Groups') }}</h5>
                                    @foreach($groups as $row)
                                        <div class="droppable-box mb-3" style="min-height: 120px;">
                                            <h6>{{$row->name}}</h6>
                                            <ul class="group-list connectedSortable mb-0" data-group-id="{{$row->id}}" style="min-height: 120px;">
                                                @foreach($row->classes as $key=>$group)
                                                    <li class="ui-state-default rounded-3" data-class-id="{{$group->id}}">{{$group->full_name}}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-6 ">
                                    <h5>{{ __('Class') }}</h5>
                                    <ul class="class-list connectedSortable droppable-box">
                                        @foreach($classes as $row)
                                            <li class="ui-state-default rounded-3" data-class-id="{{$row->id}}">{{$row->full_name}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-2">
                                    <a href="{{route('class-group.index')}}" class="btn btn-primary">Submit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Edit Class Group') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="editform" action="{{ url('exam-sequnces') }}" novalidate="novalidate" data-success-function="customSuccess">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        {{-- <span>Exam Subject Group</span> --}}
                        <div class="row mt-3">

                            <div class="form-group col-sm-12 col-md-12 local-forms">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['id'=>'edit_name','required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
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
@section('script')
    <script>
        $(function () {
            $(".group-list").sortable({
                connectWith: ".connectedSortable",
                stop: function (e, ui) {
                    if ($(ui.item[0]).parent().hasClass('class-list')) {
                        if ($('.class-list').find("[data-class-id='" + $(ui.item[0]).data('class-id') + "']").length > 1) {
                            ui.item[0].remove();
                        }
                    }
                },
                receive: function (event, ui) {
                    // alert('Class is added to the Group');
                    // This method will be called when Class is added to Group
                    let group_id = $(event.target).data('group-id');
                    let class_id = $(ui.item).data('class-id');
                    let formData = new FormData();

                    formData.append('group_id', group_id);
                    formData.append('class_id', class_id);
                    formData.append('_method', 'PUT');
                    ajaxRequest('POST', baseUrl + '/class-group/add-class', formData);
                },
            }).disableSelection();

            $(".class-list").sortable({
                connectWith: ".connectedSortable",
                helper: function (e, ui) {
                    ui.clone().insertAfter(ui);
                    return ui.clone();
                },
                stop: function (e, ui) {
                    console.log(ui);
                    if ($(ui.item[0]).parent().hasClass('class-list')) {
                        ui.item[0].remove();
                    } else {
                        if ($(ui.item[0]).parent().find("[data-class-id='" + $(ui.item[0]).data('class-id') + "']").length > 1) {
                            ui.item[0].remove();
                        }
                    }
                },
                opacity: '.5',
                receive: function (event, ui) {
                    // This method will be called when Class is deleted from Group
                    let class_id = $(ui.item).data('class-id');
                    let group_id = $(ui.sender).data('group-id');
                    let formData = new FormData();
                    formData.append('group_id', group_id);
                    formData.append('class_id', class_id);
                    formData.append('_method', 'DELETE');
                    ajaxRequest('POST', baseUrl + '/class-group/remove-class', formData, null, function () {
                        ui.item[0].remove();
                    });
                },
            }).disableSelection();
        });

        function customSuccess() {
            window.location.reload();
        }
    </script>
@endsection