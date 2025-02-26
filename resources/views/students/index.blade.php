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
                        {!! form_start($form, [
                            'attr' => [
                                'class' => 'student-registration-form'
                            ]
                        ]) !!}
                            <div class="row">
                                {!! form_rest($form) !!}

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
                                            <label>{{ ucfirst(str_replace('_', ' ', $row->name)) }}{!! $row->is_required ? ' <span class="text-danger">*</span></label>' : '' !!}</label>
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
                                                {!! $row->is_required ? ' <span class="text-danger">*</span></label>' : '' !!}</label>
                                            <br>
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
                                            <textarea {{ $row->is_required ? 'required' : '' }} name="{{ $row->name }}"
                                                      cols="10" rows="3"
                                                      placeholder="{{ ucfirst(str_replace('_', ' ', $row->name)) }}"
                                                      class="form-control"></textarea>
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
                            </div>

                            {{-- Parent details --}}
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
                                    <label for="guardian_first_name">{{ __('guardian') . ' ' . __('first_name') }} <span
                                                class="text-danger">*</span></label>
                                    <select class="guardian-search form-control" id="guardian_first_name"
                                            name="guardian_first_name"></select>
                                    <a href="#" class="clear-select2 ms-2">Clear</a>
                                </div>


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
                                    <label>{{ __('guardian') . ' ' . __('email') }} <span
                                                class="text-danger">*</span></label>
                                    {!! Form::text('guardian_email', null, [
                                        'placeholder' => __('guardian') . ' ' . __('email'),
                                        'class' => 'form-control',
                                        'id' => 'guardian_email',
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

                        {!! form_end($form) !!}
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

        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                // dropdownParent: $('#create-form'),
                placeholder: "Select statuses",
                allowClear: true,
            });
        });

        function addFormField() {
            let formRepeater = document.getElementById('guardian_form');
            let template = formRepeater.firstElementChild.cloneNode(true);

            // Clear the input values
            let inputs = template.querySelectorAll('input');
            inputs.forEach(function (input) {
                input.value = '';
            });

            console.log(template);
            // Add the remove button event listener
            let removeButton = template.querySelector('.remove-field');
            removeButton.addEventListener('click', function () {
                formRepeater.removeChild(template);
            });

            // Append the new form field to the repeater
            formRepeater.appendChild(template);
        }

        $("#repeater").on('change', function () {
            if ($(this).is(':checked')) {
                $(this).attr('value', 1);
            } else {
                $(this).attr('value', 0);
            }
        });

    </script>
@endsection

@section('script')
<style>
    .required:after {
        content: ' *';
        color: red;
    }
    </style>
@endsection
