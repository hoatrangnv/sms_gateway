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
    private $permission_view = 'smsMonthReport_view';
    private $permission_full = 'smsMonthReport_full';
//    private $permission_delete = 'carrierSetting_delete';
//    private $permission_create = 'carrierSetting_create';
//    private $permission_edit = 'carrierSetting_edit';

    private $arrManager_station = array();
    private $arrManager_customer = array();
    private $arrTypeReport = array();
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
//        $this->arrManager = User::getOptionUserFullNameAndMail();
        $this->arrManager_station = User::getOptionUserFullMail(2);
        $this->arrManager_customer = User::getOptionUserFullMail(3);
        $this->arrStatus = array(
            CGlobal::active => FunctionLib::controLanguage('active', $this->languageSite),
            CGlobal::not_active => FunctionLib::controLanguage('not_active', $this->languageSite)
        );
        $this->arrTypeReport = array(
            "1"=>FunctionLib::controLanguage('station_account',$this->languageSite),
            "2"=>FunctionLib::controLanguage('customer_account',$this->languageSite)
        );
    }

    public function getPermissionPage()
    {
        return $this->viewPermission = [
            'is_root' => $this->is_root ? 1 : 0,
            'permission_full' => in_array($this->permission_full, $this->permission) ? 1 : 0,
            'user_role_type' => $this->role_type,
        ];
    }

    public function view()
    {
        //Check phan quyen.
        if (!$this->is_root && !in_array($this->permission_full, $this->permission) && !in_array($this->permission_view, $this->permission)) {
            return Redirect::route('admin.dashboard', array('error' => Define::ERROR_PERMISSION));
        }

        $dataSearch['type_report'] = addslashes(Request::get('type_report',''));
        $dataSearch['station_account1'] = addslashes(Request::get('station_account1',''));
        $dataSearch['station_account2'] = addslashes(Request::get('station_account2',''));

        $dataSearch['month'] = addslashes(Request::get('month', ''));
        $dataSearch['year'] = addslashes(Request::get('year', ''));
        $dataSearch['carrier_id'] = addslashes(Request::get('carrier_id', ''));
        $year = date('Y', time());
        if (isset($dataSearch['year']) && $dataSearch['year'] != "") {
            $year = $dataSearch['year'];
        }

        if ($this->role_type == Define::ROLE_TYPE_SUPER_ADMIN) {
            $dataSearch['user_id'] = $dataSearch['type_report'] == "1"?(int)Request::get('station_account1'):(int)Request::get('station_account2');
//            $dataSearch['user_id'] = addslashes(Request::get('station_account', ''));
        } else {
            $dataSearch['user_id'] = $this->user_id;
        }
        $arrCarrier = CarrierSetting::getOptionCarrier();

        $current_year = date("Y", time());
        $last_10_year = date("Y", strtotime("-10 year"));
        $arrYear = array();
        for ($i = $current_year; $i >= $last_10_year; $i--) {
            $arrYear[$i] = $i;
        }

        $sql_where = " wsr.year=" . $year . " ";
        if (isset($dataSearch['carrier_id']) && $dataSearch['carrier_id'] > 0 && $dataSearch['carrier_id'] != "") {
            $sql_where .= "AND wsr.carrier_id=" . $dataSearch['carrier_id'];
        }
        $data = array();
        if (isset($dataSearch['user_id']) && $dataSearch['user_id'] > 0 && $dataSearch['user_id'] != "") {
            $sql_where .= " AND wsr.user_id=" . $dataSearch['user_id'];
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
        SELECT (Sum(wsr.success_number)/Sum(wsr.success_number+wsr.fail_number))*100 as success_per,Sum(wsr.success_number+wsr.fail_number) as total_sms_month,Sum(wsr.success_number) as total_success,wsr.month,wsr.year from web_sms_report wsr 
WHERE {$sql_where} 
GROUP BY wsr.month,wsr.year
        ";
        $data = SmsReport::executesSQL($sql);


        foreach ($data as $k => $v) {
            $data[$k] = (array)$v;
        }
        $arr_month_report = array();
        foreach ($data as $v) {
            $arr_month_report[$v['month']] = $v['month'] . '/' . $year;
        }
        $dataSearch['station_account'] = addslashes(Request::get('station_account', ''));

//        $optionUser = FunctionLib::getOption(array('' => '' . FunctionLib::controLanguage('select_user', $this->languageSite) . '') + $this->arrManager, (isset($dataSearch['station_account']) ? $dataSearch['station_account'] : 0));
        $optionUser_station = FunctionLib::getOption(array(''=>''.FunctionLib::controLanguage('all',$this->languageSite).'')+$this->arrManager_station, (isset($dataSearch['station_account1']) && $dataSearch['station_account1'] !=""?$dataSearch['station_account1']:0));
        $optionUser_customer = FunctionLib::getOption(array(''=>''.FunctionLib::controLanguage('all',$this->languageSite).'')+$this->arrManager_customer, (isset($dataSearch['station_account2']) && $dataSearch['station_account2']!=""?$dataSearch['station_account2']:0));
        $optionTypeReort = FunctionLib::getOption($this->arrTypeReport, (isset($dataSearch['type_report'])?$dataSearch['type_report']:"1"));

        $optionYear = FunctionLib::getOption($arrYear, (isset($dataSearch['year']) ? $dataSearch['year'] : $current_year));
        $optionCarrier = FunctionLib::getOption(array('' => '' . FunctionLib::controLanguage('all', $this->languageSite) . '') + $arrCarrier, (isset($dataSearch['carrier_id']) ? $dataSearch['carrier_id'] : 0));
        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSMSMonthReportChart.view', array_merge([
            'data' => $data,
            'search' => $dataSearch,
            'optionUser_station' => $optionUser_station,
            'optionUser_customer' => $optionUser_customer,
            'optionYear' => $optionYear,
            'optionCarrier' => $optionCarrier,
            'arr_month_report' => $arr_month_report,
            'optionTypeReort'=>$optionTypeReort,
        ], $this->viewPermission));
    }
}