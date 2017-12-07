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
                @if(isset($error))
                    <div class="alert alert-danger" role="alert">
                        @foreach($error as $itmError)
                            <p>{{ $itmError }}</p>
                        @endforeach
                    </div>
                @endif
                <div style="float: left; width: 50%">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="name" class="control-label">{{FunctionLib::viewLanguage('phone_number')}}</label>
                            <textarea type="text" id="phone_number" name="phone_number"  class="form-control input-sm" rows="5">@if(isset($data['phone_number'])){{$data['phone_number']}}@endif</textarea>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="name" class="control-label">{{functionlib::viewlanguage('sms_content')}}</label>
                            <textarea type="text"id="sms_content" name="sms_content"  class="form-control input-sm" rows="5">@if(isset($data['sms_content'])){{$data['sms_content']}}@endif</textarea>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="control-label">{{FunctionLib::viewLanguage('send_sms_deadline')}}</label>
                            <input type="text" class="form-control" id="send_sms_deadline" name="send_sms_deadline"  data-date-format="dd-mm-yyyy" value="@if(isset($data['send_sms_deadline'])){{$data['send_sms_deadline']}}@endif">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-sm-12 text-left">
                        <a class="btn btn-warning" href="{{URL::route('admin.menuView')}}"><i class="fa fa-reply"></i> {{FunctionLib::viewLanguage('back')}}</a>
                        <button  class="btn btn-primary"><i class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('save')}}</button>
                    </div>
                    <input type="hidden" id="id_hiden" name="id_hiden" value="{{$id}}"/>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div><!-- /.page-content -->
</div>

<script>
    $(document).ready(function(){
        var checkin = $('#send_sms_deadline').datepicker({ });
    });
</script>
@stop
