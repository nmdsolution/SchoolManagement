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
                            {{ __('create') . ' ' . __('students') }}
                        </h4>
                        {{-- <form class="pt-3 student-registration-form" enctype="multipart/form-data"
                            action="{{ route('students.store') }}" method="POST" novalidate="novalidate"> --}}
                            {!! Form::model($student, ['route' => ['students.update',$student->id], 'class' => 'edit-student-registration-form', 'enctype' => 'multipart/form-data']) !!}
                            {{-- @csrf --}}
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('full_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" value="{{ $student->user->first_name }}" name="first_name" placeholder="{{ __('full_name') }}"
                                        class="form-control" required>
                                </div>

                                <div class="form-group col-sm-12 col-md-3 local-forms">
                                    <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('dob', date('d-m-Y',strtotime($student->user->dob)), [
                                        'placeholder' => __('dob'),
                                        'class' => 'dob-date form-control',
                                    ]) !!}
                                    <span class="input-group-addon input-group-append"></span>
                                </div>
                                <div class="form-group col-sm-12 col-md-3 d-flex align-items-center">
                                    <label class="col-form-label p-0 me-3">{{ __('gender') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="form-group mb-0 d-flex align-items-center">
                                        <input type="radio" id="male" checked name="gender" value="male" required>
                                        <label class="ms-2 mb-0"
                                            for="male">{{ __('male') }}</label>&nbsp;&nbsp;&nbsp;

                                        <input type="radio" id="female" name="gender" value="female" required>
                                        <label class="ms-2 mb-0" for="female">{{ __('female') }}</label>
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms local-forms-files">
                                    <label>{{ __('image') }} (150 * 150) </label>
                                    {!! Form::file('image', ['placeholder' => __('image'), 'class' => 'form-control']) !!}
                                </div>

                                @foreach ($formFields as $row)
                                    @if ($row->type === 'text' || $row->type === 'number')
                                        <div class="form-group col-sm-12 col-md-4 local-forms">
                                            <label>{{ str_replace('_', ' ', $row->name) }} {!! $row->is_required ? ' <span class="text-danger">*</span></label>' : '' !!}</label>
                                            <input type="{{ $row->type }}" name="{{ $row->name }}"
                                                placeholder="{{ ucfirst(str_replace('_', ' ', $row->name)) }}"
                                                class="form-control" {{ $row->is_required === 1 ? 'required' : '' }}>
                                        </div>
                                    @endif

                                    @if ($row->type === 'dropdown')
                                        <div class="form-group col-sm-12 col-md-4 local-forms">
                                            <label>{{ ucfirst(str_replace('_', ' ', $row->name)) }}
                                                {!! $row->is_required ? ' <span class="text-danger">*</span></label>' : '' !!}</label>
                                            <select name="{{ $row->name }}" class="select">
                                                @foreach (json_decode($row->default_values) as $options)
                                                    <option value="{{ $options }}">{{ ucfirst($options) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    @if ($row->type === 'radio')
                                        <div class="form-group col-sm-12 col-md-4">
                                            <label>{{ ucfirst(str_replace('_', ' ', $row->name)) }}
                                                {!! $row->is_required ? ' <span class="text-danger">*</span></label>' : '' !!}</label> <br>
                                            <div class="d-flex">
                                                @foreach (json_decode($row->default_values) as $options)
                                                    <div class="form-check form-check-inline">
                                                        <label class="form-check-label">
                                                            <input type="radio" name="{{ $row->name }}"
                                                                value="{{ $options }}">
                                                            {{ ucfirst($options) }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($row->type === 'checkbox')
                                        <div class="form-group col-sm-12 col-md-4">
                                            <label
                                                class="col-form-label col-md-2">{{ ucfirst(str_replace('_', ' ', $row->name)) }}</label>
                                            <div class="">
                                                @foreach (json_decode($row->default_values) as $options)
                                                    <div class="checkbox">
                                                        <label>
                                                            {{-- <input type="checkbox" name="{{ $row->name }}">
                                                            {{ ucfirst($options) }} --}}
                                                            <input type="checkbox"
                                                                name="{{ 'checkbox[' . $row->name . '][' . $options . ']' }}">
                                                            {{ ucfirst(str_replace('_', ' ', $options)) }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($row->type === 'textarea')
                                        <div class="form-group col-sm-12 col-md-4 local-forms">
                                            <label>{{ ucfirst(str_replace('_', ' ', $row->name)) }}
                                                {!! $row->is_required ? ' <span class="text-danger">*</span></label>' : '' !!}</label>
                                            <textarea {{ $row->is_required ? 'required' : '' }} name="{{ $row->name }}" cols="10" rows="3"
                                                placeholder="{{ ucfirst(str_replace('_', ' ', $row->name)) }}" class="form-control"></textarea>
                                        </div>
                                    @endif

                                    @if ($row->type === 'file')
                                        <div class="col-12 col-sm-12 col-md-4">
                                            <div class="form-group files row">
                                                <label class="col-4">{{ str_replace('_', ' ', ucfirst($row->name)) }}
                                                    {!! $row->is_required ? ' <span class="text-danger">*</span></label>' : '' !!}</label>
                                                <input name="{{ $row->name }}" type="file" class="form-control">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach


                                <div class="form-group col-sm-12 col-md-4  local-forms">
                                    <label>{{ __('class') . ' ' . __('section') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="class_section_id" id="class_section" class="form-control select">
                                        <option value="">{{ __('select') . ' ' . __('class') . ' ' . __('section') }}
                                        </option>
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}">
                                                {{ $section->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('Matricule') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ $initial_code }}</span>
                                        {!! Form::text('admission_no', null, [
                                            'placeholder' => __('Matricule'),
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>

                                    {{-- {!! Form::text('admission_no_1', $initial_code.$admission_no, [
                                        'placeholder' => __('Matricule'),
                                        'class' => 'form-control',
                                    ]) !!}
                                    {!! Form::hidden('admission_no', $admission_no, [
                                        'readonly',
                                        'placeholder' => __('Matricule'),
                                        'class' => 'form-control',
                                    ]) !!} --}}
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('admission_date') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('admission_date', null, [
                                        'placeholder' => __('dd-mm-yyyy'),
                                        'class' => 'disable-future-date form-control',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                    <span class="input-group-addon input-group-append"></span>
                                </div>
                            </div>

                            {{-- Parent details --}}
                            <hr>
                            <h4>{{ __('parent_details') }}</h4><br>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('father_email') }}</label>
                                    <select class="father-search w-100" id="father_email" name="father_email"></select>
                                    <a href="#" class="clear-select2 ms-2">Clear</a>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('father') . ' ' . __('full_name') }} </label>
                                    {!! Form::text('father_first_name', null, [
                                        'placeholder' => __('father') . ' ' . __('full_name'),
                                        'class' => 'form-control',
                                        'id' => 'father_first_name',
                                    ]) !!}
                                </div>

                                {{-- <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('father') . ' ' . __('last_name') }} <span
                                            class="text-danger">*</span></label>
                                    {!! Form::text('father_last_name', null, [
                                        'placeholder' => __('father') . ' ' . __('last_name'),
                                        'class' => 'form-control',
                                        'id' => 'father_last_name',
                                    ]) !!}
                                </div> --}}
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('father') . ' ' . __('mobile') }} </label>
                                    {!! Form::number('father_mobile', null, [
                                        'placeholder' => __('father') . ' ' . __('mobile'),
                                        'class' => 'form-control remove-number-increment',
                                        'id' => 'father_mobile',
                                        'min' => 1,
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('father') . ' ' . __('dob') }}</label>
                                    {!! Form::text('father_dob', null, [
                                        'placeholder' => __('dd-mm-yyyy'),
                                        'class' => 'form-control dob-date form-control',
                                        'id' => 'father_dob',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('father') . ' ' . __('occupation') }} </label>
                                    {!! Form::text('father_occupation', null, [
                                        'placeholder' => __('father') . ' ' . __('occupation'),
                                        'class' => 'form-control',
                                        'id' => 'father_occupation',
                                    ]) !!}
                                </div>

                                <div class="col-12 col-sm-12 col-md-4">
                                    <div class="form-group local-forms-files local-forms">
                                        <label>{{ __('father') . ' ' . __('image') }}</label>
                                        <input name="father_image" type="file" class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('mother_email') }} </label>
                                    <select class="mother-search w-100" id="mother_email" name="mother_email"></select>
                                    <a href="#" class="clear-select2 ms-2">Clear</a>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">

                                    <label>{{ __('mother') . ' ' . __('full_name') }} </label>
                                    {!! Form::text('mother_first_name', null, [
                                        'placeholder' => __('mother') . ' ' . __('full_name'),
                                        'class' => 'form-control',
                                        'id' => 'mother_first_name',
                                    ]) !!}
                                </div>


                                {{-- <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('mother') . ' ' . __('last_name') }} <span
                                            class="text-danger">*</span></label>
                                    {!! Form::text('mother_last_name', null, [
                                        'placeholder' => __('mother') . ' ' . __('last_name'),
                                        'class' => 'form-control',
                                        'id' => 'mother_last_name',
                                    ]) !!}
                                </div> --}}
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('mother') . ' ' . __('mobile') }} </label>
                                    {!! Form::number('mother_mobile', null, [
                                        'placeholder' => __('mother') . ' ' . __('mobile'),
                                        'class' => 'form-control remove-number-increment',
                                        'id' => 'mother_mobile',
                                        'min' => 1,
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('mother') . ' ' . __('dob') }}</label>
                                    {!! Form::text('mother_dob', null, [
                                        'placeholder' => __('dd-mm-yyyy'),
                                        'class' => 'form-control dob-date form-control',
                                        'id' => 'mother_dob',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('mother') . ' ' . __('occupation') }} </label>
                                    {!! Form::text('mother_occupation', null, [
                                        'placeholder' => __('mother') . ' ' . __('occupation'),
                                        'class' => 'form-control',
                                        'id' => 'mother_occupation',
                                    ]) !!}
                                </div>

                                <div class="col-12 col-sm-12 col-md-4">
                                    <div class="form-group local-forms-files local-forms">
                                        <label>{{ __('mother') . ' ' . __('image') }}</label>
                                        <input name="mother_image" type="file" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {{-- Student Guardian --}}
                            <div class="form-group">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="guardian" value="1" class="form-check-input"
                                            id="show-guardian-details">{{ __('guardian_details') }}
                                    </label>
                                </div>
                            </div>
                            <div class="row" id="guardian_div">
                                <div class="form-group col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('guardian') . ' ' . __('email') }} <span class="text-danger">*</span></label>
                                    <select class="guardian-search form-control" id="guardian_email" name="guardian_email"></select>
                                    <a href="#" class="clear-select2 ms-2">Clear</a>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('guardian') . ' ' . __('full_name') }} <span
                                            class="text-danger">*</span></label>
                                    {!! Form::text('guardian_first_name', null, [
                                        'placeholder' => __('guardian') . ' ' . __('full_name'),
                                        'class' => 'form-control',
                                        'id' => 'guardian_first_name',
                                    ]) !!}
                                </div>

                                {{-- <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('guardian') . ' ' . __('last_name') }} <span
                                            class="text-danger">*</span></label>
                                    {!! Form::text('guardian_last_name', null, [
                                        'placeholder' => __('guardian') . ' ' . __('last_name'),
                                        'class' => 'form-control ',
                                        'id' => 'guardian_last_name',
                                    ]) !!}
                                </div> --}}
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('guardian') . ' ' . __('mobile') }} <span
                                            class="text-danger">*</span></label>
                                    {!! Form::number('guardian_mobile', null, [
                                        'placeholder' => __('guardian') . ' ' . __('mobile'),
                                        'class' => 'form-control remove-number-increment',
                                        'id' => 'guardian_mobile',
                                        'min' => 1,
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('guardian') . ' ' . __('dob') }} <span
                                            class="text-danger">*</span></label>
                                    {!! Form::text('guardian_dob', null, [
                                        'placeholder' => __('dd-mm-yyyy'),
                                        'class' => 'form-control dob-date form-control',
                                        'id' => 'guardian_dob',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label> <br>
                                    <div class="form-group col-sm-12 col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="guardian_gender"
                                                id="gender_male" value="male" checked>
                                            <label class="form-check-label" for="gender_male">{{ __('male') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="guardian_gender"
                                                id="gender_female" value="female">
                                            <label class="form-check-label" for="gender_female">{{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('guardian') . ' ' . __('occupation') }} <span
                                            class="text-danger">*</span></label>
                                    {!! Form::text('guardian_occupation', null, [
                                        'placeholder' => __('guardian') . ' ' . __('occupation'),
                                        'class' => 'form-control',
                                        'id' => 'guardian_occupation',
                                    ]) !!}
                                </div>

                                <div class="col-12 col-sm-12 col-md-4">
                                    <div class="form-group local-forms-files local-forms">
                                        <label>{{ __('guardian') . ' ' . __('image') }}</label>
                                        <input name="guardian_image" type="file" class="form-control">
                                    </div>
                                </div>
                            </div>
                            {{-- <div id="guardian_div">
                                <div id="guardian_form">
                                    <div class="row mt-4">
                                        <div class="form-group col-sm-12 col-md-12 local-forms">
                                            <label>{{ __('guardian') . ' ' . __('email') }} <span
                                                    class="text-danger">*</span></label>
                                            <select class="guardian-search form-control" id="guardian_email"
                                                name="guardian_email[]"></select>
                                        </div>

                                        <div class="form-group col-sm-12 col-md-4 local-forms">
                                            <label>{{ __('guardian') . ' ' . __('first_name') }} <span
                                                    class="text-danger">*</span></label>
                                            {!! Form::text('guardian_first_name[]', null, [
                                                'placeholder' => __('guardian') . ' ' . __('first_name'),
                                                'class' => 'form-control',
                                                'id' => 'guardian_first_name',
                                            ]) !!}
                                        </div>

                                        <div class="form-group col-sm-12 col-md-4 local-forms">
                                            <label>{{ __('guardian') . ' ' . __('last_name') }} <span
                                                    class="text-danger">*</span></label>
                                            {!! Form::text('guardian_last_name[]', null, [
                                                'placeholder' => __('guardian') . ' ' . __('last_name'),
                                                'class' => 'form-control',
                                                'id' => 'guardian_last_name',
                                            ]) !!}
                                        </div>
                                        <div class="form-group col-sm-12 col-md-4 local-forms">
                                            <label>{{ __('guardian') . ' ' . __('mobile') }} <span
                                                    class="text-danger">*</span></label>
                                            {!! Form::text('guardian_mobile[]', null, [
                                                'placeholder' => __('guardian') . ' ' . __('mobile'),
                                                'class' => 'form-control',
                                                'id' => 'guardian_mobile',
                                            ]) !!}
                                        </div>
                                        <div class="form-group col-sm-12 col-md-12">
                                            <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                            <br>
                                            <div class="form-group col-sm-12 col-md-12">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="guardian_gender[]"
                                                        id="gender_male" value="male" checked>
                                                    <label class="form-check-label"
                                                        for="gender_male">{{ __('male') }}</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="guardian_gender[]"
                                                        id="gender_female" value="female">
                                                    <label class="form-check-label"
                                                        for="gender_female">{{ __('female') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-12 col-md-4 local-forms">
                                            <label>{{ __('guardian') . ' ' . __('dob') }} <span
                                                    class="text-danger">*</span></label>
                                            {!! Form::text('guardian_dob[]', null, [
                                                'placeholder' => __('dd-mm-yyyy'),
                                                'class' => 'form-control datetimepicker form-control',
                                                'id' => 'guardian_dob',
                                            ]) !!}
                                        </div>
                                        <div class="form-group col-sm-12 col-md-4 local-forms">
                                            <label>{{ __('guardian') . ' ' . __('occupation') }} <span
                                                    class="text-danger">*</span></label>
                                            {!! Form::text('guardian_occupation[]', null, [
                                                'placeholder' => __('guardian') . ' ' . __('occupation'),
                                                'class' => 'form-control',
                                                'id' => 'guardian_occupation',]) !!}
                                </div>

                                <div class="col-12 col-sm-12 col-md-4">
                                    <div class="form-group files">
                                        <label>{{ __('guardian') . ' ' . __('image') }}</label>
                                        <span class="text-danger">*</span>
                                        <div class="uplod">
                                            <label class="file-upload image-upbtn mb-0">Choose File <input
                                                    name="guardian_image[]" type="file"></label>
                                        </div>
                                    </div>
                                </div><div class="col-sm-12 col-md-2">
                                            <button type="button" class="remove-field btn remove-guardian">Remove</button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-add-guardian mt-3 mb-3" id="add-field">Add Field</button>
                            </div> --}}
                            <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        window.load = $('#show-guardian-details').trigger('change');
    </script>
    <script>
        function addFormField() {
            let formRepeater = document.getElementById('guardian_form');
            let template = formRepeater.firstElementChild.cloneNode(true);

            // Clear the input values
            let inputs = template.querySelectorAll('input');
            inputs.forEach(function(input) {
                input.value = '';
            });

            console.log(template);
            // Add the remove button event listener
            let removeButton = template.querySelector('.remove-field');
            removeButton.addEventListener('click', function() {
                formRepeater.removeChild(template);
            });

            // Append the new form field to the repeater
            formRepeater.appendChild(template);
        }

        // Attach event listener to the add field button
        // let addFieldButton = document.getElementById('add-field');
        // addFieldButton.addEventListener('click', addFormField);
    </script>
@endsection
