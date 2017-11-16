<?php use App\Library\AdminFunction\CGlobal; ?>
<?php use App\Library\AdminFunction\Define; ?>
<?php use App\Library\AdminFunction\FunctionLib; ?>
    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                <form method="POST" action="" role="form" id="form_user_setting">
                <div style="float: left; width: 50%">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Tên đăng nhập<span class="red"> (*) </span></label>
                            <input type="text" placeholder="Tên đăng nhập" id="user_name" name="user_name"  class="form-control input-sm" value="@if(isset($data['user_name'])){{$data['user_name']}}@endif">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Tên nhân viên<span class="red"> (*) </span></label>
                            <input type="text" placeholder="Tên nhân viên" id="user_full_name" name="user_full_name"  class="form-control input-sm" value="@if(isset($data['user_full_name'])){{$data['user_full_name']}}@endif">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Email<span class="red"> (*) </span></label>
                            <input type="text" placeholder="Email" id="user_email" name="user_email"  class="form-control input-sm" value="@if(isset($data['user_email'])){{$data['user_email']}}@endif">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Phone</label>
                            <input type="text" placeholder="Phone" id="user_phone" name="user_phone"  class="form-control input-sm" value="@if(isset($data['user_phone'])){{$data['user_phone']}}@endif">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Telephone</label>
                            <input type="text" placeholder="Telephone" id="telephone" name="telephone"  class="form-control input-sm" value="@if(isset($data['telephone'])){{$data['telephone']}}@endif">
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="name" class="control-label">Số đăng ký kinh doanh</label>
                            <input type="text" placeholder="Số đăng ký kinh doanh" id="number_code" name="number_code"  class="form-control input-sm" value="@if(isset($data['number_code'])){{$data['number_code']}}@endif">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="name" class="control-label">Địa chỉ kinh doanh</label>
                            <input type="text" placeholder="Địa chỉ kinh doanh" id="address_register" name="address_register"  class="form-control input-sm" value="@if(isset($data['address_register'])){{$data['address_register']}}@endif">
                        </div>
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

