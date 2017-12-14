<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\User;
use App\Http\Models\CarrierSetting;
use App\Http\Models\SmsReport;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Translation\Dumper\FileDumper;

class AdminSMSMonthReportChartController extends BaseAdminController
{
    private $permission_view = 'stationReport_view';
    private $permission_full = 'stationReport_full';
//    private $permission_delete = 'carrierSetting_delete';
//    private $permission_create = 'carrierSetting_create';
//    private $permission_edit = 'carrierSetting_edit';

    private $arrManager = array();
    private $arrStatus = array();
    private $error = array();
    private $viewPermission = array();//check quyen

    public function __construct()
    {
        parent::__construct();
        CGlobal::$pageAdminTitle = 'Quản lý menu';
        $this->getDataDefault();

        FunctionLib::link_js(array(
            'lib/highcharts/highcharts.js',
            'lib/highcharts/highcharts-3d.js',
            'lib/highcharts/exporting.js',
        ));
    }

    public function getDataDefault()
    {
        $this->arrManager = User::getOptionUserFullNameAndMail();
        $this->arrStatus = array(
            CGlobal::active => FunctionLib::controLanguage('active',$this->languageSite),
            CGlobal::not_active => FunctionLib::controLanguage('not_active',$this->languageSite)
        );
    }

    public function getPermissionPage(){
        return $this->viewPermission = [
            'is_root'=> $this->is_root ? 1:0,
            'permission_full'=>in_array($this->permission_full, $this->permission) ? 1 : 0,
        ];
    }

    public function view() {
        //Check phan quyen.
        if(!$this->is_root && !in_array($this->permission_full,$this->permission)&& !in_array($this->permission_view,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }

        $dataSearch['month'] = addslashes(Request::get('month',''));
        $dataSearch['year'] = addslashes(Request::get('year',''));
        $dataSearch['carrier_id'] = addslashes(Request::get('carrier_id',''));
        $year = date('Y',time());
        if (isset($dataSearch['year']) && $dataSearch['year'] !=""){
            $year = $dataSearch['year'];
        }

        $arrCarrier = CarrierSetting::getOptionCarrier();

        $current_year = date("Y",time());
        $last_10_year = date("Y",strtotime("-10 year"));
        $arrYear = array();
        for($i=$current_year;$i>=$last_10_year;$i--){
            $arrYear[$i] = $i;
        }

        $sql_where = "wsr.user_id = 8 AND wsr.year=".$year." ";
        if (isset($dataSearch['carrier_id']) && $dataSearch['carrier_id']>0 && $dataSearch['carrier_id']!=""){
            $sql_where.="AND wsr.carrier_id=".$dataSearch['carrier_id'];
        }

        $sql = "
        SELECT (Sum(wsr.success_number)/Sum(wsr.success_number+wsr.fail_number))*100 as success_per,Sum(wsr.success_number+wsr.fail_number) as total_sms_month,Sum(wsr.success_number) as total_success,wsr.month,wsr.year from web_sms_report wsr inner join web_carrier_setting wcs ON wsr.carrier_id = wcs.carrier_setting_id
WHERE {$sql_where} 
GROUP BY wsr.month,wsr.year
        ";
        $data = SmsReport::executesSQL($sql);
        foreach ($data as $k => $v){
            $data[$k] = (array)$v;
        }
        $arr_month_report = array();
        foreach ($data as $v){
            $arr_month_report[$v['month']] = $v['month'].'/'.$year;
        }
//        $data_report = array();
//        foreach ($data as $k => $v){
//            $data_report[$v['carrier_name']][] = $v;
//        }

        $dataSearch['station_account'] = addslashes(Request::get('station_account',''));
        $optionUser = FunctionLib::getOption(array(''=>''.FunctionLib::controLanguage('select_user',$this->languageSite).'')+$this->arrManager, (isset($dataSearch['station_account'])?$dataSearch['station_account']:0));
        $optionYear = FunctionLib::getOption($arrYear, (isset($dataSearch['year'])?$dataSearch['year']:$current_year));
        $optionCarrier = FunctionLib::getOption(array(''=>''.FunctionLib::controLanguage('all',$this->languageSite).'')+$arrCarrier, (isset($dataSearch['carrier_id'])?$dataSearch['carrier_id']:0));
        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
//        FunctionLib::debug($data);
        return view('admin.AdminSMSMonthReportChart.view',array_merge([
            'data'=>$data,
            'search'=>$dataSearch,
            'optionUser'=>$optionUser,
            'optionYear'=>$optionYear,
            'optionCarrier'=>$optionCarrier,
            'arr_month_report'=>$arr_month_report,
        ],$this->viewPermission));
    }
}