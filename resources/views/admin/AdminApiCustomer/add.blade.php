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
            <li>
                {{FunctionLib::viewLanguage('api_document')}}
            </li>
            <li>
                <a href="{{URL::route('admin.customerAPIView')}}">{{FunctionLib::viewLanguage('customer_api')}}</a>
            </li>
            <li>
                {{FunctionLib::viewLanguage('edit_api')}}
            </li>
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
            <label for="concatenation_strings" class="control-label col-sm-2"></label>
            <div class="col-sm-12">
                <textarea class="form form-control h100 ckeditor" rows="10" id="editorConfig " @if($lang==1)name="api_customer"@else name="api_customer_en" @endif>
                    @if(isset($data['content'])){{$data['content']}}@endif
                </textarea>
            </div>
        </div>
        <div class="clear"></div>
        <div class="form-group col-sm-12 text-left">
            <a class="btn btn-warning" href="{{URL::route('admin.customerAPIView')}}"><i class="fa fa-reply"></i> {{FunctionLib::viewLanguage('back')}}</a>
            <button  class="btn btn-primary"><i class="fa fa-floppy-o"></i> {{FunctionLib::viewLanguage('save')}}</button>
        </div>
        <input type="hidden" id="id_hiden" name="id_hiden" value="{{FunctionLib::inputId($id)}}"/>
        {{ Form::close() }}
    </div><!-- /.page-content -->
</div>
@stop

