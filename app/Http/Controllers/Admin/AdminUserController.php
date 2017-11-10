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
    private $arrSex = array();
    private $error = array();

    public function __construct(){
        parent::__construct();
        $this->getDataDefaul();
    }

    public function getDataDefaul(){
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

        $optionStatus = FunctionLib::getOption($this->arrStatus, isset($data['user_status'])? $data['user_status']: CGlobal::status_show);
        $optionSex = FunctionLib::getOption($this->arrSex, isset($data['user_sex'])? $data['user_sex']: CGlobal::status_show);
        return view('admin.AdminUser.add',[
            'data'=>$data,
            'arrStatus'=>$this->arrStatus,
            'arrGroupUser'=>$arrGroupUser,
            'menuAdmin'=>$menuAdmin,
            'arrUserGroupMenu'=>$arrUserGroupMenu,

            'optionStatus'=>$optionStatus,
            'optionSex'=>$optionSex,

            'is_root'=>$this->is_root,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_change_pass'=>in_array($this->permission_change_pass, $this->permission) ? 1 : 0,
            'permission_remove'=>in_array($this->permission_remove, $this->permission) ? 1 : 0,
        ]);
    }
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

        $optionStatus = FunctionLib::getOption($this->arrStatus, isset($data['user_status'])? $data['user_status']: CGlobal::status_show);
        $optionSex = FunctionLib::getOption($this->arrSex, isset($data['user_sex'])? $data['user_sex']: CGlobal::status_show);

        return view('admin.AdminUser.add',[
            'data'=>$data,
            'arrStatus'=>$this->arrStatus,
            'arrGroupUser'=>$arrGroupUser,
            'menuAdmin'=>$menuAdmin,
            'arrUserGroupMenu'=>$groupUserMenu,
            'optionStatus'=>$optionStatus,
            'optionSex'=>$optionSex,

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

}