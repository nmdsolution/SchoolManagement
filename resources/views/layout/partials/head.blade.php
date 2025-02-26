<!-- Main CSS -->
<link rel="stylesheet" href="{{ URL::asset('/assets/plugins/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ URL::asset('/assets/css/bootstrap5-dropdown-ml-dropdown.css')}}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Fontawesome CSS -->
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/fontawesome/css/fontawesome.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/fontawesome/css/all.min.css')}}">
<!-- Feather CSS -->
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/feather/feather.css')}}">
<!-- Select2 css -->
<link href="{{ URL::asset('/assets/plugins/select2/select2.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;0,900;1,400;1,500;1,700&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="{{URL::asset('/assets/css/bootstrap-datetimepicker.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('/assets/css/bootstrap-daterangepicker.min.css')}}">
<!-- Summernote css -->
<link href="{{ URL::asset('/assets/plugins/summernote/summernote-lite.css')}}" rel="stylesheet" type="text/css"/>
<!-- DataTables CSS -->
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/datatables/css/jquery.dataTables.min.css')}}">
@if(Route::is(['event']))
    <!-- Full calendar css -->
    <link href="{{ URL::asset('/assets/plugins/fullcalendar/fullcalendar.min.css')}}" rel="stylesheet" type="text/css"/>
@endif
@if(Route::is(['student-dashboard','teacher-dashboard']))
    <!-- Simple calendar css -->
    <link href="{{ URL::asset('/assets/plugins/simple-calendar/simple-calendar.css')}}" rel="stylesheet"
          type="text/css"/>
@endif
@if(Route::is(['seo-settings', 'competency.create']))
    <link href="{{ URL::asset('/assets/css/bootstrap-tagsinput.css')}}" rel="stylesheet" type="text/css"/>
@endif
@if(Route::is(['others-settings','add-blog','edit-blog']))
    <link rel="stylesheet" href="{{URL::asset('/assets/css/ckeditor.css')}}">
@endif
@if(Route::is(['chart-c3']))
    <link rel="stylesheet" href="{{URL::asset('/assets/plugins/c3/c3.min.css')}}">
@endif
@if(Route::is(['drag-drop']))
    <link rel="stylesheet" href="{{URL::asset('/assets/plugins/dragula/dragula.min.css')}}">
@endif
@if(Route::is(['form-wizard']))
    <link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/twitter-bootstrap-wizard/form-wizard.css')}}">
@endif
@if(Route::is(['icon-feather']))
    <link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/feather/feather.css')}}">
@endif

<link rel="stylesheet" href="{{ asset('/assets/color-picker/color.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/ionic/ionicons.css')}}">
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/flags/flags.css')}}">
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/material/materialdesignicons.css')}}">
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/pe7/pe-icon-7.css')}}">
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/simpleline/simple-line-icons.css')}}">
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/themify/themify.css')}}">
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/typicons/typicons.css')}}">
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/weather/weathericons.css')}}">
<!-- lightbox -->
<link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/lightbox/glightbox.min.css')}}">
{{--<link rel="stylesheet" href="{{ asset('/assets/css/ekko-lightbox.css') }}">--}}
@if(Route::is(['notification']))
    <link rel="stylesheet" href="{{URL::asset('/assets/plugins/alertify/alertify.min.css')}}">
@endif
@if(Route::is(['rangeslider']))
    <link rel="stylesheet" href="{{URL::asset('/assets/plugins/ion-rangeslider/ion.rangeSlider.min.css')}}">
@endif
@if(Route::is(['scrollbar']))
    <link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/scrollbar/scroll.min.css')}}">
@endif
@if(Route::is(['stickynote']))
    <link rel="stylesheet" href="{{URL::asset('/assets/plugins/icons/stickynote/sticky.css')}}">
@endif
<link rel="stylesheet" href="{{URL::asset('assets/plugins/icons/toastr/toatr.css')}}">
<link rel="stylesheet" href="{{ asset('/assets/bootstrap-table/bootstrap-table.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/bootstrap-table/fixed-columns.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/bootstrap-table/bootstrap-table-reorder-rows.css') }}">
<!-- Switchery CSS -->
<link rel="stylesheet" href="{{ asset('/assets/css/switchery.min.css') }}">
<!-- Main CSS -->
<link rel="stylesheet" href="{{ URL::asset('/assets/css/jquery-ui.css')}}">
<link rel="stylesheet" href="{{ URL::asset('/assets/css/app.css')}}">
<script src="{{ asset('/assets/plugins/jquery/jquery.min.js')}}"></script>

@php
    $theme_color = getSettings('theme_color');
    $theme_color = $theme_color['theme_color']?? '';
    $bgImage = \App\Models\Settings::where('type','login_page_background')->first();
@endphp
<style>
    :root {
        --theme-color: <?=$theme_color ?>;
    }

    .login-body{
        background-image: url("{{$bgImage['message'] ?? ''}}");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: top;
        height: 100vh;
    }
</style>
<script !src="">
    const baseUrl = "{{ URL::to('/') }}";
    const default_horizontal_logo_url = "{{ url(env('DEFAULT_HORIZONTAL_LOGO')) }}";
    const default_vertical_logo_url = "{{ url(env('DEFAULT_VERTICAL_LOGO')) }}";
    const theme_color = "{{$theme_color}}";
    const no_image_available = "{{asset('/storage/no_image_available.jpg')}}";
    const onErrorImage = (e) => {
        if (e.target.src !== no_image_available) {
            e.target.src = no_image_available;
            $(e.target).parent().attr('href', no_image_available);
        }
    };

    function laodDefaultLogo(image, type = "vertical") {
        if (image.src !== default_horizontal_logo_url || image.src !== default_vertical_logo_url) {
            image.onerror = "";
            if (type === "vertical") {
                image.src = default_vertical_logo_url;
            } else {
                image.src = default_horizontal_logo_url;
            }
        }
        return true;
    }
</script>
