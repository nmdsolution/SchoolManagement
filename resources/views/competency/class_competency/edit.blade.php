@extends('layout.master')

@section('content')
    <div class="container mx-auto">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center mb-6">
                            <h3 class="text-2xl font-bold">Edit Class Competency</h3>
                            <x-ls::button triggers_modal="competencyModal" :label="__('Add New Competency')" color="primary" type="button" /> 
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-condensed">
                            <thead>
                                <tr>
                                    <td>Name</td>
                                    <td>Domain</td>
                                    <td>Code</td>
                                    <td>Actions</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($class->competencies as $competency)
                                <tr>
                                    <td>{{ $competency->name }}</td>
                                    <td class="text-truncate" style="max-width: 150px;" title="{{ $competency->competency_domain->name }}">
                                        {{ $competency->competency_domain->name }}
                                    </td>
                                    <td>{{ $competency->pivot->code }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('class-competency.destroy', $class->id) }}"
                                                class="btn btn-sm btn-danger btn-rounded btn-icon delete-btn" 
                                                data-route="{{ route('class-competency.destroy', $class->id) }}" 
                                                data-competency-id="{{ $competency->id }}">
                                                <i class="fa fa-trash"></i>
                                            </a>
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

<x-ls::modal id="competencyModal" title="Add New Competency">
    <x-ls::form action="{{ route('class-competency.store') }}" method="POST" id="competency_form">
        <x-ls::hidden name="class_id" :value="$class->id" /> 
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
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            
            if(confirm('@lang("Are you sure you want to delete this item?")')) {
                const btn = $(this);
                const route = btn.data('route');

                $.ajax({
                    url: route,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        competency_id: btn.data('competency-id')
                    },
                    success: function(response) {
                        if(response.success) {
                            btn.closest('tr').fadeOut();
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message || '@lang("An error occurred")');
                    }
                });
            }
        });

        $('#competency_form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
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
                    toastr.error(xhr.responseJSON.message || 'Une erreur est survenue');
                }
            });
        });
    });
</script>

<style>
.truncate-text {
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}
</style>
@endsection

