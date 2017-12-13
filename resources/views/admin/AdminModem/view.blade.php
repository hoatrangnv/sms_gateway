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
            <li class="active">{{FunctionLib::viewLanguage('carrier_setting')}}</li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
                <!-- PAGE CONTENT BEGINS -->
                <div class="panel panel-info">
                    {{ Form::open(array('method' => 'GET', 'role'=>'form')) }}
                    <div class="panel-body">
                        <div class="form-group col-lg-4">
                            <label for="station_account">{{FunctionLib::viewLanguage('station_account')}}</label>
                            <select name="station_account" id="station_account" class="form-control input-sm">
                                {!! $optionUser !!}
                            </select>
                        </div>

                        <div class="form-group col-lg-12 text-right">
                                {{--<button class="btn btn-warning btn-sm" type="submit" name="submit" value="2"><i class="fa fa-file-excel-o"></i> Xuất Excel</button>--}}
                                <button class="btn btn-primary btn-sm" type="submit" name="submit" value="1"><i class="fa fa-search"></i> {{FunctionLib::viewLanguage('search')}}</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
                @if(sizeof($data) > 0)
                    <div class="span clearfix"> @if($size >0) {{FunctionLib::viewLanguage('total')}} <b>{{$size}}</b> {{FunctionLib::viewLanguage('results')}}  @endif </div>
                    <br>
                    <table class="table table-bordered">
                        <thead class="thin-border-bottom">
                        <tr class="">
                            <th class="w10" class="text-center">{{FunctionLib::viewLanguage('no')}}</th>
                            <th width="w50">{{FunctionLib::viewLanguage('modem_name')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('modem_type')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('station_account')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('successful')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('failure')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('update')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('digital')}}</th>
                            <th width="w100">{{FunctionLib::viewLanguage('status')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key1 => $item1)
                            @foreach ($item1 as $key => $item)
                            <tr class="middle">
                                <td class="text-center middle">{{ $start+$key+1 }}</td>
                                <td>{{ $item['modem_name'] }}</td>
                                <td>{{ $item['modem_type'] }}</td>
                                <td>{{ $item['user_name'] }}</td>
                                <td>{{ $item['sum_success'] }}</td>
                                <td>{{ $item['sum_error'] }}</td>
                                <td>{{ $item['updated_date'] }}</td>
                                <td>{{ $item['digital'] }}</td>
                                <td>
                                    @if($item['is_active']=='1')
                                        <i class="fa fa-toggle-on fa-2x green" aria-hidden="true"></i>
                                    @else
                                        <i class="fa fa-toggle-off fa-2x red" aria-hidden="true"></i>
                                    @endif
                                </td>

                            </tr>
                            @endforeach
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
@stop