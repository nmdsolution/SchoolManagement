@extends('layout.master')

@section('content')
<div class="container mx-auto">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <h3 class="text-2xl font-bold">@lang('Competencies')</h3>
                        <x-ls::button triggers_modal="competencyModal" :label="__('Add New Competency')" color="primary" type="button" /> 
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <td>@lang('Name')</td>
                                <td>@lang('Domain')</td>
                                <td>@lang('Actions')</td>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($competencies as $competency)
                            <tr>
                                <td>{{ $competency->name }}</td>
                                <td class="text-truncate" style="max-width: 150px;" title="{{ $competency->competency_domain->name }}">
                                    {{ $competency->competency_domain->name }}
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" 
                                                class="btn btn-sm btn-warning edit-competency"
                                                data-id="{{ $competency->id }}"
                                                data-name="{{ $competency->name }}"
                                                data-domain="{{ $competency->competency_domain_id }}"
                                                data-route="{{ route('competency.update', $competency->id) }}"
                                                >
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-danger delete-btn"
                                                data-id="{{ $competency->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">@lang('No competencies found')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    {{-- <div class="mt-4">
                        {{ $competencies->links() }}
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter/éditer une compétence -->
<x-ls::modal id="competencyModal" title="Competency">
    <x-ls::form action="" method="POST" id="competency_form">
        <input type="hidden" name="edit_mode" value="0">
        <input type="hidden" name="competency_id" value="">

        <x-ls::select-model name="competency_domain_id" 
            label="Domain" 
            placeholder="Select Domain" 
            required="true" 
            :options="$competency_domains" />
        <x-ls::text label="Name" name="name" placeholder="Name" required="true" />
    </x-ls::form>
</x-ls::modal>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Gestionnaire pour le bouton d'édition
        $('.edit-competency').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const domain = $(this).data('domain');
            const routePath = $(this).data('route');

            // Remplir le formulaire
            $('#competency_form').attr('action', routePath);
            $('input[name="edit_mode"]').val('1');
            $('input[name="competency_id"]').val(id);
            $('input[name="name"]').val(name);
            $('select[name="competency_domain_id"]').val(domain).trigger('change');
            
            // Mettre à jour le titre du modal
            $('#competencyModal .modal-title').text('@lang("Edit Competency")');
            
            // Ouvrir le modal
            $('#competencyModal').modal('show');
        });

        // Réinitialiser le formulaire quand le modal est fermé
        $('#competencyModal').on('hidden.bs.modal', function () {
            $('#competency_form')[0].reset();
            $('input[name="edit_mode"]').val('0');
            $('input[name="competency_id"]').val('');
            $('#competency_form').attr('action', null);
            $('#competencyModal .modal-title').text('@lang("Add New Competency")');
        });

        $('#competency_form').on('submit', function(e) {
            e.preventDefault();
            
            const isEdit = $('input[name="edit_mode"]').val() === '1';
            const competencyId = $('input[name="competency_id"]').val();
            const url = isEdit 
                ? `/competency/${competencyId}`
                : $(this).attr('action');
            const method = isEdit ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: $(this).serialize(),
                success: function(response) {
                    if(response.error) {
                        toastr.error(response.message);
                    } else {
                        toastr.success(response.message);
                        $('#competencyModal').modal('hide');
                        location.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || '@lang("An error occurred")');
                }
            });
        });

        // Gestionnaire pour le bouton de suppression
        $('.delete-btn').on('click', function() {
            const $btn = $(this);
            const competencyId = $btn.data('id');
            
            Swal.fire({
                title: '@lang("Are you sure?")',
                text: '@lang("alert_delete_competency")',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '@lang("Yes")',
                cancelButtonText: '@lang("No")',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/competency/${competencyId}`,
                        method: 'DELETE',
                        success: function(response) {
                            if(response.error) {
                                toastr.error(response.message);
                            } else {
                                toastr.success(response.message);
                                $btn.closest('tr').fadeOut();
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || '@lang("An error occurred")');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection

@section('js')

<style>
.badge {
    font-size: 0.8em;
    margin-right: 0.2em;
}
.truncate-text {
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}
</style>
@endsection 