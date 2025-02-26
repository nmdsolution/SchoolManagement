"use strict";
let debug = false;

function errorPlacement(label, element) {
    label.addClass('mt-2 text-danger');
    if (element.is(":radio") || element.is(":checkbox")) {
        label.insertAfter(element.parent().parent().parent());
    } else if (element.is(":file")) {
        label.insertAfter(element.siblings('div'));
    } else if (element.hasClass('color-picker')) {
        label.insertAfter(element.parent());
    } else {
        label.insertAfter(element);
    }
}

function highlight(element, errorClass) {
    if ($(element).hasClass('color-picker')) {
        $(element).parent().parent().addClass('has-danger')
    } else {
        $(element).parent().addClass('has-danger')
    }

    $(element).addClass('form-control-danger')
}

$(".medium-create-form").validate({
    debug: debug,
    rules: {
        'name': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".medium-edit-form").validate({
    debug: debug,
    rules: {
        'username': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".section-create-form").validate({
    debug: debug,
    rules: {
        'username': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".section-edit-form").validate({
    debug: debug,
    rules: {
        'username': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".user-create-form").validate({
    debug: debug,
    rules: {
        'first_name': "required",
        'last_name': "required",
        'user_mobile': "required",
        'email': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

// $(".create-form").validate({
//     rules: {
//         'first_name': "required",
//         'email': "required",
//         'password': "required",
//         'confirm-password': "required",
//         "contact": "required"
//     },
//     errorPlacement: function (label, element) {
//         errorPlacement(label, element);
//     },
//     highlight: function (element, errorClass) {
//         highlight(element, errorClass);
//     }
// });

$(".center-validate-form").validate({
    debug: debug,
    rules: {
        contact: {
            required: true,
            number: true,
            minlength: 10
        },
        user_contact: {
            required: true,
            number: true,
            minlength: 10
        },
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".create-form-upload-bulk").validate({
    debug: debug,
    rules: {
        'file': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});


$(".class-create-form").validate({
    debug: debug,
    rules: {
        'name': "required",
        'medium_id': "required",
        'section_id[]': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".class-edit-form").validate({
    debug: debug,
    rules: {
        'name': "required",
        'medium_id': "required",
        'section_id[]': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".subject-create-form").validate({
    debug: debug,
    rules: {
        'medium_id': "required",
        'name': "required",
        'bg_color': "required",
        'type': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});
$(".assign-class-subject-form").validate({
    debug: debug,
    rules: {
        'class_id': "required",
        'core_subject_id[0]': "required",
        // 'elective_subject_id[0][0]': "required",
        'total_selectable_subjects[]': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

// $("#formdata").validate({
//     debug: debug,
//     errorPlacement: function (label, element) {
//         errorPlacement(label, element);
//     },
//     highlight: function (element, errorClass) {
//         highlight(element, errorClass);
//     },
// });

$("#formdata-upload-bulk").validate({
    debug: debug,
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    },
});


$(".sequence_mark_update").validate({
    debug: debug,
    rules: {
        'obtained_marks': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".edit-class-teacher-form").validate({
    debug: debug,
    rules: {
        'class_section_id': "required",
        'teacher_id': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".student-registration-form").validate({
    debug: debug,
    rules: {
        'first_name': "required",
        'mobile': "number",
        'dob': "required",
        'class_section_id': "required",
        'admission_no': "required",
        'roll_number': "required",
        'admission_date': "required",
        'blood_group': "required",
        'height': "required",
        'weight': "required",
        'current_address': "required",
        'permanent_address': "required",
        'nationality': "required",
        'father_email': {
            "email": true,
        },
        'father_mobile': {
            "number": true,
            "minlength": 10,
        },
        'mother_email': {
            "email": true,
        },
        'mother_mobile': {
            "number": true,
            "minlength": 10,
        },
        'guardian_email': {
            "email": true,
        },
        'guardian_first_name': "required",
        'guardian_mobile': {
            "number": true,
        },

    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".edit-student-registration-form").validate({
    debug: debug,
    rules: {
        'first_name': "required",
        'dob': "required",
        'class_section_id': "required",
        'admission_no': "required",
        'roll_number': "required",
        'admission_date': "required",
        'blood_group': "required",
        'height': "required",
        'weight': "required",
        'address': "required",
        'nationality': 'required',
        'father_mobile': {
            "number": true,
        },
        'mother_mobile': {
            "number": true,
        },

        'guardian_mobile': {
            "number": true,
        },
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".add-lesson-form").validate({
    debug: debug,
    rules: {
        'class_section_id': "required",
        'subject_id': "required",
        'name': "required",
        'description': "required",
        'file[0][name]': "required",
        'file[0][thumbnail]': "required",
        'file[0][file]': "required",
        'file[0][link]': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

//Added this Event here because this form has dynamic input fields.
// $('.add-lesson-form').on('submit', function () {
//     var file = $('[name^="file"]');
//     file.filter('input').each(function (key, data) {
//         $(this).rules("add", {
//             required: true,
//         });
//     });
//     file.filter('input[name$="[name]"]').each(function (key, data) {
//         $(this).rules("add", {
//             required: true,
//         });
//     });
// })

$(".edit-lesson-form").validate({
    debug: debug,
    rules: {
        'class_section_id': "required",
        'subject_id': "required",
        'name': "required",
        'description': "required",
        'edit_file[0][name]': "required",
        'edit_file[0][link]': "required",
        'file[0][name]': "required",
        'file[0][thumbnail]': "required",
        'file[0][file]': "required",
        'file[0][link]': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".add-topic-form").validate({
    debug: debug,
    rules: {
        'class_section_id': "required",
        'subject_id': "required",
        'lesson_id': "required",
        'name': "required",
        'description': "required",
        'file[0][name]': "required",
        'file[0][thumbnail]': "required",
        'file[0][file]': "required",
        'file[0][link]': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".edit-topic-form").validate({
    debug: debug,
    rules: {
        'class_section_id': "required",
        'subect_id': "required",
        'name': "required",
        'description': "required",
        'edit_file[0][name]': "required",
        'edit_file[0][link]': "required",
        'file[0][name]': "required",
        'file[0][thumbnail]': "required",
        'file[0][file]': "required",
        'file[0][link]': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".add-exam-form").validate({
    debug: debug,
    rules: {
        'class_id': "required",
        'name': "required",
        'timetable[0][subject_id]': "required",
        'timetable[0][total_marks]': "required",
        'timetable[0][passing_marks]': "required",
        'timetable[0][start_time]': "required",
        'timetable[0][end_time]': "required",
        'timetable[0][date]': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".add-assignment-form").validate({
    debug: debug,
    rules: {
        'class_section_id': "required",
        'subject_id': "required",
        'name': "required",
        'due_date': "required",
        'extra_days_for_resubmission': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".edit-assignment-form").validate({
    debug: debug,
    rules: {
        'class_section_id': "required",
        'subject_id': "required",
        'name': "required",
        'due_date': "required",
        'extra_days_for_resubmission': "required",
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$('.super-teacher-validate-form').validate({
    debug: debug,
    rules: {
        mobile: {
            required: true,
            number: true,
            minlength: 10
        }
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})

$('.teacher-validate-form').validate({
    debug: debug,
    rules: {
        "teacher-search": {
            required: true,
        },
        mobile: {
            // required: true,
            number: true,
            // minlength: 10
        }
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})

$('.update-teacher-validate-form').validate({
    debug: debug,
    rules: {
        "teacher-search": {
            required: true,
        },
        mobile: {
            // required: true,
            number: true,
            minlength: 10
        }
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})

$('.teacher-create-exam-validate').validate({
    debug: debug,
    rules: {
        timetable_passing_marks: {
            required: true,
            max: function () {
                return parseInt($('.total_marks').val())
            },
        },
        timetable_end_time: {
            required: true,
            min: function () {
                return $('.start_time').val()
            },
        }

    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})

$('.create-event-form-validate').validate({
    debug: debug,
    rules: {
        end_date: {
            required: true,
            min: function () {
                return $('#start_date_check').val();
            },
        },
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})

$('.update-event-form-validate').validate({
    debug: debug,
    rules: {
        end_date: {
            required: true,
            min: function () {
                return $('#start_date').val();
            },
        },
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})

$('.edit-form-timetable').validate({
    debug: debug,
    rules: {},
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})

$('.create_exam_timetable_form').validate({
    debug: debug,
    rules: {
        exam_id: "required"
    },
    errorPlacement: function (label, element) {
        console.log("create timetable");
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})


$('.create-sequence-form-validate').validate({
    debug: debug,
    rules: {
        end_date: {
            dateGreaterThan: $('.start_date')
        }
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})

$('.edit-sequence-form-validate').validate({
    debug: debug,
    rules: {
        end_date: {
            dateGreaterThan: $('#edit-start-date')
        }
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})

$('#create-grades').validate({
    debug: debug,
    rules: {
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})


//End Time Custom Validation
$.validator.addMethod("timeGreaterThan", function (value, element, params) {
    let startTime = $(params).val();
    let endTime = $(element).val();
    return endTime > startTime;

}, "End time should be greater than Start time.");

// let newData = endDate.replace(/(\d+[/])(\d+[/])/, '$2$1');
// console.log(newData);
// endDate = endDate.split("-");
// console.log(endDate);
// console.log(new Date(endDate[2], endDate[1] - 1, endDate[0]));


$.validator.addMethod("dateGreaterThan", function (value, element, params) {
    let startDate = moment($(params).val(), 'DD-MM-YYYY');
    let endDate = moment($(element).val(), 'DD-MM-YYYY');
    // return endDate > startDate;
    return endDate.diff(startDate) > 0;
}, "End Date should be greater than Start Date.");

// Add this in the Last line only because it's a common validator function
$('.create-form').validate({
    debug: debug,
    rules: {},
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})
$('.edit-form').validate({
    debug: debug,
    rules: {},
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
})