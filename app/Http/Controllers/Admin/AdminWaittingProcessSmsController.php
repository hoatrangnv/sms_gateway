<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\SmsLog;
use App\Http\Models\User;
use App\Http\Models\CarrierSetting;
use App\Http\Models\SmsSendTo;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class AdminWaittingProcessSmsController extends BaseAdminController
{
    private $permission_view = 'waittingSms_view';
    private $permission_full = 'waittingSms_full';
    private $permission_delete = 'waittingSms_delete';
    private $permission_create = 'waittingSms_create';
    private $permission_edit = 'waittingSms_edit';
    private $arrStatus = array();
    private $error = array();
    private $arrMenuParent = array();
    private $viewPermission = array();//check quyen
    private $infoListUser = array();
    private $arrCarrier = array();//check quyen

    public function __construct()
    {
        parent::__construct();
        CGlobal::$pageAdminTitle = 'SMS Waitting Process';
    }

    public function getDataDefault(){
        $this->infoListUser = User::getListUserNameFullName();
        $this->arrCarrier = CarrierSetting::getOptionCarrier();
    }

    public function getPermissionPage(){
        return $this->viewPermission = [
            'is_root'=> $this->is_root ? 1:0,
            'user_role_type'=> $this->role_type,
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
        $pageNo = (int) Request::get('page_no',1);
        $sbmValue = Request::get('submit', 1);
        $limit = CGlobal::number_show_30;
        $offset = ($pageNo - 1) * $limit;
        $search = $data = array();
        $total = 0;

        $search['carrier_id'] = (int)Request::get('carrier_id',-1);
        if($this->role_type == Define::ROLE_TYPE_SUPER_ADMIN){
            $search['user_customer_id'] = (int)Request::get('user_customer_id',-1);
        }else{
            $search['user_customer_id'] = $this->user_id;
        }
        //$search['field_get'] = 'menu_name,menu_id,parent_id';//cac truong can lay
        $data = SmsLog::searchByCondition($search, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getNewPager(3,$pageNo,$total,$limit,$search) : '';

        $this->getDataDefault();
        $optionCarrier = FunctionLib::getOption(array(-1=>'')+$this->arrCarrier, $search['carrier_id']);

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminWaittingProcessSms.view',array_merge([
            'data'=>$data,
            'search'=>$search,
            'total'=>$total,
            'stt'=>($pageNo - 1) * $limit,
            'paging'=>$paging,
            'optionCarrier'=>$optionCarrier,
            'infoListUser'=>$this->infoListUser,
        ],$this->viewPermission));
    }

    public function getItem($ids) {
        $sms_log_id = FunctionLib::outputId($ids);

        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $data = array();
        if($sms_log_id > 0) {
            $data = SmsSendTo::getListSmsSendToBySmsLogId($sms_log_id);
        }
        //FunctionLib::debug($data);
        $this->getDataDefault();
        $optionStatus = FunctionLib::getOption($this->arrStatus, isset($data['active'])? $data['active']: CGlobal::status_show);

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminWaittingProcessSms.add',array_merge([
            'data'=>$data,
            'id'=>$sms_log_id,
            'arrStatus'=>$this->arrStatus,
            'optionStatus'=>$optionStatus,
        ],$this->viewPermission));
    }

    public function postItem($ids) {
        $id = FunctionLib::outputId($ids);
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $id_hiden = (int)Request::get('id_hiden', 0);
        $data = $_POST;
        $data['ordering'] = (int)($data['ordering']);
        if($this->valid($data) && empty($this->error)) {
            $id = ($id == 0)?$id_hiden: $id;
            if($id > 0) {
                //cap nhat
                if(MenuSystem::updateItem($id, $data)) {
                    return Redirect::route('admin.menuView');
                }
            }else{
                //them moi
                if(MenuSystem::createItem($data)) {
                    return Redirect::route('admin.menuView');
                }
            }
        }

        $this->getDataDefault();
        $optionStatus = FunctionLib::getOption($this->arrStatus, isset($data['active'])? $data['active']: CGlobal::status_hide);
        $optionShowContent = FunctionLib::getOption($this->arrStatus, isset($data['showcontent'])? $data['showcontent']: CGlobal::status_show);
        $optionShowMenu = FunctionLib::getOption($this->arrStatus, isset($data['show_menu'])? $data['show_menu']: CGlobal::status_show);
        $optionShowPermission = FunctionLib::getOption($this->arrStatus, isset($data['show_permission'])? $data['show_permission']: CGlobal::status_hide);
        $optionMenuParent = FunctionLib::getOption($this->arrMenuParent, isset($data['parent_id'])? $data['parent_id'] : 0);

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminWaittingProcessSms.add',array_merge([
            'data'=>$data,
            'id'=>$id,
            'error'=>$this->error,
            'arrStatus'=>$this->arrStatus,
            'optionStatus'=>$optionStatus,
            'optionShowContent'=>$optionShowContent,
            'optionShowPermission'=>$optionShowPermission,
            'optionShowMenu'=>$optionShowMenu,
            'optionMenuParent'=>$optionMenuParent,
        ],$this->viewPermission));
    }

    public function deleteMenu()
    {
        $data = array('isIntOk' => 0);
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_delete,$this->permission)){
            return Response::json($data);
        }
        $id = (int)Request::get('id', 0);
        if ($id > 0 && MenuSystem::deleteItem($id)) {
            $data['isIntOk'] = 1;
        }
        return Response::json($data);
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
