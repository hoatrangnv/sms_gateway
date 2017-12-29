{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>--}}
{{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>--}}
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
                <li class="active">{{FunctionLib::viewLanguage('send_sms_chart')}}</li>
                <li class="active">{{FunctionLib::viewLanguage('billing_graph_of_successful')}}</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    @if($is_root || $permission_full ==1)
                        <div class="panel panel-info">
                            {{ Form::open(array('method' => 'GET', 'role'=>'form')) }}
                            <div class="panel-body">
                                @if($user_role_type == \App\Library\AdminFunction\Define::ROLE_TYPE_SUPER_ADMIN)
                                    <div class="col-sm-2">
                                        <label for="type_report">{{FunctionLib::viewLanguage('report_type')}}</label>
                                        <select onchange="show_opt_user()" name="type_report" id="type_report"
                                                class="form-control input-sm">
                                            {!! $optionTypeReort !!}
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="station_account">{{FunctionLib::viewLanguage('station_account')}}</label>
                                        <select name="station_account1" id="station_account1"
                                                class="form-control input-sm">
                                            {!! $optionUser_station !!}
                                        </select>
                                        <select name="station_account2" id="station_account2"
                                                class="form-control input-sm hide">
                                            {!! $optionUser_customer !!}
                                        </select>
                                    </div>
                                @endif
                                <div class="col-sm-2">
                                    <label for="carrier_id">{{FunctionLib::viewLanguage('choose_carrier')}}</label>
                                    <select name="carrier_id" id="carrier_id" class="form-control input-sm">
                                        {!! $optionCarrier !!}
                                    </select>
                                </div>
                                <div class="form-group col-lg-3">
                                    <label for="from_date"><i>{{FunctionLib::viewLanguage('from_day')}}</i></label>
                                    <input type="text" class="form-control input-sm date-picker1212" id="txtFromDate"
                                           name="from_date" autocomplete="off"
                                           @if(isset($search['from_date']))value="{{$search['from_date']}}"@endif>
                                </div>
                                <div class="form-group col-lg-3">
                                    <label for="to_date"><i>{{FunctionLib::viewLanguage('to_day')}}</i></label>
                                    <input type="text" class="form-control input-sm date-picker1212" id="txtToDate"
                                           name="to_date" autocomplete="off"
                                           @if(isset($search['to_date']))value="{{$search['to_date']}}"@endif>
                                </div>
                                <div class="form-group col-lg-12 text-right">
                                    <button class="btn btn-primary btn-sm" type="submit" name="submit" value="1"><i
                                                class="fa fa-search"></i> {{FunctionLib::viewLanguage('search')}}
                                    </button>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    @endif
                    @if(!empty($data))
                        <div id="container"
                             style="min-width: 310px; height: 400px; max-width: 800px; margin: 0 auto">
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
    <script type="text/javascript">
        $(document).ready(function () {
            show_opt_user();
        });
        function show_opt_user(){
            if($("#type_report").val() == "2"){
                $("#station_account2").removeClass( 'hide' );
                $("#station_account1").addClass( 'hide' );
            }else{
                $("#station_account1").removeClass( 'hide' );
                $("#station_account2").addClass( 'hide' );
            }
        }
        $(function () {

            $('#container').highcharts({
                chart: {
                    type: 'column'
                },
                title: {
                    text: '{{FunctionLib::viewLanguage('report_by_month')}}'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Values'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}'
                        }
                    }
                },

                tooltip: {
//                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '{point.y}</b> of total<br/>' +
                    '<b>{point.success}</b> of success <br/>' +
                    '<b>{point.success_per:.1f}%</b> success <br/>'
                },
                series: [
                    {
                        name: 'Brands',
                        colorByPoint: true,
                        data: [
                            <?php
                            foreach ($data as $v) {
                                echo "{
                            name:'{$v['month']}/{$v['year']}',
                            y:{$v['total_sms_month']},
                            success:{$v['total_success']},
                            success_per:{$v['success_per']}
                            },";
                            }
                            ?>
                        ]
                    }
                ]
            });
        });

        $(document).ready(function () {
//            var checkin = $('.date-picker1212').datepicker({ });

            $("#txtFromDate").datepicker({
                numberOfMonths: 1,
                onSelect: function (selected) {
                    $("#txtToDate").datepicker("option", "minDate", selected)
                }
            });
            $("#txtToDate").datepicker({
                numberOfMonths: 1,
                onSelect: function (selected) {
                    $("#txtFromDate").datepicker("option", "maxDate", selected)
                }
            });
        });

    </script>
@stop
