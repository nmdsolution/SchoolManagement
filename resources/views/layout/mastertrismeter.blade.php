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
        // Complet JavaScript
        $(document).ready(function() {
            // Handle Add Exam Term form submission
            $('#AddTerms').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                //        showNotification(response.message || 'Exam term added successfully');
                        $('#addTermModal').modal('hide');
                        setTimeout(() => {
                            window.location.reload(); // Refresh the page
                        }, 0);
                    },
                    error: function(xhr) {
                        showNotification(xhr.responseJSON.message || 'An error occurred', 'error');
                    }
                });
            });
        });

        $(document).ready(function() {
            // Function to show Bootstrap alert notification
            function showNotification(message, type = 'success') {
                Swal.fire({
                    title: type.charAt(0).toUpperCase() + type.slice(1),
                    text: message,
                    icon: type,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 30,
                    timerProgressBar: true
                });
            }

            // Handle Delete operation
            $('.delete-button').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: form.attr('action'),
                            type: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                showNotification('Item deleted successfully');
                                setTimeout(() => {
                                    window.location.reload(); // Refresh the page
                                }, 1500);
                            },
                            error: function(xhr) {
                                showNotification(xhr.responseJSON.message || 'An error occurred while deleting', 'error');
                            }
                        });
                    }
                });
            });

            // Handle sequence addition
            $(document).on('click', '.add-sequence', function() {
                var termId = $(this).data('term-id');

                // Get all existing sequences for this term
                var existingSequences = [];
                $('#sequence-' + termId + ' tbody tr').each(function() {
                    var sequenceName = $(this).find('td:first').text().trim();
                    if (sequenceName.startsWith('Seq ')) {
                        existingSequences.push(sequenceName);
                    }
                });

                // Find the next available sequence number
                var nextSequenceNum = 1;
                while (nextSequenceNum <= 6) {
                    var seqName = 'Seq ' + nextSequenceNum;
                    if (!existingSequences.includes(seqName)) {
                        break;
                    }
                    nextSequenceNum++;
                }

                // Check if we've reached the maximum number of sequences
                if (nextSequenceNum > 6) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Maximum number of sequences (6) has been reached',
                        icon: 'warning',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 30,
                        timerProgressBar: true
                    });
                    return;
                }

                // Add the new sequence
                $.ajax({
                    url: '/exam-terms/' + termId + '/add-sequence',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        sequence_name: 'Seq ' + nextSequenceNum
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success',
                            text: response.message || 'Sequence ' + nextSequenceNum + ' added successfully',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 30,
                            timerProgressBar: true
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    },
                    error: function(xhr) {
                        var errorMessage = xhr.responseJSON?.message || 'An error occurred while adding sequence';
                        if (errorMessage.includes('Sequence already exists')) {
                            errorMessage = 'Sequence ' + nextSequenceNum + ' already exists';
                        }
                        Swal.fire({
                            title: 'Error',
                            text: errorMessage,
                            icon: 'error',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 30,
                            timerProgressBar: true
                        });
                    }
                });
            });

            // Toggle sequence details
            $(document).on('click', '.toggle-btn', function() {
                var termId = $(this).data('term-id');
                var sequenceRow = $('#sequence-' + termId);
                var icon = $(this).find('i');

                sequenceRow.toggle(); // Toggle the visibility of the sequence details

                // Change the icon based on the visibility
                if (sequenceRow.is(':visible')) {
                    icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                } else {
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                }
            });
        });


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
            // add ExamTerm to table list
            // Handle Create form submission with AJAX
            $('.create-form').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        showNotification(response.message || 'Exam term added successfully');
                        $('#create-form').modal('hide');

                        // Dynamically append the new term to the table
                        if(response.term) {
                            var newTerm = response.term;
                            var newRow = `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <button class="btn btn-link p-0 me-2 toggle-btn" data-term-id="${newTerm.id}">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                                ${newTerm.name}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">${newTerm.sequences_count || 0} sequences</span>
                            <button class="btn btn-sm btn-outline-success add-sequence" data-term-id="${newTerm.id}">
                                <i class="fa fa-plus"></i>
                            </button>
                        </td>
                        <td>${newTerm.start_date}</td>
                        <td>${newTerm.end_date}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm edit-button"
                                        data-id="${newTerm.id}"
                                        data-name="${newTerm.name}"
                                        data-start_date="${newTerm.start_date}"
                                        data-end_date="${newTerm.end_date}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <form action="${window.location.origin}/exam-terms/${newTerm.id}" method="POST" class="d-inline">
                                    @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm delete-button"
                                    data-id="${newTerm.id}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr class="sequence-details" id="sequence-${newTerm.id}" style="display: none;">
                        <td colspan="5">
                            <div class="p-3 bg-light">
                                <div class="sequence-management">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>${$('#create-form').find('.modal-title').text()} Name</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Sequences will be added here -->
                            </tbody>
                            </table>
                            </div>
                            </div>
                            </td>
                            </tr>
                            `;
                // Append the new row to the table body
                $('table tbody').append(newRow);

                // Reset form
                $('.create-form')[0].reset();


            }
        },
        error: function(xhr) {
            showNotification(xhr.responseJSON.message || 'An error occurred', 'error');
        }
    });
});

   // Handle Edit button click
        $('.edit-button').on('click', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var start_date = $(this).data('start_date');
            var end_date = $(this).data('end_date');

            $('#id').val(id);
            $('#edit_name').val(name);
            $('#edit_start_date').val(start_date);
            $('#edit_end_date').val(end_date);
        });

    // Handle Edit form submission with AJAX
    // Handle Edit form submission with AJAX
$('.editform').on('submit', function(e) {
    e.preventDefault();
    var id = $('#id').val();
    var formData = new FormData(this);

    $.ajax({
        url: $(this).attr('action') + '/' + id,
        type: 'PUT', // Change this to 'PUT' or 'PATCH'
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function(response) {
        //    showNotification(response.message || 'Exam term updated successfully');
            $('#editModal').modal('hide');

            // Update the row in the table
            if(response.term) {
                var updatedTerm = response.term;
                var rowToUpdate = $('button.delete-button[data-id="'+id+'"]').closest('tr');

                // Update the content of the row
                rowToUpdate.find('td:eq(0) div').contents().filter(function() {
                    return this.nodeType === 3; // Text nodes only
                }).last().replaceWith(updatedTerm.name);

                rowToUpdate.find('td:eq(2)').text(updatedTerm.start_date);
                rowToUpdate.find('td:eq(3)').text(updatedTerm.end_date);

                // Update data attributes for the edit button
                rowToUpdate.find('.edit-button')
                    .data('name', updatedTerm.name)
                    .data('start_date', updatedTerm.start_date)
                    .data('end_date', updatedTerm.end_date)
                    .attr('data-name', updatedTerm.name)
                    .attr('data-start_date', updatedTerm.start_date)
                    .attr('data-end_date', updatedTerm.end_date);
            }

            // Refresh the page after successful save
            location.reload();
        },
        error: function(xhr) {
            showNotification(xhr.responseJSON.message || 'An error occurred', 'error');
        }
    });
});


    // Handle Delete operation with sequences check
    $(document).on('click', '.delete-button', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var termId = $(this).data('id');
        var sequenceCount = parseInt($('button.add-sequence[data-term-id="'+termId+'"]').siblings('.badge').text());

        if (sequenceCount > 0) {
            // Show warning about existing sequences
            Swal.fire({
                title: 'Cannot Delete',
                text: 'This term contains sequences. Please delete all sequences first before deleting this term.',
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        } else {
            // Proceed with deletion
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json'
                        },
                        success: function(response) {
                     //       showNotification('Term deleted successfully');

                            // Remove the term rows from the table
                            var termRow = $('button.delete-button[data-id="'+termId+'"]').closest('tr');
                            var sequenceRow = $('#sequence-' + termId);
                            termRow.remove();
                            sequenceRow.remove();
                        },
                        error: function(xhr) {
                            showNotification(xhr.responseJSON.message || 'An error occurred while deleting', 'error');
                        }
                    });
                }
            });
        }
    });

    // Handle sequence deletion with dynamic update
    $(document).on('click', '.delete-sequence-button', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var sequenceId = $(this).data('id');
        var termRow = $(this).closest('.sequence-details');
        var termId = termRow.attr('id').replace('sequence-', '');
        var badgeElement = $('button.add-sequence[data-term-id="'+termId+'"]').siblings('.badge');
        var currentSequences = parseInt(badgeElement.text());

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
//                        showNotification('Sequence deleted successfully');

                        // Update sequence count badge
                        badgeElement.text((currentSequences - 1) + ' sequences');

                        // Remove the sequence row
                        $(form).closest('tr').remove();
                    },
                    error: function(xhr) {
                        showNotification(xhr.responseJSON.message || 'An error occurred while deleting', 'error');
                    }
                });
            }
        });
    })
       //Start chevron dropdown

//Edit Sequence start here
  $(document).ready(function() {
    // Set up edit sequence modal
  // Toggle sequence details
            $('.toggle-btn').on('click', function() {
                var termId = $(this).data('term-id');
                $('#sequence-' + termId).toggle();
                $(this).find('i').toggleClass('fa-chevron-right fa-chevron-down');
            });

 $(document).on('click', '.edit-sequence-button', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var startDate = $(this).data('start_date');
        var endDate = $(this).data('end_date');
        var status = $(this).data('status');

        // Set basic sequence data
        $('#editSequenceModal #id').val(id);
        $('#editSequenceModal #edit_name').val(name);
        $('#editSequenceModal #edit-start-date').val(startDate);
        $('#editSequenceModal #edit-end-date').val(endDate);

        // Reset select all checkbox and clear all previous selections
        $('#select-all-class-section').prop('checked', false);
        $('#editSequenceModal #class_section_id').val(null).trigger('change');
        $('.selected-classes-info').remove();

        // Get existing class sections for this sequence
        $.ajax({
            url: '/sequence-classes/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (!response.error && response.class_sections) {
                    var classSectionIds = response.class_sections.map(String);

                    // Clear and set new selections
                    $('#editSequenceModal #class_section_id').val(classSectionIds).trigger('change');

                    // Show selected classes count
                    var selectedCount = classSectionIds.length;
                    if (selectedCount > 0) {
                        $('#class_section_div').append('<div class="selected-classes-info mt-2 text-info">' +
                            selectedCount + ' classes currently assigned</div>');
                    }
                }
            },
            error: function(xhr) {
                console.error("Error fetching class sections", xhr);
                showNotification('Failed to load assigned classes', 'error');
            }
        });

        // Set the status radio button
        if (status == 1) {
            $('#editSequenceModal #edit-active').prop('checked', true);
        } else {
            $('#editSequenceModal #edit-inactive').prop('checked', true);
        }
    });

    // Handle select all checkbox for class sections
    $('#select-all-class-section').on('change', function() {
        var isChecked = $(this).prop('checked');
        if (isChecked) {
            // Select all options
            var allValues = $('#class_section_id option').map(function() {
                return $(this).val();
            }).get();
            $('#class_section_id').val(allValues);
        } else {
            // Deselect all options
            $('#class_section_id').val(null);
        }
        $('#class_section_id').trigger('change');
    });

    // Initialize select2 for class section dropdown
    if ($.fn.select2) {
        $('.class-section-in-sequence').select2({
            placeholder: 'Select Classes',
            allowClear: true,
            dropdownParent: $('#editSequenceModal')
        });
    }

    // Handle Edit Sequence form submission
    $('#editSequenceForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#editSequenceModal #id').val();

        if (!id) {
            showNotification('Sequence ID is missing', 'error');
            return;
        }

        var formData = new FormData(this);
        formData.append('_method', 'PUT');

        var submitBtn = $(this).find('input[type="submit"]');
        var originalBtnText = submitBtn.val();
        submitBtn.val('Please Wait...').addClass('disabled').prop('disabled', true);

        $.ajax({
            url: $(this).attr('action') + '/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (!response.error) {
                    // Clear the modal's select2 values before reload
                    $('#editSequenceModal #class_section_id').val(null).trigger('change');
                    $('#editSequenceModal').modal('hide');

                    // Add a small delay before reload to ensure modal cleanup
                    setTimeout(function() {window.location.reload();}, 0);
                } else {
                    showNotification(response.message || 'An error occurred', 'error');
                    submitBtn.val(originalBtnText).removeClass('disabled').prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.error('Error response:', xhr.responseJSON);
                showNotification(xhr.responseJSON?.message || 'An error occurred', 'error');
                submitBtn.val(originalBtnText).removeClass('disabled').prop('disabled', false);
            }
        });
    });
});});
    </script>
    </body>
</html>
