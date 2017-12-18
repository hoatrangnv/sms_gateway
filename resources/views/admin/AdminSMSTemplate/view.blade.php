{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">--}}
{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">--}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
{{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>--}}
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
            <li class="active">{{FunctionLib::viewLanguage('sent_sms_history')}}</li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="col-md-8 panel-content">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="fa fa-child"></i> {{\App\Library\AdminFunction\FunctionLib::viewLanguage('web_sms_template_list')}}</h4>
                </div> <!-- /widget-header -->
                <div class="panel-body">
                    @if(sizeof($data) > 0)
                        <table class="table table-bordered">
                            <thead class="thin-border-bottom">
                            <tr class="">
                                <th class="w10" class="text-center">{{FunctionLib::viewLanguage('no')}}</th>
                                <th width="w50">{{FunctionLib::viewLanguage('template_name')}}</th>
                                <th width="w100">{{FunctionLib::viewLanguage('content')}}</th>
                                <th width="w100">{{FunctionLib::viewLanguage('update')}}</th>
                                <th width="w100">{{FunctionLib::viewLanguage('action')}}</th>
                            </tr>
                            </thead>
                            <tbody id="list_sms_template">
                            @foreach ($data as $key => $item)
                                <td class="text-center middle">{{$key+1 }}</td>
                                <td>{{$item['template_name']}}</td>
                                <td>{{ $item['content'] }}</td>
                                <td>{{ $item['updated_date'] }}</td>
                                <td>thao tac</td>
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
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4><i class="fa fa-user"></i> {{\App\Library\AdminFunction\FunctionLib::viewLanguage('add_template_sms')}}</h4>
                </div> <!-- /widget-header -->
                <div class="panel-body">
                    <div class="form-group">
                        <label for="name_template">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('sms_template_name')}}</label>
                        <input type="" class="form-control" id="name_template">
                    </div>
                    <div class="form-group">
                        <label for="content">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('sms_content_grafted')}}</label>
                        <textarea style="resize: none" class="form-control" rows="5" id="content"></textarea>
                    </div>
                    <a class="btn btn-success" onclick="add_sms_template()">Submit</a>
                    <a class="btn btn-danger">Cancel</a>
                </div> <!-- /widget-content -->
            </div>
        </div>
        <div class="row">
        </div>
    </div>
</div>
@stop
<script>
    function add_sms_template() {
        var name_template = $("#name_template").val()
        var content = $("#content").val()
        $.ajax({
            type: 'post',
            url: '/manager/smsTeplate/addTemplate',
            data: {
                'name_template':name_template,
                'content':content
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                debugger
                if ((data.errors)) {
                    alert(data.errors)
                }
            },
        });
    }
    $(document).ready(function(){
        $(".date-picker").datepicker({
            format: "yyyy-mm-dd",
            language: "vi",
            autoclose: true,
            keyboardNavigation:true
        })});
</script>