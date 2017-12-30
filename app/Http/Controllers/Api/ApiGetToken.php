<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Models\User;
use App\Http\Models\ApiApp;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Memcache;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
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
        if($_SERVER["CONTENT_TYPE"] == Define::APPLICATION_JSON){
            $data = file_get_contents("php://input");
            $data   = json_decode($data,true);

            $client_id = $data['client_id'];
            $client_secret = $data['client_secret'];
            $PartnerID="";
            $return=array();
            if ($this->checkClient($client_id,$client_secret,$PartnerID)){
                $access_token = FunctionLib::encodeToken($client_id,$client_secret,$PartnerID);
                $return = array(
                    "status_code"=>Define::HTTP_STATUS_CODE_200,
                    "access_token"=>$access_token,
                    "expires_in"=>Memcache::CACHE_TIME_TO_LIVE_ONE_DAY,
                    "token_type"=>"Bearer",
                );
            }else{
                $return = array(
                    "status_code"=>Define::HTTP_STATUS_CODE_400,
                    "error_description"=>"Các thông tin client là không đúng."
                    );
            }
        }

        return FunctionLib::responeJson($return);
    }

    private function checkClient($client_id,$client_secret,&$PartnerID){

        $data_check = array(
            'client_id'=>FunctionLib::encodeBase64($client_id),
            'client_secret'=>FunctionLib::encodeBase64($client_secret),
            'field_get'=>"app_id,user_id"
        );

        $total = 0;
        $result = ApiApp::searchByCondition($data_check,1,0,$total);
        $PartnerID = isset($result[0]) && trim($result[0]['user_id'])!="" ?$result[0]['user_id']:"";
        $return = (int)$total>0?1:0;
        return $return;
    }
}
