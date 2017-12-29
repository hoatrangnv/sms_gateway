<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Models\User;
use App\Http\Models\ApiApp;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;


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

    public function getToken(){
//        FunctionLib::debug('xxxx');
        return response(json_encode(array("ip"=>$_SERVER['REMOTE_ADDR'],"hello"=>"get token Welcome to SMSGateways Service")), 200)
            ->header('Content-Type', 'application/json');
    }
}
