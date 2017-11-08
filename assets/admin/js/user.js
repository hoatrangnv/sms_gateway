/**
 * Created by Tuan on 10/07/2015.
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
