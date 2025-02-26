@extends('layout.master')

@section('title')
    {{ __('assign_new_student_class') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('assign_new_student_class') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('students.assign-class.store') }}" class="assign_student_class" id="formdata">
                            @csrf
                            <div class="row" id="toolbar">
                                <div class="form-group col-sm-12 col-md-3 local-forms">
                                    <label for="">{{ __('from_class_section') }}</label>
                                    <select name="from_class_section_id" class="form-control select2"
                                            id="from_class_section_id" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @foreach ($class_sections as $class_section)
                                            <option value="{{ $class_section->id }}">
                                                {{ $class_section->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-3 local-forms">
                                    <label for="">{{ __('to_class_section') }}</label>
                                    <select name="to_class_section_id" class="form-control select2"
                                            style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @foreach ($class_sections as $class_section)
                                            <option value="{{ $class_section->id }}">
                                                {{ $class_section->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <textarea readonly hidden name="selected_id" id="all_id"></textarea>
                            </div>
                            <div class="assign_student_show">
                                @php
                                    $url = route('students.transfer-student-list');
                                    $columns = [
                                        trans('no')=>['data-field'=>'no'],
                                        trans('id')=>['data-field'=>'id','data-visible'=>false],
                                        "#"=>['data-field'=>'chk'],
                                        trans('user_id')=>['data-field'=>'user_id','data-visible'=>false],
                                        trans('first_name')=>['data-field'=>'first_name','data-sortable'=>true],
                                        trans('last_name')=>['data-field'=>'last_name','data-sortable'=>true],
                                        trans('image')=>['data-field'=>'image','data-sortable'=>true,'data-formatter'=>"imageFormatter"],
                                        trans('class') . ' ' . trans('section')=>['data-field'=>'class_section_name','data-sortable'=>true],
                                        trans('admission_no')=>['data-field'=>'admission_no','data-sortable'=>true],
                                        trans('roll_no')=>['data-field'=>'roll_number','data-sortable'=>true]
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns queryParams="assignClassqueryParams" data-pagination="false"></x-bootstrap-table>
                            </div>

                            <input class="btn btn-primary" id="btn_assign" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script type="text/javascript">
        selected_student = [];

        $('#btn_assign').hide();

        $(document).on('click', '.assign_student', function (e) {
            if (this.checked === true) {
                selected_student.push($(this).val());

            } else {

                var index = selected_student.indexOf($(this).val());
                if (index > -1) {
                    selected_student.splice(index, 1);
                }
            }
            $('#all_id').val(selected_student);
            if ($('#all_id').val() !== '') {
                $('#btn_assign').show();
            } else {
                $('#btn_assign').hide();
            }
        });
    </script>
@endsection
