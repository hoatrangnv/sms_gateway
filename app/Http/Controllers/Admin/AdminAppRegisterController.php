<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\SmsCustomer;
use App\Http\Models\SmsSendTo;
use App\Http\Models\User;
use App\Http\Models\ApiApp;
use App\Http\Models\CarrierSetting;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use View;

class AdminAppRegisterController extends BaseAdminController
{
    private $permission_view = 'appRegister_view';
    private $permission_full = 'appRegister_full';
    private $permission_delete = 'appRegister_delete';
    private $permission_create = 'appRegister_create';
    private $permission_edit = 'appRegister_edit';

    private $error = array();
    private $viewPermission = array();//check quyen

    public function __construct()
    {
        parent::__construct();
        CGlobal::$pageAdminTitle = 'Quản lý menu';
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
        $dataSearch['app_name'] = addslashes(Request::get('app_name_s',''));
        $dataSearch['user_id'] = md5($this->user_id);
        $limit = CGlobal::number_limit_show;
        $total = 0;
        $offset = ($page_no - 1) * $limit;
        $data = ApiApp::searchByCondition($dataSearch, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getNewPager(3,$page_no,$total,$limit,$dataSearch) : '';

        $this->viewPermission = $this->getPermissionPage();

        $endPoint = FunctionLib::checkHttps().$_SERVER['SERVER_NAME'].'/oauth2/token';

        return view('admin.AdminAppRegister.view',array_merge([
            'data'=>$data,
            'search'=>$dataSearch,
            'size'=>$total,
            'start'=>($page_no - 1) * $limit,
            'paging'=>$paging,
            'endPoint'=>$endPoint,
        ],$this->viewPermission));
    }

    public function addApp(){
        $app_name = isset($_POST['app_name'])?$_POST['app_name']:"";
        $description = isset($_POST['description'])?$_POST['description']:"";
        $ip_server = isset($_POST['ip_server'])?$_POST['ip_server']:"";
        $update_at = date("Y-m-d H:i",time());

        $id = isset($_POST['id'])?FunctionLib::outputId($_POST['id']):0;

        $data = array(
            "app_name"=>$app_name,
            "description"=>$description,
            "ip_server"=>$ip_server,
            "user_id"=>md5($this->user_id),
            "update_at"=>$update_at,
        );

        if ($id!=0 && $id!="0" && $id>0){
            ApiApp::updateItem($id,$data);
        }else{
            $data['client_id'] = FunctionLib::encodeBase64(FunctionLib::gen_uuid());
            $data['client_secret'] = FunctionLib::encodeBase64(bin2hex(openssl_random_pseudo_bytes(32)));
            $data['created_at'] =$update_at;
            ApiApp::createItem($data);
        }
        $data_full = ApiApp::getAll(array("user_id"=>md5($this->user_id)));

        $data_view = [
            'view' => View::make('admin.AdminAppRegister.list')
                ->with('data', $data_full)
                ->render()
        ];

        return Response::json($data_view, 200);
    }

    public function deleteApp(){
        $id = isset($_GET['id'])?FunctionLib::outputId($_GET['id']):0;
        if ($id>0){
            ApiApp::deleteItem($id);
        }
        $data_full = ApiApp::getAll(array("user_id"=>md5($this->user_id)));
        $data_view = [
            'view' => View::make('admin.AdminAppRegister.list')
                ->with('data', $data_full)
                ->render()
        ];
        return Response::json($data_view, 200);
    }
}
