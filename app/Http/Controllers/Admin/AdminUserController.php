<?php
/*
* @Created by: HSS
* @Author    : nguyenduypt86@gmail.com
* @Date      : 08/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\GroupUser;
use App\Http\Models\User;
use App\Http\Models\MenuSystem;
use App\Http\Models\UserSetting;
use App\Http\Models\CarrierSetting;
use App\Http\Models\UserCarrierSetting;

use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class AdminUserController extends BaseAdminController{
    private $permission_view = 'user_view';
    private $permission_create = 'user_create';
    private $permission_edit = 'user_edit';
    private $permission_change_pass = 'user_change_pass';
    private $permission_remove = 'user_remove';
    private $arrStatus = array();
    private $arrRoleType = array();
    private $arrSex = array();
    private $error = array();

    public function __construct(){
        parent::__construct();

    }

    public function getDataDefault(){
        $this->arrRoleType = Define::$arrUserRole;
        $this->arrStatus = array(
            CGlobal::status_hide => FunctionLib::controLanguage('status_all',$this->languageSite),
            CGlobal::status_show => FunctionLib::controLanguage('status_show',$this->languageSite),
            CGlobal::status_block => FunctionLib::controLanguage('status_block',$this->languageSite));
        $this->arrSex = array(
            CGlobal::status_hide => FunctionLib::controLanguage('sex_girl',$this->languageSite),
            CGlobal::status_show => FunctionLib::controLanguage('sex_boy',$this->languageSite));
    }
    public function view(){
        CGlobal::$pageAdminTitle  = "Quản trị User | Admin CMS";
        //check permission
        if (!$this->is_root && !in_array($this->permission_view, $this->permission)) {
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $page_no = Request::get('page_no', 1);
        $dataSearch['user_status'] = Request::get('user_status', 0);
        $dataSearch['user_email'] = Request::get('user_email', '');
        $dataSearch['user_full_name'] = Request::get('user_full_name', '');
        $dataSearch['user_name'] = Request::get('user_name', '');
        $dataSearch['user_group'] = Request::get('user_group', 0);
        $dataSearch['user_view'] = ($this->is_boss)? 1: 0;

        $limit = CGlobal::number_limit_show;
        $total = 0;
        $offset = ($page_no - 1) * $limit;
        $data = User::searchByCondition($dataSearch, $limit, $offset, $total);
        $arrGroupUser = GroupUser::getListGroupUser();

        $paging = $total > 0 ? Pagging::getNewPager(3,$page_no,$total,$limit,$dataSearch) : '';
        $this->getDataDefault();
        return view('admin.AdminUser.view',[
                'data'=>$data,
                'dataSearch'=>$dataSearch,
                'size'=>$total,
                'start'=>($page_no - 1) * $limit,
                'paging'=>$paging,
                'arrStatus'=>$this->arrStatus,
                'arrGroupUser'=>$arrGroupUser,
                'is_root'=>$this->is_root,
                'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
                'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
                'permission_change_pass'=>in_array($this->permission_change_pass, $this->permission) ? 1 : 0,
                'permission_remove'=>in_array($this->permission_remove, $this->permission) ? 1 : 0,
            ]);
    }

    //get
    public function editInfo($ids)
    {
        $id = FunctionLib::outputId($ids);
        CGlobal::$pageAdminTitle = "Sửa User | ".CGlobal::web_name;
//        //check permission
        if (!$this->is_root && !in_array($this->permission_edit, $this->permission)) {
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $arrUserGroupMenu = $data = array();
        if($id > 0){
            $data = User::getUserById($id);
            $data['user_group'] = explode(',', $data['user_group']);
            $arrUserGroupMenu = explode(',', $data['user_group_menu']);
        }

        $arrGroupUser = GroupUser::getListGroupUser($this->is_boss);
        $menuAdmin = MenuSystem::getListMenuPermission();
        //FunctionLib::debug($this->arrStatus);
        $this->getDataDefault();
        $optionStatus = FunctionLib::getOption($this->arrStatus, isset($data['user_status'])? $data['user_status']: CGlobal::status_show);
        $optionSex = FunctionLib::getOption($this->arrSex, isset($data['user_sex'])? $data['user_sex']: CGlobal::status_show);
        $optionRoleType = FunctionLib::getOption($this->arrRoleType, isset($data['role_type'])? $data['role_type']: Define::ROLE_TYPE_CUSTOMER);
        return view('admin.AdminUser.add',[
            'data'=>$data,
            'arrStatus'=>$this->arrStatus,
            'arrGroupUser'=>$arrGroupUser,
            'menuAdmin'=>$menuAdmin,
            'arrUserGroupMenu'=>$arrUserGroupMenu,

            'optionStatus'=>$optionStatus,
            'optionSex'=>$optionSex,
            'optionRoleType'=>$optionRoleType,

            'is_root'=>$this->is_root,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_change_pass'=>in_array($this->permission_change_pass, $this->permission) ? 1 : 0,
            'permission_remove'=>in_array($this->permission_remove, $this->permission) ? 1 : 0,
        ]);
    }
    //post
    public function edit($ids){
        //check permission
        if (!$this->is_root && !in_array($this->permission_edit, $this->permission)) {
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $id = FunctionLib::outputId($ids);
        $data['user_status'] = (int)Request::get('user_status', -1);
        $data['user_sex'] = (int)Request::get('user_sex', CGlobal::status_show);
        $data['user_full_name'] = htmlspecialchars(trim(Request::get('user_full_name', '')));
        $data['user_email'] = htmlspecialchars(trim(Request::get('user_email', '')));
        $data['user_phone'] = htmlspecialchars(trim(Request::get('user_phone', '')));
        $data['user_name'] = Request::get('user_name', '');
        $data['telephone'] = Request::get('telephone', '');
        $data['address_register'] = Request::get('address_register', '');
        $data['number_code'] = Request::get('number_code', '');
        $data['role_type'] = Request::get('role_type', Define::ROLE_TYPE_CUSTOMER);

        $this->validUser($id,$data);
        //FunctionLib::debug($this->error);
        $groupUser = $data['user_group'] = Request::get('user_group', array());
        if ($groupUser) {
            $strGroupUser = implode(',', $groupUser);
            $dataInsert['user_group'] = $strGroupUser;
        }
        $groupUserMenu = $data['user_group_menu'] = Request::get('user_group_menu', array());
        if ($groupUserMenu) {
            $strGroupUserMenu = implode(',', $groupUserMenu);
            $dataInsert['user_group_menu'] = $strGroupUserMenu;
        }
        if (empty($this->error)) {
            //Insert dữ liệu
            $dataInsert['user_name'] = $data['user_name'];
            $dataInsert['user_email'] = $data['user_email'];
            $dataInsert['user_phone'] = $data['user_phone'];
            $dataInsert['telephone'] = $data['telephone'];
            $dataInsert['address_register'] = $data['address_register'];
            $dataInsert['number_code'] = $data['number_code'];
            $dataInsert['role_type'] = $data['role_type'];
            $dataInsert['role_name'] = Define::$arrUserRole[$data['role_type']];
            $dataInsert['user_full_name'] = $data['user_full_name'];
            $dataInsert['user_status'] = (int)$data['user_status'];
            $dataInsert['user_edit_id'] = User::user_id();
            $dataInsert['user_edit_name'] = User::user_name();
            $dataInsert['user_updated'] = time();

            if($id > 0){
                if (User::updateUser($id, $dataInsert)) {
                    return Redirect::route('admin.user_view');
                } else {
                    $this->error[] = 'Lỗi truy xuất dữ liệu';;
                }
            }else{
                $dataInsert['user_create_id'] = User::user_id();
                $dataInsert['user_create_name'] = User::user_name();
                $dataInsert['user_created'] = time();
                if (User::createNew($dataInsert)) {
                    return Redirect::route('admin.user_view');
                } else {
                    $this->error[] = 'Lỗi truy xuất dữ liệu';;
                }
            }

        }
        $arrGroupUser = GroupUser::getListGroupUser();
        $menuAdmin = MenuSystem::getListMenuPermission();
        $this->getDataDefault();
        $optionStatus = FunctionLib::getOption($this->arrStatus, isset($data['user_status'])? $data['user_status']: CGlobal::status_show);
        $optionSex = FunctionLib::getOption($this->arrSex, isset($data['user_sex'])? $data['user_sex']: CGlobal::status_show);
        $optionRoleType = FunctionLib::getOption($this->arrRoleType, isset($data['role_type'])? $data['role_type']: Define::ROLE_TYPE_CUSTOMER);
        return view('admin.AdminUser.add',[
            'data'=>$data,
            'arrStatus'=>$this->arrStatus,
            'arrGroupUser'=>$arrGroupUser,
            'menuAdmin'=>$menuAdmin,
            'arrUserGroupMenu'=>$groupUserMenu,
            'optionStatus'=>$optionStatus,
            'optionSex'=>$optionSex,
            'optionRoleType'=>$optionRoleType,

            'error'=>$this->error,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_change_pass'=>in_array($this->permission_change_pass, $this->permission) ? 1 : 0,
            'permission_remove'=>in_array($this->permission_remove, $this->permission) ? 1 : 0,
        ]);
    }

    private function validUser($user_id =0, $data=array()) {
        if(!empty($data)) {
            if(isset($data['user_name']) && trim($data['user_name']) == '') {
                $this->error[] = 'Tài khoản đăng nhập không được bỏ trống';
            }elseif(isset($data['user_name']) && trim($data['user_name']) != ''){
                $checkIssetUser = User::getUserByName($data['user_name']);
                if($checkIssetUser && $checkIssetUser->user_id != $user_id){
                    $this->error[] = 'Tài khoản này đã tồn tại, hãy tạo lại';
                }
            }

            if(isset($data['user_full_name']) && trim($data['user_full_name']) == '') {
                $this->error[] = 'Tên nhân viên không được bỏ trống';
            }
            if(isset($data['user_email']) && trim($data['user_email']) == '') {
                $this->error[] = 'Mail không được bỏ trống';
            }
        }
        return true;
    }

    public function changePassInfo($ids)
    {
        $id = FunctionLib::outputId($ids);
        $user = User::user_login();
        if (!$this->is_root && !in_array($this->permission_change_pass, $this->permission) && (int)$id !== (int)$user['user_id']) {
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }

        return view('admin.AdminUser.change',[
            'id'=>$id,
            'is_root'=>$this->is_root,
            'permission_change_pass'=>in_array($this->permission_change_pass, $this->permission) ? 1 : 0,
        ]);
    }
    public function changePass($ids)
    {
        $id = FunctionLib::outputId($ids);
        $user = User::user_login();
        //check permission
        if (!$this->is_root && !in_array($this->permission_change_pass, $this->permission) && (int)$id !== (int)$user['user_id']) {
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }

        $error = array();
        $old_password = Request::get('old_password', '');
        $new_password = Request::get('new_password', '');
        $confirm_new_password = Request::get('confirm_new_password', '');

        if(!$this->is_root && !in_array($this->permission_change_pass, $this->permission)){
            $user_byId = User::getUserById($id);
            if($old_password == ''){
                $error[] = 'Bạn chưa nhập mật khẩu hiện tại';
            }
            if(User::encode_password($old_password) !== $user_byId->user_password ){
                $error[] = 'Mật khẩu hiện tại không chính xác';
            }
        }
        if ($new_password == '') {
            $error[] = 'Bạn chưa nhập mật khẩu mới';
        } elseif (strlen($new_password) < 5) {
            $error[] = 'Mật khẩu quá ngắn';
        }
        if ($confirm_new_password == '') {
            $error[] = 'Bạn chưa xác nhận mật khẩu mới';
        }
        if ($new_password != '' && $confirm_new_password != '' && $confirm_new_password !== $new_password) {
            $error[] = 'Mật khẩu xác nhận không chính xác';
        }
        if (empty($error)) {
            //Insert dữ liệu
            if (User::updatePassword($id, $new_password)) {
                if((int)$id !== (int)$user['user_id']){
                    return Redirect::route('admin.user_view');
                }else{
                    return Redirect::route('admin.dashboard');
                }
            } else {
                $error[] = 'Không update được dữ liệu';
            }
        }
        return view('admin.AdminUser.change',[
            'id'=>$id,
            'is_root'=>$this->is_root,
            'error'=>$error,
            'permission_change_pass'=>in_array($this->permission_change_pass, $this->permission) ? 1 : 0,
        ]);
    }

    public function remove($ids){
        $id = FunctionLib::outputId($ids);
        $data['success'] = 0;
        if(!$this->is_root && !in_array($this->permission_remove, $this->permission)){
            return Response::json($data);
        }
        $user = User::find($id);
        if($user){
            if(User::remove($user)){
                $data['success'] = 1;
            }
        }
        return Response::json($data);
    }

    //ajax
    public function getInfoSettingUser(){
        $user_ids = Request::get('user_id', '');
        $user_id = FunctionLib::outputId($user_ids);
        $arrData = $data = array();
        $arrData['intReturn'] = 1;
        $arrData['msg'] = '';

        //thong tin user
        $infoUser = User::getUserById($user_id);

        //thong tin user ở setting
        $arrInfoUser = UserSetting::getUserSettingByUserId($user_id);

        //get thong tin cua nha mang theo user id
        $dataUserCarrierSetting = UserCarrierSetting::getListAllByUserId($user_id);

        //show data
        if(empty($arrInfoUser)){
            $data['user_full_name'] = $infoUser['user_full_name'];
            $data['role_type'] = $infoUser['role_type'];
            $data['role_name'] = $infoUser['role_name'];
            $data['user_id'] = $infoUser['user_id'];
        }else{
            $data = (array)$arrInfoUser;
        }

        //get thong tin cua nha mang
        $arrNhaMang = array();
        $arrInfoCarrierSetting = CarrierSetting::getListAll();
        foreach ($arrInfoCarrierSetting as $carrier){
            $arrNhaMang[$carrier['carrier_setting_id']] = $carrier['carrier_name'];
        }

        if(!empty($arrNhaMang)){
            foreach ($arrNhaMang as $carrier_id =>$carrier_name){
                $data['carrier'][] = array(
                    'carrier_id'=>$carrier_id,
                    'carrier_name'=>$carrier_name,
                    'cost'=>(isset($dataUserCarrierSetting[$carrier_id])?$dataUserCarrierSetting[$carrier_id]['cost']:''));
            }
        }

        $optionPayment = FunctionLib::getOption(Define::$arrPayment, isset($data['payment_type'])? $data['payment_type']: Define::PAYMENT_TYPE_FIRST);
        $optionScanAuto = FunctionLib::getOption(Define::$arrScanAuto, isset($data['scan_auto'])? $data['scan_auto']: Define::SCAN_AUTO_FASLE);
        $optionSendAuto = FunctionLib::getOption(Define::$arrSendAuto, isset($data['sms_send_auto'])? $data['sms_send_auto']: Define::SEND_AUTO_FASLE);
        $html =  view('admin.AdminUser.infoUserSetting',[
            'data'=>$data,
            'optionPayment'=>$optionPayment,
            'optionScanAuto'=>$optionScanAuto,
            'optionSendAuto'=>$optionSendAuto,
            'user_id'=>$user_ids,
            ])->render();
        $arrData['html'] = $html;
        return response()->json( $arrData );
    }

    public function submitInfoSettingUser(){
        $arrData['intReturn'] = -1;
        $arrData['msg'] = 'Error';
        $formData= Request::get('formData', '');
        $formInput = explode('&',$formData);
        $arrForm = array();
        if(!empty($formInput)){
            foreach ($formInput as $k=>$string_valu){
                $arrVal = explode('=',$string_valu);
                $arrForm[$arrVal[0]] = $arrVal[1];
            }
        }
        /**
         *  [0] => user_id=NjZSV3JLXzdfZm52RjQ5
        [1] => user_setting_id=+SlJpZVpJXzBfS21XamJ5
        [2] => priority=
        [3] => account_balance=
        [4] => cost_1=500
        [5] => carrier_id_1=1
        [6] => cost_2=
        [7] => carrier_id_2=2
        [8] => scan_auto=1
        [9] => payment_type=1
        [10] => _token=VUUJi57LMZMWDs3oAQB3Xl4jYSVpZr1HNwCoEbZw
         */
        if(!isset($arrForm['user_id']) || !isset($arrForm['user_setting_id']))
            return response()->json( $arrData );

        //thong tin user
        $user_id = FunctionLib::outputId($arrForm['user_id']);
        $infoUser = User::getUserById($user_id);

        //id user_carrier_setting
        $user_setting_id = FunctionLib::outputId($arrForm['user_setting_id']);

        $dataInputUserSetting = array();
        $dataInputUserSetting['priority'] = isset($arrForm['priority'])?(int)$arrForm['priority']: 0;
        $dataInputUserSetting['payment_type'] = isset($arrForm['payment_type'])?(int)$arrForm['payment_type']: Define::PAYMENT_TYPE_FIRST;
        $dataInputUserSetting['account_balance'] = isset($arrForm['account_balance'])?(float)$arrForm['account_balance']: 0;
        if(isset($arrForm['scan_auto'])){
            $dataInputUserSetting['scan_auto'] = (int)$arrForm['scan_auto'];
        }
        if(isset($arrForm['sms_send_auto'])){
            $dataInputUserSetting['sms_send_auto'] = (int)$arrForm['sms_send_auto'];
        }

        $dataInputUserSetting['user_id'] = $infoUser['user_id'];
        $dataInputUserSetting['role_type'] = $infoUser['role_type'];
        $dataInputUserSetting['role_name'] = $infoUser['role_name'];
        $dataInputUserSetting['updated_date'] = date('Y-m-d h:i:s');

        //lấy du liệu vào DB
        //cập nhật user_setting

        if($user_setting_id > 0){//cap nhat
            UserSetting::updateItem($user_setting_id,$dataInputUserSetting);
        }else{//thêm mới
            $dataInputUserSetting['created_date'] = date('Y-m-d h:i:s');
            UserSetting::createItem($dataInputUserSetting);
        }
        $arrData['intReturn'] = 1;
        $arrData['msg'] = '';
        return response()->json( $arrData );
        //return Response::json($arrData);//json_encode($arrData);
    }
}