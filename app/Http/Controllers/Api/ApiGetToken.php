<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
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
    public function __construct(){
        parent::__construct();
    }

    public function getToken(){
        $return=array();
        if (isset($_SERVER[Define::CONTENT_TYPE])){
            if($_SERVER[Define::CONTENT_TYPE] == Define::APPLICATION_JSON){
                $data = file_get_contents("php://input");
                $data   = json_decode($data,true);

                $client_id = isset($data[Define::KEY_CLIENT_ID])?$data[Define::KEY_CLIENT_ID]:"";
                $client_secret = isset($data[Define::KEY_CLIENT_SECRET])?$data[Define::KEY_CLIENT_SECRET]:"";
                $PartnerID="";
                $return=array();
                if (trim($client_id!="") && trim($client_secret !="") && $this->checkClient($client_id,$client_secret,$PartnerID)){
                    $access_token = FunctionLib::encodeToken($client_id,$client_secret,$PartnerID);
                    $access_token = substr($access_token,0,strlen($access_token)-2);
                    $return = array(
                        Define::STATUS_CODE=>Define::HTTP_STATUS_CODE_200,
                        Define::MESSAGE=>Define::HTTP_STATUS_MESSAGE_SUCCESS,
                        Define::ACCESS_TOKEN=>$access_token,
                        Define::EXPIRES_IN=>Memcache::CACHE_TIME_TO_LIVE_ONE_DAY,
                        Define::TOKEN_TYPE=>"Bearer",
                    );
                }else{
                    $return = array(
                        Define::STATUS_CODE=>Define::HTTP_STATUS_CODE_406,
                        Define::MESSAGE=>Define::HTTP_STATUS_MESSAGE_INVALID_CLIENT
                    );
                }
            }
        }else{
            $return = array(
                Define::STATUS_CODE=>Define::HTTP_STATUS_CODE_400,
                Define::MESSAGE=>Define::HTTP_STATUS_MESSAGE_BAD_REQUEST
            );
        }

        return FunctionLib::responeJson($return);
    }

    private function checkClient($client_id,$client_secret,&$PartnerID){

        $data_check = array(
            Define::KEY_CLIENT_ID=>FunctionLib::encodeBase64($client_id),
            Define::KEY_CLIENT_SECRET=>FunctionLib::encodeBase64($client_secret),
            'field_get'=>"app_id,user_id"
        );

        $total = 0;
        $result = ApiApp::searchByCondition($data_check,1,0,$total);
        $PartnerID = isset($result[0]) && trim($result[0]['user_id'])!="" ?$result[0]['user_id']:"";
        $return = (int)$total>0?1:0;
        return $return;
    }
}
