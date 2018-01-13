<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\ModemCom;
use App\Http\Models\User;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class AdminStationListController extends BaseAdminController
{
    private $permission_view = 'stationList_view';
    private $permission_full = 'stationList_full';
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
        $this->arrManager = User::getOptionUserFullMail(2);
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

        if($this->role_type == Define::ROLE_TYPE_SUPER_ADMIN){
            $dataSearch['station_account'] = addslashes(Request::get('station_account',''));
            $optionUser = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('select_user',$this->languageSite).'---')+$this->arrManager, (isset($dataSearch['station_account'])?$dataSearch['station_account']:0));
        }else{
            $dataSearch['station_account'] = $this->user_id;
            $arr = array(
                $this->user_id=>$this->user['user_name'].' - '.$this->user['user_full_name']
            );
            $optionUser = FunctionLib::getOption($arr,$this->user_id);
        }
        $total = 0;
        $data = ModemCom::searchByCondition($dataSearch,$total);
        $data_by_modem = array();
        foreach ($data as $k => $v){
            $data_by_modem[$v['modem_name']]['list'][] = $v;
            $data_by_modem[$v['modem_name']]['user_name_view'] = $v['user_name'].' - '.$v['user_full_name'];
            $data_by_modem[$v['modem_name']]['status_content'] = $v['status_content'];
        }

        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminStationList.view',array_merge([
//            'data'=>$data,
            'data'=>$data_by_modem,
            'search'=>$dataSearch,
            'size'=>$total,
            'arrUser'=>$this->arrManager,
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
