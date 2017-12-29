<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
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

class ApiGetToken extends BaseApiController
{
    private $permission_view = 'appRegister_view';
    private $permission_full = 'appRegister_full';
    private $permission_delete = 'appRegister_delete';
    private $permission_create = 'appRegister_create';
    private $permission_edit = 'appRegister_edit';

    private $error = array();
    private $viewPermission = array();//check quyen

    public function __construct(){
        parent::__construct();
    }

    public function addApp(){
        FunctionLib::debug('xxxx');
    }

    public function welcome(){
        $result = json_encode(array("ip"=>$_SERVER['REMOTE_ADDR'],"hello"=>"Welcome to SMSGateways Service"));
        return $result;
    }
}
