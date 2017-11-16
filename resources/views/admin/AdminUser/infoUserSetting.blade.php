<?php use App\Library\AdminFunction\FunctionLib; ?>
    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                <form method="POST" action="" role="form" id="form_user_setting">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Họ và tên</label>
                            <input type="text" class="form-control input-sm" value="@if(isset($data['user_full_name'])){{$data['user_full_name']}}@endif" readonly>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Vai trò</label>
                            <input type="text" class="form-control input-sm" value="@if(isset($data['role_name'])){{$data['role_name']}}@endif" readonly>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Hình thức thanh toán</label>
                            <select name="payment_type" id="payment_type" class="form-control input-sm">
                                {!! $optionPayment !!}
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Số dư tài khoản</label>
                            <input type="text" placeholder="Email" id="account_balance" name="account_balance"  class="form-control input-sm" value="@if(isset($data['account_balance'])){{$data['account_balance']}}@endif">
                        </div>
                    </div>

                    @if($data['role_type'] == \App\Library\AdminFunction\Define::ROLE_TYPE_ADMIN && isset($data['role_type']))
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Tự động quét khi gửi tin</label>
                            <select name="scan_auto" id="scan_auto" class="form-control input-sm">
                                {!! $optionScanAuto !!}
                            </select>
                        </div>
                    </div>
                    @endif
                    @if($data['role_type'] == \App\Library\AdminFunction\Define::ROLE_TYPE_CUSTOMER && isset($data['role_type']))
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Lựa chọn gửi tin</label>
                            <select name="sms_send_auto" id="sms_send_auto" class="form-control input-sm">
                                {!! $optionSendAuto !!}
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="form-group col-sm-12 text-left">
                        {!! csrf_field() !!}
                        <a href="#"  class="btn btn-primary" onclick="Admin.submitInfoSettingUser()"><i class="fa fa-floppy-o"></i> Lưu lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

