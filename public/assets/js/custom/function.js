"use strict";

function showErrorToast(message) {
    toastr.error(message)
}

function showSuccessToast(message) {
    toastr.success(message)
}

function showWarningToast(message) {
    toastr.warning(message)
}

function ajaxRequest(type, url, data, beforeSendCallback = null, successCallback = null, errorCallback = null, finalCallback = null, processData = false) {
    /*
    * @param
    * beforeSendCallback : This function will be executed before Ajax sends its request
    * successCallback : This function will be executed if no Error will occur
    * errorCallback : This function will be executed if some error will occur
    * finalCallback : This function will be executed after all the functions are executed
    */
    $.ajax({
        type: type,
        url: url,
        data: data,
        cache: false,
        processData: processData,
        contentType: false,
        dataType: 'json',
        beforeSend: function () {
            if (beforeSendCallback != null) {
                beforeSendCallback(data);
            }
        },
        success: function (data) {
            if (!data.error) {

                if (successCallback != null) {
                    successCallback(data);
                }
            } else {
                if (errorCallback != null) {
                    errorCallback(data);
                }
            }
            if (finalCallback != null) {
                finalCallback(data);
            }
        }, error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseJSON) {

                showErrorToast(jqXHR.responseJSON.message);
            }

            if (finalCallback != null) {
                finalCallback();
            }
        }
    })
}

function formAjaxRequest(type, url, data, formElement, submitButtonElement, successCallback, errorCallback) {
    // To Remove Red Border from the Validation tag.
    formElement.find('.has-danger').removeClass("has-danger");

    formElement.validate();
    if (formElement.valid()) {
        let submitButtonText = submitButtonElement.val();

        function mainBeforeSendCallback() {
            submitButtonElement.val('Please Wait...').attr('disabled', true);
        }

        function mainSuccessCallback(response) {
            if (response.warning) {
                showWarningToast(response.message);
            } else {
                showSuccessToast(response.message);
            }

            if (successCallback != null) {
                successCallback(response);
            }
        }

        function mainErrorCallback(response) {
            showErrorToast(response.message);
            if (errorCallback != null) {
                errorCallback(response);
            }
        }

        function finalCallback(response) {
            submitButtonElement.val(submitButtonText).attr('disabled', false);
        }

        ajaxRequest(type, url, data, mainBeforeSendCallback, mainSuccessCallback, mainErrorCallback, finalCallback)
    }
}

function cloneNewCoreSubjectTemplate() {
    let core_subject = null;
    if ($('.core-subject-div').length === 1 && $('.core-subject-div').css('display') === "none") {
        core_subject = $('.core-subject-div').show();
        $('.form-control').each(function (e) {
            $(this).attr('disabled', false);
            $(this).val('');
        })
    } else {
        core_subject = $('.core-subject-div:last').clone();
        //Add incremental name value
        core_subject.find('.form-control').each(function (key, element) {
            this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
                return '[' + (parseInt(p1, 10) + 1) + ']';
            });
            this.value = '';
            $(this).closest('.has-danger').removeClass('has-danger');
            $(this).siblings('.error').remove();
        })

        core_subject.find('.remove-core-subject').removeAttr('data-id');
    }

    return core_subject;
}

function cloneNewElectiveSubjectGroup() {
    let html = null;
    if ($('.elective-subject-group').length === 1 && $('.elective-subject-group').css('display') === "none") {
        html = $('.elective-subject-group').show();
        $('.form-control').each(function (e) {
            $(this).attr('disabled', false);
            $(this).val('');
        })
    } else {
        html = $('.elective-subject-group:last').clone().show();
        html.find('.elective-subject:gt(1)').remove();
        html.find('.form-control').each(function (key, element) {
            this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
                return '[' + (parseInt(p1, 10) + 1) + ']';
            });
            $(element).attr('disabled', false);
            this.value = '';
            $(this).closest('.has-danger').removeClass('has-danger');
            $(this).siblings('.error').remove();
        })
        html.find('.remove-elective-subject').removeAttr('data-id');
        html.find('.remove-elective-subject-group').removeAttr('data-id');

    }
    return html;
}

function cloneNewElectiveSubject() {
    //add-new-elective-subject class button element
    let subject_list = $('.elective-subject:last').clone();
    subject_list.find('select').each(function (key, element) {
        this.name = this.name.replace(/\[subject_id]\[(\d+)\]/, function (str, p1) {
            return '[subject_id][' + (parseInt(p1, 10) + 1) + ']';
        });
        this.value = '';
        $(this).closest('.has-danger').removeClass('has-danger');
        $(this).siblings('.error').remove();
    })
    subject_list.find('input').each(function (key, element) {
        this.name = this.name.replace(/\[class_subject_id]\[(\d+)\]/, function (str, p1) {
            return '[class_subject_id][' + (parseInt(p1, 10) + 1) + ']';
        });
        this.value = '';
        $(this).closest('.has-danger').removeClass('has-danger');
        $(this).siblings('.error').remove();
    })
    subject_list.find('.weightage').each(function (key, element) {
        this.name = this.name.replace(/\[weightage]\[(\d+)\]/, function (str, p1) {
            return '[weightage][' + (parseInt(p1, 10) + 1) + ']';
        });
        this.value = '';
        $(this).closest('.has-danger').removeClass('has-danger');
        $(this).siblings('.error').remove();
    })
    //Show the second last or element on screen
    $('.or:last').parent().show();

    //Removed Class subject id because its new elective subject
    subject_list.find('.edit-elective-subject-class-id').remove();
    subject_list.find('.remove-elective-subject').removeAttr('data-id');
    return subject_list;
}

/**
 *
 * @param searchElement
 * @param searchUrl
 * @param data Object
 * @param placeHolder
 * @param templateDesignEvent
 * @param onTemplateSelectEvent
 */
function select2Search(searchElement, searchUrl, data, placeHolder, templateDesignEvent, onTemplateSelectEvent) {
    //Select2 Ajax Searching Functionality function
    if (!data) {
        data = {};
    }
    $(searchElement).select2({
        tags: true,
        dropdownParent: $(searchElement).parent(),
        ajax: {
            url: searchUrl,
            dataType: 'json',
            delay: 250,
            cache: true,
            data: function (params) {
                // data.email = params.term;
                data.search = params.term;
                data.page = params.page;
                return data;
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            }
        },
        placeholder: placeHolder,
        minimumInputLength: 1,
        templateResult: templateDesignEvent,
        templateSelection: onTemplateSelectEvent
    });
}

function parentSearchSelect2DesignTemplate(repo) {
    /**
     * This function is used in Select2 Searching Functionality
     */
    if (repo.loading) {
        return repo.text;
    }
    if (repo.id && repo.text) {
        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__title'></div>" +
            "</div>"
        );
        $container.find(".select2-result-repository__title").text(repo.text);
    } else {
        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='row'>" +
            "<div class='col-1 select2-result-repository__avatar' style='width:20px'>" +
            "<img src='" + repo.image + "' class='w-100' onerror='onErrorImage(event)'/>" +
            "</div>" +
            "<div class='col-10'>" +
            "<div class='select2-result-repository__title'></div>" +
            "<div class='select2-result-repository__description'></div>" +
            "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.first_name + " " + repo.last_name);
        $container.find(".select2-result-repository__description").text(repo.mobile);
    }

    return $container;
}

function userSearchSelect2DesignTemplate(repo) {
    /**
     * This function is used in Select2 Searching Functionality
     */
    if (repo.loading) {
        return repo.text;
    }
    if (repo.id && repo.text) {
        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__title'></div>" +
            "</div>"
        );
        $container.find(".select2-result-repository__title").text(repo.text);
    } else {
        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='row'>" +
            "<div class='col-1 select2-result-repository__avatar' style='width:20px'>" +
            "<img src='" + repo.image + "' class='w-100' onerror='onErrorImage(event)'/>" +
            "</div>" +
            "<div class='col-10'>" +
            "<div class='select2-result-repository__title'></div>" +
            "<div class='select2-result-repository__description'></div>" +
            "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.first_name + " " + repo.last_name);
        $container.find(".select2-result-repository__description").text(repo.mobile + " " + repo.email);
    }

    return $container;
}

function teacherSearchSelect2DesignTemplate(repo) {
    /**
     * This function is used in Select2 Searching Functionality
     */
    if (repo.loading) {
        return repo.text;
    }
    if (repo.id && repo.text) {
        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__title'></div>" +
            "</div>"
        );
        $container.find(".select2-result-repository__title").text(repo.text);
    } else {
        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='row'>" +
            "<div class='col-1 select2-result-repository__avatar'>" +
            "<img src='" + repo.user.image + "' class='w-100' onerror='onErrorImage(event)'/>" +
            "</div>" +
            "<div class='col-10'>" +
            "<div class='select2-result-repository__title'></div>" +
            "<div class='select2-result-repository__description'></div>" +
            "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.user.first_name + " " + repo.user.last_name);
        $container.find(".select2-result-repository__description").text(repo.user.mobile);
    }

    return $container;
}

function createCkeditor() {
    // for (var equation_editor in CKEDITOR.instances) {
    //     CKEDITOR.instances[equation_editor].destroy();
    // }
    // CKEDITOR.replaceAll(function (textarea, config) {
    //     if (textarea.className == "editor_question") {
    //         config.mathJaxLib = '//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML';
    //         config.extraPlugins = 'mathjax';
    //         config.height = 200;
    //         return true;
    //     }
    //     // if (textarea.className == "editor_options") {
    //     //     config.mathJaxLib = '//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML';
    //     //     config.extraPlugins = 'mathjax';
    //     //     config.height = 100
    //     //     return true;
    //     // }
    //     return false;
    // });
    //
    // // inline editors
    // var elements = CKEDITOR.document.find('.equation-editor-inline'), i = 0, element;
    // while ((element = elements.getItem(i++))) {
    //     CKEDITOR.inline(element, {
    //         mathJaxLib: '//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.4/MathJax.js?config=TeX-AMS_HTML',
    //         extraPlugins: 'mathjax',
    //         readOnly: true,
    //     });
    // }
}

function hideLastOR() {
    $('.elective-subject-group').each(function () {
        $(this).find('.or').eq(-2).show();
        $(this).find('.or:last').hide();
        $(this).find('.add-new-elective-subject:last').show();
        $(this).find('.add-new-elective-subject').eq(-2).hide();
    })
}

function changeRemoveElectiveButtonState() {
    $('.elective-subject-group').each(function () {
        if ($(this).find('.elective-subject').length > 2) {
            $(this).find('.remove-elective-subject').attr('disabled', false);
        } else {
            $(this).find('.remove-elective-subject').attr('disabled', true);
        }
    })
}

function changeGroupNumber() {
    $('.elective-subject-group').each(function (index, element) {
        $(this).find('#group-number').text(index + 1);
    });
}

function orverall(pass, fail) {
    var chart = c3.generate({
        bindto: '#chart-donut-overall', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', pass],
                ['data2', fail],
            ],
            type: 'donut', // default type of chart
            colors: {
                data1: '#00E4AE',
                data2: '#FE606F',
            },
            names: {
                // name of each serie
                'data1': 'PASS : ',
                'data2': 'FAIL : ',
            }
        },
        axis: {},
        legend: {
            show: true, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
}

function barchart(class_name, class_wise_pass, class_wise_fail) {
    // if ($('#bar-chart').length > 0) {
    var optionsBar = {
        chart: {
            type: 'bar',
            height: 350,
            width: '100%',
            stacked: false,
            toolbar: {
                show: false
            },
        },
        dataLabels: {
            enabled: true
        },
        plotOptions: {
            bar: {
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        series: [{
            name: "PASS",
            color: '#00E4AE',
            data: class_wise_pass,
        }, {
            name: "FAIL",
            color: '#FE606F',
            data: class_wise_fail,
        }],
        labels: class_name,
        // labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
        xaxis: {
            labels: {
                show: true
            },
            axisBorder: {
                show: true
            },
            axisTicks: {
                show: true
            },
        },
        yaxis: {
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            labels: {
                style: {
                    colors: '#777'
                }
            }
        },
        title: {
            text: '',
            align: 'left',
            style: {
                fontSize: '18px'
            }
        }

    }
    var chartBar = new ApexCharts(document.querySelector('#bar-chart'), optionsBar);
    chartBar.render();
    chartBar.update();
    // }
}

function class_wise(pass, fail) {
    var chart = c3.generate({
        bindto: '#chart-donut-class-section', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', pass],
                ['data2', fail],
            ],
            type: 'donut', // default type of chart
            colors: {
                data1: '#00E4AE',
                data2: '#FE606F',
            },
            names: {
                // name of each serie
                'data1': 'PASS : ',
                'data2': 'FAIL : ',
            }
        },
        axis: {},
        legend: {
            show: true, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
}

function gender_wise(total_male, total_female) {
    var chart = c3.generate({
        bindto: '#chart-donut-gender-ratio', // id of chart wrapper
        data: {
            columns: [
                // each columns data
                ['data1', total_male],
                ['data2', total_female],
            ],
            type: 'donut', // default type of chart
            colors: {
                data1: '#44C4FA',
                data2: '#664DC9',
            },
            names: {
                // name of each serie
                'data1': 'BOYS : ',
                'data2': 'GIRLS : ',
            }
        },
        axis: {},
        legend: {
            show: true, //hide legend
        },
        padding: {
            bottom: 0,
            top: 0
        },
    });
}

function boys_girls() {
    // chart-bar-rotated-boys-girls
    var options = {
        series: [{
            name: 'Boys',
            data: [80]
        }, {
            name: 'Girls',
            data: [20]
        }],
        chart: {
            type: 'bar',
            height: 140,
            stacked: true,
            toolbar: false
        },
        plotOptions: {
            bar: {
                horizontal: true,
                dataLabels: {
                    total: {
                        enabled: true,
                        offsetX: 0,
                        style: {
                            fontSize: '13px',
                            fontWeight: 900
                        }
                    }
                }
            },
        },
        stroke: {
            width: 1,
            colors: ['#fff']
        },
        title: {
            text: "PERFORMANCE RATIO BETWEEB BOYS & GIRLS",
            align: 'center'
        },
        xaxis: {
            categories: ['Boys / Girls'],
            labels: {
                formatter: function (val) {
                    return val
                }
            }
        },
        yaxis: {
            title: {
                text: undefined
            },
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val
                }
            }
        },
        fill: {
            opacity: 1
        },
        legend: {
            position: 'bottom',
            horizontalAlign: 'center',
            offsetX: 40,
            show: true,
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart-bar-rotated-boys-girls"), options);
    chart.render();
}

function bar_chart_boys_girls(class_name, male_students, female_students) {
    var optionsBar = {
        chart: {
            type: 'bar',
            height: 350,
            width: '100%',
            stacked: false,
            toolbar: {
                show: true
            },
        },
        dataLabels: {
            enabled: false
        },
        plotOptions: {
            bar: {
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        series: [{
            name: "BOYS",
            color: '#70C4CF',
            data: male_students,
        }, {
            name: "GIRLS",
            color: '#3D5EE1',
            data: female_students,
        }],
        labels: class_name,
        xaxis: {
            labels: {
                show: true
            },
            axisBorder: {
                show: true
            },
            axisTicks: {
                show: true
            },
        },
        yaxis: {
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            labels: {
                style: {
                    colors: '#777'
                }
            }
        },
        title: {
            text: '',
            align: 'left',
            style: {
                fontSize: '18px'
            }
        }

    }
    var chartBar = new ApexCharts(document.querySelector('#bar-chart-boys-girls'), optionsBar);
    chartBar.render();
}

function apexcharts_overview(attendance_class_name, boys_data, girls_data) {
    var options = {
        chart: {
            height: 350,
            type: "line",
            toolbar: {
                show: true
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: "smooth"
        },
        series: [{
            name: "BOYS",
            color: '#70C4CF',
            // data: [45, 60, 75, 51, 42, 42, 30]
            data: boys_data
        }, {
            name: "GIRLS",
            color: '#3D5EE1',
            // data: [24, 48, 56, 32, 34, 52, 25]
            data: girls_data
        }],
        xaxis: {
            // categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            categories: attendance_class_name,

        }
    }
    var chart = new ApexCharts(
        document.querySelector("#apexcharts-overview"),
        options
    );
    chart.render();
}

function class_group_wise_boys_girls(class_name, boys, girls) {
    var optionsBar = {
        chart: {
            type: 'bar',
            height: 350,
            width: '100%',
            stacked: false,
            toolbar: {
                show: true
            },
        },
        dataLabels: {
            enabled: false
        },
        plotOptions: {
            bar: {
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        series: [{
            name: "BOYS",
            color: '#70C4CF',
            data: boys,
        }, {
            name: "GIRLS",
            color: '#3D5EE1',
            data: girls,
        }],
        labels: class_name,
        xaxis: {
            labels: {
                show: true
            },
            axisBorder: {
                show: true
            },
            axisTicks: {
                show: true
            },
        },
        yaxis: {
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            labels: {
                style: {
                    colors: '#777'
                }
            }
        },
        title: {
            text: '',
            align: 'left',
            style: {
                fontSize: '18px'
            }
        }

    }
    var chartBar = new ApexCharts(document.querySelector('#bar-chart-boys-girls-class-group'), optionsBar);
    chartBar.render();
    chartBar.update();
}

function createEffectiveDomainHTML() {
    return '<div class="row">' +
        '<div class="form-group col-md-4 col-sm-12 local-forms">' +
        '<input type="text" name="effective_domain[]" value="" required placeholder="Effective Domain" class="form-control"/>' +
        '</div>' +
        ' <div class="form-group col-md-4 col-sm-12 local-forms d-flex align-middle">' +
        '<button type="button" class="btn btn-danger remove-affective-domain"><span class="fa fa-times"></span></button>' +
        '</div>' +
        '</div>';
}