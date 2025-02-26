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
                        <form class="pt-3 student-registration-form" enctype="multipart/form-data" action="{{ route('students.store') }}" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('first_name', null, ['placeholder' => __('first_name'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('last_name', null, ['placeholder' => __('last_name'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('mobile') }}</label>
                                    {!! Form::text('mobile', null, ['placeholder' => __('mobile'), 'class' => 'form-control']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="form-group col-sm-12 col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="male" value="male" checked>
                                            <label class="form-check-label" for="male">{{ __('male') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                                            <label class="form-check-label" for="female">{{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12 col-md-4">
                                    <div class="form-group files">
                                        <label>{{ __('image') }}</label>
                                        <input name="image" type="file" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('dob') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('dob', null, ['placeholder' => __('dd-mm-yyyy'),'class' => 'datetimepicker form-control','id' => 'date',]) !!}
                                    <span class="input-group-addon input-group-append"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4  local-forms">
                                    <label>{{ __('class') . ' ' . __('section') }} <span class="text-danger">*</span></label>
                                    <select name="class_section_id" id="class_section" class="form-control select">
                                        <option value="">{{ __('select') . ' ' . __('class') . ' ' . __('section') }}
                                        </option>
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}">{{ $section->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('Matricule') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('admission_no', $admission_no, ['readonly','placeholder' => __('Matricule'),'class' => 'form-control',]) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('caste') }}</label>
                                    {!! Form::text('caste', null, ['placeholder' => __('caste'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('religion') }}</label>
                                    {!! Form::text('religion', null, ['placeholder' => __('religion'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('admission_date') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('admission_date', null, [
                                        'placeholder' => __('dd-mm-yyyy'),
                                        'class' => 'datetimepicker form-control',
                                    ]) !!}
                                    <span class="input-group-addon input-group-append"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('blood_group') }} <span class="text-danger">*</span></label>
                                    <select name="blood_group" class="form-control select">
                                        <option value="">{{ __('select') . ' ' . __('blood_group') }}</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('height') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('height', null, ['placeholder' => __('height'), 'class' => 'form-control']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('weight') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('weight', null, ['placeholder' => __('weight'), 'class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('current_address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('current_address', null, ['placeholder' => __('current_address'),'class' => 'form-control','id' => 'current_address','rows' => 2,]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('permanent_address') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('permanent_address', null, ['placeholder' => __('permanent_address'),'class' => 'form-control','id' => 'permanent_address','rows' => 2,]) !!}
                                </div>
                            </div>
                            {{-- Parent details --}}
                            <hr>
                            <h4>{{ __('parent_details') }}</h4><br>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('father_email') }} <span class="text-danger">*</span></label>
                                    <select class="father-search w-100" id="father_email" name="father_email"></select>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('father') . ' ' . __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('father_first_name', null, ['placeholder' => __('father') . ' ' . __('first_name'),'class' => 'form-control','id' => 'father_first_name',]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('father') . ' ' . __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('father_last_name', null, ['placeholder' => __('father') . ' ' . __('last_name'),'class' => 'form-control','id' => 'father_last_name',]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('father') . ' ' . __('mobile') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('father_mobile', null, ['placeholder' => __('father') . ' ' . __('mobile'),'class' => 'form-control','id' => 'father_mobile',]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('father') . ' ' . __('dob') }}
                                        <span class="text-danger">*</span></label>
                                    {!! Form::text('father_dob', null, ['placeholder' =>  __('dd-mm-yyyy'),'class' => 'form-control datetimepicker form-control','id' => 'father_dob',]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('father') . ' ' . __('occupation') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('father_occupation', null, ['placeholder' => __('father') . ' ' . __('occupation'),'class' => 'form-control','id' => 'father_occupation',]) !!}
                                </div>

                                <div class="col-12 col-sm-12 col-md-4">
                                    <div class="form-group files">
                                        <label>{{ __('father') . ' ' . __('image') }}</label> <span class="text-danger">*</span>
                                        <div class="uplod">
                                            <label class="file-upload image-upbtn mb-0">Choose File <input name="father_image" type="file"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('mother_email') }} <span class="text-danger">*</span></label>
                                    <select class="mother-search w-100" id="mother_email" name="mother_email"></select>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('mother') . ' ' . __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('mother_first_name', null, ['placeholder' => __('mother') . ' ' . __('first_name'),'class' => 'form-control','id' => 'mother_first_name',]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('mother') . ' ' . __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('mother_last_name', null, ['placeholder' => __('mother') . ' ' . __('last_name'),'class' => 'form-control','id' => 'mother_last_name',]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('mother') . ' ' . __('mobile') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('mother_mobile', null, ['placeholder' => __('mother') . ' ' . __('mobile'),'class' => 'form-control','id' => 'mother_mobile',]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('mother') . ' ' . __('dob') }}<span class="text-danger">*</span></label>
                                    {!! Form::text('mother_dob', null, ['placeholder' => __('dd-mm-yyyy'),'class' => 'form-control datetimepicker form-control','id' => 'mother_dob',]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('mother') . ' ' . __('occupation') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('mother_occupation', null, ['placeholder' => __('mother') . ' ' . __('occupation'),'class' => 'form-control','id' => 'mother_occupation',]) !!}
                                </div>

                                <div class="col-12 col-sm-12 col-md-4">
                                    <div class="form-group files">
                                        <label>{{ __('mother') . ' ' . __('image') }}</label> <span class="text-danger">*</span>
                                        <div class="uplod">
                                            <label class="file-upload image-upbtn mb-0">Choose File <input name="mother_image" type="file"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="show-guardian-details">{{ __('guardian_details') }}
                                    </label>
                                </div>
                            </div>
                            <div class="row" id="guardian_div">
                                <div class="form-group col-sm-12 col-md-12 local-forms">
                                    <label>{{ __('guardian') . ' ' . __('email') }} <span class="text-danger">*</span></label>
                                    <select class="guardian-search form-control" id="guardian_email" name="guardian_email"></select>
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('guardian') . ' ' . __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('guardian_first_name', null, ['placeholder' => __('guardian') . ' ' . __('first_name'),'class' => 'form-control','id' => 'guardian_first_name',]) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('guardian') . ' ' . __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('guardian_last_name', null, ['placeholder' => __('guardian') . ' ' . __('last_name'),'class' => 'form-control','id' => 'guardian_last_name',]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('guardian') . ' ' . __('mobile') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('guardian_mobile', null, ['placeholder' => __('guardian') . ' ' . __('mobile'),'class' => 'form-control','id' => 'guardian_mobile',]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label>
                                    <br>
                                    <div class="form-group col-sm-12 col-md-12">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="guardian_gender" id="gender_male" value="male" checked>
                                            <label class="form-check-label" for="gender_male">{{ __('male') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="guardian_gender" id="gender_female" value="female">
                                            <label class="form-check-label" for="gender_female">{{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('guardian') . ' ' . __('dob') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('guardian_dob', null, ['placeholder' => __('dd-mm-yyyy'),'class' => 'form-control datetimepicker form-control','id' => 'guardian_dob',]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('guardian') . ' ' . __('occupation') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('guardian_occupation', null, ['placeholder' => __('guardian') . ' ' . __('occupation'),'class' => 'form-control','id' => 'guardian_occupation',]) !!}
                                </div>

                                <div class="col-12 col-sm-12 col-md-4">
                                    <div class="form-group files">
                                        <label>{{ __('guardian') . ' ' . __('image') }}</label>
                                        <span class="text-danger">*</span>
                                        <div class="uplod">
                                            <label class="file-upload image-upbtn mb-0">Choose File <input name="guardian_image" type="file"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
@endsection
