<!-- jQuery -->
{{-- <script src="{{ asset('assets/plugins/jquery/jquery.min.js')}}"></script> --}}

<!-- Bootstrap JS -->
<script src="{{ asset('/assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/assets/js/bootstrap5-dropdown-ml-dropdown.js') }}"></script>

<!-- Feather Icon JS -->
<script src="{{ asset('/assets/plugins/feather/feather.min.js') }}"></script>

<!-- Slimscroll JS -->
<script src="{{ asset('/assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

<script src="{{ asset('/assets/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('/assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Select JS -->
<script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>

<!-- Datepicker Core JS -->
<script src="{{ asset('/assets/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('/assets/js/bootsrtap-daterangepicker.min.js') }}"></script>
<!-- Summernote JS -->
{{-- <script src="{{ asset('assets/plugins/summernote/summernote-lite.min.js')}}"></script> --}}
<script src="{{ asset('/assets/tinymce/tinymce.min.js') }}"></script>

<!-- DataTables JS -->
{{--<script src="{{ asset('/assets/plugins/datatables/js/jquery.dataTables.min.js')}}"></script>--}}
{{--<script src="{{ asset('/assets/plugins/datatables/js/datatables.min.js')}}"></script>--}}
{{--Bootstrap Table--}}
<script src="{{ asset('/assets/bootstrap-table/jquery.tablednd.min.js') }}"></script>
<script src="{{ asset('/assets/bootstrap-table/bootstrap-table.min.js') }}"></script>
<script src="{{ asset('/assets/bootstrap-table/bootstrap-table-mobile.js') }}"></script>
<script src="{{ asset('/assets/bootstrap-table/bootstrap-table-export.min.js') }}"></script>
<script src="{{ asset('/assets/bootstrap-table/fixed-columns.min.js') }}"></script>
<script src="{{ asset('/assets/bootstrap-table/tableExport.min.js') }}"></script>
<script src="{{ asset('/assets/bootstrap-table/jspdf.min.js') }}"></script>
<script src="{{ asset('/assets/bootstrap-table/jspdf.plugin.autotable.js') }}"></script>
<script src="{{ asset('/assets/bootstrap-table/bootstrap-table-reorder-rows.min.js') }}"></script>
@if(Route::is(['form-validation']))
    <!-- Form Validation JS -->
    <script src="{{ asset('/assets/js/pages/form-validation.js') }}"></script>
@endif
@if (Route::is(['seo-settings', 'competency.create']))
    <script src="{{ asset('/assets/js/bootstrap-tagsinput.js') }}"></script>
@endif
@if (Route::is(['form-mask']))
    <!-- Masked  JS -->
    <script src="{{ asset('/assets/js/jquery.maskedinput.js') }}"></script>
    <script src="{{ asset('/assets/js/mask.js') }}"></script>
@endif
@if (Route::is(['student-dashboard', 'teacher-dashboard']))
    <!-- Simple calendar css -->
    <script src="{{ asset('/assets/plugins/simple-calendar/jquery.simple-calendar.js') }}"></script>
    <script src="{{ asset('/assets/js/calander.js') }}"></script>
    <script src="{{ asset('/assets/js/circle-progress.min.js') }}"></script>
@endif
@if (Route::is(['event']))
    <!-- Full canlendar css -->
    <script src="{{ asset('/assets/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/fullcalendar/jquery.fullcalendar.js') }}"></script>
@endif
{{-- @if (Route::is(['others-settings', 'add-blog', 'edit-blog'])) --}}
<!-- ckeditor JS -->

<script src="{{ asset('/assets/js/ckeditor-4/ckeditor.js') }}"></script>
<script src="{{ asset('/assets/js/ckeditor-4/adapters/jquery.js') }}"></script>


{{-- @endif --}}
@if (Route::is(['exams.get-report', 'student-dashboard', 'teacher-dashboard', 'chart-apex','dashboard','home']))
    <!-- Chart JS -->
    <script src="{{ asset('/assets/plugins/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('/assets/js/pages/chart-data.js') }}"></script>
@endif
@if (Route::is(['exams.get-report','attendance.view','dashboard','home']))
    <script src="{{ asset('/assets/plugins/c3/d3.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/c3/c3.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/c3/chart-datac3.js') }}"></script>
@endif
@if (Route::is(['chart-flot']))
    <script src="{{ asset('/assets/plugins/flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('/assets/plugins/flot/jquery.flot.fillbetween.js') }}"></script>
    <script src="{{ asset('/assets/plugins/flot/jquery.flot.pie.js') }}"></script>
    <script src="{{ asset('/assets/plugins/flot/chart-dataflot.js') }}"></script>
@endif
@if (Route::is(['chart-js']))
    <script src="{{ asset('/assets/plugins/chartjs/chart.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/chartjs/chart-datachart.js') }}"></script>
@endif
@if (Route::is(['chart-morris']))
    <script src="{{ asset('/assets/plugins/morris/raphael.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/morris/morris.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/morris/chart-datamorris.js') }}"></script>
@endif
@if (Route::is(['chart-peity']))
    <script src="{{ asset('/assets/plugins/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/peity/chart-datapeity.js') }}"></script>
@endif
@if (Route::is(['clipboard']))
    <script src="{{ asset('/assets/plugins/dragula/dragula.js') }}"></script>
    <script src="{{ asset('/assets/plugins/clipboard/clipboard.js') }}"></script>
@endif
@if (Route::is(['drag-drop']))
    <script src="{{ asset('/assets/plugins/dragula/dragula.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/dragula/drag-drop.min.js') }}"></script>
@endif
@if (Route::is(['counter']))
    <!-- Clipboard JS -->
    <script src="{{ asset('/assets/plugins/counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/counterup/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/counterup/jquery.missofis-countdown.js') }}"></script>
@endif
@if (Route::is(['form-wizard']))
    <script src="{{ asset('/assets/plugins/icons/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/icons/twitter-bootstrap-wizard/prettify.js') }}"></script>
    <script src="{{ asset('/assets/plugins/icons/twitter-bootstrap-wizard/form-wizard.js') }}"></script>
@endif
@if (Route::is(['horizontal-timeline']))
    <!-- Timeline JS -->
    <script src="{{ asset('/assets/js/horizontal-timeline.js') }}"></script>
@endif
<!-- LightBox -->
{{-- <script src="{{ asset('/assets/js/ekko-lightbox.min.js') }}"></script> --}}
<script src="{{ asset('/assets/plugins/icons/lightbox/glightbox.min.js') }}"></script>
<script src="{{ asset('/assets/plugins/icons/lightbox/lightbox.js') }}"></script>

@if (Route::is(['notification']))
    <script src="{{ asset('/assets/plugins/alertify/alertify.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/alertify/custom-alertify.min.js') }}"></script>
@endif
@if (Route::is(['rangeslider']))
    <script src="{{ asset('/assets/plugins/ion-rangeslider/ion.rangeSlider.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/ion-rangeslider/custom-rangeslider.js') }}"></script>
@endif
@if (Route::is(['rating']))
    <script src="{{ asset('/assets/plugins/raty/jquery.raty.js') }}"></script>
    <script src="{{ asset('/assets/plugins/raty/custom.raty.js') }}"></script>
@endif
@if (Route::is(['scrollbar']))
    <script src="{{ asset('/assets/plugins/icons/scrollbar/scrollbar.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/icons/scrollbar/custom-scroll.js') }}"></script>
@endif
@if (Route::is(['stickynote']))
    <script src="{{ asset('/assets/plugins/icons/stickynote/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/icons/stickynote/sticky.js') }}"></script>
@endif

{{-- Sweet Alert --}}
<script src="{{ asset('/assets/plugins/icons/sweetalert/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('/assets/plugins/icons/sweetalert/sweetalerts.min.js') }}"></script>

<script src="{{ asset('/assets/plugins/icons/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('/assets/plugins/icons/toastr/toastr.js') }}"></script>

<script src="{{ asset('assets/color-picker/jquery-asColor.min.js') }}"></script>
<script src="{{ asset('assets/color-picker/color.min.js') }}"></script>
<!-- Switchery JS -->
<script src="{{ asset('/assets/js/switchery.min.js')}}"></script>
<!-- Custom JS -->
<script src="{{ asset('/assets/js/app.min.js') }}"></script>

<script src="{{ asset('/assets/js/jquery.validate.min.js') }}"></script>

<script src="{{ asset('/assets/js/custom/validate.js?version=1.0.0') }}"></script>
<script src="{{ asset('/assets/js/custom/function.js?version=1.0.2') }}"></script>
<script src="{{ asset('/assets/js/custom/common.js?version=1.0.23') }}"></script>
<script src="{{ asset('/assets/js/custom/custom.js?version=1.0.2') }}"></script>
<script src="{{ asset('/assets/js/custom/custom-bootstrap-table.js?version=1.0.3') }}"></script>


<script src="{{ asset('/assets/js/jquery.repeater.js') }}"></script>

@if ($errors->any())
    @foreach ($errors->all() as $error)
        @if ($error != 1)
            <script type='text/javascript'>
                $(function () {
                    toastr["error"]('{{ $error }}');
                });
            </script>
        @endif
    @endforeach
@endif

@if (\Session::has('success'))
    {{-- <div class="alert alert-success"> --}}
    <script type='text/javascript'>
        $(function () {
            toastr["success"]('{{ \Session::get('success') }}');
        });
    </script>
    {{-- </div> --}}
@endif

@if (\Session::has('error'))
    <div class="alert alert-success">
        <script type='text/javascript'>
            $(function () {
                toastr["error"]('{{ \Session::get('error') }}');
            });
        </script>
    </div>
@endif



<script>
    window.onload = $('#center_id').trigger('change');
    window.onload = $('#filter_center_id').trigger('change');
    window.onload = $('#filter_class_section_id').trigger('change');
    window.onload = $('#center_id_get_class').trigger('change');
    window.onload = $('#filter_center_id_get_class').trigger('change');

</script>

<script>
    const lang_no = "{{ __('no') }}"
    const lang_yes = "{{ __('yes') }}"
    const lang_cannot_delete_because_data_is_associated_with_other_data =
        "{{ __('cannot_delete_because_data_is_associated_with_other_data') }}"
    const lang_delete_title = "{{ __('delete_title') }}"
    const lang_delete_warning = "{{ __('delete_warning') }}"
    const lang_yes_delete = "{{ __('yes_delete') }}"
    const lang_cancel = "{{ __('cancel') }}"
    const lang_no_data_found = "{{ __('no_data_found') }}"
    const lang_cash = "{{ __('cash') }}"
    const lang_cheque = "{{ __('cheque') }}"
    const lang_online = "{{ __('online') }}"
    const lang_success = "{{ __('success') }}"
    const lang_failed = "{{ __('failed') }}"
    const lang_pending = "{{ __('pending') }}"
    const lang_select_subject = "{{ __('select_subject') }}"
    const lang_option = "{{ __('option') }}"
    const lang_simple_question = "{{ __('simple_question') }}"
    const lang_equation_based = "{{ __('equation_based') }}"
    const lang_select_option = "{{ __('select') . ' ' . __('option') }}"
    const lang_enter_option = "{{ __('enter') . ' ' . __('option') }}"
    const lang_add_new_question = "{{ __('add_new_question') }}";
    const lang_yes_change_it = "{{ __('yes_change_it') }}"
    const lang_yes_uncheck = "{{ __('Yes Uncheck') }}";
    const lang_all = "{{ __('all') }}";
    const lang_select_student_name = "{{ __('Select Student Name') }}";
    const lang_select_subject_name = "{{ __('Select Subject Name') }}";
    const lang_no_student_found = "{{ __('No Student Found') }}";
    const lang_no_subject_found = "{{ __('No Subject Found') }}";
    const lang_pay_in_installment = "{{__('pay_in_installment')}}";
    const lang_partial_paid = "{{ __('partial_paid') }}";
    const lang_due_date_on = "{{ __('due_date_on') }}";
    const lang_charges = "{{ __('charges') }}";
    const lang_total_amount = "{{ __('total')}} {{__('amount')}}";
    const lang_paid_on = "{{ __('paid_on')}}";
    const lang_due_charges = "{{ __('due_charges')}}";
    const lang_date = "{{ __('date')}}";
    const lang_active = "{{ __('active') }}";
    const lang_inactive = "{{ __('inactive') }}";
    const lang_mobile_money = "Mobile money";
    const lang_yes_check = "{{ __('Yes Check') }}";
</script>


<script>
    function updatePrintButtonHref(){
        $('table.table_list').parents('.bootstrap-table').map(function(index, element){
            let table = $(element).find('table.table_list')[0]
            let url = $(table).data('url')
            let param_function = $(table).data('query-params')
            let params = {
                limit: 1000000,
                sort: $(table).data('sort-name'),
                order: $(table).data('sort-order'),
                offset: 0,
                search: '',
            }
        
            params = window[param_function](params)
            params.print=true
            for(const prop in params){
                if(params[prop]==='undefined' || params[prop]===undefined) params[prop]=''
            }

            params = new URLSearchParams(params).toString()
            
            let href = $(element).find('.yadiko-pdf-print-btn > a')[0]
            $(href).prop('href', url+'?'+params);
        })
    }

    function removePrintButtons(){
        $('button.yadiko-pdf-print-btn').remove();
    }

    function insertPrintButtons(){
        let html = '<button type="button" class="export btn-secondary yadiko-pdf-print-btn"><a class="text-white" href="{{route('home')}}" download><i class="fa fa-print"></i>PDF</a></button>'
        $('.fixed-table-toolbar .columns.btn-group').append(html);
        updatePrintButtonHref();
    }

    $(document).ready(function (){
        removePrintButtons();
        insertPrintButtons();
        $('.bootstrap-table .form-control').change(insertPrintButtons);
        // $('table.table_list').on('DOMSubtreeModified', 'tbody', updatePrintButtonHref);
    });
</script>