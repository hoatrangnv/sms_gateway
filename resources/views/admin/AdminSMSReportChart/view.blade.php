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
                                    <label for="carrier_id">{{FunctionLib::viewLanguage('choose_carrier')}}</label>
                                    <select name="carrier_id" id="carrier_id" class="form-control input-sm">
                                        {!! $optionCarrier !!}
                                    </select>
                                </div>
                                <div class="col-sm-1">
                                    <label for="year">{{FunctionLib::viewLanguage('choose_year')}}</label>
                                    <select name="year" id="year" class="form-control input-sm">
                                        {!! $optionYear !!}
                                    </select>
                                </div>
                                <div class="col-sm-1">
                                    <label for="month">{{FunctionLib::viewLanguage('choose_month')}}</label>
                                    <select name="month" id="month" class="form-control input-sm">
                                        {!! $optionMonth !!}
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
                    @if(!empty($arrData))
                    <!--view biểu đồ line-->
                        <div id="container_2"></div>
                    @else
                        <div class="alert">
                            {{FunctionLib::viewLanguage('no_data')}}
                        </div>
                    @endif
                    @if(!empty($arrPieChart))
                    <!--view biểu đồ tròn-->
                        <div id="container"
                             style="min-width: 310px; height: 400px; max-width: 800px; margin: 0 auto">
                        </div>
                        <div id=""
                             style="min-width: 310px; height: 400px; max-width: 800px; margin: 0 auto">
                            <p>{{FunctionLib::viewLanguage('total').' '.$total_num_pie}}</p>
                        </div>
                    @endif
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
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        },
//                        dataLabels: {
//                            enabled: false
//                        },
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