@extends('layout.master')

@section('title')
    {{ __('general_settings') }}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Report Settings') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12 d-flex justify-content-end mb-3">
                            <a data-toggle='lightbox' href="{{url('images/sample-report.jpg')}}" class='image-popup'>{{__('View Sample Report')}}</a>
                        </div>
                        <div class="col-12 d-flex justify-content-end mb-3">
                            <a data-toggle='lightbox' href="{{url('images/with_competence.png')}}" class='image-popup'>{{__('table') . " " . __('with') . " " . __('competency')}}</a>
                        </div>
                        <div class="col-12 d-flex justify-content-end mb-3">
                            <a data-toggle='lightbox' href="{{url('images/sample-report.jpg')}}" class='image-popup'>{{__('table') . " " . __('without') . " " . __('competency')}}</a>
                        </div>
                        <form id="report-setting-form" action="{{ route('report-settings.update') }}" novalidate="novalidate" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-5">
                            {{-- <div class="row">
                                    <h5>
                                        {{ __('Report Layout')}}
                                    </h5>
                                </div> 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select name="report_layout_type" class="form-control">
                                            @php
                                                $reportLayoutType = $settings['report_layout_type'] ?? 0;
                                            @endphp  
                                            <option value="0" {{ $reportLayoutType == 0 ? 'selected' : '' }}>
                                                {{ __('Old Layout Without Competencies') }}
                                            </option>                                            
                                            <option value="1" {{ $reportLayoutType == 1 ? 'selected' : '' }}>
                                                {{ __('New Layout With Competencies') }}
                                            </option>                                           
                                            @if (isPrimaryCenter())
                                                <option value="2" {{ $reportLayoutType == 2 ? 'selected' : '' }}>
                                                    {{ __('New Layout With Competencies Without Header') }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div> --}}
                            </div>
                            <div class="row mb-5">
                                <div class="row">
                                    <h5>
                                        {{ __('absence')}}
                                    </h5>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">&nbsp;</div>
                                    <div class="col-md-4 text-center">{{ __('Min') }}</div>
                                    <div class="col-md-4 text-center">{{ __('Max') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label for="">{{ __('Warning (AV)') }} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min="1" name="report_warning_min" value="{{ $settings['report_warning_min'] ?? '' }}" required placeholder="{{ __('Min').' '.__('Warning (AV)') }}" class="form-control"/>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min="1" name="report_warning_max" value="{{ $settings['report_warning_max'] ?? '' }}" required placeholder="{{ __('Max').' '.__('Warning (AV)') }}" class="form-control"/>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label for="">{{ __('Blame average') }} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min="1" name="report_blame_min" value="{{ $settings['report_blame_min'] ?? '' }}" required placeholder="{{ __('Min').' '.__('Blame average') }}" class="form-control"/>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min="1" name="report_blame_max" value="{{ $settings['report_blame_max'] ?? '' }}" required placeholder="{{ __('Max').' '.__('Blame average') }}" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="row">
                                    <h5>
                                        {{ __('Average')}}
                                    </h5>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">&nbsp;</div>
                                    <div class="col-md-4 text-center">{{ __('Min Avg') }}</div>
                                    <div class="col-md-4 text-center">{{ __('Max Avg') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label for="">{{ __('Blame average') }} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min="1" name="average_blame_min" value="{{ $settings['average_blame_min'] ?? '' }}" required placeholder="{{ __('Min')}}" class="form-control"/>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min="1" name="average_blame_max" value="{{ $settings['average_blame_max'] ?? '' }}" required placeholder="{{ __('Max')}}" class="form-control"/>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label for="">{{ __('Warning (AV)') }} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min="1" name="average_warning_min" value="{{ $settings['average_warning_min'] ?? '' }}" required placeholder="{{ __('Min') }}" class="form-control"/>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min="1" name="average_warning_max" value="{{ $settings['average_warning_max'] ?? '' }}" required placeholder="{{ __('Max') }}" class="form-control"/>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label for="">{{ __('Encouragement (ENR)') }} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min="1" name="encouragement_min" value="{{ $settings['encouragement_min'] ?? '' }}" required placeholder="{{ __('Min').' '.__('Encouragement (ENR)') }}" class="form-control"/>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min="1" name="encouragement_max" value="{{ $settings['encouragement_max'] ?? '' }}" required placeholder="{{ __('Max').' '.__('Encouragement (ENR)') }}" class="form-control"/>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label for="">{{ __('Congratulations (FEL)') }} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min="1" name="congratulations_min" value="{{ $settings['congratulations_min'] ?? '' }}" required placeholder="{{ __('Min').' '.__('Congratulations (FEL)') }}" class="form-control"/>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" min="1" name="congratulations_max" value="{{ $settings['congratulations_max'] ?? '' }}" required placeholder="{{ __('Max').' '.__('Congratulations (FEL)') }}" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-12 local-forms">
                                    <label for="">{{ __('Honor Roll') }} <span class="text-danger">*</span></label>
                                    <input type="number" min="1" name="report_honor_roll" value="{{ $settings['report_honor_roll'] ?? '' }}" required placeholder="{{ __('Honor Roll') }}" class="form-control"/>
                                </div>
                                <div class="form-group col-md-6 col-sm-12 local-forms">
                                    <label for="">{{ __('Honor Roll').' '.__('absence') }} <span class="text-danger">*</span></label>
                                    <input type="number" min="1" name="report_honor_roll_absences" value="{{ $settings['report_honor_roll_absences'] ?? '' }}" required placeholder="{{ __('Honor Roll').' '.__('absence') }}" class="form-control"/>
                                </div>
                                <div class="form-group col-md-6 col-sm-12 local-forms">
                                    <label for="">{{ __('Low Subject Averege') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="number" min="1" name="report_low_subject_average" value="{{ $settings['report_low_subject_average'] ?? '' }}" required placeholder="{{ __('Low Subject Average') }}" class="form-control"/>
                                </div>
                                <div class="form-group col-md-6 col-sm-12 local-forms">
                                    <label for="">{{ __('Report Color') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="report_color" value="{{ $settings['report_color'] ?? '' }}" required placeholder="{{ __('Color') }}" class="color-picker" autocomplete="off"/>
                                </div>
                                <div class="form-group col-md-3 col-sm-12 local-forms">
                                    <label for="">{{'Marks'.' '. __('font_size') }} </label>
                                    <select name="marks_font_size" class="form-control">
                                        <option value="14">Default (14)</option>
                                        @for ($i = 1; $i <= 15; $i++)
                                            <option value="{{ $i }}" {{ (isset($settings['marks_font_size']) && $settings['marks_font_size'] == $i) ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-sm-12 local-forms">
                                    <label for="">{{'Teacher Name'.' '. __('font_size') }} </label>
                                    <select name="teacher_name_font_size" class="form-control">
                                        <option value="12">Default (12)</option>
                                        @for ($i = 1; $i <= 15; $i++)
                                            <option value="{{ $i }}" {{ (isset($settings['teacher_name_font_size']) && $settings['subject_font_size'] == $i) ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-sm-12 local-forms">
                                    <label for="">{{'Subject '.' '. __('font_size') }} </label>
                                    <select name="subject_font_size" class="form-control">
                                        <option value="12">Default (12)</option>
                                        @for ($i = 1; $i <= 15; $i++)
                                            <option value="{{ $i }}" {{ (isset($settings['subject_font_size']) && $settings['subject_font_size'] == $i) ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-sm-12 local-forms">
                                    <label for="">{{'Competence '.' '. __('font_size') }} </label>
                                    <select name="competence_font_size" class="form-control">
                                        <option value="9">Default (9)</option>
                                        @for ($i = 1; $i <= 15; $i++)
                                            <option value="{{ $i }}" {{ (isset($settings['competence_font_size']) && $settings['competence_font_size'] == $i) ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                @php
                                // in case keys change
                                    $fontStyles = [
                                        'normal' => 'normal',
                                        'bold' => 'bold',
                                        'italic' => 'italic',
                                    ];
                                @endphp

                                <div class="form-group col-md-3 col-sm-12 local-forms">
                                    <label for="">{{ 'Marks' . ' ' . __('font_style') }}</label>
                                    <select name="marks_font_style" id="marks_font_style" class="form-control styled-select">
                                        <option value="bold">Default (bold)</option>
                                        @foreach ($fontStyles as $tag => $fontStyle)
                                            <option value="{{ $tag }}" {{ (isset($settings['marks_font_style']) && $settings['marks_font_style'] == $tag) ? 'selected' : '' }}>
                                                {{ $fontStyle }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-sm-12 local-forms">
                                    <label for="">{{ 'Teacher Name' . ' ' . __('font_style') }}</label>
                                    <select name="teacher_name_font_style" id="teacher_name_font_style" class="form-control styled-select">
                                        <option value="bold">Default (bold)</option>
                                        @foreach ($fontStyles as $tag => $fontStyle)
                                            <option value="{{ $tag }}" {{ (isset($settings['teacher_name_font_style']) && $settings['teacher_name_font_style'] == $tag) ? 'selected' : '' }}>
                                                {{ $fontStyle }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-sm-12 local-forms">
                                    <label for="">{{ __('subject') . ' ' . __('font_style') }}</label>
                                    <select name="subject_font_style" id="subject_font_style" class="form-control styled-select">
                                        <option value="normal">Default (normal)</option>
                                        @foreach ($fontStyles as $tag => $fontStyle)
                                            <option value="{{ $tag }}" {{ (isset($settings['subject_font_style']) && $settings['subject_font_style'] == $tag) ? 'selected' : '' }}>
                                                {{ $fontStyle }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3 col-sm-12 local-forms">
                                    <label for="">{{ __('Result Subject Group') . ' ' . __('font_style') }}</label>
                                    <select name="subject_group_style" id="subject_group_style" class="form-control styled-select">
                                        <option value="bold">Default (bold)</option>
                                        @foreach ($fontStyles as $tag => $fontStyle)
                                            <option value="{{ $tag }}" {{ (isset($settings['subject_group_style']) && $settings['subject_group_style'] == $tag) ? 'selected' : '' }}>
                                                {{ $fontStyle }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                {{-- <div class="form-group col-md-4 col-sm-12 local-forms">
                                    <label for="report_date_generated" class="form-label">
                                         {{ __('show') . " " . __('date_generated') }}
                                    </label>
                                    <select name="report_date_generated" id="report_date_generated" class="form-control">
                                        <option value="1" {{ !empty($settings['report_date_generated']) ? 'selected' : '' }}>
                                            {{ __('Yes') }}
                                        </option>
                                        <option value="0" {{ empty($settings['report_date_generated']) ? 'selected' : '' }}>
                                            {{ __('No') }}
                                        </option>
                                    </select>
                                </div> --}}

                                <div class="row d-flex align-items-center justify-content-between col-md-8">
                                    <div class="form-group col-md-6 col-sm-12 local-forms">
                                        <label for="discipline_master_signature" class="form-label">
                                            <input type="checkbox" id="discipline_master_signature" name="discipline_master_signature" value="1" {{ !empty($settings['discipline_master_signature']) ? 'checked' : '' }}>
                                            {{  __('Signature') . " " . __('discipline_master')}}
                                        </label>
                                    </div>

                                    <div class="form-group col-md-6 col-sm-12 local-forms">
                                        <label for="council_decision" class="form-label">
                                            <input type="checkbox" id="council_decision" name="council_decision" value="1" {{ !empty($settings['council_decision']) ? 'checked' : '' }}>
                                            {{ __('council_decision') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="">{{ __('Report Header Logo') }}</label>
                                        <input type="file" name="report_header_logo" placeholder="{{ __('Report Header Logo') }}" class="form-control"/>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12 d-flex align-items-end">
                                        <a data-toggle='lightbox' href="{{$settings['report_header_logo'] ?? ''}}" class='image-popup'>
                                            <img id="header-image" src="{{$settings['report_header_logo'] ?? ''}}" alt="Report Header Logo" onerror="onErrorImage(event)" class="w-25">
                                        </a>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Upload Watermark Image -->
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="">{{ __('Water Mark Image') }} 
                                            <span class="text-info">( {{ __('SIZE : 300px * 150px') }})</span>
                                        </label>
                                        <input type="file" name="report_water_mark" placeholder="{{ __('Water Mark Image') }}" class="form-control"/>
                                    </div>

                                    <!-- Existing Watermark Image with Remove Option -->
                                    <div class="form-group col-md-6 col-sm-12 d-flex align-items-end">
                                        @if(!empty($settings['report_water_mark']))
                                            <a data-toggle='lightbox' href="{{ $settings['report_water_mark'] }}" class='image-popup'>
                                                <img id="water-mark-image" src="{{ $settings['report_water_mark'] }}" 
                                                    alt="Water Mark Image" onerror="onErrorImage(event)" class="w-25">
                                            </a>

                                            <!-- Remove Checkbox -->
                                            <div class="form-check ms-3">
                                                <input type="hidden" name="remove_water_mark" value="0">
                                                <input type="checkbox" name="remove_water_mark" id="remove-water-mark" value="1" {{ old('remove_water_mark') ? 'checked' : '' }}>
                                                <label class="form-check-label text-danger" for="remove-water-mark">{{ __('delete') .' '. __('Water Mark Image') }}</label>
                                            </div>
                                        @endif
                                    </div>

                                </div>


                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="">{{ __('Report Left Header') }}</label>
                                        <textarea name="report_left_header" id="report_left_header" class="form-control tinymce_message">{{$settings['report_left_header'] ?? ''}}</textarea>
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label for="">{{ __('Report Right Header') }}</label>
                                        <textarea name="report_right_header" id="report_right_header" class="form-control tinymce_message">{{$settings['report_right_header'] ?? ''}}</textarea>
                                    </div>
                                </div>

                                <hr>
                                <h5>{{__("Effective Domains")}}</h5>
                                <div class="col-md-1 col-sm-12 mb-3">
                                    <button type="button" class="btn btn-primary btn-block add-affective-domain">
                                        <span class="fa fa-plus"></span></button>
                                </div>
                                <div id="effective-domain-div">
                                    @foreach($effective_domain as $row)
                                        <div class="row">
                                            <div class="form-group col-md-4 col-sm-12 local-forms">
                                                <input type="text" name="effective_domain[]" required placeholder="Effective Domain" class="form-control" value="{{$row->name}}"/>
                                            </div>
                                            <div class="form-group col-md-4 col-sm-12 local-forms d-flex align-middle">
                                                <button type="button" class="btn btn-danger remove-affective-domain" data-id="{{$row->id}}">
                                                    <span class="fa fa-times"></span>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                            <input class="btn btn-primary" type="submit" value="{{ __('submit') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $('#report-setting-form').on('submit', function (e) {
            e.preventDefault();
            tinyMCE.triggerSave();
            let data = new FormData(this);
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: $(this).attr('action'),
                data: data,
                processData: false,
                cache: false,
                contentType: false,
                success: function (response) {
                    if (!response.error) {
                        if (response.image) {
                            $('#header-image').attr('src', response.image);
                            $('#header-image').parent().attr('href', response.image);
                        }
                        showSuccessToast(response.message);
                    } else {
                        showErrorToast(response.message);
                    }
                }
            });
        })

        $(document).on('click', '.add-affective-domain', function () {
            let html = createEffectiveDomainHTML();
            $('#effective-domain-div').append(html);
        })

        $(document).on('click', '.remove-affective-domain', function () {
            let $this = $(this);
            if ($(this).data('id')) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't Delete this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let id = $this.data('id');
                        let url = baseUrl + '/settings/delete/effective-domain/' + id;

                        function successCallback() {
                            $this.parent().parent().remove();
                        }

                        function errorCallback(response) {
                            showErrorToast(response.message);
                        }

                        ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
                    }
                })
            } else {
                $(this).parent().parent().remove();
            }
        })
    </script>

    <script>
    // Apply font styles dynamically to the dropdown options
    document.addEventListener("DOMContentLoaded", function () {
        const fontStyles = {
            bold: "font-weight: bold;",
            italic: "font-style: italic;",
            normal: "font-style: normal",
            underline: "text-decoration: underline;",
        };

        document.querySelectorAll(".styled-select").forEach((select) => {
            select.querySelectorAll("option").forEach((option) => {
                const style = fontStyles[option.value];
                if (style) {
                    option.style = style;
                }
            });
        });
    });
    </script>

@endsection