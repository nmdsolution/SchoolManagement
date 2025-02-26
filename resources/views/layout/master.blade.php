<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <!-- Favicon -->
    {{-- <link rel="shortcut icon" href="{{ URL::asset('assets/img/favicon.png')}}"> --}}
    <link rel="shortcut icon" href="{{ getSettings('favicon') ? url(Storage::url(getSettings('favicon')['favicon'])) : URL::asset('assets/img/favicon.png')}}">
    {{-- getSettings('logo2') ? url(Storage::url(getSettings('logo2')['logo2'])) : url('assets/logo.svg') }} --}}
    @routes
    @include('layout.partials.head')
    @livewireStyles
</head>

@if(Route::is(['error-404']))
    <body class="error-page">
    @endif
    <body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
    @if(!Route::is(['error-404']))
        @include('layout.partials.header')
        @include('layout.partials.nav')

        <!-- Page Wrapper -->
            <div class="page-wrapper">
                <div class="content container-fluid">

                    @yield('content')
                </div>

                @if(Route::is(['components','data-tables','departments','event','exam','expenses','fees','fees-collections','form-basic-inputs',
                'form-horizontal','form-input-groups','form-mask','form-validation','form-vertical','holiday','hostel','library','index','salary','sports',
                'student-dashboard','student-details','students','subjects','tables-basic','teacher-dashboard','teacher-details','teachers','time-table','transport',
                'icon-weather','icon-fontawesome','icon-ionic','icon-material','icon-pe7','icon-simpleline','icon-themify','icon-typicon','icon-feather','icon-flag',
                'accordions','alerts','avatar','badges','cards','buttons','carousel','chart-apex','chart-c3','chart-flot','chart-js','chart-morris',
                'chart-peity','clipboard','counter','drag-drop','form-wizard','grid','horizontal-timeline','images','lightbox','media','modal',
                'notification','offcanvas','placeholders','popover','progress','rangeslider','scrollbar','rating','ribbon','spinner','spinners','stickynote',
                'students-grid','sweetalerts','tab','teachers-grid','text-editor','timeline','toastr','tooltip','typography','video']))
                    @include('layout.partials.footer')
                @endif
            </div>
            <!-- /Page Wrapper -->
        @endif
        @if(Route::is(['error-404']))
            @yield('content')
        @endif
    </div>
    <!-- /Main Wrapper -->
    @livewireScripts
    @include('layout.partials.footer-scripts')
    @yield('js')
    @yield('script')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('success-message', event => {
                showSuccessToast(event.detail.message);
                $('#table_list').bootstrapTable('refresh');
            });
            window.addEventListener('error-message', event => {
                showErrorToast(event.detail.message);
            });
        });
        $(document).ready(function() {
            // Function to show notifications
            function showNotification(message, type = 'success') {
                Swal.fire({
                    title: type.charAt(0).toUpperCase() + type.slice(1),
                    text: message,
                    icon: type,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 0,
                    timerProgressBar: true
                });
            }

    </script>
    </body>
</html>
