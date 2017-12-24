<?php use App\Library\AdminFunction\CGlobal; ?>
<?php use App\Library\AdminFunction\Define; ?>
<?php use App\Library\AdminFunction\FunctionLib; ?>
@extends('admin.AdminLayouts.index')
@section('content')
<div class="main-content-inner">
    <div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{URL::route('admin.dashboard')}}">Home</a>
            </li>
            <li><a href="{{URL::route('admin.user_view')}}"> Danh sách tài khoản</a></li>
            <li class="active">Sửa tài khoản</li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->
                <form method="POST" action="" role="form">
                @if(isset($error))
                    <div class="alert alert-danger" role="alert">
                        @foreach($error as $itmError)
                            <p>{!! $itmError !!}</p>
                        @endforeach
                    </div>
                @endif

                <div style="float: left; width: 50%">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Tên đăng nhập<span class="red"> (*) </span></label>
                            <input type="text" placeholder="Tên đăng nhập" id="user_name" name="user_name"  class="form-control input-sm" value="@if(isset($data['user_name'])){{$data['user_name']}}@endif">
                        </div>
                    </div>
                    @if($id == 0)
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Mật khẩu<span class="red"> (* Mặc định: Sms@!2017) </span></label>
                            <input type="password"  id="user_password" name="user_password" class="form-control input-sm" value="Sms@!2017">
                        </div>
                    </div>
                    @endif
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="name" class="control-label">Tên nhân viên<span class="red"> (*) </span></label>
                            <input type="text" placeholder="Tên nhân viên" id="user_full_name" name="user_full_name"  class="form-control input-sm" value="@if(isset($data['user_full_name'])){{$data['user_full_name']}}@endif">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Kiểu User<span class="red"> (*) </span></label>
                            <select name="role_type" id="role_type" class="form-control input-sm">
                                {!! $optionRoleType !!}
                            </select>
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
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Giới tính</label>
                            <select name="user_sex" id="user_sex" class="form-control input-sm">
                                {!! $optionSex !!}
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">Trạng thái</label>
                            <select name="user_status" id="user_status" class="form-control input-sm">
                                {!! $optionStatus !!}
                            </select>
                        </div>
                    </div>
                </div>

                <div style="float: left; width: 50%; display: none">
                    <div id="show_category_sub_campaign" class="body">
                        <label for="name" class="control-label">Menu hiển thị</label>
                        @if(isset($menuAdmin) && !empty($menuAdmin))
                            <div style="float: left; width: 100%;min-height: 250px;max-height:405px;overflow-x: hidden;">
                                <table class="table table-bordered table-hover">
                                    @foreach ($menuAdmin as $menu_id => $menu_name)
                                        <tr>
                                            <td class="text-center text-middle">
                                                <input type="checkbox" class="checkItem" name="user_group_menu[]"
                                                       @if(in_array($menu_id,$arrUserGroupMenu)) checked="checked" @endif
                                                       value="{{(int)$menu_id}}" />
                                            </td>
                                            <td class="text-left text-middle">
                                                @if(isset($languageSite) && $languageSite == Define::VIETNAM_LANGUAGE)
                                                    <b>{{ $menu_name['menu_name'] }}</b>
                                                @else
                                                    <b>{{ $menu_name['menu_name_en'] }}</b>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <div style="display: none">
                <div class="clearfix"></div>
                <div class="col-sm-2">
                    <label for="name" class="control-label">Danh sách nhóm quyền</label>
                </div>
                <div class="clearfix"></div>
                <hr/>
                <div class="clearfix"></div>
                    <div style="float: left; width: 100%;min-height: 100px;max-height:100px;overflow-x: hidden;">
                        @foreach($arrGroupUser as $key => $val)
                            <div class="col-sm-2">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="user_group[]" id="user_group_{{$val['group_user_id']}}" value="{{$val['group_user_id']}}" @if(isset($data['user_group']) && in_array($val['group_user_id'],$data['user_group'])) checked="checked" @endif> {{$val['group_user_name']}}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-sm-12 text-left">
                    {!! csrf_field() !!}
                    <a class="btn btn-warning" href="{{URL::route('admin.user_view')}}"><i class="fa fa-reply"></i> Trở lại</a>
                    <button  class="btn btn-primary"><i class="fa fa-floppy-o"></i> Lưu lại</button>
                </div>
                </form>
                <!-- PAGE CONTENT ENDS -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div><!-- /.page-content -->
</div>
@stop