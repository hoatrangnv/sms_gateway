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
        </ul>
    </div>
    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box-header">
                    <h3 class="box-title" style="text-align: center;">{{CGlobal::web_name}} </h3>
                </div>
                @if(isset($error) && !empty($error))
                    <div class="alert alert-danger" role="alert">
                        @foreach($error as $itmError)
                            <p><b>{{ $itmError }}</b></p>
                        @endforeach
                    </div>
                @endif
                <div class="box-body" style="margin-top: 35px">
                    @if(!empty($menu))
                        @foreach($menu as $item)
                            @if(isset($item['sub']) && !empty($item['sub']))
                                @foreach($item['sub'] as $sub)
                                    @if($is_boss || (!empty($aryPermissionMenu) && in_array($sub['menu_id'],$aryPermissionMenu) && $sub['show_menu'] == 1))
                                        @if(isset($sub['showcontent']) && $sub['showcontent'] == 1)
                                            <div class="col-sm-6 col-md-3">
                                                <a class="quick-btn a_control"  href="{{ URL::route($sub['RouteName']) }}">
                                                    <div class="thumbnail text-center">
                                                        <i class="{{ $sub['icon'] }} fa-5x"></i><br>{{ $sub['name'] }}
                                                    </div>
                                                </a>
                                            </div>
                                        @endif
                                        @if(isset($sub['clear']) && $sub['clear'] == 1)
                                            <div class="clear"></div>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                 </div>
            </div>
        </div>
    </div>
</div>
@stop