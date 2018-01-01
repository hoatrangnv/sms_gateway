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

class AdminSMSHoursReportChartController extends BaseAdminController
{
    private $permission_view = 'smsHoursReport_view';
    private $permission_full = 'smsHoursReport_full';
//    private $permission_delete = 'carrierSetting_delete';
//    private $permission_create = 'carrierSetting_create';
//    private $permission_edit = 'carrierSetting_edit';

    private $arrManager_station = array();
    private $arrManager_customer = array();
    private $arrTypeReport = array();
    private $hours = array();
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
        $this->arrManager_station = User::getOptionUserFullMail(2);
        $this->arrManager_customer = User::getOptionUserFullMail(3);
        $this->hours = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            6 => 6,
            8 => 8,
            12 => 12,
        );
        $this->arrTypeReport = array(
            "1"=>FunctionLib::controLanguage('station_account',$this->languageSite),
            "2"=>FunctionLib::controLanguage('customer_account',$this->languageSite)
        );
    }

    public function getPermissionPage(){
        return $this->viewPermission = [
            'is_root'=> $this->is_root ? 1:0,
            'permission_full'=>in_array($this->permission_full, $this->permission) ? 1 : 0,
            'user_role_type'=> $this->role_type,
        ];
    }

    public function view() {
        //Check phan quyen.
        if(!$this->is_root && !in_array($this->permission_full,$this->permission)&& !in_array($this->permission_view,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }

        $dataSearch['type_report'] = addslashes(Request::get('type_report',''));
        $dataSearch['station_account1'] = addslashes(Request::get('station_account1',''));
        $dataSearch['station_account2'] = addslashes(Request::get('station_account2',''));

        if($this->role_type == Define::ROLE_TYPE_SUPER_ADMIN){
//            $dataSearch['user_id'] = (int)Request::get('station_account');
            $dataSearch['user_id'] = $dataSearch['type_report'] == "1"?(int)Request::get('station_account1'):(int)Request::get('station_account2');
        }else{
            $dataSearch['user_id'] = $this->user_id;
        }

        $dataSearch['carrier_id'] = addslashes(Request::get('carrier_id',''));
        $dataSearch['day'] = addslashes(Request::get('day',''));
        $dataSearch['hours'] = addslashes(Request::get('hours',''));

        $hours_div = 8 ;
        if (isset($dataSearch['hours']) && $dataSearch['hours'] >0){
            $hours_div= $dataSearch['hours'];
        }
        $current_day = date('m/d/Y');
        if (isset($dataSearch['day']) && $dataSearch['day'] !=""){
            $current_day= $dataSearch['day'];
        }else{
            $dataSearch['day'] = $current_day;
        }

        $month_search = date('m',strtotime($current_day));
        $day_search = date('d',strtotime($current_day));
        $year_search = date('Y',strtotime($current_day));

        $arrCarrier = CarrierSetting::getOptionCarrier();
        $sql_where = "wsr.year=".$year_search." AND wsr.month=".$month_search." AND wsr.day=".$day_search;
        if (isset($dataSearch['carrier_id']) && $dataSearch['carrier_id']>0 && $dataSearch['carrier_id']!=""){
            $sql_where.=" AND wsr.carrier_id=".$dataSearch['carrier_id'];
        }
        $data = array();
        if (isset($dataSearch['user_id']) && $dataSearch['user_id']>0 && $dataSearch['user_id']!=""){
            $sql_where.=" AND wsr.user_id=".$dataSearch['user_id'];

        }

        if ($dataSearch['type_report'] == "") $dataSearch['type_report']="1";

        if ($dataSearch['type_report'] == "1" && $dataSearch['user_id'] == ""){
            $id_station = join(",",array_keys($this->arrManager_station));
            $sql_where.=" AND wsr.user_id in (".$id_station.") ";
        }

        if ($dataSearch['type_report'] == "2" && $dataSearch['user_id'] == ""){
            $id_customer = join(",",array_keys($this->arrManager_customer));
            $sql_where.=" AND wsr.user_id in (".$id_customer.") ";
        }

        $sql = "
        SELECT  SUM(wsr.cost) as total_cost,Sum(wsr.success_number + wsr.fail_number) as total_sms_hour,Sum(wsr.success_number) as total_sms_success,
        (Sum(wsr.success_number)/ Sum(wsr.success_number + wsr.fail_number)) * 100 as success_percent,
        wsr.day,wsr.month,wsr.year,concat((ceil(wsr.hour/{$hours_div})-1)*{$hours_div},'-',((ceil(wsr.hour/{$hours_div})-1)*{$hours_div})+{$hours_div}) as range_time from web_sms_report wsr 
WHERE {$sql_where} 
GROUP BY wsr.day,wsr.month,wsr.year,ceil(wsr.hour/{$hours_div})
        ";
        $data = SmsReport::executesSQL($sql);
        foreach ($data as $k => $v){
            $data[$k] = (array)$v;
        }
        $dataSearch['station_account'] = addslashes(Request::get('station_account',''));
        $optionUser_station = FunctionLib::getOption(array(''=>''.FunctionLib::controLanguage('all',$this->languageSite).'')+$this->arrManager_station, (isset($dataSearch['station_account1']) && $dataSearch['station_account1'] !=""?$dataSearch['station_account1']:0));
        $optionUser_customer = FunctionLib::getOption(array(''=>''.FunctionLib::controLanguage('all',$this->languageSite).'')+$this->arrManager_customer, (isset($dataSearch['station_account2']) && $dataSearch['station_account2']!=""?$dataSearch['station_account2']:0));
        $optionTypeReort = FunctionLib::getOption($this->arrTypeReport, (isset($dataSearch['type_report'])?$dataSearch['type_report']:"1"));
        $optionCarrier = FunctionLib::getOption(array(''=>''.FunctionLib::controLanguage('all',$this->languageSite).'')+$arrCarrier, (isset($dataSearch['carrier_id'])?$dataSearch['carrier_id']:0));
        $optionHours = FunctionLib::getOption($this->hours, (isset($dataSearch['hours']) && $dataSearch['hours']>0?$dataSearch['hours']:8));
        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSMSHoursReportChart.view',array_merge([
            'data'=>$data,
            'search'=>$dataSearch,
            'optionUser_station'=>$optionUser_station,
            'optionUser_customer'=>$optionUser_customer,
            'optionHours'=>$optionHours,
            'optionCarrier'=>$optionCarrier,
            'optionTypeReort'=>$optionTypeReort,
            'hours_div'=>$hours_div,
        ],$this->viewPermission));
    }
}