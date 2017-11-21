<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\SystemSetting;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class AdminSystemSettingController extends BaseAdminController
{
    private $permission_view = 'systemSetting_view';
    private $permission_full = 'systemSetting_full';
    private $permission_delete = 'systemSetting_delete';
    private $permission_create = 'systemSetting_create';
    private $permission_edit = 'systemSetting_edit';

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
        $page_no = (int) Request::get('page_no',1);
        $sbmValue = Request::get('submit', 1);
        $dataSearch['time_check_connect'] = addslashes(Request::get('time_check_connect',''));
        $dataSearch['active'] = (int)Request::get('active',-1);

        $limit = CGlobal::number_limit_show;
        $total = 0;
        $offset = ($page_no - 1) * $limit;
        $data = SystemSetting::searchByCondition($dataSearch, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getNewPager(3,$page_no,$total,$limit,$dataSearch) : '';

//        FunctionLib::debug($this->arrRuleString);
        $this->getDataDefault();
        $optionRuleString = FunctionLib::getOption($this->arrRuleString, (isset($data['concatenation_rule'])?$data['concatenation_rule']:CGlobal::concatenation_rule_first));

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSystemSetting.view',array_merge([
            'data'=>$data,
            'search'=>$dataSearch,
            'size'=>$total,
            'start'=>($page_no - 1) * $limit,
            'paging'=>$paging,
            'optionRuleString'=>$optionRuleString,
        ],$this->viewPermission));
    }

    public function getItem($ids) {
        $id = FunctionLib::outputId($ids);
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $data = array();
        if($id > 0) {
            $data = SystemSetting::find($id);
        }
        //FunctionLib::debug($data);
        $optionRuleString = FunctionLib::getOption($this->arrRuleString, (isset($data['concatenation_rule'])?$data['concatenation_rule']:CGlobal::concatenation_rule_first));
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSystemSetting.add',array_merge([
            'data'=>$data,
            'id'=>$id,
            'optionRuleString'=>$optionRuleString,
        ],$this->viewPermission));
    }

    public function postItem($ids) {
        $id = FunctionLib::outputId($ids);
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $id_hiden = (int)Request::get('id_hiden', 0);
        $data = $_POST;
        $data['updated_date'] = date("Y/m/d H:i",time());
        if($this->valid($data) && empty($this->error)) {
            $id = ($id == 0)?$id_hiden: $id;
            if($id > 0) {
                //cap nhat
                if(SystemSetting::updateItem($id, $data)) {
                    return Redirect::route('admin.systemSettingView');
                }
            }else{
                $data['created_date']=$data['updated_date'];
                //them moi
                if(SystemSetting::createItem($data)) {
                    return Redirect::route('admin.systemSettingView');
                }
            }
        }

        $optionRuleString = FunctionLib::getOption($this->arrRuleString, isset($data['active'])? $data['active']: CGlobal::status_show);
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSystemSetting.add',array_merge([
            'data'=>$data,
            'id'=>$id,
            'error'=>$this->error,
            'optionRuleString'=>$optionRuleString,
        ],$this->viewPermission));
    }

    public function deleteSystemSetting()
    {
        $data = array('isIntOk' => 0);
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_delete,$this->permission)){
            return Response::json($data);
        }
        $id = (int)Request::get('id', 0);
        if ($id > 0 && SystemSetting::deleteItem($id)) {
            $data['isIntOk'] = 1;
        }
        return Response::json($data);
    }

    public function getInfoEdit(){
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $total = 0;
        $data = SystemSetting::searchByCondition(array("field_get"=>"system_setting_id,system_content,system_content_en"),1,0,$total);
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminDashBoard.add',array_merge([
            'data'=>$data,
            'lang'=>$this->languageSite,
            'id'=>$data[0]['system_setting_id'],
        ],$this->viewPermission));
    }
    public function postInfoEdit($ids){
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        FunctionLib::debug($ids);
        $total = 0;
        $data = SystemSetting::searchByCondition(array("field_get"=>"system_setting_id,system_content,system_content_en"),1,0,$total);
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminDashBoard.add',array_merge([
            'data'=>$data,
            'lang'=>$this->languageSite,
            'id'=>$data[0]['system_setting_id'],
        ],$this->viewPermission));
    }
    private function valid($data=array()) {
        if(!empty($data)) {
            if(isset($data['time_check_connect']) && trim($data['time_check_connect']) == '') {
                $this->error[] = '* '.FunctionLib::controLanguage('time_check_connect').' '.FunctionLib::controLanguage('is_require');
            }
        }
        return true;
    }
}
