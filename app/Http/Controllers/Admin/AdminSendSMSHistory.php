<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\SmsCustomer;
use App\Http\Models\SmsSendTo;
use App\Http\Models\User;
use App\Http\Models\CarrierSetting;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class AdminSendSMSHistory extends BaseAdminController
{
    private $permission_view = 'sendSmsHistory_view';
    private $permission_full = 'sendSmsHistory_full';
    private $permission_delete = 'sendSmsHistory_delete';
    private $permission_create = 'sendSmsHistory_create';
    private $permission_edit = 'sendSmsHistory_edit';

    private $arrMenuParent = array();
    private $arrStatus = array();
    private $arrCarrier = array();
    private $arrUser = array();
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

        $this->arrStatus = array(
            ''=>FunctionLib::controLanguage('all',$this->languageSite),
            '0'=>'Processing',
            '1'=>'Successful'
        );

        $this->arrCarrier = CarrierSetting::getOptionCarrier();

        $this->arrUser = User::getOptionUserFullName();
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

//        FunctionLib::debug($this->arrUser);
        if ($this->role_type == Define::ROLE_TYPE_SUPER_ADMIN){
            $dataSearch['user_id'] = addslashes(Request::get('user_id',''));
            $optionUser = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('select_user',$this->languageSite).'---')+$this->arrUser,isset($dataSearch['user_id'])&& $dataSearch['user_id']>0?$dataSearch['user_id']:0);
        }else{
            $dataSearch['user_id'] = $this->user_id;
            $arr = array(
                $this->user_id=>$this->user['user_name'].' - '.$this->user['user_full_name']
            );
            $optionUser = FunctionLib::getOption($arr,$this->user_id);
        }

        $dataSearch['status'] = addslashes(Request::get('status',''));
        $dataSearch['from_day'] = addslashes(Request::get('from_day',''));
        $dataSearch['to_day'] = addslashes(Request::get('to_day',''));

        $limit = CGlobal::number_limit_show;
        $total = 0;
        $offset = ($page_no - 1) * $limit;
//        $data = SmsCustomer::searchByCondition($dataSearch, $limit, $offset, $total);

        $sql = "SELECT u.user_full_name,wsc.sms_deadline,(wsc.correct_number + wsc.incorrect_number) as total_sms,wsc.incorrect_number,wsc.cost,wsc.status,wsc.status_name,wsc.sms_customer_id,SUM(IFNULL(send_successful,0)) total_success from web_sms_customer wsc
LEFT JOIN web_sms_log wsl ON wsc.sms_customer_id = wsl.sms_customer_id 
INNER JOIN web_user u on wsc.user_customer_id = u.user_id ";

        if ($dataSearch['user_id'] !="" && $dataSearch['user_id'] >0){
            $sql.=" WHERE wsc.user_customer_id=".$dataSearch['user_id'];
        }

        if ($dataSearch['status'] !=""){
            $sql.=" AND wsc.status=".$dataSearch['status'];
        }

        if ($dataSearch['from_day'] !="" && $dataSearch['to_day'] != ""){
            $from = date('Y-m-d',strtotime($dataSearch['from_day']));
            $to = date('Y-m-d',strtotime($dataSearch['to_day']));
            $sql.=" AND UNIX_TIMESTAMP(wsc.created_date) >= UNIX_TIMESTAMP('".$from."') AND UNIX_TIMESTAMP(wsc.created_date) <= UNIX_TIMESTAMP('".$to."') ";
        }

        $sql.=" GROUP BY wsc.sms_customer_id";
//        FunctionLib::debug($sql);
        $data = SmsCustomer::executesSQL($sql);
        $arr = array();
        foreach ($data as $k => $v){
            $arr[] = (array) $v;
        }
//        FunctionLib::debug($arr);
        $paging = $total > 0 ? Pagging::getNewPager(3,$page_no,$total,$limit,$dataSearch) : '';

        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
//        $optionUser = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('select_user',$this->languageSite).'---')+$this->arrUser,isset($dataSearch['user_id'])&& $dataSearch['user_id']>0?$dataSearch['user_id']:0);
        $optionStatus = FunctionLib::getOption($this->arrStatus,isset($dataSearch['status'])&& $dataSearch['status']>0?$dataSearch['status']:'');
        return view('admin.AdminSendSMSHistory.view',array_merge([
            'data'=>$arr,
            'search'=>$dataSearch,
            'size'=>$total,
            'start'=>($page_no - 1) * $limit,
            'paging'=>$paging,
            'optionStatus'=>$optionStatus,
            'arrStatus'=>$this->arrStatus,
            'optionUser'=>$optionUser,
            'from_day'=>date('m/d/Y',strtotime(date('Y-m-d',time()).'- 6 month')),
            'to_day'=>date('m/d/Y',time()),
//            'optionRuleString'=>$optionRuleString,
        ],$this->viewPermission));
    }

    public function viewDetails() {
        $id_cs = isset($_GET['id_cs']) && $_GET['id_cs']>0 ?$_GET['id_cs']:0;
        //Check phan quyen.
        if(!$this->is_root && !in_array($this->permission_full,$this->permission)&& !in_array($this->permission_view,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $page_no = (int) Request::get('page_no',1);
        $limit = CGlobal::number_limit_show;
        $total = 0;
        $offset = ($page_no - 1) * $limit;

//        FunctionLib::debug($id_cs);
        if ($id_cs <=0){
            return Redirect::route('admin.smsHistoryView');
        }
        $dataSearch['id_cs'] = $id_cs;
        $sbmValue = Request::get('submit', 1);
        $dataSearch['carrier_id'] = addslashes(Request::get('carrier_id',''));
        $dataSearch['status'] = addslashes(Request::get('status',''));
        $dataSearch['from_day'] = addslashes(Request::get('from_day',''));
        $dataSearch['to_day'] = addslashes(Request::get('to_day',''));

        if ($dataSearch['from_day'] !=""){
            $dataSearch['from_day'] = date('Y-m-d',strtotime($dataSearch['from_day']));
        }
        if ($dataSearch['to_day'] !=""){
            $dataSearch['to_day'] = date('Y-m-d',strtotime($dataSearch['to_day']));
        }
        $data = SmsSendTo::joinByCondition($dataSearch, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getNewPager(3,$page_no,$total,$limit,$dataSearch) : '';

        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
        $optionCarrier = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('choose_carrier',$this->languageSite).'---')+$this->arrCarrier,isset($dataSearch['carrier_id'])&& $dataSearch['carrier_id']>0?$dataSearch['carrier_id']:0);
        $optionStatus = FunctionLib::getOption($this->arrStatus,isset($dataSearch['status'])&& $dataSearch['status']>0?$dataSearch['status']:'');
        $incorrect_number_list = isset($data[0])?$data[0]['incorrect_number_list']:"";
        return view('admin.AdminSendSMSHistory.viewdetails',array_merge([
            'data'=>$data,
            'search'=>$dataSearch,
            'size'=>$total,
            'start'=>($page_no - 1) * $limit,
            'paging'=>$paging,
            'optionStatus'=>$optionStatus,
            'arrStatus'=>$this->arrStatus,
            'optionCarrier'=>$optionCarrier,
            'incorrect_number_list'=>$incorrect_number_list,
            'id_cs'=>$id_cs,
            'from_day'=>date('m/d/Y',strtotime(date('Y-m-d',time()).'- 6 month')),
            'to_day'=>date('m/d/Y',time()),
//            'optionRuleString'=>$optionRuleString,
        ],$this->viewPermission));
    }

    public function getItem($ids) {
        $id = FunctionLib::outputId($ids);
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $data = array();
        if($id > 0) {
            $data = CarrierSetting::find($id);
        }
//        $optionRuleString = FunctionLib::getOption($this->arrRuleString, (isset($data['concatenation_rule'])?$data['concatenation_rule']:CGlobal::concatenation_rule_first));
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminCarrierSetting.add',array_merge([
            'data'=>$data,
            'id'=>$id,
//            'optionRuleString'=>$optionRuleString,
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
                if(CarrierSetting::updateItem($id, $data)) {
                    return Redirect::route('admin.carrierSettingView');
                }
            }else{
                $data['created_date']=$data['updated_date'];
                //them moi
                if(CarrierSetting::createItem($data)) {
                    return Redirect::route('admin.carrierSettingView');
                }
            }
        }

//        $optionRuleString = FunctionLib::getOption($this->arrRuleString, isset($data['active'])? $data['active']: CGlobal::status_show);
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminCarrierSetting.add',array_merge([
            'data'=>$data,
            'id'=>$id,
            'error'=>$this->error,
//            'optionRuleString'=>$optionRuleString,
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
        $arr_require = array(
            array("key_input"=>$data['carrier_name'],"label"=>FunctionLib::controLanguage('carrier_name',$this->languageSite)),
            array("key_input"=>$data['slipt_number'],"label"=>FunctionLib::controLanguage('slipt_number',$this->languageSite)),
            array("key_input"=>$data['min_number'],"label"=>FunctionLib::controLanguage('min_number',$this->languageSite)),
            array("key_input"=>$data['max_number'],"label"=>FunctionLib::controLanguage('max_number',$this->languageSite)),

        );
        FunctionLib::check_require($arr_require,$this->error);
        return true;
    }
}
