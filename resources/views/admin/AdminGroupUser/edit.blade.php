@extends('admin.AdminLayouts.index')
@section('content')
<div class="main-content-inner">
    <div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{URL::route('admin.dashboard')}}">Home</a>
            </li>
            <li><a href="{{URL::route('admin.groupUser_view')}}"> Danh sách nhóm quyền</a></li>
            <li class="active">Sửa nhóm quyền</li>
        </ul>
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
                        <i>Tên nhóm</i>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" name="group_user_name"
                               value="@if(isset($data['group_user_name'])){{$data['group_user_name']}}@endif">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <i>Vị trí hiển thị</i>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" name="group_user_order"
                               value="@if(isset($data['group_user_order'])){{$data['group_user_order']}}@endif">
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <i>Show quyền</i>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <select name="group_user_view" id="group_user_view" class="form-control input-sm">
                            {!! $optionView !!}
                        </select>
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
                        <select name="group_user_status" id="group_user_status" class="form-control input-sm">
                            {!! $optionStatus !!}
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div valign="top" class="col-sm-2">
                    <div class="form-group">
                        <i>Danh sách quyền</i>
                    </div>
                </div>
                <div class="col-sm-10" style="float: left; width: 100%;min-height: 400px;max-height:400px;overflow-x: hidden;">
                    @foreach($arrPermissionByController as $key => $val)
                        <h4 class="header">@if($key || $key != ''){{$key}}@else Khac @endif</h4>
                        @foreach($val as $k => $v)
                            <label class="middle col-sm-2">
                                <input type="checkbox" name="permission_id[]" value="{{$v['permission_id']}}"
                                       class="ace" @if(isset($data['strPermission'])) @if(in_array($v['permission_id'],$data['strPermission']))
                                       checked @endif @endif>
                                <span class="lbl"> {{$v['permission_name']}}</span>
                            </label>
                        @endforeach
                        <div class="clearfix"></div>
                    @endforeach
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-sm-12 text-right">
                    @if($is_root || $permission_edit || $permission_view)
                        <button class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Lưu lại</button>
                    @endif
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div><!-- /.page-content -->
</div>
@stop