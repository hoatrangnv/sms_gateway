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

class AdminSMSYearReportChartController extends BaseAdminController
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

        $dataSearch['year_from'] = addslashes(Request::get('year_from',''));
        $dataSearch['year_to'] = addslashes(Request::get('year_to',''));
        $dataSearch['carrier_id'] = addslashes(Request::get('carrier_id',''));
        $year = date('Y',time());

        $year_to = date("Y",time());
        $year_from = date("Y",strtotime("-2 year"));
        if (isset($dataSearch['year_from']) && $dataSearch['year_from'] !="" && isset($dataSearch['year_to']) && $dataSearch['year_to'] !="" ){
            $year_from = $dataSearch['year_from'];
            $year_to = $dataSearch['year_to'];
        }

        $arrCarrier = CarrierSetting::getOptionCarrier();

        $current_year = date("Y",time());
        $last_10_year = date("Y",strtotime("-10 year"));
        $arrYear = array();
        for($i=$current_year;$i>=$last_10_year;$i--){
            $arrYear[$i] = $i;
        }

        $sql_where = "wsr.user_id = 8 AND wsr.year>=".$year_from." AND wsr.month<=".$year_to." ";
        if (isset($dataSearch['carrier_id']) && $dataSearch['carrier_id']>0 && $dataSearch['carrier_id']!=""){
            $sql_where.="AND wsr.carrier_id=".$dataSearch['carrier_id'];
        }

        $sql = "
        SELECT Sum(wsr.success_number) as total_sms_year,wcs.carrier_name,wsr.year,wsr.carrier_id from web_sms_report wsr inner join web_carrier_setting wcs ON wsr.carrier_id = wcs.carrier_setting_id
WHERE {$sql_where} 
GROUP BY wsr.year,wsr.carrier_id
        ";
        $data = SmsReport::executesSQL($sql);
        foreach ($data as $k => $v){
            $data[$k] = (array)$v;
        }
        $arr_year_report = array();
        foreach ($data as $v){
            $arr_year_report[$v['year']] =$v['year'];
        }
        foreach ($arr_year_report as $value ){
            foreach ($data as $k => $v){
                if (!isset($v['year']) || $value != $v['year']){
                    FunctionLib::debug('xx');
                }
            }
        }
        $data_report = array();
        foreach ($data as $k => $v){
            $data_report[$v['carrier_name']][] = $v;
        }

        FunctionLib::debug($data_report);
        $dataSearch['station_account'] = addslashes(Request::get('station_account',''));
        $optionUser = FunctionLib::getOption(array(''=>''.FunctionLib::controLanguage('select_user',$this->languageSite).'')+$this->arrManager, (isset($dataSearch['station_account'])?$dataSearch['station_account']:0));
        $optionYear = FunctionLib::getOption($arrYear, (isset($dataSearch['year'])?$dataSearch['year']:$current_year));
        $optionCarrier = FunctionLib::getOption(array(''=>''.FunctionLib::controLanguage('all',$this->languageSite).'')+$arrCarrier, (isset($dataSearch['carrier_id'])?$dataSearch['carrier_id']:0));
        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSMSYearReportChart.view',array_merge([
            'data'=>$data_report,
            'search'=>$dataSearch,
            'optionUser'=>$optionUser,
            'optionYear'=>$optionYear,
            'optionCarrier'=>$optionCarrier,
            'arr_year_report'=>$arr_year_report,
        ],$this->viewPermission));
    }
}