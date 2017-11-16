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
            <li><a href="{{URL::route('admin.deviceTokenView')}}">{{FunctionLib::viewLanguage('device_stting')}}</a></li>
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
            <label for="device_code" class="control-label no-padding-right col-sm-2">
                {{FunctionLib::viewLanguage('device_code')}}
                <span class="badge badge-danger">{{FunctionLib::viewLanguage('required')}}</span>
            </label>
            <div class="col-sm-10">
                <input type="text" id="device_code" name="device_code"  class="form-control w30p" value="@if(isset($data['device_code'])){{$data['device_code']}}@endif">
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="min_number" class="control-label no-padding-right col-sm-2">
                {{FunctionLib::viewLanguage('token')}}
                <span class="badge badge-danger">{{FunctionLib::viewLanguage('required')}}</span>
            </label>
            <div class="col-sm-10">
                <input type="text" id="token" name="token"  class="form-control w30p" value="@if(isset($data['token'])){{$data['token']}}@endif">
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="messeger_center" class="control-label no-padding-right col-sm-2">
                {{FunctionLib::viewLanguage('messeger_center')}}
                <span class="badge badge-danger">{{FunctionLib::viewLanguage('required')}}</span>
            </label>
            <div class="col-sm-10">
                <input type="text" id="messeger_center" name="messeger_center"  class="form-control w30p" value="@if(isset($data['messeger_center'])){{$data['messeger_center']}}@endif">
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="status" class="control-label col-sm-2">{{FunctionLib::viewLanguage('status')}}</label>
            <div class="col-sm-10">
                <select name="status" id="status" class="w200 form-control input-sm">
                    {!! $optionStatus !!}
                </select>
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-right" for="form-field-2"></label>
            <div class="col-sm-10">
                <a class="btn btn-warning" href="{{URL::route('admin.deviceTokenView')}}"><i class="fa fa-reply"></i> {{FunctionLib::viewLanguage('back')}}</a>
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
