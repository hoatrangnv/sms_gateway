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
                    url: WEB_ROOT + '/admin/'+url+id,
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
     * @param id
     * Function cho SMS
     * *******************************************************************************************************************
     */
    changeUserWaittingProcessSms: function(sms_log_id,total_sms,status) {
        var user_manager_id = $('#user_manager_id_'+sms_log_id).val();
        if(user_manager_id > 0 && total_sms > 0 && sms_log_id > 0){
            $('#img_loading_'+sms_log_id).show();
            $.ajax({
                type: "GET",
                url: WEB_ROOT + '/manager/waittingSms/changeUserWaittingProcessSms',
                data: {sms_log_id : sms_log_id, total_sms : total_sms, user_manager_id : user_manager_id},
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
        }
    },

    updateWaittingSms2: function(user_id) {
        $('#sys_showPopupInfoSetting').modal('show');
        $('#img_loading_').show();
        $('#sys_show_infor').html('');
        $.ajax({
            type: "GET",
            url: WEB_ROOT + '/manager/user/getInfoSettingUser',
            data: {user_id : user_id},
            dataType: 'json',
            success: function(res) {
                $('#img_loading').hide();
                $('#sys_show_infor').html(res.html);
            }
        });
    },
}
