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
                {{FunctionLib::viewLanguage('api_document')}}
            </li>
            <li>
                {{FunctionLib::viewLanguage('customer_api')}}
            </li>
        </ul>
    </div>
    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                @if($user_role_type== \App\Library\AdminFunction\Define::ROLE_TYPE_SUPER_ADMIN)
                    <a href="{{URL::route('admin.customer_api_edit',['id'=>FunctionLib::inputId($id)])}}" class=""><i class="fa fa-pencil-square-o" aria-hidden="true"></i>{{FunctionLib::viewLanguage('edit')}}</a>
                @endif

                <div>
                    @if(isset($data['content'])){!! $data['content'] !!}@endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop