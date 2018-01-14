<?php
/**
 * QuynhTM
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Http\Models\User;
use Illuminate\Http\Request;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\Define;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ModemCom;
use App\Http\Models\SmsReport;
use App\Http\Models\UserSetting;
use App\Http\Models\SmsPacket;
use App\Http\Models\SmsLog;

class ApiSendSuccessController extends BaseApiController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function sendSmsSuccess(Request $request)
    {
        $packet_id = $request->json('packet_id');
        $send_successful = $request->json('send_successful');
        $send_fail = $request->json('send_fail');
        $infoPacket = SmsPacket::find($packet_id);

        if ($infoPacket) {
            $user_manager_id = $infoPacket->user_manager_id;
            $sms_log_id = $infoPacket->sms_log_id;

            //lấy thông tin nhà mạng
            $smsLog = SmsLog::find($sms_log_id);
            $carrier_id = ($smsLog) ? $smsLog->carrier_id : 0;
            $user_customer_id = ($smsLog) ? $smsLog->user_customer_id : 0;
            $sms_customer_id = ($smsLog) ? $smsLog->sms_customer_id : 0;

            if ($carrier_id > 0 && $user_manager_id > 0 && $user_customer_id > 0) {
                //cost admin
                $userCarrierSetting = DB::table(Define::TABLE_USER_CARRIER_SETTING)
                    ->where('carrier_id', '=', $carrier_id)
                    ->where('user_id', '=', $user_manager_id)->get(array('cost'));
                $costAdmin = 0;
                if ($userCarrierSetting) {
                    $costAdmin = $userCarrierSetting[0]->cost;
                }
                //cost customer
                $userCarrierSetting2 = DB::table(Define::TABLE_USER_CARRIER_SETTING)
                    ->where('carrier_id', '=', $carrier_id)
                    ->where('user_id', '=', $user_customer_id)->get(array('cost'));
                //FunctionLib::debug($userCarrierSetting2);
                $costCustomer = 0;
                if ($userCarrierSetting2) {
                    $costCustomer = $userCarrierSetting2[0]->cost;
                }

                //cập nhật dữ liệu
                $cost_report_admin = $costAdmin * $send_successful;
                $infoUser = User::find($user_manager_id);
                $role_type = $infoUser->role_type;

                $cost_report_customer = $costCustomer * $send_successful;
                $infoUserCustomer = User::find($user_customer_id);
                $role_type_customer = $infoUserCustomer->role_type;
                if ($role_type > 0 || $role_type_customer > 0) {

                    $admin = $this->updateInforUser($carrier_id,$user_manager_id,$send_successful,$send_fail,$cost_report_admin,$role_type);

                    $customer = $this->updateInforUser($carrier_id,$user_customer_id,$send_successful,$send_fail,$cost_report_customer,$role_type_customer);

                    //web_sms_log của khach hàng
                    $dataSmsLog = array(
                        'send_successful' => $send_successful,
                        'send_fail' => $send_fail,
                        'status' => Define::SMS_STATUS_SUCCESS,
                        'status_name' => Define::$arrSmsStatus[Define::SMS_STATUS_SUCCESS],
                        'cost' => $cost_report_customer,
                    );
                    SmsLog::updateItem($sms_log_id, $dataSmsLog);

                    //web_sms_customer
                    //update cost lúc nào cũng cập nhật
                    //tim status = 0 ở bảng SMS_log where sms_customer_id = $user_customer_id: dùng else thì cập nhật status của Customer
                    $dataSmsCustomer = array(
                        'send_date' => date('Ymd',time()),
                        'cost' => $cost_report_customer,//sum (cost trong sms_log_id theo sms_customer_id = $user_customer_id)
                    );
                    DB::table(Define::TABLE_SMS_CUSTOMER)
                        ->where('sms_customer_id', $sms_customer_id)
                        ->where('user_customer_id', $user_customer_id)
                        ->update($dataSmsCustomer);
                    //kiểm tra xe gói đó thành công xong chưa để cập nhật lại giá
                    $getSmsCustomer = DB::table(Define::TABLE_SMS_LOG)
                        ->where('status', '=', Define::SMS_STATUS_PROCESSING)
                        ->where('sms_customer_id', '=', $sms_customer_id)->get();
                    if(count($getSmsCustomer) == 0){
                        //lấy cost của khách hàng để tính tổng lai
                        $getCostCusto = DB::table(Define::TABLE_SMS_LOG)
                            ->where('sms_customer_id', '=', $sms_customer_id)
                            ->where('user_customer_id', '=', $user_customer_id)->get(array('cost'));
                        $costCustomer2 = 0;
                        if($getCostCusto){
                            foreach ($getCostCusto as $cost2){
                                $costCustomer2 = $costCustomer2+$cost2->cost;
                            }
                        }
                        //update
                        $dataSmsCustomer = array(
                            'status' => Define::SMS_STATUS_SUCCESS,
                            'status_name' => Define::$arrSmsStatus[Define::SMS_STATUS_SUCCESS],
                            'send_date' => date('Ymd',time()),
                            'cost' => $costCustomer2,//sum (cost trong sms_log_id theo sms_customer_id = $user_customer_id)
                        );
                        DB::table(Define::TABLE_SMS_CUSTOMER)
                            ->where('sms_customer_id', $sms_customer_id)
                            ->where('user_customer_id', $user_customer_id)
                            ->update($dataSmsCustomer);
                    }
                    //xóa bảng web_sms_packet
                    SmsPacket::deleteItem($packet_id);
                    return $this->returnResultSuccess();
                }
                return $this->returnResultError(array(), 'Role không tồn tại');
            }
            return $this->returnResultError(array(), 'Dữ liệu không đúng');
        } else {
            return $this->returnResultError(array(), 'Dữ liệu không đúng');
        }
        return $this->returnResultSuccess(array());
    }

    public function updateInforUser($carrier_id,$user_id,$send_successful,$send_fail,$cost_report,$role_type){
        $date = time();
        $hour = date('H', $date);
        $day = date('d', $date);
        $month = date('m', $date);
        $year = date('Y', $date);

        $smsReport = DB::table(Define::TABLE_SMS_REPORT)
            ->where('hour', '=', $hour)
            ->where('day', '=', $day)
            ->where('month', '=', $month)
            ->where('year', '=', $year)
            ->where('carrier_id', '=', $carrier_id)
            ->where('user_id', '=', $user_id)
            ->get();

        $dataInput = array(
            'carrier_id' => $carrier_id,
            'success_number' => $send_successful,
            'fail_number' => $send_fail,
            'cost' => $cost_report,
            'hour' => $hour,
            'day' => $day,
            'month' => $month,
            'year' => $year,
            'user_id' => $user_id,
            'role_type' => $role_type,
            'created_date' => FunctionLib::getDateTime()
        );
        //web_sms_report
        if (!empty($smsReport) && count($smsReport) > 0) {
            $sms_report_id = $smsReport[0]->sms_report_id;
            $dataInput['success_number'] = $smsReport[0]->success_number + $send_successful;
            $dataInput['fail_number'] = $smsReport[0]->fail_number + $send_fail;
            $dataInput['cost'] = $smsReport[0]->cost + $cost_report;
            SmsReport::updateItem($sms_report_id, $dataInput);
        } else {
            SmsReport::createItem($dataInput);
        }

        //web_user_setting
        $infoUserSetting = DB::table(Define::TABLE_USER_SETTING)->where('user_id', '=', $user_id)->get();
        if (!empty($infoUserSetting) && count($infoUserSetting) > 0) {
            $user_setting_id = $infoUserSetting[0]->user_setting_id;
            $account_balance = $infoUserSetting[0]->account_balance;
            $payment_type = $infoUserSetting[0]->payment_type;
            $dataInputUserSetting['account_balance'] = ($role_type == Define::ROLE_TYPE_ADMIN) ? ($account_balance + $cost_report) : ($account_balance - $cost_report);
            if ($payment_type == Define::PAYMENT_TYPE_FIRST) {
                UserSetting::updateItem($user_setting_id, $dataInputUserSetting);
            }
        }
    }

    public function sendSmsSuccess22(Request $request)
    {
        $packet_id = $request->json('packet_id');
        //$type = $request->json('type');
        //$sms_log_id = $request->json('sms_log_id');
        $send_successful = $request->json('send_successful');
        $send_fail = $request->json('send_fail');
        //$status = $request->json('status');
        $infoPacket = SmsPacket::find($packet_id);
        if ($infoPacket) {
            $user_manager_id = $infoPacket->user_manager_id;
            $sms_log_id = $infoPacket->sms_log_id;

            //lấy thông tin nhà mạng
            $smsLog = SmsLog::find($sms_log_id);
            $carrier_id = ($smsLog) ? $smsLog->carrier_id : 0;
            $sms_customer_id = ($smsLog) ? $smsLog->sms_customer_id : 0;

            if ($carrier_id > 0 && $user_manager_id > 0 && $sms_customer_id > 0) {
                //cost admin
                $userCarrierSetting = DB::table(Define::TABLE_USER_CARRIER_SETTING)
                    ->where('carrier_id', '=', $carrier_id)
                    ->where('user_id', '=', $user_manager_id)->get(array('cost'));
                $costAdmin = -1;
                if ($userCarrierSetting) {
                    $costAdmin = $userCarrierSetting[0]->cost;
                }
                //cost customer
                $userCarrierSetting2 = DB::table(Define::TABLE_USER_CARRIER_SETTING)
                    ->where('carrier_id', '=', $carrier_id)
                    ->where('user_id', '=', $sms_customer_id)->get(array('cost'));
                $costCustomer = -1;
                if ($userCarrierSetting) {
                    $costCustomer = $userCarrierSetting2[0]->cost;
                }

                if ($costAdmin > 0) {
                    $cost_report = $costAdmin * $send_successful;
                    $infoUser = User::find($user_manager_id);
                    $role_type = $infoUser->role_type;
                    if ($role_type > 0) {
                        $date = time();
                        $hour = date('H', $date);
                        $day = date('d', $date);
                        $month = date('m', $date);
                        $year = date('Y', $date);

                        $smsReport = DB::table(Define::TABLE_SMS_REPORT)
                            ->where('hour', '=', $hour)
                            ->where('day', '=', $day)
                            ->where('month', '=', $month)
                            ->where('year', '=', $year)
                            ->where('carrier_id', '=', $carrier_id)
                            ->where('user_id', '=', $user_manager_id)
                            ->get();
                        $dataInput = array(
                            'carrier_id' => $carrier_id,
                            'success_number' => $send_successful,
                            'fail_number' => $send_fail,
                            'cost' => $cost_report,
                            'hour' => $hour,
                            'day' => $day,
                            'month' => $month,
                            'year' => $year,
                            'user_id' => $user_manager_id,
                            'role_type' => $role_type,
                            'created_date' => FunctionLib::getDateTime()
                        );
                        //web_sms_report
                        if (!empty($smsReport) && count($smsReport) > 0) {
                            $sms_report_id = $smsReport[0]->sms_report_id;
                            $dataInput['success_number'] = $smsReport[0]->success_number + $send_successful;
                            $dataInput['fail_number'] = $smsReport[0]->fail_number + $send_fail;
                            $dataInput['cost'] = $smsReport[0]->cost + $cost_report;
                            SmsReport::updateItem($sms_report_id, $dataInput);
                        } else {
                            SmsReport::createItem($dataInput);
                        }

                        //web_sms_log
                        $dataSmsLog = array(
                            'send_successful' => $send_successful,
                            'send_fail' => $send_fail,
                            'status' => Define::SMS_STATUS_SUCCESS,
                            'status_name' => Define::$arrSmsStatus[Define::SMS_STATUS_SUCCESS],
                            'cost' => $cost_report,
                        );
                        SmsLog::updateItem($sms_log_id, $dataSmsLog);

                        //web_sms_customer
                        //update cost lúc nào cũng cập nhật
                        //tim status = 0 ở bảng SMS_log where sms_customer_id = $sms_customer_id: dùng else thì cập nhật status của Customer
                        $dataSmsCustomer = array(
                            'status' => Define::SMS_STATUS_SUCCESS,
                            'status_name' => Define::$arrSmsStatus[Define::SMS_STATUS_SUCCESS],
                            'cost' => $cost_report,//sum (cost trong sms_log_id theo sms_customer_id = $sms_customer_id)
                        );
                        DB::table(Define::TABLE_SMS_CUSTOMER)->where('user_customer_id', $user_manager_id)->update($dataSmsCustomer);

                        //web_user_setting
                        $infoUserSetting = DB::table(Define::TABLE_USER_SETTING)->where('user_id', '=', $user_manager_id)->get();
                        if (!empty($infoUserSetting) && count($infoUserSetting) > 0) {
                            $user_setting_id = $infoUserSetting[0]->user_setting_id;
                            $account_balance = $infoUserSetting[0]->account_balance;
                            $payment_type = $infoUserSetting[0]->payment_type;
                            $dataInputUserSetting['account_balance'] = ($role_type == Define::ROLE_TYPE_ADMIN) ? ($account_balance + $cost_report) : ($account_balance - $cost_report);
                            if ($payment_type == Define::PAYMENT_TYPE_FIRST) {
                                UserSetting::updateItem($user_setting_id, $dataInputUserSetting);
                            }
                        }

                        //xóa bảng web_sms_packet
                        SmsPacket::deleteItem($packet_id);
                        return $this->returnResultSuccess();
                    }
                    return $this->returnResultError(array(), 'Role không tồn tại');
                }
                return $this->returnResultError(array(), 'Chưa có giá tiền SMS cho User này');
            }
            return $this->returnResultError(array(), 'Dữ liệu không đúng');
        } else {
            return $this->returnResultError(array(), 'Dữ liệu không đúng');
        }
        return $this->returnResultSuccess(array());
    }
}
