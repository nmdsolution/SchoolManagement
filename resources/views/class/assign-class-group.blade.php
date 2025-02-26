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
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        {{--                        <h5>Class : {{$class->name}}</h5>--}}
                        <small class="text-danger">* Drag and Drop Class to Add / Remove in Group</small>
                        <div class="row mt-3">
                            <div class="col-md-6 ">
                                <h5>Groups</h5>
                                @foreach($groups as $row)
                                    <div class="droppable-box mb-3" style="min-height: 120px;">
                                        <h6>{{$row->name}}</h6>
                                        <ul class="group-list connectedSortable mb-0" data-group-id="{{$row->id}}" style="min-height: 120px;">
                                            @foreach($row->classes as $key=>$class)
                                                <li class="ui-state-default rounded-3" data-class-id="{{$class->id}}">{{$class->name}}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-6 ">
                                <h5>Class</h5>
                                <ul class="class-list connectedSortable droppable-box">
                                    @foreach($classes as $row)
                                        <li class="ui-state-default rounded-3" data-class-id="{{$row->id}}">{{$row->name}}</li>
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
@endsection
@section('script')
    <script>
        $(function () {
            let dropback =
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
                        ajaxRequest('POST', baseUrl + '/class-group/add-class', formData, null, function () {
                            showSuccessToast("Class is added to the Group");
                        });
                    },
                }).disableSelection();

            $(".class-list").sortable({
                connectWith: ".connectedSortable",
                helper: function (e, ui) {
                    ui.clone().insertAfter(ui);
                    return ui.clone();
                },
                stop: function (e, ui) {
                    if ($(ui.item[0]).parent().hasClass('class-list')) {
                        ui.item[0].remove();
                    } else {
                        if ($('.group-list').find("[data-class-id='" + $(ui.item[0]).data('class-id') + "']").length > 1) {
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
                        showWarningToast("Class is Removed from the Group");
                        ui.item[0].remove();
                    });
                },
            }).disableSelection();
        });
    </script>
@endsection
