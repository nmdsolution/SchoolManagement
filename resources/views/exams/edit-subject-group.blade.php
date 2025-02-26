@extends('layout.master')

@section('title')
    {{ __('Exam Result Subject Group') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Result Subject Group') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h5>{{ __('Class') }} : {{$class->name}}</h5>
                        <small class="text-danger">* {{ __('Drag and Drop Subject to Add / Remove in Subject Group') }}</small>
                        <div class="row mt-3">
                            <div class="col-md-6 ">
                                <h5>{{ __('Subject Groups') }}</h5>
                                @foreach($subjectGroups as $row)
                                    <div class="droppable-box mb-3">
                                        <h6>{{$row->name}}</h6>
                                        <ul class="subject-group-list connectedSortable mb-0" data-subject-group-id="{{$row->id}}">
                                            @foreach($row->subjects as $key=>$subject)
                                                <li class="ui-state-default rounded-3" data-subject-id="{{$subject->id}}">{{$subject->name}}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-6 ">
                                <h5>{{ __('Subjects') }}</h5>
                                <ul class="subject-list connectedSortable droppable-box">
                                    @foreach($subjects as $row)
                                        <li class="ui-state-default rounded-3" data-subject-id="{{$row->id}}">{{$row->name}}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-2">
                                <a href="{{route('result-subject-group.index')}}" class="btn btn-primary">Submit</a>
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
        console.log("{{$id}}");
        $(function () {
            $(".subject-group-list").sortable({
                connectWith: ".connectedSortable",
                receive: function (event, ui) {
                    // This method will be called when Subject is added to Group
                    let exam_result_group_id = $(event.target).data('subject-group-id');
                    let subject_id = $(ui.item).data('subject-id');
                    let formData = new FormData();
                    formData.append('subject_id', subject_id);
                    formData.append('exam_result_group_id', exam_result_group_id);
                    formData.append('class_id', {{$id}});
                    formData.append('_method', 'PUT');
                    ajaxRequest('POST', baseUrl + '/exam/result-subject-group/subject', formData, null, function () {
                        showSuccessToast("Subject is added to the Group");
                    });

                },
            }).disableSelection();

            $(".subject-list").sortable({
                connectWith: ".connectedSortable",
                receive: function (event, ui) {
                    // This method will be called when Subject is deleted from Group
                    let subject_id = $(ui.item).data('subject-id');
                    let formData = new FormData();
                    formData.append('subject_id', subject_id);
                    formData.append('class_id', {{$id}});
                    formData.append('_method', 'DELETE');
                    ajaxRequest('POST', baseUrl + '/exam/result-subject-group/subject', formData, null, function () {
                        showWarningToast("Subject is Removed from the Group");

                    });
                },
            }).disableSelection();
        });
    </script>
@endsection
