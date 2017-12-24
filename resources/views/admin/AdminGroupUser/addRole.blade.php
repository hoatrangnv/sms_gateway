<?php use App\Library\AdminFunction\FunctionLib; ?>
<?php use App\Library\AdminFunction\Define; ?>
@extends('admin.AdminLayouts.index')
@section('content')
<div class="main-content-inner">
    <div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{URL::route('admin.dashboard')}}">Home</a>
            </li>
            <li><a href="{{URL::route('admin.viewRole')}}"> Danh sách phân quyền theo role</a></li>
            <li class="active">@if($id > 0)Cập nhật @else Tạo mới @endif</li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->
                {{Form::open(array('method' => 'POST','role'=>'form','files' => true))}}
                @if(isset($error))
                    <div class="alert alert-danger" role="alert">
                        @foreach($error as $itmError)
                            <p>{{ $itmError }}</p>
                        @endforeach
                    </div>
                @endif
                <div style="float: left; width: 50%">
                    <div class="col-sm-10">
                        <div class="form-group">
                            <label for="name" class="control-label">Phân quyền theo Role</label>
                            <select name="role_id" id="role_id" class="form-control input-sm">
                                {!! $optionRole !!}
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-10">
                        <div class="form-group">
                            <label for="name" class="control-label">Trạng thái</label>
                            <select name="role_status" id="role_status" class="form-control input-sm">
                                {!! $optionStatus !!}
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="id_hiden" name="id_hiden" value="{{$id}}"/>
                </div>

                <div style="float: left; width: 50%">
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
                                    <input type="checkbox" name="user_group[]" id="user_group_{{$val['group_user_id']}}" value="{{$val['group_user_id']}}" @if(isset($data['role_group_permission']) && in_array($val['group_user_id'],$data['role_group_permission'])) checked="checked" @endif> {{$val['group_user_name']}}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="clearfix"></div>
                {!! csrf_field() !!}
                <div class="form-group col-sm-12 text-left">
                    <a class="btn btn-warning" href="{{URL::route('admin.viewRole')}}"><i class="fa fa-reply"></i> {{FunctionLib::viewLanguage('back')}}</a>
                    <button  class="btn btn-primary"><i class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('save')}}</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div><!-- /.page-content -->
</div>
@stop
