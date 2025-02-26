<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/img/favicon.png')}}">
    @include('layout.partials.head')
</head>
<body>
<!-- Main Wrapper -->
<div class="main-wrapper login-body">
    @yield('content')
</div>
<!-- /Main Wrapper -->

@include('layout.partials.footer-scripts')
@yield('js')
</body>
</html>
