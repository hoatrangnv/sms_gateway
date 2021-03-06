<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\DeviceToken;
use App\Http\Models\User;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class AdminDeviceTokenController extends BaseAdminController
{
    private $permission_view = 'deviceToken_view';
    private $permission_full = 'deviceToken_full';
    private $permission_delete = 'deviceToken_delete';
    private $permission_create = 'deviceToken_create';
    private $permission_edit = 'deviceToken_edit';

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
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_delete'=>in_array($this->permission_delete, $this->permission) ? 1 : 0,
            'permission_full'=>in_array($this->permission_full, $this->permission) ? 1 : 0,
            'user_role_type'=> $this->role_type,
        ];
    }

    public function view() {
        //Check phan quyen.
        if(!$this->is_root && !in_array($this->permission_full,$this->permission)&& !in_array($this->permission_view,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $page_no = (int) Request::get('page_no',1);

        if($this->role_type == Define::ROLE_TYPE_SUPER_ADMIN){
            $dataSearch['user_id'] = (int)Request::get('user_id');
            $optionUser = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('select_user',$this->languageSite).'---')+$this->arrManager, (isset($dataSearch['user_id'])?$dataSearch['user_id']:0));
        }else{
            $dataSearch['user_id'] = $this->user_id;
            $arr = array(
                $this->user_id=>$this->user['user_name'].' - '.$this->user['user_full_name']
            );
            $optionUser = FunctionLib::getOption($arr,$this->user_id);
        }
        $limit = CGlobal::number_limit_show;
        $total = 0;
        $offset = ($page_no - 1) * $limit;
        $data = DeviceToken::searchByCondition($dataSearch, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getNewPager(3,$page_no,$total,$limit,$dataSearch) : '';
        $ids = array();
        foreach ($data as $k=>$v){
            $ids[$v['user_id']] = $v['user_id'];
        }
        $sql_user = "select user_id,user_name,user_full_name FROM ".Define::TABLE_USER." WHERE user_id in (".implode(",",$ids).") ";
        $arr_info_user = FunctionLib::executesSQL($sql_user);
        foreach ($arr_info_user as $k =>$v){
            $arr_info_user[$arr_info_user[$k]->user_id] = (array)$v;
            unset($arr_info_user[$k]);
        }
        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
//        FunctionLib::debug(array_key_exists("5",$arr_info_user));
        return view('admin.AdminDeviceToken.view',array_merge([
            'data'=>$data,
            'search'=>$dataSearch,
            'size'=>$total,
            'start'=>($page_no - 1) * $limit,
            'paging'=>$paging,
            'arrUser'=>$this->arrManager,
            'optionUser'=>$optionUser,
            'arr_info_user'=>$arr_info_user,
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

    public function postItem($ids) {
        $id = FunctionLib::outputId($ids);
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $id_hiden = (int)Request::get('id_hiden', 0);
        $data = $_POST;
        $data['updated_date'] = date("Y/m/d H:i",time());
        $optionUser = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('select_user',$this->languageSite).'---')+$this->arrManager, (isset($data['user_id'])?$data['user_id']:0));
        $optionStatus = FunctionLib::getOption($this->arrStatus, (isset($data['status'])?$data['status']:CGlobal::active));
        if($this->valid($data) && empty($this->error)) {
            $id = ($id == 0)?$id_hiden: $id;
            if($id > 0) {
                //cap nhat
                if(DeviceToken::updateItem($id, $data)) {
                    return Redirect::route('admin.deviceTokenView');
                }
            }else{
                $data['created_date']=$data['updated_date'];
                //them moi
                if(DeviceToken::createItem($data)) {
                    return Redirect::route('admin.deviceTokenView');
                }
            }
        }

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminDeviceToken.add',array_merge([
            'data'=>$data,
            'id'=>$id,
            'optionUser'=>$optionUser,
            'optionStatus'=>$optionStatus,
            'error'=>$this->error,
        ],$this->viewPermission));
    }

    public function deleteDeviceToken()
    {
        $data = array('isIntOk' => 0);
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_delete,$this->permission)){
            return Response::json($data);
        }
        $id = (int)Request::get('id', 0);
        if ($id > 0 && DeviceToken::deleteItem($id)) {
            $data['isIntOk'] = 1;
        }
        return Response::json($data);
    }



    public function valid($data=array()) {
        $arr_require = array(
            array("key_input"=>$data['user_id'],"label"=>FunctionLib::controLanguage('acc',$this->languageSite)),
            array("key_input"=>$data['device_code'],"label"=>FunctionLib::controLanguage('device_code',$this->languageSite)),
            array("key_input"=>$data['token'],"label"=>FunctionLib::controLanguage('token',$this->languageSite)),
            array("key_input"=>$data['messeger_center'],"label"=>FunctionLib::controLanguage('messeger_center',$this->languageSite)),
            array("key_input"=>$data['status'],"label"=>FunctionLib::controLanguage('status',$this->languageSite)),

        );
        FunctionLib::check_require($arr_require,$this->error);
        return true;
    }
}
