@extends('layout.master')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>{{ __('Enter Competency Marks') }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('Competency Marks') }}</li>
            </ol>
        </nav>
    </div>

    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Sequence') }} <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="sequence_id" required>
                        <option value="">{{ __('Select Sequence') }}</option>
                        @foreach($sequences as $sequence)
                            <option value="{{ $sequence->id }}">{{ $sequence->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">{{ __('Please select a sequence') }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Class') }} <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="class_id" required disabled>
                        <option value="">{{ __('Select Class') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">{{ __('Please select a class') }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Competency') }} <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="competency_id" required disabled>
                        <option value="">{{ __('Select Competency') }}</option>
                        {{-- @foreach($competencies as $competency)
                            <option value="{{ $competency->id }}">{{ $competency->name }}</option>
                        @endforeach --}}
                    </select>
                    <div class="invalid-feedback">{{ __('Please select a competency') }}</div>
                </div>
            </div>
        </div>

        <div class="marks-table d-none">
            <form id="marks-form" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="competency_id" id="form_competency_id">
                <input type="hidden" name="sequence_id" id="form_sequence_id">
                <input type="hidden" name="class_id" id="form_class_id">
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-responsive">
                        <thead>
                            <tr>
                                <th style="width: 50px">#</th>
                                <th>{{ __('Student') }}</th>
                                <th class="competency-types-header"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="100%" class="text-center">{{ __('Select all fields above to load students') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4">
                    
                    <div class="col-md-6 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            {{ __('Save Marks') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Initialisation de Select2
    $('.select2').select2();

    // Désactiver initialement les champs dépendants
    $('#competency_id').prop('disabled', true);
    $('#class_id').prop('disabled', true);

    // Variables globales pour stocker les données de compétence
    let competencyTypes = [];
    let totalMaxMarks = 0;

    // Fonction pour calculer le total des notes
    function calculateTotal(row) {
        let total = 0;
        row.find('input[type="number"]').each(function() {
            const value = parseFloat($(this).val()) || 0;
            total += value;
        });
        row.find('.total-marks').text(total.toFixed(2));

        // Mettre en évidence si le total dépasse le maximum
        if (total > totalMaxMarks) {
            row.addClass('table-danger');
        } else {
            row.removeClass('table-danger');
        }
    }

    // Fonction pour mettre à jour le tableau des notes
    function updateMarksTable(data) {
        // Stocker les types de compétence
        competencyTypes = data.competency?.types || [];
        
        // Calculer le total des notes maximum
        totalMaxMarks = competencyTypes.reduce((sum, type) => parseFloat(sum) + parseFloat(type.total_marks), 0);

        // Réinitialiser l'en-tête du tableau
        $('thead tr').html(`
            <th style="width: 50px">#</th>
            <th>{{ __('Student') }}</th>
        `);

        // Mettre à jour l'en-tête du tableau avec les types de compétences
        let headerHtml = '';
        
        competencyTypes.forEach(type => {
            headerHtml += `<th class="text-center">
                ${type.name}
                <br>
                <small class="text-muted">(/${type.total_marks})</small>
            </th>`;
        });
        
        // Ajouter la colonne du total
        headerHtml += `<th class="text-center" style="width: 100px">
            {{ __('Total') }}
            <br>
            <small class="text-muted">/${totalMaxMarks}</small>
        </th>`;

        // Ajouter directement les colonnes à la ligne d'en-tête
        $('thead tr').append(headerHtml);

        // Générer les lignes du tableau
        let html = '';
        if (data.students && data.students.length > 0) {
            data.students.forEach((student, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${student.full_name}</td>
                `;

                // Ajouter les champs de notes pour chaque type
                competencyTypes.forEach(type => {
                    // Récupérer la note existante pour ce type de compétence
                    const existingMark = student.marks ? student.marks[type.id] : null;
                    const value = existingMark ? existingMark.mark : '';

                    html += `
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="number" 
                                       name="marks[${student.id}][${type.id}]" 
                                       class="form-control text-end mark-input"
                                       value="${value}"
                                       min="0"
                                       max="${type.total_marks}"
                                       step="0.25"
                                       required>
                            </div>
                        </td>
                    `;
                });

                // Ajouter la cellule du total
                html += `
                        <td class="text-center">
                            <span class="total-marks fw-bold">${student.total || '0.00'}</span>
                        </td>
                    </tr>
                `;
            });
        } else {
            html = `
                <tr>
                    <td colspan="${2 + competencyTypes.length + 1}" class="text-center">
                        <i class="fas fa-user-slash me-2"></i>
                        {{ __('No students found') }}
                    </td>
                </tr>
            `;
        }
        $('.marks-table tbody').html(html);

        // Ajouter les écouteurs d'événements pour le calcul des totaux
        $('.mark-input').on('input', function() {
            calculateTotal($(this).closest('tr'));
        });

        // Calculer les totaux initiaux
        $('.marks-table tbody tr').each(function() {
            calculateTotal($(this));
        });
    }

    // Gestionnaire pour le changement de trimestre
    $('#term_id').on('change', function() {
        const termId = $(this).val();
        $('#form_term_id').val(termId);
        
        if (!termId) {
            // Réinitialiser et désactiver les champs dépendants
            $('#sequence_id').prop('disabled', true).val('').trigger('change');
            $('#competency_id').prop('disabled', true).val('').trigger('change');
            $('#class_id').prop('disabled', true).val('').trigger('change');
            $('.marks-table').addClass('d-none');
            return;
        }

        // Activer le champ sequence
        $('#sequence_id').prop('disabled', false);
    });

    // Gestionnaire pour le changement de séquence
    $('#sequence_id').on('change', function() {
        const sequenceId = $(this).val();
        $('#form_sequence_id').val(sequenceId);
        
        if (!sequenceId) {
            $('#competency_id').prop('disabled', true).val('').trigger('change');
            $('#class_id').prop('disabled', true).val('').trigger('change');
            $('.marks-table').addClass('d-none');
            return;
        }

        // Activer le champ classe
        $('#class_id').prop('disabled', false);
        
        // Recharger les données si tous les champs requis sont remplis
        if ($('#class_id').val() && $('#competency_id').val()) {
            loadMarks();
        }
    });

    // Gestionnaire pour le changement de classe
    $('#class_id').on('change', function() {
        const classId = $(this).val();
        $('#form_class_id').val(classId);

        if (!classId) {
            $('#competency_id').prop('disabled', true).val('').trigger('change');
            $('.marks-table').addClass('d-none');
            return;
        }

        // Charger les competences de la classe
        loadCompetenciesList();
        
        // Activer le champ compétence
        $('#competency_id').prop('disabled', false);
        
        // Recharger les données si la compétence est déjà sélectionnée
        if ($('#competency_id').val()) {
            loadMarks();
        }
    });

    // Gestionnaire pour le changement de compétence
    function handleCompetencyChange() {
        const competencyId = $(this).val();
        $('#form_competency_id').val(competencyId);
        
        if (!competencyId) {
            $('.marks-table').addClass('d-none');
            return;
        }

        // Activer le champ classe si présent
        $('#class_id').prop('disabled', false);
        
        // Recharger les données si tous les champs requis sont remplis
        if (!$('#class_id').length || $('#class_id').val()) {
            loadMarks();
        }
    };

    $('#competency_id').on('change', handleCompetencyChange);

    // Gestionnaire pour le changement de classe
    $('#class_id').on('change', function() {
        const classId = $(this).val();
        $('#form_class_id').val(classId);

        if (!classId) {
            $('.marks-table').addClass('d-none');
            return;
        }
        
        // Recharger les données si tous les champs requis sont remplis
        if ($('#sequence_id').val() && $('#competency_id').val()) {
            loadMarks();
        }
    });

    function loadCompetenciesList() {
        $.ajax({
            url: route('competency.index'),
            data: {
                class_id: $('#class_id').val()
            },
            success: function (response) {

                let optionsHtml = `
                <option value="">@lang('Select Competency')</option>
                `;

                response.competencies.forEach(competency => {
                    optionsHtml += `
                    <option value="${competency.id}">${competency.name}</option>
                    `;
                });

                $('#competency_id').html(optionsHtml);
                $('#competency_id').on('change', handleCompetencyChange);
            },
            error: function (error) {
                console.log(error.message);
                
            }
        });
    }

    // Fonction pour charger les notes
    function loadMarks() {
        // Réinitialiser le tableau avant de charger
        $('.marks-table tbody').empty();
        competencyTypes = [];
        totalMaxMarks = 0;

        const sequenceId = $('#sequence_id').val();
        const competencyId = $('#competency_id').val();
        const classId = $('#class_id').val();

        if (!sequenceId || !competencyId) {
            return;
        }

        // Afficher un indicateur de chargement
        $('.marks-table').removeClass('d-none');
        $('.marks-table tbody').html(`
            <tr>
                <td colspan="${2 + (competencyTypes.length || 0) + 1}" class="text-center">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="spinner-border text-primary me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        {{ __('Loading students...') }}
                    </div>
                </td>
            </tr>
        `);

        // Effectuer la requête AJAX
        $.ajax({
            url: "{{ route('competency.marks.students') }}",
            method: 'GET',
            data: {
                sequence_id: sequenceId,
                competency_id: competencyId,
                class_id: classId ?? null
            },
            success: function(response) {
                updateMarksTable(response);
            },
            error: function(xhr) {
                $('.marks-table tbody').html(`
                    <tr>
                        <td colspan="${2 + (competencyTypes.length || 0) + 1}" class="text-center text-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ __('An error occurred while loading the data') }}
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Validation du formulaire avant soumission
    $('#marks-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        // Désactiver le bouton de soumission
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            {{ __('Saving...') }}
        `);

        $.ajax({
            url: "{{ route('competency.marks.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Afficher une notification de succès
                toastr.success("{{ __('Marks have been saved successfully') }}");
                
                // Recharger les notes pour afficher les mises à jour
                loadMarks();
            },
            error: function(xhr) {
                // Afficher une notification d'erreur
                toastr.error(xhr.responseJSON?.message || "{{ __('An error occurred while saving the marks') }}");
            },
            complete: function() {
                // Réactiver le bouton de soumission
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endsection