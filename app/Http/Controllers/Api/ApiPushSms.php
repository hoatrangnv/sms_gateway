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


class ApiPushSms extends BaseApiController
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
    }

    public function pushSms($user_id,$phones,$messages){
        FunctionLib::debug($messages);
    }

    public function authorization()
    {
        $return = array();
        if (isset($_SERVER['CONTENT_TYPE'])) {
            if ($_SERVER["CONTENT_TYPE"] == Define::APPLICATION_JSON) {
                $data = file_get_contents("php://input");
                $data = json_decode($data, true);

                $token = isset($data['access_token']) ? $data['access_token'] : "";
                $phone = isset($data['phone']) ? $data['phone'] : "";
                $message = isset($data['message']) ? $data['message'] : "";

                if (trim($token != "")) {
                    $result = $this->checkToken($token);
                    if ($result['code'] == Define::HTTP_STATUS_CODE_200) {
                        $arr = array();
                        foreach ($result['response'] as $k => $v){
                            $arr[] = (array) $v;
                        }
                        $user_id = $arr[0]['user_id'];
                        self::pushSms($user_id,$phone,$message);
                    } else {
                        $return = FunctionLib::returnAPI($result['code'], $result['message']);
                    }
                } else {
                    $return = FunctionLib::returnAPI(Define::HTTP_STATUS_CODE_401, 'Token không hợp lệ');
                }
            } else {
                $return = FunctionLib::returnAPI(Define::HTTP_STATUS_CODE_400, 'Bad Request');
            }
        } else {
            FunctionLib::debug($_SERVER['CONTENT_TYPE']);
            $return = FunctionLib::returnAPI(Define::HTTP_STATUS_CODE_400, 'Bad Request');
        }

        return FunctionLib::responeJson($return);
    }

    private function checkToken($token)
    {
        date_default_timezone_set('Asia/Bangkok');
        $tokenDecode = base64_decode($token);
        $tokenData = explode('_', $tokenDecode);
        $return = array();
        if (count($tokenData)==4 && isset($tokenData[3])) {
            $timeRange = (double)$tokenData[3] - (double)time();
            if (0 <= $timeRange && $timeRange <= Define::CACHE_TIME_TO_LIVE_ONE_DAY) {
                if (isset($tokenData[0]) && isset($tokenData[1]) && isset($tokenData[2])){
                    $clientIDData = explode('+',$tokenData[0]);
                    $clientID = isset($clientIDData[1])?$clientIDData[1]:"";
                    $clientSecretData = explode('+',$tokenData[1]);
                    $clientSecret = isset($clientSecretData[1])?$clientSecretData[1]:"";
                    $clientUserData = explode('+',$tokenData[2]);
                    $clientUser = isset($clientUserData[1])?$clientUserData[1]:"";

                    if ($clientID !=""||$clientSecret !=""||$clientUser !=""){

                        $sql = "SELECT user_id,app_id  from api_app 
                                WHERE md5(CONCAT(SUBSTRING_INDEX(FROM_BASE64(client_id),'_',1),'".Define::SIGN_KEY_TOKEN."')) = '".$clientID."' 
                                AND md5(CONCAT(SUBSTRING_INDEX(FROM_BASE64(client_secret),'_',1),'".Define::SIGN_KEY_TOKEN."')) = '".$clientSecret."'
                                ";

                        $result = FunctionLib::executesSQL($sql);

                        if (!empty($result)){
                            $return = array(
                                "code" => Define::HTTP_STATUS_CODE_200,
                                "response" => $result
                            );
                        }else{
                            $return = array(
                                "code" => 101,
                                "message" => "Token không hợp lệ"
                            );
                        }
                    }else{
                        $return = array(
                            "code" => 101,
                            "message" => "Token không hợp lệ"
                        );
                    }

                }else{
                    $return = array(
                        "code" => 101,
                        "message" => "Token không hợp lệ"
                    );
                }
            } else {
                $return = array(
                    "code" => 100,
                    "message" => "Token expire"
                );
            }
        }else{
            $return = array(
                "code" => 101,
                "message" => "Token không hợp lệ"
            );
        }
        return $return;
    }
}
