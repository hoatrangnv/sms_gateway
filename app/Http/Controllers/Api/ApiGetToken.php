<?php

namespace App\Http\Controllers\Api;

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

class ApiGetToken extends BaseAdminController
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

    public function addApp(){
        FunctionLib::debug('xxxx');
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
            $data['client_id'] = FunctionLib::gen_uuid();
            $data['client_secret'] = bin2hex(openssl_random_pseudo_bytes(32));
            $data['created_at'] =$update_at;
            ApiApp::createItem($data);
        }
        $data_full = ApiApp::getAll();
        $data_view = [
            'view' => View::make('admin.AdminAppRegister.list')
                ->with('data', $data_full)
                ->render()
        ];

        return Response::json($data_view, 200);
    }

    public function welcome(){
        $result = json_encode(array("ip"=>$_SERVER['REMOTE_ADDR'],"hello"=>"Welcome to SMSGateways Service"));
        return $result;
    }
}
