{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">--}}
{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">--}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
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
            <li class="active">{{FunctionLib::viewLanguage('sent_sms_history')}}</li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->
                <div class="panel panel-info">
                    <form method="" action="" role="form">
                        <div class="panel-body">
                            <div class="form-group col-lg-3">
                                <label for="user_id"><i>{{FunctionLib::viewLanguage('customer')}}</i></label>
                                <select name="user_id" id="user_id" class="form-control input-sm" tabindex="12" data-placeholder="">
                                    {!! $optionUser !!}
                                </select>
                            </div>
                            <div class="form-group col-lg-3">
                                <label for="status"><i>{{FunctionLib::viewLanguage('sending_status')}}</i></label>
                                <select name="status" id="status" class="form-control input-sm" tabindex="12" data-placeholder="">
                                    {!! $optionStatus !!}
                                </select>
                            </div>
                            <div class="form-group col-lg-3">
                                <label for="from_day"><i>{{FunctionLib::viewLanguage('from_day')}}</i></label>
                                <input type="text" class="form-control input-sm date-picker" id="from_day" name="from_day" @if(isset($dataSearch['from_day']))value="{{$dataSearch['from_day']}}"@endif>
                            </div>
                            <div class="form-group col-lg-3">
                                <label for="to_day"><i>{{FunctionLib::viewLanguage('to_day')}}</i></label>
                                <input type="text" class="form-control input-sm date-picker" name="to_day" autocomplete="off"  @if(isset($dataSearch['to_day']))value="{{$dataSearch['to_day']}}"@endif>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <span class="">
                            <button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-search"></i> Tìm kiếm</button>
                        </span>
                        </div>
                    </form>
                </div>
                </div>

                @if(sizeof($data) > 0)
                    <div class="span clearfix"> @if($size >0) {{FunctionLib::viewLanguage('total')}} <b>{{$size}}</b> {{FunctionLib::viewLanguage('results')}}  @endif </div>
                    <br>
                    <table class="table table-bordered">
                        <thead class="thin-border-bottom">
                        <tr class="">
                            <th class="w10" class="text-center">{{FunctionLib::viewLanguage('no')}}</th>
                            <th width="w50">{{FunctionLib::viewLanguage('customer_account')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('send_sms_deadline')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('total_sms')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('total_sent')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('incrorrect_numbers')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('total_cost_vnd')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('status')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('detail')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key => $item)
                            <tr @if($item['user_status'] == -1)class="red bg-danger middle" {else} class="middle" @endif>
                                <td class="text-center middle">{{ $start+$key+1 }}</td>
                                <td>{{ $item['user_customer_id'] }}</td>
                                <td>{{ $item['sms_deadline'] }}</td>
                                <td>{{ $item['correct_number'] + $item['incorrect_number'] }}</td>
                                <td>{{ $item['correct_number']}}</td>
                                <td>{{ $item['incorrect_number'] }}</td>
                                <td>{{ $item['cost'] }}</td>
                                <td>{{ $arrStatus[$item['status']]}}</td>
                                <td class="center">
                                    <a href="{{URL::route('admin.smsHistoryDetailsView',array('id_customer_sms' => FunctionLib::inputId($item['sms_customer_id'])))}}" title="Sửa item">
                                        <i class="fa fa-asterisk" aria-hidden="true"></i>
                                    </a>
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
<script>
    $(document).ready(function(){
        $(".date-picker").datepicker({
            format: "yyyy-mm-dd",
            language: "vi",
            autoclose: true,
            keyboardNavigation:true
        })});
</script>