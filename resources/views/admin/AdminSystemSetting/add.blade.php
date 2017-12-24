<?php use App\Library\AdminFunction\FunctionLib; ?>
<?php use App\Library\AdminFunction\Define; ?>
@extends('admin.AdminLayouts.index')
{{--@include ('admin.CommonTemplate.modal_excel')--}}
@section('content')
<div class="main-content-inner">
    <div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{URL::route('admin.dashboard')}}">{{FunctionLib::viewLanguage('home')}}</a>
            </li>
            <li><a href="{{URL::route('admin.menuView')}}">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('system_management')}}</a></li>
            <li class="active">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('system_setting')}}</li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <!-- PAGE CONTENT BEGINS -->
        {{Form::open(array('method' => 'POST','role'=>'form','files' => true))}}
        @if(isset($error) && !empty($error))
            <div class="alert alert-danger" role="alert">
                @foreach($error as $itmError)
                    <p>{{ $itmError }}</p>
                @endforeach
            </div>
        @endif
        <div class="form-group">
            <label for="time_check_connect" class="control-label col-sm-2">{{FunctionLib::viewLanguage('time_check_connect')}}<span class="red"> (*)</span></label>
            <div class="col-sm-10">
                <input type="number" id="time_check_connect" name="time_check_connect"  class="form-control input-sm" value="@if(isset($data['time_check_connect'])){{$data['time_check_connect']}}@endif">
            </div>
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
                {{--<button  class="btn btn-success"><i class="fa fa-cloud"></i> {{FunctionLib::viewLanguage('import_excel')}}</button>--}}
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
            {{--<a class="btn btn-warning" href="{{URL::route('admin.systemSettingView')}}"><i class="fa fa-reply"></i> {{FunctionLib::viewLanguage('back')}}</a>--}}
            <button  class="btn btn-primary"><i class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('save')}}</button>
        </div>
        <input type="hidden" id="id_hiden" name="id_hiden" value="{{$id}}"/>
        {{ Form::close() }}
    </div><!-- /.page-content -->
</div>
@stop
