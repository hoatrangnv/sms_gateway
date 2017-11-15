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
            <li><a href="{{URL::route('admin.menuView')}}"> Danh sách menu</a></li>
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
            <label for="carrier_name" class="control-label col-sm-2 no-padding-right">
                {{FunctionLib::viewLanguage('carrier_name')}}
                <span class="badge badge-danger">{{FunctionLib::viewLanguage('required')}}</span>
            </label>
            <div class="col-sm-10">
                <input type="text" id="carrier_name" name="carrier_name"  class="form-control w30p" value="@if(isset($data['carrier_name'])){{$data['carrier_name']}}@endif">
            </div>
        </div>

        <div class="clear"></div>
        <div class="form-group">
            <label for="slipt_number" class="control-label no-padding-right col-sm-2">
                {{FunctionLib::viewLanguage('slipt_number')}}
                <span class="badge badge-danger">{{FunctionLib::viewLanguage('required')}}</span>
            </label>
            <div class="col-sm-10">
                <input type="number" id="slipt_number" name="slipt_number"  class="form-control w30p" value="@if(isset($data['slipt_number'])){{$data['slipt_number']}}@endif">
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="min_number" class="control-label no-padding-right col-sm-2">
                {{FunctionLib::viewLanguage('min_number')}}
                <span class="badge badge-danger">{{FunctionLib::viewLanguage('required')}}</span>
            </label>
            <div class="col-sm-10">
                <input type="number" id="min_number" name="min_number"  class="form-control w30p" value="@if(isset($data['min_number'])){{$data['min_number']}}@endif">
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
            <label for="max_number" class="control-label no-padding-right col-sm-2">
                {{FunctionLib::viewLanguage('max_number')}}
                <span class="badge badge-danger">{{FunctionLib::viewLanguage('required')}}</span>
            </label>
            <div class="col-sm-10">
                <input type="number" id="max_number" name="max_number"  class="form-control w30p" value="@if(isset($data['max_number'])){{$data['max_number']}}@endif">
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group">
{{--            <label for="first_number" class="control-label col-sm-2">{{FunctionLib::viewLanguage('first_number')}}</label>--}}
            <label for="first_number" class="control-label no-padding-right col-sm-2">
                Đầu số hợp lệ
            </label>
            <div class="col-sm-10">
                <textarea class="form form-control h100 w50p" id="first_number" name="first_number">@if(isset($data['first_number'])){{$data['first_number']}}@endif</textarea>
            </div>
        </div>
        <div class="clear"></div>

        <div class="form-group">
            <label class="col-sm-2 control-label no-padding-right" for="form-field-2"></label>
            <div class="col-sm-10">
                <a class="btn btn-warning" href="{{URL::route('admin.carrierSettingView')}}"><i class="fa fa-reply"></i> {{FunctionLib::viewLanguage('back')}}</a>
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
