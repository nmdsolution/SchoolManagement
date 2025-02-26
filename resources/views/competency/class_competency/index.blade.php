@extends('layout.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>{{ __('Class Competencies') }}</h4>
        {{-- <div class="card-header-action">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignCompetencyModal">
                <i class="fas fa-plus"></i> {{ __('Assign Competency') }}
            </button>
        </div> --}}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="competencyTable">
                <thead>
                    <tr>
                        <th>{{ __('Class') }}</th>
                        <th>{{ __('Competencies') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $class)
                    <tr>
                        <td>{{ $class->name }}</td>
                        <td>
                            @if($class->competencies->isEmpty())
                                <div class="text-muted fst-italic">
                                    {{ __('No competencies assigned to this class') }}
                                </div>
                            @else
                            <ul class="list-unstyled">
                                @foreach($class->competencies as $competency)
                                <li>
                                    {{ $competency->name }}
                                    <button class="btn btn-sm btn-danger rounded-circle remove-competency" 
                                            data-class-id="{{ $class->id }}"
                                            data-competency-id="{{ $competency->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </td>
                        <td>
                            <button type="button" 
                                    class="btn btn-primary edit-competencies"
                                    data-class-id="{{ $class->id }}"
                                    data-class-name="{{ $class->name }}"
                                    data-competencies="{{ $class->competencies->isNotEmpty() ? $class->competencies->pluck('id')->join(',') : '' }}">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal d'association de compétence -->
<div class="modal fade" id="assignCompetencyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Assign Competency to Class') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignForm">
                @csrf
                <input type="hidden" name="edit_mode" value="0">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>{{ __('Class') }}</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">{{ __('Select Class') }}</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>{{ __('Competencies') }}</label>
                        <select name="competency_ids[]" class="form-control select2" multiple required>
                            @foreach($competencies as $competency)
                            <option value="{{ $competency->id }}">{{ $competency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('.select2').select2({
        dropdownParent: $('#assignCompetencyModal')
    });

    // Gestionnaire pour le bouton d'édition
    $('.edit-competencies').on('click', function() {
        const classId = $(this).data('class-id');
        const className = $(this).data('class-name');
        const competencies = $(this).data('competencies');

        console.log(classId, className, competencies);
        // Remplir le formulaire
        $('input[name="edit_mode"]').val('1');
        $('select[name="class_id"]').val(classId).trigger('change');
        const competencyIds = typeof competencies === 'string' ? competencies.split(',') : [competencies.toString()];
        $('select[name="competency_ids[]"]').val(competencyIds).trigger('change');
        
        // Ouvrir le modal
        $('#assignCompetencyModal').modal('show');
    });

    $('#assignForm').on('submit', function(e) {
        e.preventDefault();
        
        const isEdit = $('input[name="edit_mode"]').val() === '1';
        const classId = $('select[name="class_id"]').val();
        const url = isEdit 
            ? `/class-competency/${classId}`
            : "{{ route('class-competency.store') }}";
        const method = isEdit ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                if (!response.error) {
                    location.reload();
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON.message);
            }
        });
    });

    // Réinitialiser le formulaire quand le modal est fermé
    $('#assignCompetencyModal').on('hidden.bs.modal', function () {
        $('#assignForm')[0].reset();
        $('input[name="edit_mode"]').val('0');
        $('select[name="competency_ids[]"]').val(null).trigger('change');
    });

    $('.remove-competency').on('click', function() {
        if (!confirm("{{ __('Are you sure?') }}")) return;

        const classId = $(this).data('class-id');
        const competencyId = $(this).data('competency-id');

        $.ajax({
            url: `/class-competency/${classId}`,
            method: 'DELETE',
            data: {
                competency_id: competencyId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON.message);
            }
        });
    });
});
</script>
@endsection