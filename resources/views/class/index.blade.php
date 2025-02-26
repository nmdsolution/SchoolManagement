@extends('layout.master')

@section('title')
    {{ __('class') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('class') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('class') }}
                        </h4>
                        <form class="pt-3 class-create-form" id="create-form" action="{{ route('class.store') }}" method="POST" novalidate="novalidate">
                            <div class="row">
                                <div class="form-group">
                                    <label>{{ __('Sector') }} <span class="text-danger">*</span></label>
                                    <div class="col-12 d-flex row">
                                        @foreach ($mediums as $medium)
                                            <div class="form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input" name="medium_id" id="medium_{{ $medium->id }}" value="{{ $medium->id }}">
                                                    {{ $medium->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group local-forms">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" type="text" placeholder="{{ __('name') }}" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('shifts') }} <span class="text-info">(optional)</span></label>
                                    <select name="shift_id" id="shift_id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">{{__('Please')}}  {{__('select')}}</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{$shift->id}}">{{$shift->title}}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label>{{ __('section') }} <span class="text-danger">*</span></label>
                                    @foreach ($sections as $section)
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" name="section_id[]" value="{{ $section->id }}">{{ $section->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @if(count($streams))
                                <div class="form-group">
                                    <label>{{ __('stream') }} <span class="text-info">(optional)</span></label>
                                    @foreach ($streams as $stream)
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" name="stream_id[]"
                                                       value="{{ $stream->id }}">{{ $stream->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <input class="btn btn-primary" id="create-btn" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('class') }}
                        </h4>
                        @php
                            $url = url('class-list');
                            $columns = [
                                trans('no') => ['data-field' => 'no'],
                                trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                trans('name') => ['data-field' => 'name'],
                                trans('section') => ['data-field' => 'section_names'],
                                trans('shifts') => ['data-field' => 'shift_name'],
                                trans('stream') => ['data-field' => 'stream_name'],
                                trans('created_at') => ['data-field' => 'created_at', 'data-visible' => false],
                                trans('updated_at') => ['data-field' => 'updated_at', 'data-visible' => false],
                            ];

                            $actionColumn = [
                                'editButton' => ['url' => url('class')],
                                'deleteButton' => ['url' => url('class')],
                                'data-events' => 'classEvents',
                                'customButton'=>[
                                    ['iconClass'=>'feather-refresh-ccw',
                                    'url'=>url('class-section/rename'),
                                    'title'=>'Move Section to Another Section',
                                    'customClass'=>'rename-class-section'],
                                ],
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn queryParams="classQueryParams" toolbar="false"></x-bootstrap-table>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="customModal" role="dialog" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteStudentModalLabel">{{ trans('confirm_student_deletion') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3">{{ __("action_cannot_be_undone") }}</p>
                            <div class="mb-3">
                                <label for="confirmStudentName" class="form-label">Student Name: <span id="selected_student"></span></label>
                                <input type="text" class="form-control" id="confirmStudentName" placeholder="{{ __("copy_name") }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="deleteStudentBtn" disabled>Delete Student</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('edit') . ' ' . __('class') }}</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 class-edit-form" id="edit-form" action="{{ url('class') }}" novalidate="novalidate">
                            <div class="modal-body">
                                <input type="hidden" name="edit_id" id="edit_id" value=""/>
                                <div class="form-group">
                                    <label>{{ __('Sector') }} <span class="text-danger">*</span></label>
                                    <div class="ml-1">
                                        @foreach ($mediums as $medium)
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" class="form-check-input edit" name="medium_id"
                                                           id="edit_medium_{{ $medium->id }}"
                                                           value="{{ $medium->id }}"> {{ $medium->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                    <input name="name" id="edit_name" type="text" placeholder="{{ __('name') }}" class="form-control"/>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('shifts') }} <span class="text-info">(optional)</span></label>
                                        <select name="shift_id" id="edit_shift_id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                            <option value="">{{__('Please')}}  {{__('select')}}</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{$shift->id}}">{{$shift->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-12">
                                        <label>{{ __('stream') }}<span class="text-info">(optional)</span></label>
                                        <select name="stream_id" id="edit_stream_id" class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                            <option value=" ">{{__('Please')}}  {{__('select')}}</option>
                                            @foreach($streams as $stream)
                                                <option value="{{$stream->id}}">{{$stream->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('section') }} <span class="text-danger">*</span></label>
                                    @foreach ($sections as $section)
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input edit" name="section_id[]" id="edit_section_id" value="{{ $section->id }}">{{ $section->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                                <input class="btn btn-primary" type="submit" value={{ __('edit') }} />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">

        $(document).ready(function () {

            $('.rename-class-section').on('click', function () {
                console.log('button clicked');
            })
        });

    </script>
@endsection
