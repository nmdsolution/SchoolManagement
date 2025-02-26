@extends('layout.master')

@section('title')
    {{ __('class') . ' ' . __('subject') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Class Subjects') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3 class-edit-form" id="class-subject-form" action="{{ route('class.subject.update',$class->id) }}" novalidate="novalidate">
                            <input type="hidden" name="class_id" id="class_id" value="{{$class->id}}"/>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>{{ __('class') }} <span class="text-danger"></span></label> : <b>{{$class->full_name}}</b>
                                </div>

                                <h5 title="Core Subjects are the Compulsory Subject." class="mb-3">
                                    {{ __('core_subject') }}
                                    <span class="fa fa-info-circle pl-2"></span>
                                </h5>
                                {{-- Template for old core subject --}}
                                <div id="all-core-subjects">
                                    @if(count($class->coreSubject)==0)
                                        {{--Set the Default values in this array so that foreach loop atleast runs once and option will be visible if no data exists--}}
                                        @php
                                            $class->coreSubject=[(object)[
                                                'id'=>'',
                                                'subject_id'=>'',
                                                'weightage'=>''
                                            ]];
                                        @endphp
                                    @endif

                                    @foreach($class->coreSubject as $key=>$row)
                                        <div class="row core-subject-div">
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <input type="hidden" name="core_subject[{{$key}}][class_subject_id]" class="class-subject-id form-control" value="{{$row->id}}">
                                                    <select name="core_subject[{{$key}}][subject_id]" class="core-subject-id form-control" required="required">
                                                        <option value="" hidden="">{{ __('select_subject') }}</option>
                                                        @foreach ($subjects as $subject)
                                                            <option value="{{ $subject->id }}" data-medium-id="{{ $subject->medium_id }}" {{$row->subject_id===$subject->id?"selected":""}}>
                                                                {{ $subject->name }} - {{$subject->type}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <input type="number" name="core_subject[{{$key}}][weightage]" class="form-control weightage" required="required" min="1" placeholder="Coef" value="{{$row->weightage}}">
                                                </div>
                                            </div>
                                            <div class="col-1 pl-0">
                                                <button type="button" class="btn btn-icon btn-inverse-danger remove-core-subject btn-danger" title="Remove Core Subject" data-id="{{$row->id}}">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div>
                                    <div class="form-group pl-0 mt-4">
                                        <button type="button" class="col-md-3 btn btn-inverse-success add-new-core-subject btn-primary">
                                            {{ __('core_subject') }} <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <hr>

                                <h4 class="mb-4" title="Elective Subjects are the subjects where student have the choice to select the subject from the given subjects.">
                                    {{ __('elective_subject') }} <span class="fa fa-info-circle pl-2"></span>
                                </h4>

                                <div id="elective-subject-group-div">

                                    @if(count($class->electiveSubjectGroup)==0)
                                        {{--Set the Default values in this array so that foreach loop atleast runs once and option will be visible if no data exists--}}
                                        @php
                                            $class->electiveSubjectGroup=[(object)[
                                                'id'=>'',
                                                'name'=>'',
                                                'total_selectable_subjects'=>'',
                                                'electiveSubjects'=>[(object)[
                                                   'id'=>'',
                                                   'subject_id'=>'',
                                                   'weightage'=>'',
                                                   'total_selectable_subjects'=>''
                                                ],
                                                (object)[
                                                   'id'=>'',
                                                   'subject_id'=>'',
                                                   'weightage'=>'',
                                                   'total_selectable_subjects'=>''
                                                ]]
                                            ]];
                                        @endphp
                                    @endif
                                    @foreach($class->electiveSubjectGroup as $groupKey=>$group)
                                        <div class="elective-subject-group">
                                            <input type="hidden" name="elective_subjects[{{$groupKey}}][subject_group_id]" class="edit-elective-subject-group-id form-control" value="{{$group->id}}"/>
                                            <div class="col d-flex align-items-center mb-2">
                                                <h5 class="mb-0 group-no me-2">{{ __('group') }} <span id="group-number">{{$groupKey+1}}</span></h5>
                                                <i class="fa fa-2x fa-times-circle text-left pl-1 pr-0  text-danger remove-elective-subject-group" data-id="{{$group->id}}"></i>
                                            </div>
                                            @foreach($group->electiveSubjects as $subjectKey=>$electiveSubject)
                                                <div class="form-group row elective-subject">
                                                    <div class="col-5 align-items-end">
                                                        <input type="hidden" name="elective_subjects[{{$groupKey}}][class_subject_id][{{$subjectKey}}]" class="edit-elective-subject-class-id form-control" value="{{$electiveSubject->id}}"/>
                                                        <select name="elective_subjects[{{$groupKey}}][subject_id][{{$subjectKey}}]" class="form-control edit-elective-subject-name" required="required">
                                                            <option value="" hidden="">{{ __('select_subject') }}</option>
                                                            @foreach ($subjects as $subject)
                                                                <option value="{{ $subject->id }}" data-medium-id="{{ $subject->medium_id }}" {{$electiveSubject->subject_id===$subject->id?"selected":""}}>
                                                                    {{ $subject->name }} - {{$subject->type}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-5 align-items-end">
                                                        <input type="number" name="elective_subjects[{{$groupKey}}][weightage][{{$subjectKey}}]" class="form-control weightage" required="required" min="1" placeholder="Coef" value="{{$electiveSubject->weightage}}">
                                                        <div class="row justify-content-end">
                                                            <button type="button" class='btn w-auto fa fa-times-circle text-danger remove-elective-subject' data-id="{{$electiveSubject->id}}"></button>
                                                        </div>
                                                    </div>

                                                    <div class="col-1 d-flex justify-content-center align-items-baseline">
                                                        <span class='mt-3 or'>{{ __('or') }}</span>
                                                        <button type="button" class="btn btn-success btn-icon add-new-elective-subject ml-3" title="Add New Elective Subject" value="1" style="display: none">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="form-group row total-selectable-subjects">
                                                <div class="col-md-3 col-sm-12">
                                                    <label>{{ __('total_selectable_subjects') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input name="elective_subjects[{{$groupKey}}][total_selectable_subjects]" type="number" placeholder="{{ __('total_selectable_subjects') }}" class="form-control edit-total-selectable-subject" min="1" max="1" required value="{{$group->total_selectable_subjects}}"/>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                    @endforeach
                                </div>
                                <div>
                                    <div class="form-group pl-0 mt-4">
                                        <button type="button" class="col-md-3 btn add-elective-subject-group btn-primary">
                                            {{ __('Elective Subject Group') }} <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input class="btn btn-primary col-md-3 col-sm-12" type="submit" value={{ __('save') }} />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        hideLastOR();
        changeRemoveElectiveButtonState();
    </script>
@endsection