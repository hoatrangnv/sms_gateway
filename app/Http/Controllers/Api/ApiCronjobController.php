<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\Define;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ModemCom;
use App\Http\Models\UserSetting;

class ApiCronjobController extends BaseApiController{
	
	public function __construct(){
		parent::__construct();
	}

    /**
     * - Reset lại trường "sms_max_com_day", "success_number", "error_number" về 0 và cuối ngày trong bảng "web_modem_Com"
     */
	public function resetModemCom(){
        $dataUpdate['sms_max_com_day'] = 0;
        $dataUpdate['success_number'] = 0;
        $dataUpdate['error_number'] = 0;
        $data = DB::table(Define::TABLE_MODEM_COM)
            ->where('is_active', '=', Define::STATUS_SHOW)
            ->get(array('modem_com_id'));
        if($data){
            foreach ($data as $k=>$modem_com){
                ModemCom::updateItem($modem_com->modem_com_id,$dataUpdate);
            }
            echo count($data).' đã cập nhật xong';
        }
    }

    /**
    - Reset lại trường "count_sms_number" về 0  trong bảng "web_user_setting"
     */
    public function resetUserSetting(){
        $dataUpdate['count_sms_number'] = 0;
        $data = DB::table(Define::TABLE_USER_SETTING)
            ->where('user_id', '>', 0)
            ->get(array('user_setting_id'));
        if($data){
            foreach ($data as $k=>$user_setting){
                UserSetting::updateItem($user_setting->user_setting_id,$dataUpdate);
            }
            echo count($data).' đã cập nhật xong';
        }
    }
}
