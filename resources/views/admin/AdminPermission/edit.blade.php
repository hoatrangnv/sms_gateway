@extends('admin.AdminLayouts.index')
@section('content')
<div class="main-content-inner">
    <div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{URL::route('admin.dashboard')}}">Home</a>
            </li>
            <li><a href="{{URL::route('admin.permission_view')}}"> Danh sách quyền</a></li>
            <li class="active">Sửa quyền</li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->
                {{Form::open(array('method' => 'POST', 'role'=>'form'))}}
                @if(isset($error))
                    <div class="alert alert-danger" role="alert">
                        @foreach($error as $itmError)
                            <p>{{ $itmError }}</p>
                        @endforeach
                    </div>
                @endif
                <div class="col-sm-2">
                    <div class="form-group">
                        <i>Mã quyền</i>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" name="permission_code"
                               value="@if(isset($data['permission_code'])){{$data['permission_code']}}@endif">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <i>Tên quyền</i>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" name="permission_name"
                               value="@if(isset($data['permission_name'])){{$data['permission_name']}}@endif">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <i>Danh mục</i>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" name="permission_group_name"
                               value="@if(isset($data['permission_group_name'])){{$data['permission_group_name']}}@endif">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <i>Trạng thái</i>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <select name="permission_status" id="permission_status" class="form-control input-sm">
                            @foreach($arrStatus as $k => $v)
                                @if($k != 0)
                                    <option value="{{$k}}" @if($data['permission_status'] == $k)
                                            selected="selected" @endif>{{$v}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-sm-12 text-right">
                    <button  class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Lưu lại</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@stop