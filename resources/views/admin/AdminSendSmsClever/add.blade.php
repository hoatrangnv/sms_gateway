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
                <li class="active">{{FunctionLib::viewLanguage('send_sms')}}</li>
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
                    <div style="float: left; width: 100%">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input type="file" id="csv_file" style="display: none;" name="file_excel_sms_clever"
                                       accept="text/csv">
                                <button type="button" class="btn btn-warning" onClick="$('#csv_file').click();"><i
                                            class="fa fa-cloud-upload"></i>{{FunctionLib::viewLanguage('csv_upload')}}
                                </button>
                                <button class="btn btn-primary" type="submit" name="submit" value="1"><i
                                            class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('Inport Excel')}}
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name"
                                       class="control-label">{{FunctionLib::viewLanguage('send_sms_deadline')}}</label>
                                <input type="text" class="form-control" id="send_sms_deadline" name="send_sms_deadline"
                                       data-date-format="dd-mm-yyyy"
                                       value="@if(isset($data['send_sms_deadline'])){{$data['send_sms_deadline']}}@endif">
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        @if(isset($dataSendClever) && count($dataSendClever) > 0)
                            <div class="col-xs-12">
                                <div class="span clearfix"> @if($totalClever >0) Có tổng số <b>{{$totalClever}}</b>
                                    item @endif </div>
                                <br>
                                <div class="col-xs-12" style="height: 300px; overflow-x: hidden;">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="thin-border-bottom">
                                            <tr class="">
                                                <th width="5%" class="text-center">STT</th>
                                                <th width="15%"
                                                    class="text-center">{{FunctionLib::viewLanguage('carrier')}}</th>
                                                <th width="15%"
                                                    class="text-center">{{FunctionLib::viewLanguage('phone_number')}}</th>
                                                <th width="55%">{{FunctionLib::viewLanguage('sms_content')}}</th>
                                                <th width="10%"
                                                    class="text-center">{{FunctionLib::viewLanguage('action')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($dataSendClever as $key => $item)
                                                <tr>
                                                    <td class="text-center text-middle">{{$key+1}}</td>
                                                    <td class="text-center text-middle">{{$item['carrier_name']}}</td>
                                                    <td class="text-center text-middle">{{$item['phone_receive']}}</td>
                                                    <td>{{$item['content']}}</td>
                                                    <td class="text-center text-middle">
                                                        @if($is_root || $permission_full ==1|| $permission_edit ==1  )
                                                            @if($is_root || $permission_full ==1|| $permission_edit ==1  )
                                                                <a href="#"
                                                                   onclick="SmsAdmin.getContentSmsClever('{{$item['sms_clever_id']}}')"
                                                                   title="Sửa item item"><i
                                                                            class="fa fa-edit fa-2x"></i></a>
                                                            @endif
                                                            <span class="img_loading"
                                                                  id="img_loading_{{$item['sms_clever_id']}}"></span>
                                                            @if($is_root || $permission_full ==1|| $permission_edit ==1  )
                                                                <a href="javascript:void(0)" class="sys_delete_user"
                                                                   data-content="Xóa Sms" data-placement="bottom"
                                                                   data-trigger="hover" data-rel="popover"
                                                                   data-url="sendSmsClever/remove/"
                                                                   data-id="{{FunctionLib::inputId($item['sms_clever_id'])}}">
                                                                    <i class="fa fa-trash fa-2x"></i>
                                                                </a>
                                                            @endif
                                                            {{--<a href="javascript:void(0);" onclick="SmsAdmin.changeUserWaittingProcessSms()" title="Chuyển đổi"><i class="fa fa-sign-in fa-2x"></i></a>
                                                            <a href="{{URL::route('admin.waittingSmsEdit',array('id' => FunctionLib::inputId(0),'type_page'=>1))}}" title="Sửa item"><i class="fa fa-edit fa-2x"></i></a>--}}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="clearfix"></div>
                        <div class="form-group col-sm-12 text-left">
                            @if($key_action > 0)
                                <button class="btn btn-danger" type="submit" name="submit" value="2"><i
                                            class="fa fa-file-excel-o"></i> {{FunctionLib::viewLanguage('export_data')}}
                                </button>
                                <button class="btn btn-primary" type="submit" name="submit" value="3"><i
                                            class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('send_sms')}}
                                </button>
                            @endif
                        </div>
                        <input type="hidden" id="id_hiden" name="id_hiden" value="{{$id}}"/>
                        <input type="hidden" id="key_action" name="key_action" value="{{$key_action}}"/>
                    </div>
                    {{ csrf_field() }}
                    {{ Form::close() }}
                </div>
            </div>
        </div><!-- /.page-content -->
    </div>

    <script>
        $(document).ready(function () {
            var checkin = $('#send_sms_deadline').datepicker({});
        });
    </script>
    <!--Popup anh khac de chen vao noi dung bai viet-->
    <div class="modal fade" id="sys_showContentSms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Sửa nội dung send Sms</h4>
                </div>
                <img src="{{Config::get('config.WEB_ROOT')}}assets/admin/img/ajax-loader.gif" width="20"
                     style="display: none" id="img_loading_district">
                <div class="modal-body" id="sys_show_infor">
                    <textarea type="text" id="content_clever" name="content_clever" class="form-control input-sm"
                              rows="8"></textarea>
                    <input type="hidden" id="sms_clever_id_popup" name="sms_clever_id_popup" value=""/>
                    <div class="text-left marginTop10">
                        <a href="#" class="btn btn-primary" onclick="SmsAdmin.submitContentSmsClever();"><i
                                    class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('save')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
