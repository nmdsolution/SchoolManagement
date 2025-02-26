"use strict";

var $table = $(".table_list"); // "table" accordingly
var electiveSubjectGroupCounter = 1;
$(document).ready(function () {
    // $("#sortable-row").sortable({
    //     placeholder: "ui-state-highlight"
    // });
    function checkList(listName, newItem, id) {
        var dupl = false;
        $("#" + listName + " > div").each(function () {
            if ($(this)[0] !== newItem[0]) {
                if ($(this).find("li").attr('id') == newItem.find("li").attr('id')) {
                    dupl = true;
                }
            }
        });
        return dupl;
    }

    $('#table_list').on('check.bs.table', function (e, row) {
        var questions = $(this).bootstrapTable('getSelections');
        let li = ''
        $.each(questions, function (index, value) {
            if (value.question_type) {
                li = $('<div class="list-group row"><input type="hidden" name="assign_questions[' + value.question_id + '][question_id]" value="' + value.question_id + '"><li id="q' + value.question_id + '"class="list-group-item justify-content-between align-items-center ui-state-default list-group-item-secondary m-2">' + value.question_id +
                    '<hr class="question - divider">' + value.question + ' <span class="text-right row"><div class="col-md-10"><input type="number" placeholder="Marks" class="list-group-item col-md-6" name="assign_questions[' + value.question_id + '][marks]" style="width: 20%"></div><div class="col-md-2"><a class="btn btn-danger btn-sm remove-row ml-2" data-id="' + value.question_id + '"><i class="fa fa-times" aria-hidden="true"></i></a></div></span></li></div>'
                );
            } else {
                li = $('<div class="list-group row"><input type="hidden" name="assign_questions[' + value.question_id + '][question_id]" value="' + value.question_id + '"><li id="q' + value.question_id + '"class="list-group-item justify-content-between align-items-center ui-state-default list-group-item-secondary m-2">' + value.question_id + ". " + '<hr class="question-divider"><span class="text-center">' + value.question + '</span> <span class="text-right row"><div class="col-md-10"><input type="number" placeholder="Marks" class="list-group-item" name="assign_questions[' + value.question_id + '][marks]" style="width: 20%"></div><div class="col-md-2"><a class="btn btn-danger btn-sm remove-row ml-2" data-id="' + value.question_id + '"><i class="fa fa-times" aria-hidden="true"></i></a></div></span></li></div>');
            }
            var pasteItem = checkList("sortable-row", li, row.question_id);
            if (!pasteItem) {
                $("#sortable-row").append(li);
            }
        });
        createCkeditor();
    })
    $('#table_list').on('uncheck.bs.table', function (e, row) {
        $("#sortable-row > div").each(function () {
            $(this).find('#q' + row.question_id).remove();
        });
    })
    $table.bootstrapTable('destroy').bootstrapTable({
        exportTypes: ['csv', 'excel', 'pdf', 'txt', 'json'],
        iconsPrefix: 'fa',
        icons: {
            paginationSwitchDown: 'fa-caret-square-down',
            paginationSwitchUp: 'fa-caret-square-up',
            refresh: 'fa-sync',
            toggleOff: 'fa-toggle-off',
            toggleOn: 'fa-toggle-on',
            columns: 'fa-th-list',
            detailOpen: 'fa-plus',
            detailClose: 'fa-minus',
            fullscreen: 'fa-arrows-alt',
            search: 'fa-search',
            clearSearch: 'fa-trash',
            export: 'fa-download'
        }
    });

    $table.on('load-success.bs.table', function (e) {
        if ($('.js-switch').length) {
            $('.switchery').remove();
            let elem = document.querySelector('.js-switch');
            new Switchery(elem, {color: theme_color});
        }
    })

    $("#toolbar")
        .find("select")
        .change(function () {
            $table.bootstrapTable("refreshOptions", {
                exportDataType: $(this).val()
            });
        });

    //File Upload Custom Component
    $('.file-upload-browse').on('click', function () {
        var file = $(this).parent().parent().parent().find('.file-upload-default');
        file.trigger('click');
    });
    $('.file-upload-default').on('change', function () {

        $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
    });
    tinymce.init({
        height: "400",
        selector: '#tinymce_message,.tinymce_message',
        menubar: 'file edit view formate tools',
        toolbar: [
            'styleselect fontselect fontsize undo redo | cut copy paste | bold italic | alignleft aligncenter alignright alignjustify bullist numlist | outdent indent | blockquote autolink | lists |  code'
        ],
        plugins: 'autolink link image lists code'
    });

    $('.modal').on('hidden.bs.modal', function () {
        //Reset input file on modal close
        $('.file-upload-default').val('');
        $('.file-upload-info').val('');
    })
    /*simplemde editor*/
    if ($("#simpleMde").length) {
        var simplemde = new SimpleMDE({
            element: $("#simpleMde")[0],
            hideIcons: ["guide", "fullscreen", "image", "side-by-side"],
        });
    }

    //Color Picker Custom Component
    if ($(".color-picker").length) {
        $('.color-picker').asColorPicker();
    }
    //Date Picker
    if ($(".datepicker-popup").length) {
        $('.datepicker-popup').datepicker({
            enableOnReadonly: false,
            todayHighlight: true,
        });
    }
    //Added this for Dynamic Date Picker input Initialization
    $('body').on('focus', ".datepicker-popup", function () {
        $(this).datepicker();
    });

    //Time Picker
    if ($("#timepicker-example").length) {
        $('#timepicker-example').datetimepicker({
            format: 'LT'
        });
    }
    //Select
    if ($(".js-example-basic-single").length) {
        $(".js-example-basic-single").select2();
    }

    $(document).on('click', '[data-toggle="lightbox"]', function (event) {
        event.preventDefault();
        // $(this).ekkoLightbox();
        GLightbox({selector: ".image-popup", title: !1});
        GLightbox({selector: ".image-popup-desc"});
        GLightbox({selector: ".image-popup-video-map", title: !1});
    });

    if ($('.js-switch').length) {
        let elem = document.querySelector('.js-switch');
        new Switchery(elem, {color: theme_color});
    }

    if ($('.datepicker').length > 0) {
        $('body').on('focus', ".datepicker", function () {
            $(this).datetimepicker({
                format: 'DD-MM-YYYY',
                icons: {
                    up: "fas fa-angle-up",
                    down: "fas fa-angle-down",
                    next: 'fas fa-angle-right',
                    previous: 'fas fa-angle-left'
                },

            })
            $(this).on('dp.show', function () {
                $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
            }).on('dp.hide', function () {
                $(this).closest('.temp').addClass('table-responsive').removeClass('temp')
            }).on('dp.change', function () {
                $(this).change();
            });
        });
        // $('.datepicker').datetimepicker({
        //     format: 'DD-MM-YYYY',
        //     icons: {
        //         up: "fas fa-angle-up",
        //         down: "fas fa-angle-down",
        //         next: 'fas fa-angle-right',
        //         previous: 'fas fa-angle-left'
        //     },
        //
        // })
        // $('.datepicker').on('dp.show', function () {
        //     $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
        // }).on('dp.hide', function () {
        //     $(this).closest('.temp').addClass('table-responsive').removeClass('temp')
        // }).on('dp.change', function () {
        //     $(this).change();
        // });
    }


    if ($('.disable-past-date').length > 0) {
        $('.disable-past-date').datetimepicker({
            format: 'DD-MM-YYYY',
            icons: {
                up: "fas fa-angle-up",
                down: "fas fa-angle-down",
                next: 'fas fa-angle-right',
                previous: 'fas fa-angle-left'
            },
            minDate: new Date()
        });
        $('.disable-past-date').on('dp.show', function () {
            $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
        }).on('dp.hide', function () {
            $(this).closest('.temp').addClass('table-responsive').removeClass('temp')
        });
    }

    if ($('.dob-date').length > 0) {
        let date = new Date();
        date.setFullYear(date.getFullYear() - 1);
        $('.dob-date').datetimepicker({
            format: 'DD-MM-YYYY',
            icons: {
                up: "fas fa-angle-up",
                down: "fas fa-angle-down",
                next: 'fas fa-angle-right',
                previous: 'fas fa-angle-left'
            },
            maxDate: date
        });
        $('.dob-date').on('dp.show', function () {
            $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
        }).on('dp.hide', function () {
            $(this).closest('.temp').addClass('table-responsive').removeClass('temp')
        });
    }

    if ($('.disable-future-date').length > 0) {
        $('.disable-future-date').datetimepicker({
            format: 'DD-MM-YYYY',
            icons: {
                up: "fas fa-angle-up",
                down: "fas fa-angle-down",
                next: 'fas fa-angle-right',
                previous: 'fas fa-angle-left'
            },
            maxDate: new Date()
        });
        $('.disable-future-date').on('dp.show', function () {
            $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
        }).on('dp.hide', function () {
            $(this).closest('.temp').addClass('table-responsive').removeClass('temp')
        });
    }

});

$('.edit-class-teacher-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let data = new FormData(this);
    let url = $(this).attr('action');

    function successCallback(response) {
        $('#table_list').bootstrapTable('refresh');

        //Reset input file field
        $('.file-upload-default').val('');
        $('.file-upload-info').val('');
        setTimeout(function () {
            window.location.reload();
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$('.add-new-core-subject').on('click', function (e) {
    e.preventDefault();
    let core_subject = cloneNewCoreSubjectTemplate();
    $('#all-core-subjects').append(core_subject);
});

$(document).on('click', '.remove-core-subject', function (e) {
    e.preventDefault();
    let $this = $(this);
    if ($(this).data('id')) {
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
                let id = $this.data('id');
                let url = baseUrl + '/class/subject/' + id;

                function successCallback() {
                    if ($('.core-subject-div').length === 1) {
                        $('.core-subject-div').hide();
                        $('#all-core-subjects').find('.form-control').each(function (e) {
                            $(this).attr('disabled', true);
                        })
                    } else {
                        $this.parent().parent().remove();
                    }
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
            }
        })
    } else {
        if ($('.core-subject-div').length === 1) {
            $('.core-subject-div').hide();
            $('#all-core-subjects').find('.form-control').each(function (e) {
                $(this).attr('disabled', true);
            })
        } else {
            $(this).parent().parent().remove();
        }

    }
});

$(document).on('click', '.add-new-elective-subject', function (e) {
    e.preventDefault();
    let subject_list = cloneNewElectiveSubject();
    $(subject_list).insertAfter($(this).parent().parent());
    let total_selectable_subject = $(this).parent().parent().siblings('.total-selectable-subjects').children().find('.edit-total-selectable-subject');
    let max = $(this).parent().parent().siblings('.elective-subject').length;
    $(total_selectable_subject).rules("add", {
        max: (max + 1),
    });
    $(total_selectable_subject).attr('max', (max + 1));
    hideLastOR();
    changeRemoveElectiveButtonState();
});

$(document).on('click', '.remove-elective-subject', function (e) {
    e.preventDefault();
    let $this = $(this);

    let total_selectable_subject = $(this).parent().parent().parent().siblings('.total-selectable-subjects').children().find('.edit-total-selectable-subject');
    if ($(this).data('id')) {
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
                let id = $this.data('id');
                let url = baseUrl + '/class/subject/' + id;

                function successCallback() {
                    let max = $this.parent().parent().parent().siblings('.elective-subject').length;
                    $(total_selectable_subject).rules("add", {
                        max: max,
                    });
                    $(total_selectable_subject).attr('max', max);
                    $this.parent().parent().parent().remove();
                    hideLastOR();
                    changeRemoveElectiveButtonState();
                }

                ajaxRequest('DELETE', url, null, null, successCallback);
            }
        })
    } else {
        let max = $(this).parent().parent().parent().siblings('.elective-subject').length;
        $(total_selectable_subject).rules("add", {
            max: max,
        });
        $(total_selectable_subject).attr('max', max);

        $(this).parent().parent().parent().remove();

        hideLastOR();
        changeRemoveElectiveButtonState();
    }
});

$(document).on('click', '.add-elective-subject-group', function (e) {
    e.preventDefault();
    let html = cloneNewElectiveSubjectGroup();
    $('#elective-subject-group-div').append(html);
    hideLastOR();
    changeGroupNumber();
});

$(document).on('click', '.remove-elective-subject-group', function (e) {
    e.preventDefault();
    let $this = $(this);
    if ($(this).data('id')) {
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
                let id = $this.data('id');
                let url = baseUrl + '/class/subject-group/' + id;

                function successCallback() {
                    $('#table_list').bootstrapTable('refresh');

                    if ($('.elective-subject-group').length === 1) {
                        $('.elective-subject-group').hide();
                        $this.parent().parent().find('.form-control').each(function (e) {
                            $(this).attr('disabled', true);
                        })
                    } else {
                        $this.parent().parent().remove();
                    }
                    changeGroupNumber();
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
            }
        })
    } else {
        if ($('.elective-subject-group').length === 1) {
            $('.elective-subject-group').hide();
            $(this).parent().parent().find('.form-control').each(function (e) {
                $(this).attr('disabled', true);
            })
        } else {
            $(this).parent().parent().remove();
        }
        changeGroupNumber();
    }

});

$('#show-guardian-details').on('change', function () {
    if ($(this).is(':checked')) {
        $('#guardian_div').show(500);
        $('#guardian_div input,#guardian_div select').attr('disabled', false);
    } else {
        $('#guardian_div').hide(500);
        //Added this to prevent data submission while elective subject option is Off.
        $('#guardian_div input,#guardian_div select').attr('disabled', true);
    }
})

$('#show-edit-guardian-details').on('change', function () {
    if ($(this).is(':checked')) {
        $('#edit_guardian_div').show(500);
        $('#edit_guardian_div input,#edit_guardian_div select').attr('disabled', false);
    } else {
        $('#edit_guardian_div').hide(500);
        //Added this to prevent data submission while elective subject option is Off.
        $('#edit_guardian_div input,#edit_guardian_div select').attr('disabled', true);
    }
})

$(document).on('change', '.file_type', function () {
    var type = $(this).val();
    var parent = $(this).parent();
    if (type == "file_upload") {
        parent.siblings('#file_name_div').show();
        parent.siblings('#file_thumbnail_div').hide();
        parent.siblings('#file_div').show();
        parent.siblings('#file_link_div').hide();
    } else if (type == "video_upload") {
        parent.siblings('#file_name_div').show();
        parent.siblings('#file_thumbnail_div').show();
        parent.siblings('#file_div').show();
        parent.siblings('#file_link_div').hide();
    } else if (type == "youtube_link") {
        parent.siblings('#file_name_div').show();
        parent.siblings('#file_thumbnail_div').show();
        parent.siblings('#file_div').hide();
        parent.siblings('#file_link_div').show();
    } else if (type == "other_link") {
        parent.siblings('#file_name_div').show();
        parent.siblings('#file_thumbnail_div').show();
        parent.siblings('#file_div').hide();
        parent.siblings('#file_link_div').show();
    } else {
        parent.siblings('#file_name_div').hide();
        parent.siblings('#file_thumbnail_div').hide();
        parent.siblings('#file_div').hide();
        parent.siblings('#file_link_div').hide();
    }
})


$(document).on('click', '.add-lesson-file', function (e) {
    e.preventDefault();
    let html = $('.file_type_div:last').clone();
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find(':input').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
    })
    html.find('.add-lesson-file i').addClass('fa-times').removeClass('fa-plus');
    html.find('.add-lesson-file').addClass('btn-inverse-danger remove-lesson-file').removeClass('btn-inverse-success add-lesson-file');
    $(this).parent().parent().siblings('.extra-files').append(html);
    // Trigger change only after the html is appended to DOM
    html.find('.file_type').val('').trigger('change');
    html.find('input').val('');
});

$(document).on('click', '.add-lesson-file', function (e) {
    e.preventDefault();
    let html = $('.file_type_div:last').clone();
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find(':input').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
    })
    html.find('.add-lesson-file i').addClass('fa-times').removeClass('fa-plus');
    html.find('.add-lesson-file').addClass('btn-inverse-danger remove-lesson-file').removeClass('btn-inverse-success add-lesson-file');
    $(this).parent().parent().siblings('.edit-extra-files').append(html);
    // Trigger change only after the html is appended to DOM
    html.find('.file_type').val('').trigger('change');
    html.find('input').val('');
});

$(document).on('click', '.remove-lesson-file', function (e) {
    e.preventDefault();
    let $this = $(this);
    // If button has Data ID then Call ajax function to delete file
    if ($(this).data('id')) {
        let file_id = $(this).data('id');

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
                let url = baseUrl + '/file/delete/' + file_id;
                let data = null;

                function successCallback(response) {
                    $this.parent().parent().remove();
                    $('#table_list').bootstrapTable('refresh');
                    showSuccessToast(response.message);
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('DELETE', url, data, null, successCallback, errorCallback);
            }
        })
    } else {
        // If button don't have any Data Id then simply remove that row from DOM
        $(this).parent().parent().remove();
    }
});

$('#topic_class_section_id').on('change', function () {
    let html = "<option value=''>--Select Lesson--</option>";
    $('#topic_lesson_id').html(html);
    $('#topic_subect_id').trigger('change');
})


$('#top_students').change(function (e) {
    e.preventDefault();
    let exam_id = $(this).val();
    let url = baseUrl + '/get-top-students/';
    let data = {
        'exam_id': $(this).val(),
    };

    function successCallback(response) {
        let html = ""
        var srno = 1;
        if (response.data) {

            response.data.forEach(function (data) {
                html += '<tr>';
                html += '<td>' + srno++ + '</td>';
                html += '<td>' + data.student_name + '</td>';
                html += '<td class="text-center">' + data.class + '</td>';
                html += '<td class="text-center">' + data.obtained_marks + '</td>';
                html += '<td class="text-center">' + data.percentage + '</td>';
                html += '</tr>';
            })

        } else {
            html = '';
        }
        $('#top-student-list').html(html);
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);

});

$('#overview_exam_id').change(function (e) {
    e.preventDefault();
    let exam_id = $(this).val();
    let url = baseUrl + '/exam-overview/';
    let data = {
        'exam_id': exam_id,
    };

    function successCallback(response) {
        let html = ""

        if (response.data) {
            html += '<div class="row">';
            html += '<div class="col-md-12 mt-3">';
            html += '<label for=""><strong># ' + response.data.name + '</strong></label>';
            html += '<div class="progress progress-lg"> <div class="progress-bar bg-success" role="progressbar" style="width: ' + (response.data.exam_statistics_sum_pass * 100) / response.data.exam_statistics_sum_total_attempt_student + '%;animation: progressAnimation 2s" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"> <span class="text-end m-2">' + ((response.data.exam_statistics_sum_pass * 100) / response.data.exam_statistics_sum_total_attempt_student).toFixed(2) + ' %</span> </div> </div>';
            html += '</div>';

            response.data.exam_statistics.forEach(function (data) {
                // html += '<div class="col-sm-12 col-md-6 mt-3"> <label for=""> Class : '+ data.class_section.class.name  +' - '+ data.class_section.section.name +'  </label> <div class="progress progress-lg"> <div class="progress-bar bg-info" role="progressbar" style="width: '+ data.percentage +'%;animation: progressAnimation 2s;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"> <span class="text-end m-2"> '+ data.percentage +'  %</span> </div> </div> </div>';

                html += '<div class="col-sm-12 col-md-6 mt-3"> <label for=""> Class : ' + data.class_section.class.name + ' - ' + data.class_section.section.name + '  </label>';
                if (data.percentage) {
                    html += '<div class="progress progress-lg"> <div class="progress-bar bg-info" role="progressbar" style="width: ' + data.percentage + '%;animation: progressAnimation 2s;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"> <span class="text-end m-2"> ' + data.percentage + '  %</span> </div> </div> </div>';
                } else {
                    html += '<div class="progress progress-lg"> <div class="progress-bar bg-no-data-found" role="progressbar" style="width: 100%;animation: progressAnimation 2s;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"> <span class="text-center text-black"> No exams found </span> </div> </div> </div>';
                }

            })
            html += '</div>';


        } else {
            html = '';
        }
        $('#exam_overview_data').html(html);
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
});

// overview_attendance_class_group_id
$('#overview_attendance_class_group_id').change(function (e) {
    e.preventDefault();
    let class_group_id = $(this).val();
    let url = baseUrl + '/attendance-overview/';
    let data = {
        'class_group_id': class_group_id,
    };

    function successCallback(response) {
        let html = "";
        if (response.data) {
            html += '<div class="row">';
            html += '<div class="col-md-12 mt-3">';
            html += '<label for=""><strong># ' + response.group_name + '</strong></label>';
            html += '</div>';

            response.data.forEach(function (data) {
                if (data.attendance_count == (data.attendance_count + data.absent_attendance_count) && data.attendance_count != 0) {
                    html += '<div class="col-sm-12 col-md-6 mt-3"> <label for=""> Class : ' + data.class.name + ' - ' + data.section.name + '  </label> <div class="progress progress-lg"> <div class="progress-bar bg-info" role="progressbar" style="width: 100%;animation: progressAnimation 2s;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"> <span class="text-end m-2"> 100 %</span> </div> </div> </div>';
                } else if (data.attendance_count == 0 && data.absent_attendance_count == 0) {
                    html += '<div class="col-sm-12 col-md-6 mt-3"> <label for=""> Class : ' + data.class.name + ' - ' + data.section.name + '  </label> <div class="progress progress-lg"> <div class="progress-bar bg-no-data-found" role="progressbar" style="width: 100%;animation: progressAnimation 2s;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"> <span class="text-center text-black">  No data found </span> </div> </div> </div>';
                } else {

                    html += '<div class="col-sm-12 col-md-6 mt-3"> <label for=""> Class : ' + data.class.name + ' - ' + data.section.name + '  </label> <div class="progress progress-lg"> <div class="progress-bar bg-info" role="progressbar" style="width: ' + (data.attendance_count * 100) / (data.attendance_count + data.absent_attendance_count) + '%;animation: progressAnimation 2s;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"> <span class="text-end m-2"> ' + ((data.attendance_count * 100) / (data.attendance_count + data.absent_attendance_count)).toFixed(2) + '  %</span> </div> </div> </div>';
                }
            })
            html += '</div>';
        } else {
            html = '';
        }
        $('#attendance_overview_data').html(html);
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
});

// class_group_id_boys_girls
$('#class_group_id_boys_girls').on('change', function () {
    let url = baseUrl + '/class-group-boys-grils';
    let data = {
        'class_group_id': $(this).val(),
    };

    function successCallback(response) {
        let html = ""
        var class_name = '';
        var boys = '';
        var girls = '';
        class_name = response.class_name;
        boys = response.male_student_count;
        girls = response.female_student_count;

        class_group_wise_boys_girls(class_name, boys, girls)


    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
})


$('#subject_id').on('change', function () {
    let url = baseUrl + '/search-lesson';
    let data = {
        'subject_id': $(this).val(),
        'class_section_id': $('#topic_class_section_id').val()
    };

    function successCallback(response) {
        let html = ""
        if (response.data.length > 0) {
            html += "<option>--Select Lesson--</option>"
            response.data.forEach(function (data) {
                html += "<option value='" + data.id + "'>" + data.name + "</option>";
            })
        } else {
            html = "<option value=''>No Data Found</option>";
        }
        $('#topic_lesson_id').html(html);
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
})
// filter_subject_id
$('#filter_subject_id').on('change', function () {
    let url = baseUrl + '/search-lesson';
    let data = {
        'subject_id': $(this).val(),
        'class_section_id': $('#topic_class_section_id').val()
    };

    function successCallback(response) {
        let html = ""
        if (response.data.length > 0) {
            html += "<option value=''>--Select Lesson--</option>"
            response.data.forEach(function (data) {
                html += "<option value='" + data.id + "'>" + data.name + "</option>";
            })
        } else {
            html = "<option value=''>No Data Found</option>";
        }
        $('#filter_lesson_id').html(html);
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
})

// report_class_section_id
$('#report_class_section_id').change(function (e) {
    e.preventDefault();

    let url = baseUrl + '/get-students';
    let data = {
        'class_section_id': $(this).val()
    };

    function successCallback(response) {
        let html = ""
        if (response.data.length > 0) {
            html += "<option value=''>-- Select Student --</option>";
            response.data.forEach(function (data) {
                html += "<option value='" + data.id + "'>" + data.user.full_name + "</option>";
            })
        } else {
            html = "<option value=''>No Data Found</option>";
        }
        $('#report_student_id').html(html);
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
});


$('#resubmission_allowed').on('change', function () {
    if ($(this).is(':checked')) {
        $(this).val(1);
        $('#extra_days_for_resubmission_div').show();
    } else {
        $(this).val(0);
        $('#extra_days_for_resubmission_div').hide();
    }
})

$('#edit_resubmission_allowed').on('change', function () {
    if ($(this).is(':checked')) {
        $(this).val(1);
        $('#edit_extra_days_for_resubmission_div').show();
    } else {
        $(this).val(0);
        $('#edit_extra_days_for_resubmission_div').hide();
    }
})

$('#edit_topic_class_section_id').on('change', function () {
    let html = "<option value=''>--Select Lesson--</option>";
    $('#topic_lesson_id').html(html);
    $('#topic_subect_id').trigger('change');
})

$('#edit_subject_id').on('change', function () {
    let url = baseUrl + '/search-lesson';
    let data = {
        'subject_id': $(this).val(),
        'class_section_id': $('#edit_topic_class_section_id').val()
    };

    function successCallback(response) {
        let html = ""
        if (response.data.length > 0) {
            response.data.forEach(function (data) {
                html += "<option value='" + data.id + "'>" + data.name + "</option>";
            })
        } else {
            html = "<option value=''>No Data Found</option>";
        }
        $('#edit_topic_lesson_id').html(html);
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
})

$(document).on('click', '.remove-assignment-file', function (e) {
    e.preventDefault();
    var $this = $(this);
    var file_id = $(this).data('id');

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
            let url = baseUrl + '/file/delete/' + file_id;
            let data = null;

            function successCallback(response) {
                $this.parent().remove();
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('DELETE', url, data, null, successCallback, errorCallback);
        }
    })
});

$(document).on('click', '.add-exam-timetable', function (e) {
    e.preventDefault();
    let html = $('.exam_timetable:last').clone();
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find('.form-control').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
    })
    html.find('.add-exam-timetable i').addClass('fa-times').removeClass('fa-plus');
    html.find('.add-exam-timetable').addClass('btn-inverse-danger remove-exam-timetable').removeClass('btn-inverse-success add-exam-timetable');
    $(this).parent().parent().siblings('.extra-timetable').append(html);
    html.find('.form-control').val('');
});

$(document).on('click', '.edit-exam-timetable', function (e) {
    e.preventDefault();
    let html = $('.exam_timetable:last').clone();
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find('.form-control').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
    })
    html.find('.add-exam-timetable i').addClass('fa-times').removeClass('fa-plus');
    html.find('.add-exam-timetable').addClass('btn-inverse-danger remove-exam-timetable').removeClass('btn-inverse-success add-exam-timetable');
    $(this).parent().parent().siblings('.edit-extra-timetable').append(html);
    html.find('.form-control').val('');
});

$(document).on('click', '.remove-exam-timetable', function (e) {
    e.preventDefault();
    let $this = $(this);
    // If button has Data ID then Call ajax function to delete file
    if ($(this).data('id')) {
        let timetable_id = $(this).data('id');

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
                let url = baseUrl + '/exams/delete-timetable/' + timetable_id;

                function successCallback(response) {
                    $this.parent().parent().remove();
                    $('#table_list').bootstrapTable('refresh');
                    showSuccessToast(response.message);
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
            }
        })
    } else {
        // If button don't have any Data Id then simply remove that row from DOM
        $(this).parent().parent().remove();
    }
});

// User search using mobile number
select2Search($(".user-search"), baseUrl + "/user/search", {'type': 'user'}, 'Search for user number', userSearchSelect2DesignTemplate, function (repo) {
    if (!repo.text) {
        //Remove dynamic jquery validation
        $(".user-search").rules("remove", "mobile");
        $(".image").rules("remove", "required");
        $('#first_name').val(repo.first_name).attr('readonly', true);
        $('#last_name').val(repo.last_name).attr('readonly', true);
        $('#mobile').val(repo.mobile).attr('readonly', true);
        $('#dob').val(repo.dob).attr('readonly', true);
        $('#email').val(repo.email).attr('readonly', true);
        $('#current_address').val(repo.current_address).attr('readonly', true);
        $('#permanent_address').val(repo.permanent_address).attr('readonly', true);
        $('.image-div').removeClass('d-none');
        $('#user-image').attr('src', repo.image);
        $('#' + repo.gender).prop('checked', true).trigger('click');
        var role_id = new Array();
        $.each(repo.staff_role, function (key, value) {
            role_id.push(value.role_id);
        });
    } else {
        //Add dynamic jquery validation
        $(".user-search").rules("add", {
            mobile: true,
        });

        $(".image").rules("add", {
            required: true,
        });

        $('.mobile-div').addClass('d-none');
        $('.image-div').addClass('d-none');
        $('#edit_id').val('').attr('readonly', false);
        $('#first_name').val('').attr('readonly', false);
        $('#last_name').val('').attr('readonly', false);
        $('#mobile').val('').attr('readonly', false);
        $('#dob').val('').attr('readonly', false);
        $('#email').val('').attr('readonly', false);
        $('#current_address').val('').attr('readonly', false);
        $('#permanent_address').val('').attr('readonly', false);
        $('#user-image').attr('src', '');

    }
    return repo.mobile || repo.text;
});


//Father Search
select2Search($(".father-search"), baseUrl + "/parent/search", {'type': 'father'}, 'Search for Father First Name OR Mobile', parentSearchSelect2DesignTemplate, function (repo) {
    if (!repo.text) {
        $('#father_email').val(repo.email).attr('readonly', true);
        $('#father_mobile').val(repo.mobile).attr('readonly', true);
        $('#father_occupation').val(repo.occupation).attr('readonly', true);
        $('#father_dob').val(repo.dob).attr('readonly', true);
        $('#father-image-tag').attr('src', repo.image);
    } else {
        $('#father_email').val('').attr('readonly', false);
        $('#father_mobile').val('').attr('readonly', false);
        $('#father_occupation').val('').attr('readonly', false);
        $('#father_dob').val('').attr('readonly', false);
        $('#father-image-tag').attr('src', '');
    }
    return repo.email || repo.text;
});
select2Search($(".mother-search"), baseUrl + "/parent/search", {'type': 'mother'}, 'Search for Mother First Name OR Mobile', parentSearchSelect2DesignTemplate, function (repo) {
    if (!repo.text) {
        $('#mother_email').val(repo.email).attr('readonly', true);
        $('#mother_mobile').val(repo.mobile).attr('readonly', true);
        $('#mother_occupation').val(repo.occupation).attr('readonly', true);
        $('#mother_dob').val(repo.dob).attr('readonly', true);
        $('#mother-image-tag').attr('src', repo.image);
    } else {
        $('#mother_email').val('').attr('readonly', false);
        $('#mother_mobile').val('').attr('readonly', false);
        $('#mother_occupation').val('').attr('readonly', false);
        $('#mother_dob').val('').attr('readonly', false);
        $('#mother-image-tag').attr('src', '');
    }
    return repo.email || repo.text;
});
//Father Search
select2Search($(".guardian-search"), baseUrl + "/parent/search", null, 'Search for Guardian First Name OR Mobile', parentSearchSelect2DesignTemplate, function (repo) {
    if (!repo.text) {
        $('#guardian_email').val(repo.email).attr('readonly', true);
        $('#guardian_mobile').val(repo.mobile).attr('readonly', true);
        $('#guardian_occupation').val(repo.occupation).attr('readonly', true);
        $('#guardian_dob').val(repo.dob).attr('readonly', true);
        $('#guardian-image-tag').attr('src', repo.image).attr('readonly', true);
    } else {
        $('#guardian_email').val('').attr('readonly', false);
        $('#guardian_mobile').val('').attr('readonly', false);
        $('#guardian_occupation').val('').attr('readonly', false);
        $('#guardian_dob').val('').attr('readonly', false);
        $('#guardian-image-tag').attr('src', '').attr('readonly', false);
    }
    return repo.email || repo.text;
});

select2Search($(".edit-father-first-name"), baseUrl + "/parent/search", {'type': 'father'}, 'Search for Father First Name Or Mobile', parentSearchSelect2DesignTemplate, function (repo) {
    fillEditFatherForm(repo.first_name, repo.mobile, repo.occupation, repo.dob, repo.image, repo.email);
    return repo.first_name || repo.text;
});

function fillEditFatherForm(first_name = '', mobile = '', occupation = '', dob = '', image = '', email = '') {
    // $('#edit_father_first_name').val(first_name).attr('readonly', first_name!=='');
    $('#edit_father_email').val(email).attr('readonly', email !== '');
    $('#edit_father_mobile').val(mobile).attr('readonly', mobile !== '');
    $('#edit_father_occupation').val(occupation).attr('readonly', occupation !== '');
    $('#edit_father_dob').val(dob).attr('readonly', dob !== '');
    $('#edit-father-image-tag').attr('src', image);
}

select2Search($(".edit-mother-first-name"), baseUrl + "/parent/search", {'type': 'mother'}, 'Search for Mother First Name Or Mobile', parentSearchSelect2DesignTemplate, function (repo) {
    fillEditMotherForm(repo.first_name, repo.mobile, repo.occupation, repo.dob, repo.image, repo.email);
    return repo.first_name || repo.text;
});

function fillEditMotherForm(first_name = '', mobile = '', occupation = '', dob = '', image = '', email = '') {
    // $('#edit_mother_first_name').val(first_name).attr('readonly', first_name!=='');
    $('#edit_mother_email').val(email).attr('readonly', email !== '');
    $('#edit_mother_mobile').val(mobile).attr('readonly', mobile !== '');
    $('#edit_mother_occupation').val(occupation).attr('readonly', occupation !== '');
    $('#edit_mother_dob').val(dob).attr('readonly', dob !== '');
    $('#edit-mother-image-tag').attr('src', image);
}

select2Search($(".edit-guardian-first-name"), baseUrl + "/parent/search", null, 'Search for Guardian FirstName Or Mobile', parentSearchSelect2DesignTemplate, function (repo) {
    fillEditGuardianForm(repo.first_name, repo.mobile, repo.occupation, repo.dob, repo.image, repo.email);
    return repo.first_name || repo.text;
});

function fillEditGuardianForm(first_name = '', mobile = '', occupation = '', dob = '', image = '', email = '') {
    // $('#edit_guardian_first_name').val(first_name).attr('readonly', first_name!=='');
    $('#edit_guardian_email').val(email).attr('readonly', email !== '');
    $('#edit_guardian_mobile').val(mobile).attr('readonly', mobile !== '');
    $('#edit_guardian_occupation').val(occupation).attr('readonly', occupation !== '');
    $('#edit_guardian_dob').val(dob).attr('readonly', dob !== '');
    $('#edit-guardian-image-tag').attr('src', image);
}

$(document).on('submit', '.setting-form', function (e) {
    e.preventDefault();
    var data = new FormData(this);
    var message = data.get('setting_message');
    let submitButtonElement = $(this).find(':submit');
    var type = $('#type').val();
    var url = $(this).attr('action');
    let submitButtonText = submitButtonElement.val();
    $.ajax({
        type: "POST",
        url: url,
        data: {message: message, type: type},
        beforeSend: function () {
            submitButtonElement.val('Please Wait...').attr('disabled', true);
        },
        success: function (response) {
            if (response.error == false) {
                showSuccessToast(response.message);
                submitButtonElement.val(submitButtonText).attr('disabled', false);
            } else {
                showErrorToast(response.message);
            }
        }

    });
});

$('.general-setting').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        setTimeout(function () {
            location.reload();
        }, 3000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
});

$('#timetable_class_section').on('change', function () {
    if ($(this).val() !== "") {
        $('#timetable-div').removeClass('d-none');
    } else {
        $('#timetable-div').addClass('d-none');
    }
});


$('.assign_student_class').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        formElement[0].reset();
        $('#assign_table_list').bootstrapTable('refresh');
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('.class_section_id').on('change', function () {
    let class_section_id = $(this).val();
    if (class_section_id) {
        let url = baseUrl + '/subject-by-class-section';
        let data = {class_section_id: class_section_id};

        function successCallback(response) {
            if (response.length > 0) {
                let html = '';
                html += '<option>--' + lang_select_subject + '--</option>';
                $.each(response, function (key, value) {
                    html += '<option value="' + value.subject_id + '">' + value.subject.name + ' - ' + value.subject.type + '</option>'
                });
                $('.subject_id').html(html);
            } else {
                $('.subject_id').html("<option value=''>--" + lang_no_data_found + "--</option>");
            }
        }

        ajaxRequest('GET', url, data, null, successCallback, null, null, true)
    }
    $('.subject_id').html('<option>--' + lang_select_subject + '--</option>');

})

$('#edit_class_section_id').on('change', function (e, subject_id) {
    let class_section_id = $(this).val();
    if (class_section_id) {
        let url = baseUrl + '/subject-by-class-section';
        let data = {class_section_id: class_section_id};

        function successCallback(response) {
            if (response.length > 0) {
                let html = '';
                $.each(response, function (key, value) {
                    html += '<option value="' + value.subject_id + '">' + value.subject.name + ' - ' + value.subject.type + '</option>'
                });
                $('#edit_subject_id').html(html);
                if (subject_id) {
                    $('#edit_subject_id').val(subject_id);
                }
            } else {
                $('#edit_subject_id').html("<option value=''>--No data Found--</option>>");
            }
        }

        ajaxRequest('GET', url, data, null, successCallback, null, null, true)
    }

})

$(document).on('change', '.timetable_start_time', function () {
    let $this = $(this);
    let end_time = $(this).parent().siblings().children('.timetable_end_time');
    $(end_time).rules("add", {
        timeGreaterThan: $this,
    });
})

$('#system-update').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        setTimeout(function () {
            window.location.reload();
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$("#create-form-bulk-data").submit(function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        formElement[0].reset();

    }


    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
});


// get classes on Drop down exam changes
$('#exam_options').on('change', function () {
    let exam_id = $(this).val();
    let url = baseUrl + '/exam/get-classes/' + exam_id;
    $.ajax({
        type: "get",
        url: url,
        success: function (response) {
            let html = ""
            if (response.data.length > 0) {
                html += "<option value='" + null + "'>--- Select ---</option>";
                $.each(response.data, function (key, data) {
                    html += "<option value='" + data.class_section.id + "' data-class-id='" + data.class_section.class.id + "'>" + data.class_section.class.name + ' ' + data.class_section.section.name + "</option>";
                });
            } else {
                html = "<option value=''>No Data Found</option>";
            }
            $('#exam_classes_options').html(html);
        }
    });
});

// get classes on Drop down exam changes
$('#exam_id').on('change', function () {
    let exam_id = $(this).val();
    let url = baseUrl + '/exam/get-class/' + exam_id;
    $('#class_id').html("<option value=''>--- Select Class ---</option>")
    $('#section_id').html("<option value=''>--- Select Section---</option>")
    $.ajax({
        type: "get",
        url: url,
        success: function (response) {
            let html = ""
            let class_name = [];
            if (response.data && response.data.exam_class_section.length > 0) {
                html += "<option value=''>--- Select Class ---</option>";
                $.each(response.data.exam_class_section, function (key, data) {
                    if (data.publish == 1) {
                        if (!class_name.includes(data.class_section.class.id)) {
                            class_name.push(data.class_section.class.id);
                            html += "<option value='" + data.class_section.class.id + "' data-class-id='" + data.class_section.class.id + "'>" + data.class_section.class.name + "</option>";
                        }
                    }
                });
            } else {
                html = "<option value=''>No Data Found</option>";
            }
            $('#class_id').html(html);
            $('#section_id').html("<option value=''>--- Select Section---</option>")
        }
    });
});

// get section on Drop down class changes
$('#class_id').on('change', function () {
    let class_id = $(this).val();
    let url = baseUrl + '/get/section/' + class_id;
    $('#section_id').html("<option value=''>--- Select Section---</option>")
    $.ajax({
        type: "get",
        url: url,
        success: function (response) {
            let html = ""
            if (response.data.length > 0) {
                html += "<option value=''>--- Select Section---</option>";
                $.each(response.data, function (key, data) {
                    html += "<option value='" + data.section.id + "' data-class-id='" + data.section.id + "'>" + data.section.name + "</option>";
                });
            } else {
                html = "<option value=''>No Data Found</option>";
            }
            $('#section_id').html(html);
        }
    });
});

// get Subjects on Drop down classes changes
$('#exam_classes_options').on('change', function () {
    // let class_id = $(this).find('option:selected').data('class-id');
    let class_section_id = $(this).val();
    let url = baseUrl + '/exam/get-subjects/' + class_section_id;
    $.ajax({
        type: "get",
        url: url,
        data: {
            'exam_term_id': $("#exam_term_id").val(),
            'exam_sequence_id': $("#exam_sequence_id").val()
        },
        success: function (response) {
            let html = ""
            if (response.data.length > 0) {
                $.each(response.data, function (key, data) {
                    html += "<option value='" + data.subject.id + "'>" + data.subject.name + ' - ' + data.subject.type + "</option>";
                });
            } else {
                html = "<option value=''>No Data Found</option>";
            }
            $('.exam_subjects_options').html(html);
        }
    });
});


// add more subject in create exam timetable
$(document).on('click', '.add-exam-timetable-content', function (e) {
    e.preventDefault();
    let html = $('.exam_timetable_content:last').clone();
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find('.form-control').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
    })
    html.find('.add-exam-timetable-content i').addClass('fa-times').removeClass('fa-plus');
    html.find('.add-exam-timetable-content').addClass('btn-danger remove-exam-timetable-content').removeClass('btn-inverse-success add-exam-timetable');
    html.find('.disable-past-date').datetimepicker({
        format: 'DD-MM-YYYY',
        icons: {
            up: "fas fa-angle-up",
            down: "fas fa-angle-down",
            next: 'fas fa-angle-right',
            previous: 'fas fa-angle-left'
        },
        minDate: new Date()
    });

    $(this).parent().parent().parent().siblings('.extra-timetable').append(html);
    html.find('.form-control').val('');
    html.find('.select2').remove();
    html.find('.select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id');
    html.find('.select option').removeAttr('data-select2-id');
    html.find('.select').select2();
});

// remove more subject in create exam timetable
$(document).on('click', '.remove-exam-timetable-content', function (e) {
    e.preventDefault();
    $(this).parent().parent().parent().remove();
});

$(".exam_class_filter").find("select").change(function () {
    $table.bootstrapTable("refreshOptions", {
        exportDataType: $(this).val()
    });
});

$("#edit_class_id").on('change', function () {
    let data = $(this).find(':selected').data("medium");
    // let url = baseUrl + "/class-subject-list/" + data
    let url = baseUrl + "/class-subject-list/";
    $.ajax({
        type: "GET",
        url: url,
        success: function (response) {
            let html = ""
            if (response.data.length > 0) {
                response.data.forEach(function (data) {
                    html += "<option value='" + data.id + "'>" + data.name + "</option>";
                })
            } else {
                html = "<option value=''>No Data Found</option>";
            }
            $('.core-subject-id').html(html);
            $('.elective-subject-name').html(html)
        }
    });
});

// According to Conditions Show the Button of Adding new row
function checkAddNewRowBtn() {
    if ($('.grade_content').find('.ending_range').length) {
        let chk_max = $(this).val();
        if (chk_max < 100 && chk_max != '') {
            $('.add-grade-content').prop('disabled', false);
        } else {
            $('.add-grade-content').prop('disabled', true);
        }
        $('.ending_range:last').keyup(function (e) {
            let chk_max = $(this).val();
            if (chk_max < 100 && chk_max != '') {
                $('.add-grade-content').prop('disabled', false);
            } else {
                $('.add-grade-content').prop('disabled', true);
            }
        });

    } else {
        $('.add-grade-content').prop('disabled', false);
    }
}

checkAddNewRowBtn();

// create grade ajax
$('#create-grades').on('submit', function (e) {
    e.preventDefault();

    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        window.location.reload();
        checkAddNewRowBtn(); // calling the function of adding new row btn
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('.remove-grades').hide();
$('.grade_content:last').find('.remove-grades').show();
let value = parseInt($('.grade_content:last').find('.ending_range').val());
if (value >= 100) {
    $('.add-grade-content').prop('disabled', true);
} else {
    $('.add-grade-content').prop('disabled', false);
}
//adding new row for grade
$(document).on('click', '.add-grade-content', function (e) {
    e.preventDefault();
    let value = parseFloat($('.grade_content:last').find('.ending_range').val());
    if (value) {
        value = value + 1;
    } else {
        value = 0;
    }
    let html = $('.grade_content:last').clone();
    $('.grade_content:last').find('.remove-grades').hide();
    html.find('.error').remove();
    html.find('.temp_starting_range').removeClass('temp_starting_range').addClass('starting_range');
    html.find('.temp_ending_range').removeClass('temp_ending_range').addClass('ending_range');
    html.find('.temp_grade').removeClass('temp_grade').addClass('grade');
    html.css('display', 'block');
    html.find('.has-danger').removeClass('has-danger');
    html.find('.hidden').remove();
    html.find(".remove-grades").removeAttr('data-id');
    // This function will replace the last index value and increment in the multidimensional name attribute
    $(this).parent().siblings('.extra-grade-content').append(html);
    $('.add-grade-content').prop('disabled', true);
    html.find('.starting_range').val('')
    html.find('.ending_range').val('');
    html.find('.grade').val('');
    html.find('input').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
    })
    let increment_stating_range = html.find('.starting_range').val(value);
    increment_stating_range.attr('min', value);
    let min_attr = parseInt(increment_stating_range.attr("min"));
    increment_stating_range.keyup(function () {
        if ($(this).val()) {
            if ($(this).val() < min_attr) {
                $('.add-grade-content').prop('disabled', true);
            }
        } else {
            $('.add-grade-content').prop('disabled', true);
        }
    });

    let ending_range = html.find('.ending_range');
    ending_range.attr('max', 100);
    ending_range.keyup(function () {
        if ($(this).val()) {
            if ($(this).val() <= min_attr) {
                $('.add-grade-content').prop('disabled', true);
            } else {
                if ($(this).val() < 100) {
                    $('.add-grade-content').prop('disabled', false);
                } else {
                    $('.add-grade-content').prop('disabled', true);
                }
            }
        } else {
            $('.add-grade-content').prop('disabled', true);
        }
    });
});
// delete-course-section
$(document).on('click', '.delete-course-section', function (e) {
    e.preventDefault();
    let $this = $(this);
    let id = $(this).data('id');
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
            let id = $this.data('id');
            let url = baseUrl + '/course/material/delete/' + id;

            function successCallback() {
                // $this.parent().parent().remove();
                $this.parent().parent().slideUp(500);
                showSuccessToast('Data Delete Successfully');
            }

            ajaxRequest('get', url, null, null, successCallback);

        }
    })

});

// remove more grade in create grade
$(document).on('click', '.remove-grades', function (e) {
    e.preventDefault();
    let $this = $(this);
    if ($(this).data('id')) {
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
                let id = $this.data('id');
                let url = baseUrl + '/destroy-grades/' + id;

                function successCallback() {
                    $this.parent().parent().remove();
                    window.location.reload();
                    checkAddNewRowBtn();
                }

                ajaxRequest('DELETE', url, null, null, successCallback);

            }
        })
    } else {
        $(this).parent().parent().parent().remove();
        $('.grade_content:last').find('.remove-grades').show();
        let last_ending_val = $('.grade_content:last').find('.ending_range').val();
        if (last_ending_val >= 100 && last_ending_val == '') {
            $('.add-grade-content').prop('disabled', true);
        } else {
            $('.add-grade-content').prop('disabled', false);
        }
        $('.ending_range:last').keyup(function (e) {
            let chk_max = $(this).val();
            if (chk_max < 100 && chk_max != '') {
                $('.add-grade-content').prop('disabled', false);
            } else {
                $('.add-grade-content').prop('disabled', true);
            }
        });
    }
});

$('.assign_subject_teacher').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        formElement[0].reset();
        $('.select2-selection__rendered').html('');
        $('#table_list').bootstrapTable('refresh');
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('.student-registration-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        setTimeout(function () {
            window.location.reload();
        }, 3000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$('#admin-profile-update').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);


    function successCallback() {
        setTimeout(() => {
            window.location.reload();
        }, 3000);
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('.edit_exam_result_marks_form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);


    function successCallback() {
        $('#editModal').modal('hide');
        $('#table_list').bootstrapTable('refresh');
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('.create_exam_timetable_form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);


    function successCallback() {
        setTimeout(() => {
            window.location.reload();
        }, 3000);
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('.add-new-timetable-data').click(function (e) {
    e.preventDefault();
    let html;
    if (!$('.edit-timetable-container:last').is(':empty')) {
        html = $('.edit-timetable-container').find('.edit_exam_timetable:last').clone();
    } else {
        html = $('.edit_exam_timetable_tamplate').clone();
    }
    html.css('display', 'block');
    html.find('.error').remove();
    html.removeClass('edit_exam_timetable_tamplate').addClass('edit_exam_timetable');
    html.find('.has-danger').removeClass('has-danger');
    html.find('.remove-edit-exam-timetable-content').removeAttr('data-timetable_id');
    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find('.form-control').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
    })

    html.find('.disable-past-date').datetimepicker({
        format: 'DD-MM-YYYY',
        icons: {
            up: "fas fa-angle-up",
            down: "fas fa-angle-down",
            next: 'fas fa-angle-right',
            previous: 'fas fa-angle-left'
        },
        minDate: new Date()
    });

    $(this).parent().siblings('.edit-timetable-container').append(html);
    html.find('.form-control').val('');

});

$('.edit-form-timetable').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        $('#editModal').modal('hide');
        $('#table_list').bootstrapTable('refresh');
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('.verify_email').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);


    function successCallback() {
        setTimeout(() => {
            window.location.reload();
        }, 3000);
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
});
$('.subject_id').on('change', function () {
    // let class_id = $(this).find(':selected').data('class');
    let class_section_id = $('.class_section_id').val();
    let subject_id = $(this).val();
    let url = baseUrl + '/teacher-by-class-subject';
    let data = {
        class_section_id: class_section_id,
        subject_id: subject_id
    };


    function successCallback(response) {
        if (response.length > 0) {
            let html = '';
            $.each(response, function (key, value) {
                html += '<option value="' + value.id + '">' + value.user.first_name + ' ' + value.user.last_name + '</option>'
            });
            $('#teacher_id').html(html);
        } else {
            $('#teacher_id').html("<option value=''>--No data Found--</option>>");
        }
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true)
})

// Select class section by center id
$('#center_id').on('change', function () {
    let center_id = $('#center_id').val();
    let url = baseUrl + '/class-section-by-center';
    let data = {
        center_id: center_id,
    };

    function successCallback(response) {
        if (response.data.length > 0) {
            let html = '';
            html += '<option value="">-Select Class Section-</option>'
            $.each(response.data, function (key, value) {
                html += '<option value="' + value.id + '">' + value.class.name + ' ' + value.section.name + '</option>'
            });
            $('#class_section_id').html(html);
        } else {
            $('#class_section_id').html("<option value=''>--No data Found--</option>>");
        }
        $("#subject_id").prop("selectedIndex", 0);
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true)
})

$('#edit_center_id').on('change', function () {
    let center_id = $('#edit_center_id').val();
    let url = baseUrl + '/class-section-by-center';
    let data = {
        center_id: center_id,
    };

    function successCallback(response) {
        if (response.data.length > 0) {
            let html = '';
            html += '<option value="">-Select Class Section-</option>'
            $.each(response.data, function (key, value) {
                html += '<option value="' + value.id + '">' + value.class.name + ' ' + value.section.name + '</option>'
            });
            $('#edit_class_section_id').html(html);
        } else {
            $('#edit_class_section_id').html("<option value=''>--No data Found--</option>>");
        }
        $('#edit_subject_id').html('<option value="">-Select Subject-</option>');
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true)
})
$('#filter_center_id').on('change', function () {
    let center_id = $('#filter_center_id').val();
    let url = baseUrl + '/class-section-by-center';
    let data = {
        center_id: center_id,
    };

    function successCallback(response) {
        if (response.data.length > 0) {
            let html = '';
            html += '<option value="">All</option>'
            $.each(response.data, function (key, value) {
                html += '<option value="' + value.id + '">' + value.class.name + ' - ' + value.section.name + '</option>'
            });
            $('#filter_class_section_id').html(html);
        } else {
            $('#filter_class_section_id').html("<option value=''>--No data Found--</option>>");
        }
        $('#filter_subject_id').html('<option value="">All</option>');
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true)
})

// center_id_get_class
$('#center_id_get_class').on('change', function () {
    let center_id = $('#center_id_get_class').val();
    let url = baseUrl + '/class-by-center';
    let data = {
        center_id: center_id,
    };

    function successCallback(response) {
        if (response.data.length > 0) {
            let html = '';
            html += '<option value="">--Select Class--</option>'
            $.each(response.data, function (key, value) {
                html += '<option value="' + value.id + '">' + value.name + '</option>'
            });
            $('#class_id').html(html);
        } else {
            $('#class_id').html("<option value=''>--No data Found--</option>>");
        }
        $('#filter_subject_id').html('<option value="">All</option>');
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true)
})

$('#edit_center_id_get_class').on('change', function () {
    let center_id = $('#edit_center_id_get_class').val();
    let url = baseUrl + '/class-by-center';
    let data = {
        center_id: center_id,
    };

    function successCallback(response) {
        if (response.data.length > 0) {
            let html = '';
            html += '<option value="">--Select Class--</option>'
            $.each(response.data, function (key, value) {
                html += '<option value="' + value.id + '">' + value.name + ' < /option>'
            });
            $('#edit-online-exam-class-id').html(html);
        } else {
            $('#edit-online-exam-class-id').html("<option value=''>--No data Found--</option>>");
        }
        $('#filter_subject_id').html('<option value="">All</option>');
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true)
})

// filter_center_id_get_class
$('#filter_center_id_get_class').on('change', function () {
    let center_id = $('#filter_center_id_get_class').val();
    let url = baseUrl + '/class-by-center';
    let data = {
        center_id: center_id,
    };

    function successCallback(response) {
        if (response.data.length > 0) {
            let html = '';
            html += '<option value="">All</option>'
            $.each(response.data, function (key, value) {
                html += '<option value="' + value.id + '">' + value.name + '</option>'
            });
            $('#filter_class_id').html(html);
        } else {
            $('#filter_class_id').html("<option value=''>--No data Found--</option>>");
        }
        $('#filter_subject_id').html('<option value="">All</option>');
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true)
})
$('#filter_class_section_id').on('change', function () {
    let class_section_id = $(this).val();
    if (class_section_id) {
        let url = baseUrl + '/subject-by-class-section';
        let data = {class_section_id: class_section_id};

        function successCallback(response) {
            if (response.length > 0) {
                let html = '';
                html += '<option value="">' + lang_all + '</option>';
                $.each(response, function (key, value) {
                    html += '<option value="' + value.subject_id + '">' + value.subject.name + ' - ' + value.subject.type + '</option>'
                });
                $('#filter_subject_id').html(html);
            } else {
                $('#filter_subject_id').html("<option value=''>--" + lang_no_data_found + "--</option>");
            }
        }

        ajaxRequest('GET', url, data, null, successCallback, null, null, true)
    }

})

$('#edit_subject_id').on('change', function () {

    let edit_id = $('#id').val();
    let class_section_id = $('#edit_class_section_id').val();
    let subject_id = $(this).val();
    let url = baseUrl + '/teacher-by-class-subject';
    let data = {
        edit_id: edit_id,
        class_section_id: class_section_id,
        subject_id: subject_id
    };

    function successCallback(response) {
        if (response.length > 0) {
            let html = '';
            $.each(response, function (key, value) {
                html += '<option value="' + value.id + '">' + value.user.first_name + ' ' + value.user.last_name + '</option>'
            });
            $('#edit_teacher_id').html(html);
        } else {
            $('#edit_teacher_id').html("<option value=''>--No data Found--</option>>");
        }
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true)
})
$('.add-new-fees-type').on('click', function (e) {
    e.preventDefault();
    let html = ''
    if ($('.edit-extra-fees-types').find('.template_fees_type:last').html()) {
        html = $('.edit-extra-fees-types').find('.template_fees_type:last').clone();
        html.find('.form-control').each(function (key, element) {
            this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
                return '[' + (parseInt(p1, 10) + 1) + ']';
            });
            this.id = this.id.replace(/\_(\d+)/, function (str, p1) {
                return '_' + (parseInt(p1, 10) + 1);
            });
            $(element).attr('disabled', false);
        })
        html.find('.form-check-input').each(function (key, element) {
            this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
                return '[' + (parseInt(p1, 10) + 1) + ']';
            });
            this.id = this.id.replace(/\_(\d+)/, function (str, p1) {
                return '_' + (parseInt(p1, 10) + 1);
            });
            $(element).attr('disabled', false);
        })
    } else {
        html = $('.template_fees_type').clone().show();
    }
    html.find('select').siblings('.error').remove();
    html.find('.add-fees-type i').addClass('fa-times').removeClass('fa-plus');
    html.find('.add-fees-type').addClass('btn-inverse-danger remove-fees-type').removeClass('btn-inverse-success add-fees-type');
    $('.edit-extra-fees-types').append(html);
});

$('#fees-class-create-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        setTimeout(function () {
            $('#editModal').modal('hide');
        }, 1000)
        $('#table_list').bootstrapTable('refresh');
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
});
$(document).on('click', '.remove-fees-type', function (e) {
    e.preventDefault();
    // let $this = $(this);
    if ($(this).data('id')) {
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
                let id = $(this).data('id');
                let url = baseUrl + '/class/fees-type/' + id;

                function successCallback(response) {
                    showSuccessToast(response['message']);
                    setTimeout(function () {
                        $('#editModal').modal('hide');
                    }, 1000)
                    $('#table_list').bootstrapTable('refresh');
                    $(this).parent().parent().remove();
                }

                function errorCallback(response) {
                    showErrorToast(response['message']);
                }

                ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
            }
        })
    } else {
        $(this).parent().parent().remove();
    }
});
$('.mode').on('change', function (e) {
    e.preventDefault();
    let mode_val = $(this).val();
    if (mode_val == 1) {
        $('.cheque_no_container').show(200);
    } else {
        $('.cheque_no_container').hide(200);
    }
});
$('.pay_student_fees_offline').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);


    function successCallback() {
        $('#editModal').modal('hide');
        $('.cheque_no_container').hide();
        formElement[0].reset();
        $('#table_list').bootstrapTable('refresh');
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('.edit_mode').on('change', function (e) {
    e.preventDefault();
    let mode_val = $(this).val();
    if (mode_val == 1) {
        $('.edit_cheque_no_container').show(200);
    } else {
        $('.edit_cheque_no_container').hide(200);
    }
});
$(document).on('click', '.remove-paid-choiceable-fees', function (e) {
    e.preventDefault();
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
            let amount = $(this).data("amount");
            // let url = $(this).attr('href');
            let id = $(this).data("id");
            let url = baseUrl + '/fees/paid/remove-choiceable-fees/' + id;
            let data = null;

            function successCallback(response) {
                $('#table_list').bootstrapTable('refresh');
                setTimeout(function () {
                    $('#editFeesPaidModal').modal('hide');
                }, 1000)
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('DELETE', url, data, null, successCallback, errorCallback);
        }
    })
})
$('#create-fees-config-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        setTimeout(() => {
            window.location.reload();
        }, 3000);
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('#edit-fees-paid-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let data = new FormData(this);
    data.append("_method", "PUT");
    let url = $(this).attr('action') + "/" + data.get('edit_id');

    function successCallback(response) {
        $('#table_list').bootstrapTable('refresh');
        setTimeout(function () {
            $('#editFeesPaidModal').modal('hide');
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('#class_timetable_class_section').on('change', function (e) {
    $('.list_buttons').show(200);
    var class_section_id = $(this).val();
    // var class_id = $(this).find(':selected').attr('data-class');
    // var section_id = $(this).find(':selected').attr('data-section');
    $('.set_timetable').html('');
    $.ajax({
        url: baseUrl + '/gettimetablebyclass',
        type: "GET",
        data: {class_section_id: class_section_id},
        success: function (response) {
            var html = '';
            if (response['days'].length) {
                $('.warning_no_data').hide(300);
                for (let i = 0; i < response['days'].length; i++) {
                    html += '<div class="col-lg-2 col-md-2 col-sm-2 col-12 project-grid">';
                    html += '<div class="project-grid-inner">';
                    html += '<div class="wrapper bg-light">';
                    html += '<h5 class="card-header header-sm bg-secondary">' + response['days'][i]['day_name'].charAt(0).toUpperCase() + response['days'][i]['day_name'].slice(1) + '</h5>';
                    for (let j = 0; j < response['timetable'].length; j++) {
                        if (response['days'][i]['day'] == response['timetable'][j]['day']) {
                            html += '<p class="card-body">'
                                + response['timetable'][j]['subject_teacher']['subject']['name'] + ' - ' + response['timetable'][j]['subject_teacher']['subject']['type']
                                + '<br>' + response['timetable'][j]['subject_teacher']['teacher']['user']['first_name'] + ' ' + response['timetable'][j]['subject_teacher']['teacher']['user']['last_name']
                                + '<br>start time: ' + response['timetable'][j]['start_time'] + '<br>end time: '
                                + response['timetable'][j]['end_time'] + '</p><hr>';

                        }
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('.set_timetable').html(html);
                }
            } else {
                $('.warning_no_data').show(300);
                $('.table_content').hide();
            }
        }
    })
});

$('#teacher_timetable_class_section').on('change', function (e) {
    var class_section_id = $(this).val();
    var class_id = $(this).find(':selected').attr('data-class');
    var section_id = $(this).find(':selected').attr('data-section');
    $('.set_timetable').html('');
    $.ajax({
        url: baseUrl + "/get-timetable-by-subject-teacher-class",
        type: "GET",
        data: {class_section_id: class_section_id, class_id: class_id},
        success: function (response) {
            if (response['days'].length) {
                $('.warning_no_data').hide(300);
                var html = '';
                let counter = 0
                for (let i = 0; i < response['days'].length; i++) {
                    html += '<div class="col-lg-2 col-md-2 col-sm-2 col-12 project-grid">';
                    html += '<div class="project-grid-inner">';
                    html += '<div class="wrapper bg-light">';
                    html += '<h5 class="card-header header-sm bg-secondary">' + response['days'][i]['day_name'].charAt(0).toUpperCase() + response['days'][i]['day_name'].slice(1) + '</h5>';
                    for (let j = 0; j < response['timetable'].length; j++) {
                        if (response['days'][i]['day'] == response['timetable'][j]['day']) {
                            html += '<p class="card-body">'
                                + response['timetable'][j]['class_section']['class']['name'] + ' - ' + response['timetable'][j]['class_section']['section']['name']
                                + '<br>' + response['timetable'][j]['subject_teacher']['subject']['name'] + ' - ' + response['timetable'][j]['subject_teacher']['subject']['type']
                                + '<br>start time: ' + response['timetable'][j]['start_time'] + '<br>end time: '
                                + response['timetable'][j]['end_time'] + '</p><hr>';

                        }
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('.set_timetable').html(html);
                }
            } else {
                $('.warning_no_data').show(300);
            }
        }
    })
});

$('#teacher_timetable_teacher_id').on('change', function (e) {
    var teacher_id = $(this).val();
    $('.set_timetable').html('');
    $.ajax({
        url: baseUrl + "/gettimetablebyteacher",
        type: "GET",
        data: {
            teacher_id: teacher_id
        },
        success: function (response) {
            var html = '';
            for (let n = 0; n < response['days'].length; n++) {
                for (let i = 0; i < response['days'][n].length; i++) {
                    html += '<div class="col-lg-2 col-md-2 col-sm-2 col-12 project-grid">';
                    html += '<div class="project-grid-inner">';
                    html += '<div class="wrapper bg-light">';
                    html += '<h5 class="card-header header-sm bg-secondary">' + response['days'][n][i]['day_name'].charAt(0).toUpperCase() + response['days'][n][i]['day_name'].slice(1) + '</h5>';
                    for (let m = 0; m < response['timetable'].length; m++) {
                        if (response['timetable'][m] != '') {
                            for (let j = 0; j < response['timetable'][m].length; j++) {
                                if (response['days'][n][i]['day'] == response['timetable'][m][j]['day']) {
                                    html += '<p class="card-body">' + response['timetable'][m][j]['class_section']['class']['name'] +
                                        ' - ' + response['timetable'][m][j]['class_section']['section']['name'] +
                                        '<br>' + response['timetable'][m][j]['subject_teacher']['subject']['name'] + '-' + response['timetable'][m][j]['subject_teacher']['subject']['type'] +
                                        '<br>start time: ' + response['timetable'][m][j]['start_time'] +
                                        '<br>end time: ' + response['timetable'][m][j]['end_time'] + '</p><hr>';
                                }
                            }
                        }
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('.set_timetable').html(html);
                }
            }
            if (response.days.length > 0 && response.timetable.length > 0) {
                $('.warning_no_data').hide(300);
            } else {
                $('.warning_no_data').show(300);
            }
        }
    })
});
$('#razorpay_status').on('change', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        $('#stripe_status').val(0);
    } else {
        $('#stripe_status').val(1);
    }
});
$('#stripe_status').on('change', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        $('#razorpay_status').val(0);
    } else {
        $('#razorpay_status').val(1);
    }
});
$('#assign-roll-no-form').on('submit', function (e) {
    e.preventDefault();
    Swal.fire({
        title: lang_delete_title,
        text: lang_delete_warning,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: lang_yes_change_it
    }).then((result) => {
        if (result.isConfirmed) {
            let formElement = $(this);
            let submitButtonElement = $(this).find(':submit');
            let url = $(this).attr('action');
            let data = new FormData(this);

            function successCallback() {
                $('#table_list').bootstrapTable('refresh');
            }

            formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
        }
    })
})
$('.online-exam-class-id').on('change', function (e) {
    e.preventDefault();
    let url = baseUrl + '/get-subject-online-exam';
    let data = {
        'class_id': $(this).val()
    };

    function successCallback(response) {
        let html = ""
        if (response.data.length) {
            html += "<option value=''>-- Select Subject --</option>"
            response.data.forEach(function (data) {
                html += "<option value='" + data.subject_id + "'>" + data.subject.name + ' - ' + data.subject.type + "</option>";
            })
            $('#subject_id').html(html);
        } else {
            html = "<option value=''>-- Select Subject --</option>";
            $('#subject_id').html(html);
        }

    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
})

$('#edit-online-exam-class-id').on('change', function (e) {
    e.preventDefault();
    let url = baseUrl + '/get-subject-online-exam';
    let data = {
        'class_id': $(this).val()
    };

    function successCallback(response) {
        let html = ""
        if (response.data.length) {
            html += "<option value=''>-- Select Subject --</option>"
            response.data.forEach(function (data) {
                html += "<option value='" + data.subject_id + "'>" + data.subject.name + ' - ' + data.subject.type + "</option>";
            })
            $('#edit-online-exam-subject-id').html(html);
        } else {
            html = "<option value=''>-- Select Subject --</option>";
            $('#edit-online-exam-subject-id').html(html);
        }
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
})
$('#filter_class_id').on('change', function (e) {
    e.preventDefault();
    let url = baseUrl + '/get-subject-online-exam';
    let data = {
        'class_id': $(this).val()
    };

    function successCallback(response) {
        let html = ""
        if (response.data.length) {
            html += "<option value=''>-- Select Subject --</option>"
            response.data.forEach(function (data) {
                html += "<option value='" + data.subject_id + "'>" + data.subject.name + ' - ' + data.subject.type + "</option>";
            })
        } else {
            html = "<option value=''>-- Select Subject --</option>";
        }
        $('#filter_subject_id').html(html);
    }

    ajaxRequest('GET', url, data, null, successCallback, null, null, true);
})

$('#add-new-option').on('click', function (e) {
    e.preventDefault();
    let html = $('.option-container').find('.form-group:last').clone();
    html.find('.add-question-option').val('');
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    $('.remove-option-content').css('display', 'none');
    html.addClass('quation-option-extra');

    // html.removeClass('col-md-6').addClass('col-md-5');
    // This function will increment in the label option number
    let inner_html = html.find('.option-number:last').html();
    html.find('.option-number:last').each(function (key, element) {
        inner_html = inner_html.replace(/(\d+)/, function (str, p1) {
            return (parseInt(p1, 10) + 1);
        });
    })
    html.find('.option-number:last').html(inner_html)

    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find(':input').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
    })
    html.find('.remove-option-content').html('<button class="btn btn-inverse-danger remove-option btn-sm mt-1" type="button"><i class="fa fa-times"></i></button>')
    $('.option-container').append(html)

    let select_answer_option = '<option value=' + inner_html + ' class="answer_option extra_answers_options">Option ' + inner_html + '</option>'
    $('#answer_select').append(select_answer_option)
});
$(document).on('click', '.remove-option', function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
    $('.option-container').find('.form-group:last').find('.remove-option-content').css('display', 'block');
    $('#answer_select').find('.answer_option:last').remove();
})
$('#create-online-exam-questions-form').on('submit', function (e) {
    e.preventDefault();
    for (var equation_editor in CKEDITOR.instances) {
        CKEDITOR.instances[equation_editor].updateElement();
    }
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        setTimeout(() => {
            window.location.reload();
        }, 3000);
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('.question_type').on('change', function (e) {
    $('.quation-option-extra').remove();
    $('.equation-editor-options-extra').remove();

    // answer_select
    let html = '';
    html += '<option value="1">Option 1</option>';
    html += '<option value="2">Option 2</option>';
    $('#answer_select').html(html);
    $('#answer_select').val(null).trigger("change");
    if ($(this).val() == 1) {
        $('#simple-question').hide();
        $('#equation-question').show(500);
    } else {
        $('#simple-question').show(500);
        $('#equation-question').hide();
    }
})
$('#add-new-eqation-option').on('click', function (e) {
    e.preventDefault();
    let html = $('.equation-option-container').find('.quation-option-template:last').clone();
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    $('.remove-equation-option-content').css('display', 'none');

    // html.removeClass('col-md-6').addClass('col-md-5');
    // This function will increment in the label equation-option-number
    let inner_html = html.find('.equation-option-number:last').html();
    html.find('.equation-option-number:last').each(function (key, element) {
        inner_html = inner_html.replace(/(\d+)/, function (str, p1) {
            return (parseInt(p1, 10) + 1);
        });
    })

    // This function will replace the last index value and increment in the multidimensional name attribute
    let name;
    html.find(':input').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            name = '[' + (parseInt(p1, 10) + 1) + ']';
            return name;
        });
    })

    let option_html = '<div class="form-group col-md-6 equation-editor-options-extra quation-option-template"><label>' + lang_option + ' <span class="equation-option-number">' + inner_html + '</span> <span class="text-danger">*</span></label><textarea class="editor_options" name="eoption' + name + '" placeholder="' + lang_select_option + '"></textarea><div class="remove-equation-option-content"><button class="btn btn-inverse-danger remove-equation-option btn-sm mt-1" type="button"><i class="fa fa-times"></i></button></div></div>'
    $('.equation-option-container').append(option_html).ready(function () {
        createCkeditor();
    });
    let select_answer_option = '<option value=' + inner_html + ' class="answer_option extra_answers_options">' + lang_option + ' ' + inner_html + '</option>'
    $('#answer_select').append(select_answer_option)
});
$(document).on('click', '.remove-equation-option', function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
    $('.equation-option-container').find('.form-group:last').find('.remove-equation-option-content').css('display', 'block');
    $('#answer_select').find('.answer_option:last').remove();
})

$('.edit-question-type').on('change', function (e) {
    if ($(this).val() == 1) {
        $('#edit-simple-question-content').hide();
        $('#edit-equation-question-content').show(500);
    } else {
        $('#edit-simple-question-content').show(500);
        $('#edit-equation-question-content').hide();
    }
})
$(document).on('click', '.add-new-edit-option', function (e) {
    e.preventDefault();
    let html = $('.edit_option_container').find('.form-group:last').clone();
    html.find('.add-edit-question-option').val('');
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    html.find('.edit_option_id').val('')
    let hide_button = {}
    hide_button = $('.remove-edit-option-content:last').find('.remove-edit-option')
    if (hide_button.data('id')) {
        $('.remove-edit-option-content:last').css('display', 'block');
    } else {
        $('.remove-edit-option-content:last').css('display', 'none');
    }

    // This function will increment in the label option number
    let inner_html = html.find('.edit-option-number:last').html();
    html.find('.edit-option-number:last').each(function (key, element) {
        inner_html = inner_html.replace(/(\d+)/, function (str, p1) {
            return (parseInt(p1, 10) + 1);
        });
    })
    html.find('.edit-option-number:last').html(inner_html)

    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find(':input').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
    })
    html.find('.remove-edit-option-content').html('<button class="btn btn-inverse-danger remove-edit-option btn-sm mt-1" type="button"><i class="fa fa-times"></i></button>')
    $('.edit_option_container').append(html)

    let select_answer_option = '<option value="new' + $.trim(inner_html) + '" class="edit_answer_option">' + lang_option + ' ' + inner_html + '</option>'
    $('.edit_answer_select').append(select_answer_option)
});
$(document).on('click', '.remove-edit-option', function (e) {
    e.preventDefault();
    if ($(this).data('id')) {
        Swal.fire({
            title: lang_delete_title,
            text: lang_delete_warning,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: lang_yes_delete
        }).then((result) => {
            if (result.isConfirmed) {
                let id = $(this).data('id');
                let url = baseUrl + '/online-exam-question/remove-option/' + id;

                function successCallback(response) {
                    $('#editModal').modal('hide');
                    setTimeout(function () {
                        $('#table_list').bootstrapTable('refresh');
                    }, 500)
                    showSuccessToast(response.message);
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
            }
        })
    } else {
        $(this).parent().parent().remove();
        $('.edit_answer_select').find('.edit_answer_option:last').remove()
        $('.edit_option_container').find('.form-group:last').find('.remove-edit-option-content').css('display', 'block');
        $('.edit_eoption_container').find('.form-group:last').find('.remove-edit-option-content').css('display', 'block');
    }
});
$(document).on('click', '.remove-answers', function (e) {
    e.preventDefault();
    Swal.fire({
        title: lang_delete_title,
        text: lang_delete_warning,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: lang_yes_delete
    }).then((result) => {
        if (result.isConfirmed) {
            let id = $(this).data('id');
            let url = baseUrl + '/online-exam-question/remove-answer/' + id;

            function successCallback(response) {
                $('#editModal').modal('hide');
                setTimeout(function () {
                    $('#table_list').bootstrapTable('refresh');
                }, 500)
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
        }
    })
});
$('#add-new-question-online-exam').on('submit', function (e) {
    e.preventDefault();
    for (var equation_editor in CKEDITOR.instances) {
        CKEDITOR.instances[equation_editor].updateElement();
    }
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback(response) {
        // Get the CKEditor instance
        var editors = Object.values(CKEDITOR.instances);


        // Loop through each instance
        editors.filter(editor => editor.element.hasClass('editor_question')).forEach(editor => {
            editor.setData(''); // clear the text
            editor.resetDirty(); // reset the points to save the changes
        });

        editors.filter(editor => editor.element.hasClass('editor_options')).forEach(editor => {
            editor.setData(''); // clear the text
            editor.resetDirty(); // reset the points to save the changes
        });


        // remove the extra options of ckeditors
        $(document).find('.equation-editor-options-extra').remove();
        $(document).find('.extra_answers_options').remove();

        $('.add-new-question-container').hide(200)
        $('.add-new-question-button').show(300).ready(function () {
            $('.add-new-question-button').html(lang_add_new_question);
        })
        formElement[0].reset();
        $('#simple-question').show();
        $('#equation-question').hide();

        $('#answer_select').val(null).trigger("change");
        $('.quation-option-extra').remove();
        $('#table_list_exam_questions').bootstrapTable('refresh');

        function checkList(listName, newItem) {
            var dupl = false;
            $("#" + listName + " > div").each(function () {
                if ($(this)[0] !== newItem[0]) {
                    if ($(this).html() == newItem.html()) {
                        dupl = true;
                    }
                }
            });
            return !dupl;
        }

        let li = ''
        if (response.data.question_type == 1) {
            li = $('<div class="list-group row">' +
                '<input type="hidden" name="assign_questions[' + response.data.question_id + '][question_id]" value="' + response.data.question_id + '">' +
                '<li id="q' + response.data.question_id + '"class="list-group-item justify-content-between align-items-center ui-state-default list-group-item-secondary m-2">' + response.data.question_id +
                '<hr class="question - divider"> ' + response.data.question + ' ' +
                '<span class="text-right row"><div class="col-md-10">' +
                '<input type="number" placeholder="Marks" class="list-group-item" name="assign_questions[' + response.data.question_id + '][marks]" style="width: 20%;"></div>' +
                '<div class="col-md-2"><a class="btn btn-danger btn-sm remove-row ml-2" data-id="' + response.data.question_id + '"><i class="fa fa-times" aria-hidden="true"></i></a></div></span></li></div>'
            )
            ;
        } else {
            li = $('<div class="list-group row">' +
                '<input type="hidden" name="assign_questions[' + response.data.question_id + '][question_id]" value="' + response.data.question_id + '">' +
                '<li id="q' + response.data.question_id + '" class="list-group-item justify-content-between align-items-center ui-state-default list-group-item-secondary m-2">' + response.data.question_id +
                '<hr class="question - divider"> ' +
                '<span class="text-center">' + response.data.question + '</span> ' +
                '<span class="text-right row"><div class="col-md-10">' +
                '<input type="number" placeholder="Marks" class="list-group-item" name="assign_questions[' + response.data.question_id + '][marks]" style="width: 20%;"></div>' +
                '<div class="col-md-2"><a class="btn btn-danger btn-sm remove-row ml-2" data-id="' + response.data.question + '"><i class="fa fa-times" aria-hidden="true"></i></a></div></span></li></div>'
            )
            ;
        }
        var pasteItem = checkList("sortable-row", li);
        if (pasteItem) {
            $("#sortable-row").append(li);
        }
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
});
$('.add-new-question-button').on('click', function (e) {
    e.preventDefault();
    $('#answer_select').val(null).trigger("change");
    $('.add-new-question-container').show(300);
    $(this).hide();
    $(this).html('');
})
$('.remove-add-new-question').on('click', function (e) {
    e.preventDefault();
    $('.add-new-question-container').hide(300);
    $('.add-new-question-button').show(300).ready(function () {
        $('.add-new-question-button').html(lang_add_new_question);
    });
})
$(document).on('click', '.remove-row', function (e) {
    let id = $(this).data('id');
    let edit_id = $(this).data('edit_id');
    let $this = $(this);
    if (edit_id) {
        Swal.fire({
            title: lang_delete_title,
            text: lang_delete_warning,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: lang_yes_delete
        }).then((result) => {
            if (result.isConfirmed) {
                let url = baseUrl + '/online-exam/remove-choiced-question/' + edit_id;

                function successCallback(response) {
                    showSuccessToast(response.message);
                    $this.parent().parent().parent().remove();
                    $('#table_list').bootstrapTable('refresh');
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
            }
        })
    } else {
        $(this).parent().parent().parent().remove();
        $('#table_list').bootstrapTable('uncheckBy', {field: 'question_id', values: [id]})
    }
})
$('#store-assign-questions-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        window.location.reload();
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$('#edit-question-form').on('submit', function (e) {
    e.preventDefault();
    for (var equation_editor in CKEDITOR.instances) {
        CKEDITOR.instances[equation_editor].updateElement();
    }
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let data = new FormData(this);
    data.append("_method", "PUT");
    let url = $(this).attr('action') + "/" + data.get('edit_id');

    function successCallback(response) {
        $('#table_list').bootstrapTable('refresh');
        setTimeout(function () {
            $('#editModal').modal('hide');
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$(document).on('click', '.delete-question-form', function (e) {
    e.preventDefault();
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
            let url = $(this).attr('href');
            let data = null;

            function successCallback(response) {
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('DELETE', url, data, null, successCallback, errorCallback);
        }
    })
})
$('#table_list').on('load-success.bs.table', function () {
    createCkeditor();
});
$('#table_list_exam_questions').on('load-success.bs.table', function () {
    createCkeditor();
});
$(document).on('click', '.add-new-edit-eoption', function (e) {
    e.preventDefault();

    // destroy the editors for no cloning the last ckeditor
    for (var equation_editor in CKEDITOR.instances) {
        CKEDITOR.instances[equation_editor].destroy();
    }
    let html = $('.edit_eoption_container').find('.form-group:last').clone();
    html.find('.editor_options').val('');
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    html.find('.edit_eoption_id').val('')
    let hide_button = {}
    hide_button = $('.remove-edit-option-content:last').find('.remove-edit-option')
    if (hide_button.data('id')) {
        $('.remove-edit-option-content:last').css('display', 'block');
    } else {
        $('.remove-edit-option-content:last').css('display', 'none');
    }

    // This function will increment in the label option number
    let inner_html = html.find('.edit-eoption-number:last').html();
    html.find('.edit-eoption-number:last').each(function (key, element) {
        inner_html = inner_html.replace(/(\d+)/, function (str, p1) {
            return (parseInt(p1, 10) + 1);
        });
    })
    html.find('.edit-eoption-number:last').html(inner_html)

    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find(':input').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
    })
    html.find('.remove-edit-option-content').html('<button class="btn btn-inverse-danger remove-edit-option btn-sm mt-1" type="button"><i class="fa fa-times"></i></button>')
    $('.edit_eoption_container').append(html).ready(function () {
        createCkeditor();
    })

    let select_answer_option = '<option value="new' + $.trim(inner_html) + '" class="edit_answer_option">' + lang_option + ' ' + inner_html + '</option>'
    $('.edit_answer_select').append(select_answer_option)
});

$(document).ready(function () {
    /// START :: ACTIVE MENU CODE
    $(".sidebar-menu a").each(function () {
        var pageUrl = window.location.href.split(/[?#]/)[0];

        if (this.href == pageUrl) {
            $(this).addClass("active");
            $(this).parent().parent().addClass("active");
            $(this).parent().parent().css("display", "block");

            $(this).parent().addClass("active"); // add active to li of the current link

            $(this).parent().parent().prev().addClass("active"); // add active class to an anchor
            $(this).parent().parent().parent().addClass("active"); // add active class to an anchor
            $(this).parent().parent().parent().parent().addClass("active"); // add active class to an anchor
        }
        var subURL = $("a#subURL").attr("href");
        if (subURL != 'undefined') {
            if (this.href == subURL) {
                $(this).addClass("active");
                $(this).parent().addClass("active"); // add active to li of the current link
                $(this).parent().parent().addClass("active");
                $(this).parent().parent().prev().addClass("active"); // add active class to an anchor
                $(this).parent().parent().parent().addClass("active"); // add active class to an anchor
            }
        }
    });
    /// END :: ACTIVE MENU CODE
});

$('#type').on('change', function () {
    if ($.inArray($(this).val(), ['dropdown', 'checkbox', 'radio']) > -1) {
        $('#default-values-div').show();
        $('.default_values').attr('disabled', false);
    } else {
        $('#default-values-div').hide();
        $('.default_values').attr('disabled', true);
    }
})

$('#edit_type').on('change', function () {
    if ($.inArray($(this).val(), ['dropdown', 'checkbox', 'radio']) > -1) {
        $('#edit-default-values-div').show();
        $('.edit_default_values').attr('disabled', false);
    } else {
        $('#edit-default-values-div').hide();
        $('.edit_default_values').attr('disabled', true);
    }
})
$('.add-more-default-values').on('click', function (e) {
    e.preventDefault();
    $('.remove-default-values').attr('disabled', false);
    let html = $('#add-default-values .row:last').clone();
    $('#add-default-values').append(html);
})


$('.edit-add-more-default-values').on('click', function (e) {
    e.preventDefault();
    $('.edit-remove-default-values').attr('disabled', false);
    let html = $('#edit-add-default-values .row:last').clone();
    $('#edit-add-default-values').append(html);
})
$(document).on('click', '.remove-default-values', function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
    if ($('#add-default-values .row').length === 2) {
        $('.remove-default-values').attr('disabled', true);
    }
})

$(document).on('click', '.edit-remove-default-values', function (e) {
    e.preventDefault();
    $(this).parent().parent().remove();
    if ($('#edit-add-default-values .row').length === 2) {
        $('.edit-remove-default-values').attr('disabled', true);
    }
})

select2Search($(".teacher-search"), baseUrl + "/teachers/search", null, 'Search for Teacher Mobile', teacherSearchSelect2DesignTemplate, function (repo) {
    if (!repo.text) {
        //Remove dynamic jquery validation
        $(".teacher-image").rules("remove", "required");
        $('#first_name').val(repo.user.first_name).attr('readonly', true);
        $('#last_name').val(repo.user.last_name).attr('readonly', true);
        // $('#mobile').val(repo.user.mobile).attr('readonly', true);
        $('#dob').val(repo.user.dob).attr('readonly', true);
        $('#qualification').val(repo.qualification).attr('readonly', true);
        $('#current_address').val(repo.user.current_address).attr('readonly', true);
        $('#permanent_address').val(repo.user.permanent_address).attr('readonly', true);
        $('#image-tag').attr('src', repo.user.image).show();
        $('#' + repo.user.gender).attr('checked', true);
        $(".teacher-image").rules("add", {
            required: false,
        });
    } else {
        $('#first_name').val('').attr('readonly', false);
        $('#last_name').val('').attr('readonly', false);
        // $('#mobile').val('').attr('readonly', false);
        $('#occupation').val('').attr('readonly', false);
        $('#qualification').val('').attr('readonly', false);
        $('#current_address').val('').attr('readonly', false);
        $('#permanent_address').val('').attr('readonly', false);
        $('#dob').val('').attr('readonly', false);
        $('#image-tag').attr('src', '').hide();
    }
    return (repo.user && repo.user.mobile) || repo.text;
});

$('#class-subject-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let data = new FormData(this);
    data.append("_method", "PUT");
    let url = $(this).attr('action');

    function successCallback(response) {
        window.location.href = baseUrl + "/class/subject";
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
});

$("#select-all-centers,#select-all-roles,#select-all-class-section").click(function () {
    let dropdown = $(this).parent().parent().siblings('select');
    if ($(this).is(':checked')) {
        $(dropdown).find("option").prop("selected", "selected");
        $(dropdown).trigger("change");
    } else {
        $(dropdown).find("option").removeAttr("selected");
        $(dropdown).val('').trigger("change");
    }
});

$('.clear-select2').on('click', function (e) {
    e.preventDefault();
    $(this).siblings('select').val('').trigger('change');
})

$('.start_date').on('change', function () {
    let $this = $(this);
    $('.end_date').rules("add", {
        dateGreaterThan: $this,
    });
})

$('.class-section-in-sequence').select2({dropdownParent: "#editModal"});

$('.generate-new-email').on('click', function (e) {
    e.preventDefault();
    ajaxRequest("GET", baseUrl + "/teacher/generate-email", null, null, function (response) {
        $('#email').val(response.email);
    })
})

function sequentialExamListFormatter(index, row) {
    let html = []
    $.each(row.exams, function (key, exam) {
        html += '<p><b>' + (key + 1) + ':</b> ' + exam.name;
        if (exam.timetable[0]) {
            html += ' : ' + exam.timetable[0].subject.name;
        }
        html += '</p>';
    })
    return html;
}

$('.fees_installment_toggle').on('change', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        $('.fees_installment_content').show(200)
    } else {
        $('.fees_installment_content').hide(200)
    }
})

$('.edit_fees_installment_toggle').on('change', function (e) {
    e.preventDefault();
    if ($(this).val() == 1) {
        $('.edit_fees_installment_content').show(200);
    } else {
        $('.edit_fees_installment_content').hide(200)
    }
})
$(document).on('click', '.add-fee-installment-content', function (e) {
    e.preventDefault();
    let html = $('.fees_installment_content:last').clone();
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find('.form-control').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
        this.id = this.id.replace(/_(\d+)/, function (str, p1) {
            return '_' + (parseInt(p1, 10) + 1);
        });
    })
    html.find('.add-fee-installment-content i').addClass('fa-times').removeClass('fa-plus');
    html.find('.add-fee-installment-content').addClass('btn-inverse-danger remove-exam-timetable-content').removeClass('btn-inverse-success add-exam-timetable');
    $(this).parent().parent().parent().siblings('.extra-fee-installment-content').append(html);
    html.find('.form-control').val('');
});

$('.add-extra-fee-installment-data').on('click', function (e) {
    e.preventDefault();
    // let html = $('.installment-div').find('.edit-installment-container').find('.edit-installment-content:last').clone();
    let html = $('.edit-installment-content-template').clone().show();
    html.find('.error').remove();
    html.find('.has-danger').removeClass('has-danger');
    // This function will replace the last index value and increment in the multidimensional name attribute
    html.find('.form-control').each(function (key, element) {
        this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
            return '[' + (parseInt(p1, 10) + 1) + ']';
        });
        this.id = this.id.replace(/_(\d+)/, function (str, p1) {
            return '_' + (parseInt(p1, 10) + 1);
        });
    })
    html.find('.add-edit-fee-installment-content i').addClass('fa-times').removeClass('fa-plus');
    html.find('.add-edit-fee-installment-content').addClass('btn-inverse-danger remove-edit-fee-installment-content').removeClass('btn-inverse-success add-edit-fee-installment-content');
    html.find('.remove-edit-fee-installment-content').removeAttr("data-id")
    $(this).parent().siblings('.edit-installment-container').append(html);
    html.find('.form-control').val('');
});
$(document).on('click', '.remove-edit-fee-installment-content', function (e) {
    e.preventDefault();
    let $this = $(this);
    if ($(this).data('id')) {
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
                let id = $this.data('id');
                let url = baseUrl + '/remove-installment-data/' + id;

                function successCallback(response) {
                    $('#table_list').bootstrapTable('refresh');
                    setTimeout(function () {
                        $('#editModal').modal('hide');
                    }, 500)
                    showSuccessToast(response.message);
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);

            }
        })
    } else {
        $(this).parent().parent().parent().remove();
    }
});
$(document).on('click', '.pay_in_installment', function (e) {
    if ($(this).is(':checked')) {
        $('#installment_mode').val(1)
        $('.due_charges_whole_year').hide(200);
        $('.installment_rows').show(200);
        $('.compulsory_amount').html(Number(0).toFixed(2))

        let choice_amount = parseInt($('.compulsory_amount').html());
        // Check the Amount And Make PAY Button Clickable Or Not
        if (choice_amount > 1) {
            $(document).find('.compulsory_fees_payment').prop('disabled', false);
        } else {
            $(document).find('.compulsory_fees_payment').prop('disabled', true);
        }
    } else {
        $(document).find('.compulsory_fees_payment').prop('disabled', false);
        $('#installment_mode').val(0)
        $('.installment_rows').hide(200);
        $('.due_charges_whole_year').show(200);
        $('.compulsory_amount').html($(this).data("base_amount"))
    }
})
$(document).on('click', '.remove-installment-fees-paid', function (e) {
    e.preventDefault();
    let $this = $(this);
    if ($(this).data('id')) {
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
                let id = $this.data('id');
                let url = baseUrl + '/fees/paid/remove-installment-fees/' + id;

                function successCallback(response) {
                    showSuccessToast(response.message);
                    $('#table_list').bootstrapTable('refresh');
                    setTimeout(() => {
                        $('#compulsoryModal').modal('hide');
                    }, 500);

                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
            }
        })
    }
});
$('.pay_compulsory_fees_offline').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        $('#compulsoryModal').modal('hide');
        $('.cheque_no_container').hide();
        formElement[0].reset();
        $('#table_list').bootstrapTable('refresh');
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('.pay_optional_fees_offline').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);


    function successCallback() {
        $('#optionalModal').modal('hide');
        $('.cheque_no_container').hide();
        formElement[0].reset();
        $('#table_list').bootstrapTable('refresh');
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$(document).on('click', '.remove-optional-fees-paid', function (e) {
    e.preventDefault();
    let $this = $(this);
    if ($(this).data('id')) {
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
                let id = $this.data('id');
                let url = baseUrl + '/fees/paid/remove-choiceable-fees/' + id;

                function successCallback() {
                    $('#table_list').bootstrapTable('refresh');
                    setTimeout(() => {
                        $('#optionalModal').modal('hide');
                    }, 500);

                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
            }
        })
    } else {
        $(this).parent().parent().remove();
    }
});

$('.ending_range').on('input', function () {
    $(this).attr('max', $(this).val());
    $(this).rules("add", {
        max: $(this).val()
    });
    $(this).parents('.grade_content').first().next().find('.starting_range').attr('min', parseInt($(this).val()) + 1);
    $(this).parents('.grade_content').first().next().find('.starting_range').rules("add", {
        max: parseInt($(this).val()) + 1,
        min: parseInt($(this).val()) + 1
    });
})

$('.starting_range').on('input', function () {
    $(this).attr('min', $(this).val());
    $(this).rules("add", {
        min: $(this).val()
    });
    let prev_ending_range = $(this).parents('.grade_content').first().prev().find('.ending_range');
    prev_ending_range.attr('min', parseInt($(this).val()) - 1);
    prev_ending_range.rules("add", {
        max: parseInt($(this).val()) - 1,
        min: parseInt($(this).val()) - 1
    });
})