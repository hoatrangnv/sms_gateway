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
    private $permission_view = 'stationReport_view';
    private $permission_full = 'stationReport_full';
//    private $permission_delete = 'carrierSetting_delete';
//    private $permission_create = 'carrierSetting_create';
//    private $permission_edit = 'carrierSetting_edit';

    private $arrManager = array();
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
        $this->arrManager = User::getOptionUserFullNameAndMail();
        $this->hours = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            6 => 6,
            8 => 8,
            12 => 12,
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

        $dataSearch['carrier_id'] = addslashes(Request::get('carrier_id',''));
        $dataSearch['day'] = addslashes(Request::get('day',''));
        $dataSearch['hours'] = addslashes(Request::get('hours',''));

        $hours_div = 8 ;
        if (isset($dataSearch['hours']) && $dataSearch['hours'] >1){
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

        $sql_where = "wsr.user_id = 8 AND wsr.year=".$year_search." AND wsr.month=".$month_search." AND wsr.day=".$day_search;
        if (isset($dataSearch['carrier_id']) && $dataSearch['carrier_id']>0 && $dataSearch['carrier_id']!=""){
            $sql_where.="AND wsr.carrier_id=".$dataSearch['carrier_id'];
        }

        $sql = "
        SELECT Sum(wsr.success_number) as total_sms_hour,wsr.hour,wsr.day,wsr.month,wsr.year from web_sms_report wsr inner join web_carrier_setting wcs ON wsr.carrier_id = wcs.carrier_setting_id
WHERE {$sql_where} 
GROUP BY wsr.day,wsr.month,wsr.year,ceil(wsr.hour/{$hours_div})
        ";
        $data = SmsReport::executesSQL($sql);
        foreach ($data as $k => $v){
            $data[$k] = (array)$v;
        }
//        FunctionLib::debug($data);
        $dataSearch['station_account'] = addslashes(Request::get('station_account',''));
        $optionUser = FunctionLib::getOption(array(''=>''.FunctionLib::controLanguage('select_user',$this->languageSite).'')+$this->arrManager, (isset($dataSearch['station_account'])?$dataSearch['station_account']:0));
        $optionCarrier = FunctionLib::getOption(array(''=>''.FunctionLib::controLanguage('all',$this->languageSite).'')+$arrCarrier, (isset($dataSearch['carrier_id'])?$dataSearch['carrier_id']:0));
        $optionHours = FunctionLib::getOption($this->hours, (isset($dataSearch['hours'])?$dataSearch['hours']:8));
        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSMSHoursReportChart.view',array_merge([
            'data'=>$data,
            'search'=>$dataSearch,
            'optionUser'=>$optionUser,
            'optionHours'=>$optionHours,
            'optionCarrier'=>$optionCarrier,
        ],$this->viewPermission));
    }
}