<?php use App\Library\AdminFunction\CGlobal; ?>
<?php use App\Library\AdminFunction\Define; ?>
<?php use App\Library\AdminFunction\FunctionLib; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>{!! CGlobal::$pageAdminTitle !!}</title>

    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <!-- bootstrap & fontawesome -->
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/css/bootstrap.min.css')}}" />
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/lib/font-awesome/4.2.0/css/font-awesome.min.css')}}" />

    <!-- page specific plugin styles -->

    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/css/jquery-ui.min.css')}}" />
    <!-- text fonts -->
    {{--<link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/fonts/fonts.googleapis.com.css')}}" />--}}

    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/css/chosen.min.css')}}" />
    <!-- ace styles -->
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/css/ace.min.css')}}" />
    <!--[if lte IE 9]>
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/css/ace-part2.min.css')}}" />
    <![endif]-->

    <!--[if lte IE 9]>
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/css/ace-ie.min.css')}}" />
    <![endif]-->

    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/admin/css/admin_css.css')}}" />
    <!-- inline styles related to this page -->

    <!-- ace settings handler -->
    <script src="{{URL::asset('assets/js/ace-extra.min.js')}}"></script>

    <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/lib/datetimepicker/datetimepicker.css')}}" />

    <!--[if lte IE 8]>
    <script src="{{URL::asset('assets/js/html5shiv.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/respond.min.js')}}"></script>
    <![endif]-->
    <script type="text/javascript">
       var WEB_ROOT = "{{URL::to('/')}}";
    </script>
    <!-- basic scripts -->

    <!--[if !IE]> -->
    <script src="{{URL::asset('assets/js/jquery.2.1.1.min.js')}}"></script>
    <!--[if IE]>
    <script src="{{URL::asset('assets/js/jquery.1.11.1.min.js')}}"></script>
    <![endif]-->

    <script src="{{URL::asset('assets/js/bootstrap.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/chosen.jquery.js')}}"></script>
    <script src="{{URL::asset('assets/js/jquery-ui.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/jquery.ui.touch-punch.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/ace.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/ace-elements.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/moment.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/bootbox.min.js')}}"></script>
    <script src="{{URL::asset('assets/admin/js/admin.js')}}"></script>
    <script src="{{URL::asset('assets/admin/js/format.js')}}"></script>
    <script src="{{URL::asset('assets/lib/datetimepicker/jquery.datetimepicker.js')}}"></script>

    {!!CGlobal::$extraHeaderCSS!!}
    {!!CGlobal::$extraHeaderJS!!}

    <script type="text/javascript" src="../../../../assets/lib/ckeditor/ckeditor.js"></script>
</head>

<body class="no-skin" @if($languageSite == \App\Library\AdminFunction\Define::VIETNAM_LANGUAGE) lang="vi" @else lang="en" @endif>
<div id="navbar" class="navbar navbar-default navbar-fixed-top">
    <div class="navbar-container" id="navbar-container">
        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
            <span class="sr-only">Toggle sidebar</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div class="navbar-header pull-left">
            <a href="#" class="navbar-brand">
                <small>
                    <i class="fa fa-leaf"></i>
                    {{CGlobal::web_name}}
                </small>
            </a>
        </div>
        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">
                <li class="light-blue">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        @if(isset($languageSite) && $languageSite == Define::VIETNAM_LANGUAGE)
                            <img src="{{Config::get('config.WEB_ROOT')}}assets/admin/img/icon/vi.png"/>
                            Viet Nam
                        @else
                            <img src="{{Config::get('config.WEB_ROOT')}}assets/admin/img/icon/en.png"/>
                            English
                        @endif
                    </a>
                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        @if(isset($languageSite) && $languageSite == Define::ENGLISH_LANGUAGE)
                            <li>
                                <a href="{{URL::route('admin.dashboard',array('lang'=>Define::VIETNAM_LANGUAGE))}}">
                                    <img src="{{Config::get('config.WEB_ROOT')}}assets/admin/img/icon/vi.png"/>
                                    Viet Nam
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{URL::route('admin.dashboard',array('lang'=>Define::ENGLISH_LANGUAGE))}}">
                                    <img src="{{Config::get('config.WEB_ROOT')}}assets/admin/img/icon/en.png"/>
                                    English
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                <li class="light-blue">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <span class="user-info">
                            <small>{{FunctionLib::viewLanguage('hello')}},</small>
                            @if(isset($user))
                                {{$user['user_name']}}
                            @endif
                        </span>
                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>
                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li>
                            <a href="{{URL::route('admin.user_change',array('id' => FunctionLib::inputId($user['user_id'])))}}">
                                <i class="ace-icon fa fa-unlock"></i>
                                {{FunctionLib::viewLanguage('changePass')}}
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="{{URL::route('admin.logout')}}">
                                <i class="ace-icon fa fa-power-off"></i>
                                {{FunctionLib::viewLanguage('logout')}}
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="main-container" id="main-container">
    <div id="sidebar" class="sidebar sidebar-fixed sidebar-scroll responsive">
        <div class="sidebar-shortcuts" id="sidebar-shortcuts">
            <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                <a href="{{URL::route('admin.dashboard')}}" title="CMS Admin"><img width="100%" src="{{Config::get('config.WEB_ROOT')}}assets/frontend/img/logoCustomer.png" alt="CMS Admin" /></a>
            </div>
            <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
                <span class="btn btn-success"></span>
                <span class="btn btn-info"></span>
                <span class="btn btn-warning"></span>
                <span class="btn btn-danger"></span>
            </div>
        </div>
        <ul class="nav nav-list">
            @if(!empty($menu))
                @foreach($menu as $item)
                    @if($is_boss || $item['show_menu'] == CGlobal::status_show)
                    <li class="@if(!empty($item['arr_link_sub']) && in_array(Route::currentRouteName(),$item['arr_link_sub']))active @endif">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon {{$item['icon']}}"></i>
                            <span class="menu-text">
                                @if(isset($languageSite) && $languageSite == Define::VIETNAM_LANGUAGE)
                                    {{ $item['name'] }}
                                @else
                                    {{ $item['name_en'] }}
                                @endif
                            </span>
                            <b class="arrow fa fa-angle-down"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            @if(isset($item['sub']) && !empty($item['sub']))
                                @foreach($item['sub'] as $sub)
                                    @if($is_boss || (!empty($aryPermissionMenu) && in_array($sub['menu_id'],$aryPermissionMenu)))
                                        <li class="@if(strcmp(Route::currentRouteName(),$sub['RouteName']) == 0) active @endif">
                                            <a href="{{URL::route($sub['RouteName'])}}">
                                                <i class="menu-icon fa fa-caret-right"></i>
                                                @if(isset($languageSite) && $languageSite == Define::VIETNAM_LANGUAGE)
                                                    {{ $sub['name'] }}
                                                @else
                                                    {{ $sub['name_en'] }}
                                                @endif
                                            </a>
                                            <b class="arrow"></b>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </li>
                    @endif
                @endforeach
            @endif
        </ul>
        <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
            <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
        </div>
        <script type="text/javascript">
            try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
        </script>
        {!! csrf_field() !!}
    </div>

    <div class="main-content">
        @yield('content')
    </div>
    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-info">
        <i class="ace-icon fa fa-angle-double-up icon-only bigger-300"></i>
    </a>
</div>
{!!CGlobal::$extraFooterCSS!!}
{!!CGlobal::$extraFooterJS!!}
</body>
</html>
