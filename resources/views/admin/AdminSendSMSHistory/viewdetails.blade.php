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
                <li class="active"><a href="{{URL::route('admin.smsHistoryView')}}">
                        {{FunctionLib::viewLanguage('sent_sms_history')}}
                    </a>
                </li>
                <li class="active">{{FunctionLib::viewLanguage('detail')}}</li>
            </ul><!-- /.breadcrumb -->
        </div>
        @if($incorrect_number_list !="")
            <div class="space-16"></div>
            <label for="status">{{FunctionLib::viewLanguage('incorrect_number_list')}}</label>
            <div class="alert alert-danger">
                {{$incorrect_number_list}}
            </div>
        @endif
        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    <div class="panel panel-info">
                        <form method="get" action="{{URL::route('admin.smsHistoryDetailsView')}}" role="form">
                            <input type="hidden" name="id_customer_sms" value="{{FunctionLib::inputId($id_cs)}}">
                            <div class="panel-body">
                                <div class="form-group col-lg-3">
                                    <label for="carrier_id"><i>{{FunctionLib::viewLanguage('carrier')}}</i></label>
                                    <select name="carrier_id" id="carrier_id" class="form-control input-sm" tabindex="12"
                                            data-placeholder="">
                                        {!! $optionCarrier !!}
                                    </select>
                                </div>
                                <div class="form-group col-lg-3">
                                    <label for="status"><i>{{FunctionLib::viewLanguage('sending_status')}}</i></label>
                                    <select name="status" id="status" class="form-control input-sm" tabindex="12"
                                            data-placeholder="">
                                        {!! $optionStatus !!}
                                    </select>
                                </div>
                                <div class="form-group col-lg-3">
                                    <label for="from_day"><i>{{FunctionLib::viewLanguage('from_day')}}</i></label>
                                    <input type="text" class="form-control input-sm date-picker" id="from_day"
                                           name="from_day"
                                           @if(isset($dataSearch['from_day']))value="{{$dataSearch['from_day']}}"@endif>
                                </div>
                                <div class="form-group col-lg-3">
                                    <label for="to_day"><i>{{FunctionLib::viewLanguage('to_day')}}</i></label>
                                    <input type="text" class="form-control input-sm date-picker" name="to_day"
                                           autocomplete="off"
                                           @if(isset($dataSearch['to_day']))value="{{$dataSearch['to_day']}}"@endif>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                            <span class="">
                            <button class="btn btn-primary btn-sm" type="submit"><i
                                        class="fa fa-search"></i>{{FunctionLib::viewLanguage('search')}}</button>
                        </span>
                            </div>
                        </form>
                    </div>
                </div>

                @if(sizeof($data) > 0)
                    <div class="span clearfix"> @if($size >0) {{FunctionLib::viewLanguage('total')}}
                        <b>{{$size}}</b> {{FunctionLib::viewLanguage('results')}}  @endif </div>
                    <br>
                    <table class="table table-bordered">
                        <thead class="thin-border-bottom">
                        <tr class="">
                            <th class="w10" class="text-center">{{FunctionLib::viewLanguage('no')}}</th>
                            <th width="w50">{{FunctionLib::viewLanguage('carrier_name')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('sent_date')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('subscriber')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('content')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('cost_vnd')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('status')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key => $item)
                            <tr @if($item['user_status'] == -1)class="red bg-danger middle" {else}
                                class="middle" @endif>
                                <td class="text-center middle">{{ $start+$key+1 }}</td>
                                <td>{{ $item['carrier_name'] }}</td>
                                <td class="center">{{ $item['send_date_at'] }}</td>
                                <td class="center">{{ $item['phone_receive']}}</td>
                                <td>{{ $item['content']}}</td>
                                <td class="center">{{ $item['cost'] }}</td>
                                <td>{{ $arrStatus[$item['status']]}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="text-right">
                        {!! $paging !!}
                    </div>
                @else
                    <div class="alert">
                        {{FunctionLib::viewLanguage('no_data')}}
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>
    <script>
        $(document).ready(function () {
            $(".date-picker").datepicker({
                format: "yyyy-mm-dd",
                language: "vi",
                autoclose: true,
                keyboardNavigation: true
            })
        });
    </script>
@stop