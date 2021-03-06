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

class AdminSMSReportChartController extends BaseAdminController
{
    private $permission_view = 'smsReportChart_view';
    private $permission_full = 'smsReportChart_full';
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
        $this->arrManager = User::getOptionUserFullMail();
        $this->arrStatus = array(
            CGlobal::active => FunctionLib::controLanguage('active', $this->languageSite),
            CGlobal::not_active => FunctionLib::controLanguage('not_active', $this->languageSite)
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
    {//Check phan quyen.
        if (!$this->is_root && !in_array($this->permission_full, $this->permission) && !in_array($this->permission_view, $this->permission)) {
            return Redirect::route('admin.dashboard', array('error' => Define::ERROR_PERMISSION));
        }

        $dataSearch['month'] = addslashes(Request::get('month', ''));
        $dataSearch['year'] = addslashes(Request::get('year', ''));
        $dataSearch['carrier_id'] = addslashes(Request::get('carrier_id', ''));
        $month = date('m', time());
        $year = date('Y', time());
        if ($dataSearch['month'] != "" && $dataSearch['year'] != "") {
            $month = $dataSearch['month'];
            $year = $dataSearch['year'];
        }

        if ($this->role_type == Define::ROLE_TYPE_SUPER_ADMIN) {
            $dataSearch['user_id'] = addslashes(Request::get('station_account', ''));
        } else {
            $dataSearch['user_id'] = $this->user_id;
        }

        $arrCarrier = CarrierSetting::getOptionCarrier();

        $number_day_of_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $arrDay = array();
        for ($i = 1; $i <= $number_day_of_month; $i++) {
            $arrDay[] = $i;
        }

        $year_range = date('Y', time()) - date("Y", strtotime("-10 year"));
        $current_year = date("Y", time());
        $current_month = date("m", time());
        $last_10_year = date("Y", strtotime("-10 year"));
        $arrYear = array();
        $arrMonth = array();
        for ($i = $current_year; $i >= $last_10_year; $i--) {
            $arrYear[$i] = $i;
        }
        for ($i = 12; $i >= 1; $i--) {
            $arrMonth[$i] = $i;
        }

        $sql_where = " wsr.month=" . $month . " AND wsr.year=" . $year . " ";
        if (isset($dataSearch['carrier_id']) && $dataSearch['carrier_id'] > 0 && $dataSearch['carrier_id'] != "") {
            $sql_where .= "AND wsr.carrier_id=" . $dataSearch['carrier_id'];
        }

        $data = array();
        if (isset($dataSearch['user_id']) && $dataSearch['user_id'] > 0 && $dataSearch['user_id'] != "") {
            $sql_where .= " AND wsr.user_id=" . $dataSearch['user_id'];
            $sql = "SELECT SUM(wsr.cost) total_cost,wsr.sms_report_id,wcs.carrier_setting_id,wsr.hour,wsr.day,wsr.month,sum(wsr.success_number) as num_mess,wsr.user_id,wcs.carrier_name from web_sms_report wsr 
                INNER JOIN web_carrier_setting wcs ON wsr.carrier_id = wcs.carrier_setting_id 
                WHERE {$sql_where} 
                GROUP BY wsr.carrier_id,wsr.day,wsr.month,wsr.hour,wsr.year,wsr.user_id
";
            $data = SmsReport::executesSQL($sql);
        }


        foreach ($data as $k => $v) {
            $data[$k] = (array)$v;
        }

        $arrData = array();
        $arrData_pie = array();
        foreach ($data as $k => $v) {
            foreach ($arrDay as $v1) {
                if ($v['day'] == $v1) {
                    $arrData[$v['carrier_name']][$v1] = $v['num_mess'];
                    $arrData_pie[$v['carrier_name']][$v1]['num'] = $v['num_mess'];
                    $arrData_pie[$v['carrier_name']][$v1]['cost'] = $v['total_cost'];
                }
            }
        }
//        FunctionLib::debug($arrData);
        foreach ($arrDay as $d1) {
            foreach ($arrData as $k => $v) {
                if (!array_key_exists($d1, $v)) {
                    $arrData[$k][$d1] = 0;
                    $arrData_pie[$k][$d1]['num'] = 0;
                    $arrData_pie[$k][$d1]['cost'] = 0;
                }
            }
        }
        $arrPieChart = array();

        foreach ($arrData_pie as $k => $v) {
            ksort($v);
            $arrData_pie[$k] = $v;
            $total_num = 0;
            $total_cost = 0;
            foreach ($v as $item) {
                $total_num += $item['num'];
                $total_cost += $item['cost'];
            }
            $arrPieChart[$k]['num'] = $total_num;
            $arrPieChart[$k]['cost'] = $total_cost;
        }
        foreach ($arrData as $k => $v) {
            ksort($v);
            $arrData[$k] = $v;
        }
        $arrPieChart1 = array();
        $total_num_pie = 0;
        $total_cost_pie = 0;
        foreach ($arrPieChart as $k => $v) {
            $total_num_pie += $v['num'];
            $total_cost_pie += $v['cost'];
            $arrPieChart1[] = array(
                "name" => $k,
                "percent" => $v['num'],
                "total_cost" => $v['cost'],
                "sliced" => "false",
                "selected" => "false"
            );
            /*
                        if (max($arrPieChart) == $v) {
                            $arrPieChart1[] = array(
                                "name" => $k,
                                "percent" => $v,
                                "sliced" => "true",
                                "selected" => "true"
                            );
                        } else {
                            $arrPieChart1[] = array(
                                "name" => $k,
                                "percent" => $v,
                                "sliced" => "false",
                                "selected" => "false"
                            );
                        }*/
        }
//        FunctionLib::debug($arrPieChart1);
        $dataSearch['station_account'] = addslashes(Request::get('station_account', ''));
        $total = 0;
        $optionUser = FunctionLib::getOption(array('' => '' . FunctionLib::controLanguage('select_user', $this->languageSite) . '') + $this->arrManager, (isset($dataSearch['station_account']) ? $dataSearch['station_account'] : 0));
        $optionYear = FunctionLib::getOption($arrYear, (isset($dataSearch['year']) ? $dataSearch['year'] : $current_year));
        $optionMonth = FunctionLib::getOption($arrMonth, (isset($dataSearch['month']) && $dataSearch['month'] !="" ? $dataSearch['month'] : $current_month));
        $optionCarrier = FunctionLib::getOption(array('' => '' . FunctionLib::controLanguage('all', $this->languageSite) . '') + $arrCarrier, (isset($dataSearch['carrier_id']) ? $dataSearch['carrier_id'] : 0));
        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
//        FunctionLib::debug($arrData);

        foreach ($arrData as $k => $value){
            foreach ($value as $k_car => $item_car){
                if ($k_car>1){
                    $value[$k_car] = $value[$k_car-1] + $item_car;
                }
            }
            $arrData[$k] = $value;
        }
//        FunctionLib::debug($dataSearch['month']);
        return view('admin.AdminSMSReportChart.view', array_merge([
            'data' => $data,
            'arrDay' => $arrDay,
            'arrData' => $arrData,
            'arrPieChart' => $arrPieChart1,
            'search' => $dataSearch,
            'size' => $total,
            'optionUser' => $optionUser,
            'optionYear' => $optionYear,
            'optionMonth' => $optionMonth,
            'optionCarrier' => $optionCarrier,
            'total_num_pie' => $total_num_pie,
            'total_cost_pie' => $total_cost_pie,
            'title_line_chart' => FunctionLib::controLanguage('report', $this->languageSite) . ' ' . $month . '/' . $year,
        ], $this->viewPermission));
    }

}