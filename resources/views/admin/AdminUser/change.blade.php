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
            <li class="active">Đổi mật khẩu</li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        {{--<div class="page-header">--}}
        {{--<h1>--}}
        {{--<small>--}}
        {{--Danh sách khách hàng--}}
        {{--</small>--}}
        {{--</h1>--}}
        {{--</div><!-- /.page-header -->--}}

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
                @if(!$permission_change_pass)
                    <div class="col-sm-2">
                        <div class="form-group">
                            <i>Mật khẩu hiện tại</i>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <input type="password" class="form-control input-sm" name="old_password"
                                   value="@if(isset($data['old_password'])){{$data['old_password']}}@endif">
                        </div>
                    </div>
                @endif
                <div class="clearfix"></div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <i>Mật khẩu mới</i>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="password" class="form-control input-sm" name="new_password"
                               value="@if(isset($data['new_password'])){{$data['new_password']}}@endif">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <i>Xác nhận mật khẩu</i>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="password" class="form-control input-sm" name="confirm_new_password"
                               value="@if(isset($data['confirm_new_password'])){{$data['confirm_new_password']}}@endif">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-sm-12 text-right">
                    {!! csrf_field() !!}
                    <button  class="btn btn-primary">Đổi mật khẩu</button>
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
