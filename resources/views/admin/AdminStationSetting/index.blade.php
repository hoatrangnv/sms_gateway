<?php use App\Library\AdminFunction\CGlobal; ?>
<?php use App\Library\AdminFunction\Define; ?>
<?php use App\Library\AdminFunction\FunctionLib; ?>
@extends('admin.AdminLayouts.index')
@section('content')
<div class="main-content-inner">
    <div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                {{FunctionLib::viewLanguage('home')}}
            </li>
            <li>
                {{FunctionLib::viewLanguage('station_management')}}
            </li>
            <li>
                {{FunctionLib::viewLanguage('station_setting')}}
            </li>
        </ul>
    </div>
    <div class="page-content">
        <!-- PAGE CONTENT BEGINS -->
        {{Form::open(array('method' => 'POST','role'=>'form','files' => true))}}
        <input type="hidden" id="" name="sms_max_hd"  class="form-control input-sm" value="@if(isset($data['sms_max'])){{$data['sms_max']}}@endif">
        <input type="hidden" id="" name="sms_error_max_hd"  class="form-control input-sm" value="@if(isset($data['sms_error_max'])){{$data['sms_error_max']}}@endif">
        <input type="hidden" id="" name="time_delay_from_hd"  class="form-control input-sm" value="@if(isset($data['time_delay_from'])){{$data['time_delay_from']}}@endif">
        <input type="hidden" id="" name="time_delay_to_hd"  class="form-control input-sm" value="@if(isset($data['time_delay_to'])){{$data['time_delay_to']}}@endif">
        @if(isset($error) && !empty($error))
            <div class="alert alert-danger" role="alert">
                @foreach($error as $itmError)
                    <p>{{ $itmError }}</p>
                @endforeach
            </div>
        @endif
        <div class="form-group">
            <label for="sms_max" class="control-label col-sm-2">{{FunctionLib::viewLanguage('number_of_loop_max_each_com')}}</label>
            <div class="col-sm-2">
                <input type="number" id="sms_max" name="sms_max"  class="form-control input-sm" value="@if(isset($data['sms_max'])){{$data['sms_max']}}@endif">
            </div>
            <span style="font-style: italic; margin-top: 5px">({{FunctionLib::viewLanguage('in_one_day')}})</span>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="sms_error_max" class="control-label col-sm-2">{{FunctionLib::viewLanguage('number_of_sent_error_each_com')}}</label>
            <div class="col-sm-2">
                <input type="number" id="sms_error_max" name="sms_error_max"  class="form-control input-sm" value="@if(isset($data['sms_error_max'])){{$data['sms_error_max']}}@endif">
            </div>
            <span style="font-style: italic; margin-top: 5px">({{FunctionLib::viewLanguage('in_one_connected')}})</span>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="time_delay_from" class="control-label col-sm-2">{{FunctionLib::viewLanguage('delay_between_two_times')}}</label>
            <div class="col-sm-2">
                <input type="number" id="time_delay_from" name="time_delay_from"  class="form-control input-sm" value="@if(isset($data['time_delay_from'])){{$data['time_delay_from']}}@endif">
            </div>
            <label for="time_delay_from" class="control-label col-sm-1">{{FunctionLib::viewLanguage('to')}}</label>
            <div class="col-sm-2">
                <input type="number" id="time_delay_to" name="time_delay_to"  class="form-control input-sm" value="@if(isset($data['time_delay_to'])){{$data['time_delay_to']}}@endif">
            </div>
            <span style="font-style: italic; margin-top: 5px">({{FunctionLib::viewLanguage('seconds')}})</span>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="concatenation_strings" class="control-label col-sm-2">{{FunctionLib::viewLanguage('concatenation_strings')}}</label>
            <div class="col-sm-10">
                <textarea class="form form-control h100" id="concatenation_strings" name="concatenation_strings">@if(isset($data['concatenation_strings'])){{$data['concatenation_strings']}}@endif</textarea>
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="concatenation_strings" class="control-label col-sm-2"></label>
            <div class="col-sm-10">
                <a href="#" class="btn btn-success btn-sm mg-t20" onclick="showModal(this)" element="#concatenation_strings" data-toggle="modal" ajax_url="/manager/systemSetting/importString" data-target="#modal-csv-upload">
                    <i class="fa fa-cloud"></i> {{FunctionLib::viewLanguage('import_excel')}}
                </a>
                <span style="float: right; font-style: italic; margin-top: 5px">{{FunctionLib::viewLanguage('notice_1')}}</span>
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="concatenation_rule" class="control-label col-sm-2">{{FunctionLib::viewLanguage('concatenation_rule')}}</label>
            <div class="col-sm-10">
                <select name="concatenation_rule" id="concatenation_rule" class="w200 form-control input-sm">
                    {!! $optionRuleString !!}
                </select>
            </div>
        </div>

        <div class="clear"></div>
        <div class="form-group col-sm-12 text-left">
{{--            <a class="btn btn-warning" href="{{URL::route('admin.systemSettingView')}}"><i class="fa fa-reply"></i> {{FunctionLib::viewLanguage('back')}}</a>--}}
            <button  class="btn btn-primary"><i class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('save')}}</button>
        </div>
        <input type="hidden" id="id_hiden" name="id_hiden" value="{{$id}}"/>
        <input type="hidden" id="user_id" name="user_id" value="{{$admin_id}}"/>
        {{ Form::close() }}
    </div><!-- /.page-content -->
    </div>
</div>
@stop