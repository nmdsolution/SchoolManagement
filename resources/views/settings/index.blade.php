@extends('layout.master')

@section('title')
    {{ __('general_settings') }}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('general_settings') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="frmData" class="general-setting" action="{{ url('settings') }}" novalidate="novalidate"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="col-sm-12 col-md-12 form-group">
                                <h3>{{ __('Center Information') }}</h3>
                                <hr>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4 col-sm-12 local-forms">
                                    <label>{{ __('school_name') }} <span class="text-danger">*</span></label>
                                    <input name="school_name" value="{{ isset($settings['school_name']) ? $settings['school_name'] : '' }}" type="text" required placeholder="{{ __('school_name') }}" class="form-control"/>
                                </div>
                                <div class="form-group col-md-4 col-sm-12 local-forms">
                                    <label>{{ __('school_email') }} <span class="text-danger">*</span></label>
                                    <input name="school_email" value="{{ isset($settings['school_email']) ? $settings['school_email'] : '' }}" type="email" required placeholder="{{ __('school_email') }}" class="form-control"/>
                                </div>

                                <div class="form-group col-md-4 col-sm-12 local-forms">
                                    <label>{{ __('school_phone') }} <span class="text-danger">*</span></label>
                                    <input name="school_phone" value="{{ isset($settings['school_phone']) ? $settings['school_phone'] : '' }}" type="text" required placeholder="{{ __('school_phone') }}" class="form-control"/>
                                </div>
                                <div class="form-group col-md-6 col-sm-12 local-forms">
                                    <label>{{ __('school_tagline') }} <span class="text-danger">*</span></label>
                                    <textarea name="school_tagline" required placeholder="{{ __('school_tagline') }}" class="form-control">{{ isset($settings['school_tagline']) ? $settings['school_tagline'] : '' }}</textarea>
                                </div>

                                <div class="form-group col-md-6 col-sm-12 local-forms">
                                    <label>{{ __('school_address') }} <span class="text-danger">*</span></label>
                                    <textarea name="school_address" required placeholder="{{ __('school_address') }}" class="form-control">{{ isset($settings['school_address']) ? $settings['school_address'] : '' }}</textarea>
                                </div>

                                <div class="form-group col-md-4 col-sm-12 local-forms">
                                    <label>{{ __('time_zone') }} <span class="text-danger">*</span></label>
                                    <select name="time_zone" required class="form-control select" style="width:100%">
                                        @foreach ($getTimezoneList as $timezone)
                                            <option value="@php echo $timezone[2]; @endphp"{{ isset($settings['time_zone']) ? ($settings['time_zone'] == $timezone[2] ? 'selected' : '') : '' }}>
                                                @php  echo $timezone[2] .' - GMT ' . $timezone[1] .' - '.$timezone[0] @endphp</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-sm-12 local-forms">
                                    <label>{{ __('date_formate') }} <span class="text-danger">*</span></label>
                                    <select name="date_formate" required class="select form-control">
                                        @foreach ($getDateFormat as $key => $dateformate)
                                            <option value="{{ $key }}"{{ isset($settings['date_formate']) ? ($settings['date_formate'] == $key ? 'selected' : '') : '' }}>{{ $dateformate }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-sm-12 local-forms">
                                    <label>{{ __('time_formate') }} <span class="text-danger">*</span></label>
                                    <select name="time_formate" required class="select form-control">
                                        @foreach ($getTimeFormat as $key => $timeformate)
                                            <option value="{{ $key }}"{{ isset($settings['time_formate']) ? ($settings['time_formate'] == $key ? 'selected' : '') : '' }}>{{ $timeformate }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-2 col-sm-12 local-forms">
                                    <label>{{ __('Initial Code') }} <span class="text-danger">*</span></label>
                                    <input type="text" required name="initial_code" value="{{ isset($settings['initial_code']) ? $settings['initial_code'] : '' }}" class="form-control">

                                </div>

                                {{-- <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('favicon') }} <span class="text-danger">*</span></label>
                                    <input class="form-control" name="favicon" type="file">
                                </div> --}}

                                <div class="col-12 col-sm-12 col-md-4">
                                    <div class="form-group files">
                                        <label>{{ __('favicon') }} <span class="text-danger">*</span></label>
                                        <input name="favicon" type="file" class="form-control">
                                        @if (isset($settings['favicon']))
                                            <img src="{{ url(Storage::url($settings['favicon'])) }}" height="100" alt="">
                                        @endif
                                    </div>
                                </div>

                                {{-- <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('horizontal_logo') }} <span class="text-danger">*</span></label>
                                    <input class="form-control" name="logo1" type="file">
                                </div> --}}

                                <div class="col-12 col-sm-12 col-md-4">
                                    <div class="form-group files">
                                        <label>{{ __('horizontal_logo') }} <span class="text-danger">*</span>
                                            <span class="text-info">( {{ __('SIZE : 300px * 150px') }} )</span></label>
                                        <input name="logo1" type="file" class="form-control">
                                        @if (isset($settings['logo1']))
                                            <img src="{{ url(Storage::url($settings['logo1'])) }}" height="100" alt="">
                                        @endif
                                    </div>
                                </div>

                                {{-- <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('vertical_logo') }} <span class="text-danger">*</span></label>
                                    <input class="form-control" name="logo2" type="file">
                                </div> --}}

                                <div class="col-12 col-sm-12 col-md-4">
                                    <div class="form-group files">
                                        <label>{{ __('vertical_logo') }} <span class="text-danger">*</span></label>
                                        <input name="logo2" type="file" class="form-control">
                                        @if (isset($settings['logo2']))
                                            <img src="{{ url(Storage::url($settings['logo2'])) }}" height="100" alt="">
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md-4 col-sm-12 local-forms">
                                    <label>{{ __('color') }}</label>
                                    <input name="theme_color" value="{{ isset($settings['theme_color']) ? $settings['theme_color'] : '' }}" type="text" required placeholder="{{ __('color') }}" class="color-picker"/>
                                </div>
                                <div class="form-group col-md-4 col-sm-12 local-forms">
                                    <label>{{ __('session_years') }}</label>
                                    <select name="session_year" required class=" select form-control">
                                        @foreach ($session_year as $key => $year)
                                            <option value="{{ $year->id }}"{{ isset($settings['session_year']) ? ($settings['session_year'] == $year->id ? 'selected' : '') : '' }}>{{ $year->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- online payment mode setting --}}
                                @if (isset($settings['online_payment']))
                                    @if ($settings['online_payment'])
                                        <div class="form-inline col-md-4">
                                            <label>{{ __('online_payment_mode') }}</label>
                                            <span class="ml-1 text-danger">*</span>
                                            <div class="ml-4 d-flex">
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        <input type="radio" name="online_payment" class="online_payment_toggle" value="1" checked>
                                                        {{ __('enable') }}
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        <input type="radio" name="online_payment" class="online_payment_toggle" value="0">
                                                        {{ __('disable') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="form-inline col-md-4">
                                            <label>{{ __('online_payment_mode') }}</label>
                                            <span class="ml-1 text-danger">*</span>
                                            <div class="ml-4 d-flex">
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        <input type="radio" name="online_payment" class="online_payment_toggle" value="1">
                                                        {{ __('enable') }}
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label">
                                                        <input type="radio" name="online_payment"
                                                               class="online_payment_toggle" value="0" checked>
                                                        {{ __('disable') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="form-inline col-md-4">
                                        <label>{{ __('online_payment_mode') }}</label>
                                        <span class="ml-1 text-danger">*</span>
                                        <div class="ml-4 d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" name="online_payment" class="online_payment_toggle" value="1" checked>
                                                    {{ __('enable') }}
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" name="online_payment" class="online_payment_toggle" value="0">
                                                    {{ __('disable') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                {{-- end of online payment mode setting --}}
                                <div class="form-inline col-md-4">
                                    <label>{{ __('Auto Publish Exams') }}
                                        <span class="fa fa-info-circle" title="If this option is turned on then All the exams will be Auto Published. Otherwise Class Teacher / Center Admin has to Publish Manually"></span>
                                    </label><br>
                                    <input type="checkbox" class="js-switch" name="auto_publish_exams"
                                            {{ isset($settings['auto_publish_exams']) && $settings['auto_publish_exams'] == 1 ? 'checked' : '' }} />
                                </div>

                                <div class="form-group col-sm-12 col-md-12 local-forms mt-3">
                                    <label for="">{{ __('Student Honor Roll Certificate Paragraph') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('student_honor_roll_text', isset($settings['student_honor_roll_text']) ? $settings['student_honor_roll_text'] : '', ['class' => 'form-control','required','placeholder' => __('Student Honor Roll Certificate Paragraph'),'maxlength' => '805']) !!}
                                </div>


                                @if (!Auth::user()->hasRole('Super Admin'))
                                    <div class="form-group col-sm-12 col-md-12">
                                        <hr>
                                        <h4>{{__('Student ID Card')}}</h4>
                                        <hr>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12 local-forms">
                                        <label for="">{{ __('Header Color') }}
                                            <span class="text-danger">*</span></label>
                                        <input name="header_color" value="{{ isset($settings['header_color']) ? $settings['header_color'] : '' }}" type="text" required placeholder="{{ __('color') }}" class="color-picker"/>
                                    </div>

                                    <div class="form-group col-md-4 col-sm-12 local-forms">
                                        <label for="">{{ __('Footer Color') }}
                                            <span class="text-danger">*</span></label>
                                        <input name="footer_color" value="{{ isset($settings['footer_color']) ? $settings['footer_color'] : '' }}" type="text" required placeholder="{{ __('color') }}" class="color-picker"/>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12 local-forms">
                                        <label for="">{{ __('Text Color') }} <span class="text-danger">*</span></label>
                                        <input name="text_color" value="{{ isset($settings['text_color']) ? $settings['text_color'] : '' }}" type="text" required placeholder="{{ __('color') }}" class="color-picker"/>
                                    </div>
                                    {{-- <div class="form-group col-sm-12 col-md-3 local-forms">
                                        <label for="">{{__('Print per page')}}</label>
                                        {!! Form::select(
                                            'print_per_page',
                                            ['1' => '1', '2' => '2', '3' => 3],
                                            isset($settings['print_per_page']) ? $settings['print_per_page'] : '',
                                            ['class' => 'form-control'],
                                        ) !!}
                                    </div> --}}
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="">{{ __('fields') }} <span class="text-danger">*</span></label>
                                        {{ Form::select('student_id_fields[]', ['full_name' => 'Name', 'class_name' => 'Class Section', 'roll_number' => 'Roll Number', 'admission_no' => 'Matricule', 'session_year' => 'Session Year', 'nationality'=> trans('nationality')], $student_id_fields, ['required', 'multiple', 'class' => 'form-control js-example-basic-single select2-hidden-accessible', 'style' => 'width:100%']) }}
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label for="">{{__('Water Mark Image')}} <span class="text-info">( {{ __('SIZE : 300px *150px') }})</span></label>
                                        {!! Form::file('water_mark', ['class' => 'form-control']) !!}
                                        @if (isset($settings['water_mark']))
                                            <img src="{{ url(Storage::url($settings['water_mark'])) }}" height="150" alt="">
                                        @endif
                                    </div>

                                    <div class="form-group col-sm-12 col-md-12">
                                        <hr>
                                        <h4>{{__('Certificates')}}</h4>
                                        <hr>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12">

                                        <label for="">{{ __('School Leaving Certificate File') }}
                                            <span class="text-info text-small">({{ __('Word file only .doc, .docx') }})</span></label>
                                        {!! Form::file('certificate_file', ['class' => 'form-control']) !!}
                                        {{-- <a href="{{ url('storage/school_certificate_sample_file.docx') }}" download>Sample
                                        File</a> --}}
                                        <a href="{{ asset('assets/file/school_certificate_sample_file.docx') }}"
                                           download>{{__('Sample File')}}</a>
                                    </div>

                                    {{-- <div class="form-group col-md-6 col-sm-12">
                                        <label for="">{{ __('Student Honor Roll Certificate File') }} <span
                                                    class="text-info text-small">({{ __('Word file only .doc, .docx') }})</span></label>
                                        {!! Form::file('honor_roll_certificate_file', ['class' => 'form-control']) !!}
                                        <a href="{{ asset('assets/file/student-honor-roll.docx') }}" download>{{__("Sample File")}}</a>
                                    </div> --}}

                                    {{-- <div class="form-group col-md-3 col-sm-12">
                                    <label for="">{{ __('Issue Date') }} </label>
                                    {!! Form::select('certificate_issue_date', ['Current Date','Exam Publish Date'], 0, ['class' => 'form-control select']) !!}
                                </div> --}}
                                @endif

                                <div class="form-group col-sm-12 col-md-12">
                                    <hr>
                                    <h4>{{__('global_statistics')}}</h4>
                                    <hr>
                                </div>
                                <div class="form-group col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('global_minimum_coefficient_percentage') }}</label> 
                                    {!! Form::number('global_report_minimum_coefficient_percentage', $settings['global_report_minimum_coefficient_percentage'], ['placeholder' => __('Minimum Coefficient Percentage'), 'class' => 'form-control']) !!}
                                </div>

                            </div>
                            <input class="btn btn-primary mt-3" type="submit" value="{{ __('submit') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type='text/javascript'>
        if ($(".color-picker").length) {
            $('.color-picker').asColorPicker();
        }
    </script>
@endsection
