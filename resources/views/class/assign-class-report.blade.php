@extends('layout.master')

@section('title')
    {{ __('class_type_settings') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('class_type_settings') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <livewire:report-card-assignment />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('templates-assigned', event => {
                showSuccessToast(event.detail.message);
            });
        });
    </script>
@endsection
