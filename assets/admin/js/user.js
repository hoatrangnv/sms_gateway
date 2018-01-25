/**
 * Created by QuynhTM on 10/07/2015.
 */
$(document).ready(function () {
    $(".sys_delete_user").on('click',function(){
        var $this = $(this);
        var id = $(this).attr('data-id');
        var url = $(this).attr('data-url');
        var _token = $('input[name="_token"]').val();
        bootbox.confirm("Bạn chắc chắn muốn xóa item này", function(result) {
            if(result == true){
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: WEB_ROOT + '/manager/'+url+id,
                    data: {
                        '_token':_token,
                    },
                    beforeSend: function () {
                        $('.modal').modal('hide')
                    },
                    error: function () {
                        bootbox.alert('Lỗi hệ thống');
                    },
                    success: function (data) {
                        if(data.success == 1){
                            bootbox.alert('Xóa item thành công');
                            $this.parents('tr').html('');
                        }else{
                            bootbox.alert('Lỗi cập nhật');
                        }
                    }
                });
            }
        });
    });
})

var SmsAdmin = {
    /**
     *********************************************************************************************************************
     * Function cho SMS
     * *******************************************************************************************************************
     */
    changeUserWaittingProcessSms: function(sms_log_id,total_sms) {
        var user_manager_id = $('#user_manager_id_'+sms_log_id).val();
        var _token = $('input[name="_token"]').val();
        if(user_manager_id > 0 && total_sms > 0 && sms_log_id > 0){
            $('#img_loading_'+sms_log_id).show();
            $.ajax({
                type: "POST",
                url: WEB_ROOT + '/manager/waittingSms/changeUserWaittingProcessSms',
                data: {sms_log_id : sms_log_id, total_sms : total_sms, user_manager_id : user_manager_id, _token : _token},
                dataType: 'json',
                success: function(res) {
                    $('#img_loading_'+sms_log_id).hide();
                    if(res.isIntOk == 1){
                        window.location.reload();
                    }else {
                        alert(res.msg);
                    }
                }
            });
        }else {
            alert('Bạn chưa tài khoản nào!')
        }
    },

    getSettingContentAttach: function() {
        var type_page = $('#type_page').val();
        $.ajax({
            type: "GET",
            url: WEB_ROOT + '/manager/waittingSms/getSettingContentAttach',
            data: {type_page:type_page},
            dataType: 'json',
            success: function(res) {
                if(res.isIntOk == 1){
                    $('#concatenation_strings').val(res.msg);
                }
            }
        });
    },
    getContentGraftedSms: function(sms_sendTo_id) {
        var _token = $('input[name="_token"]').val();
        $.ajax({
            type: "GET",
            url: WEB_ROOT + '/manager/waittingSms/getContentGraftedSms',
            data: {sms_sendTo_id:sms_sendTo_id, _token:_token},
            dataType: 'json',
            success: function(res) {
                if(res.isIntOk == 1){
                    $('#sys_showContentSms').modal('show');
                    $('#content_grafted').val(res.content_grafted);
                    $('#sms_sendTo_id_popup').val(res.sms_sendTo_id);
                }
            }
        });
    },

    submitContentGraftedSms: function() {
        var _token = $('input[name="_token"]').val();
        var content_grafted = $('#content_grafted').val();
        var sms_sendTo_id = $('#sms_sendTo_id_popup').val();
        $.ajax({
            type: "GET",
            url: WEB_ROOT + '/manager/waittingSms/submitContentGraftedSms',
            data: {sms_sendTo_id:sms_sendTo_id, content_grafted:content_grafted, _token:_token},
            dataType: 'json',
            success: function(res) {
                if(res.isIntOk == 1){
                    $('#sys_showContentSms').modal('hide');
                    window.location.reload();
                }
            }
        });
    },
    getContentSmsClever: function(sms_clever_id) {
        var _token = $('input[name="_token"]').val();
        $.ajax({
            type: "GET",
            url: WEB_ROOT + '/manager/sendSmsClever/getContentSms',
            data: {sms_clever_id:sms_clever_id, _token:_token},
            dataType: 'json',
            success: function(res) {
                if(res.isIntOk == 1){
                    $('#sys_showContentSms').modal('show');
                    $('#content_clever').val(res.content);
                    $('#sms_clever_id_popup').val(res.sms_clever_id);
                }
            }
        });
    },
    submitContentSmsClever: function() {
        var _token = $('input[name="_token"]').val();
        var content_clever = $('#content_clever').val();
        var sms_clever_id = $('#sms_clever_id_popup').val();
        $.ajax({
            type: "Post",
            url: WEB_ROOT + '/manager/sendSmsClever/submitContentSms',
            data: {sms_clever_id:sms_clever_id, content_clever:content_clever, _token:_token},
            dataType: 'json',
            success: function(res) {
                if(res.isIntOk == 1){
                    $('#sys_showContentSms').modal('hide');
                    window.location.reload();
                }
            }
        });
    },

    /***********************************************************************************************************************
     * WaittingSendSms
     * @param sms_log_id
     * @param total_sms
     * *********************************************************************************************************************
     */
    changeModemWaittingSendSms: function(sms_log_id,total_sms) {
        var list_modem = $('#list_modem_'+sms_log_id).val();
        var _token = $('input[name="_token"]').val();
        if(list_modem > 0 && total_sms > 0 && sms_log_id > 0){
            $('#img_loading_'+sms_log_id).show();
            $.ajax({
                type: "POST",
                url: WEB_ROOT + '/manager/waittingSms/changeModemWaittingSendSms',
                data: {sms_log_id : sms_log_id, total_sms : total_sms, list_modem : list_modem, _token : _token},
                dataType: 'json',
                success: function(res) {
                    $('#img_loading_'+sms_log_id).hide();
                    if(res.isIntOk == 1){
                        window.location.reload();
                    }else {
                        alert(res.msg);
                    }
                }
            });
        }else {
            alert('Bạn chưa chọn Modem nào!')
        }
    },
    refuseModem: function(sms_log_id) {
        var _token = $('input[name="_token"]').val();
        if(confirm('Bạn có chắc chắn chuyển đổi')){
            $.ajax({
                type: "POST",
                url: WEB_ROOT + '/manager/waittingSms/refuseModem',
                data: {sms_log_id:sms_log_id, _token:_token},
                dataType: 'json',
                success: function(res) {
                    if(res.isIntOk == 1){
                        alert(res.msg);
                        window.location.reload();
                    }
                }
            });
        }
    },

    uploadFileExcelPhoneSend: function() {
        var _token = $('input[name="_token"]').val();
        $.ajax({
            type: "GET",
            url: WEB_ROOT + '/manager/sendSms/uploadFileExcelPhone',
            data:new FormData($("#upload_form")[0]),
            dataType:'json',
            async:false,
            processData: false,
            contentType: false,
            success:function(response){
                console.log(response);
            },
        });
    },
}
