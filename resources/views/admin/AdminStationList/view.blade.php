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
                <li class="active">{{FunctionLib::viewLanguage('station_list')}}</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    @if($is_root || $permission_full ==1)
                    <div class="panel panel-info">
                        {{ Form::open(array('method' => 'GET', 'role'=>'form')) }}
                        <div class="panel-body">
                            <div class="col-sm-3">
                                <label for="station_account">{{FunctionLib::viewLanguage('station_account')}}</label>
                                <select name="station_account" id="station_account" class="w200 form-control input-sm">
                                    {!! $optionUser !!}
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <label for="email_address">{{FunctionLib::viewLanguage('email_address')}}</label>
                                <select name="email_address" id="email_address" class="w200 form-control input-sm">
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
                                            class="fa fa-search"></i> {{FunctionLib::viewLanguage('search')}}</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                    @endif
                    @if(sizeof($data) > 0)
                        <div class="span clearfix"> @if($size >0) {{FunctionLib::viewLanguage('total')}}
                            <b>{{$size}}</b> {{FunctionLib::viewLanguage('results')}}  @endif </div>
                        <br>
                        @foreach ($data as $key1 => $item1)
                            {{$key1}} ({{$item1['user_name_view']}})
                            <span class="label label-success">Đã thành SIM toàn modem</span>
                        <div class="space-4"></div>
                            <table class="table table-bordered">
                                <thead class="thin-border-bottom">
                                <tr class="">
                                    <th class="w10" class="text-center">{{FunctionLib::viewLanguage('no')}}</th>
                                    <th width="w50">{{FunctionLib::viewLanguage('com')}}</th>
                                    <th width="w100">{{FunctionLib::viewLanguage('carrier')}}</th>
                                    <th width="w100">{{FunctionLib::viewLanguage('imei_com')}}</th>
                                    <th width="w100">{{FunctionLib::viewLanguage('successful')}}</th>
                                    <th width="w100">{{FunctionLib::viewLanguage('failure')}}</th>
                                    <th width="w100">{{FunctionLib::viewLanguage('update')}}</th>
                                    <th width="w100">{{FunctionLib::viewLanguage('content')}}</th>
                                    <th width="w100">{{FunctionLib::viewLanguage('status')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($item1['list'] as $key => $item)
                                    <tr @if($item['user_status'] == -1)class="red bg-danger middle" {else}
                                        class="middle" @endif>
                                        <td class="text-center middle">{{$key+1 }}</td>
                                        <td>{{$item['modem_com_name']}}</td>
                                        <td>{{ $item['carrier_name'] }}</td>
                                        <td>{{ $item['mei_com'] }}</td>
                                        <td>{{ $item['success_number'] }}</td>
                                        <td>{{ $item['error_number'] }}</td>
                                        <td>{{ $item['updated_date'] }}</td>
                                        <td class="center">{{ $item['content'] }}</td>
                                        <td class="center">
                                            @if($item['is_active']=='1')
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
                        @endforeach
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