<?php use App\Library\AdminFunction\FunctionLib; ?>
    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                <form method="POST" action="" role="form" id="form_user_setting">
                    <input type="hidden" id="user_id" name="user_id" class="form-control input-sm" value="{{$user_id}}">
                    <input type="hidden" id="user_setting_id" name="user_setting_id" class="form-control input-sm" value="@if(isset($data['user_setting_id'])){{FunctionLib::inputId($data['user_setting_id'])}}@else {{FunctionLib::inputId(0)}}@endif">
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
                            <label for="name" class="control-label">Độ ưu tiên</label>
                            <input type="text" placeholder="Email" id="priority" name="priority"  class="form-control input-sm" value="@if(isset($data['priority'])){{$data['priority']}}@endif">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Số dư tài khoản</label>
                            <input type="text" placeholder="Email" id="account_balance" name="account_balance"  class="form-control input-sm" value="@if(isset($data['account_balance'])){{$data['account_balance']}}@endif">
                        </div>
                    </div>


                    @if(!empty($data['carrier']))
                        <div class="clear"></div>
                        <div class="col-sm-6">
                            <label for="name" class="control-label">Chi phí 1 SMS (vnđ)</label>
                            <div class="clear"></div>
                            @foreach ($data['carrier'] as $key => $carr)
                                <div class="col-sm-5">
                                    <input type="text" id="cost" name="cost_{{$carr['carrier_id']}}"  class="form-control input-sm" value="@if(isset($carr['cost'])){{$carr['cost']}}@endif">
                                    <input type="hidden" id="carrierId" name="carrier_id_{{$carr['carrier_id']}}" class="form-control input-sm" value="@if(isset($carr['carrier_id'])){{$carr['carrier_id']}}@else 0 @endif">
                                </div>
                                <div class="col-sm-7">
                                    <label for="name" class="control-label">{{$carr['carrier_name']}}</label>
                                </div>
                                <div class="clear"></div>
                            @endforeach
                        </div>
                    @endif


                    @if(isset($data['role_type']) && $data['role_type'] == \App\Library\AdminFunction\Define::ROLE_TYPE_ADMIN )
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Số SMS đã chuyển/ngày</label>
                                <input type="text" class="form-control input-sm" value="@if(isset($data['count_sms_number'])){{$data['count_sms_number']}}@endif" readonly>
                            </div>
                        </div>
                    @endif
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
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Hình thức thanh toán</label>
                            <select name="payment_type" id="payment_type" class="form-control input-sm">
                                {!! $optionPayment !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-sm-12 text-left">
                        {!! csrf_field() !!}
                        <a href="#"  class="btn btn-primary" onclick="Admin.submitInfoSettingUser()"><i class="fa fa-floppy-o"></i> Lưu lại</a>
                    </div>
            </form>
        </div>
    </div>
</div>

