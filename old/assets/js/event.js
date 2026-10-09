$('#forgotSpan').click(function() {
    $('#loginSpan').show();
    $('#loginBody').hide(600);
    $('#forgotBody').show(700);
    $('#forgotSpan').hide();
});
$('#loginSpan').click(function() {
    $('#loginSpan').hide();
    $('#loginBody').show(700);
    $('#forgotBody').hide(600);
    $('#forgotSpan').show();
});


var baseUrl = 'http://brainwarecomputer.co.in/api/';
var imgUrl = 'http://brainwarecomputer.co.in/uploads/';
var token = localStorage.getItem('token');
var user_id = localStorage.getItem('user_id');
var user_type = localStorage.getItem('user_type');
//localStorage.clear();

/*----------------------------------------functions-----------*/

function login(ele) {
    localStorage.clear();
    var table = $(ele).data('name');
    var action = $(ele).data('ctrl');
    var formName = action + 'Form';
    var btn = $(ele).val();
    var formData = new FormData($('#' + formName)[0]);
    var allData = $("#" + formName).serialize() + "&table=" + table + "&action=" + action + "&token=" + token;

    if ($('#' + formName).valid()) {
        $(ele).prop('disabled', true);
        $.ajax({
            beforeSend: function() {
                $(ele).html("<i class='fa fa-refresh fa-spin'></i> Wait ...");
            },
            'type': 'POST',
            'url': baseUrl,
            'data': allData,
            success: function(response) {
                //alert(response);
                console.log(response);
                var all = JSON.parse(response);
                var message = all.message;
                var status = all.status;
                notify(status, message);
                if (status == 'success') {
                    $('#' + formName)[0].reset();
                    localStorage.setItem('token', all.token);
                    localStorage.setItem('user_id', all.user_id);
                    localStorage.setItem('user_type', all.user_type);
                    setTimeout(function() {
                        //window.location.href ='dashboard';
                    window.location.replace('dashboard');  
                    }, 1000);

                }

            },
            error: function(e) {
                console.log("ERROR : ", e);
                notify(e.statusText, e.responseText);
            },
            complete: function(data) {
                $(ele).prop('disabled', false);
                $(ele).html(btn);

            }

        });
    }
}
//localStorage.clear();


/*----------------------------------Activities funcitons-------------*/
function loadTable(fromTable) {
    allData = "action=load_table&table=" + fromTable + "&token=" + token;
    $.ajax({
        'type': 'POST',
        'url': baseUrl,
        'data': allData,
         beforeSend: function() {
            $('#preload-body').show();
        },
        success: function(response) {

            //console.log(response);
            var all = JSON.parse(response);

            $('#default-datatable').DataTable({
                destroy: true,
               data: all.data,
                aLengthMenu: [
                    [10, 25, 50, 100, 200, -1],
                    [10, 25, 50, 100, 200, "All"]
                ],
                iDisplayLength: 10
            });
        },
        error: function(e) {
             $('#preload-body').hide();
            alert(JSON.stringify(e.responseText));
        },
        complete: function(data) {
             $('#preload-body').hide();

        }

    });
}

// ----------------------General Functions----------------------------------------
function checkDuplicate(ele) {
    var field_name = $(ele).data('column');
    if(!field_name){
        field_name = $(ele).attr('id'); 
        var value = $(ele).val();
    }else {
         value = $('#'+field_name).val();
    }
    var table = $(ele).data('name');
    var action = 'check_duplicate';
    var errorId = $(ele).data('error');
    var dataId = $('#id').val();
    if (!dataId) {
        dataId = 0;
    }
    $.post(baseUrl, {
            token,
            action,
            field_name,
            value,
            table,
            dataId,

        },
        function(data) {
            console.log(data);
            var all = JSON.parse(data);
            var status = all.status;
            var message = all.message;
            if (status == 'error') {
                $(errorId).show();
                $(errorId).html(message);
                $('.btn').prop('disabled', true);
            } else {
                $('.btn').prop('disabled', false);
                $(errorId).hide();
                $(errorId).html('');
            }
        });
}

function toggleCheck() {
    if ($('#masterCheckBox').prop('checked')) {
        $('#masterCheckBox').prop('checked', false)
    } else {
        $('#masterCheckBox').prop('checked', true)
    }
    checkIdAll();
}

function checkIdAll() {

    if ($('#masterCheckBox').prop('checked')) {
        $('.my-id').prop('checked', true);
    } else {
        $('.my-id').prop('checked', false);
    }
}

function generateNew(ele) {
    var table = $(ele).data('name');
    var action = 'generate_row';
    $.post(baseUrl, {
            table,
            action,
            token,
        },
        function(data) {
            var all = JSON.parse(data);
            console.log(data);
            $('#id').val(all.id);
        });
}


function fillSyllabus() {
    dataId = [];
    $(".module-id:checked").each(function() {
        dataId.push($(this).val());

    });
    console.log(dataId);
    $('#course_syllabus').val(dataId.toString());
}

function checkModules() {

    var list = $('#course_syllabus').val();
    var arr = list.split(',');
    console.log(arr);
    $('.module-id').prop('checked', false);
    $.each(arr, function(index, value) {
        console.log(value);
        $('#module' + value).prop('checked', true);
        // $('#module' + value).val(value);

    });
    //alert(list);
}

function fillRegNo() {
    var centerCode = $('#center_id option:selected').html();
    var roll = $('#student_roll').val();
    var regNo = centerCode + '/' + roll;
    $('#registration_no').val(regNo);
}


/*---------------------------------Edit function*/
function editNow(ele) {
    var num = $('.my-id').filter(':checked').length;
    var tableName = $(ele).data('name');
    if (num > 1 || num == 0) {
        notify('error', 'Sorry! Please Select Any One Row');
    } else {
        $(".my-id:checked").each(function() {
            var val = $(this).val();
            var row = $(this).data('row');
            $('#' + tableName + '-modal').modal();
            //alert(JSON.stringify(row));
            fillForm(tableName, row[0]);

        });
        if (tableName == 'course') {
            checkModules();
        }
    }
}

/*---------------------------------Make Default Moodal*/
function makeDefault(ele) {
    var target = $(ele).data('modal');
    var formName = target + 'Form';
    $('#' + formName)[0].reset();
    $('#' + target + 'TitleUpdate').css('display', 'none');
    $('#' + target + 'TitleNew').css('display', 'block');
    $('#dispPhoto').hide();
}
function cleanModal() {
    $('#marksForm')[0].reset();
}
/*--------------------------------*/
function fillForm(tableName, all) {

    var formName = '#' + tableName + 'Form';
    $('#id').val(all.id);
    $('#' + tableName + 'TitleUpdate').css('display', 'block');
    var nameField = tableName + '_name';
    $('#' + tableName + 'TitleUpdate').html('Update Information Of ' + all[nameField]);
    $('#' + tableName + 'TitleNew').css('display', 'none');
    var photoName = tableName + '_photo';
    $('#dispPhoto').show();
    //alert(all[photoName]);
    $('#dispPhoto').attr("src", imgUrl + all[photoName]);
    var $inputs = $(formName + ' :input');
    $inputs.each(function() {
        var x = $(this).attr('id');

        ($("#" + x).val(all[x]));
    });
}

function editResult() {
    var num = $('.my-id').filter(':checked').length;
    if (num > 1 || num == 0) {
        notify('error', 'Sorry! Please Select Any One Row');
    } else {
        $(".my-id:checked").each(function() {
            var val = $(this).val();
            var all = $(this).data('row');
            $('#result-modal').modal();
            $('#id').val(all.id);
            $('#resultTitleUpdate').css('display', 'block');
            $('#resultTitleUpdate').html('Update Result Of ' + all.student_name);
            var $inputs = $('#resultForm :input ');
            $inputs.each(function() {
                var x = $(this).attr('id');
                ($("#" + x).val(all[x]));
            });
        });
    }
}
function sessionGen(ele) {
    var num = $('.my-id').filter(':checked').length;
    var idName = $(ele).data('id_name');
    var dataName = $(ele).data('name');
    var btn = $(ele).val();
    console.log(btn);
    var dataLocation = $(ele).data('location');
    var action = 'session_gen';
    if (num > 1 || num == 0) {
        notify('error', 'Sorry! Please Select Any One Row');
    } else {
        $(".my-id:checked").each(function() {
            var dataId = $(this).val();
            $(ele).html("<i class='fa fa-refresh fa-spin'></i> Wait ...");
            $(ele).prop('disabled', true);
            $.post(baseUrl, {
                    token,
                    action,
                    dataId,
                    dataName,
                    idName,
                },
                function(data) {
                    $(ele).html(btn);
                    $(ele).prop('disabled', false);
                    var all = JSON.parse(data);
                    var message = all.message;
                    var status = all.status;
                    if (status == 'success') {

                        //alert(dataLocation);
                        if (dataLocation == 'registration_receipt' || dataLocation == 'payment_receipt') {
                            var printUrl = baseUrl.slice(0, -4);
                            window.open(printUrl + dataLocation, '_blank');
                        } else {
                            window.open(dataLocation, '_blank');
                        }
                    } else {
                        notify(status, message);
                    }
                });
        });
    }
}

function updateData(ele) {
    var table = $(ele).data('name');
    var action = $(ele).data('ctrl');
    var formName = $(ele).data('frm');
    var btn = $(ele).val();
    var dataId = $('#id').val();
    var formData = new FormData($(formName)[0]);
    var allData = $(formName).serialize() + "&table=" + table + "&action=" + action + "&token=" + token + "&dataId=" + dataId;

    if ($(formName).valid()) {
        $(ele).prop('disabled', true);
        $.ajax({
            beforeSend: function() {
                $(ele).html("<i class='fa fa-refresh fa-spin'></i> Wait ...");
            },
            'type': 'POST',
            'url': baseUrl,
            'data': allData,
            success: function(response) {

                console.log(response);
                var all = JSON.parse(response);
                var message = all.message;
                var status = all.status;
                var loadPage = all.location;

                if (status == 'success') {
                    $(formName)[0].reset();
                    $('.modal').modal('hide');
                    loadTable(table); //-------------------automatic loading table data
                    if (loadPage == 'NA') {
                        notify(status, message);
                    } else {
                        setTimeout(function() {
                            window.location.href = loadPage;
                        }, 500);
                    }
                } else {
                    notify(status, message);
                }
            },
            error: function(e) {
                notify(e.statusText, e.responseText);
            },
            complete: function(data) {
                $(ele).prop('disabled', false);
                $(ele).html(btn);

            }

        });
    }
}

function deleteData(ele) {
    var num = $('.my-id').filter(':checked').length;
    var dataId = [];
    var element = '';
    var table = $(ele).data('name');
    var action = 'delete_data';
    if (num == 0) {
        notify('error', 'Sorry! At Least ONe Row Should Be Selected');
    } else {
        $(".my-id:checked").each(function() {
            dataId.push($(this).val());
            element = $(this);
        });
        bootbox.confirm("Are You Sure To Delete Selected Data?", function(result) {
            if (result) {

                $(ele).prop('disabled', true);
                $(ele).html("<i class='fa fa-refresh fa-spin'></i>");
                $.post(baseUrl, {
                        dataId,
                        table,
                        action,
                        token,
                    },
                    function(data) {
                        var all = JSON.parse(data);
                        var message = all.message;
                        var status = all.status;
                        if (status == 'success') {
                            notify(status, message);
                            $(ele).prop('disabled', false);
                            $(ele).html($(ele).val());
                            element.closest("tr").remove();
                            loadTable(table);

                        } else {
                            notify(status, message);
                            $(ele).prop('disabled', false);
                            $(ele).html($(ele).val());
                        }

                    });
            }
        });

    }
}

/*----------------------------------------Upload Image------------------*/

$("input[type=file]").on('change', function(e) {
    var eleTag = this;
    var file = this.files[0];
    var category, maxSize, realSize, fileType, fileVerify, sizeVerify, finalError, dataControl, displayTag, imagePath;
    var fileName, table, dataId;
    fileType = file.type;
    table = ($(this).attr('data-name'));
    category = ($(this).attr('data-category'));
    displayTag = ($(this).attr('display-tag'));
    dataControl = ($(this).attr('data-ctrl'));
    fileName = ($(this).attr('id'));
    if (!dataControl) {
        dataControl = 'upload_images';
    }
    if (category == 'photo') {
        maxSize = 180000; // bytes 
        realSize = '150 Kb';
    } else if (category == 'signatue') {
        maxSize = 70000; // bytes
        realSize = '50 Kb';
    } else if (category == 'documents') {
        maxSize = 33000000;
        realSize = '3 Mb';
    }

    /*---------------------size validation-----------------------*/
    if (file) { //-------------check empty
        if (category == 'photo' || category == 'signature') {
            if (fileType == 'image/jpg' || fileType == 'image/jpeg' || fileType == 'image/png' || fileType == 'image/gif') { //-----------file type checking --------------*/
                fileVerify = 'success';
            } else {
                // notify('error', 'Sorry! fle is ' + fileType + '.  Please slect a real image');
                finalError = 'Sorry! fle is ' + fileType + '.  Please slect a real image';
                $(this).val('');
            }

            /*--------------------------uploading-------------------------*/
        } else {
            /*-------------------check file type for documents----------------------*/
            if (fileType == 'image/jpg' || fileType == 'image/jpeg' || fileType == 'image/png' || fileType == 'application/pdf') {
                fileVerify = 'success';
            } else {
                finalError = 'Sorry! Only jpg, jpeg, png, gif & pdf files are allowed.';
                $(this).val('');
            }
        }

        if ((file.size) > maxSize) {
            // notify("error", "Sorry please! Selcted file is to  large");
            finalError = "Sorry Please selct a file ";

            $(this).val('');
        } else {

            sizeVerify = 'success';

        }
        /*---------------------end size validation-----------------------*/

        if (fileVerify == 'success' && sizeVerify == 'success') { // ---------- checking validation success------------------------------------*/
            var formData = new FormData(); //--------------------upload Process---------------------*/
            formData.append('table', table);
            var id = $('#id').val();
            formData.append(fileName, file);
            formData.append('category', category);
            formData.append('dataId', id);
            formData.append('token', token);
            formData.append('action', dataControl);
            formData.append('field_name', fileName);
            $('#saveBtn').prop('disabled', true);
            $.ajax({
                url: baseUrl,
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(returndata) {
                    $('#saveBtn').prop('disabled', false);
                    console.log(returndata);
                    var all = JSON.parse(returndata);
                    var message = all.message;
                    var status = all.status;
                    notify(status, message);
                    if (category == 'photo' || category == 'signature') {
                        readURL(eleTag, displayTag);
                    } else {
                        var filleLink = imgUrl + all.data;
                        $(displayTag).show();
                        $(displayTag).attr('href', filleLink);
                    }
                },
                error: function(e) {
                    $('#saveBtn').prop('disabled', false);
                    notify(e.statusText, e.responseText);
                },
                complete: function(data) {
                    $('#saveBtn').prop('disabled', false);
                    console.log(data);

                }
            });
        } else {
            notify('error', finalError + 'less than ' + realSize);
        }
    }
});

function readURL(input, displayTag) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $(displayTag).show();
            $(displayTag).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
//--------------------------------------------Restult & Marksheet-------------*/
function updateResult(ele) {
    var num = $('.my-id').filter(':checked').length;
    var modalName = $(ele).data('modal');
    if (num > 1 || num == 0) {
        notify('error', 'Sorry! Please Select Any One Row');
    } else {
        $(".my-id:checked").each(function() {
            var val = $(this).val();
            var row = $(this).data('row');
            $('#' + modalName + '-modal').modal();
            $('#' + modalName + 'TitleUpdate').html("Update " + modalName + " details Of " + row[0].student_name);
            $('#id').val(row[0].id);
             getSem(row[0].id);
            var all = row[0];
            //alert(JSON.stringify(row));
            if (modalName == 'certificate') {
                var formName = '#' + modalName + 'Form';
                var $inputs = $(formName + ' :input ');
                $inputs.each(function() {
                    var x = $(this).attr('id');
                    ($("#" + x).val(all[x]));
                });
            }
        });
    }
}

function getSem(dataId) {
    var action = 'get_semester';
    $.post(baseUrl, {
            token,
            dataId,
            action,

        },
        function(data) {
            console.log(data);
            // var all = JSON.parse(data);
            // var resultData = all.data;
            $("[name=exam_name]").html(data);
            // $('#id').val(resultData.id);
            // if (all.status == 'success') {
            //     var formName = '#marksForm';
            //     var $inputs = $(formName + ' :input ');
            //     $inputs.each(function() {
            //         var x = $(this).attr('id');
            //         ($("#" + x).val(resultData[x]));
            //     });
            // }
        });
}
function fillMarks(ele) {
    var action = 'get_result';
    var dataId = '';
    var student_id = $('#student_id').val();
    if (student_id == '') {
        dataId = $('#id').val();
    } else {
        dataId = student_id;
    }
    $('#preload-body').show();
    var exam_name = $(ele).val();
    $.post(baseUrl, {
            token,
            dataId,
            action,
            exam_name,


        },
        function(data) {
            $('#preload-body').hide();
            console.log(data);
            var all = JSON.parse(data);
            var resultData = all.data;
            $('#id').val(resultData.id);
            if (all.status == 'success') {
                var formName = '#marksForm';
                var $inputs = $(formName + ' :input ');
                $inputs.each(function() {
                    var x = $(this).attr('id');
                    ($("#" + x).val(resultData[x]));
                });
            }
        });

}

function updateMarks(ele) {
    var table = $(ele).data('name');
    var action = $(ele).data('ctrl');
    var formName = $(ele).data('frm');
    var btn = $(ele).val();
    var dataId = $('#id').val();
    var formData = new FormData($(formName)[0]);
    var allData = $(formName).serialize() + "&table=" + table + "&action=" + action + "&token=" + token + "&dataId=" + dataId;

    if ($(formName).valid()) {
        $(ele).prop('disabled', true);
        $.ajax({
            beforeSend: function() {
                $(ele).html("<i class='fa fa-refresh fa-spin'></i> Wait ...");
            },
            'type': 'POST',
            'url': baseUrl,
            'data': allData,
            success: function(response) {

                console.log(response);
                var all = JSON.parse(response);
                var message = all.message;
                var status = all.status;
                var loadPage = all.location;

                if (status == 'success') {
                    $(formName)[0].reset();
                    $('.modal').modal('hide');
                    $('.my-id').prop('checked', false);
                    // loadTable(table); //-------------------automatic loading table data
                    if (loadPage == 'NA') {
                        notify(status, message);
                    } else {
                        setTimeout(function() {
                            window.location.href = loadPage;
                        }, 500);
                    }
                } else {
                    notify(status, message);
                }
            },
            error: function(e) {
                notify(e.statusText, e.responseText);
            },
            complete: function(data) {
                $(ele).prop('disabled', false);
                $(ele).html(btn);

            }

        });
    }
}

/*--------------------------------Change Password ---------------------------*/
function passToggle() {
    var x = document.getElementById("confirm_password");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}

function confirmPass() {
    var newPass = $('#new_password').val();
    var conPass = $('#confirm_password').val();
    if (newPass != conPass) {
        $('#passError').show();
        $('#changePass').prop('disabled', true);
        $('#passError').html("Confirm Password Didn't Match");
    } else {
        $('#passError').hide();
        $('#changePass').attr('disabled', false);
    }
}

function changePassword(ele) {
    var table = $(ele).data('name');
    var action = $(ele).data('ctrl');
    var formName = $(ele).data('frm');
    var btn = $(ele).val();
    var formData = new FormData($(formName)[0]);
    var allData = $(formName).serialize() + "&table=" + table + "&action=" + action + "&token=" + token + "&dataId=" + user_id;

    if ($(formName).valid()) {
        $(ele).prop('disabled', true);
        $.ajax({
            beforeSend: function() {
                $(ele).html("<i class='fa fa-refresh fa-spin'></i> Wait ...");
            },
            'type': 'POST',
            'url': baseUrl,
            'data': allData,
            success: function(response) {
                console.log(response);
                var all = JSON.parse(response);
                var message = all.message;
                var status = all.status;
                var loadPage = all.location;

                if (status == 'success') {
                    $(formName)[0].reset();
                    $('.modal').modal('hide');
                    notify(status, message);
                } else {
                    notify(status, message);
                }
            },
            error: function(e) {
                notify(e.statusText, e.responseText);
            },
            complete: function(data) {
                $(ele).prop('disabled', false);
                $(ele).html(btn);

            }

        });
    }
}

function resetPass(ele) {
    var table = 'user';
    var action = 'reset_password';
    var formName = "#resetForm";
    var btn = $(ele).val();
    var formData = new FormData($(formName)[0]);
    var allData = $(formName).serialize() + "&table=" + table + "&action=" + action;

    if ($(formName).valid()) {
        $(ele).prop('disabled', true);
        $.ajax({
            beforeSend: function() {
                $(ele).html("<i class='fa fa-refresh fa-spin'></i> Wait ...");
            },
            'type': 'POST',
            'url': baseUrl,
            'data': allData,
            success: function(response) {
                console.log(response);
                var all = JSON.parse(response);
                var message = all.message;
                var status = all.status;
                var loadPage = all.location;

                if (status == 'success') {
                    $(formName)[0].reset();
                    $('.modal').modal('hide');
                    notify(status, message);
                } else {
                    notify(status, message);
                }
            },
            error: function(e) {
                notify(e.statusText, e.responseText);
            },
            complete: function(data) {
                $(ele).prop('disabled', false);
                $(ele).html(btn);

            }

        });
    }
}
/*--------------------------------End Change Password---------------------------*/
/*--------------------------------logout---------------------------*/
function logOut() {
    bootbox.confirm("Are You Sure To Logout?", function(result) {
        if (result) {
            var action = 'logout';
            $.post(baseUrl, {
                    token,
                    action,

                },
                function(data) {
                    var all = JSON.parse(data);
                    var message = all.message;
                    var status = all.status;
                    if (message == 'success') {
                        localStorage.setItem('token', null);
                        localStorage.setItem('user_id', null);
                        localStorage.setItem('user_type', null);
                        notify(status, message);
                        setTimeout(function() {
                            window.location.href = 'admin';
                        }, 1000);
                    } else {
                        notify(status, message);
                        setTimeout(function() {
                            window.location.href = 'admin';
                        }, 1000);
                    }
                });

        }
    });
}


/*-----------------------------Notification function------------------------*/
function notify(status, message) {
    if (status == 'success') {
        round_success_noti(message);
        return false;
    } else {
        round_error_noti(message)
        return false;
    }
}