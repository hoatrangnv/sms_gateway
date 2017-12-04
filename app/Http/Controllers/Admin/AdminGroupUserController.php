<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\GroupUser;
use App\Http\Models\GroupUserPermission;
use App\Http\Models\Permission;
use App\Http\Models\RoleMenu;
use App\Http\Models\MenuSystem;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class AdminGroupUserController extends BaseAdminController{
    private $permission_view = 'group_user_view';
    private $permission_create = 'group_user_create';
    private $permission_edit = 'group_user_edit';
    private $arrStatus = array(CGlobal::status_hide=> 'Ẩn', CGlobal::status_show => 'Hoạt động');
    private $viewPermission = array();//check quyen
    private $error = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function view(){
        //check permission
        if (!$this->is_root && !in_array($this->permission_view, $this->permission)) {
            return Redirect::route('admin.dashboard',array('error'=>1));
        }

        $page_no = Request::get('page_no', 1);//phan trang
        $dataSearch['group_user_name'] = Request::get('group_user_name', '');
        $dataSearch['group_user_status'] = Request::get('group_user_status', 0);

        $limit = 30;
        $offset = ($page_no - 1) * $limit;
        $total = 0;
        //call api
        $aryGroupUser = GroupUser::searchGroupUser($dataSearch, $limit, $offset, $total);
        if (!empty($aryGroupUser)) {
            $aryGroupId = array();
            foreach ($aryGroupUser as $val) {
                $aryGroupId[] = $val->group_user_id;
            }
            if (!empty($aryGroupId)) {
                $aryPermission = GroupUserPermission::getListPermissionByGroupId($aryGroupId);
                if (!empty($aryPermission)) {
                    foreach ($aryGroupUser as $k => $v) {
                        $items = $v;
                        foreach ($aryPermission as $val) {
                            if ($v->group_user_id == $val->group_user_id) {
                                $item = isset($v->permissions) ? $v->permissions : array();
                                $count = isset($v->countPermission) ? $v->countPermission : 0;
                                $item[] = $val;
                                $count++;
                                $items->permissions = $item;
                                $items->countPermission = $count;
                            }
                        }
                        $aryGroupUser[$k] = $items;
                    }
                }
            }
        }
        $paging = $total > 0 ? Pagging::getNewPager(3, $page_no, $total, $limit, $dataSearch) : '';

        return view('admin.AdminGroupUser.view',[
            'data'=>$aryGroupUser,
            'dataSearch'=>$dataSearch,
            'total'=>$total,
            'start'=>($page_no - 1) * $limit,
            'paging'=>$paging,
            'arrStatus'=>$this->arrStatus,
            'is_root'=>$this->is_root,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_view'=>in_array($this->permission_view, $this->permission) ? 1 : 0,
        ]);
    }

    public function createInfo()
    {
        if (!$this->is_root && !in_array($this->permission_view, $this->permission) && !in_array($this->permission_create, $this->permission)) {
            return Redirect::route('admin.dashboard',array('error'=>1));
        }
        $listPermission = Permission::getListPermission();
        $arrPermissionByController = $this->buildArrayPermissionByController($listPermission);
        return view('admin.AdminGroupUser.create',[
            'arrPermissionByController'=>$arrPermissionByController,
            'is_root'=>$this->is_root,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_view'=>in_array($this->permission_view, $this->permission) ? 1 : 0,
        ]);
    }

    public function create()
    {
        //check permission
        if (!$this->is_root && !in_array($this->permission_view, $this->permission) && !in_array($this->permission_create, $this->permission)) {
            return Redirect::route('admin.dashboard',array('error'=>1));
        }

        $error = array();
        $data['group_user_name'] = htmlspecialchars(trim(Request::get('group_user_name', '')));
        $data['group_user_status'] = 1;

        $arrPermission = Request::get('permission_id', array());

        if ($data['group_user_name'] == '') {
            $error[] = 'Tên nhóm người dùng không được để trống ';
        }
        if (GroupUser::checkExitsGroupName($data['group_user_name'])) {
            $error[] = 'Tên nhóm người dùng đã được sử dụng ';
        }
        if ($error != null) {
            $listPermission = Permission::getListPermission();
            $arrPermissionByController = $this->buildArrayPermissionByController($listPermission);
            $data['strPermission'] = $arrPermission;
        } else {
            if (GroupUser::createGroup($data, $arrPermission)) {
                return Redirect::route('admin.groupUser_view');
            } else {
                $listPermission = Permission::getListPermission();
                $arrPermissionByController = $this->buildArrayPermissionByController($listPermission);
                $data['strPermission'] = $arrPermission;
                $error[] = 'Lỗi truy xuất dữ liệu';
            }
        }

        return view('admin.AdminGroupUser.create',[
            'error'=>$error,
            'data'=>$data,
            'is_root'=>$this->is_root,
            'arrPermissionByController'=>$arrPermissionByController,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_view'=>in_array($this->permission_view, $this->permission) ? 1 : 0,
        ]);
    }

    public function editInfo($id=0)
    {
//        CGlobal::$pageTitle = "Sửa nhóm User | Admin Seo";
        if (!$this->is_root && !in_array($this->permission_view, $this->permission) && !in_array($this->permission_edit, $this->permission)) {
            return Redirect::route('admin.dashboard',array('error'=>1));
        }

        $data = ($id > 0)? GroupUser::find($id): array();//lay dl permission theo id
        $dataPermission = GroupUserPermission::getListPermissionByGroupId(array($id));

        if(!empty($data)){
            $aryPermission = array();
            if ($dataPermission) {
                foreach ($dataPermission as $per) {
                    $aryPermission[] = $per->permission_id;
                }
            }
            $data->strPermission = $aryPermission;
        }

        // Show the page
        $listPermission = Permission::getListPermission();
        $arrPermissionByController = $this->buildArrayPermissionByController($listPermission);
        $optionStatus = FunctionLib::getOption($this->arrStatus, isset($data['group_user_status'])? $data['group_user_status']: CGlobal::status_show);
        $optionView = FunctionLib::getOption($this->arrStatus, isset($data['group_user_view'])? $data['group_user_view']: CGlobal::status_show);
        return view('admin.AdminGroupUser.edit',[
            'data'=>$data,
            'optionStatus'=>$optionStatus,
            'optionView'=>$optionView,

            'is_root'=>$this->is_root,
            'arrPermissionByController'=>$arrPermissionByController,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_view'=>in_array($this->permission_view, $this->permission) ? 1 : 0,
        ]);
    }

    public function edit($id=0)
    {
        //check permission
        if (!$this->is_root && !in_array($this->permission_view, $this->permission) && !in_array($this->permission_edit, $this->permission)) {
            return Redirect::route('admin.dashboard',array('error'=>1));
        }
        $error = array();
        $data['group_user_name'] = htmlspecialchars(trim(Request::get('group_user_name', '')));
        $data['group_user_status'] = Request::get('group_user_status', CGlobal::status_show);
        $data['group_user_order'] = Request::get('group_user_order', 1);
        $data['group_user_view'] = Request::get('group_user_view', CGlobal::status_show);

        $arrPermission = Request::get('permission_id');

        if ($data['group_user_name'] == '') {
            $error[] = 'Tên nhóm người dùng không được để trống ';
        }
        if (GroupUser::checkExitsGroupName($data['group_user_name'], $id)) {
            $error[] = 'Tên nhóm người dùng đã được sử dụng ';
        }
        if ($error != null) {
            $listPermission = Permission::getListPermission();
            $arrPermissionByController = $this->buildArrayPermissionByController($listPermission);
            $data['strPermission'] = $arrPermission;
        } else {
            if($id > 0){
                if (GroupUser::updateGroup($id, $data, $arrPermission)) {
                    return Redirect::route('admin.groupUser_view');
                } else {
                    $listPermission = Permission::getListPermission();
                    $arrPermissionByController = $this->buildArrayPermissionByController($listPermission);
                    $error['mess'] = 'Lỗi truy xuất dữ liệu';
                    $data['strPermission'] = $arrPermission;
                }
            }else{
                if (GroupUser::createGroup($data, $arrPermission)) {
                    return Redirect::route('admin.groupUser_view');
                } else {
                    $listPermission = Permission::getListPermission();
                    $arrPermissionByController = $this->buildArrayPermissionByController($listPermission);
                    $error['mess'] = 'Lỗi truy xuất dữ liệu';
                    $data['strPermission'] = $arrPermission;
                }
            }

        }

        $optionStatus = FunctionLib::getOption($this->arrStatus, isset($data['group_user_status'])? $data['group_user_status']: CGlobal::status_show);
        $optionView = FunctionLib::getOption($this->arrStatus, isset($data['group_user_view'])? $data['group_user_view']: CGlobal::status_show);
        return view('admin.AdminGroupUser.edit',[
            'error'=>$error,
            'data'=>$data,
            'optionStatus'=>$optionStatus,
            'optionView'=>$optionView,

            'is_root'=>$this->is_root,
            'arrPermissionByController'=>$arrPermissionByController,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_view'=>in_array($this->permission_view, $this->permission) ? 1 : 0,
        ]);
    }

    private function buildArrayPermissionByController($listPermission){
        $arrPermissionByController = array();
        if (!empty($listPermission)) {
            foreach ($listPermission as $permission) {
                $arrPermissionByController[$permission['permission_group_name']][] = $permission;
            }
        }
        return $arrPermissionByController;
    }

    public function remove($id){
        $data['success'] = 0;
        if(!$this->is_root){
            return Response::json($data);
        }
        $user = GroupUser::find($id);
        if($user){
            if(GroupUser::remove($user)){
                $data['success'] = 1;
            }
        }
        return Response::json($data);
    }

    /**********************************************Role menu*********************************************************************/
    public function viewRole() {
        //Check phan quyen.
        if(!$this->is_root && !in_array($this->permission_view,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $pageNo = (int) Request::get('page_no',1);
        $sbmValue = Request::get('submit', 1);
        $limit = 200;
        $offset = ($pageNo - 1) * $limit;
        $search = $data = array();
        $total = 0;

        $search['role_id'] = (int)Request::get('role_id',-1);
        //$search['field_get'] = 'menu_name,menu_id,parent_id';//cac truong can lay

        $dataSearch = RoleMenu::searchByCondition($search, $limit, $offset,$total);
        if(!empty($dataSearch)){
            $data =$dataSearch;
        }
        $paging = '';

        //FunctionLib::debug($data);
        $arrRoleType = Define::$arrUserRole;
        $optionStatus = FunctionLib::getOption($arrRoleType, $search['role_id']);

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminGroupUser.viewRole',array_merge([
            'data'=>$data,
            'search'=>$search,
            'total'=>$total,
            'stt'=>($pageNo - 1) * $limit,
            'paging'=>$paging,
            'optionStatus'=>$optionStatus,
        ],$this->viewPermission));
    }

    public function getRole($ids) {
        $id = FunctionLib::outputId($ids);

        if(!$this->is_root && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }

        $arrUserGroupMenu = $data = array();
        if($id > 0){
            $data = RoleMenu::find($id);
            $data['role_group_permission'] = explode(',', $data['role_group_permission']);
            $arrUserGroupMenu = explode(',', $data['role_group_menu_id']);
        }

        $arrGroupUser = GroupUser::getListGroupUser($this->is_boss);
        $menuAdmin = MenuSystem::getListMenuPermission();

        $arrRoleType = Define::$arrUserRole;
        $optionRole = FunctionLib::getOption($arrRoleType, isset($data['role_id'])? $data['role_id']: Define::ROLE_TYPE_CUSTOMER);
        $optionStatus = FunctionLib::getOption($this->arrStatus, isset($data['role_status'])? $data['role_status']: CGlobal::status_show);

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminGroupUser.addRole',array_merge([
            'data'=>$data,
            'id'=>$id,
            'optionRole'=>$optionRole,
            'optionStatus'=>$optionStatus,
            'arrGroupUser'=>$arrGroupUser,
            'menuAdmin'=>$menuAdmin,
            'arrUserGroupMenu'=>$arrUserGroupMenu,
        ],$this->viewPermission));
    }

    public function postRole($ids) {
        $id = FunctionLib::outputId($ids);
        if(!$this->is_root && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $arrRoleType = Define::$arrUserRole;

        $id_hiden = (int)Request::get('id_hiden', 0);
        $data['role_status'] = (int)Request::get('role_status',CGlobal::status_show);
        $data['role_id'] = (int)Request::get('role_id',Define::ROLE_TYPE_CUSTOMER);
        $data['role_name'] = isset($arrRoleType[$data['role_id']])? $arrRoleType[$data['role_id']]: 'no name';

        //lay nhóm quyền
        $groupUser = $data['user_group'] = Request::get('user_group', array());
        if ($groupUser) {
            $strGroupUser = implode(',', $groupUser);
            $data['role_group_permission'] = $strGroupUser;
        }
        $groupUserMenu = $data['user_group_menu'] = Request::get('user_group_menu', array());
        if ($groupUserMenu) {
            $strGroupUserMenu = implode(',', $groupUserMenu);
            $data['role_group_menu_id'] = $strGroupUserMenu;
        }

        if(empty($this->error)) {
            $dataInsert['role_id'] = $data['role_id'];
            $dataInsert['role_name'] = $data['role_name'];
            $dataInsert['role_status'] = $data['role_status'];
            $dataInsert['role_group_menu_id'] = $data['role_group_menu_id'];
            $dataInsert['role_group_permission'] = $data['role_group_permission'];
            $id = ($id == 0)?$id_hiden: $id;
            if($id > 0) {
                //cap nhat
                if(RoleMenu::updateItem($id, $data)) {
                    return Redirect::route('admin.viewRole');
                }
            }else{
                //them moi
                if(RoleMenu::createItem($data)) {
                    return Redirect::route('admin.viewRole');
                }
            }
        }
        $arrGroupUser = GroupUser::getListGroupUser($this->is_boss);
        $menuAdmin = MenuSystem::getListMenuPermission();
        $arrRoleType = Define::$arrUserRole;
        $optionRole = FunctionLib::getOption($arrRoleType, isset($data['role_id'])? $data['role_id']: Define::ROLE_TYPE_CUSTOMER);
        $optionStatus = FunctionLib::getOption($this->arrStatus, isset($data['role_status'])? $data['role_status']: CGlobal::status_show);

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminGroupUser.addRole',array_merge([
            'data'=>$data,
            'id'=>$id,
            'error'=>$this->error,
            'optionRole'=>$optionRole,
            'optionStatus'=>$optionStatus,
            'arrGroupUser'=>$arrGroupUser,
            'menuAdmin'=>$menuAdmin,
        ],$this->viewPermission));
    }

    public function getPermissionPage(){
        return $this->viewPermission = [
            'is_root'=> $this->is_root ? 1:0,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_full'=>in_array($this->permission_view, $this->permission) ? 1 : 0,
        ];
    }
}