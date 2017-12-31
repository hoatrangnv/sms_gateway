<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Models\CarrierSetting;
use App\Http\Models\SmsCustomer;
use App\Http\Models\SmsLog;
use App\Http\Models\SmsSendTo;
use App\Http\Models\User;
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
    public function __construct()
    {
        parent::__construct();
    }

    public function pushSms($user_id="",$phones="",$messages="",$dead_line=""){
        if (trim($user_id!="")){
            $user = User::getUserByMd5Id($user_id);
            $user_id = $user['user_id'];
        }
        if (trim($phones) != "" && trim($messages)!=""){
            $send_sms_deadline = trim($dead_line!="")?date("Y-m-d H:i:s",strtotime($dead_line)):"";
            $phones_list = explode(',',$phones);
            $dataPhone = array();
            $dataPhone_err = array();
            foreach ($phones_list as $k =>$number){
                $checkNumber = FunctionLib::checkNumberPhoneAPI($number);
                if($checkNumber > 0){
                    $dataPhone[] = trim($number);
                }else{
                    $dataPhone_err[$number] = trim($number);
                }
            }

            //đẩy dữ liệu theo nhà mạng
            $carrier = CarrierSetting::getInfoCarrier();
            $arrNumberCarries = array();
            foreach ($carrier as $kc=>$val_carr) {
                $number_firsr = (trim($val_carr['first_number']) != '') ? explode(',', $val_carr['first_number']) : array();
                if(!empty($number_firsr)){
                    foreach ($number_firsr as $kk =>$number_firsr_carr){
                        $arrNumberCarries[$number_firsr_carr] = array(
                            'first_number'=>$number_firsr_carr,
                            'carrier_id'=>$val_carr['carrier_setting_id'],
                            'carrier_name'=>$val_carr['carrier_name'],
                            'slipt_number'=>$val_carr['slipt_number'],
                            'min_number'=>$val_carr['min_number'],
                            'max_number'=>$val_carr['max_number']);
                    }
                }
            }


            //check số có phù hợp với nhà mạng
            if(!empty($carrier)){
                $arrMsg = array();
                $infoPhone = array();
                foreach ($dataPhone as $kkk=>$phone_number){
                    $lenghtNumber = strlen($phone_number);
                    foreach ($arrNumberCarries as $kk =>$dauso){
                        $pos = strpos(trim($phone_number), trim($dauso['first_number']));
                        if($pos === 0 && $dauso['min_number'] <= $lenghtNumber && $lenghtNumber <= $dauso['max_number']){
                            $infoPhone[trim($phone_number)] = array(
                                'phone_number'=>$phone_number,
                                'lenght'=>strlen($phone_number),
                                'slipt_number'=>$dauso['slipt_number'],
                                'carrier_id'=>$dauso['carrier_id'],
                                'carrier_name'=>$dauso['carrier_name']);
                            $arrMsg[$dauso['carrier_id']] = FunctionLib::splitStringSms($messages,$dauso['slipt_number']);
                        }
                    }
                }
                //ghep data
                if(!empty($infoPhone) && !empty($arrMsg)){
                    foreach ($infoPhone as $k=>$phone){
                        foreach ($arrMsg[$phone['carrier_id']] as $kk =>$msgSms){
                            $dataSend[] = array(
                                'phone_number'=>$phone['phone_number'],
                                'content'=>$msgSms,
                                'carrier_id'=>$phone['carrier_id'],
                                'carrier_name'=>$phone['carrier_name']);

                            $dataCarriesInput[$phone['carrier_id']] = array(
                                'carrier_id'=>$phone['carrier_id'],
                                'carrier_name'=>$phone['carrier_name']);
                        }
                    }
                }

                if(!empty($dataSend)) {
                    foreach ($phones_list as $k => $v){
                        if (!array_key_exists($v,$infoPhone)){
                            $dataPhone_err[$v] = $v;
                        }
                    }
//                FunctionLib::debug($dataPhone_err);
                    //get tổng send SMS theo nhà mạng
                    foreach ($dataSend as $kkk=>$valu){
                        if(isset($dataCarriesInput[$valu['carrier_id']]['tong_sms'])){
                            $dataCarriesInput[$valu['carrier_id']]['tong_sms'] = $dataCarriesInput[$valu['carrier_id']]['tong_sms']+1;
                        }else{
                            $dataCarriesInput[$valu['carrier_id']]['tong_sms'] = 1;
                        }
                    }

                    //web_sms_customer
                    $dataInsertSmsCustomer = array(
                        'user_customer_id'=>$user_id,
                        'status'=>Define::SMS_STATUS_PROCESSING,
                        'status_name'=>Define::$arrSmsStatus[Define::SMS_STATUS_PROCESSING],
                        'correct_number'=>count($dataSend),
                        'incorrect_number'=>count($dataPhone_err),
                        'incorrect_number_list'=>implode(",",$dataPhone_err),
                        'sms_deadline'=>$send_sms_deadline,
                        'created_date'=>FunctionLib::getDateTime(),);
                    $sms_customer_id = SmsCustomer::createItem($dataInsertSmsCustomer);

                    //web_sms_log: bao nhiêu nhà mạng thì co bấy nhiêu bản ghi
                    foreach ($dataCarriesInput as $carrier_id =>&$val_carr){
                        $dataInsertSmsLog = array(
                            'user_customer_id'=>$user_id,
                            'user_manager_id'=>0,
                            'sms_customer_id'=>$sms_customer_id,
                            'carrier_id'=>$val_carr['carrier_id'],
                            'carrier_name'=>$val_carr['carrier_name'],
                            'total_sms'=>$val_carr['tong_sms'],
                            'status'=>Define::SMS_STATUS_PROCESSING,
                            'status_name'=>Define::$arrSmsStatus[Define::SMS_STATUS_PROCESSING],
                            'send_date'=>FunctionLib::getIntDate(),
                            'sms_deadline'=>$send_sms_deadline,
                            'created_date'=>FunctionLib::getDateTime(),);
                        $sms_log_id = SmsLog::createItem($dataInsertSmsLog);
                        $val_carr['sms_log_id'] = $sms_log_id;
                    }

                    //web_sms_sendTo
                    $dataInsertSmsSendTo = array();
                    foreach ($dataSend as $kk=>$val){
                        $dataInsertSmsSendTo[] = array(
                            'sms_log_id'=>isset($dataCarriesInput[$val['carrier_id']]['sms_log_id']) ? $dataCarriesInput[$val['carrier_id']]['sms_log_id']: 0,
                            'sms_customer_id'=>$sms_customer_id,
                            'user_customer_id'=>$user_id,
                            'carrier_id'=>$val['carrier_id'],
                            'phone_receive'=>$val['phone_number'],
                            'status'=>Define::SMS_STATUS_PROCESSING,
                            'status_name'=>Define::$arrSmsStatus[Define::SMS_STATUS_PROCESSING],
                            'content'=>$val['content'],
                            'content_grafted'=>$val['content'],
                            'created_date'=>FunctionLib::getDateTime(),
                        );
                    }
                    SmsSendTo::insertMultiple($dataInsertSmsSendTo);
                    $return = FunctionLib::returnAPI(200, 'Gui thanh cong !');
                }else{
                    $return = FunctionLib::returnAPI(1016, 'Gui that bai!');
                }
            }else{
                $return = FunctionLib::returnAPI(1015, 'Chua co thong tin nha mang');
            }
        }else{
            $return = FunctionLib::returnAPI(1014, 'Các tham số truyền vào bị lỗi');
        }
        echo FunctionLib::responeJson($return);
    }

    public function authorization()
    {
        $return = array();
        if (isset($_SERVER[Define::CONTENT_TYPE])) {
            if ($_SERVER[Define::CONTENT_TYPE] == Define::APPLICATION_JSON) {
                $data = file_get_contents("php://input");
                $data = json_decode($data, true);

                $token = isset($data[Define::ACCESS_TOKEN]) ? $data[Define::ACCESS_TOKEN] : "";
                $phone = isset($data[Define::PHONE]) ? $data[Define::PHONE] : "";
                $message = isset($data[Define::MESSAGE]) ? $data[Define::MESSAGE] : "";
                $dead_line = isset($data['dead_line']) ? $data['dead_line'] : "";

                if (trim($token != "")) {
                    $result = $this->checkToken($token);
                    if ($result[Define::CODE] == Define::HTTP_STATUS_CODE_200) {
                        $arr = array();
                        foreach ($result[Define::RESPONSE] as $k => $v){
                            $arr[] = (array) $v;
                        }
                        $user_id = $arr[0]['user_id'];
                        self::pushSms($user_id,$phone,$message,$dead_line);
                    } else {
                        $return = FunctionLib::returnAPI($result[Define::CODE], $result[Define::MESSAGE]);
                    }
                } else {
                    $return = FunctionLib::returnAPI(Define::HTTP_STATUS_CODE_401, Define::HTTP_STATUS_MESSAGE_UNAUTHORIZED);
                }
            } else {
                $return = FunctionLib::returnAPI(Define::HTTP_STATUS_CODE_400, Define::HTTP_STATUS_MESSAGE_BAD_REQUEST);
            }
        } else {
            $return = FunctionLib::returnAPI(Define::HTTP_STATUS_CODE_400, Define::HTTP_STATUS_MESSAGE_BAD_REQUEST);
        }

        return FunctionLib::responeJson($return);
    }

    private function checkToken($token)
    {
        date_default_timezone_set(Define::GMT_7_TIME_ZONE);
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
                                WHERE md5(CONCAT(SUBSTRING_INDEX(FROM_BASE64(".Define::KEY_CLIENT_ID."),'_',1),'".Define::SIGN_KEY_TOKEN."')) = '".$clientID."' 
                                AND md5(CONCAT(SUBSTRING_INDEX(FROM_BASE64(".Define::KEY_CLIENT_SECRET."),'_',1),'".Define::SIGN_KEY_TOKEN."')) = '".$clientSecret."'
                                ";

                        $result = FunctionLib::executesSQL($sql);

                        if (!empty($result)){
                            $return = array(
                                Define::CODE => Define::HTTP_STATUS_CODE_200,
                                Define::RESPONSE => $result
                            );
                        }else{
                            $return = array(
                                Define::CODE => Define::HTTP_STATUS_CODE_401,
                                Define::MESSAGE => Define::HTTP_STATUS_MESSAGE_UNAUTHORIZED
                            );
                        }
                    }else{
                        $return = array(
                            Define::CODE => Define::HTTP_STATUS_CODE_401,
                            Define::MESSAGE => Define::HTTP_STATUS_MESSAGE_UNAUTHORIZED
                        );
                    }

                }else{
                    $return = array(
                        Define::CODE => Define::HTTP_STATUS_CODE_401,
                        Define::MESSAGE => Define::HTTP_STATUS_MESSAGE_UNAUTHORIZED
                    );
                }
            } else {
                $return = array(
                    Define::CODE => Define::HTTP_STATUS_CODE_401,
                    Define::MESSAGE => Define::HTTP_STATUS_MESSAGE_TOKEN_EXPIRE
                );
            }
        }else{
            $return = array(
                Define::CODE => Define::HTTP_STATUS_CODE_401,
                Define::MESSAGE => Define::HTTP_STATUS_MESSAGE_UNAUTHORIZED
            );
        }
        return $return;
    }
}
