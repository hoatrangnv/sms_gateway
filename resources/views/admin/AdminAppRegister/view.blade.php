<?php use App\Library\AdminFunction\FunctionLib; ?>
<?php use App\Library\AdminFunction\Define; ?>
@extends('admin.AdminLayouts.index')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
<div class="main-content-inner">
    <div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{URL::route('admin.dashboard')}}">{{FunctionLib::viewLanguage('home')}}</a>
            </li>
            <li class="active">{{FunctionLib::viewLanguage('sms_management')}}</li>
            <li class="active">{{FunctionLib::viewLanguage('sms_template')}}</li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="col-md-8 panel-content">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-list" aria-hidden="true"></i> {{\App\Library\AdminFunction\FunctionLib::viewLanguage('list_app')}}</h4>
                </div> <!-- /widget-header -->
                {{ Form::open(array('method' => 'GET', 'role'=>'form')) }}
                <div style="margin-top: 10px">
                    <div class="col-sm-4" >
                        <input @if(isset($search['app_name'])) value="{{$search['app_name']}}" @endif placeholder="{{FunctionLib::viewLanguage('app_name')}}" name="app_name_s" class="form-control" id="app_name_s">
                        {{--<select style="height: 34px" name="name_template" id="name_template" class="form-control input-sm">--}}
                            {{--{!! $optionUser !!}--}}
                        {{--</select>--}}
                    </div>
                    <div style="float: left" class="form-group">
                        <button class="btn btn-primary btn-sm" type="submit" name="submit" value="1"><i
                                    class="fa fa-search"></i> {{FunctionLib::viewLanguage('search')}}</button>
                    </div>
                </div>
                {{ Form::close() }}
                <div class="panel-body" id="element">
                    @if(sizeof($data) > 0)
                        <table class="table table-bordered">
                            <thead class="thin-border-bottom">
                            <tr class="">
                                <th class="text-center center" style="width: 10%; font-weight: normal!important;">{{FunctionLib::viewLanguage('no')}}</th>
                                <th class="center" style="width: 30%; font-weight: normal!important;">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('app_name')}}</th>
                                <th class="center" style="width: 40%; font-weight: normal!important;">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('description')}}</th>
                                <th class="center" style="width: 20%; font-weight: normal!important;">{{FunctionLib::viewLanguage('action')}}</th>
                            </tr>
                            </thead>
                            <tbody id="list_app">
                            @foreach ($data as $key => $item)
                                <td class="text-center middle">{{$key+1 }}</td>
                                <td>
                                    <a href="#" class="mg-t20" onclick="showDetails('{{$item['app_name']}}','{{FunctionLib::decodeBase64($item['client_id'])}}','{{FunctionLib::decodeBase64($item['client_secret'])}}')" data-toggle="modal" data-target="#modal-app-details">
                                        {{$item['app_name']}}
                                    </a>
                                </td>
                                <td>{{ $item['description']}}</td>
                                </td>
                                <td class="center">
                                    <a class="btn btn-primary tooltips" title="{{\App\Library\AdminFunction\FunctionLib::viewLanguage('edit_app')}}" onclick="edit_app('{{FunctionLib::inputId($item['app_id'])}}','{{$item['app_name']}}','{{$item['description']}}')"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                    <a class="btn btn-danger tooltips" title="{{\App\Library\AdminFunction\FunctionLib::viewLanguage('delete_app')}}" onclick="delete_item('{{FunctionLib::inputId($item['app_id'])}}')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert">
                            {{FunctionLib::viewLanguage('no_data')}}
                        </div>
                    @endif
                </div> <!-- /widget-content -->
            </div> <!-- /widget -->
        </div>
        <div class="col-md-4 panel-content">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-plus-square" aria-hidden="true"></i> {{\App\Library\AdminFunction\FunctionLib::viewLanguage('add_app')}}</h4>
                </div> <!-- /widget-header -->
                <div class="panel-body">
                    <form id="form" method="post">
                        <input type="hidden" name="id" value="{{\App\Library\AdminFunction\FunctionLib::inputId(0)}}" class="form-control" id="id">
                        <div class="form-group">
                            <label for="app_name">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('app_name')}}</label>
                            <input type="" name="app_name" title="{{\App\Library\AdminFunction\FunctionLib::viewLanguage('app_name')}}" class="form-control input-required" id="app_name">
                        </div>
                        <div class="form-group">
                            <label for="description">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('description')}}</label>
                            <textarea name="description" style="resize: none" title="{{FunctionLib::viewLanguage('description')}}" class="form-control input-required" rows="5" id="description"></textarea>
                        </div>
                        <a class="btn btn-success" id="submit" onclick="add_app()"><i class="fa fa-floppy-o" aria-hidden="true"></i> Submit</a>
                        <a class="btn btn-default" id="cancel" onclick="reset()"><i class="fa fa-undo" aria-hidden="true"></i> Reset</a>
                    </form>
                </div> <!-- /widget-content -->
            </div>
        </div>
        <div class="row">
        </div>
    </div>

    <div class="modal fade" id="modal-app-details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="width: 720px!important;">

                <div class="modal-header">
                    <button type="button" class="close bt_close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        id="myModalLabel">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('app_details')}}</h4>
                </div>

                <div class="modal-body">
                    <form class="row">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                <strong>{{\App\Library\AdminFunction\FunctionLib::viewLanguage('app_name')}}</strong>
                            </label>
                            <div class="col-sm-10">
                                <label>
                                    <strong id="name_app"></strong>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                <strong>Endpoint</strong>
                            </label>
                            <div class="col-sm-10">
                                <label>
                                    http://domain-name/oauth2/token
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                <strong>ClientID</strong>
                            </label>
                            <div class="col-sm-10">
                                <label id="client_id"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                <strong>Client secret</strong>
                            </label>
                            <div class="col-sm-10">
                                <label id="client_secret"></label>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-default bt_close" data-dismiss="modal">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('close')}}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
    <script>

        function showDetails(name,client_id,client_secret) {
            $("#name_app").html(name)
            $("#client_id").html(client_id)
            $("#client_secret").html(client_secret)
        }

        function reset() {
            $("#app_name").val("");
            $("#description").val("");
            $("#id").val('{{\App\Library\AdminFunction\FunctionLib::inputId(0)}}');
        }

        function delete_item(id) {
            var a = confirm(lng['txt_mss_confirm_delete']);
            if (a){
                $.ajax({
                    type: 'get',
                    url: '/manager/registerApp/deleteApp',
                    data: {
                        'id':id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if ((data.errors)) {
                            alert(data.errors)
                        }else {
                            $("#element").html(data.view)
                            reset();
                        }
                    },
                });
            }
        }

        function add_app() {
            var is_error = false;
            var msg = {};

            $("form#form :input").each(function(){
                var input = $(this); // This is the jquery object of the input, do what you will
                if ($(this).hasClass("input-required") && $(this).val() == "") {
                    msg[$(this).attr("name")] = "※" + $(this).attr("title") + lng['is_required'];
                    is_error = true;
                }
            });

            if (is_error == true) {
                var error_msg = "";
                $.each(msg, function (key, value) {
                    error_msg = error_msg + value + "\n";
                });
//            error_msg += (str_is_sms !="")?str_is_sms:"";
                alert(error_msg);
                return false;
            }else {
                $("#submit").attr("disabled","true");
                var app_name = $("#app_name").val()
                var description = $("#description").val()
                var id = $("#id").val()
                $.ajax({
                    type: 'post',
                    url: '/manager/registerApp/addApp',
                    data: {
                        'app_name':app_name,
                        'description':description,
                        'id':id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#submit').removeAttr("disabled")
                        if ((data.errors)) {
                            alert(data.errors)
                        }else {
                            $("#element").html(data.view)
                            reset();
                        }
                    },
                });
            }
        }

        function edit_app(id,app_name,description) {
            $("#app_name").val(app_name);
            $("#description").val(description);
            $("#id").val(id);
        }

    </script>
@stop
<style>
    a:hover {
        cursor:pointer;
    }
</style>