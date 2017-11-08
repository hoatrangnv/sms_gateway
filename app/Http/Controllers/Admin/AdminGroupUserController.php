<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\GroupUser;
use App\Http\Models\GroupUserPermission;
use App\Http\Models\Permission;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class AdminGroupUserController extends BaseAdminController{
    private $permission_view = 'group_user_view';
    private $permission_create = 'group_user_create';
    private $permission_edit = 'group_user_edit';
    private $arrStatus = array(CGlobal::status_hide=> 'Ẩn', CGlobal::status_show => 'Hoạt động');

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

}