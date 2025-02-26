@extends('layout.master')

@section('content')
    <div class="row">
        <div class="col">
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                            aria-expanded="false" aria-controls="collapseOne">
                            @isset($edit)
                                @lang('Edit competency domain')
                            @else
                                @lang('Add new competency domain')
                            @endisset <i class="fa fa-plus"></i>
                        </button>
                    </h2>
                    <div id="collapseOne"
                        class="accordion-collapse collapse @isset($edit) show @endisset"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            {!! form_start($form) !!}
                            {!! form_rest($form) !!}
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">
                                    @lang('Save')
                                </button>
                            </div>
                            {!! form_end($form) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>@lang('competency_domain_list')</h3>
                </div>
                <div class="card-body">'
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>@lang('name')</th>
                                <th>@lang('rank')</th>
                                {{-- <th>@lang('total_marks')</th> --}}
                                <th>
                                    @lang('Actions')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($competencyDomains as $domain)
                                <tr>
                                    <td>{{ $domain->name }}</td>
                                    <td>{{ $domain->rank }}</td>
                                    {{-- <td>{{ $domain->total_marks }}</td> --}}
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('competency-domain.edit', $domain->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                            <a href="{{ route('competency-domain.destroy', $domain->id) }}" class="btn btn-sm btn-danger delete-form">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">
                                        <span class="alert alert-warning">
                                            @lang('No data')
                                        </span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
<div class="modal fade" id="competencyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New Competency')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="competencyForm" method="POST" action="{{ route('competency.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="competency_domain_id" id="competency_domain_id">
                    
                    <div class="mb-3">
                        <label class="form-label">@lang('Domain')</label>
                        <input type="text" class="form-control" id="domain_name" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">@lang('Competency Name')</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">@lang('Total Marks')</label>
                        <input type="number" name="total_marks" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">@lang('Passing Marks')</label>
                        <input type="number" name="passing_marks" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn-primary">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Ouvrir le modal et remplir les données du domaine
        $('#competencyModal').on('show.bs.modal', function(e) {
            const btn = $(e.relatedTarget);
            const domainId = btn.data('domain-id');
            const domainName = btn.data('domain-name');

            console.log(domainId);
            
            $('#competency_domain_id').val(domainId);
            $('#domain_name').val(domainName);
            
        });

        // Gestion de la soumission du formulaire
        $('#competencyForm').submit(function(e) {
            e.preventDefault();
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        $('#competencyModal').modal('hide');
                        // Recharger la page ou mettre à jour le compteur de compétences
                        location.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'Une erreur est survenue');
                }
            });
        });

        $('.delete-form').on('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "@lang('alert_delete_competency_domain')",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).closest('tr').fadeOut();
                }
            });
        });
    });
</script>
@endsection