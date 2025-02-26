@extends('layout.master')
@section('title')
    {{ __('Course Category') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Course Category') }}
            </h3>
        </div>
        @if (Auth::user()->hasRole('Super Admin'))
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                                {{ __('Edit Course Category') }}
                            </h4>
                            {!! Form::model($category, [
                                'route' => ['course_category.update', $category->id],
                                'method' => 'PUT',
                                'enctype' => 'multipart/form-data',
                            ]) !!}
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Course name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('name', null, ['required', 'placeholder' => __('Name'), 'class' => 'form-control']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-6 local-forms">
                                    <label>{{ __('Description') }} <span class="text-danger">*</span></label>
                                    {!! Form::textarea('description', null, [
                                        'required',
                                        'placeholder' => __('Description'),
                                        'class' => 'form-control',
                                    ]) !!}
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <label for="">{{__("image")}}</label>
                                    {!! Form::file('thumbnail', ['class' => 'form-control']) !!}
                                    <img src="{{ $category->thumbnail }}" class="mt-2" height="120" alt="">
                                </div>

                            </div>

                            <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('js')
    <script src="{{ asset('/assets/js/custom/form-repeater.js') }}"></script>
@endsection
