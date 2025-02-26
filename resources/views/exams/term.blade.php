@extends('layout.mastertrismeter')

@section('title')
    {{ __('Exam Terms') }}
@endsection
@php
    use App\Models\ClassSection;$class_sections = ClassSection::owner()->whereHas('class',function($q){
        $q->where('center_id',get_center_id());
        $q->activeMediumOnly();
    })->with('class.stream', 'section')->get();
@endphp
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('Manage Exam Terms') }}
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">{{ __('Exam Terms') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Manage Exam Terms') }}</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title">{{ __('Exam Terms') }}</h4>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTermModal">
                                <i class="fa fa-plus"></i> {{ __('Add Exam Term') }}
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Sequences') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('End Date') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($examTerms as $term)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <button class="btn btn-link p-0 me-2 toggle-btn" data-term-id="{{ $term->id }}">
                                                    <i class="fa fa-chevron-right"></i>
                                                </button>
                                                {{ $term->name }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $term->examSequences->count() }} sequences</span>
                                            <button class="btn btn-sm btn-outline-success add-sequence" data-term-id="{{ $term->id }}">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </td>
                                        <td>{{ $term->start_date }}</td>
                                        <td>{{ $term->end_date }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm  edit-button"
                                                        data-id="{{ $term->id }}"
                                                        data-name="{{ $term->name }}"
                                                        data-start_date="{{ $term->start_date }}"
                                                        data-end_date="{{ $term->end_date }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <form action="{{ url('exam-terms', $term->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm b delete-button"
                                                            data-id="{{ $term->id }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="sequence-details" id="sequence-{{ $term->id }}" style="display: none;">
                                        <td colspan="5">
                                            <div class="p-3 bg-light">
                                                <div class="sequence-management">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th>{{ __('Name') }}</th>
                                                            <th>{{ __('Start Date') }}</th>
                                                            <th>{{ __('End Date') }}</th>
                                                            <th>{{ __('Status') }}</th>
                                                            <th>{{ __('Actions') }}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($term->examSequences as $sequence)
                                                            <tr>
                                                                <td>{{ $sequence->name }}</td>
                                                                <td>{{ $sequence->start_date }}</td>
                                                                <td>{{ $sequence->end_date }}</td>

                                                                <td>
                                                                    <div class="toggle-switch-container">
                                                                        <label class="toggle-switch">
                                                                            <input type="checkbox"
                                                                                   class="status-toggle"
                                                                                   name="status-{{ $sequence->id }}"
                                                                                   {{ $sequence->status ? 'checked' : '' }}
                                                                                   value="1">
                                                                            <span class="slider round"></span>
                                                                        </label>
                                                                        <span class="status-text">{{ $sequence->status ? __('Active') : __('Inactive') }}</span>
                                                                    </div>


                                                                <style>
                                                                    .toggle-switch-container {
                                                                        display: flex;
                                                                        align-items: center;
                                                                        gap: 10px;
                                                                    }

                                                                    .toggle-switch {
                                                                        position: relative;
                                                                        display: inline-block;
                                                                        width: 60px;
                                                                        height: 34px;
                                                                    }

                                                                    .toggle-switch input {
                                                                        opacity: 0;
                                                                        width: 0;
                                                                        height: 0;
                                                                    }

                                                                    .slider {
                                                                        position: absolute;
                                                                        cursor: pointer;
                                                                        top: 0;
                                                                        left: 0;
                                                                        right: 0;
                                                                        bottom: 0;
                                                                        background-color: #dc3545;
                                                                        transition: .4s;
                                                                    }

                                                                    .slider:before {
                                                                        position: absolute;
                                                                        content: "";
                                                                        height: 26px;
                                                                        width: 26px;
                                                                        left: 4px;
                                                                        bottom: 4px;
                                                                        background-color: white;
                                                                        transition: .4s;
                                                                    }

                                                                    input:checked + .slider {
                                                                        background-color: #198754;
                                                                    }

                                                                    input:checked + .slider:before {
                                                                        transform: translateX(26px);
                                                                    }

                                                                    .slider.round {
                                                                        border-radius: 34px;
                                                                    }

                                                                    .slider.round:before {
                                                                        border-radius: 50%;
                                                                    }

                                                                    .status-text {
                                                                        font-weight: 500;
                                                                    }
                                                                </style>

                                                                <td>
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-sm edit-sequence-button"
                                                                                data-id="{{ $sequence->id }}"
                                                                                data-name="{{ $sequence->name }}"
                                                                                data-start_date="{{ $sequence->start_date }}"
                                                                                data-end_date="{{ $sequence->end_date }}"
                                                                                data-status="{{ $sequence->status }}"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#editSequenceModal">
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>
                                                                        <form action="{{ url('exam-sequences', $sequence->id) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm  delete-sequence-button"
                                                                                    data-id="{{ $sequence->id }}">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                                               </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Create Modal -->
    <div class="modal fade" id="addTermModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">{{ __('Create Exam Term') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form class="create-form' pt-3" id="AddTerms" action="{{ route('exam-terms.store') }}" enctype="multipart/form-data" method="POST" novalidate="novalidate">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12 local-forms">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('start_date') }} <span class="text-danger">*</span></label>
                                {!! Form::date('start_date', null, ['required', 'placeholder' => __('start_date'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('end_date') }} <span class="text-danger">*</span></label>
                                {!! Form::date('end_date', null, ['required', 'placeholder' => __('end_date'), 'class' => 'form-control']) !!}
                            </div>

                            <div class="form-group">
                                <label>{{ __('Sequence') }} </label>
                                <br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="sequence_name[]" id="seq1" value="Seq 1">
                                    <label class="form-check-label" for="seq1">Seq 1</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="sequence_name[]" id="seq2" value="Seq 2">
                                    <label class="form-check-label" for="seq2">Seq 2</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="sequence_name[]" id="seq3" value="Seq 3">
                                    <label class="form-check-label" for="seq3">Seq 3</label>
                                </div>
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="sequence_name[]" id="seq4" value="Seq 4">
                                        <label class="form-check-label" for="seq4">Seq 4</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="sequence_name[]" id="seq5" value="Seq 5">
                                        <label class="form-check-label" for="seq5">Seq 5</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="sequence_name[]" id="seq6" value="Seq 6">
                                        <label class="form-check-label" for="seq6">Seq 6</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" data-backdrop="static" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Edit Exam Terms') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="formdata" class="editform" action="{{ url('exam-terms') }}" novalidate="novalidate" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12 local-forms">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['id'=>'edit_name','required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('start_date') }} <span class="text-danger">*</span></label>
                                {!! Form::date('start_date', null, ['id'=>'edit_start_date','required', 'placeholder' => __('start_date'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('end_date') }} <span class="text-danger">*</span></label>
                                {!! Form::date('end_date', null, ['id'=>'edit_end_date','required', 'placeholder' => __('end_date'), 'class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-primary" type="submit" value={{ __('submit') }} />
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Sequence Modal -->
    <div class="modal fade" id="editSequenceModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editSequenceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSequenceModalLabel">{{ __('Edit Exam Sequence') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-close"></i></span>
                    </button>
                </div>
                <form id="editSequenceForm" class="edit-sequence-form-validate" action="{{ url('exam-sequences') }}" novalidate="novalidate" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <span>{{ __('Exam Term') }} : <b id="edit_exam_term_id"></b></span>
                        <div class="row mt-3">
                            <div class="form-group col-sm-12 col-md-12 local-forms">
                                <label>{{ __('name') }} <span class="text-danger">*</span></label>
                                {!! Form::text('name', null, ['id'=>'edit_name','required', 'placeholder' => __('name'), 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                {!! Form::text('start_date', null, ['required', 'placeholder' => __('Start Date'), 'class' => 'form-control datepicker start_date','autocomplete'=>'off','id'=>'edit-start-date']) !!}
                            </div>
                            <div class="form-group col-sm-12 col-md-6 local-forms">
                                <label>{{ __('End Date') }} <span class="text-danger">*</span></label>
                                {!! Form::text('end_date', null, ['required', 'placeholder' => __('End Date'), 'class' => 'form-control datepicker end_date','autocomplete'=>'off','id'=>'edit-end-date']) !!}
                            </div>

                            <div class="form-group col-sm-12 col-md-12" id="class_section_div">
                                <label>{{ __('class') }}<span class="text-danger">*</span></label>
                                <br>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="select-all-class-section"/>
                                        {{ __('Select All') }}
                                    </label>
                                </div>
                                <select name="class_section_id[]" id="class_section_id" class="class-section-in-sequence form-control select" multiple required>
                                    @foreach ($class_sections as $class_section)
                                        <option value="{{$class_section->id}}">
                                            {{ isset($class_section->class) ? $class_section->class->full_name : 'Class Name Not Available' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-sm-12 col-md-12">
                                <label>{{ __('status') }} <span class="text-danger">*</span></label>
                                <br>
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('status', '1',false,['id' => 'edit-active','required'=>true]) !!}
                                            {{ __('Active') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            {!! Form::radio('status', '0',false ,['id' => 'edit-inactive','required'=>true]) !!}
                                            {{ __('Inactive') }}
                                        </label>
                                    </div>
                                </div>
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

@section('script')
    <script>
        $(document).ready(function() {
            $(document).ready(function() {
                // Set up edit sequence modal
                //Start chevron dropdown
                // Toggle sequence details
                $('.toggle-btn').on('click', function() {
                    var termId = $(this).data('term-id');
                    $('#sequence-' + termId).toggle();
                    $(this).find('i').toggleClass('fa-chevron-right fa-chevron-down');
                });
// star toggle status
                $(document).on('change', '.status-toggle', function() {
                    var sequenceId = $(this).attr('name').split('-')[1];
                    var newStatus = this.checked ? 1 : 0;
                    var statusText = $(this).closest('.toggle-switch-container').find('.status-text');

                    $.ajax({
                        url: '/exam-sequences/' + sequenceId + '/status',
                        type: 'POST',
                        data: {
                            status: newStatus,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            showNotification(response.message || 'Status updated successfully');
                            statusText.text(newStatus ? 'Active' : 'Inactive');
                        },
                        error: function(xhr) {
                            console.error('Error updating status:', xhr.responseJSON);
                            showNotification(xhr.responseJSON?.message || 'An error occurred', 'error');
                            // Revert the toggle if there's an error
                            $(this).prop('checked', !newStatus);
                            statusText.text(!newStatus ? 'Active' : 'Inactive');
                        }
                    });
                });
            });
        });
//Edit Sequence start here


    </script>
@endsection
