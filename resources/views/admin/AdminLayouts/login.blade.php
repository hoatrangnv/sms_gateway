<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>Login Page - Ace Admin</title>

    <meta name="description" content="User login page" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <!-- bootstrap & fontawesome -->
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/admin/css/admin_css.css')}}" />
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/css/bootstrap.min.css')}}" />
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/lib/font-awesome/4.2.0/css/font-awesome.min.css')}}" />

    <!-- page specific plugin styles -->

    <!-- text fonts -->
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/lib/fonts/fonts.googleapis.com.css')}}" />

    <!-- ace styles -->
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/css/ace.min.css')}}" />

    <!--[if lte IE 9]>
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/css/ace-part2.min.css')}}" />
    <![endif]-->

    <!--[if lte IE 9]>
    <link media="all" type="text/css" rel="stylesheet" href="{{URL::asset('assets/css/ace-ie.min.css')}}" />
    <![endif]-->

    <!-- inline styles related to this page -->

    <!-- ace settings handler -->
    <script src="{{URL::asset('assets/js/ace-extra.min.js')}}"></script>

    <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->
    <!--[if lte IE 8]>
    <script src="{{URL::asset('assets/js/html5shiv.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/respond.min.js')}}"></script>
    <![endif]-->
</head>

<body class="login-layout">
    @yield('content')
</body>
</html>
