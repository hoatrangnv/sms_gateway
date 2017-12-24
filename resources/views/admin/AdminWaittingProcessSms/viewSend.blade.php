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
                <li class="active">SMS Waitting Send</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <div class="panel panel-info">
                        {{ Form::open(array('method' => 'POST', 'role'=>'form')) }}
                        <div class="panel-body">
                            @if($user_role_type==\App\Library\AdminFunction\Define::ROLE_TYPE_SUPER_ADMIN)
                                <div class="form-group col-lg-3">
                                    <label for="banner_name">{{FunctionLib::viewLanguage('station_account')}}</label>
                                    <select name="user_customer_id" id="user_customer_id" class="form-control input-sm">
                                        {!!$optionListUser!!}
                                    </select>
                                </div>
                            @endif
                            <div class="form-group col-lg-3">
                                <label for="category_status">{{FunctionLib::viewLanguage('carrier')}}</label>
                                <select name="carrier_id" id="carrier_id" class="form-control input-sm">
                                    {!!$optionCarrier!!}
                                </select>
                            </div>

                            <div class="form-group col-lg-3">
                                <label for="banner_name">{{FunctionLib::viewLanguage('from_date')}}</label>
                                <input type="text" class="form-control" id="from_date" name="from_date"  data-date-format="dd-mm-yyyy" value="@if(isset($data['from_date'])){{$data['from_date']}}@endif">
                            </div>
                            <div class="form-group col-lg-3">
                                <label for="banner_name">{{FunctionLib::viewLanguage('to_date')}}</label>
                                <input type="text" class="form-control" id="to_date" name="to_date"  data-date-format="dd-mm-yyyy" value="@if(isset($data['to_date'])){{$data['to_date']}}@endif">
                            </div>

                            <div class="form-group col-lg-12 text-right">
                                {{--<button class="btn btn-warning btn-sm" type="submit" name="submit" value="2"><i class="fa fa-file-excel-o"></i> Xuất Excel</button>--}}
                                <button class="btn btn-primary btn-sm" type="submit" name="submit" value="1"><i class="fa fa-search"></i> {{FunctionLib::viewLanguage('search')}}</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                    @if($data && sizeof($data) > 0)
                        <div class="span clearfix"> @if($total >0) Có tổng số <b>{{$total}}</b> item @endif </div>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thin-border-bottom">
                                <tr class="">
                                    <th width="3%" class="text-center">TT</th>
                                    @if($user_role_type==\App\Library\AdminFunction\Define::ROLE_TYPE_SUPER_ADMIN)
                                        <th width="22%">{{FunctionLib::viewLanguage('station_account')}}</th>
                                    @endif
                                    <th width="10%" class="text-center">{{FunctionLib::viewLanguage('carrier')}}</th>
                                    <th width="10%" class="text-center">{{FunctionLib::viewLanguage('total_number_of_sms')}}</th>
                                    <th width="10%" class="text-center">{{FunctionLib::viewLanguage('send_sms_deadline')}}</th>
                                    <th width="30%" class="text-center">
                                        {{FunctionLib::viewLanguage('choose_processing_modem')}}
                                    </th>
                                    <th width="15%">
                                        {{FunctionLib::viewLanguage('action')}}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data as $key => $item)
                                    <tr>
                                        <td class="text-center text-middle">{!! $stt + $key+1 !!}</td>
                                        @if($user_role_type==\App\Library\AdminFunction\Define::ROLE_TYPE_SUPER_ADMIN)
                                            <td>@if(isset($infoListUser[$item['user_customer_id']])){!! $infoListUser[$item['user_customer_id']] !!} @endif</td>
                                        @endif
                                        <td class="text-center text-middle">{!! $item['carrier_name'] !!}</td>
                                        <td class="text-center text-middle">{!! $item['total_sms'] !!}</td>
                                        <td class="text-center text-middle"></td>
                                        <td class="text-center text-middle">
                                            <?php $optionListModem = FunctionLib::getOption(array(-1=>'')+$infoListModem, $item['list_modem'])?>
                                            <select name="list_modem_{{$item['sms_log_id']}}" id="list_modem_{{$item['sms_log_id']}}" class="form-control input-sm">
                                                {!!$optionListModem!!}
                                            </select>
                                            <span class="img_loading" id="img_loading_{{$item['sms_log_id']}}"></span>
                                        </td>
                                        <td class="text-center text-middle">
                                            @if($is_root || $permission_full ==1|| $permission_edit ==1  )
                                                @if($item['status'] == \App\Library\AdminFunction\Define::SMS_STATUS_PROCESSING && $item['list_modem'] == ''
                                                || $item['status'] == \App\Library\AdminFunction\Define::SMS_STATUS_REJECT && $item['list_modem'] != '')
                                                    <a href="javascript:void(0);" onclick="SmsAdmin.changeModemWaittingSendSms({{$item['sms_log_id']}},{{$item['total_sms']}})" title="Chuyển đổi"><i class="fa fa-sign-in fa-2x"></i></a>&nbsp;&nbsp;&nbsp;
                                                    <a href="javascript:void(0);" onclick="SmsAdmin.refuseModem({{$item['sms_log_id']}})" title="Từ chối: Trả lại gói tin cho supper admin"><i class="fa fa-undo fa-2x"></i></a>&nbsp;&nbsp;&nbsp;
                                                    <a href="{{URL::route('admin.waittingSmsEdit',array('id' => FunctionLib::inputId($item['sms_log_id']),'type_page'=>2))}}" title="Sửa item"><i class="fa fa-edit fa-2x"></i></a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right">
                            {!! $paging!!}
                        </div>
                    @else
                        <div class="alert">
                            Không có dữ liệu
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            var from_date = $('#from_date').datepicker({ });
            var to_date = $('#to_date').datepicker({ });
        });
    </script>
@stop