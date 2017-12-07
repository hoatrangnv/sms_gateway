<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\User;
use App\Http\Models\UserSetting;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class AdminStationSettingController extends BaseAdminController
{
    private $permission_view = 'stationSetting_view';
    private $permission_full = 'stationSetting_full';
    private $permission_delete = 'stationSetting_delete';
    private $permission_create = 'stationSetting_create';
    private $permission_edit = 'stationSetting_edit';

    private $arrMenuParent = array();
    private $arrRuleString = array();
    private $error = array();
    private $viewPermission = array();//check quyen

    public function __construct()
    {
        parent::__construct();
        CGlobal::$pageAdminTitle = 'Quản lý menu';
        $this->getDataDefault();
    }

    public function getDataDefault(){
        $this->arrRuleString = array(
            CGlobal::concatenation_rule_first => FunctionLib::controLanguage('concatenation_rule_first',$this->languageSite),
            CGlobal::concatenation_rule_center => FunctionLib::controLanguage('concatenation_rule_center',$this->languageSite),
            CGlobal::concatenation_rule_end => FunctionLib::controLanguage('concatenation_rule_end',$this->languageSite));
    }

    public function getPermissionPage(){
        return $this->viewPermission = [
            'is_root'=> $this->is_root ? 1:0,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_delete'=>in_array($this->permission_delete, $this->permission) ? 1 : 0,
            'permission_full'=>in_array($this->permission_full, $this->permission) ? 1 : 0,
        ];
    }

    public function view() {
        //Check phan quyen.
        if(!$this->is_root && !in_array($this->permission_full,$this->permission)&& !in_array($this->permission_view,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }

        $user_id = User::user_id();
        $data = (array)UserSetting::getUserSettingByUserId('9');
        $optionRuleString = FunctionLib::getOption($this->arrRuleString, (isset($data['concatenation_rule'])?$data['concatenation_rule']:CGlobal::concatenation_rule_first));
        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminStationSetting.index',array_merge([
            'data'=>$data,
            'id'=>FunctionLib::inputId($data['user_setting_id']),
            'optionRuleString'=>$optionRuleString,
        ],$this->viewPermission));
    }

    public function postItem() {
        $data = $_POST;
        $id= (isset($data['id_hiden']))?FunctionLib::outputId($data['id_hiden']):0;

        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $data['updated_date'] = date("Y/m/d H:i",time());
        if($this->valid($data) && empty($this->error)) {
            if($id > 0) {
                //cap nhat
                if(UserSetting::updateItem($id, $data)) {
                    return Redirect::route('admin.stationSettingView');
                }
            }
//            else{
//                $data['created_date']=$data['updated_date'];
//                //them moi
//                if(UserSetting::createItem($data)) {
//                    return Redirect::route('admin.carrierSettingView');
//                }
//            }
        }

        $optionRuleString = FunctionLib::getOption($this->arrRuleString, (isset($data['concatenation_rule'])?$data['concatenation_rule']:CGlobal::concatenation_rule_first));
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSettingStation.index',array_merge([
            'data'=>$data,
            'id'=>$id,
            'error'=>$this->error,
            'optionRuleString'=>$optionRuleString,
        ],$this->viewPermission));
    }

    public function valid($data=array()) {
        $arr_require = array(

        );
        FunctionLib::check_require($arr_require,$this->error);
        return true;
    }
}