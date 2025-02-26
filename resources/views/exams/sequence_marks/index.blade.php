@extends('layout.master')

@section('title')
    {{ __('exam_marks') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('Sequence') . ' ' . __('exam_marks') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('Sequence') . ' ' . __('exam_marks') }}
                        </h4>

                        <div class="row mt-3">
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label for="">{{ __('Sequence') }}</label>

                                {!! Form::select('sequence_id', $sequences, null, [
                                    'class' => 'form-control select',
                                    'id' => 'sequence_id',
                                    'placeholder' => __('Select Sequence'),
                                ]) !!}

                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label for="">{{ __('class_section') }}</label>
                                <select required name="class_section_id" id="class_section_id" class="form-control select" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">{{ __('select_class_section') }}</option>
                                    @foreach($classSections as $classSection)
                                        <option value="{{$classSection->id}}">{{ $classSection->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-12 col-md-12 text-center">
                                <button type="button" id="search" class="btn btn-primary">{{ __('Search') }}</button>
                            </div>
                        </div>
                        <div class="show_student_list">
                            <table aria-describedby="mydesc" class='table student_table table_list' id='table_list'
                                   data-toggle="table" data-url="{{ url('sequence-exam-student') }}"
                                   data-click-to-select="true" data-side-pagination="server" data-pagination="true"
                                   data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true"
                                   data-show-refresh="true" data-fixed-columns="true" data-fixed-number="2"
                                   data-fixed-right-number="1" data-trim-on-search="false" data-mobile-responsive="true"
                                   data-sort-name="id" data-sort-order="desc" data-maintain-selected="true"
                                   data-export-types='["txt","excel"]' data-show-export="true"
                                   data-export-options='{ "fileName": "exam-result-list-<?= date(' d-m-y') ?>" ,"ignoreColumn":["operate"]}'
                                   data-query-params="sequenceExamMarksqueryParams" data-toolbar="#toolbar">
                                <thead>
                                <tr>
                                    <th scope="col" data-field="id" data-sortable="false" data-visible="false">{{ __('id') }}</th>
                                    <th scope="col" data-field="no" data-sortable="false">{{ __('no') }}</th>
                                    <th scope="col" data-field="student_name" data-sortable="false" data-formatter="examStudentNameFormatter">{{ __('name') }}</th>
                                    <th scope="col" data-field="total_marks" data-sortable="false">{{ __('total_marks') }}</th>
                                    <th scope="col" data-field="obtained_marks" data-sortable="false">{{ __('obtained_marks') }}</th>
                                    <th scope="col" data-field="avg_marks" data-sortable="false">{{ __('Average') }}</th>
                                    <th scope="col" data-field="operate" data-sortable="false" data-events="sequenceWiseMarksEvents">{{ __('action') }}</th>
                                </tr>
                                </thead>
                            </table>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            {{ __('edit') . ' ' . __('lesson') }}
                                        </h5>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form class="pt-3 edit-form" action="{{ url('sequence-exam-mark-update') }}" novalidate="novalidate">
                                        <input type="hidden" name="edit_id" id="edit_id" value=""/>
                                        <div class="modal-body">
                                            <h5>Student Name : <span id="student_name"></span></h5>
                                            <div id="subject-list"></div>

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
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('#search').on('click , input', function () {
            $('.student_table').bootstrapTable('refresh');
            updatePrintButtonHref();
        });
    </script>

@endsection
