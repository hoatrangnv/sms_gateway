<?php

/**
 * Created by PhpStorm.
 * User: MT969
 * Date: 5/30/2015
 * Time: 4:22 PM
 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\Permission;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\ArrayPermission;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class AdminPermissionController extends BaseAdminController
{

    private $permission_full = 'permission_full';
    private $permission_create = 'permission_create';
    private $permission_edit = 'permission_edit';
    private $arrStatus = array(-1 => 'Xóa', 0 => 'Tất cả', 1 => 'Hoạt động');

    public function __construct()
    {
        parent::__construct();
    }

    public function view(){
        if(!$this->is_root && !in_array($this->permission_full,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>1));
        }
        /*echo strtotime('17-10-2016 14:30:41');
        echo '<br/>'.strtotime('17-10-2016 14:32:41');
        die;*/

        $dataSearch = $dataResponse = $data = array();
        $page_no = Request::get('page_no', 1);//phan trang

        $dataSearch['permission_code'] = Request::get('permission_code', '');
        $dataSearch['permission_name'] = Request::get('permission_name', '');
        $dataSearch['permission_status'] = Request::get('permission_status', 0);
        $limit = 30;
        $offset = ($page_no - 1) * $limit;
        $total = 0;
        $aryPermission = Permission::searchPermission($dataSearch, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getNewPager(3,$page_no,$total,$limit,$dataSearch) : '';
        return view('admin.AdminPermission.view',[
            'data'=>$aryPermission,
            'dataSearch'=>$dataSearch,
            'total'=>$total,
            'start'=>($page_no - 1) * $limit,
            'paging'=>$paging,
            'arrStatus'=>$this->arrStatus,
            'is_root'=>$this->is_root,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_full'=>in_array($this->permission_full, $this->permission) ? 1 : 0,
        ]);
    }

    public function createInfo()
    {
        return view('admin.AdminPermission.create',[]);
    }

    public function create()
    {
//        //check permission
//        if (!in_array($this->permission_create, $this->permission)) {
//            return Redirect::route('admin.dashboard',array('error'=>1));
//        }
        $error = array();
        $data['permission_code'] = htmlspecialchars(trim(Request::get('permission_code', '')));
        $data['permission_name'] = htmlspecialchars(trim(Request::get('permission_name', '')));
        $data['permission_group_name'] = htmlspecialchars(trim(Request::get('permission_group_name', '')));
        $data['permission_status'] = 1;
        if ($data['permission_code'] == '') {
            $error[] = 'Mã quyền không được để trống ';
        }
        if ($data['permission_name'] == '') {
            $error[] = 'Tên quyền không được để trống ';
        }
        if ($data['permission_group_name'] == '') {
            $error[] = 'Nhóm quyền không được để trống ';
        }
        if (Permission::checkExitsPermissionCode($data['permission_code'])) {
            $error[] = 'Mã quyền đã tồn tại ';
        }

        if ($error == null){
            //insert dl
            if (Permission::createPermission($data)) {
                return Redirect::route('admin.permission_view');
            }
        }

        return view('admin.AdminPermission.create',[
            'error'=>$error,
            'data'=>$data,
        ]);
    }

    public function editInfo($id)
    {
//        CGlobal::$pageTitle = "Sửa quyền | Admin Seo";
//        if (!in_array($this->permission_edit, $this->permission)) {
//            return Redirect::route('admin.dashboard',array('error'=>1));
//        }
        $data = Permission::find($id);//lay dl permission theo id
        return view('admin.AdminPermission.create',[
            'arrStatus'=>$this->arrStatus,
            'data'=>$data,
        ]);
    }

    public function edit($id)
    {
        //check permission
//        if (!in_array($this->permission_edit, $this->permission)) {
//            return Redirect::route('admin.dashboard',array('error'=>1));
//        }
        $error = array();
        $data['permission_code'] = htmlspecialchars(trim(Request::get('permission_code', '')));
        $data['permission_name'] = htmlspecialchars(trim(Request::get('permission_name', '')));
        $data['permission_group_name'] = htmlspecialchars(trim(Request::get('permission_group_name', '')));
        $data['permission_status'] = (int)Request::get('permission_status', 1);
        //encode các ký tự html
        if ($data['permission_code'] == '') {
            $error['mess'] = 'Mã quyền không được để trống ';
        }
        if ($data['permission_name'] == '') {
            $error['mess'] = 'Tên quyền không được để trống ';
        }
        if ($data['permission_group_name'] == '') {
            $error['mess'] = 'Nhóm quyền không được để trống ';
        }

        if (Permission::checkExitsPermissionCode($data['permission_code'], $id)) {
            $error[] = 'Mã quyền đã tồn tại ';
        }

        if ($error == null) {
            if (Permission::updatePermission($id, $data)) {
                return Redirect::route('admin.permission_view');
            } else {
                $error[] = 'Lỗi truy xuất dữ liệu';
            }
        }
        return view('admin.AdminPermission.create',[
            'arrStatus'=>$this->arrStatus,
            'error'=>$error,
            'data'=>$data,
        ]);
    }

    public function deletePermission()
    {
        $data = array('isIntOk' => 0);
        if(!$this->is_root && !in_array($this->permission_full,$this->permission)){
            return Response::json($data);
        }
        $id = (int)Request::get('id', 0);
        if ($id > 0 && Permission::deleteData($id)) {
            $data['isIntOk'] = 1;
        }
        return Response::json($data);
    }

    public function addPermit(){
        //die();
        //die('tạm dừng chức năng này');
        $arrPermit = ArrayPermission::$arrPermit;

        /*DB::table(Define::TABLE_PERMISSION)->truncate();
        DB::table(Define::TABLE_GROUP_USER)->truncate();
        DB::table(Define::TABLE_GROUP_USER_PERMISSION)->truncate();*/
        foreach($arrPermit as $permit=> $infor){
            $arrInsert = array(
                'permission_code'=>$permit,
                'permission_name'=>$infor['name_permit'],
                'permission_group_name'=>$infor['group_permit'],
                'permission_status'=>1);
            if (!Permission::checkExitsPermissionCode($permit)) {
                Permission::createPermission($arrInsert);
            }
        }
        FunctionLib::debug($arrPermit);
    }
}