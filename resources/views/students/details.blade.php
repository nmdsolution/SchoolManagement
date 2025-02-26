@extends('layout.master')

@section('title')
    {{ __('students') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('students') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list')  }}
                        </h4>
                        <div id="toolbar">
                            <div class="row">
                                <div class="col-sm-12 col-md-3 mb-4 local-forms">

                                    @if (!Auth::user()->teacher)
                                        <label for="">{{ __('class_section') }}</label>
                                        <select name="filter_class_section_id" id="filter_class_section_id" class="form-control">
                                            <option value="">{{ __('select_class_section') }}</option>
                                            @foreach ($class_section as $class)
                                                <option value={{ $class->id }}>
                                                    {{ $class->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>

                                <div class="col-sm-12 col-md-3 mb-4 local-forms">

                                    @if (!Auth::user()->teacher)
                                        <label for="">{{ __('student_status') }}</label>
                                        <select name="filter_by_student_status" id="filter_by_student_status" class="form-control">
                                                <option value={{ 1 }}>
                                                    {{  __("continuing_student") }}
                                                </option>
                                                <option value={{ 0 }}>
                                                    {{  __("dismissed_student") }}
                                                </option>
                                        </select>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center">
                                    <div class="card text-white">
                                        <div class="card-body">
                                            <div class="d-flex gap-4 align-items-center">
                                                <i class="fas text-dark fa-male fa-2x mr-3"></i>
                                                <div>
                                                    <h5 class="card-title text-dark">{{ trans("boys") }}</h5>
                                                    <p class="card-text text-dark" id="total-girls"><span id="boys-count"></span> {{ trans("boys") }} </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card text-white">
                                        <div class="card-body">
                                            <div class="d-flex gap-4 align-items-center">
                                                <i class="fas fa-female text-dark fa-2x mr-3"></i>
                                                <div>
                                                    <h5 class="card-title">{{ trans("Girls") }}</h5>
                                                    <p class="card-text text-dark" id="total-girls"><span id="girls-count"></span> {{ trans("girls") }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">

                            <textarea class="d-none"  id="selected_students"></textarea>

                            <div class="col-12">
                                @php
                                    $url = url('students-list');
                                    $columns = [
                                        '' => ['data-field' => 'state', 'data-checkbox' => true],
                                        trans('no') => ['data-field' => 'no'],
                                        trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                        trans('user_id') => ['data-field' => 'user_id', 'data-sortable' => false, 'data-visible' => false],
                                        trans('student_name') => ['data-field' => 'first_name', 'data-sortable' => false],
                                        trans('gender') => ['data-field' => 'gender', 'data-sortable' => false],
                                        trans('dob') => ['data-field' => 'dob', 'data-sortable' => false],
                                        trans('born_at') => ['data-field' => 'born_at', 'data-sortable' => false],
                                        trans('status') => ['data-field' => 'status', 'data-sortable' => false],
                                        trans('nationality') => ['data-field' => 'nationality', 'data-visible' => false],
                                        trans('image')=>["data-field"=>"image", "data-sortable"=>false, "data-formatter"=>"imageFormatter"],
                                        trans('class') . ' ' . trans('section') . ' ' . trans('id') => ['data-field' => 'class_section_id', 'data-sortable' => false, 'data-visible' => false],
                                        trans('class') . ' ' . trans('section') => ['data-field' => 'class_section_name', 'data-sortable' => false],
                                        trans('Matricule') => ['data-field' => 'admission_no', 'data-sortable' => false],
                                        trans('MINISEC MATRICULE') => ['data-field' => 'minisec_matricule',
                                        'data-sortable'
                                         => true],
                                        trans('roll_no') => ['data-field' => 'roll_number', 'data-sortable' => false, 'data-visible' => false],
                                        trans('admission_date') => ['data-field' => 'admission_date', 'data-sortable' => false, 'data-visible' => false],
                                        trans('father') . ' ' . trans('name') => ['data-field' => 'father_first_name', 'data-sortable' => false, 'data-visible' => false],
                                        trans('father') . ' ' . trans('mobile') => ['data-field' => 'father_mobile', 'data-sortable' => false, 'data-visible' => false],
                                        trans('father') . ' ' . trans('occupation') => ['data-field' => 'father_occupation', 'data-sortable' => false, 'data-visible' => false],
                                        trans('father') . ' ' . trans('image') => ['data-field' => 'father_image', 'data-sortable' => false, 'data-formatter' => 'imageFormatter', 'data-visible' => false],
                                        trans('mother') . ' ' . trans('name') => ['data-field' => 'mother_first_name', 'data-sortable' => false, 'data-visible' => false],
                                        trans('parents') . ' ' . trans('occupation') => ['data-field' => 'mother_occupation', 'data-sortable' => false, 'data-visible' => false],
                                        trans('mother') . ' ' . trans('image') => ['data-field' => 'mother_image', 'data-sortable' => false, 'data-formatter' => 'imageFormatter', 'data-visible' => false],
                                        trans('created_at') => ['data-field' => 'created_at', 'data-visible' => false],
                                        trans('updated_at') => ['data-field' => 'updated_at', 'data-visible' => false],
                                        trans('repeater') => ['data-field' => 'repeater'],
                                    ];
                                    $actionColumn = [
                                        'editButton' => ['url' => url('students')],
                                        'customModal' => ['url' => url('student/something')],
                                        'data-events' => 'studentEvents',
                                    ];
                                @endphp

                                <x-bootstrap-table :url=$url :columns=$columns data_response_handler="responseHandler" :actionColumn=$actionColumn queryParams="StudentDetailQueryParams"  sortOrder="asc"></x-bootstrap-table>

                                <button class="btn btn-primary" id="btn_assign" type="submit">{{ __('delete_students') }}</button>

                            </div>
                        </div>
                    </div>
                </div>
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
                        <label for="confirmStudentName" class="form-label">{{ trans('student_name') }} : <span id="selected_student"></span></label>
                        <input type="text" class="form-control" id="confirmStudentName" placeholder="{{ __("copy_name") }}" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="deleteStudentBtn" disabled>{{ trans("delete_student") }}</button>
                </div>
            </div>
        </div>
    </div>

    @can('student-edit')
        <div class="modal fade" id="editModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">{{ __('edit') . ' ' . __('students') }}</h4><br>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fa fa-close"></i></span>
                        </button>
                    </div>
                    <form id="edit-form" class="edit-student-registration-form" novalidate="novalidate" action="{{ url('students') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="edit_id" id="edit_id">

                            <div class="row">
                                <div class="form-group col-sm-12 col-md-8">
                                    <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('first_name', null, [
                                        'placeholder' => __('first_name'),
                                        'class' => 'form-control',
                                        'id' => 'edit_first_name',
                                    ]) !!}

                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('dob', null, [
                                        'placeholder' => __('dob'),
                                        'class' => 'dob-date form-control',
                                        'id' => 'edit_dob',
                                        'autocomplete'=>'off'
                                    ]) !!}
                                    <span class="input-group-addon input-group-append">
                                </span>
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('gender', 'male', ['id' => 'male']) !!}
                                                {{ __('male') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('gender', 'female', ['id' => 'female']) !!}
                                                {{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('image') }} </label>
                                    {!! Form::file('image',  [
                                        'placeholder' => __('image'),
                                        'class' => 'form-control',
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __("minisec_matricule") }}</label>
                                    <input id="edit_minisec_matricule" type="text" name="minisec_matricule" placeholder="{{ __
                                    ("minisec_matricule") }}" class="form-control">
                                </div>

                                @php
                                $statuses = collect(["Not applicable", "Handicap", "Refugee", "Orphan"])
                                    ->mapWithKeys(function ($status) {
                                        return [$status => __($status)];
                                    })->toArray();                                
                                @endphp

                                <div class="form-group col-sm-12 col-md-4  local-forms">
                                    <label>{{ __('status') }} <span
                                                class="text-danger">*</span></label>

                                    {!! Form::select('edit_status[]', $statuses, null, ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                                </div>

                                @foreach ($formFields as $row)
                                    @if ($row->type === 'text' || $row->type === 'number')
                                        <div class="form-group col-sm-12 col-md-4 local-forms">
                                            <label>{{ str_replace('_', ' ', ucfirst($row->name)) }}
                                                {!! $row->is_required ? ' <span class="text-danger">*</span></label>' : '' !!}</label>
                                            <input type="{{ $row->type }}" name="{{ $row->name }}" id="{{ $row->name }}" placeholder="{{ str_replace('_', ' ', ucfirst($row->name)) }}" class="form-control edit_text_number"
                                                    {{ $row->is_required === 1 ? 'required' : '' }}>
                                        </div>
                                    @endif

                                    @if ($row->type === 'dropdown')
                                        <div class="form-group col-sm-12 col-md-4 local-forms">
                                            <label>{{ str_replace('_', ' ', ucfirst($row->name)) }}
                                                {!! $row->is_required ? ' <span class="text-danger">*</span></label>' : '' !!}</label>
                                            <select name="{{ $row->name }}" id="{{ $row->name }}" class="form-control edit_dropdown">
                                                @foreach (json_decode($row->default_values) as $options)
                                                    <option value="{{ $options }}">{{ ucfirst($options) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    @if ($row->type === 'radio')
                                        <div class="form-group col-sm-12 col-md-4">
                                            <label>{{ str_replace('_', ' ', ucfirst($row->name)) }}
                                                {!! $row->is_required ? ' <span class="text-danger">*</span></label>' : '' !!}</label>
                                            <br>
                                            <div class="d-flex">
                                                @foreach (json_decode($row->default_values) as $options)
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label">
                                                            <input type="radio" id="{{ $options }}" name="{{ $row->name }}" value="{{ $options }}">
                                                            {{ ucfirst($options) }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($row->type === 'checkbox')
                                        <div class="form-group col-sm-12 col-md-4">
                                            <label class="col-form-label col-md-2">{{ str_replace('_', ' ', ucfirst($row->name)) }}</label>
                                            <div class="">
                                                @foreach (json_decode($row->default_values) as $options)
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" class="edit_checkbox" id="checkbox_{{ $options }}" name="{{ 'checkbox[' . $row->name . '][' . $options . ']' }}">
                                                            {{ ucfirst(str_replace('_', ' ', $options)) }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($row->type === 'textarea')
                                        <div class="form-group col-sm-12 col-md-4 local-forms">
                                            <label>{{ str_replace('_', ' ', ucfirst($row->name)) }}
                                                {!! $row->is_required ? ' <span class="text-danger">*</span></label>' : '' !!}</label>
                                            <textarea name="{{ $row->name }}" id="{{ $row->name }}" cols="10" rows="3" placeholder="{{ str_replace('_', ' ', ucfirst($row->name)) }}" class="form-control edit_textarea"></textarea>
                                        </div>
                                    @endif

                                    @if ($row->type === 'file')
                                        <div class="col-12 col-sm-12 col-md-4">
                                            <div class="form-group row local-forms local-forms-files">
                                                <label class="col-4">{{ str_replace('_', ' ', ucfirst($row->name)) }}{!! $row->is_required ? ' <span class="text-danger">*</span></label>' : '' !!}</label>
                                                <input name="{{ $row->name }}" type="file" class="form-control">
                                                <div id="{{ $row->name }}-div" style="display: none">
                                                    <a href="" id="{{ $row->name }}" target="_blank" rel="noopener noreferrer">{{ str_replace('_', ' ', ucfirst($row->name)) }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-3">
                                        <label>{{ __('nationality') }} <span
                                                class="text-danger">*</span></label>
                                        <select required name="nationality" id="edit_nationality" class="form-control">
                                            @foreach (get_nationalities() as $n)
                                                <option value="{{$n}}">{{ trans($n) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-3">
                                        <label>{{ __('class') . ' ' . __('section') }} <span class="text-danger">*</span></label>
                                        <select required name="class_section_id" class="form-control" id="edit_class_section_id">
                                            <option value="">{{ __('select') . ' ' . __('class') . ' ' . __('section') }}
                                            </option>
                                            @foreach ($class_section as $section)
                                                <option value="{{ $section->id }}">{{ $section->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-3 d-flex align-items-center">
                                        <label class="col-form-label p-0 me-3">{{ __('repeater')." ?" }}</label>
                                        <div class="form-group mb-0 d-flex align-items-center">
                                            <input type="checkbox" id="edit_repeater" name="repeater" class="form-check-input" value="0">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3">
                                        <label>{{ __('Matricule') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ $initial_code }}</span>
                                            {!! Form::text('admission_no', null, [
                                                'placeholder' => __('Matricule'),
                                                'class' => 'form-control',
                                                'id' => 'edit_admission_no',
                                            ]) !!}
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3">
                                        <label>{{ __('born_at') }}
                                        {!! Form::text('born_at', null, [
                                            'placeholder' => __('born_at'),
                                            'class' => 'form-control',
                                            'id' => 'edit_born_at',
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3">
                                        <label>{{ __('roll_no') }}
                                        {!! Form::text('roll_number', null, [
                                            'placeholder' => __('roll_no'),
                                            'class' => 'form-control',
                                            'id' => 'edit_roll_number',
                                        ]) !!}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-3">
                                        <label>{{ __('admission_date') }} <span class="text-danger">*</span></label>
                                        {!! Form::text('admission_date', null, [
                                            'placeholder' => __('admission_date'),
                                            'class' => 'datetimepicker form-control',
                                            'id' => 'edit_admission_date',
                                        ]) !!}
                                        <span class="input-group-addon input-group-append">
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4>{{__('parent_details')}}</h4><br>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="edit_father_first_name">{{ __('father') . ' ' . __('full_name') }}</label>
                                    <select class="edit-father-first-name w-100 select2" id="edit_father_first_name" name="father_first_name"></select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="edit_father_mobile">{{ __('father') . ' ' . __('mobile') }}</label>
                                    {!! Form::number('father_mobile', null, [
                                        'placeholder' => __('father') . ' ' . __('mobile'),
                                        'class' => 'form-control remove-number-increment',
                                        'id' => 'edit_father_mobile',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="edit_father_email">{{ __('father_email') }}</label>

                                    {!! Form::email('father_email', null, [
                                        'placeholder' => __('father') . ' ' . __('email'),
                                        'class' => 'form-control',
                                        'id' => 'edit_father_email',
                                        'readonly' => true
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('father') . ' ' . __('dob') }}</label>
                                    {!! Form::text('father_dob', null, [
                                        'placeholder' => __('father') . ' ' . __('dob'),
                                        'class' => 'form-control dob-date form-control',
                                        'id' => 'edit_father_dob',
                                        'readonly' => true,
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('father') . ' ' . __('occupation') }}</label>
                                    {!! Form::text('father_occupation', null, [
                                        'placeholder' => __('father') . ' ' . __('occupation'),
                                        'class' => 'form-control',
                                        'id' => 'edit_father_occupation',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('father') . ' ' . __('image') }}</label>
                                    <input type="file" name="father_image" class="father_image form-control"/>
                                    <div style="width: 100px;">
                                        <img src="" id="edit-father-image-tag" class="img-fluid w-100" alt=""/>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="edit_mother_first_name">{{ __('mother') . ' ' . __('full_name') }}</label>
                                    <select class="edit-mother-first-name w-100 select2" id="edit_mother_first_name" name="mother_first_name"></select>
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('mother') . ' ' . __('mobile') }}</label>
                                    {!! Form::text('mother_mobile', null, [
                                        'placeholder' => __('mother') . ' ' . __('mobile'),
                                        'class' => 'form-control remove-number-increment',
                                        'id' => 'edit_mother_mobile',
                                        'readonly' => true,
                                        'min'=>1
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('mother_email') }}</label>
                                    {!! Form::email('mother_email', null, [
                                        'placeholder' => __('mother') . ' ' . __('email'),
                                        'class' => 'form-control',
                                        'id' => 'edit_mother_email',
                                        'readonly' => true
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('mother') . ' ' . __('dob') }}</label>
                                    {!! Form::text('mother_dob', null, [
                                        'placeholder' => __('mother') . ' ' . __('dob'),
                                        'class' => 'form-control dob-date form-control',
                                        'id' => 'edit_mother_dob',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('mother') . ' ' . __('occupation') }}</label>
                                    {!! Form::text('mother_occupation', null, [
                                        'placeholder' => __('mother') . ' ' . __('occupation'),
                                        'class' => 'form-control',
                                        'id' => 'edit_mother_occupation',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('mother') . ' ' . __('image') }}</label>
                                    <input type="file" name="mother_image" class="form-control"/>
                                    <div style="width: 100px;">
                                        <img src="" id="edit-mother-image-tag" class="img-fluid w-100" alt=""/>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="show-edit-guardian-details">{{ __('guardian_details') }}
                                    </label>
                                </div>
                            </div>
                            <div class="row" id="edit_guardian_div" style="display:none;">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label for="edit_guardian_first_name">{{ __('guardian') . ' ' . __('full_name') }} <span class="text-danger">*</span></label>
                                    <select class="edit-guardian-first-name form-control" id="edit_guardian_first_name" name="guardian_first_name"></select>
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('guardian') . ' ' . __('mobile') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('guardian_mobile', null, [
                                        'placeholder' => __('guardian') . ' ' . __('mobile'),
                                        'class' => 'form-control remove-number-increment',
                                        'id' => 'edit_guardian_mobile',
                                        'readonly' => true,
                                        'min'=>1
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('guardian') . ' ' . __('email') }} <span class="text-danger">*</span></label>
                                    {!! Form::email('guardian_email', null, [
                                        'placeholder' => __('guardian') . ' ' . __('email'),
                                        'class' => 'form-control',
                                        'id' => 'edit_guardian_email'
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('guardian') . ' ' . __('dob') }}
                                        <span class="text-danger">*</span></label>
                                    {!! Form::text('guardian_dob', null, [
                                        'placeholder' => __('guardian') . ' ' . __('dob'),
                                        'class' => 'form-control dob-date form-control',
                                        'id' => 'edit_guardian_dob',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('guardian') . ' ' . __('occupation') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('guardian_occupation', null, [
                                        'placeholder' => __('guardian') . ' ' . __('occupation'),
                                        'class' => 'form-control',
                                        'id' => 'edit_guardian_occupation',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('guardian') . ' ' . __('image') }}</label>
                                    <input type="file" name="guardian_image" class="form-control"/>
                                    <div style="width: 100px;">
                                        <img src="" id="edit-guardian-image-tag" class="img-fluid w-100" alt=""/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    @endcan
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkDeleteModalLabel">{{ trans('confirm_bulk_student_deletion') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ __('warning_bulk_delete_students') }}
                    </div>
                    <p>{{ __('selected_students_count') }}: <strong><span id="selectedStudentsCount">0</span></strong></p>
                    <div class="mb-3">
                        <label class="form-label">{{ __('type_confirmation_message') }}</label>
                        <div class="alert alert-light border">
                            <p id="confirmationText">I understand that I am about to delete multiple students and this action cannot be undone.</p>
                        </div>
                        <input type="text" class="form-control" id="bulkDeleteConfirmation"
                               placeholder="{{ __('type_the_above_message') }}" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirmBulkDeleteBtn" disabled>
                        <i class="fas fa-trash-alt me-2"></i>{{ trans('delete_selected_students') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script> --}}
    {{-- <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script> --}}
    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        function queryParams(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search
            };
        }

        const table = $('#table_list');
        let selections = [];
        let user_list = [];

        // ensures that the state of each of the rows is maintained.
        function responseHandler(res) {
            $.each(res.rows, function(i, row) {
                row.state = $.inArray(row.id, selections) !== -1
            })
            return res
        }

        // listens and takes actions to the different events.
        $(function() {
            // code that runs when the document is ready.
            table.on('check.bs.table check-all.bs.table uncheck.bs.table uncheck-all.bs.table',
                function(e, rowsAfter, rowsBefore) {
                    user_list = [];
                    let rows = rowsAfter;
                    if (e.type === 'uncheck-all') {
                        rows = rowsBefore
                    }
                    const ids = $.map(!$.isArray(rows) ? [rows] : rows, function (row) {
                        return row.id
                    });

                    const func = $.inArray(e.type, ['check', 'check-all']) > -1 ? 'union' : 'difference';
                    selections = window._[func](selections, ids)
                    selections.forEach(element => {
                        user_list.push(element);
                    });
                    $('textarea#selected_students').val(user_list);

                    modifyButtonStatus();
                }
            )
        })

        $('#btn_assign').hide();

        const selectStudentsTextarea = $('#selected_students');

        $(document).on('click', '.selected_student', function (e) {
            if (this.checked === true) {
                selected_student.push($(this).val());
            } else {
                const index = selected_student.indexOf($(this).val());
                if (index > -1) {
                    selected_student.splice(index, 1);
                }
            }
            const values = selectStudentsTextarea.val(selected_student);
            if (values.isEmpty) {
                alert("nothing selected");
            }
        });

        let selected_student = [];

        $("#btn_assign").on('click', function () {
            const selectedStudents = $('textarea#selected_students').val();
            const selectedCount = selectedStudents.split(',').length;

            $('#selectedStudentsCount').text(selectedCount);

            // Show the bulk delete confirmation modal
            $('#bulkDeleteModal').modal('show');
        });


        // Add this new JavaScript code for the bulk delete confirmation
        $(document).ready(function() {

            $('#editModal').on('shown.bs.modal', function () {
            $('.select2').select2({
                width: '100%',
                dropdownParent: $('#editModal')
                });
            });

            const bulkDeleteModal = document.getElementById('bulkDeleteModal');
            const confirmInput = document.getElementById('bulkDeleteConfirmation');
            const deleteBtn = document.getElementById('confirmBulkDeleteBtn');
            const confirmationText = document.getElementById('confirmationText').textContent;

            // Reset modal when shown
            bulkDeleteModal.addEventListener('show.bs.modal', function() {
                confirmInput.value = '';
                deleteBtn.disabled = true;
            });

            // Enable/disable delete button based on input
            confirmInput.addEventListener('input', function() {
                deleteBtn.disabled = confirmInput.value !== confirmationText;
            });

            // Handle bulk delete confirmation
            deleteBtn.addEventListener('click', function() {
                const selectedStudents = $('textarea#selected_students');
                const values = selectedStudents.val();

                // Show loading state
                deleteBtn.disabled = true;
                const originalText = deleteBtn.innerHTML;
                deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Deleting Students ...';

                $.ajax({
                    url: '/bulk-delete-students',
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        ids: values,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token for security
                    },
                    success: function(response) {
                        // Close modal
                        $('#bulkDeleteModal').modal('hide');

                        // Show success message
                        showSuccessToast(response.message);

                        // Clear selections
                        selectedStudents.val("");
                        selections = [];
                        user_list = [];

                        // Refresh table
                        $('#table_list').bootstrapTable('refresh');

                        // Hide delete button
                        $('#btn_assign').hide();
                    },
                    error: function(error) {
                        showErrorToast(error.message || "An error occurred while deleting students");
                    },
                    complete: function() {
                        // Reset button state
                        deleteBtn.disabled = false;
                        deleteBtn.innerHTML = originalText;
                    }
                });
            });
        });

        window.actionEvents = {}

        $(document).on('select2:open', '.edit-father-search,.edit-mother-search,.edit-guardian-search', function (e) {
            const evt = "scroll.select2"
            $(e.target).parents().off(evt)
            $(window).off(evt)
        })

        $("#edit_repeater").on('change', function() {
            if ($(this).is(':checked')) {
                $(this).attr('value', 1);
            } else {
                $(this).attr('value', 0);
            }
        });

        $("#active_status").on('change', function() {
            if ($(this).is(':checked')) {
                $(this).attr('value', 1);
            } else {
                $(this).attr('value', 0);
            }
        });

        const filterClasSection = $('#filter_class_section_id, #filter_by_student_status');
        const filterByStudentStatus = $('#filter_by_student_status');

        filterClasSection.on('change', function () {
            getStudentCount();
        });

        function getStudentCount() {
            const classSectionId = filterClasSection.val();
            const studentStatus = filterByStudentStatus.val();

            $.ajax({
                url: '/get-student-counts',
                type: 'GET',
                data: {
                    class_section_id: classSectionId,
                    student_status: studentStatus,
                },
                success: function(response) {
                    $('#girls-count').text(response.girls);
                    $('#boys-count').text(response.boys);
                },
                error: function(error) {

                }
            });
        }

        function modifyButtonStatus() {
            if (selectStudentsTextarea.val() !== '') {
                $('#btn_assign').show();
            } else {
                $('#btn_assign').hide();
            }
        }

        $(document).ready(function () {
            getStudentCount();

            const modal = document.getElementById('customModal');
            const confirmInput = document.getElementById('confirmStudentName');
            const deleteBtn = document.getElementById('deleteStudentBtn');

            modal.addEventListener('show.bs.modal', function() {
                confirmInput.value = '';
                deleteBtn.disabled = true;

                setTimeout(function () {
                    $('#selected_student').text(localStorage.getItem('selected_name'));
                }, 500)
            });

            confirmInput.addEventListener('input', function() {
                deleteBtn.disabled = confirmInput.value.toLowerCase() !== localStorage.getItem('selected_name').toLowerCase();
            });

            deleteBtn.addEventListener('click', function() {
                const studentId = localStorage.getItem('selected_user_id') ?? 0;

                // show the loading indicator
                deleteBtn.disabled = true;

                const originalValue = deleteBtn.textContent;

                deleteBtn.textContent = "Loading....";

                $.ajax({
                    url: '/students/' + studentId,
                    type: 'DELETE',
                    data: {},
                    success: function(response) {
                        showSuccessToast(response.message);
                        deleteBtn.disabled = true;
                    },
                    error: function(error) {
                        showErrorToast(error.message ?? "An Error message Occurred");
                        deleteBtn.disabled = false;
                    }
                });

                $('#customModal').modal('hide');

                deleteBtn.textContent = originalValue;

                $('#table_list').bootstrapTable('refresh');
            });

            table.on('refresh.bs.table', function (e, params) {
                // empty the text area

                // restoreCheckedState()

                // disable the button and make it inactive
            });
        });
    </script>
@endsection
