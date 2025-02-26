@extends('layout.master')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>{{ __('Edit Competency Marks') }} - {{ $student->full_name }}</h4>
        <a href="{{ route('competency.marks.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>{{ __('Back') }}
        </a>
    </div>

    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Class') }}</label>
                    <select class="form-control select2" id="class_id" required>
                        <option value="">{{ __('Select Class') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Student') }}</label>
                    <select class="form-control select2" id="student_id" required disabled>
                        <option value="">{{ __('Select Student') }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">{{ __('Sequence') }}</label>
                    <select class="form-control select2" id="sequence_id" required disabled>
                        <option value="">{{ __('Select Sequence') }}</option>
                        @foreach($sequences as $sequence)
                            <option value="{{ $sequence->id }}">{{ $sequence->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <form id="marks-form" class="needs-validation d-none" novalidate>
            @csrf
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('Competency') }}</th>
                            @foreach($competencies->first()->competency_types as $type)
                                <th class="text-center">
                                    {{ $type->name }}<br>
                                    <small class="text-muted">(/50)</small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($competencies as $competency)
                            <tr>
                                <td>{{ $competency->name }}</td>
                                @foreach($competency->competency_types as $type)
                                    <td>
                                        <input type="number" 
                                               name="marks[{{ $competency->id }}][{{ $type->id }}]" 
                                               class="form-control form-control-sm text-end"
                                               min="0"
                                               max="50"
                                               step="0.25"
                                               required>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('.select2').select2();

    // Charger les élèves quand la classe change
    $('#class_id').on('change', function() {
        const classId = $(this).val();
        $('#student_id').prop('disabled', true).empty().append('<option value="">{{ __("Select Student") }}</option>');
        $('#sequence_id').prop('disabled', true);
        $('#marks-form').addClass('d-none');
        
        if (!classId) return;

        $.get(`{{ url('students/by-class') }}/${classId}`, function(response) {
            const students = response.sort((a, b) => a.full_name.localeCompare(b.full_name));
            students.forEach(student => {
                $('#student_id').append(new Option(student.full_name, student.id));
            });
            $('#student_id').prop('disabled', false);
        });
    });

    // Activer la séquence quand un élève est sélectionné
    $('#student_id').on('change', function() {
        const studentId = $(this).val();
        $('#sequence_id').prop('disabled', !studentId);
        $('#marks-form').addClass('d-none');
    });

    // Charger les notes quand la séquence change
    $('#sequence_id').on('change', function() {
        const sequenceId = $(this).val();
        const studentId = $('#student_id').val();
        if (!sequenceId || !studentId) return;

        // Réinitialiser le formulaire
        $('input[type="number"]').val('');
        $('#marks-form').removeClass('d-none');

        // Charger les notes existantes
        $.get(`{{ url('competency-marks/get-marks') }}/${sequenceId}/${studentId}`, function(response) {
            if (response.marks) {
                Object.keys(response.marks).forEach(competencyId => {
                    Object.keys(response.marks[competencyId]).forEach(typeId => {
                        const mark = response.marks[competencyId][typeId];
                        $(`input[name="marks[${competencyId}][${typeId}]"]`).val(mark);
                    });
                });
            }
        });
    });

    // Soumettre le formulaire
    $('#marks-form').on('submit', function(e) {
        e.preventDefault();
        
        const sequenceId = $('#sequence_id').val();
        if (!sequenceId) {
            toastr.error("{{ __('Please select a sequence') }}");
            return;
        }

        $.ajax({
            url: `{{ route('competency.marks.update', $student->id) }}`,
            method: 'POST',
            data: $(this).serialize() + `&sequence_id=${sequenceId}`,
            success: function(response) {
                if (!response.error) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || "{{ __('An error occurred') }}");
            }
        });
    });
});
</script>
@endsection 