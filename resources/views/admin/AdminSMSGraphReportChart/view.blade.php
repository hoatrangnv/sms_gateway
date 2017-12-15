<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
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
                                <div class="form-group col-lg-3">
                                    <label for="from_date"><i>{{FunctionLib::viewLanguage('from_day')}}</i></label>
                                    <input type="text" class="form-control input-sm date-picker" name="from_date" autocomplete="off"  @if(isset($search['from_date']))value="{{$search['from_date']}}"@endif>
                                </div>
                                <div class="form-group col-lg-3">
                                    <label for="to_date"><i>{{FunctionLib::viewLanguage('to_day')}}</i></label>
                                    <input type="text" class="form-control input-sm date-picker" name="to_date" autocomplete="off"  @if(isset($search['to_date']))value="{{$search['to_date']}}"@endif>
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
    </script>
@stop
<script>
    $(document).ready(function(){
        $(".date-picker").datepicker({
            format: "dd-mm-YYYY",
            language: "vi",
            autoclose: true,
            keyboardNavigation:true
        })});
</script>