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
                <li class="active">{{FunctionLib::viewLanguage('station_management')}}</li>
                <li class="active">{{FunctionLib::viewLanguage('station_list')}}</li>
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
                                <div class="col-sm-2">
                                    <label for="station_account">{{FunctionLib::viewLanguage('station_account')}}</label>
                                    <select name="station_account" id="station_account" class="form-control input-sm">
                                        {!! $optionUser !!}
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label for="station_account">{{FunctionLib::viewLanguage('station_account')}}</label>
                                    <select name="station_account" id="station_account" class="form-control input-sm">
                                        {!! $optionUser !!}
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label for="year">{{FunctionLib::viewLanguage('choose_year')}}</label>
                                    <select name="year" id="year" class="form-control input-sm">
                                        {!! $optionUser !!}
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label for="month">{{FunctionLib::viewLanguage('choose_month')}}</label>
                                    <select name="month" id="month" class="form-control input-sm">
                                        {!! $optionUser !!}
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
                <!--view biểu đồ 2-->
                    <div id="container_2"></div>
                    <!--view biểu đồ 1-->
                    <div id="container" style="min-width: 310px; height: 400px; max-width: 800px; margin: 0 auto"></div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {

            $('#container_2').highcharts({
                chart: {
                    type: 'line'
                },
                title: {
                    text: '<?php echo $title_line_chart?>'
                },
                xAxis: {
                    categories: [<?php
                        echo join($arrDay, ',')
                        ?>]
                },
                yAxis: {
                    title: {
                        text: 'Total SMS'
                    }
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: true
                    }
                },
                series: [
                    <?php
                    foreach ($arrData as $k => $v) {
                        echo "
                        {
                        name:'" . $k . "',
                        data:[
                        " . join($v, ',') . "
                        ]
                        },
                        ";
                    }
                    ?>
                ]

            });
            Highcharts.chart('container', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Browser market shares January, 2015 to May, 2015'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Brands',
                    colorByPoint: true,
                    data: [{
                        name: 'Microsoft Internet Explorer',
                        y: 56.33
                    }, {
                        name: 'Chrome',
                        y: 24.03,
                        sliced: true,
                        selected: true
                    }, {
                        name: 'Firefox',
                        y: 10.38
                    }, {
                        name: 'Safari',
                        y: 4.77
                    }, {
                        name: 'Opera',
                        y: 0.91
                    }, {
                        name: 'Proprietary or Undetectable',
                        y: 0.2
                    }]
                }]
            });
            $('#container').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie',
//                    type: 'pie',
//                    options3d: {
//                        enabled: true,
//                        alpha: 50,
//                        beta: 0
//                    }
                },
                title: {
                    text: '<?php echo $title_line_chart?>'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        depth: 35,
//                        dataLabels: {
//                            enabled: true,
//                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
//                            style: {
//                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
//                            }
//                        },
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Brands',
                    colorByPoint: true,
                    data: [
                        <?php
                        foreach ($arrPieChart as $k => $v) {
                            echo "
                            {
                            name:'" . $v['name'] . "',
                            y:" . $v['percent'] . ",
                            sliced: " . $v['sliced'] . ",
                            selected: " . $v['selected'] . "
                            },
                            ";
                        }
                        ?>
                    ]
                }]
            });
        });
    </script>
@stop