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
                <li class="active">{{FunctionLib::viewLanguage('SMS_quality_by_day')}}</li>
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
                                        <label for="station_account">{{FunctionLib::viewLanguage('user_name')}}</label>
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
                                <div class="col-sm-2">
                                    <label for="month">{{FunctionLib::viewLanguage('choose_month')}}</label>
                                    <select name="month" id="month" class="form-control input-sm">
                                        {!! $optionMonth !!}
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label for="year">{{FunctionLib::viewLanguage('choose_year')}}</label>
                                    <select name="year" id="year" class="form-control input-sm">
                                        {!! $optionYear !!}
                                    </select>
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
                    text: '{{FunctionLib::viewLanguage('report_by_date')}}'
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
                    pointFormat: '<b>{point.y}</b> of total<br/>' +
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
                            name:'{$v['day']}/{$v['month']}/{$v['year']}',
                            y:{$v['total_sms_day']},
                            success:{$v['total_sms_success']},
                            success_per:{$v['per_success']}
                            },";
                            }
                            ?>
                        ]
                    }
                ]
            });
        });
    </script>
@stop