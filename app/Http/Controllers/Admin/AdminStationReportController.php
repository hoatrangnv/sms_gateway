<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\ModemCom;
use App\Http\Models\Modem;
use App\Http\Models\User;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class AdminStationReportController extends BaseAdminController
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
//            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
//            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
//            'permission_delete'=>in_array($this->permission_delete, $this->permission) ? 1 : 0,
            'permission_full'=>in_array($this->permission_full, $this->permission) ? 1 : 0,
        ];
    }

    public function view() {
        //Check phan quyen.
        if(!$this->is_root && !in_array($this->permission_full,$this->permission)&& !in_array($this->permission_view,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $page_no = (int) Request::get('page_no',1);
        $sbmValue = Request::get('submit', 1);
        $dataSearch['station_account'] = addslashes(Request::get('station_account',''));
        $total = 0;
        $sql = "SELECT web_modem.modem_id,sum(web_modem_com.error_number) as total_err,sum(web_modem_com.success_number) as total_succ,web_modem.modem_name,web_modem.modem_type,web_modem.updated_date,web_modem.digital,web_modem.is_active,web_user.user_name FROM web_modem INNER JOIN web_modem_com ON web_modem_com.modem_id = web_modem.modem_id 
INNER JOIN web_user ON web_modem.user_id = web_user.user_id ";

        if (isset($dataSearch['station_account']) && $dataSearch['station_account'] >0){
            $sql.= " WHERE web_modem.user_id = ".$dataSearch['station_account']." 
GROUP BY web_modem.modem_id 
ORDER BY web_modem.modem_id DESC";
        }else{
            $sql.= " GROUP BY web_modem.modem_id 
ORDER BY web_modem.modem_id DESC";
        }

        $data = Modem::executesSQL($sql);
        $arr = array();
        foreach ($data as $k => $v){
            $arr[] = (array) $v;
        }
        $optionUser = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('select_user',$this->languageSite).'---')+$this->arrManager, (isset($dataSearch['station_account'])?$dataSearch['station_account']:0));
        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminStationReport.view',array_merge([
            'data'=>$arr,
            'search'=>$dataSearch,
            'size'=>$total,
            'optionUser'=>$optionUser,
        ],$this->viewPermission));
    }

    public function getItem($ids) {
        $id = FunctionLib::outputId($ids);
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $data = array();
        if($id > 0) {
            $data = DeviceToken::find($id);
        }
        $optionUser = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('select_user',$this->languageSite).'---')+$this->arrManager, (isset($data['user_id'])?$data['user_id']:0));
        $optionStatus = FunctionLib::getOption($this->arrStatus, (isset($data['status'])?$data['status']:CGlobal::active));
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminDeviceToken.add',array_merge([
            'data'=>$data,
            'id'=>$id,
            'optionUser'=>$optionUser,
            'optionStatus'=>$optionStatus,
        ],$this->viewPermission));
    }

}
