@extends('layout.master')

@section('content')
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>@lang('Learning Units')</h3>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fa fa-plus"></i> @lang('Add New')
                </button>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Class')</th>
                            <th>@lang('Exam Term')</th>
                            <th>@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($learningUnits as $unit)
                            <tr>
                                <td>{{ $unit->name }}</td>
                                <td>{{ $unit->class->name }}</td>
                                <td>{{ $unit->exam_term->name }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-warning edit-btn"
                                                data-id="{{ $unit->id }}"
                                                data-name="{{ $unit->name }}"
                                                data-class="{{ $unit->class_id }}"
                                                data-term="{{ $unit->exam_term_id }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <form action="{{ route('learning-units.destroy', $unit->id) }}" 
                                              method="POST" 
                                              class="delete-form d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('@lang('Are you sure?')')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">@lang('No data available')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add Learning Unit')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createForm" action="{{ route('learning-units.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('competency.learning_unit._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn-primary">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Edit Learning Unit')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @include('competency.learning_unit._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn-primary">@lang('Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Create Form Submit
        $('#createForm').submit(function(e) {
            e.preventDefault();
            submitForm($(this), '#createModal');
        });

        // Edit Button Click
        $('.edit-btn').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const classId = $(this).data('class');
            const termId = $(this).data('term');

            const form = $('#editForm');
            form.attr('action', `/learning-units/${id}`);
            form.find('[name="name"]').val(name);
            form.find('[name="class_id"]').val(classId);
            form.find('[name="exam_term_id"]').val(termId);

            $('#editModal').modal('show');
        });

        // Edit Form Submit
        $('#editForm').submit(function(e) {
            e.preventDefault();
            submitForm($(this), '#editModal');
        });

        function submitForm(form, modalId) {
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    if(response.success) {
                        $(modalId).modal('hide');
                        alertify.success(response.message);
                        // toastr.success(response.message);
                        // location.reload();
                    }
                },
                error: function(xhr) {
                    alertify.error(xhr.responseJSON.message || '@lang("An error occurred")');
                    // toastr.error(xhr.responseJSON.message || '@lang("An error occurred")');
                }
            });
        }
    });
</script>
@endpush