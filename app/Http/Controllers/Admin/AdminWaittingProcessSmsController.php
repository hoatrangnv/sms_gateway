<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\SmsLog;
use App\Http\Models\User;
use App\Http\Models\CarrierSetting;
use App\Http\Models\SmsSendTo;
use App\Http\Models\UserSetting;
use App\Http\Models\ModemCom;
use App\Http\Models\Modem;
use App\Http\Models\SystemSetting;
use App\Http\Models\SmsPacket;
use Illuminate\Support\Facades\DB;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class AdminWaittingProcessSmsController extends BaseAdminController
{
    private $permission_view = 'waittingSms_view';
    private $permission_full = 'waittingSms_full';
    private $permission_delete = 'waittingSms_delete';
    private $permission_create = 'waittingSms_create';
    private $permission_edit = 'waittingSms_edit';
    private $arrStatus = array();
    private $error = array();
    private $arrMenuParent = array();
    private $viewPermission = array();//check quyen
    private $infoListUser = array();
    private $infoListModem = array();
    private $arrCarrier = array();
    private $arrDuplicateString = array();
    private $arrOptionType = array();

    public function __construct()
    {
        parent::__construct();

        FunctionLib::link_js(array(
            'admin/js/user.js',
        ));
    }

    public function getDataDefault()
    {
        $this->infoListUser = User::getListUserNameFullName();
        $user_id = ($this->role_type == Define::ROLE_TYPE_SUPER_ADMIN)?0:$this->user_id;
        $this->infoListModem = Modem::getListModemName($user_id);
        $this->arrCarrier = CarrierSetting::getOptionCarrier();
        $this->arrDuplicateString = array(
            1 => FunctionLib::controLanguage('the_first_of_sms'),
            2 => FunctionLib::controLanguage('the_end_of_sms'),
            3 => FunctionLib::controLanguage('the_random_of_sms'));
        $this->arrOptionType = array(
            1 => FunctionLib::controLanguage('concatenation_strings'),
            2 => FunctionLib::controLanguage('concatenation_strings_setting'));
    }

    public function getPermissionPage()
    {
        return $this->viewPermission = [
            'is_root' => $this->is_root ? 1 : 0,
            'user_role_type' => $this->role_type,
            'permission_edit' => in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create' => in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_delete' => in_array($this->permission_delete, $this->permission) ? 1 : 0,
            'permission_full' => in_array($this->permission_full, $this->permission) ? 1 : 0,
        ];
    }

    /**
     * @return view process
     */
    public function view()
    {
        //Check phan quyen.
        if (!$this->is_root && !in_array($this->permission_full, $this->permission) && !in_array($this->permission_view, $this->permission)) {
            return Redirect::route('admin.dashboard', array('error' => Define::ERROR_PERMISSION));
        }
        CGlobal::$pageAdminTitle = 'SMS Waitting Process';
        $pageNo = (int)Request::get('page_no', 1);
        $sbmValue = Request::get('submit', 1);
        $limit = CGlobal::number_show_30;
        $offset = ($pageNo - 1) * $limit;
        $search = $data = array();
        $total = 0;

        $search['carrier_id'] = (int)Request::get('carrier_id', -1);
        $search['from_date'] = Request::get('from_date', '');
        $search['to_date'] = Request::get('to_date', '');
        $search['status'] = array(Define::SMS_STATUS_PROCESSING, Define::SMS_STATUS_REJECT);
        if ($this->role_type == Define::ROLE_TYPE_SUPER_ADMIN) {
            $search['user_customer_id'] = (int)Request::get('user_customer_id', -1);
        } else {
            $search['user_customer_id'] = $this->user_id;
        }
        //$search['field_get'] = 'menu_name,menu_id,parent_id';//cac truong can lay
        $data = SmsLog::searchByCondition($search, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getNewPager(3, $pageNo, $total, $limit, $search) : '';

        $this->getDataDefault();
        $optionCarrier = FunctionLib::getOption(array(-1 => '') + $this->arrCarrier, $search['carrier_id']);
        $optionListUser = FunctionLib::getOption(array(-1 => '') + $this->infoListUser, $search['user_customer_id']);

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminWaittingProcessSms.view', array_merge([
            'data' => $data,
            'search' => $search,
            'total' => $total,
            'stt' => ($pageNo - 1) * $limit,
            'paging' => $paging,
            'optionCarrier' => $optionCarrier,
            'optionListUser' => $optionListUser,
            'infoListUser' => $this->infoListUser,
        ], $this->viewPermission));
    }

    /**
     * Sửa sms log
     * @param $ids
     * @return
     */
    public function getItem($type_page, $ids)
    {
        $sms_log_id = FunctionLib::outputId($ids);
        if (!$this->is_root && !in_array($this->permission_full, $this->permission) && !in_array($this->permission_edit, $this->permission) && !in_array($this->permission_create, $this->permission)) {
            return Redirect::route('admin.dashboard', array('error' => Define::ERROR_PERMISSION));
        }
        $data = array();
        if ($sms_log_id > 0) {
            $data = SmsSendTo::getListSmsSendToBySmsLogId($sms_log_id);
        }
        $choose_type = Request::get('choose_type', 2);
        $concatenation_strings_1 = Request::get('concatenation_strings', '');

        $this->getDataDefault();
        $optionDuplicateString = FunctionLib::getOption($this->arrDuplicateString, 1);
        $optionChooseType = FunctionLib::getOption($this->arrOptionType, $choose_type);

        //get chuỗi setup
        $userId = ($type_page == 2) ? $this->user_id : 0;
        $systemSetting = SystemSetting::getSystemSetting($userId);
        $concatenation_strings = ($choose_type == 2)?((isset($systemSetting->concatenation_strings) && trim($systemSetting->concatenation_strings) != '') ? $systemSetting->concatenation_strings : ''): $concatenation_strings_1;

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminWaittingProcessSms.editSms', array_merge([
            'data' => $data,
            'id' => $sms_log_id,
            'type_page' => $type_page,
            'choose_type' => $choose_type,
            'concatenation_strings' => $concatenation_strings,
            'arrCarrier' => $this->arrCarrier,
            'optionDuplicateString' => $optionDuplicateString,
            'concatenation_strings_1' => $concatenation_strings_1,
            'optionChooseType' => $optionChooseType,
        ], $this->viewPermission));
    }

    public function postItem($type_page, $ids)
    {
        $sms_log_id = FunctionLib::outputId($ids);
        if (!$this->is_root && !in_array($this->permission_full, $this->permission) && !in_array($this->permission_edit, $this->permission) && !in_array($this->permission_create, $this->permission)) {
            return Redirect::route('admin.dashboard', array('error' => Define::ERROR_PERMISSION));
        }
        $choose_type = Request::get('choose_type', 2);
        $concatenation_strings = ($choose_type == 2)? Request::get('concatenation_strings', ''): Request::get('concatenation_strings_1', '');
        $concatenation_rule = Request::get('concatenation_rule', 1);
        if (trim($concatenation_strings) == '') {
            $this->error[] = FunctionLib::controLanguage('concatenation_strings', $this->languageSite) . ' null';
        }

        if (empty($this->error) && $sms_log_id > 0) {
            $arrString = explode(',', $concatenation_strings);
            $numberTotal = count($arrString);
            $numberStart = 0;
            $dataSmsLog = SmsSendTo::getListSmsSendToBySmsLogId($sms_log_id);
            if ($dataSmsLog) {
                foreach ($dataSmsLog as $sms_log) {
                    //$string_ghep = $arrString[rand(0, (count($arrString) - 1))];
                    $numberStart = ($numberStart < $numberTotal)? $numberStart: 0;
                    $string_ghep = $arrString[$numberStart];
                    $string_send = $sms_log->content_grafted;
                    FunctionLib::stringConcatenation($string_send, $string_ghep, $concatenation_rule);
                    $dataUpdate['content_grafted'] = $string_send;
                    SmsSendTo::updateItem($sms_log->sms_sendTo_id, $dataUpdate);
                    $numberStart ++;
                }
                return Redirect::route('admin.waittingSmsEdit', array('id' => $ids, 'choose_type' => $choose_type, 'type_page' => $type_page, 'concatenation_strings' => $concatenation_strings));
            }
        }

        $data = array();
        if ($sms_log_id > 0) {
            $data = SmsSendTo::getListSmsSendToBySmsLogId($sms_log_id);
        }
        //FunctionLib::debug($data);
        $this->getDataDefault();
        $optionChooseType = FunctionLib::getOption($this->arrOptionType, $choose_type);
        $optionDuplicateString = FunctionLib::getOption($this->arrDuplicateString, $concatenation_rule);

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminWaittingProcessSms.editSms', array_merge([
            'data' => $data,
            'error' => $this->error,
            'id' => $sms_log_id,
            'type_page' => $type_page,
            'choose_type' => $choose_type,
            'concatenation_strings' => $concatenation_strings,
            'arrCarrier' => $this->arrCarrier,
            'optionDuplicateString' => $optionDuplicateString,
            'optionChooseType' => $optionChooseType,
        ], $this->viewPermission));
    }

    //ajax
    public function changeUserWaittingProcessSms()
    {
        $data = array('isIntOk' => 0, 'msg' => 'Error update');
        if (!$this->is_root && !in_array($this->permission_full, $this->permission)) {
            return Response::json($data);
        }
        $user_manager_id = (int)Request::get('user_manager_id', 0);
        $sms_log_id = (int)Request::get('sms_log_id', 0);
        $total_sms = (int)Request::get('total_sms', 0);
        if ($sms_log_id > 0 && $user_manager_id > 0) {
            $infoUser = UserSetting::getUserSettingByUserId($user_manager_id);
            if (!empty($infoUser)) {
                //Tổng số lượng SMS cần gửi phải <= Tổng số SMS Trạm đó có thể gửi trong ngày - Số lượng tin đã chuyển xử lý trong ngày
                $sms_max = $infoUser->sms_max;
                $infoModemCom = ModemCom::getListModemComActionWithUserId($user_manager_id);
                $total_send_day = $sms_max * count($infoModemCom);
                $number_send_ok = $total_send_day - $infoUser->count_sms_number;
                if ($total_sms <= $number_send_ok) {
                    //web_sms_log
                    $dataUpdate['user_manager_id'] = $user_manager_id;
                    $dataUpdate['status'] = Define::SMS_STATUS_PROCESSING;
                    $dataUpdate['status_name'] = Define::$arrSmsStatus[Define::SMS_STATUS_PROCESSING];
                    SmsLog::updateItem($sms_log_id, $dataUpdate);

                    //web_sms_sendTo
                    DB::table(Define::TABLE_SMS_SENDTO)
                        ->where('sms_log_id', $sms_log_id)
                        ->update($dataUpdate);

                    //web_user_setting
                    $dataUpdateUser['count_sms_number'] = $total_sms + $infoUser->count_sms_number;
                    DB::table(Define::TABLE_USER_SETTING)
                        ->where('user_id', $user_manager_id)
                        ->update($dataUpdateUser);
                    $data['isIntOk'] = 1;
                } else {
                    $data['msg'] = 'Số lượng Com hoạt động không đủ đáp ứng';
                }
            } else {
                $data['msg'] = 'Không có thông tin trạm được gán';
            }
        }
        return Response::json($data);
    }

    //ajax get noi dung text setting
    public function getSettingContentAttach()
    {
        $data = array('isIntOk' => 0, 'data' => array(), 'msg' => 'Error update');
        if (!$this->is_root && !in_array($this->permission_full, $this->permission)) {
            return Response::json($data);
        }
        $type_page = (int)Request::get('type_page', 1);
        $userId = ($type_page == 2) ? $this->user_id : 0;
        $systemSetting = SystemSetting::getSystemSetting($userId);
        if (!empty($systemSetting)) {
            $data['isIntOk'] = 1;
            $data['msg'] = (isset($systemSetting->concatenation_strings) && trim($systemSetting->concatenation_strings) != '') ? $systemSetting->concatenation_strings : '';
        }
        return Response::json($data);
    }

    //ajax get nội dung gửi SMS
    public function getContentGraftedSms()
    {
        $data = array('isIntOk' => 0, 'data' => array(), 'msg' => '');
        if (!$this->is_root && !in_array($this->permission_full, $this->permission)) {
            return Response::json($data);
        }
        $sms_sendTo_id = (int)Request::get('sms_sendTo_id', 0);
        $sms_sendTo = SmsSendTo::find($sms_sendTo_id);
        if (!empty($sms_sendTo)) {

            $data['isIntOk'] = 1;
            $data['sms_sendTo_id'] = $sms_sendTo_id;
            $data['content_grafted'] = (isset($sms_sendTo->content_grafted) && trim($sms_sendTo->content_grafted) != '') ? $sms_sendTo->content_grafted : '';
        }
        return Response::json($data);
    }

    //ajax
    public function submitContentGraftedSms()
    {
        $data = array('isIntOk' => 0, 'data' => array(), 'msg' => '');
        if (!$this->is_root && !in_array($this->permission_full, $this->permission)) {
            return Response::json($data);
        }
        $sms_sendTo_id = (int)Request::get('sms_sendTo_id', 0);
        $content_grafted = Request::get('content_grafted', '');

        if ($sms_sendTo_id > 0 && trim($content_grafted) != '') {
            $dataUpdate['content_grafted'] = $content_grafted;
            SmsSendTo::updateItem($sms_sendTo_id, $dataUpdate);
            $data['isIntOk'] = 1;
        }
        return Response::json($data);
    }

    /****************************************************************************************************************
     *
     * Phần xử lý SMS chờ gửi
     *****************************************************************************************************************/
    /**
     * @return view Send
     */
    public function viewSend()
    {
        //Check phan quyen.
        if (!$this->is_root && !in_array($this->permission_full, $this->permission) && !in_array($this->permission_view, $this->permission)) {
            return Redirect::route('admin.dashboard', array('error' => Define::ERROR_PERMISSION));
        }
        CGlobal::$pageAdminTitle = 'SMS Waitting Send';
        $pageNo = (int)Request::get('page_no', 1);
        $sbmValue = Request::get('submit', 1);
        $limit = CGlobal::number_show_30;
        $offset = ($pageNo - 1) * $limit;
        $search = $data = array();
        $total = 0;

        $search['carrier_id'] = (int)Request::get('carrier_id', -1);
        $search['from_date'] = Request::get('from_date', '');
        $search['to_date'] = Request::get('to_date', '');
        $search['manager_id'] = 1;
        $search['status'] = array(Define::SMS_STATUS_PROCESSING, Define::SMS_STATUS_REJECT);
        if ($this->role_type == Define::ROLE_TYPE_SUPER_ADMIN) {
            $search['user_manager_id'] = (int)Request::get('user_customer_id', -1);
        } else {
            $search['user_manager_id'] = $this->user_id;
        }

        //$search['field_get'] = 'menu_name,menu_id,parent_id';//cac truong can lay
        $data = SmsLog::searchByCondition($search, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getNewPager(3, $pageNo, $total, $limit, $search) : '';

        $this->getDataDefault();
        $optionCarrier = FunctionLib::getOption(array(-1 => '') + $this->arrCarrier, $search['carrier_id']);
        $optionListUser = FunctionLib::getOption(array(-1 => '') + $this->infoListUser, isset($search['user_manager_id']));

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminWaittingProcessSms.viewSend', array_merge([
            'data' => $data,
            'search' => $search,
            'total' => $total,
            'stt' => ($pageNo - 1) * $limit,
            'paging' => $paging,
            'optionCarrier' => $optionCarrier,
            'optionListUser' => $optionListUser,
            'infoListModem' => $this->infoListModem,
            'infoListUser' => $this->infoListUser,
        ], $this->viewPermission));
    }

    //ajax
    public function changeModemWaittingSendSms()
    {
        $data = array('isIntOk' => 0, 'msg' => 'Error update');
        if (!$this->is_root && !in_array($this->permission_full, $this->permission)) {
            return Response::json($data);
        }
        $modem_id = (int)Request::get('list_modem', 0);
        $sms_log_id = (int)Request::get('sms_log_id', 0);
        $total_sms = (int)Request::get('total_sms', 0);

        //lấy thông tin người quản lý modem
        $infoSmsLog = SmsLog::find($sms_log_id);
        $infoModem = Modem::find($modem_id);
        $user_manager_id = (isset($infoModem->user_id)) ? $infoModem->user_id : 0;

        if ($sms_log_id > 0 && $user_manager_id > 0) {
            $infoUser = UserSetting::getUserSettingByUserId($user_manager_id);
            if (!empty($infoUser)) {

                $sms_max = $infoUser->sms_max;
                $infoModemCom = ModemCom::getListModemComActionWithModemId($modem_id);

                //Tổng số SMS Modem đó có thể gửi trong ngày = sms_max (trong bảng "web_user_setting") * Count số COM đang on của Modem được chọn (Điều kiện: is_active = 1
                $total_send_day = $sms_max * count($infoModemCom);

                //Tổng số SMS Modem đó đã gửi = Sum(sms_max_com_day) (trong bảng "web_user_setting") (Điều kiện: is_active = 1 và modem_id)
                $total_modem_da_gui = 0;
                foreach ($infoModemCom as $key => $modemCom) {
                    $total_modem_da_gui = $total_modem_da_gui + $modemCom->sms_max_com_day;
                }

                //Tổng số lượng SMS cần gửi phải <= Tổng số SMS Modem đó có thể gửi trong ngày - Tổng số SMS Modem đó đã gửi
                $number_send_ok = $total_send_day - $total_modem_da_gui;
                //FunctionLib::debug($total_sms.'==='.$total_send_day.'===='.$total_modem_da_gui);
                if ($total_sms <= $number_send_ok) {
                    //web_sms_log
                    $dataUpdate['list_modem'] = $modem_id;
                    $dataUpdate['status'] = Define::SMS_STATUS_PROCESSING;
                    SmsLog::updateItem($sms_log_id, $dataUpdate);

                    //web_sms_packet
                    $dataPacket['type'] = 1;
                    $dataPacket['sms_log_id'] = $sms_log_id;
                    $dataPacket['send_successful'] = 0;
                    $dataPacket['send_fail'] = 0;
                    $dataPacket['user_manager_id'] = $user_manager_id;
                    $dataPacket['modem_id'] = $modem_id;
                    $dataPacket['modem_history'] = $modem_id;//??????? Cộng chuỗi ghi nhận modem đã được chọn xử lý gửi gói tin
                    $dataPacket['sms_deadline'] = $infoSmsLog->sms_deadline;
                    $dataPacket['created_date'] = FunctionLib::getDateTime();
                    $dataPacket['status'] = null;
                    SmsPacket::createItem($dataPacket);

                    //web_sms_sendto
                    $dataUpdateSmsSendTo['modem_id'] = $modem_id;
                    DB::table(Define::TABLE_SMS_SENDTO)
                        ->where('sms_log_id', $sms_log_id)
                        ->update($dataUpdateSmsSendTo);

                    $data['isIntOk'] = 1;
                } else {
                    $data['msg'] = 'Số lượng Com hoạt động không đủ đáp ứng';
                }
            } else {
                $data['msg'] = 'Không có thông tin trạm được gán';
            }
        }else {
            $data['msg'] = 'Modem ko phải của KH này';
        }
        return Response::json($data);
    }

    //ajax
    public function refuseModem()
    {
        $data = array('isIntOk' => 0, 'data' => array(), 'msg' => ' Cập nhật lôi');
        if (!$this->is_root && !in_array($this->permission_full, $this->permission)) {
            return Response::json($data);
        }
        $sms_log_id = (int)Request::get('sms_log_id', 0);
        if ($sms_log_id > 0) {
            $dataUpdate['status'] = Define::SMS_STATUS_REJECT;
            $dataUpdate['status_name'] = Define::$arrSmsStatus[Define::SMS_STATUS_REJECT];
            if (SmsLog::updateItem($sms_log_id, $dataUpdate)) {
                $infoSmsLog = SmsLog::find($sms_log_id);
                $user_manager_id = $infoSmsLog->user_manager_id;
                $infoUser = UserSetting::getUserSettingByUserId($user_manager_id);

                //web_user_setting
                $dataUpdateUser['count_sms_number'] = $infoUser->count_sms_number - $infoSmsLog->total_sms;
                DB::table(Define::TABLE_USER_SETTING)
                    ->where('user_id', $user_manager_id)
                    ->update($dataUpdateUser);

                $data['isIntOk'] = 1;
                $data['msg'] = 'Cập nhật thành công';
            }
        }
        return Response::json($data);
    }
}
