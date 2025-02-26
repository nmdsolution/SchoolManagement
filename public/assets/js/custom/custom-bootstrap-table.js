// noinspection DuplicatedCode,EqualityComparisonWithCoercionJS,JSJQueryEfficiency,JSUnusedGlobalSymbols,HtmlDeprecatedAttribute
// noinspection EqualityComparisonWithCoercionJS

"use strict";
//Bootstrap actionEvents
window.mediumEvents = {
    'click .edit-data': function (_e, _value, row) {
        $('#id').val(row.id);
        $('#name').val(row.name);
    }
};

window.roleEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
        $('#name').val(row.name);
    }
};

window.eventEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
        $('#name').val(row.name);
        $('#start_date').val(row.start_date).trigger('change');
        $('#end_date').val(row.end_date).trigger('change');
        $('#location').val(row.location);
    }
};

window.departmentEvents = {
    'click .edit-data': function (e, value, row) {
        $('#id').val(row.id);
        $('#name').val(row.name);
        $('#responsible').val(row.responsible).trigger('change');
        const keys = Object.keys(row.subjects);
        
        $('#subjects').val(keys).trigger('change');
    }
};

function departmentSubjectsFormatter(value, row) {
    let html = '';
    if (Object.keys(row.subjects).length > 0) {
        
        html += '<ol class="list-group-numbered">'; 
        $.each(row.subjects, function (key, name) {
            console.log('departmentSubjectsFormatter', name);
            html += '<li class="">' + name + '</li>';
        });
        html += '</ol>';
    }
    return html;
}

window.lessonEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        //Reset Values
        $('.edit-extra-files').html('')
        $('.edit_file_type_div').show();
        $('#edit_id').val(row.id);
        $('.center_id').val(row.center_id);
        $('#edit_center_id').trigger('change');

        setTimeout(() => {
            $('#edit_class_section_id').val(row.class_section_id).trigger('change');
        }, 500);

        setTimeout(() => {
            $('#edit_subject_id').val(row.subject_id).trigger('change');
        }, 1000);

        $('#edit_name').val(row.name);
        $('#edit_description').val(row.description);
        if (row.file.length > 0) {
            $('.edit-extra-files').html('');
            $.each(row.file, function (key, data) {

                let html = $(editFileHTML());
                $('.edit-extra-files').append(html);
                html = html.show();
                html.removeAttr('id');
                html.find('.error').remove();
                html.find('.has-danger').removeClass('has-danger');
                // This function will replace the last index value and increment in the multidimensional name attribute
                html.find(':input').each(function (_key, _element) {
                    this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
                        // console.log($('.file_type').length);
                        // console.log(str, p1);
                        // return '[' + (parseInt(p1, 10) + 1) + ']';
                        return '[' + (parseInt($('.file_type').length) - 1) + ']';
                    });
                })

                html.find('.edit-lesson-file i').addClass('fa-times').removeClass('fa-plus');
                html.find('.edit-lesson-file').addClass('btn-inverse-danger remove-lesson-file').removeClass('btn-inverse-success edit-lesson-file')
                html.find('.remove-lesson-file').attr('data-id', data.id);
                html.find('#edit_file_id').val(data.id);

                //1 = File Upload , 2 = Youtube Link , 3 = Uploaded Video , 4 = Other Link
                if (data.type == 1) {
                    // 1 = File Ulopad
                    html.find('#edit_file_type').val('file_upload').trigger('change');
                    html.find('#file_preview').attr('href', data.file_url).text(data.file_name);
                    //Used class name as a selector instead of id because of jquery dynamic field validation.
                    html.find('.file_name').val(data.file_name);
                } else if (data.type == 2) {
                    // 2 = YouTube Link
                    html.find('#edit_file_type').val('youtube_link').trigger('change');
                    html.find('#file_thumbnail_preview').attr('src', data.file_thumbnail);
                    html.find('.file_link').val(data.file_url);

                    html.find('.file_name').val(data.file_name);
                } else if (data.type == 3) {
                    // 3 = Uploaded Video
                    html.find('#edit_file_type').val('video_upload').trigger('change');
                    html.find('#file_thumbnail_preview').attr('src', data.file_thumbnail);
                    html.find('#file_preview').attr('src', data.file_url).text(data.file_name);

                    html.find('.file_name').val(data.file_name);
                } else if (data.type == 4) {
                    // 4 = Other Link
                    html.find('#edit_file_type').val('other_link').trigger('change');
                    html.find('#file_thumbnail_preview').attr('src', data.file_thumbnail);

                    html.find('.file_name').val(data.file_name);
                    html.find('.file_link').val(data.file_url);
                }
            })
        } else {
            $('.edit_file_type_div').hide();
        }
    }
};


window.topicEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        //Reset Values
        $('.edit-extra-files').html('')
        $('.edit_file_type_div').show();
        $('#edit_id').val(row.id);
        $('#edit_center_id').val(row.center_id);

        $('#edit_center_id').trigger('change');

        setTimeout(() => {
            $('#edit_class_section_id').val(row.class_section_id).trigger('change');
        }, 500);

        setTimeout(() => {
            $('#edit_subject_id').val(row.subject_id).trigger('change');
        }, 1000);

        setTimeout(() => {
            $('#edit_topic_lesson_id').val(row.lesson_id);
        }, 1500);

        $('#edit_name').val(row.name);
        $('#edit_description').val(row.description);

        if (row.file.length > 0) {
            $('.edit-extra-files').html('');
            $.each(row.file, function (key, data) {
                let html = $(editFileHTML());
                $('.edit-extra-files').append(html);
                html.removeAttr('id');
                html.find('.error').remove();
                html.find('.has-danger').removeClass('has-danger');
                // This function will replace the last index value and increment in the multidimensional name attribute
                html.find(':input').each(function (_key, _element) {
                    this.name = this.name.replace(/\[(\d+)\]/, function (_str, p1) {
                        // return '[' + (parseInt(p1, 10) + 1) + ']';
                        return '[' + (parseInt($('.file_type').length) - 1) + ']';
                    });
                })

                html.find('.edit-lesson-file i').addClass('fa-times').removeClass('fa-plus');
                html.find('.edit-lesson-file').addClass('btn-inverse-danger remove-lesson-file').removeClass('btn-inverse-success edit-lesson-file');
                html.find('.remove-lesson-file').attr('data-id', data.id);
                html.find('#edit_file_id').val(data.id);

                //1 = File Upload , 2 = Youtube Link , 3 = Uploaded Video , 4 = Other Link
                if (data.type == 1) {
                    // 1 = File Ulopad
                    html.find('#edit_file_type').val('file_upload').trigger('change');
                    html.find('#file_preview').attr('href', data.file_url).text(data.file_name);
                    //Used class name as a selector instead of id because of jquery dynamic field validation.
                    html.find('.file_name').val(data.file_name);
                } else if (data.type == 2) {
                    // 2 = YouTube Link
                    html.find('#edit_file_type').val('youtube_link').trigger('change');
                    html.find('#file_thumbnail_preview').attr('src', data.file_thumbnail);
                    html.find('.file_link').val(data.file_url);

                    html.find('.file_name').val(data.file_name);
                } else if (data.type == 3) {
                    // 3 = Uploaded Video
                    html.find('#edit_file_type').val('video_upload').trigger('change');
                    html.find('#file_thumbnail_preview').attr('src', data.file_thumbnail);
                    html.find('#file_preview').attr('src', data.file_url).text(data.file_name);

                    html.find('.file_name').val(data.file_name);
                } else if (data.type == 4) {
                    // 4 = Other Link
                    html.find('#edit_file_type').val('other_link').trigger('change');
                    html.find('#file_thumbnail_preview').attr('src', data.file_thumbnail);

                    html.find('.file_name').val(data.file_name);
                    html.find('.file_link').val(data.file_url);
                }
            })
        } else {
            $('.edit_file_type_div').hide();
        }
    }
};

window.examEvents = {
    'click .publish-exam-result': function (e, _value, row, _index) {
        e.preventDefault();
        // alert('working');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm!'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = baseUrl + '/exams/publish/' + row.id;

                function successCallback(response) {
                    showSuccessToast(response.message);
                    $('#table_list').bootstrapTable('refresh');
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('POST', url, null, null, successCallback, errorCallback);
            }
        })
    },
};
window.sequentialExamEvents = {
    'click .activate-all-exams': function (_e, _value, row, _index) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to Activate all Exams ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = baseUrl + '/exams/sequential/update';
                let data = new FormData();
                data.append('class_section_id', row.id)
                data.append('exam_sequence_id', row.exam_sequence_id)
                data.append('teacher_status', 1);
                data.append('student_status', 1);
                data.append('_method', 'PUT');

                function successCallback() {
                    $('.table_list').bootstrapTable('refresh');
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('POST', url, data, null, successCallback, errorCallback);
            }
        })
    },
    'click .deactivate-all-exams': function (_e, _value, row, _index) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to Deactivate all Exams ?",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = baseUrl + '/exams/sequential/update';
                let data = new FormData();
                data.append('class_section_id', row.id)
                data.append('exam_sequence_id', row.exam_sequence_id)
                data.append('teacher_status', 0);
                data.append('student_status', 0);
                data.append('_method', 'PUT');

                function successCallback() {
                    $('.table_list').bootstrapTable('refresh');
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('POST', url, data, null, successCallback, errorCallback);
            }
        })
    }
};

window.assignmentEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        //Reset to Old Values
        let html_file = '';
        $("#new-files").val(null);
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_instructions').val(row.instructions);
        $('#edit_center_id').val(row.center_id);
        $('#edit_center_id').trigger('change');

        setTimeout(() => {
            $('#edit_class_section_id').val(row.class_section_id);
            $('#edit_class_section_id').trigger('change');
        }, 500);

        setTimeout(() => {
            $('#edit_subject_id').val(row.subject_id);
        }, 1000);

        let dt = new Date(row.due_date);
        let Fromdatetime = dt.getFullYear() + "-" + ("0" + (dt.getMonth() + 1)).slice(-2) + "-" + ("0" + dt.getDate()).slice(-2) + "T" + ("0" + dt.getHours()).slice(-2) + ":" + ("0" + dt.getMinutes()).slice(-2) + ":" + ("0" + dt.getSeconds()).slice(-2);
        $('#edit_due_date').val(Fromdatetime);
        $('#edit_points').val(row.points);
        if (row.resubmission) {
            $('#edit_resubmission_allowed').prop('checked', true).trigger('change');
            $('#edit_extra_days_for_resubmission').val(row.extra_days_for_resubmission);
        } else {
            $('#edit_resubmission_allowed').prop('checked', false).trigger('change');
            $('#edit_extra_days_for_resubmission').val('');
        }

        if (row.file) {
            html_file += '<div class="row">';
            $.each(row.file, function (_key, data) {
                html_file += '<div class="col-sm-12 col-md-4"><a target="_blank" href="' + data.file_url + '" class="m-1">' + data.file_name + '</a> <span class="fa fa-times btn btn-danger remove-assignment-file" data-id=' + data.id + '></span><br><br></div>'
            })
            html_file += '</div><hr>';

            $('#old_files').html(html_file);
        }
    }
};

window.announcementEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        let html_file = '';
        $('#id').val(row.id);
        $('#title').val(row.title);
        $('#description').val(row.description);
        if (row.assign == "Subject") {
            $('#edit_set_data').val('class_section').trigger('change', [row.get_data]);
            setTimeout(() => {
                $('#edit_class_section_id').val(row.assign_to['class_section_id']).trigger('change', [row.assign_to['subject_id']]);
            }, 500);

            // $('#edit_get_data').val(row.assign_to['subject_id']);
        } else {
            $('#edit_set_data').val(row.assign).trigger('change', [row.get_data])
        }
        if (row.file) {
            $.each(row.file, function (_key, data) {
                html_file += '<div class="file"><a target="_blank" href="' + data.file_url + '" class="m-1">' + data.file_name + '</a> <span class="fa fa-times text-danger remove-assignment-file" data-id=' + data.id + '></span><br><br></div>'
            })

            $('#old_files').html(html_file);
        }
    }
};
window.classSubjectEvents = {
    'click .edit-class-subject': function (e, _value, row, _index) {
        e.preventDefault();
        let button = e.currentTarget;
        // window.location.href = $(button).attr('href') + '/' + row.id;
        window.location.href = $(button).attr('href');
    }
};

window.userEvents = {
    'click .edit-data': function (e, value, row) {
        e.preventDefault();

        // remove validation
        $('.error').css('display', 'none');
        $('.form-control').removeClass('form-control-danger');
        $('.form-group').removeClass('has-danger');
        // 
        $('.search-user').addClass('d-none');
        $('#edit_id').val(row.id);
        $('#first_name').val(row.first_name);
        $('#last_name').val(row.last_name);
        $('#email').val(row.email);
        $('#current_address').val(row.current_address);
        $('#permanent_address').val(row.permanent_address);
        $('#dob').val(row.dob).trigger('change');
        $('.mobile-div').removeClass('d-none');
        $('#mobile').val(row.mobile);
        $('.image-div').removeClass('d-none');
        $('#user-image').attr('src', row.image);
        $('#' + row.gender).prop('checked', true);
        $('#roles').val(row.role_id).trigger('change');

        $('#user_mobile').removeAttr('required');

        setTimeout(() => {
            window.scrollTo({top: 0, behavior: 'smooth'});
        }, 200);
    }
};


window.parentEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#edit_id').val(row.id);
        $('#first_name').val(row.first_name);
        $('#last_name').val(row.last_name);
        $('input[name=gender][value=' + row.gender + '].edit').prop('checked', true);
        $('#email').val(row.email);
        $('#mobile').val(row.mobile);
        $('#occupation').val(row.occupation);
        $('#dob').val(row.dob).trigger('change');
        if (row.current_address) {
            $('#current_address_div').show();
            $('#current_address').val(row.current_address);
        } else {
            $('#current_address_div').hide();
        }
        if (row.permanent_address) {
            $('#permanent_address_div').show();
            $('#permanent_address').val(row.permanent_address);
        } else {
            $('#permanent_address_div').hide();
        }
    }
};

window.superTeacherEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#id').val(row.id);
        $('#user_id').val(row.user_id);
        $('#first_name').val(row.first_name);
        $('#last_name').val(row.last_name);
        $('input[name=gender][value=' + row.gender + '].edit').prop('checked', true);
        $('#email').val(row.email);
        $('#mobile').val(row.mobile);
        $('#image-show').attr("src", row.image);
        $('#image').attr("href", row.image);
        $('#dob').val(row.dob).trigger('change');
        if (row.current_address) {
            $('#current_address_div').show();
            $('#current_address').val(row.current_address);
        } else {
            $('#current_address_div').hide();
        }
        if (row.permanent_address) {
            $('#permanent_address_div').show();
            $('#permanent_address').val(row.permanent_address);
        } else {
            $('#permanent_address_div').hide();
        }
    }
};

window.expenseEvents = {

    'click .edit-data': function (_e, _value, row, _index) {
        $('#id').val(row.id);
        $('#item_name').val(row.item_name);
        $('#qty').val(row.qty);
        $('#amount').val(row.amount);
        $('#purchase_by').val(row.purchase_by);
        $('#purchase_from').val(row.purchase_from);
        $('#date').val(row.date).trigger('change');

    }
};

window.courseEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#id').val(row.id);
        $('#name').val(row.name);
        $('#code').val(row.code);
        $('#price').val(row.price);
        $('#duration').val(row.duration);
        $('#description').val(row.description);
        $('#super_teacher').val(row.super_teacher_id);
        $('#super_teacher_name').val(row.super_teachers_name);
        $('#super_teacher_id').val(row.super_teacher_id);
        $('.js-example-basic-multiple').select2({
            placeholder: "Please Select",
            dropdownParent: $("#editModal"),

        });
        $('#super_teacher_id').on('select2:unselecting', function (e) {
            // alert('working');
            e.preventDefault();

            let course_id = document.getElementById('id').value;

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = baseUrl + '/deletesuperteacher/' + course_id + '/' + e.params.args.data.id;

                    function successCallback(response) {
                        showSuccessToast(response.message);
                        $('#editModal').modal('hide');
                        $('#table_list').bootstrapTable('refresh');
                        showSuccessToast(response.message);

                    }

                    function errorCallback(response) {
                        showErrorToast(response.message);
                    }

                    ajaxRequest('DELETE', url, null, null, successCallback, errorCallback);
                }
            })
        });

    }
};

window.studentEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#edit_id').val(row.user_id);
        $('#edit_nationality').val(row.nationality);
        $('#edit_first_name').val(row.first_name);
        $('#edit_last_name').val(row.last_name);
        $('#edit_mobile').val(row.mobile);
        $('#edit_dob').val(row.dob).trigger('change');
        $('#edit_class_section_id').val(row.class_section_id);
        $('#edit_minisec_matricule').val(row.minisec_matricule);
        $('#edit_status').val(row.status);
        $('#edit_admission_no').val(row.admission_no);
        $('#edit_roll_number').val(row.roll_number);
        $('#edit_born_at').val(row.born_at);
        $('#edit_caste').val(row.caste);
        $('#edit_religion').val(row.religion);
        $('#edit_admission_date').val(row.admission_date).trigger('change');
        $('#edit_blood_group').val(row.blood_group);
        $('#edit_height').val(row.height);
        $('#edit_weight').val(row.weight);
        $('#edit_current_address').val(row.current_address);
        $('#edit_permanent_address').val(row.permanent_address);
        $('#edit-student-image-tag').attr('src', row.image_link);
        $('#edit_repeater').attr('checked', row.repeater==1);
        $("input[name=gender][value="+row.gender+"]").prop('checked', true);
        //Father Data

        let id = '';
        let id_with_hash = '';

        // DYNAMIC FIELDS
        setTimeout(function () {
            $('input[type=checkbox].edit_checkbox').removeAttr('checked');
            $('.edit_text_number').val('');
            $('.edit_dropdown').val('');
            $('.edit_textarea').val('');

            $.each(row.dynamic_data_field, function (key, value) {
                if(!row.dynamic_data_field[key]){
                    //Continue statement
                    return;
                }
                id = Object.keys(row.dynamic_data_field[key])[0];
                id_with_hash = '#' + Object.keys(row.dynamic_data_field[key])[0];
                $('#file-' + id + '').val('');
                $('#' + id + '-div').css('display', 'none');

                // Confirm is checkbox or not
                let count = 0;
                Object.keys(value).forEach(function (key) {
                    let value_1 = value[key];
                    if (typeof value_1 === 'object') {
                        count++;
                    }
                });

                if (count == 0) {
                    let myDiv = document.getElementById(id);
                    let tagName = '';
                    // TEXT / NUMBER / TEXTAERA / SELECT / FILE
                    if (myDiv) {
                        tagName = myDiv.tagName;
                        if (tagName == 'A') { // IF INPUT TYPE FILE
                            if (value[id]) {
                                $('#' + id + '-div').css('display', 'block');
                                $(id_with_hash).attr('href', 'storage/' + value[id]);
                                $('#file-' + id + '').val(value[id]);
                            }
                        } else { // TEXT / NUMBER / TEXTAERA / SELECT
                            // let input_type = $(id_with_hash).attr('type');
                            $(id_with_hash).val('');
                            $(id_with_hash).val(value[id]);
                        }

                    } else {
                        // RADIO
                        let input_type = $('#' + value[id]).attr('type');
                        if (input_type == 'radio') {
                            $('#' + value[id]).attr('checked', true);
                        }
                    }

                } else { // CHECKBOX
                    let checkbox_value = [];
                    // console.log(value);

                    $.each(value, function (key_1, value_1) {
                        if (value_1 != null) {
                            checkbox_value.push(Object.keys(value_1));
                        }
                    })

                    $.each(checkbox_value, function (key_1, value_1) {
                        $.each(value_1, function (key_2, value_2) {
                            $('#checkbox_' + value_2).attr('checked', true);
                        })
                    })

                }

            });
        }, 1000);

        $("#edit_father_first_name").select2("trigger", "select", {
            data: {
                id: row.father_id ? row.father_id : "",
                text: row.father_first_name ? row.father_first_name : "",
            }
        });
        //Adding delay to fill data so that select2 code and this code don't conflict each other
        setTimeout(function () {
            // $('#edit_father_first_name').val(row.father_first_name).attr('readonly', true);
            $('#edit_father_email').val(row.father_email).attr('readonly', true);
            $('#edit_father_last_name').val(row.father_last_name).attr('readonly', true);
            $('#edit_father_mobile').val(row.father_mobile).attr('readonly', true);
            $('#edit_father_dob').val(row.father_dob).attr('readonly', true).trigger('change');
            $('#edit_father_occupation').val(row.father_occupation).attr('readonly', true);
            $('#edit-father-image-tag').attr('src', row.father_image_link);
            $(".edit-father-search").rules("remove", "email");
            $(".father_image").rules("remove", "required");
        }, 500);


        //Mother Data
        $("#edit_mother_first_name").select2("trigger", "select", {
            data: {
                id: row.mother_id ? row.mother_id : "",
                text: row.mother_first_name ? row.mother_first_name : "",
            }
        });
        //Adding delay to fill data so that select2 code and this code don't conflict each other
        setTimeout(function () {
            $('#edit_mother_email').val(row.mother_email).attr('readonly', true);
            $('#edit_mother_last_name').val(row.mother_last_name).attr('readonly', true);
            $('#edit_mother_mobile').val(row.mother_mobile).attr('readonly', true);
            $('#edit_mother_dob').val(row.mother_dob).attr('readonly', true).trigger('change');
            $('#edit_mother_occupation').val(row.mother_occupation).attr('readonly', true);
            $('#edit-mother-image-tag').attr('src', row.mother_image_link);
            $(".edit-mother-search").rules("remove", "email");
            $(".mother_image").rules("remove", "required");
        }, 500);


        if (row.guardian_id) {
            $('#show-edit-guardian-details').attr('checked', true).trigger('change');
        } else {
            $('#show-edit-guardian-details').attr('checked', false).trigger('change');
        }


        // Guardian Data
        $("#edit_guardian_first_name").select2("trigger", "select", {
            data: {
                id: row.guardian_id ? row.guardian_id : "",
                text: row.guardian_first_name ? row.guardian_first_name : "",
                // edit_data: true,
            }
        });

        //Adding delay to fill data so that select2 code and this code don't conflict each other
        setTimeout(function () {
            $('#edit_guardian_email').val(row.guardian_email).attr('readonly', true);
            $('#edit_guardian_mobile').val(row.guardian_mobile).attr('readonly', true);
            $('#edit_guardian_dob').val(row.guardian_dob).attr('readonly', true).trigger('change');
            $('#edit_guardian_occupation').val(row.guardian_occupation).attr('readonly', true);
            $('#edit-guardian-image-tag').attr('src', row.guardian_image_link);
            $(".edit-guardian-search").rules("remove", "email");
            $(".guardian_image").rules("remove", "required");

        }, 500);
    },

    'click .delete-data': function (_e, _value, row, _index) {
        localStorage.setItem('selected_name', row.first_name);
        localStorage.setItem('selected_user_id', row.user_id);
    }
};

window.assignmentSubmissionEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        let file_html = "";
        $('#edit_id').val(row.id);
        $('#assignment_name').val(row.assignment_name);
        $('#subject').val(row.subject);
        $('#student_name').val(row.student_name);

        $.each(row.file, function (_key, data) {
            file_html += " <a target='_blank' href='" + data.file_url + "'>" + data.file_name + "</a><br>";
        });

        $('#files').html(file_html);
        if (row.assignment_points) {
            $('#points_div').show();
            $('#assignment_points').text('/ ' + row.assignment_points);
            $('#points').prop('max', row.assignment_points);
            $('#points').val(row.points);
        } else {
            $('#points_div').hide();
            $('#assignment_points').text('');
        }
        $('#feedback').val(row.feedback);
        if (row.status === 1) {
            $('#status_accept').attr('checked', true);
        } else if (row.status === 2) {
            $('#status_reject').attr('checked', true);
        }
    }
};

window.examMarksEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('.student_name').html(row.student_name);
        $('.subject_container').html('');
        let no = 0;
        $.each(row.data, function (_key, data) {
            let html_data = '<div class="row"><input type="hidden" id="marks_id form-control" readonly name="edit[' + no + '][marks_id]" value="' + data.id + '"/><div class="row mx-2"><input type="hidden" id="marks_id form-control" readonly name="edit[' + no + '][exam_id]" value="' + data.timetable.exam_id + '"/><div class="row mx-2"><input type="hidden" id="marks_id form-control" readonly name="edit[' + no + '][student_id]" value="' + row.student_id + '"/><div class="row mx-2"><input type="hidden" id="marks_id form-control" readonly name="edit[' + no + '][passing_marks]" value="' + data.timetable.passing_marks + '"/><div class="form-group col-sm-12 col-md-4"><input type="text" class="subject_name form-control" readonly name="edit[' + no + '][subject_name]" value="' + data.subject.name + '" /></div><div class="form-group col-sm-12 col-md-4"><input type="text" class="total_marks form-control" readonly name="edit[' + no + '][total_marks]" value="' + data.timetable.total_marks + '" /></div><div class="form-group col-sm-12 col-md-4"><input type="text" class="obtained_marks form-control" name="edit[' + no + '][obtained_marks]" value="' + data.obtained_marks + '" /></div></div>';
            $('.subject_container').append(html_data);
            no++;
        });
    }
};

window.examTimetableEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('.edit_timetable_exam_id').val(row.exam_id);
        $('.edit_timetable_class_id').val(row.class_section_id);
        $('.edit_timetable_session_year_id').val(row.session_year_id);

        $('.edit-timetable-container').html('');
        let select_subject_html = "";
        if (row.subjects.length > 0) {
            $.each(row.subjects, function (_key, data) {
                select_subject_html += "<option value='" + data.id + "'>" + data.name + ' - ' + data.type + "</option>";
            });
        } else {
            select_subject_html = "<option value=''>No Data Found</option>";
        }
        $('.edit_exam_subjects_options').html(select_subject_html);
        if (row.timetable.length != 0) {
            $.each(row.timetable, function (_key, value) {
                let html;
                if (!$('.edit-timetable-container:last').is(':empty')) {
                    html = $('.edit-timetable-container').find('.edit_exam_timetable:last').clone();
                } else {
                    html = $('.edit_exam_timetable_tamplate').clone();
                }
                html.addClass('edit_exam_timetable').removeClass('edit_exam_timetable_tamplate');
                html.css('display', 'block');
                html.find('.error').remove();
                html.find('.has-danger').removeClass('has-danger');
                // This function will replace the last index value and increment in the multidimensional name attribute
                html.find('.form-control').each(function (_key, _element) {
                    this.name = this.name.replace(/\[(\d+)\]/, function (_str, p1) {
                        return '[' + (parseInt(p1, 10) + 1) + ']';
                    });
                })

                html.find('.remove-edit-exam-timetable-content').attr("data-timetable_id", value.id);

                html.find('.edit_timetable_id').val(value.id);

                html.find('.edit_timetable_exam_id').val(value.exam_id);

                html.find('.edit_timetable_class_id').val(value.class_id);

                html.find('.edit_exam_subjects_options').val(value.subject_id)

                html.find('.edit_total_marks').val(value.total_marks);

                html.find('.edit_passing_marks').val(value.passing_marks);

                html.find('.edit_start_time').val(value.start_time);

                html.find('.edit_end_time').val(value.end_time);

                html.find('.edit_date').datetimepicker({
                    format: 'DD-MM-YYYY',
                    icons: {
                        up: "fas fa-angle-up",
                        down: "fas fa-angle-down",
                        next: 'fas fa-angle-right',
                        previous: 'fas fa-angle-left'
                    },
                    minDate: new Date(),
                });

                const exam_date = value.date;
                const inputDate = new Date(exam_date);
                // Extract the day, month, and year from the input date object
                const day = inputDate.getDate().toString().padStart(2, "0");
                const month = (inputDate.getMonth() + 1).toString().padStart(2, "0");
                const year = inputDate.getFullYear();
                // Format the output date string in dd-mm-yyyy format
                const outputDateString = `${day}-${month}-${year}`;
                // Output the result
                html.find('.edit_date').val(outputDateString);
                $('.edit-timetable-container').append(html);
            });
            $(document).on('click', '.remove-edit-exam-timetable-content', function (e) {
                e.preventDefault();

                let $this = $(this);
                // If button has Data ID then Call ajax function to delete file
                let timetable_id = $(this).data('timetable_id');

                if (timetable_id) {
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
                                // noinspection JSValidateTypes
                                $this.parent().parent().parent().remove();
                                $('#editModal').modal('hide');
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
                    $(this).parent().parent().parent().remove();
                }

            });
        }
    }
}
window.FeesTypeActionEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_description').val(row.description);
        // if (row.choiceable) {
        //     $('#edit_choiceable_true').val(row.choiceable).attr('checked', true);
        //     $('#edit_choiceable_false').attr('checked', false)
        // } else {
        //     $('#edit_choiceable_false').val(row.choiceable).attr('checked', true);
        //     $('#edit_choiceable_true').removeAttr('checked', false)
        // }
    }
};
window.feesClassEvents = {
    'click .edit-data': function (e, value, row, index) {
        $('#edit_class_id').val(row.class_id);
        $('#class_id').val(row.class_id);

        if (row.fees_type.length) {
            $('.edit-extra-fees-types').html('');
            $.each(row.fees_type, function (key, value) {
                let fees_type = $('.edit-fees-type-div:last').clone().show();
                // Remove the error label from the main html so that duplicate error will not be show
                fees_type.find('select').siblings('.error').remove();

                //Change the Name array attribute for jquery validation
                //Add incremental name value
                fees_type.find('.form-control').each(function (key, element) {
                    this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
                        return '[' + (parseInt(p1, 10) + 1) + ']';
                    });
                    this.id = this.id.replace(/\_(\d+)/, function (str, p1) {
                        return '_' + (parseInt(p1, 10) + 1);
                    });
                    $(element).attr('disabled', false);
                })
                fees_type.find('.form-check-input').each(function (key, element) {
                    this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
                        return '[' + (parseInt(p1, 10) + 1) + ']';
                    });
                    this.id = this.id.replace(/\_(\d+)/, function (str, p1) {
                        return '_' + (parseInt(p1, 10) + 1);
                    });
                    $(element).attr('disabled', false);
                })
                //Fill the Values
                fees_type.find('.remove-fees-type').attr('data-id', value.id);
                fees_type.find('.edit-fees-type-id').val(value.id);
                fees_type.find('select').find("option[value = '" + value.fees_type_id + "']").attr("selected", "selected");
                fees_type.find('.edit_amount').val(value.amount);
                if(value.choiceable){
                    fees_type.find('#editChoiceableNo_'+(key+1)).removeAttr("checked");
                    fees_type.find('#editChoiceableYes_'+(key+1)).attr("checked",true);
                }else{
                    fees_type.find('#editChoiceableYes_'+(key+1)).removeAttr("checked");
                    fees_type.find('#editChoiceableNo_'+(key+1)).attr("checked",true);
                }
                fees_type.find('.edit_choiceable').val(value.choiceable).attr('checked',true);
                $('.edit-extra-fees-types').append(fees_type);
            })
        } else {
            $('.edit-extra-fees-types').html('');
        }
    }
};

window.FeesDiscountActionEvents = {
    'click .edit-data': function (e, value, row, index) {
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_amount').val(row.amount);
        $('#edit_applicable_status').val(row.applicable_status.split(',')).change();
        $('#edit_description').val(row.description);
    },

'click .toggle-status': function (e, value, row, index) {
    e.preventDefault();
    const url = baseUrl + "/fees-discounts/toggle/" + row.id;
    console.log(url);

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to toggle the status?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, toggle it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(url, {_token: $('meta[name="csrf-token"]').attr('content')}, function (response) {
                console.log(response);

                if (!response.error) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    });
                    $('#table_list').bootstrapTable('refresh'); // Refresh the table
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                    });
                }
            }).fail(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.',
                });
            });
        }
    });
}



};



/*window.feesPaidEvents = {
    'click .pay-data': function (_e, _value, row, _index) {
        $('#student_id').val(row.student_id);
        $('#class_id').val(row.class_id);
        if (row.due_charges != 0) {
            $('#due_charges').val(row.due_charges);
        } else {
            $('#due_charges').val(null);
        }
        $('.student_name').html(row.student_name + ' - ' + row.class_name);
        $('.date').val(row.current_date).trigger('change');
        if (row.fees_class_data != null) {
            $('.choiceable_div').show();
            let html = '';
            let base_amount;
            if (row.due_charges != 0) {
                html += '<div class="form-check form-check-inline"><label>Base amount - ' + row.base_amount + '</label></div>'
                html += '<div class="form-check form-check-inline"><label>Due Charges - ' + row.due_charges + '</label></div>'
                base_amount = parseInt(row.base_amount) + parseInt(row.due_charges)
            } else {
                html += '<div class="form-check form-check-inline"><label>Base amount - ' + row.base_amount + '</label></div>'
                base_amount = parseInt(row.base_amount);
            }
            $.each(row.fees_class_data, function (_index, value) {
                html += '<div class="form-check form-check-inline"><label class="form-check-label"><input type="checkbox" name="choiceable_fees[]" class="form-check-input chkclass" value="' + value.fees_type_id + '" data-amount="' + value.amount + '">' + value.fees_type.name + ' - ' + value.amount + '<i class="input-helper"></i></label></div>'
            });
            $('.choiceable_fees_content').html(html);
            $('.total_amount_label').html(base_amount);
            let choice_amount = parseInt(base_amount);
            $('.chkclass').on('click', function (_e) {
                if ($(this).is(':checked')) {
                    $(this).addClass('added_price');
                    $(this).removeClass('chkclass');
                    choice_amount += $(this).data("amount");
                    $('.total_amount_label').html(choice_amount);
                    $('.total_amount').val(choice_amount);
                } else {
                    $(this).addClass('added_price');
                    $(this).removeClass('chkclass');
                    choice_amount -= $(this).data("amount");
                    $('.total_amount_label').html(choice_amount);
                    $('.total_amount').val(choice_amount);
                }
            });
        } else {
            $('.choiceable_div').hide();
        }
    },
    'click .edit-data': function (_e, _value, row, _index) {
        $('#edit_id').val(row.id);
        $('#edit_student_id').val(row.student_id);
        $('#edit_class_id').val(row.class_id);
        $('.edit_total_amount').val(row.total_fees);
        $('.edit_student_name').html(row.student_name + ' - ' + row.class_name);
        $('.edit_date').val(row.formatted_date).trigger('change');
        if (row.mode) {
            $('#edit_mode_cheque').attr('checked', true);
            $('.edit_cheque_no_container').show(200);
            $('#edit_cheque_no').val(row.cheque_no);
        } else {
            $('#edit_mode_cash').attr('checked', true);
            $('.edit_cheque_no_container').hide(200);
        }

        if (row.fees_class_choiceable_data != null || row.fees_class_paid_choiceable_data != null) {
            $('.edit_choiceable_div').show();
            let html = '';
            html += '<div class="form-check form-check-inline"><label class="edit_paid_amount" data-amount=' + row.total_fees + '>Paid amount - ' + row.total_fees + '</label></div>'
            $.each(row.fees_class_choiceable_data, function (_index, value) {
                html += '<div class="form-check form-check-inline"><label class="form-check-label"><input type="checkbox" name="add_new_choiceable_fees[]" class="form-check-input edit_new_chkclass" value="' + value.fees_type_id + '" data-amount="' + value.amount + '">' + value.fees_type.name + ' - ' + value.amount + '<i class="input-helper"></i></label></div>'
            });
            if (row.fees_class_paid_choiceable_data != null) {
                html += '<hr>';
                $.each(row.fees_class_paid_choiceable_data, function (_index, value) {
                    html += '<div><label><a href="#" data-id=' + value.id + ' "data-amount="' + value.total_amount + ' style="color:red" class="remove-paid-choiceable-fees"><i class="fa fa-remove"></i></a> ' + value.fees_type.name + ' - ' + value.total_amount + '</label></div>'
                });
            }
            $('.edit_choiceable_fees_content').html(html);
            $('.edit_total_amount_label').html(row.total_fees);
            let choice_amount = parseInt(row.total_fees);
            $('.edit_new_chkclass').on('click', function (_e) {
                if ($(this).is(':checked')) {
                    $(this).addClass('added_price');
                    $(this).removeClass('chkclass');
                    choice_amount += $(this).data("amount");
                    $('.edit_total_amount_label').html(choice_amount);
                    $('.edit_total_amount').val(choice_amount);
                } else {
                    $(this).addClass('added_price');
                    $(this).removeClass('chkclass');
                    choice_amount -= $(this).data("amount");
                    $('.edit_total_amount_label').html(choice_amount);
                    $('.edit_total_amount').val(choice_amount);
                }
            });
        } else {
            $('.edit_choiceable_div').hide();
        }
    }
};*/
window.feesPaidEvents = {
    'click .compulsory-data': function (e, value, row, index) {
        $(document).find('.cheque_no').val(null)
        $('.cheque_compulsory_mode').attr('checked',false);
        $('.cash_compulsory_mode').attr('checked',true).change();
        $('#compulsory_data_student_id').val(row.student_id);
        $('#compulsory_data_class_id').val(row.class_id);

        $('.student_name').html(row.student_name + ' - (' + row.class_name+')');
        $('.paid_date').val(row.current_date);

        if((row.mode == 1) && (row.type_of_fee == 0 || row.type_of_fee == 1 || row.type_of_fee == null)){
            $(document).find('.mode_container').show(200);
            $('.cash_compulsory_mode').attr('checked',false)
            $('.cheque_compulsory_mode').attr('checked',true).change();
            $(document).find('.cheque_no').val(row.cheque_no)
        }else if(row.mode == 0 && (row.type_of_fee == 0 || row.type_of_fee == 1 || row.type_of_fee == null)){
            $(document).find('.mode_container').show(200);
            $(document).find('.cheque_no').val(null)
            $('.cheque_compulsory_mode').attr('checked',false);
            $('.cash_compulsory_mode').attr('checked',true).change();
        }else if(row.mode == 2 && (row.type_of_fee == 0 || row.type_of_fee == 1 || row.type_of_fee == null)){
            $(document).find('.mode_container').show(200);
            $(document).find('.cheque_no').val(null)
            $('.cheque_compulsory_mode').attr('checked',false);
            $('.mobile_money_compulsory_mode').attr('checked',true).change();
        }else if (row.mode == null){
            $(document).find('.mode_container').show(200);
            $(document).find('.cheque_no').val(null)
            $('.cheque_compulsory_mode').attr('checked',false);
            $('.cash_compulsory_mode').attr('checked',true).change();
        }else{
            $(document).find('.mode_container').hide(200);
        }
        if (row.compulsory_fees != null) {
            $('.compulsory_div').show();
            let html = '';
            let base_amount = 0;

            // Adding the data of compulsory fees with installment data
            html = '<table class="table"><tbody>'
            $.each(row.compulsory_fees, function (index, value) {
                html += '<tr><td scope="row" class="text-left"></td><td colspan="2" class="text-left">'+value.fees_type.name+'</td><td class="text-right">'+(value.amount).toFixed(2)+'</td></tr>'
            });

            //Due Charges For Whole Session Year
            if(row.due_charges.charges){
                html += '<tr class="due_charges_whole_year"><td scope="row" class="text-left"></td><td colspan="2" class="text-left">'+lang_due_charges+'<br><small>'+lang_date+' :- ('+row.due_charges.date+')</small></td><td class="text-right">'+row.due_charges.charges ?? (row.due_charges.charges).toFixed(2)+'</td></tr>'
            }

            // Pay In Installment Tick
            if(row.due_charges){
                html += '<tr class="pay_in_installment_row"><td scope="row" class="text-left"></td><td colspan="2" class="text-left">'+lang_pay_in_installment+'</td><td class="text-right"><input type="checkbox" name="pay_in_installment" class="form-check-input pay_in_installment" value="" data-base_amount="'+row.base_amount_with_due_charges+'"></td></tr>'
            }else{
                html += '<tr class="pay_in_installment_row"><td scope="row" class="text-left"></td><td colspan="2" class="text-left">'+lang_pay_in_installment+'</td><td class="text-right"><input type="checkbox" name="pay_in_installment" class="form-check-input pay_in_installment" value="" data-base_amount="'+row.base_amount+'"></td></tr>'
            }

            // Get the total Count of Installment is paid
            let paid_installment_count = -1;
            if(row.installment_data.length){
                $.each(row.installment_data, function (index, installment_data) {
                    if(installment_data.paid){
                        paid_installment_count++;
                    }
                });
            }

            // show the installment data
            if(row.installment_data.length){
                // get installment amount
                let installment_amount = (Number(row.base_amount) / Number(row.installment_data.length)).toFixed(2);
                $.each(row.installment_data, function (index, installment_data) {
                    // if due charges applicable then show the data accordingly
                    if(installment_data.due_charges_applicable){
                        let due_charges = (installment_amount * Number(installment_data.due_charges) / 100).toFixed(2)
                        let total_installment_amount = (Number(installment_amount) + Number(due_charges)).toFixed(2);

                        // if the data is last
                        if(row.installment_data.length == (index + 1 )){
                            // if the data is paid
                            if(installment_data.paid){
                                //if the count of paid installment equals to index of loop
                                if(paid_installment_count == index){
                                    // then show the cross sign
                                    html += '<tr class="installment_rows"><td scope="row" class="text-left"><span class="remove-installment-fees-paid text-left" data-id="'+installment_data.paid_id+'"><i class="fa fa-times text-danger" style="cursor:pointer" aria-hidden="true"></i></span></td><td colspan="2" class="text-left"><lable>'+installment_data.name+'<lable><br><small class="text-success">'+lang_paid_on+': '+installment_data.paid_on+'</small></td><td class="text-right"><lable>'+total_installment_amount+'</lable></td></tr>'
                                }else{
                                    html += '<tr class="installment_rows"><td scope="row" class="text-left"></td><td colspan="2" class="text-left"><lable>'+installment_data.name+'<lable><br><small class="text-success">'+lang_paid_on+': '+installment_data.paid_on+'</small></td><td class="text-right"><lable>'+total_installment_amount+'</lable></td></tr>'
                                }
                            }else{
                                // show the check box
                                html += '<tr class="installment_rows"><td scope="row" class="text-left"><input type="checkbox" name="installment_fees['+index+'][id]" class="installment-chkclass" value="' + installment_data.id + '" data-amount="' + total_installment_amount + '"></td><td colspan="2" class="text-left"><lable>'+installment_data.name+'<lable><br><small class="text-danger">'+lang_due_date_on+': '+installment_data.due_date+',<br>'+lang_charges+': '+installment_data.due_charges+' %</small></td><td class="text-right"><lable>'+installment_amount+'<br><small>'+due_charges+'</small><br><hr>'+total_installment_amount+'</lable><input type="hidden" name="installment_fees['+index+'][amount]" value="'+total_installment_amount+'"><input type="hidden" name="installment_fees['+index+'][fully_paid]" value="1"><input type="hidden" name="installment_fees['+index+'][due_charges]" value="'+due_charges+'"></td></tr>'
                            }
                        }else{
                            // if the installment is paid
                            if(installment_data.paid){
                                //if the count of paid installment equals to index of loop
                                if(paid_installment_count == index){
                                    // then show the cross sign
                                    html += '<tr class="installment_rows"><td scope="row" class="text-left"><span class="remove-installment-fees-paid text-left" data-id="'+installment_data.paid_id+'"><i class="fa fa-times text-danger" style="cursor:pointer" aria-hidden="true"></i></span></td><td colspan="2" class="text-left"><lable>'+installment_data.name+'<lable><br><small class="text-success">'+lang_paid_on+': '+installment_data.paid_on+'</small></td><td class="text-right"><lable>'+total_installment_amount+'</lable></td></tr>'
                                }else{
                                    html += '<tr class="installment_rows"><td scope="row" class="text-left"></td><td colspan="2" class="text-left"><lable>'+installment_data.name+'<lable><br><small class="text-success">'+lang_paid_on+': '+installment_data.paid_on+'</small></td><td class="text-right"><lable>'+total_installment_amount+'</lable></td></tr>'
                                }
                            }else{
                                // show the check box
                                html += '<tr class="installment_rows"><td scope="row" class="text-left"><input type="checkbox" name="installment_fees['+index+'][id]" class="installment-chkclass" value="' + installment_data.id + '" data-amount="' + total_installment_amount + '"></td><td colspan="2" class="text-left"><lable>'+installment_data.name+'<lable><br><small class="text-danger">'+lang_due_date_on+': '+installment_data.due_date+',<br>'+lang_charges+': '+installment_data.due_charges+' %</small></td><td class="text-right"><lable>'+installment_amount+'<br><small>'+due_charges+'</small><br><hr>'+total_installment_amount+'</lable><input type="hidden" name="installment_fees['+index+'][amount]" value="'+total_installment_amount+'"><input type="hidden" name="installment_fees['+index+'][fully_paid]" value="0"><input type="hidden" name="installment_fees['+index+'][due_charges]" value="'+due_charges+'"></td></tr>'
                            }
                        }
                    }else{
                        // if the due charges is not applicable to installment
                        // if the data is last
                        if(row.installment_data.length == (index + 1 )){
                            if(installment_data.paid){
                                if(paid_installment_count == index){
                                    html += '<tr class="installment_rows"><td scope="row" class="text-left"><span class="remove-installment-fees-paid text-left" data-id="'+installment_data.paid_id+'"><i class="fa fa-times text-danger" style="cursor:pointer" aria-hidden="true"></i></span></td><td colspan="2" class="text-left"><lable>'+installment_data.name+'<lable><br><small class="text-success">'+lang_paid_on+': '+installment_data.paid_on+'</small></td><td class="text-right"><lable>'+installment_amount+'</lable></td></tr>'
                                }else{
                                    html += '<tr class="installment_rows"><td scope="row" class="text-left"></td><td colspan="2" class="text-left"><lable>'+installment_data.name+'<lable><br><small class="text-success">'+lang_paid_on+': '+installment_data.paid_on+'</small></td><td class="text-right"><lable>'+installment_amount+'</lable></td></tr>'
                                }
                            }else{
                                html += '<tr class="installment_rows"><td scope="row" class="text-left"><input type="checkbox" name="installment_fees['+index+'][id]" class="installment-chkclass" value="' + installment_data.id + '" data-amount="' + installment_amount + '" ></td><td colspan="2" class="text-left"><lable>'+installment_data.name+'<lable><br><small>'+lang_due_date_on+': '+installment_data.due_date+',</small><br><small>'+lang_charges+': '+installment_data.due_charges+' %</small></td><td class="text-right"><lable>'+installment_amount+'</lable><input type="hidden" name="installment_fees['+index+'][amount]" value="'+installment_amount+'"><input type="hidden" name="installment_fees['+index+'][fully_paid]" value="1"></td></tr>'
                            }
                        }else{
                            if(installment_data.paid){
                                if(paid_installment_count == index){
                                    html += '<tr class="installment_rows"><td scope="row" class="text-left"><span class="remove-installment-fees-paid text-left" data-id="'+installment_data.paid_id+'"><i class="fa fa-times text-danger" style="cursor:pointer" aria-hidden="true"></i></span></td><td colspan="2" class="text-left"><lable>'+installment_data.name+'<lable><br><small class="text-success">'+lang_paid_on+': '+installment_data.paid_on+'</small></td><td class="text-right"><lable>'+installment_amount+'</lable></td></tr>'
                                }else{
                                    html += '<tr class="installment_rows"><td scope="row" class="text-left"></td><td colspan="2" class="text-left"><lable>'+installment_data.name+'<lable><br><small class="text-success">'+lang_paid_on+': '+installment_data.paid_on+'</small></td><td class="text-right"><lable>'+installment_amount+'</lable></td></tr>'
                                }
                            }else{
                                html += '<tr class="installment_rows"><td scope="row" class="text-left"><input type="checkbox" name="installment_fees['+index+'][id]" class="installment-chkclass" value="' + installment_data.id + '" data-amount="' + installment_amount + '" ></td><td colspan="2" class="text-left"><lable>'+installment_data.name+'<lable><br><small>'+lang_due_date_on+': '+installment_data.due_date+',</small><br><small>'+lang_charges+': '+installment_data.due_charges+' %</small></td><td class="text-right"><lable>'+installment_amount+'</lable><input type="hidden" name="installment_fees['+index+'][amount]" value="'+installment_amount+'"><input type="hidden" name="installment_fees['+index+'][fully_paid]" value="0"></td></tr>'
                            }
                        }
                    }
                });
            }
            html += '<tr><td scope="row" class="text-left"></td><td colspan="2" class="text-left"></td><td class="text-right"><hr></td></tr>';

            // Add Total Amount Section
            if(row.due_charges.charges){
                html += '<tr><td scope="row" class="text-left"></td><td colspan="2" class="text-left">'+lang_total_amount+' </td><td class="text-right compulsory_amount">'+(row.base_amount_with_due_charges).toFixed(2)+'</td></tr>';
            }else{
                html += '<tr><td scope="row" class="text-left"></td><td colspan="2" class="text-left">'+lang_total_amount+' </td><td class="text-right compulsory_amount">'+(row.base_amount).toFixed(2)+'</td></tr>';
            }
            html += '</tbody></table>';


            if(row.due_charges){
                $('#total_amount').val(row.base_amount_with_due_charges);
            }else{
                $('#total_amount').val(row.base_amount);
            }
            $('.compulsory_fees_content').html(html);
            $('.installment_rows').hide();

            // make Installment Payment Enabled disabled
            if(row.is_installment_paid){
                $(document).find('.pay_in_installment').click();
                $(document).find('.pay_in_installment').attr('disabled',true);
            }else if(row.fees_status == 1){
                $(document).find('.pay_in_installment_row').hide(200);
                $(document).find('.pay_in_installment').attr('disabled',true);
            }else{
                $(document).find('.pay_in_installment').attr('disabled',false);
            }
            if(row.fees_status == 0 || row.fees_status == null){
                if(row.mode == 2){
                    $(document).find('.compulsory_fees_payment').prop('disabled', true);
                }else{
                    $(document).find('.compulsory_fees_payment').prop('disabled', false);
                }
            }else{
                $(document).find('.compulsory_fees_payment').prop('disabled', true);
            }


            $('.installment-chkclass').on('click', function (e) {
                let choice_amount = parseFloat($('.compulsory_amount').html());
                if ($(this).is(':checked')) {
                    $(document).find('.mode_container').show(200);
                    $(this).addClass('added_price');
                    $(this).removeClass('installment-chkclass');
                    choice_amount += parseFloat($(this).data("amount"));
                    $('.compulsory_amount').html((choice_amount).toFixed(2));
                    $('.total_amount').val((choice_amount).toFixed(2));
                } else {
                    $(document).find('.mode_container').hide(200);
                    $(this).removeClass('added_price');
                    $(this).addClass('installment-chkclass');
                    choice_amount -= parseFloat($(this).data("amount"));
                    $('.compulsory_amount').html((choice_amount).toFixed(2));
                    $('.total_amount').val((choice_amount).toFixed(2));
                }

                // Check the Amount And Make PAY Button Clickable Or Not
                if(choice_amount > 1){
                    $(document).find('.compulsory_fees_payment').prop('disabled', false);
                }else{
                    $(document).find('.compulsory_fees_payment').prop('disabled', true);
                }

                $('#total_amount').val(choice_amount);
            });
        } else {
            $('.compulsory_div').hide();
        }
    },

        'click .optional-data': function (e, value, row, index) {
        // Disable PAY Button
        $(document).find('.optional_fees_payment').prop('disabled', true);

        // Add data in Modal
        $('#optional_student_id').val(row.student_id);
        $('#optional_class_id').val(row.class_id);
        $('.student_name').html(row.student_name + ' - (' + row.class_name + ')');
        $('.current-date').val(row.current_date);

        if (row.mode == 1 && (row.type_of_fee == 2 || row.type_of_fee == null)) {
            $(document).find('.mode_container').show(200);
            $('.cash_mode').prop('checked', false);
            $('.cheque_mode').prop('checked', true).change();
            $(document).find('.cheque_no').val(row.cheque_no);
        } else if (row.mode == 0 && (row.type_of_fee == 2 || row.type_of_fee == null)) {
            $(document).find('.mode_container').show(200);
            $(document).find('.cheque_no').val(null);
            $('.cheque_mode').prop('checked', false);
            $('.cash_mode').prop('checked', true).change();
        } else if (row.mode == 2 && (row.type_of_fee == 2 || row.type_of_fee == null)) {
            $(document).find('.mode_container').show(200);
            $(document).find('.cheque_no').val(null);
            $('.cheque_mode').prop('checked', false);
            $('.mobile_money_mode').prop('checked', true).change();
        } else if (row.mode == null) {
            $(document).find('.mode_container').show(200);
            $(document).find('#cheque_no').val(null);
            $('.cheque_mode').prop('checked', false);
            $('.cash_mode').prop('checked', true).change();
        } else {
            $(document).find('.mode_container').hide(200);
        }

        // IF Optional Fee is not Empty
        if (row.choiceable_fees.length) {
            // Make Optional DIV visible
            $('.optional_div').show();

            // Declare HTML
            let html = '';

            // Adding the data of Optional Fees with Paid Optional Fee
            html = '<table class="table"><tbody>';
            $.each(row.choiceable_fees, function (index, value) {
                // IF is Paid then add CROSS ICON for Delete Else Add Checkbox
                if (value.is_paid) {
                    if (value.date) {
                        html += '<tr><td scope="row" class="text-left"><span class="remove-optional-fees-paid text-left" data-id="' + value.paid_id + '"><i class="fa fa-times text-danger" style="cursor:pointer" aria-hidden="true"></i></span></td><td colspan="2" class="text-left">' + value.name + '<br><span class="text-small text-success">(' + lang_paid_on + ' :- ' + value.date + ')</span></td><td class="text-right">' + (value.amount).toFixed(2) + '</td></tr>';
                    } else {
                        html += '<tr><td scope="row" class="text-left"><span class="remove-optional-fees-paid text-left" data-id="' + value.paid_id + '"><i class="fa fa-times text-danger" style="cursor:pointer" aria-hidden="true"></i></span></td><td colspan="2" class="text-left">' + value.name + '</td><td class="text-right">' + (value.amount).toFixed(2) + '</td></tr>';
                    }
                } else {
                    html += '<tr><td scope="row" class="text-left"><input type="checkbox" class="chkclass" name="optional_fees_type_data[' + index + '][id]" value="' + value.fees_type_id + '" data-amount="' + value.amount + '"></td><td colspan="2" class="text-left">' + value.name + '</td><td class="text-right">' + (value.amount).toFixed(2) + '<input type="hidden" name="optional_fees_type_data[' + index + '][amount]" value=' + value.amount + '></td></tr>';
                }
            });

            // Add Total Amount Section
            html += '<tr><td></td><td colspan="2" class="text-left">' + lang_total_amount + ' </td><td class="text-right"><strong><span class="optional_total_amount_label"></span></strong><input type="hidden" name="total_amount" class="optional_total_amount"></td></tr></tbody></table>';

            // Add The Html to Optional DIV
            $('.optional_fees_content').html(html);

            // Make Total Amount Fixed to 2 Decimal Points
            $('.optional_total_amount_label').html((0).toFixed(2));

            // Get the Amount Of Total Amount From DIV
            let choice_amount = parseInt($('.optional_total_amount_label').html());
            $('.chkclass').on('click', function (e) {
                // Check if Checkbox Checked or not then Update the total Amount Accordingly
                if ($(this).is(':checked')) {
                    $(document).find('.amount_paid').hide();
                    $(document).find('.mode_container').show(200);
                    $(this).addClass('added_price');
                    $(this).removeClass('chkclass');
                    (choice_amount += $(this).data("amount")).toFixed(2);
                    $('.optional_total_amount_label').html((choice_amount).toFixed(2));
                    $('.optional_total_amount').val((choice_amount).toFixed(2));
                } else {
                    $(document).find('.amount_paid').show();
                    $(this).removeClass('added_price');
                    $(this).addClass('chkclass');
                    (choice_amount -= $(this).data("amount")).toFixed(2);
                    $('.optional_total_amount_label').html((choice_amount).toFixed(2));
                    $('.optional_total_amount').val((choice_amount).toFixed(2));
                }

                // Enable PAY button if conditions are met
                let feePaidValue = parseFloat($(document).find('.amount_paid').val()) || 0;
                if (choice_amount > 1 || choice_amount > feePaidValue) {
                    $(document).find('.optional_fees_payment').prop('disabled', false);
                } else {
                    $(document).find('.optional_fees_payment').prop('disabled', true);
                }
            });

            // Handle input in .amount_paid field
            $('.amount_paid').on('input', function () {
                let feePaidValue = parseFloat($(this).val()) || 0;
                if (feePaidValue < choice_amount) {
                    $(document).find('.optional_fees_payment').prop('disabled', true);
                } else {
                    $(document).find('.optional_fees_payment').prop('disabled', false);
                }
            });
        } else {
            $('.compulsory_div').hide();
        }
    },


    'click .edit-data': function (e, value, row, index) {
        $('#edit_id').val(row.id);
        $('#edit_student_id').val(row.student_id);
        $('#edit_class_id').val(row.class_id);
        $('.edit_total_amount').val(row.total_fees);
        $('.edit_student_name').html(row.student_name + ' - ' + row.class_name);
        $('.edit_date').val(row.formatted_date);
        if (row.mode==1) {
            $('#edit_mode_cheque').attr('checked', true);
            $('.edit_cheque_no_container').show(200);
            $('#edit_cheque_no').val(row.cheque_no);
        } else if(row.mode==0) {
            $('#edit_mode_cash').attr('checked', true);
            $('.edit_cheque_no_container').hide(200);
        } else if(row.mode==2) {
            $('#edit_mode_mobile_money').attr('checked', true);
            $('.edit_cheque_no_container').hide(200);
        }

        if (row.fees_class_choiceable_data != null || row.fees_class_paid_choiceable_data != null) {
            $('.edit_choiceable_div').show();
            let html = '';
            html += '<div class="form-check form-check-inline"><label class="edit_paid_amount" data-amount=' + row.total_fees + '>Paid amount - ' + row.total_fees + '</label></div>'
            $.each(row.fees_class_choiceable_data, function (index, value) {
                html += '<div class="form-check form-check-inline"><label class="form-check-label"><input type="checkbox" name="add_new_choiceable_fees[]" class="form-check-input edit_new_chkclass" value="' + value.fees_type_id + '" data-amount="' + value.amount + '">' + value.fees_type.name + ' - ' + value.amount + '<i class="input-helper"></i></label></div>'
            });
            if (row.fees_class_paid_choiceable_data != null) {
                html += '<hr>';
                $.each(row.fees_class_paid_choiceable_data, function (index, value) {
                    html += '<div><label><a href="#" data-id=' + value.id + ' data-amount=' + value.amount + ' style="color:red" class="remove-paid-choiceable-fees"><i class="fa fa-remove"></i></a> ' + value.fees_type.name + ' - ' + value.amount + '</label></div>'
                });
            }
            $('.edit_choiceable_fees_content').html(html);
            $('.edit_total_amount_label').html(row.total_fees);
            let choice_amount = parseInt(row.total_fees);
            $('.edit_new_chkclass').on('click', function (e) {
                if ($(this).is(':checked')) {
                    $(this).addClass('added_price');
                    $(this).removeClass('chkclass');
                    choice_amount += $(this).data("amount");
                    $('.edit_total_amount_label').html(choice_amount);
                    $('.edit_total_amount').val(choice_amount);
                } else {
                    $(this).addClass('added_price');
                    $(this).removeClass('chkclass');
                    choice_amount -= $(this).data("amount");
                    $('.edit_total_amount_label').html(choice_amount);
                    $('.edit_total_amount').val(choice_amount);
                }
            });
        } else {
            $('.edit_choiceable_div').hide();
        }
    }
};

window.onlineExamEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#edit_id').val(row.online_exam_id);
        $('#edit-online-exam-class-id').val(row.class_id).trigger('change');
        setTimeout(() => {
            $('#edit-online-exam-subject-id').val(row.subject_id).trigger('change');
        }, 1000);
        $('#edit-online-exam-title').val(row.title);
        $('#edit-online-exam-key').val(row.exam_key);
        $('#edit-online-exam-duration').val(row.duration);
        $('#edit-online-exam-start-date').val(row.start_date).trigger('change');
        $('#edit-online-exam-end-date').val(row.end_date).trigger('change');
    },
};
window.onlineExamQuestionsEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#edit_id').val(row.online_exam_question_id);
        $('.edit_question_type').val(row.question_type);

        $('#edit_center_id_get_class').val(row.center_id).trigger('change');

        setTimeout(() => {
            $('#edit-online-exam-class-id').val(row.class_id).trigger('change');
        }, 500);


        //added the subject on class id after 0.5 seconds
        setTimeout(() => {
            $('#edit-online-exam-subject-id').val(row.subject_id);
        }, 1500);

        if (row.question_type) {
            $('.edit_question').html('')
            $('.edit_option_container').html('')
            // set data in question text area
            CKEDITOR.instances['edit_equestion'].setData(row.question_row)

            $('#edit-simple-question').hide(100)
            $('#edit-equation-question').show(300);
            $('.edit_eoption_container').html('')

            let html_option = '';
            $.each(row.options, function (index, value) {
                if (index >= 2) {
                    html_option += '<div class="form-group col-md-6"><input type="hidden" class="edit_eoption_id" name="edit_eoption[' + (index + 1) + '][id]" value=' + value.id + '><label>' + lang_option + ' <span class="edit-eoption-number">' + (index + 1) + '</span> <span class="text-danger">*</span></label><textarea class="editor_options" name="edit_eoption[' + (index + 1) + '][option]" placeholder="' + lang_enter_option + '">' + value.option_row + '</textarea><div class="remove-edit-option-content"><button type="button" class="btn btn-inverse-danger remove-edit-option btn-sm mt-1" data-id="' + value.id + '"><i class="fa fa-times"></i></button></div></div>'
                    $('.edit_eoption_container').html(html_option);
                } else {
                    html_option += '<div class="form-group col-md-6"><input type="hidden" class="edit_eoption_id" name="edit_eoption[' + (index + 1) + '][id]" value=' + value.id + '><label>' + lang_option + ' <span class="edit-eoption-number">' + (index + 1) + '</span> <span class="text-danger">*</span></label><textarea class="editor_options" name="edit_eoption[' + (index + 1) + '][option]" placeholder="' + lang_enter_option + '">' + value.option_row + '</textarea></div>'
                    $('.edit_eoption_container').html(html_option);
                }
            });
            createCkeditor();
        } else {
            $('#edit-equation-question').hide(100);
            $('#edit-simple-question').show(300);
            $('.edit_option_container').html('')

            $('.edit-question').html(row.question);
            // add options and add the options in answers
            let html = ''
            $.each(row.options, function (index, value) {
                if (index >= 2) {
                    html = '<div class="form-group col-md-6"><input type="hidden" class="edit_option_id" name="edit_options[' + (index + 1) + '][id]" value=' + value.id + '><label>' + lang_option + ' <span class="edit-option-number"> ' + (index + 1) + '</span> <span class="text-danger">*</span></label><input type="text" name="edit_options[' + (index + 1) + '][option]" value="' + value.option + '" placeholder="' + lang_enter_option + '" class="form-control add-edit-question-option" /><div class="remove-edit-option-content"><button type="button" class="btn btn-inverse-danger remove-edit-option btn-sm mt-1" data-id="' + value.id + '"><i class="fa fa-times"></i></button></div></div>';
                } else {
                    html = '<div class="form-group col-md-6"><input type="hidden" class="edit_option_id" name="edit_options[' + (index + 1) + '][id]" value=' + value.id + '><label>' + lang_option + ' <span class="edit-option-number"> ' + (index + 1) + '</span> <span class="text-danger">*</span></label><input type="text" name="edit_options[' + (index + 1) + '][option]" value="' + value.option + '" placeholder="' + lang_enter_option + '" class="form-control add-edit-question-option" /><div class="remove-edit-option-content"></div></div>';
                }
                $('.edit_option_container').append(html);
            });
        }
        $('.answers_db').html('');
        $('.edit_answer_select').html('');
        if (row.answers.length) {
            $.each(row.options, function (index, value) {
                $.each(row.answers, function (_answer_index, answer_value) {
                    if (value.id == answer_value.option_id) {
                        if (row.answers.length == 1) {
                            let html = '<i class="fa fa-circle" aria-hidden="true"></i> ' + lang_option + ' ' + (index + 1) + '<br>';
                            $('.answers_db').append(html);
                            return false;
                        } else {
                            let html = '<i class="fa fa-circle" aria-hidden="true"></i> ' + lang_option + ' ' + (index + 1) + ' <span class="fa fa-times text-danger remove-answers" data-id=' + answer_value.id + ' style="cursor:pointer"></span><br>';
                            $('.answers_db').append(html);
                            return false;
                        }
                    }
                });
            });
        }

        if (row.options_not_answers) {
            $.each(row.options, function (index, value) {
                $.each(row.options_not_answers, function (_answer_index, option_data) {
                    if (value.id == option_data.id) {
                        $('.edit_answer_select').append('<option value="' + (option_data.id) + '">' + lang_option + ' ' + (index + 1) + '</option>');

                        return false;
                    }
                });
            });
        }

        $('.edit_answer_select').ready(function () {
            if ($('.answers_db').html() == '') {
                $('.edit_answer_select').attr('required', true);
            } else {
                $('.edit_answer_select').removeAttr('required');
            }
        })
        $('#image_preview').attr('src', row.image);
        $('.edit_note').val(row.note);

    },
};
window.teacherActionEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#id').val(row.id);
        $('#user_id').val(row.user_id);
        $('#edit_first_name').val(row.first_name);
        $('#edit_last_name').val(row.last_name);
        $('input[name=gender][value=' + row.gender + '].edit').prop('checked', true);
        $('#edit_current_address').val(row.current_address);
        $('#edit_permanent_address').val(row.permanent_address);
        $('#edit_salary').val(row.salary);
        $('#edit_mobile').val(row.mobile);
        $('#edit_dob').val(row.dob).trigger('change');
        $('#edit_qualification').val(row.qualification);

        $('#manage_student_parent').prop("checked", false);
        document.getElementById("manage_student_parent").checked = false;
        if (row.manage_student_parent == 1) {
            document.getElementById("manage_student_parent").checked = true;
        } else {
            document.getElementById("manage_student_parent").checked = false;
        }

        if (row.manage_student_parent) {
            // $('.edit_permission_chk').prop("checked", false);
            $('.edit_permission_chk').addClass('warning_ckh')
            $(document).on('change', '.warning_ckh', function () {
                if (!this.checked) {
                    Swal.fire({
                        title: lang_delete_title,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: lang_yes_uncheck
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).prop("checked", false);
                        } else {
                            $(this).prop("checked", true);
                        }
                    })
                }
            });
        } else {
            $('.edit_permission_chk').prop("checked", false);
            $('.edit_permission_chk').removeClass('warning_ckh')
        }
    }
}

window.sectionEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
    }
};

window.subjectEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_code').val(row.code);
        $('#edit_bg_color').asColorPicker('val', row.bg_color);
        // $('input[name=medium_id][value=' + row.medium_id + '].edit').prop('checked', true);
        $('input[name=type][value=' + row.type + '].edit').prop('checked', true);
    }
};

window.classEvents = {
    'click .edit-data': function (_e, _value, row) {
        //Reset the Checkbox and Radio Button
        $('input[name="section_id[]"].edit').prop('checked', false)
        $('input[name=medium_id].edit').prop('checked', false);

        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_stream_id').val(row.stream_id);
        $('#edit_shift_id').val(row.shift_id);
        $('input[name=medium_id][value=' + row.medium_id + '].edit').prop('checked', true);


        // $('input[name=medium_id][value=' + row.medium_id + '].edit').prop('checked', true);
        row.sections.forEach(function (data) {
            $('input[name="section_id[]"][value=' + data.id + '].edit').prop('checked', true);
        });
    }
};

window.classTeacherEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#class_section_id').val(row.id);

        //hidden input to store id
        $('#class_section_id_value').val(row.id);

        $('#teacher_id').val(row.teacher_id);
        if (row.teacher_id != null) {
            $('#remove_class_teacher').show();
            $('#teacher_name').html(row.teacher)

            $('#remove_class_teacher').on('click', function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Remove Class Teacher!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Confirm!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let url = baseUrl + '/remove-class-teacher/' + row.id;

                        function successCallback(response) {
                            $(function () {
                                toastr["success"](response.message);
                            });
                            // $.toast({
                            //     text: response.message,
                            //     icon: 'success',
                            //     loader: false,
                            //     position: 'top-right',
                            // });
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }

                        function errorCallback(response) {
                            showErrorToast(response.message);
                        }

                        ajaxRequest('POST', url, null, null, successCallback, errorCallback);
                    }
                });
            });
        } else {
            $('#remove_class_teacher').hide();
        }
    }
};

window.subjectTeachersEvents = {
    'click .edit-data': function (_e, _value, row, _index) {

        $('#id').val(row.id);
        $('#edit_class_section_id').val(row.class_section_id).trigger('change', row.subject_id);
        setTimeout(() => {
            $('#edit_subject_id').val(row.subject_id).trigger('change');
            setTimeout(() => {
                $('#edit_teacher_id').val(row.teacher_id).trigger('change');
            }, 500);
        }, 1000);
    }
};

window.holidayEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#id').val(row.id);
        $('#date').val(row.date).trigger('change');
        $('#title').val(row.title);
        $('#description').val(row.description);
    }
};

window.sliderEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#edit_id').val(row.id);
        $('#edit_slider_image').attr('src', row.image);
        $('#edit_url').val(row.url);

        let center_ids = row.centers.map(({id}) => (id));
        $('#edit-center-id').val(center_ids).trigger('change');

        let role_ids = row.roles.map(({id}) => (id));
        $('#edit-role-id').val(role_ids).trigger('change');

        $('input[name="delete_center_id[]"]').remove();
        $('input[name="delete_role_id[]"]').remove();
    }
};
/*window.sessionYearEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#id').val(row.id);
        $('#name').val(row.name);
        $('#start_date').val(row.start_date).trigger('change');
        $('#end_date').val(row.end_date).trigger('change');
    }
};*/

window.sessionYearEvents = {
    'click .edit-data': function(e, value, row, index) {
        $('#id').val(row.id);
        $('#name').val(row.name);
        $('#start_date').val(row.start_date);
        $('#end_date').val(row.end_date);
        $('#fees_due_date').val(row.fees_due_date);
        $('#fees_due_charges').val(row.fees_due_charges);
        // $('#edit_include_fee_installments').val(row.include_fee_installments).trigger('change');
        $('.edit_fees_installment_toggle[value='+row.include_fee_installments+']').prop('checked',true);
        setTimeout(function(){
            $('.edit_fees_installment_toggle[value='+row.include_fee_installments+']').trigger('change');
        },500)

        if(row.fee_installments.length){

            let html = '';
            $('.edit-installment-container').html("");
            $('.installment-div').show();
            $.each(row.fee_installments, function (key, value) {
                if (!$('.edit-installment-container:last').is(':empty')) {
                    html = $('.edit-installment-container').find('.edit-installment-content:last').clone();
                } else {
                    html = $('.edit-installment-content-template').clone();
                }
                html.addClass('edit-installment-content').removeClass('edit-installment-content-template');
                html.css('display', 'block');
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
                html.find('#editInstallmentId_'+(key+1)).val(value.id);
                html.find('#editInstallmentName_'+(key+1)).val(value.name);
                html.find('#editInstallmentDueDate_'+(key+1)).val(value.due_date);
                html.find('#editInstallmentDueCharges_'+(key+1)).val(value.due_charges);

                html.find('.add-edit-fee-installment-content i').addClass('fa-times').removeClass('fa-plus');
                html.find('.add-edit-fee-installment-content').addClass('btn-danger remove-edit-fee-installment-content').removeClass('btn-inverse-success add-edit-fee-installment-content');
                html.find('.remove-edit-fee-installment-content').attr("data-id", value.id);

                $('.edit-installment-container').append(html);
            });
        }else{
            $('.installment-div').hide();
            $('.edit-installment-container').html("");
        }
    }
};

window.languageEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#id').val(row.id);
        $('#name').val(row.name);
        $('#code').val(row.code);
        if (row.rtl) {
            $('input:checkbox[name=rtl]').attr('checked', true); // set CheckBox True
        } else {
            $('input:checkbox[name=rtl]').attr('checked', false); // set CheckBox False
        }
        $('#rtl').val(row.rtl);
    }
};
window.categoryEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#id').val(row.id);
        $('#name').val(row.name);
        $('#status').val(row.status);
    }
};
window.centerActionEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#type option[value="' + row.type + '"]').prop('selected', true);
        $('#id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_email').val(row.email);
        $('#edit_contact').val(row.contact);
        $('#edit_tagline').val(row.tagline);
        $('#edit_address').val(row.address);

        $('#user_id').val(row.admin.id);
        $('#edit_user_first_name').val(row.admin.full_name);
        // $('#edit_user_last_name').val(row.admin.last_name);
        $('#edit_user_email').val(row.admin.email);
        $('#edit_user_contact').val(row.admin.mobile);
        $('#edit_user_dob').val(moment(row.admin.dob).format('DD-MM-YYYY'));
        $('#edit_user_current_address').val(row.admin.current_address);
        $('#edit_user_permanent_address').val(row.admin.permanent_address);
        if (row.admin.gender == "male") {
            $('#edit_male').prop('checked', true);
        } else {
            $('#edit_female').prop('checked', true);
        }
    },
    'change .js-switch ': function (e, _value, row) {
        let data = new FormData();
        data.append('status', e.target.checked ? "1" : "0")
        data.append('_method', 'PATCH')
        let url = baseUrl + '/centers/status/' + row.id;
        ajaxRequest("POST", url, data)
    }
};
window.formFieldEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_type').val(row.type).trigger('change');
        $('#edit_is_required').siblings('.switchery').remove();
        $('#edit_is_required').prop('checked', (row.is_required === 1));
        new Switchery(document.querySelector('#edit_is_required'), {color: theme_color});
        $('.edit-remove-default-values').each(function () {
            $(this).trigger('click');
        })
        if (row.default_values) {
            for (let i = 2; i < row.default_values.length; i++) {
                $('.edit-add-more-default-values').trigger('click');
            }

            $('.edit_default_values').each(function (index, _value) {
                $(this).val(row.default_values[index]);
            })
        }
    }
};

window.importFormFieldEvents = {
    'click .import': function (_e, _value, row, _index) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to import this Form Field",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, import it!'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = baseUrl + '/import-form-fields/import/' + row.id;

                function successCallback() {
                    $('#table_list').bootstrapTable('refresh');
                }

                function errorCallback(response) {
                    showErrorToast(response.message);
                }

                ajaxRequest('POST', url, null, null, successCallback, errorCallback);
            }
        })
    }
};

window.ExamTermEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#id').val(row.id);
        $('#edit_name').val(row.name);
    }
};

window.ExamSequenceEvents = {
    'click .edit-data': function (_e, _value, row, _index) {
        $('#id').val(row.id);
        $('#edit_exam_term_id').text(row.term);
        $('#edit_name').val(row.name);
        if (row.start_date !== null && row.end_date !== null) {
            $('#edit-start-date').val(moment(row.start_date).format('DD-MM-YYYY'));
            $('#edit-end-date').val(moment(row.end_date).format('DD-MM-YYYY'));
            $('#edit-end-date').rules("add", {
                dateGreaterThan: $('#edit-start-date'),
            });
        } else {
            $('#edit-start-date').val('');
            $('#edit-end-date').val('');
        }
        (row.status == 1) ? $('#edit-active').prop('checked', true) : $('#edit-inactive').prop('checked', true);
        $('#class_section_id').val(row.auto_sequence_exam_class_section_id).trigger('change');
    }
};

window.examResultSubjectGroupEvents = {
    'click .edit-data': function (e, value, row) {
        e.preventDefault();
        $('#edit_id').val(row.id);
        $('#edit_name').val(row.name);
        $('#edit_rank').val(row.position);
    }
};

window.changeSubjectGroups = {
    'click .edit-data': function (e, value, row) {
        window.location.href = '/exam/result-subject-group/' + row.id +  '/edit';
    }
}

window.examReportEvent = {
    'click .print-report': function (e, value, row) {
        e.preventDefault();
        window.location.href = $(e.currentTarget).attr('href') + '/' + row.exam_report_id;
    }
};

window.classGroupEvent = {
    'click .edit-data': function (e, value, row) {
        e.preventDefault();
        $('#edit_name').val(row.name);
    }
};


window.sequenceWiseMarksEvents = {
    'click .edit-data': function (e, value, row) {
        e.preventDefault();
        $('#student_name').text(row.student_name);
        if (row.exam_marks.length > 0) {
            let html = '';
            row.exam_marks.forEach(function (value, key) {
                html += '<div class="row mt-3">' +
                    '<div class="form-group col-sm-12 col-md-6 local-forms">' +
                    '   <label for="">Subject</label>' +
                    '   <input type="text" value="' + value.subject.name + '" class="form-control" disabled>' +
                    '   </div>' +
                    '<div class="form-group col-sm-12 col-md-3 local-forms">' +
                    '   <label for="">Total Marks</label>' +
                    '   <input type="text" value="' + value.timetable.total_marks + '" class="form-control" disabled>' +
                    '</div>' +
                    '<div class="form-group col-sm-12 col-md-3 local-forms">' +
                    '   <label for="">Obtained Marks</label>' +
                    '   <input type="hidden" name="exam_marks[' + key + '][id]" value="' + value.id + '">' +
                    '   <input type="number" name="exam_marks[' + key + '][obtained_marks]" class="form-control" min=0 max="' + value.timetable.total_marks + '" value="' + value.obtained_marks + '">' +
                    '</div>' +
                    '</div>';
            })

            $('#subject-list').html(html);
        }

    }
};


// RTL Status Data-Formatter Function
function languageRtlStatusFormatter(_value, row) {
    let html;
    if (row.rtl) {
        html = "<span class='badge badge-success'>YES</span>";
    } else {
        html = "<span class='badge badge-danger'>NO</span>";
    }
    return html;
}


// Bootstrap Custom Column Formatters
function actionColumnFormatter(_value, row) {
    let buttons = this.buttons;

    let html = '<div class="actions">';

    if (buttons.editButton) {
        let customClass = buttons.editButton.customClass || 'bg-success-light';
        let redirection = buttons.editButton.redirection || false;
        let formSubmitURL = buttons.editButton.url + "/" + row.id;
        let editPageURL = buttons.editButton.url + "/" + row.id + "/edit";
        let url = redirection ? editPageURL : formSubmitURL;
        if (redirection) {
            html += '<a href="' + url + '" class="btn btn-sm bg-success-light' + customClass + '">'
        } else {
            html += '<a href="' + url + '" data-bs-toggle="modal" data-bs-target="#editModal" class="btn btn-sm bg-success-light edit-data ' + customClass + '">'
        }

        html += '<i class="feather-edit"></i>'
            + '</a>';
    }

    if (buttons.customModal) {
        let customClass = buttons.customModal.customClass || 'bg-success-light';

        html += '<a id="customModalId" href="' + buttons.customModal.url + "/" + row.id + '" data-bs-toggle="modal" data-bs-target="#customModal" class="btn btn-sm bg-success-light delete-data ' + customClass + '">'

        html += '<i class="feather-trash"></i>'
            + '</a>';
    }

    if (buttons.deleteButton) {
        let customClass = buttons.deleteButton.customClass || 'bg-success-light';
        let redirection = buttons.deleteButton.redirection || false;
        let formSubmitURL = buttons.deleteButton.url + "/" + row.id;
        let deletePageURL = buttons.deleteButton.url + "/" + row.id + "/delete";
        let url = redirection ? deletePageURL : formSubmitURL;
        if (redirection) {
            html += '<a href="' + url + '" class="btn btn-sm bg-danger-light me-2 ' + customClass + '">';
        } else {
            html += '<a href="' + url + '" class="btn btn-sm bg-danger-light me-2 delete-form ' + customClass + '">';
        }

        html += '<i class="feather-trash"></i>'
            + '</a>';
    }
    if (buttons.customButton) {
        buttons.customButton.forEach(function (value, _index) {
            let customClass = value.customClass || 'bg-success-light';
            let title = value.title || '';
            // let url = value.url || '#';
            // html += '<a href="' + url + '" class="btn btn-sm me-2 ' + customClass + '" title="' + title + '">'
            let target = '';
            if (value.new_tab == 1) {
                target = '_blank';
            }
            if (value.url != baseUrl + '/no-permission') {
                if (value.url == baseUrl + '/user/role/edit') {
                    if (row.is_default == 0) {
                        html += '<a href="' + value.url + '/' + row.id + '" target="' + target + '" class="btn btn-sm me-2 ' + customClass + '" title="' + title + '">'
                            + '<i class="' + value.iconClass + '"></i>'
                            + '</a>';
                    }
                } else if (value.url == baseUrl + '/role/delete') {
                    if (row.is_default == 0) {
                        html += '<a href="' + value.url + '/' + row.id + '" target="' + target + '" class="btn btn-sm me-2 ' + customClass + '" title="' + title + '">'
                            + '<i class="' + value.iconClass + '"></i>'
                            + '</a>';
                    }
                } else {
                    html += '<a href="' + value.url + '/' + row.id + '" target="' + target + '" class="btn btn-sm me-2 ' + customClass + '" title="' + title + '">'
                        + '<i class="' + value.iconClass + '"></i>'
                        + '</a>';
                }
            }
        })
    }

    if (row.active !== undefined) {
        let toggleIcon = 'fa-exchange';        
        let toggleLabel = row.checkActive ? 'Deactivate' : 'Activate';
        let toggleUrl = baseUrl + "/fees-discounts/toggle/" + row.id;
    
        html += `<a href="#" 
                class="btn btn-sm toggle-status" 
                data-url="${toggleUrl}" 
                title="${toggleLabel}">
                <i class="fa ${toggleIcon}"></i>
            </a>`;

    }
    

    html += '</div>';
    return html;
}

function notesFormatter(_value, row) {
    // console.log(row);
    let file_upload = "<br><h6>File Upload</h6>";
    let file_upload_counter = 1;
    let html = "";

    $.each(row.file, function (_key, data) {

        $.each(data.file, function (_key, value) {
            file_upload += "<a href='" + value.file_url + "' target='_blank' >" + file_upload_counter + ". " + value.file_name + "</a><br>";
            file_upload_counter++;
        })
    })


    if (file_upload_counter > 1) {
        html += file_upload;
    }
    return html;

}

function fileFormatter(_value, row) {
    let file_upload = "<br><h6>File Upload</h6>";
    let youtube_link = "<br><h6>YouTube Link</h6>";
    let video_upload = "<br><h6>Video Upload</h6>";
    let other_link = "<br><h6>Other Link</h6>";

    let file_upload_counter = 1;
    let youtube_link_counter = 1;
    let video_upload_counter = 1;
    let other_link_counter = 1;

    $.each(row.file, function (_key, data) {
        //1 = File Upload , 2 = YouTube , 3 = Uploaded Video , 4 = Other
        if (data.type == 1) {
            // 1 = File Ulopad
            file_upload += "<a href='" + data.file_url + "' target='_blank' >" + file_upload_counter + ". " + data.file_name + "</a><br>";
            file_upload_counter++;
        } else if (data.type == 2) {
            // 2 = YouTube Link
            youtube_link += "<a href='" + data.file_url + "' target='_blank' >" + youtube_link_counter + ". " + data.file_name + "</a><br>";
            youtube_link_counter++;
        } else if (data.type == 3) {
            // 3 = Uploaded Video
            video_upload += "<a href='" + data.file_url + "' target='_blank' >" + video_upload_counter + ". " + data.file_name + "</a><br>";
            video_upload_counter++;
        } else if (data.type == 4) {
            // 4 = Other Link
            other_link += "<a href='" + data.file_url + "' target='_blank' >" + other_link_counter + ". " + data.file_name + "</a><br>";
            other_link_counter++;
        }
    })
    let html = "";
    if (file_upload_counter > 1) {
        html += file_upload;
    }

    if (youtube_link_counter > 1) {
        html += youtube_link;
    }

    if (video_upload_counter > 1) {
        html += video_upload;
    }

    if (other_link_counter > 1) {
        html += other_link;
    }

    return html;
}

function resubmissionFormatter(_value, row) {
    let html;
    if (row.resubmission) {
        html = "<span class='alert alert-success'>YES</span>";
    } else {
        html = "<span class='alert alert-danger'>NO</span>";
    }
    return html;
}


function assignmentFileFormatter(_value, row) {
    return "<a target='_blank' href='" + row.file + "'>" + row.name + "</a>";
}

function assignmentSubmissionStatusFormatter(_value, row) {
    let html = "";
    // 0 = Pending/In Review , 1 = Accepted , 2 = Rejected , 3 = Resubmitted
    if (row.status === 0) {
        html = "<span class='badge badge-warning'>Pending</span>";
    } else if (row.status === 1) {
        html = "<span class='badge badge-success'>Accepted</span>";
    } else if (row.status === 2) {
        html = "<span class='badge badge-danger'>Rejected</span>";
    } else if (row.status === 3) {
        html = "<span class='badge badge-warning'>Resubmitted</span>";
    }
    return html;
}

function imageFormatter(value) {
    if (value) {
        // noinspection JSDeprecatedSymbols
        return "<a data-toggle='lightbox' href='" + value + "' class='image-popup'><img src='" + value + "' class='img-fluid w-25'  alt='image' onerror='onErrorImage(event)'  /></a>";
    } else {
        return '-'
    }
}

function examTimetableFormatter(_value, row) {
    let html = []
    if (row.timetable.length != null) {
        $.each(row.timetable, function (_key, timetable) {
            html.push('<p>' + timetable.subject.name + '(' + timetable.subject.type + ')  - ' + timetable.total_marks + '/' + timetable.passing_marks + ' - ' + timetable.start_time + ' - ' + timetable.end_time + ' - ' + timetable.date + '</p>')
        });
    }
    return html.join('')
}

function examSubjectFormatter(_index, row) {
    if (row.subject_name) {
        return row.subject_name;
    } else {
        return $('#subject_id :selected').text();
    }
}

function examStudentNameFormatter(_index, row) {
    return "<input type='hidden' name='exam_marks[" + row.no + "][student_id]' class='form-control' value='" + row.student_id + "' />" + row.student_name
}

function obtainedMarksFormatter(_index, row) {
    if (row.obtained_marks) {
        return "<input type='hidden' name='exam_marks[" + row.no + "][exam_marks_id]' class='form-control' value='" + row.exam_marks_id + "' />" +
            "<input type='text' name='exam_marks[" + row.no + "][obtained_marks]' class='form-control' value='" + row.obtained_marks + "' />" + "<input type='hidden' name='exam_marks[" + row.no + "][total_marks]' class='form-control' value='" + parseInt(row.total_marks) + "' />"
    } else {
        // return "<input type='text' name='exam_marks[" + row.no + "][obtained_marks]' class='form-control' value='" + ' ' + "' />" + "<input type='hidden' name='exam_marks[" + row.no + "][total_marks]' class='form-control' value='" + parseInt(row.total_marks) + "' />"
        const minNumber = 33;
        const maxNumber = 100;

        const randomValue = getRandomDigit(minNumber, maxNumber);
        return "<input type='text' name='exam_marks[" + row.no + "][obtained_marks]' class='form-control' value='0' />" + "<input type='hidden' name='exam_marks[" + row.no + "][total_marks]' class='form-control' value='" + parseInt(row.total_marks) + "' />"
    }
}


function getRandomDigit(min, max) {
    // Generate a random decimal number between 0 and 1
    const randomDecimal = Math.random();

    // Scale the decimal number to the range [min, max]
    const scaledNumber = randomDecimal * (max - min + 1);

    // Floor the number to remove the decimal part
    const randomDigit = Math.floor(scaledNumber);


    // Add the minimum value to the random digit
    return randomDigit + min;
}

function teacherReviewFormatter(index, row) {
    if (row.teacher_review) {
        return "<textarea name='exam_marks[" + row.no + "][teacher_review]' class='form-control'>" + row.teacher_review + "</textarea>"
    } else {
        return "<textarea name='exam_marks[" + row.no + "][teacher_review]' class='form-control'>" + ' ' + "</textarea>"
    }
}

function examPublishFormatter(index, _row) {
    if (index == 0) {
        return "<span class='badge badge-danger'>No</span>"
    } else {
        return "<span class='badge badge-success'>Yes</span>"
    }
}

function coreSubjectFormatter(_value, row) {
    let core_subject_count = 1;
    let html = "<div style='line-height: 20px;'>";
    $.each(row.core_subjects, function (_key, value) {
        if (value.subject) {
            html += "<br>" + core_subject_count + ". " + value.subject.name + " - " + value.subject.type
            core_subject_count++;
        }
    })
    html += "</div>";
    return html;
}

function electiveSubjectFormatter(_value, row) {
    let html = "<div style='line-height: 20px;'>";
    $.each(row.elective_subject_groups, function (key, group) {
        let elective_subject_count = 1;
        html += "<b>Group " + (key + 1) + "</b><br>";
        $.each(group.elective_subjects, function (_key, elective_subject) {
            html += elective_subject_count + ". " + elective_subject.subject.name + " - " + elective_subject.subject.type + "<br>"
            elective_subject_count++;
        })
        html += "<b>Total Subjects : </b>" + group.total_subjects + "<br>"
        html += "<b>Total Selectable Subjects : </b>" + group.total_selectable_subjects + "<br><br>"
    })
    html += "</div>";
    return html;
}

function defaultYearFormatter(index, _row) {
    if (index == 0) {
        return "<span class='badge badge-danger'>No</span>"
    } else {
        return "<span class='badge badge-success'>Yes</span>"
    }
}

function feesTypeChoiceable(_index, row) {
    if (row.choiceable) {
        return "<span class='badge badge-success'>Yes</span>"
    } else {
        return "<span class='badge badge-danger'>No</span>"
    }
}

function feesTypeFormatter(_index, row) {
    let html = [];
    if (row.fees_type.length) {
        let no = 1;
        $.each(row.fees_type, function (_key, value) {
            html.push("<span>" + no + ". " + value.fees_name + " - " + value.amount + "</span><br>")
            no++;
        });
    } else {
        html.push("<p class='text-center'>-</p>")
    }
    return html.join('')

}

function feesPaidModeFormatter(_index, row) {
    if (row.mode != null) {
        if (row.mode == 0) {
            return "<span class='badge badge-info'>" + lang_cash + "</span>"
        } else if (row.mode == 1) {
            return "<span class='badge badge-warning'>" + lang_cheque + "</span>"
        }else if (row.mode == 2) {
            return "<span class='badge badge-primary'>" + lang_mobile_money + "</span>"
        } else {
            return "<span class='badge badge-success'>" + lang_online + "</span>"
        }
    }
}

function feesOnlineTransactionLogParentGateway(_index, row) {
    if (row.payment_gateway == 1) {
        return "<span class='badge badge-info'>RazorPay</span>";
    } else if (row.payment_gateway == 2) {
        return "<span class='badge badge-secondary'>Stripe</span>";
    } else {
        return " ";
    }
}

function feesOnlineTransactionLogPaymentStatus(_index, row) {
    if (row.payment_status == 1) {
        return "<span class='badge badge-success'>" + lang_success + "</span>"
    } else if (row.payment_status == 2) {
        return "<span class='badge badge-warning'>" + lang_pending + "</span>"
    } else {
        return "<span class='badge badge-danger'>" + lang_failed + "</span>";
    }
}

function questionTypeFormatter(_index, row) {
    if (row.question_type) {
        return "<span class='badge badge-secondary'>" + lang_equation_based + "</span>"
    } else {
        return "<span class='badge badge-info'>" + lang_simple_question + "</span>"
    }
}

function optionsFormatter(_index, row) {
    let html = '';
    $.each(row.options, function (_index, value) {
        html += "<div class='row'>";
        html += "<div class= 'col-md-1 text-center'><i class='fa fa-arrow-right small' aria-hidden='true'></i></div><div class='col-md-6'>" + value.option + "</div><br>"
        html += "</div>";
    });
    return html;
}

function answersFormatter(_index, row) {
    let html = '';
    $.each(row.answers, function (_index, value) {
        html += "<div class='row'>";
        html += "<span class= 'col-md-1 text-center'><i class='fa fa-arrow-right small' aria-hidden='true'></i></span><div class='col-md-6'>" + value.answer + "</div><br>"
        html += "</div>";
    });
    return html;
}

function centerStatusFormatter(value, _data) {
    if (value) {
        return "<input type='checkbox' class='js-switch' checked/>";
    } else {
        return "<input type='checkbox' class='js-switch'/>";
    }
}

function badgeFormatter(value, _data) {
    if (value) {
        return "<span class='badge badge-success'>Yes</span>";
    } else {
        return "<span class='badge badge-danger'>No</span>";
    }
}

function marksUploadStatus(value, row) {
    let html = []
    if (row.timetable.length != null) {
        $.each(row.timetable, function (key, timetable) {
            let marks_upload_status_badge = '';
            if (timetable.marks_upload_status === 0) {
                marks_upload_status_badge = "<span class='badge badge-danger'>Pending</span>"
            } else if (timetable.marks_upload_status === 1) {
                marks_upload_status_badge = "<span class='badge badge-success'>Submitted</span>"
            } else if (timetable.marks_upload_status === 2) {
                marks_upload_status_badge = "<span class='badge badge-info'>In Progress</span>"
            }
            // html.push('<p>' + timetable.subject.name + ' - ' + marks_upload_status_badge + '</p>')
            html.push('<p>' + timetable.subject.name + ' ' + timetable.total_marks + '/' + timetable.passing_marks + ' - ' + timetable.start_time + ' - ' + timetable.end_time + ' - ' + timetable.date + ' - ' + marks_upload_status_badge + '</p>')
        });
    }
    return html.join('')
}

function subjectGroupFormatter(value, row) {
    let html = '';
    if (row.exam_result_subject_group.length != null) {
        html += "<ol class='list-group-numbered'>";
        $.each(row.exam_result_subject_group, function (subject_group_key, subject_group) {
            html += "<li ><b>" + subject_group.name + "</b>";
            if (subject_group.subjects.length != null) {
                html += "<ol class='ms-3'>";
                $.each(subject_group.subjects, function (subject_key, subject) {
                    if (subject != null) {
                        html += "<li>" + subject.name + "</li>";
                    }
                });
                html += "</ol></li>";
            }
        });
        html += "</ol>";
    }
    return html;
}

function QualifactionDegreeFormatter(_value, row) {
    return (_value != null) ? "<a href='" + _value + "' target='_blank'>View Certificate</a>" : "";
}

function statusFormatter(_value, row) {
    if (_value) {
        return "<span class='badge badge-success'>Active</span>";
    } else {
        return "<span class='badge badge-danger'>Inactive</span>";
    }
}

function urlFormatter(value) {
    return (value != null) ? "<a href='" + value + "' target='_blank'>" + value + "</a>" : "";
}

function sliderFormatter(value) {
    let html = '';
    if (value.length != null) {
        html += "<ol class='list-group-numbered'>";
        $.each(value, function (key, data) {
            html += "<li ><b>" + data.name + "</b>";
        });
        html += "</ol>";
    }
    return html;
}

function centerAdminFormatter(value) {
    return "Name : " + value.full_name + "<br />Email : " + value.email;
}

function classQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        medium_id: $('#filter_medium_id').val()
    };
}

function queryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function topStudentListqueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'exam_id': $('#top_students').val()
    };
}

function honorRollParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
    };
}


function classReportqueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        exam_term_id: $('#exam_term_id').val()
    };
}

function attendanceReportParam(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_section_id: $('#timetable_class_section').val(),
        greater_than: $('#greater_than').val(),
        less_than: $('#less_than').val(),
    };
}


// promotedStudentParam
function promotedStudentParam(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        session_year_id: $('#session_year_id').val(),
        class_section_id: $('#class_section_id').val()
    };
}

function fetchIncomeList(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        category: $('#category').val(),
        from_date: $('#from-date').val(),
    }
}

// classWiseReport
function classWiseReport(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        exam_id: $('#class_wise_report').val(),
        class_group_id: $('#filter_class_group').val()
    };
}

function courseReportParam(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        filter: $('#filter_table_data').val(),
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val()
    };
}

// eventParam
function eventParam(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
    };
}

// topStudentQueryParams
function topStudentQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        exam_id: $('#hidden_exam_id').val(),
        class_id: $('#hidden_class_id').val(),
        section_id: $('#hidden_section_id').val(),
        top_student: $('#top_student').val(),
        class_group_id: $('#hidden_top_student_class_group_id').val()
    };
}

function failStudentQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        exam_id: $('#hidden_exam_id').val(),
        class_id: $('#hidden_class_id').val(),
        section_id: $('#hidden_section_id').val(),
        top_student: $('#top_student').val(),
        class_group_id: $('#hidden_top_student_class_group_id').val()
    };
}


function ExamClassQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        exam_id: $('#filter_exam_name').val(),
        class_section_id: $('#filter_class_name').val()
    };
}

function gradesQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
    };
}

function HolidayParam(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        center_id: $('#center_id').val()
    };
}

function AnnouncementParam(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        center_id: $('#center_id').val()
    };
}


function getExamResult(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        exam_id: $('.result_exam').val(),
    };
}

$('#filter_medium_id').on('change', function () {
    $('#table_list').bootstrapTable('refresh');
})

function SubjectQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        medium_id: $('#filter_subject_id').val()
    };
}

$('#filter_subject_id').on('change', function () {
    $('#table_list').bootstrapTable('refresh');
})

function AssignclassQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        medium_id: $('#filter_medium_id').val(),
    };

}

function AssignTeacherQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_id: $('#filter_class_id').val(),
    };
}

$('#filter_class_id').on('change', function () {
    $('#table_list').bootstrapTable('refresh');
})

function AssignSubjectTeacherQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_id: $('#filter_class_section_id').val(),
        teacher_id: $('#filter_teacher_id').val(),
        subject_id: $('#filter_subject_id').val(),
    };
}

$('#filter_class_section_id').on('change', function () {
    $('#table_list').bootstrapTable('refresh');
})

$('#filter_teacher_id').on('change', function () {
    $('#table_list').bootstrapTable('refresh');
})

$('#filter_subject_id').on('change', function () {
    $('#table_list').bootstrapTable('refresh');
})


function StudentDetailQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_id: $('#filter_class_section_id').val(),
        student_status: $('#filter_by_student_status').val(),
    };
}

function SubjectGroupQueryParam(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
    };
}

function UserQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
    };
}


function AssignmentSubmissionQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        subject_id: $('#filter_subject_id').val(),
        class_section_id: $('#filter_class_section_id').val(),
        center_id: $('#filter_center_id').val(),

    };
}

function CreateAssignmentSubmissionQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        subject_id: $('#filter_subject_id').val(),
        class_id: $('#filter_class_section_id').val(),
        center_id: $('#filter_center_id').val(),

    };
}

function CreateLessionQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        subject_id: $('#filter_subject_id').val(),
        class_id: $('#filter_class_section_id').val(),
        center_id: $('#filter_center_id').val()
    };
}

function CreateTopicQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        subject_id: $('#filter_subject_id').val(),
        class_id: $('#filter_class_section_id').val(),
        lesson_id: $('#filter_lesson_id').val(),
        center_id: $('#filter_center_id').val()
    };
}

function uploadMarksqueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'class_section_id': $('#class_section_id').val(),
        'subject_id': $('#exam_subject_id').val(),
        'exam_id': $('#exam_id').val(),
    };
}


function sequenceExamMarksqueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'class_section_id': $('#class_section_id').val(),
        'sequence_id': $('#sequence_id').val(),
        'student_id': $('#student_id').val(),
        'subject_id': $('#subject_id').val(),
    };
}

function feesPaidListQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_id: $('#filter_class_id').val(),
        session_year_id: $('#filter_session_year_id').val(),
        mode: $('#filter_mode').val(),
    };
}

function feesPaymentTransactionQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_id: $('#filter_class_id').val(),
        session_year_id: $('#filter_session_year_id').val(),
        payment_status: $('#filter_payment_status').val(),
    };
}

function studentRollNumberQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'class_section_id': $('#filter_roll_number_class_section_id').val(),
        'sort_by': $('#sort_by').val(),
    };
}

function onlineExamQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'center_id': $('#filter_center_id_get_class').val(),
        'class_id': $('#filter_class_id').val(),
        'subject_id': $('#filter_subject_id').val(),
    };
}


$('#filter-question-class-id').on('change', function () {
    $('#table_list').bootstrapTable('refresh');
})
$('#filter-question-subject-id').on('change', function () {
    $('#table_list').bootstrapTable('refresh');
})

function onlineExamQuestionsQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'center_id': $('#filter_center_id_get_class').val(),
        'class_id': $('#filter_class_id').val(),
        'subject_id': $('#filter_subject_id').val(),
    };
}

function generalQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function assignClassqueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        from_class_section_id: $('#from_class_section_id').val(),
    };
}

function PromotequeryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'class_section_id': $('#student_class_section').val(),
        'session_year_id': $('#session_year_id').val(),
    };
}

function listClassesForCloning(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'class_section_id': $('#student_class_section').val(),
        'session_year_id': $('#session_year_id').val(),
    };
}


function examReportqueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'class_section_id': $('#class_section_id').val(),
        'term_id': $('#term_id').val(),
    };
}

function examsQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        class_section_id: $('#filter_class_section_id').val(),
        sequence_id: $('#filter_sequence_id').val(),
    };
}

function feesDiscountsListQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        applicable_status: $('#filter_applicable_status').val(),
    };
}

$('#class_id').on('change', function () {
    $('#assign_table_list').bootstrapTable('refresh');
});
$('#student_class_section').on('change', function () {
    $('#promote_student_table_list').bootstrapTable('refresh');
});


function superTeacherFormatter(_value, row) {
    let super_teachers_name_count = 1;
    let html = "<div style='line-height: 20px;'>";
    $.each(row.super_teachers_name, function (_key, superTeacher) {
        if (superTeacher.super_teacher_name) {
            html += "<br>" + super_teachers_name_count + "." + superTeacher.super_teacher_name;
            super_teachers_name_count++;
        }
    })
    html += "</div>";
    return html;
}

// function coreSubjectFormatter(_value, row) {
//     let super_teachers_name_count = 1;
//     let html = "<div style='line-height: 20px;'>";
//     $.each(row.super_teachers_name, function (_key, value) {
//         if (value.super_teacher_name) {
//             html += "<br>" + super_teachers_name_count + "." + value.super_teacher_name;
//             super_teachers_name_count++;
//         }
//         })
//     html += "</div>";
//     return html;
// }

$(document).ready(function () {
    $('.js-example-basic-multiple').select2({
        placeholder: "Please Select",
    });
});

// $(document).ready(function () {
//     $('input[name="filter_daterange"]').daterangepicker({
//         opens: 'right',
//         //   autoUpdateInput: false,
//     }, function (start, end) {
//         let startdate = start.format('MM-DD-YYYY');
//         let enddate = end.format('MM-DD-YYYY');
//         // $('input[name="filter_daterange"]').val(startdate + ' - ' + enddate);
//         console.log(startdate);
//         console.log(enddate);
//
//     });
// });

function expenseQueryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'filter_daterange': $('#filter_daterange').val(),

    };
}

$('#filter_daterange').on('change', function () {
    $('#table_list').bootstrapTable('refresh');
});

$(document).ready(function () {
    $('#teachers').on('change', function () {
        let teacherid = $(this).val();
        if (teacherid != null) {
            let url = baseUrl + '/getsalary/' + teacherid;

            function successCallback(response) {
                let salary = response.salary;
                $('#salary').val(salary);
            }

            function errorCallback(xhr) {
                showErrorToast(xhr.responseText);
            }

            ajaxRequest('GET', url, null, null, successCallback, errorCallback);
        } else {
            $('#salary').text('');
        }

    })
});
function shiftStatusFormatter(value, row, index) {
    if (row.status == 1) {
        return "<span class='badge badge-success'>Active</span>";
    } else {
        return "<span class='badge badge-danger'>Inactive</span>";
    }
}