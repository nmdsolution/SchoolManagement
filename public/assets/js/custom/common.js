/*
* Common Events Across the whole project
*/

//Setup CSRF Token default in AJAX Request
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('#create-form,.create-form').on('submit', function (e) {

    e.preventDefault();
    let customSuccessFunction = $(this).data('success-function');
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');

    let customPreSubmitFunction = $(this).data('pre-submit-function');
    if (customPreSubmitFunction) {
        //If custom function name is set in the Form tag then call that function using eval
        eval(customPreSubmitFunction + "()");
    }
    let data = new FormData(this);

    function successCallback(response) {
        formElement[0].reset();
        $('#table_list').bootstrapTable('refresh');

        if (customSuccessFunction) {
            //If custom function name is set in the Form tag then call that function using eval
            eval(customSuccessFunction + "(response)");
        }
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$('.profile').on('submit', function (e) {

    e.preventDefault();
    let customSuccessFunction = $(this).data('success-function');
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');

    let customPreSubmitFunction = $(this).data('pre-submit-function');
    if (customPreSubmitFunction) {
        //If custom function name is set in the Form tag then call that function using eval
        eval(customPreSubmitFunction + "()");
    }
    let data = new FormData(this);

    function successCallback(response) {
        formElement[0].reset();
        setTimeout(() => {
            window.location.reload();
        }, 1000);

        if (customSuccessFunction) {
            //If custom function name is set in the Form tag then call that function using eval
            eval(customSuccessFunction + "(response)");
        }
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

// reset-password
$('.reset-password').on('submit', function (e) {
    e.preventDefault();
    let customSuccessFunction = $(this).data('success-function');
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');

    let customPreSubmitFunction = $(this).data('pre-submit-function');
    if (customPreSubmitFunction) {
        //If custom function name is set in the Form tag then call that function using eval
        eval(customPreSubmitFunction + "()");
    }
    let data = new FormData(this);

    function successCallback(response) {
        formElement[0].reset();
        setTimeout(() => {
            location.replace(baseUrl);
        }, 3000);

        if (customSuccessFunction) {
            //If custom function name is set in the Form tag then call that function using eval
            eval(customSuccessFunction + "(response)");
        }
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$('.create-form-upload-bulk').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        formElement[0].reset();
        $('#table_list').bootstrapTable('refresh');
        setTimeout(function () {
            $('#standard-modal').modal('hide');
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})


$('#edit-form,.editform,.edit-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let customSuccessFunction = $(this).data('success-function');
    let data = new FormData(this);
    data.append("_method", "PUT");

    // append the id of the item to be edited
    let id = $(this).find('#id').val();

    if (id === undefined) {
        id = $(this).find('#edit_id').val();
    }

    let url = $(this).attr('action');
    url += `/`+ id;

    function successCallback(response) {
        $('#table_list').bootstrapTable('refresh');
        if (customSuccessFunction) {
            //If custom function name is set in the Form tag then call that function using eval
            eval(customSuccessFunction + "(response)");
        }
        setTimeout(function () {
            $('#editModal').modal('hide');
        }, 1000)

    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$(document).on('click', '.delete-form', function (e) {
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


$(document).on('click', '.set-form-url', function (e) {
    //This event will be called when user clicks on the edit button of the bootstrap table
    e.preventDefault();
    $('#edit-form,.edit-form,.editform').attr('action', $(this).attr('href'));
})