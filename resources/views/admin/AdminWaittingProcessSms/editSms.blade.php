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
                @if(isset($error) && !empty($error))
                    <div class="alert alert-danger" role="alert">
                        @foreach($error as $itmError)
                            <p>{{ $itmError }}</p>
                        @endforeach
                    </div>
                @endif
                <div style="float: left; width: 35%">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="name" class="control-label alert-danger">{{FunctionLib::viewLanguage('perform_concatenate_strings_or_edit_each_sms')}} {{$choose_type}}</label>
                            <select name="choose_type" id="choose_type" class="form-control input-sm" onchange="onchangeRadio(this)">
                                {!!$optionChooseType!!}
                            </select>
                        </div>
                        <div class="form-group">
                            <div id="div_type_1" @if($choose_type == 2) style="display: none" @endif>
                                <textarea type="text" id="concatenation_strings_1" name="concatenation_strings_1"  class="form-control input-sm" rows="8">@if(isset($concatenation_strings_1)){{$concatenation_strings_1}}@endif</textarea>
                            </div>
                            <div id="div_type_2" @if($choose_type == 1) style="display: none" @endif>
                                <textarea type="text" id="concatenation_strings" name="concatenation_strings"  class="form-control input-sm" rows="8">@if(isset($concatenation_strings)){{$concatenation_strings}}@endif</textarea>
                            </div>
                            <label for="name" class="control-label" style="font-size: 9px">{{FunctionLib::viewLanguage('notice_1')}}</label>
                            <br>
                            <div id="div_type_3" @if($choose_type == 2) style="display: none" @endif>
                            <a href="#" class="btn btn-success btn-sm mg-t20" onclick="showModal(this)" element="#concatenation_strings_1" data-toggle="modal" ajax_url="/manager/systemSetting/importString" data-target="#modal-csv-upload">
                                <i class="fa fa-cloud"></i> {{FunctionLib::viewLanguage('import_excel')}}
                            </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label for="category_status">{{FunctionLib::viewLanguage('concatenation_rule')}}</label>
                        <select name="concatenation_rule" id="concatenation_rule" class="form-control input-sm">
                            {!!$optionDuplicateString!!}
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-sm-12 text-left marginTop10">
                        <a class="btn btn-warning" href="@if($type_page == 1){{URL::route('admin.waittingSmsView')}}@else {{URL::route('admin.waittingSendSmsView')}} @endif"><i class="fa fa-reply"></i> {{FunctionLib::viewLanguage('back')}}</a>
                        <button  class="btn btn-primary"><i class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('Thực hiện ghép')}}</button>
                    </div>
                    <input type="hidden" id="id_hiden" name="id_hiden" value="{{$id}}"/>
                    <input type="hidden" id="type_page" name="type_page" value="{{$type_page}}"/>

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
                                        @if($is_root || $permission_full ==1|| $permission_edit ==1  || $permission_Send_full ==1|| $permission_Send_edit ==1  )
                                            <a href="#" onclick="SmsAdmin.getContentGraftedSms('{{$item['sms_sendTo_id']}}')" title="Sửa item item"><i class="fa fa-edit fa-2x"></i></a>
                                        @endif
                                        <span class="img_loading" id="img_loading_{{$item['sms_log_id']}}"></span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{ csrf_field() }}
                {{ Form::close() }}
            </div>
        </div>
    </div><!-- /.page-content -->
</div>
<script>
    function onchangeRadio(event){
        //$('.radio2').click(function () {
            var choose_type  = $(event).val();
            if(parseInt(choose_type) == 1){
                $('#div_type_1').show();
                $('#div_type_3').show();
                $('#div_type_2').hide();
            }else{
                $('#div_type_2').show();
                $('#div_type_1').hide();
                $('#div_type_3').hide();
                SmsAdmin.getSettingContentAttach();
            }
        //});
    }
    $(document).ready(function() {
        /*var choose_type  = $(this).val();
        if(parseInt(choose_type) == 1){
            $('#div_type_1').show();
            $('#div_type_3').show();
            $('#div_type_2').hide();
        }else{
            $('#div_type_2').show();
            $('#div_type_1').hide();
            $('#div_type_3').hide();
            SmsAdmin.getSettingContentAttach();
        }*/
    });
</script>
<!--Popup anh khac de chen vao noi dung bai viet-->
<div class="modal fade" id="sys_showContentSms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Sửa nội dung send Sms</h4>
            </div>
            <img src="{{Config::get('config.WEB_ROOT')}}assets/admin/img/ajax-loader.gif" width="20" style="display: none" id="img_loading_district">
            <div class="modal-body" id="sys_show_infor">
                <textarea type="text" id="content_grafted" name="content_grafted"  class="form-control input-sm" rows="8"></textarea>
                <input type="hidden" id="sms_sendTo_id_popup" name="sms_sendTo_id_popup" value=""/>
                <div class="text-left marginTop10">
                    <a href="#" class="btn btn-primary" onclick="SmsAdmin.submitContentGraftedSms();"><i class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('save')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
