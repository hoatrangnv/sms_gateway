<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\MenuSystem;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class AdminSendSmsController extends BaseAdminController
{
    private $permission_view = 'sendSms_view';
    private $permission_full = 'sendSms_full';
    private $permission_delete = 'sendSms_delete';
    private $permission_create = 'sendSms_create';
    private $permission_edit = 'sendSms_edit';
    private $arrStatus = array();
    private $error = array();
    private $arrMenuParent = array();
    private $viewPermission = array();//check quyen

    public function __construct()
    {
        parent::__construct();
        $this->arrMenuParent = MenuSystem::getAllParentMenu();
        CGlobal::$pageAdminTitle = 'Send SMS';
    }

    public function getDataDefault(){
        $this->arrStatus = array(
            CGlobal::status_block => FunctionLib::controLanguage('status_choose',$this->languageSite),
            CGlobal::status_show => FunctionLib::controLanguage('status_show',$this->languageSite),
            CGlobal::status_hide => FunctionLib::controLanguage('status_hidden',$this->languageSite));
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

    public function getSendSms() {
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $data = array();

        $this->getDataDefault();
        $optionStatus = FunctionLib::getOption($this->arrStatus, isset($data['active'])? $data['active']: CGlobal::status_show);
        $optionShowContent = FunctionLib::getOption($this->arrStatus, isset($data['showcontent'])? $data['showcontent']: CGlobal::status_show);
        $optionShowPermission = FunctionLib::getOption($this->arrStatus, isset($data['show_permission'])? $data['show_permission']: CGlobal::status_hide);
        $optionShowMenu = FunctionLib::getOption($this->arrStatus, isset($data['show_menu'])? $data['show_menu']: CGlobal::status_show);
        $optionMenuParent = FunctionLib::getOption($this->arrMenuParent, isset($data['parent_id'])? $data['parent_id'] : 0);

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSendSms.add',array_merge([
            'data'=>$data,
            'id'=>0,
            'arrStatus'=>$this->arrStatus,
            'optionStatus'=>$optionStatus,
            'optionShowContent'=>$optionShowContent,
            'optionShowPermission'=>$optionShowPermission,
            'optionShowMenu'=>$optionShowMenu,
            'optionMenuParent'=>$optionMenuParent,
        ],$this->viewPermission));
    }

    public function postSendSms() {
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $id_hiden = (int)Request::get('id_hiden', 0);
        $data = $_POST;
        $data['ordering'] = (int)($data['ordering']);
        if($this->valid($data) && empty($this->error)) {

        }
        $this->getDataDefault();
        $optionStatus = FunctionLib::getOption($this->arrStatus, isset($data['active'])? $data['active']: CGlobal::status_hide);
        $optionShowContent = FunctionLib::getOption($this->arrStatus, isset($data['showcontent'])? $data['showcontent']: CGlobal::status_show);
        $optionShowMenu = FunctionLib::getOption($this->arrStatus, isset($data['show_menu'])? $data['show_menu']: CGlobal::status_show);
        $optionShowPermission = FunctionLib::getOption($this->arrStatus, isset($data['show_permission'])? $data['show_permission']: CGlobal::status_hide);
        $optionMenuParent = FunctionLib::getOption($this->arrMenuParent, isset($data['parent_id'])? $data['parent_id'] : 0);

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSendSms.add',array_merge([
            'data'=>$data,
            'id'=>0,
            'error'=>$this->error,
            'arrStatus'=>$this->arrStatus,
            'optionStatus'=>$optionStatus,
            'optionShowContent'=>$optionShowContent,
            'optionShowPermission'=>$optionShowPermission,
            'optionShowMenu'=>$optionShowMenu,
            'optionMenuParent'=>$optionMenuParent,
        ],$this->viewPermission));
    }

    private function valid($data=array()) {
        if(!empty($data)) {
            if(isset($data['banner_name']) && trim($data['banner_name']) == '') {
                $this->error[] = 'Null';
            }
        }
        return true;
    }
}
