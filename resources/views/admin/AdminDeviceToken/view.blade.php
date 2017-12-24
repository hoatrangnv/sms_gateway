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
                <li class="active">{{FunctionLib::viewLanguage('station_management')}}</li>
                <li class="active">{{FunctionLib::viewLanguage('device_stting')}}</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <div class="panel panel-info">
                        {{ Form::open(array('method' => 'GET', 'role'=>'form')) }}
                        <div class="panel-body">
                            <div class="col-sm-10">
                                <label for="carrier_name">{{FunctionLib::viewLanguage('station_account')}}</label>
                                <select name="user_id" id="user_id" class="w200 form-control input-sm">
                                    {!! $optionUser !!}
                                </select>
                            </div>
                            <div class="form-group col-lg-12 text-right">
                                {{--@if($is_root || $permission_full ==1 || $permission_create == 1)--}}
                                {{--<a class="btn btn-danger btn-sm" href="{{URL::route('admin.deviceTokenEdit',array('id' => FunctionLib::inputId(0)))}}">--}}
                                {{--<i class="ace-icon fa fa-plus-circle"></i>--}}
                                {{--{{FunctionLib::viewLanguage('add')}}--}}
                                {{--</a>--}}
                                {{--@endif--}}
                                {{--<button class="btn btn-warning btn-sm" type="submit" name="submit" value="2"><i class="fa fa-file-excel-o"></i> Xuất Excel</button>--}}
                                <button class="btn btn-primary btn-sm" type="submit" name="submit" value="1"><i
                                            class="fa fa-search"></i> {{FunctionLib::viewLanguage('search')}}
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                    @if(sizeof($data) > 0)
                        <div class="span clearfix"> @if($size >0) {{FunctionLib::viewLanguage('total')}}
                            <b>{{$size}}</b> {{FunctionLib::viewLanguage('results')}}  @endif </div>
                        <br>
                        <table class="table table-bordered">
                            <thead class="thin-border-bottom">
                            <tr class="">
                                <th class="w10" class="text-center">{{FunctionLib::viewLanguage('no')}}</th>
                                <th width="w50">{{FunctionLib::viewLanguage('acc')}}</th>
                                <th width="w100">{{FunctionLib::viewLanguage('device_code')}}</th>
                                <th width="w100">{{FunctionLib::viewLanguage('token')}}</th>
                                <th width="w100">{{FunctionLib::viewLanguage('modem_type')}}</th>
                                <th width="w100">{{FunctionLib::viewLanguage('status')}}</th>
                                {{--<th width="w50" class="text-center">Thao tác</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $key => $item)
                                <tr @if($item['user_status'] == -1)class="red bg-danger middle" {else}
                                    class="middle" @endif>
                                    <td class="text-center middle">{{ $start+$key+1 }}</td>
                                    <td>@if(in_array($item['user_id'],$arrUser)){{ $arrUser[$item['user_id']] }}@endif</td>
                                    <td>{{ $item['device_code'] }}</td>
                                    <td>{{ $item['token'] }}</td>
                                    <td>{{ $item['modem_type'] }}</td>
                                    <td>
                                        @if($item['status']=='1')
                                            <i class="fa fa-toggle-on fa-2x green" aria-hidden="true"></i>
                                        @else
                                            <i class="fa fa-toggle-off fa-2x red" aria-hidden="true"></i>
                                        @endif
                                    </td>
                                    {{--<td class="text-center middle" align="center">--}}
                                    {{--@if($is_root || $permission_edit)--}}
                                    {{--<a href="{{URL::route('admin.deviceTokenEdit',array('id' => FunctionLib::inputId($item['device_token_id'])))}}" title="Sửa item"><i class="fa fa-pencil-square-o fa-2x"></i></a>--}}
                                    {{--@endif--}}
                                    {{--@if($is_boss || $permission_remove)--}}
                                    {{--<a href="javascript:void(0);" onclick="Admin.deleteItem({{$item['device_token_id']}},15)" title="Xóa Item"><i class="fa fa-trash fa-2x"></i></a>--}}
                                    {{--<span class="img_loading" id="img_loading_{{$item['permission_id']}}"></span>--}}
                                    {{--@endif--}}
                                    {{--</td>--}}
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