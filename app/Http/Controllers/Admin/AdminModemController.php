<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\DeviceToken;
use App\Http\Models\User;
use App\Http\Models\Modem;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class AdminModemController extends BaseAdminController
{
    private $permission_view = 'modem_view';
    private $permission_full = 'modem_full';
    private $permission_delete = 'modem_delete';
    private $permission_create = 'modem_create';
    private $permission_edit = 'modem_edit';

    private $arrManager = array();
    private $arrDevice = array();
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
        $this->arrManager = User::getOptionUserFullName(2);
        $this->arrDevice = DeviceToken::getOptionDevice();

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
                $this->user_id=>$this->user['user_full_name'].' - '.$this->user['user_email']
            );
            $optionUser = FunctionLib::getOption($arr,$this->user_id);
        }

//        $dataSearch['station_account'] = addslashes(Request::get('station_account',''));

        $limit = CGlobal::number_limit_show;
        $total = 0;
        $offset = ($page_no - 1) * $limit;
        $data = Modem::searchByCondition($dataSearch, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getNewPager(3,$page_no,$total,$limit,$dataSearch) : '';
        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
//        $optionUser = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('select_user',$this->languageSite).'---')+$this->arrManager, (isset($dataSearch['station_account'])?$dataSearch['station_account']:0));
        return view('admin.AdminModem.view',array_merge([
            'data'=>$data,
            'search'=>$dataSearch,
            'size'=>$total,
            'start'=>($page_no - 1) * $limit,
            'paging'=>$paging,
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
            $data = Modem::find($id);
        }
        $optionUser = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('select_user',$this->languageSite).'---')+$this->arrManager, (isset($data['user_id'])?$data['user_id']:0));
        $optionDevice = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('select_device',$this->languageSite).'---')+$this->arrDevice, (isset($data['device_id'])?$data['device_id']:0));
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminModem.add',array_merge([
            'data'=>$data,
            'id'=>$id,
            'optionUser'=>$optionUser,
            'optionDevice'=>$optionDevice,
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
        $optionDevice = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('select_device',$this->languageSite).'---')+$this->arrDevice, (isset($data['device_id'])?$data['device_id']:0));
        if($this->valid($data) && empty($this->error)) {
            $id = ($id == 0)?$id_hiden: $id;
            if($id > 0) {
                //cap nhat
                if(Modem::updateItem($id, $data)) {
                    return Redirect::route('admin.modemView');
                }
            }else{
                $data['created_date']=$data['updated_date'];
                //them moi
                if(Modem::createItem($data)) {
                    return Redirect::route('admin.modemView');
                }
            }
        }

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminModem.add',array_merge([
            'data'=>$data,
            'id'=>$id,
            'optionUser'=>$optionUser,
            'optionDevice'=>$optionDevice,
            'error'=>$this->error,
        ],$this->viewPermission));
    }

    public function deleteSystemSetting()
    {
        $data = array('isIntOk' => 0);
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_delete,$this->permission)){
            return Response::json($data);
        }
        $id = (int)Request::get('id', 0);
        if ($id > 0 && CarrierSetting::deleteItem($id)) {
            $data['isIntOk'] = 1;
        }
        return Response::json($data);
    }



    public function valid($data=array()) {
//        $arr_require = array(
//            array("key_input"=>$data['user_id'],"label"=>FunctionLib::controLanguage('acc',$this->languageSite)),
//            array("key_input"=>$data['device_code'],"label"=>FunctionLib::controLanguage('device_code',$this->languageSite)),
//            array("key_input"=>$data['token'],"label"=>FunctionLib::controLanguage('token',$this->languageSite)),
//            array("key_input"=>$data['messeger_center'],"label"=>FunctionLib::controLanguage('messeger_center',$this->languageSite)),
//            array("key_input"=>$data['status'],"label"=>FunctionLib::controLanguage('status',$this->languageSite)),
//
//        );
//        FunctionLib::check_require($arr_require,$this->error);
        return true;
    }
}
