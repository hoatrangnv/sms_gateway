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
            <li><a href="{{URL::route('admin.modemView')}}">{{FunctionLib::viewLanguage('modem_stting')}}</a></li>
            <li class="active">@if($id > 0)Cập nhật menu @else Tạo mới menu @endif</li>
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
            <label for="modem_name" class="control-label no-padding-right col-sm-2">
                {{FunctionLib::viewLanguage('modem_name')}}
                <span class="badge badge-danger">{{FunctionLib::viewLanguage('required')}}</span>
            </label>
            <div class="col-sm-10">
                <input type="text" id="modem_name" name="modem_name"  class="form-control w30p" value="@if(isset($data['modem_name'])){{$data['modem_name']}}@endif">
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="user_id" class="control-label no-padding-right col-sm-2">
                {{FunctionLib::viewLanguage('acc')}}
                <span class="badge badge-danger">{{FunctionLib::viewLanguage('required')}}</span>
            </label>
            <div class="col-sm-10">
                <select name="user_id" id="user_id" class="w200 form-control input-sm">
                    {!! $optionUser !!}
                </select>
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="device_id" class="control-label no-padding-right col-sm-2">
                {{FunctionLib::viewLanguage('device_id')}}
                <span class="badge badge-danger">{{FunctionLib::viewLanguage('required')}}</span>
            </label>
            <div class="col-sm-10">
                <select name="device_id" id="device_id" class="w200 form-control input-sm">
                    {!! $optionDevice !!}
                </select>
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="digital" class="control-label no-padding-right col-sm-2">
                {{FunctionLib::viewLanguage('digital')}}
                <span class="badge badge-danger">{{FunctionLib::viewLanguage('required')}}</span>
            </label>
            <div class="col-sm-10">
                <input type="text" id="digital" name="digital"  class="form-control w30p" value="@if(isset($data['digital'])){{$data['digital']}}@endif">
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-right" for="form-field-2"></label>
            <div class="col-sm-10">
                <a class="btn btn-warning" href="{{URL::route('admin.modemView')}}"><i class="fa fa-reply"></i> {{FunctionLib::viewLanguage('back')}}</a>
                <button  class="btn btn-primary"><i class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('save')}}</button>
            </div>
        </div>

        {{--<div class="form-group col-sm-12 text-left">--}}
            {{--<a class="btn btn-warning" href="{{URL::route('admin.carrierSettingView')}}"><i class="fa fa-reply"></i> {{FunctionLib::viewLanguage('back')}}</a>--}}
            {{--<button  class="btn btn-primary"><i class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('save')}}</button>--}}
        {{--</div>--}}
        <input type="hidden" id="id_hiden" name="id_hiden" value="{{$id}}"/>
        {{ Form::close() }}
    </div><!-- /.page-content -->
</div>
@stop
