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
            <li><a href="{{URL::route('admin.waittingSmsView')}}"> SMS Waitting Process</a></li>
            <li class="active">Edit sms</li>
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
                <div style="float: left; width: 35%">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="name" class="control-label alert-danger">{{FunctionLib::viewLanguage('perform_concatenate_strings_or_edit_each_sms')}}</label>
                            <label for="name" class="control-label">
                                <input name=”choose_type” type="radio" value=”type_input” checked />{{FunctionLib::viewLanguage('concatenation_strings')}}
                                &nbsp;&nbsp;&nbsp;<input name=”choose_type”  type="radio" value=”type_setting” />Chọn mặc đinh từ cài đặt
                            </label>
                            <textarea type="text" id="phone_number" name="phone_number"  class="form-control input-sm" rows="5">@if(isset($data['phone_number'])){{$data['phone_number']}}@endif</textarea>
                            <label for="name" class="control-label" style="font-size: 9px">Nhập các chuỗi ký tự ngăn cách nhau bởi dấu phẩy</label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label for="category_status">{{FunctionLib::viewLanguage('concatenation_rule')}}</label>
                        <select name="carrier_id" id="carrier_id" class="form-control input-sm">
                            {!!$optionDuplicateString!!}
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-sm-12 text-left marginTop10">
                        <a class="btn btn-warning" href="javascript:void(0);" onclick="window.history.back();"><i class="fa fa-reply"></i> {{FunctionLib::viewLanguage('back')}}</a>
                        <button  class="btn btn-primary"><i class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('Thực hiện ghép')}}</button>
                    </div>
                    <input type="hidden" id="id_hiden" name="id_hiden" value="{{$id}}"/>
                </div>
                <!--Danh sách các tin nhắn-->
                <div style="float: left; width: 65%">
                    <div class="span clearfix"> @if(count($data) >0) Có tổng số <b>{{count($data)}}</b> sms @endif </div>
                    <br>
                    <div class="table-responsive" style="width: 100%;min-height: 400px;max-height:500px;overflow-x: hidden;">
                        <table class="table table-bordered table-hover">
                            <thead class="thin-border-bottom">
                            <tr class="">
                                <th width="3%" class="text-center">TT</th>
                                <th width="14%" class="text-center">{{FunctionLib::viewLanguage('carrier')}}</th>
                                <th width="12%" class="text-center">{{FunctionLib::viewLanguage('phone_number')}}</th>
                                <th width="68%">{{FunctionLib::viewLanguage('sms_content')}}</th>
                                <th width="3%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $key => $item)
                                <tr>
                                    <td class="text-center text-middle">{!! $key+1 !!}</td>
                                    <td class="text-center text-middle">
                                        @if(isset($arrCarrier[$item['carrier_id']])){!! $arrCarrier[$item['carrier_id']] !!} @endif
                                    </td>
                                    <td class="text-center text-middle">{!! $item['phone_receive'] !!}</td>
                                    <td>{!! $item['content_grafted'] !!}</td>
                                    <td class="text-center text-middle">
                                        @if($is_root || $permission_full ==1|| $permission_edit ==1 || $permission_Send_full ==1|| $permission_Send_edit ==1  )
                                           <a href="{{URL::route('admin.smsEdit',array('id' => FunctionLib::inputId($item['sms_sendTo_id'])))}}" title="Sửa item"><i class="fa fa-edit fa-2x"></i></a>
                                        @endif
                                        <span class="img_loading" id="img_loading_{{$item['sms_log_id']}}"></span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div><!-- /.page-content -->
</div>
@stop
