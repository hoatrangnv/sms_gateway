<?php use App\Library\AdminFunction\FunctionLib; ?>
<?php use App\Library\AdminFunction\Define; ?>
@extends('admin.AdminLayouts.index')
@section('content')
<div class="main-content-inner">
    <div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{URL::route('admin.dashboard')}}">{{FunctionLib::viewLanguage('home')}}</a>
            </li>
            <li class="active">{{FunctionLib::viewLanguage('system_setting')}}</li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->
                <div class="panel panel-info">
                    {{ Form::open(array('method' => 'GET', 'role'=>'form')) }}
                    <div class="panel-body">
                        <div class="form-group col-lg-4">
                            <label for="banner_name">{{FunctionLib::viewLanguage('time_check_connect')}}</label>
                            <input type="number" class="form-control input-sm" id="time_check_connect" name="time_check_connect" placeholder="" @if(isset($search['time_check_connect']) && $search['time_check_connect'] != '')value="{{$search['time_check_connect']}}"@endif>
                        </div>

                        <div class="form-group col-lg-12 text-right">
                            @if($is_root || $permission_full ==1 || $permission_create == 1)
                                <a class="btn btn-danger btn-sm" href="{{URL::route('admin.systemSettingEdit',array('id' => FunctionLib::inputId(0)))}}">
                                    <i class="ace-icon fa fa-plus-circle"></i>
                                    {{FunctionLib::viewLanguage('add')}}
                                </a>
                            @endif
                                {{--<button class="btn btn-warning btn-sm" type="submit" name="submit" value="2"><i class="fa fa-file-excel-o"></i> Xuất Excel</button>--}}
                                <button class="btn btn-primary btn-sm" type="submit" name="submit" value="1"><i class="fa fa-search"></i> {{FunctionLib::viewLanguage('search')}}</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
                @if(sizeof($data) > 0)
                    <div class="span clearfix"> @if($size >0) {{FunctionLib::viewLanguage('total')}} <b>{{$size}}</b> {{FunctionLib::viewLanguage('results')}}  @endif </div>
                    <br>
                    <table class="table table-bordered">
                        <thead class="thin-border-bottom">
                        <tr class="">
                            <th class="w10" class="text-center">{{FunctionLib::viewLanguage('no')}}</th>
                            <th width="w50">{{FunctionLib::viewLanguage('time_check_connect')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('concatenation_strings')}}</th>
                            <th width="w100" class="text-center">{{FunctionLib::viewLanguage('create_at')}}</th>
                            <th width="w50" class="text-center">Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key => $item)
                            <tr @if($item['user_status'] == -1)class="red bg-danger middle" {else} class="middle" @endif>
                                <td class="text-center middle">{{ $start+$key+1 }}</td>
                                <td>{{ $item['time_check_connect'] }}</td>
                                <td>{{ $item['concatenation_strings'] }}</td>
                                <td class="text-center middle">{{$item['created_date']}}</td>
                                <td class="text-center middle" align="center">
                                    @if($is_root || $permission_edit)
                                        <a href="{{URL::route('admin.systemSettingEdit',array('id' => FunctionLib::inputId($item['system_setting_id'])))}}" title="Sửa item"><i class="fa fa-pencil-square-o fa-2x"></i></a>
                                    @endif
                                    @if($is_boss || $permission_remove)
                                            <a href="javascript:void(0);" onclick="Admin.deleteItem({{$item['system_setting_id']}},13)" title="Xóa Item"><i class="fa fa-trash fa-2x"></i></a>
                                            <span class="img_loading" id="img_loading_{{$item['permission_id']}}"></span>
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
                        {{FunctionLib::viewLanguage('no_data')}}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop