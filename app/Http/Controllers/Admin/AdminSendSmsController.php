<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\CarrierSetting;
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
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSendSms.add',array_merge([
            'data'=>$data,
            'id'=>0,
            'arrStatus'=>$this->arrStatus,
        ],$this->viewPermission));
    }

    public function postSendSms() {
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $data = $_POST;

        //check số hợp lệ
        $dataSend = array();
        $dataPhone = array();
        $arr_numberFone = (trim($data['phone_number']) != '')? explode(',',trim($data['phone_number'])): array();
        if(!empty($arr_numberFone)){
            foreach ($arr_numberFone as $k =>$number){
                $checkNumber = $this->checkNumberFone($number);
                if($checkNumber > 0){
                    $dataPhone[] = trim($checkNumber);
                }else{
                    $this->error[] = trim($number).' not number phone';
                }
            }
        }else{
            $this->error[] = FunctionLib::controLanguage('phone_number',$this->languageSite).' null';
        }

        //đẩy dữ liệu theo nhà mạng
        if( empty($this->error)){
            $carrier = CarrierSetting::getInfoCarrier();
            $arrNumberCarries = array();
            foreach ($carrier as $kc=>$val_carr) {
                $number_firsr = (trim($val_carr['first_number']) != '') ? explode(',', $val_carr['first_number']) : array();
                if(!empty($number_firsr)){
                    foreach ($number_firsr as $kk =>$number_firsr_carr){
                        $arrNumberCarries[$number_firsr_carr] = array(
                            'first_number'=>$number_firsr_carr,
                            'carrier_id'=>$val_carr['carrier_setting_id'],
                            'carrier_name'=>$val_carr['carrier_name'],
                            'slipt_number'=>$val_carr['slipt_number'],
                            'min_number'=>$val_carr['min_number'],
                            'max_number'=>$val_carr['max_number']);
                    }
                }
            }
            //check số có phù hợp với nhà mạng
            if(!empty($carrier)){
                foreach ($dataPhone as $kkk=>$phone_number){
                    $lenghtNumber = strlen($phone_number);
                    foreach ($arrNumberCarries as $kk =>$dauso){
                        $pos = strpos(trim($phone_number), trim($dauso['first_number']));
                        if($pos === 0){
                            if($dauso['min_number'] >= $lenghtNumber || $lenghtNumber <= $dauso['max_number']){
                                $dataSend[trim($phone_number)] = array(
                                    'phone_number'=>$phone_number,
                                    'lenght'=>strlen($phone_number),
                                    'carrier_id'=>$dauso['carrier_id'],
                                    'carrier_name'=>$dauso['carrier_name']);
                            }else{
                                $this->error[] = trim($phone_number).' not valiable';
                            }
                        }
                    }
                    if(!empty($dataSend) && !in_array(trim($phone_number),array_keys($dataSend))){
                        $this->error[] = trim($phone_number).' not number first';
                    }
                }
            }
        }
        //FunctionLib::debug($dataSend);

        if($this->valid($data) && empty($this->error)) {
            FunctionLib::debug($dataSend);
            //web_sms_customer

            //web_sms_log: bao nhiêu nhà mạng thì co bấy nhiêu bản ghi
            //user_manager_id = 0;
        }

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSendSms.add',array_merge([
            'data'=>$data,
            'id'=>0,
            'error'=>$this->error,
            'arrStatus'=>$this->arrStatus,
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

    public function checkNumberFone($stringFone = ''){
        if(trim($stringFone) != ''){
            $stringFone = str_replace(' ', '', $stringFone);
            $stringFone = str_replace('-', '', $stringFone);
            $stringFone = str_replace('.', '', $stringFone);
            $pattern = '/^\d+$/';
            if (preg_match($pattern, $stringFone)) {
                return $stringFone;
            } else {
                return 0;
            }
        }
        return false;
    }
}
