@extends('layout.master')

@section('title')
    {{ __('Income') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Income Category') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create_new_income_category') }}
                        </h4>
                        <form class="create-form pt-3" id="formdata" action="{{ url('accounting/income-category') }}"
                              method="POST"
                              novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4 local-forms">
                                    <label>{{ __('title') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('title', null, ['required', 'placeholder' => __('title'),
                                    'class' => 'form-control']) !!}
                                </div>

                                <div class="form-group  col-sm-12 col-md-4 local-forms">
                                    <label for="">{{__('medium')}}</label>
                                    {!! Form::select('medium', $mediums , null, ['class' =>
                                    'form-control select', 'id' => 'category']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-9 local-forms">
                                    <label>{{ __('description') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('description', null, ['required', 'placeholder' => __('
                                    Description'),
                                    'class' => 'form-control']) !!}
                                </div>
                            </div>

                            <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list_income_categories') }}
                        </h4>

                        <div id="toolbar">
                        </div>

                        <div class="row">
                            <div class="col-12">

                                @php
                                    $url = route('income-category.show', 'show');
                                    $columns = [
                                        trans('no') => ['data-field' => 'no'],
                                        trans('id') => ['data-field' => 'id', 'data-visible' => false],
                                        trans('title') => ['data-field' => 'title'],
                                        trans('description') => ['data-field' => 'description'],
                                        trans('slug') => ['data-field' => 'slug'],
                                        trans('medium') => ['data-field' => 'medium'],
                                        trans('created_at' ) => ['data-field' => 'created_at']
                                    ];
                                    $actionColumn = [
                                        'editButton' => ['url' => url('/accounting/income-category')],
                                        'deleteButton' => ['url' => url('/accounting/income-category')],
                                        'data-events' => 'expenseEvents',
                                    ];
                                @endphp
                                <x-bootstrap-table :url=$url :columns=$columns :actionColumn=$actionColumn
                                                   queryParams="expenseQueryParams">
                                </x-bootstrap-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Edit Income Category') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form class="editform" action="" novalidate="novalidate" enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="id">

                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Title') }} <span class="text-danger">*</span></label>
                                {!! Form::text('title', null, [
                                    'id' => 'title',
                                    'required',
                                    'placeholder' => __('Title'),
                                    'class' => 'form-control',
                                ]) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Description') }} <span class="text-danger">*</span></label>
                                {!! Form::text('description', null, [
                                    'id' => 'description',
                                    'required',
                                    'placeholder' => __('description'),
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-primary" type="submit" value={{ __('submit') }}>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
