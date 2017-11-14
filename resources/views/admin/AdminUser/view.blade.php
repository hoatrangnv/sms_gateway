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
            <li class="active">Danh sách tài khoản</li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->
                <div class="panel panel-info">
                    <form method="Post" action="" role="form">
                     {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="form-group col-lg-3">
                            <label for="user_name"><i>Tên đăng nhập</i></label>
                            <input type="text" class="form-control input-sm" id="user_name" name="user_name" autocomplete="off" placeholder="Tên đăng nhập" @if(isset($dataSearch['user_name']))value="{{$dataSearch['user_name']}}"@endif>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="user_email"><i>Email</i></label>
                            <input type="text" class="form-control input-sm" id="user_email" name="user_email" autocomplete="off" placeholder="Địa chỉ email" @if(isset($dataSearch['user_email']))value="{{$dataSearch['user_email']}}"@endif>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="user_phone"><i>Di động</i></label>
                            <input type="text" class="form-control input-sm" id="user_phone" name="user_phone" autocomplete="off" placeholder="Số di động" @if(isset($dataSearch['user_phone']))value="{{$dataSearch['user_phone']}}"@endif>
                        </div>
                        <div class="form-group col-lg-3">
                            <label for="user_group"><i>Nhóm quyền</i></label>
                            <select name="user_group" id="user_group" class="form-control input-sm" tabindex="12" data-placeholder="Chọn nhóm quyền">
                                <option value="0">--- Chọn nhóm quyền ---</option>
                                @foreach($arrGroupUser as $k => $v)
                                    <option value="{{$k}}" @if($dataSearch['user_group'] == $k) selected="selected" @endif>{{$v['group_user_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <span class="">
                            <a class="btn btn-danger btn-sm" href="{{URL::route('admin.user_edit',array('id' => FunctionLib::inputId(0)))}}">
                                <i class="ace-icon fa fa-plus-circle"></i>
                                Thêm mới
                            </a>
                        </span>
                        <span class="">
                            <button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-search"></i> Tìm kiếm</button>
                        </span>
                    </div>
                    </form>
                </div>
                @if(sizeof($data) > 0)
                    <div class="span clearfix"> @if($size >0) Có tổng số <b>{{$size}}</b> tài khoản  @endif </div>
                    <br>
                    <table class="table table-bordered">
                        <thead class="thin-border-bottom">
                        <tr class="">
                            <th width="5%" class="text-center">STT</th>
                            <th width="20%">Thông tin User</th>
                            <th width="40%">Thông tin liên hệ</th>
                            <th width="10%" class="text-center">Vai trò</th>
                            <th width="10%" class="text-center">Ngày tạo</th>
                            <th width="15%" class="text-center">Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key => $item)
                            <tr @if($item['user_status'] == -1)class="red bg-danger middle" {else} class="middle" @endif>
                                <td class="text-center middle">{{ $start+$key+1 }}</td>
                                <td>
                                    <div class="green"><b>Tài khoản : </b>{{ $item['user_name'] }}</div>
                                    <div><b>Họ tên : </b>{{ $item['user_full_name'] }}</div>
                                    <div><b>Email : </b>{{ $item['user_email'] }}</div>
                                </td>
                                <td>
                                    @if(trim($item['user_phone']) != '')<div><b>Phone : </b>{{ $item['user_phone'] }}</div>@endif
                                    @if(trim($item['telephone']) != '')<div><b>Telephone : </b>{{ $item['telephone'] }}</div>@endif
                                    @if(trim($item['number_code']) != '')<div><b>Giấy phép KD : </b>{{ $item['number_code'] }}</div>@endif
                                    @if(trim($item['address_register']) != '')<div><b>Địa chỉ KD : </b>{{ $item['address_register'] }}</div>@endif
                                </td>
                                <td class="text-center middle">{{$item['role_name']}}</td>
                                <td class="text-center middle">
                                    @if($item['user_created'])
                                        {{ date("d-m-Y",$item['user_created']) }}
                                    @endif
                                </td>
                                <td class="text-center middle" align="center">
                                    @if($is_root || $permission_edit)
                                        <a href="{{URL::route('admin.user_edit',array('id' => FunctionLib::inputId($item['user_id'])))}}" title="Setting item"><i class="fa fa-cog fa-2x"></i></a> &nbsp;&nbsp;&nbsp;
                                    @endif
                                    @if($is_root || $permission_edit)
                                        <a href="{{URL::route('admin.user_edit',array('id' => FunctionLib::inputId($item['user_id'])))}}" title="Sửa item"><i class="fa fa-edit fa-2x"></i></a>&nbsp;&nbsp;&nbsp;
                                    @endif
                                    @if($is_root || $permission_change_pass)
                                        <a href="{{URL::route('admin.user_change',array('id' => FunctionLib::inputId($item['user_id'])))}}" title="Đổi mật khẩu"><i class="fa fa-refresh fa-2x"></i></a>&nbsp;&nbsp;&nbsp;
                                    @endif
                                    @if($is_boss || $permission_remove)
                                        <a href="javascript:void(0)" class="sys_delete_user" data-content="Xóa tài khoản" data-placement="bottom" data-trigger="hover" data-rel="popover" data-url="user/remove/" data-id="{{FunctionLib::inputId($item['user_id'])}}">
                                            <i class="fa fa-trash fa-2x"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="text-right">
                        {!! $paging !!}
                    </div>
                @else
                    <div class="alert">
                        Không có dữ liệu
                    </div>
                @endif
            </div>
        </div>
    </div><!-- /.page-content -->
</div>

<script type="text/javascript">
    $('[data-rel=popover]').popover({container: 'body'});
</script>
@stop