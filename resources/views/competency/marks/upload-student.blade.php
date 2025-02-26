@extends('layout.master')

@section('title')
    {{ __('Upload Student Marks') }}
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>{{ __('upload_student_marks') }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('dashboard') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('upload_student_marks') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Sequence') }} <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="sequence_id" name="sequence_id" required>
                        <option value="">{{ __('select_sequence') }}</option>
                        @foreach($sequences as $sequence)
                            <option value="{{ $sequence->id }}">{{ $sequence->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Class') }} <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="class_id" name="class_id" required disabled>
                        <option value="">{{ __('select_class') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Student') }} <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="student_id" name="student_id" required disabled>
                        <option value="">{{ __('select_student') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <form id="marksForm" method="POST" action="{{ route('competency.marks.update') }}" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" name="sequence_id" id="form_sequence_id">
            <input type="hidden" name="class_id" id="form_class_id">
            <input type="hidden" name="student_id" id="form_student_id">
            
            <div class="table-responsive"  id="marksTable">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Competency') }}</th>
                            <th>{{ __('Types') }}</th>
                            <th class="text-center" style="width: 150px;">{{ __('Marks') }}</th>
                        </tr>
                    </thead>
                    <tbody id="competencyMarksBody">
                        <tr><td colspan="100%" class="text-center"> <i class="fa fa-book me-2"></i> {{ __("no_data") }} {{ __("select_options_to_load_data") }}</td></tr>
                    </tbody>
                </table>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ __('save_marks') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<link rel="stylesheet" href="{{ asset('assets/css/jquery.resizableColumns.css') }}">
<script src="{{ asset('assets/js/jquery.resizableColumns.js') }}" defer></script>
<script>
$(document).ready(function() {
    // Initialiser Select2
    $('.select2').select2();

    $('.table').resizableColumns();

    // Gérer les changements de sélection
    $('#sequence_id').on('change', function() {
        const sequenceId = $(this).val();
        $('#form_sequence_id').val(sequenceId);
        
        if (sequenceId) {
            $('#class_id').prop('disabled', false);
            reloadTableData();
        } else {
            $('#class_id').prop('disabled', true);
            $('#student_id').prop('disabled', true);
            hideTable();
        }
    });

    $('#class_id').on('change', function() {
        const classId = $(this).val();
        $('#form_class_id').val(classId);
        
        if (classId) {
            loadStudents(classId);
            reloadTableData();
        } else {
            $('#student_id').prop('disabled', true).empty().append('<option value="">{{ __("select_student") }}</option>');
            hideTable();
        }
    });

    $('#student_id').on('change', function() {
        const studentId = $(this).val();
        $('#form_student_id').val(studentId);
        
        if (studentId) {
            reloadTableData();
        } else {
            hideTable();
        }
    });

    // Charger la liste des élèves
    function loadStudents(classId) {
        const sequenceId = $('#sequence_id').val();
        $.ajax({
            url: "{{ route('competency.marks.students-list') }}",
            data: { class_id: classId, sequence_id: sequenceId },
            success: function(response) {
                const studentSelect = $('#student_id');
                studentSelect.empty().append('<option value="">{{ __("Student") }}</option>');
                
                response.students.forEach(function(student) {
                    studentSelect.append(`<option value="${student.id}">${student.full_name}</option>`);
                });
                
                studentSelect.prop('disabled', false);
                toastr.success("{{ __('alert_students_loaded_successfully') }}");
            },
            error: function() {
                toastr.error("{{ __('alert_error_loading_students') }}");
            }
        });
    }

    // Cacher le tableau et réinitialiser son contenu
    function hideTable() {
        // $('#marksTable').addClass();
        $('#competencyMarksBody').empty().append('<tr><td colspan="3" class="text-center">{{ __("no_data") }} {{ __("select_options_to_load_data") }}</td></tr>');
    }

    // Recharger les données du tableau
    function reloadTableData() {
        const sequenceId = $('#sequence_id').val();
        const classId = $('#class_id').val();
        const studentId = $('#student_id').val();

        if (!sequenceId || !classId || !studentId) {
            hideTable();
            return;
        }

        // Afficher un indicateur de chargement
        $('#competencyMarksBody').html('<tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>{{ __("loading") }}</td></tr>');
        $('#marksTable').removeClass('d-none');

        $.ajax({
            url: "{{ route('competency.marks.class-competencies') }}",
            data: {
                sequence_id: sequenceId,
                class_id: classId,
                student_id: studentId
            },
            success: function(response) {
                if (!response.error) {
                    renderCompetencyMarks(response.competencies, response.marks);
                } else {
                    toastr.error(response.message);
                    hideTable();
                }
            },
            error: function() {
                toastr.error("{{ __('alert_error_loading_competencies') }}");
                hideTable();
            }
        });
    }

    // Afficher les compétences et notes dans le tableau
    function renderCompetencyMarks(competencies, marks) {
        const tbody = $('#competencyMarksBody');
        tbody.empty();

        if (competencies.length === 0) {
            tbody.html('<tr><td colspan="3" class="text-center">{{ __("alert_no_competencies_found") }}</td></tr>');
            return;
        }

        competencies.forEach(function(competency) {
            let typeInputs = '';
            let totalMarks = 0;
            let studentId = $('#student_id').val();
            
            console.log('Competency', competency);
            competency.competency_types.forEach(function(type) {
                console.log('Type', type);
                const maxMarks = type.pivot.total_marks || 20;
                const mark = (marks && marks[competency.competency.id] && marks[competency.competency.id]['marks'] && 
                            marks[competency.competency.id]['marks'][type.id]) ? marks[competency.competency.id]['marks'][type.id].mark : 0;
                
                            console.log('MK', mark);
                totalMarks += parseFloat(mark);

                typeInputs += `
                    <div class="mb-2">
                        <label class="form-label small d-inline">${type.name} (/${maxMarks})</label>
                        <input type="number" 
                               class="form-control form-control-sm d-inline"
                               name="marks[${competency.competency.id}][${type.id}]"
                               value="${mark}"
                               min="0"
                               max="${maxMarks}"
                               step="0.25"
                               required>
                    </div>`;
            });

            tbody.append(`
                <tr>
                        <td>${competency.competency.name}</td>
                        <td>${typeInputs}</td>
                    <td class="text-center align-middle">
                        <span class="total-marks">${totalMarks.toFixed(2)}</span>
                    </td>
                </tr>
            `);
        });
    }

    // Mettre à jour les totaux quand les notes changent
    $(document).on('input', 'input[type="number"]', function() {
        updateTotals();
    });

    function updateTotals() {
        $('#competencyMarksBody tr').each(function() {
            let total = 0;
            $(this).find('input[type="number"]').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $(this).find('.total-marks').text(total.toFixed(2));
        });
    }

    // Validation du formulaire
    $('#marksForm').on('submit', function(e) {
        e.preventDefault();
        
        if (this.checkValidity()) {
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>{{ __("saving") }}');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (!response.error) {
                        toastr.success(response.message);
                        reloadTableData(); // Recharger les données après la sauvegarde
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error("{{ __('Error saving marks') }}");
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>{{ __("save_marks") }}');
                }
            });
        }
        
        $(this).addClass('was-validated');
    });
});
</script>
@endsection