@extends('layout.master')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>@lang('assign_competency_type')</h3>
                        <x-ls::button triggers_modal="clone-competency-modal" class="btn btn-primary" :label="trans('clone_competency')" />
                </div>
                <div class="card-body">
                    {{ html()->label(__('select_class'))->for('class_id') }}
                    {{ html()->select(
                            'class_id', 
                            $classes->pluck('name', 'id')->prepend('-- '. __('select_class') .' --', 0)
                        )->class('form-control select2') 
                    }}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="table_list">
                        <thead>
                        <tr>
                            <th scope="col">@lang('Name')</th>
                            <th scope="col">@lang('Competency Types')</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

        <x-ls::modal id="clone-competency-modal" :title="__('clone_class_competency')">
            <x-ls::form action="{{ route('competency.clone') }}" method="POST" id="clone-competency-form">
                <x-ls::select name="source_class_id" id="source_class_id" :label="trans('source_class')" :options="$classes->pluck('name', 'id')->prepend('-- '. __('select_class') .' --', 0)" />
                <x-ls::select name="target_class_id" id="target_class_id" :label="trans('target_class')" :options="$classes->pluck('name', 'id')->prepend('-- '. __('select_class') .' --', 0)" />
            </x-ls::form>
        </x-ls::modal>

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#class_id').change(function() {
                const class_id = $(this).val();
                $.ajax({
                    url: "{{ route('competency-type.competency-list') }}",
                    method: 'POST',
                    data: { class_id: class_id },
                    success: function(data) {
                        let html = '';
                        data.competencies.forEach(function(competency) {
                            let competencyTypesHtml = '<form class="competency-type-form">';

                                console.log('competency', competency);
                                
                            
                            // Récupération des types déjà associés
                            const assignedTypes = competency.competency_types.map(type => ({
                                id: type.id,
                                total_marks: type.pivot.total_marks
                            }));

                            // Création des lignes pour chaque type de compétence
                            data.competencyTypes.forEach(function(type) {
                                const isAssigned = assignedTypes.find(t => t.id === type.id);
                                const totalMarks = isAssigned ? isAssigned.total_marks : 0;

                                competencyTypesHtml += `
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-2">
                                            <input type="checkbox" 
                                                   name="types[${type.id}][assigned]" 
                                                   class="form-check-input"
                                                   ${isAssigned ? 'checked' : ''}
                                                   value="1">
                                        </div>
                                        <div class="me-2">
                                            <label class="form-check-label">${type.name}</label>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="number" 
                                                   name="types[${type.id}][total_marks]"
                                                   class="form-control form-control-sm" 
                                                   placeholder="Total Marks"
                                                   value="${totalMarks}"
                                                   ${!isAssigned ? 'disabled' : ''}>
                                        </div>
                                    </div>
                                `;
                            });

                            competencyTypesHtml += `
                                <input type="hidden" name="competency_id" value="${competency.competency.id}">
                                <input type="hidden" name="class_id" value="${class_id}">
                                <button type="submit" class="btn btn-primary btn-sm mt-2">Enregistrer</button>
                            </form>`;
                            
                            html += `
                                <tr>
                                    <td>${competency.competency.name}</td>
                                    <td>${competencyTypesHtml}</td>
                                </tr>`;
                        });

                        if (html === '') {
                            html = '<tr><td colspan="2" class="text-center">@lang("no_data")</td></tr>';
                        }
                        
                        $('#table_list tbody').html(html);

                        // Gestion des checkboxes pour activer/désactiver les inputs
                        $('.competency-type-form input[type="checkbox"]').change(function() {
                            const totalMarksInput = $(this).closest('.d-flex').find('input[type="number"]');
                            totalMarksInput.prop('disabled', !this.checked);
                            if (!this.checked) {
                                totalMarksInput.val('');
                            }
                        });

                        // Gestion de la soumission des formulaires
                        $('.competency-type-form').on('submit', function(e) {
                            e.preventDefault();
                            const formData = $(this).serializeArray();
                            console.log('FormData', formData);
                            
                            
                            $.ajax({
                                url: '{{ route('competency-type.assign-class-store') }}',
                                method: 'POST',
                                data: formData,
                                success: function(response) {
                                    if (!response.error) {
                                        toastr.success(response.message);
                                    } else {
                                        toastr.error(response.message);
                                    }
                                }
                            });
                        });

                    }
                });
            });

            $('#source_class_id').change(function() {
                $('#target_class_id option[value="'+$(this).val()+'"]').remove();
            });

            $('#clone-competency-form').on('submit', function(e) {
                e.preventDefault();
                // $(this).submit();
                const source_class_id = $('#source_class_id').val();
                const target_class_id = $('#target_class_id').val();

                if (!source_class_id || !target_class_id) {
                    Swal.fire({
                        icon: 'error',
                        text: "@lang('error_competency_clone_fill_all_fields')"
                    });
                }

                if (source_class_id == target_class_id) {
                    Swal.fire({
                        icon: 'error',
                        text: "@lang('error_not_cloning_the_same_class_competency')"
                    });

                    return;
                }

                Swal.fire({
                    icon: 'warning',
                    text: "@lang('alert_competency_cloning')",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '@lang("Yes")',
                    cancelButtonText: '@lang("No")',
                }).then(result => {
                    if (result) {
                        $.ajax({
                            url: "{{ route('competency.clone') }}",
                            method: 'POST',
                            data: {
                                source_class_id,
                                target_class_id,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: response => {
                                toastr.success("@lang('alert_competency_clone_success')");
                                $('#class_id option[value="0"]').attr('selected', true);
                                return;
                            },
                            fail: error => {
                                console.log(error.message);


                            }
                        });
                    }
                });

            });
        });
    </script>
@endsection