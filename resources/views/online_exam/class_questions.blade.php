@extends('layout.master')

@section('title')
    {{ __('manage').' '.__('online').' '.__('exam').' '.__('questions') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('add_online_exam_questions') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form class="pt-3 mt-6" id="create-online-exam-questions-form" method="POST" action="{{ route('online-exam-question.store') }}" novalidate>
                            <div class="row">
                                <div class="form-group col-md-3 col-sm-12 local-forms">
                                    <label>{{ __('class') }} <span class="text-danger">*</span></label>
                                    <select required name="class_id" class="form-control select2 online-exam-class-id" style="width:100%;" tabindex="-1" aria-hidden="true" id="class_id">
                                        <option value="">--- {{ __('select') . ' ' . __('class') }} ---</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->class->id }}">{{ $class->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-sm-12 local-forms">
                                    <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                    <select required name="subject_id" class="form-control select2 online-exam-subject-id" style="width:100%;" tabindex="-1" aria-hidden="true" id="subject_id">
                                        <option value="">--- {{ __('select') . ' ' . __('subject') }} ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="badge-soft-primary p-4">
                                <div class="form-group">
                                    <label>{{ __('question_type') }} <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="question_type" class="question_type" value="0" checked>
                                                {{ __('simple_question') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="question_type" class="question_type" value="1">
                                                {{ __('equation_based') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div id="simple-question">
                                    <div class="form-group">
                                        <label>{{ __('question') }} <span class="text-danger">*</span></label>
                                        {!! Form::textarea('question', null, ['placeholder' => __('enter').' '.__('question'), 'class' => 'form-control','rows'=>4]) !!}
                                    </div>
                                    <div class="row option-container">
                                        <div class="form-group col-md-6">
                                            <label>{{ __('option') }} <span class="option-number">1</span>
                                                <span class="text-danger">*</span></label>
                                            <input type="text" name="option[1]" placeholder="{{ __('enter').' '.__('option') }}" class="form-control add-question-option"/>
                                        </div>
                                        <div class="form-group col-md-6 option-template">
                                            <label>{{ __('option') }} <span class="option-number">2</span>
                                                <span class="text-danger">*</span></label>
                                            <input type="text" name="option[2]" placeholder="{{ __('enter').' '.__('option') }}" class="form-control add-question-option"/>
                                            <div class="remove-option-content"></div>
                                        </div>
                                    </div>
                                    <div class="add_button">
                                        <button class="btn btn-dark" type="button" id="add-new-option">
                                            <i class="fa fa-plus-circle mr-2" aria-hidden="true"></i> {{__('add_option')}}
                                        </button>
                                    </div>
                                </div>
                                <div id="equation-question" style="display: none">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label>{{ __('question') }} <span class="text-danger">*</span></label>
                                        <textarea class="editor_question" name="equestion" required placeholder="{{__('enter').' '.__('question')}}"></textarea>
                                    </div>
                                    <div class="row equation-option-container p-4">
                                        <div class="form-group col-md-6">
                                            <label>{{ __('option') }} <span class="option-number">1</span>
                                                <span class="text-danger">*</span></label>
                                            <textarea class="editor_options" name="eoption[1]" required placeholder="{{__('enter').' '.__('option')}}"></textarea>
                                        </div>
                                        <div class="form-group col-md-6 quation-option-template">
                                            <label>{{ __('option') }} <span class="equation-option-number">2</span>
                                                <span class="text-danger">*</span></label>
                                            <textarea class="editor_options" name="eoption[2]" required placeholder="{{__('enter').' '.__('option')}}"></textarea>
                                            <div class="remove-equation-option-content"></div>
                                        </div>
                                    </div>
                                    <div class="add_button_equations">
                                        <button class="btn btn-dark" type="button" id="add-new-eqation-option">
                                            <i class="fa fa-plus-circle mr-2" aria-hidden="true"></i> {{__('add_option')}}
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="form-group col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('answer') }} <span class="text-danger">*</span></label>
                                            <select multiple required name="answer[]" id="answer_select" class="form-control js-example-basic-single select2-hidden-accessible" style="width:100%;" tabindex="-1" aria-hidden="true">
                                                <option value="1">{{__('option')}} 1</option>
                                                <option value="2">{{__('option')}} 2</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>{{ __('image') }}</label>
                                        <input type="file" name="image" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group p-1">
                                    <label>{{ __('note') }}</label>
                                    <input type="text" name="note" class="form-control">
                                </div>
                            </div>
                            <input class="btn btn-primary mt-4" id="new-question-add" type="submit" value={{__('submit')}}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('online'). ' ' . __('exam').' '.__('question') }}
                        </h4>
                        <div id="toolbar" class="row mt-4">
                            <div class="form-group col-sm-12 col-md-3 local-forms">
                                <label>{{ __('class') }}</label>
                                <select name="class_id" id="filter_class_id" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">{{ __('all') }}</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-12 col-md-3 local-forms">
                                <label>{{ __('subject') }}</label>
                                <select name="subject_id" id="filter_subject_id" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">{{ __('all') }}</option>
                                    @foreach ($all_subjects as $subject)
                                        <option value="{{ $subject->id }}">
                                            {{ $subject->name }} - {{ $subject->type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @php
                            $url = route('online-exam-question.show', 1);
                            $table_id = 'table_list';
                            $columns = [
                                trans('No') => ['data-field' => 'no'],
                                trans('id') => ['data-field' => 'online_exam_question_id', 'data-visible' => false],
                                trans('class') => ['data-field' => 'class_name', 'data-sortable' => false],
                                trans('subject') => ['data-field' => 'subject_name', 'data-sortable' => false],
                                trans('question') => ['data-field' => 'question', 'data-sortable' => false],
                                trans('option') => ['data-field' => 'exam_key','data-formatter' => 'optionsFormatter', 'data-sortable' => false],
                                trans('answer') => ['data-field' => 'duration','data-formatter' => 'answersFormatter', 'data-sortable' => false],
                                trans('image') => ['data-field' => 'start_date','data-formatter' => 'imageFormatter', 'data-sortable' => false],
                                
                            ];
                            $actionColumn = [
                                'editButton' => ['url' => url('online-exam-question')],
                                'deleteButton' => ['url' => url('online-exam-question')],
                                'data-events' => 'onlineExamQuestionsEvents',
                            ];
                        @endphp
                        <x-bootstrap-table :url=$url :table_id=$table_id :columns=$columns :actionColumn=$actionColumn queryParams="onlineExamQuestionsQueryParams"></x-bootstrap-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- model --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{__('edit').' '.__('online').' '.__('exam').' '.__('question')}}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="edit-question-form" class="pt-3" action="{{ url('online-exam-question') }}" novalidate>
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="modal-body">
                        <div class="row">

                            <div class="form-group col-md-3 col-sm-12 local-forms">
                                <label>{{ __('class') }} <span class="text-danger">*</span></label>
                                <select required name="edit_class_id" id="edit-online-exam-class-id" class="form-control select2 online-exam-class-id" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">--- {{ __('select') . ' ' . __('class') }} ---</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->class->id }}">{{ $class->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3 col-sm-12 local-forms">
                                <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                <select required name="edit_subject_id" id="edit-online-exam-subject-id" class="form-control select2 online-exam-subject-id" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">--- {{ __('select') . ' ' . __('subject') }} ---</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="edit_question_type" class="edit_question_type">
                        <div id="edit-simple-question" class="bg-light p-3" style="display: none">
                            <div class="form-group">
                                <label>{{ __('question') }} <span class="text-danger">*</span></label>
                                {!! Form::textarea('edit_question', null, ['placeholder' => __('enter').' '.__('question'), 'class' => 'form-control edit-question','rows'=>4]) !!}
                            </div>
                            <div class="row edit_option_container"></div>
                            <div class="add_button">
                                <button class="btn btn-dark add-new-edit-option" type="button">
                                    <i class="fa fa-plus-circle mr-2" aria-hidden="true"></i> {{__('add_option')}}
                                </button>
                            </div>
                        </div>
                        <div id="edit-equation-question" class="bg-light p-3" style="display: none">
                            <div class="form-group col-md-12 col-sm-12">
                                <label>{{ __('question') }} <span class="text-danger">*</span></label>
                                <textarea class="editor_question" name="edit_equestion" required placeholder="{{__('enter').' '.__('question')}}"></textarea>
                            </div>
                            <div class="row edit_eoption_container p-4"></div>
                            <div class="add_button_equations p-4">
                                <button class="btn btn-dark add-new-edit-eoption" type="button">
                                    <i class="fa fa-plus-circle mr-2" aria-hidden="true"></i> {{__('add_option')}}
                                </button>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="form-group col-md-6">
                                <div class="form-group" id="ans-menu">
                                    <label>{{ __('answer') }} <span class="text-danger">*</span></label>
                                    <select multiple data-dropdown-parent="#ans-menu" required name="edit_answer[]" class="edit_answer_select form-control js-example-basic-single select2-hidden-accessible" style="width:100%;" data-allow-clear="true" tabindex="-1" aria-hidden="true"></select>
                                </div>
                                <div class="answers_db"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{ __('image') }}</label>
                                <input type="file" name="edit_image" class="form-control"/>
                                <div style="width: 60px">
                                    <img src="" id="image_preview" class="w-100" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group p-1">
                            <label>{{ __('note') }}</label>
                            <input type="text" name="edit_note" class="form-control edit_note">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                        <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        // window.onload = createCkeditor();
    </script>
@endsection
