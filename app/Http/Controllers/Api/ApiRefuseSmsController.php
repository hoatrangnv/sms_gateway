<?php
/**
 * QuynhTM
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use Illuminate\Http\Request;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\Define;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Modem;
use App\Http\Models\SmsPacket;
use App\Http\Models\SmsLog;

class ApiRefuseSmsController extends BaseApiController{
	
	public function __construct(){
		parent::__construct();
	}

	//'Từ chối gói tin đã được chuyển xử lý gửi tin

    /**
     * @param Request $request
     * @return mixed
     * http://localhost/sms_gateway/api/refuseModemSend:post
     * {
    "packet_id": "8",
    "stauts": "-1"
    }
     */
	public function refuseModemSend(Request $request){
        $packet_id = $request->json('packet_id');
        if($packet_id > 0){
            //lay thong tin packet
            $infoPacket = SmsPacket::find($packet_id);
            if($infoPacket){
                $user_manager_id = $infoPacket->user_manager_id;
                $modem_history = $infoPacket->modem_history;
                $sms_log_id = $infoPacket->sms_log_id;
                $arrHistory = explode(',',$modem_history);
                //danh sách các modem của người dùng
                $listModem = Modem::getListModemName($user_manager_id);
                $modem_id = 0;
                foreach ($listModem as $moId=>$moName){
                    if(!in_array($moId,$arrHistory)){
                        $modem_id = $moId;
                        break;
                    }else{
                        $modem_id = -1;
                    }
                }
                if($modem_id > 0){
                    $arrHistory[] = $modem_id;
                    $dataUpdate['modem_id'] = $modem_id;
                    $dataUpdate['status'] = '';
                    $dataUpdate['modem_history'] = implode(',',$arrHistory);
                    if(DB::table(Define::TABLE_SMS_PACKET)->where('sms_log_id', $sms_log_id)->update($dataUpdate)){
                        SmsLog::updateItem($sms_log_id,array('list_modem'=>$modem_id));
                        return $this->returnResultSuccess(array(),'Đã chọn modem_id: '.$modem_id.' để cập nhật');
                    }
                }else{
                    SmsLog::updateItem($sms_log_id,array('list_modem'=>''));
                    DB::table(Define::TABLE_SMS_PACKET)->where('sms_log_id', '=', $sms_log_id)->delete();
                    return $this->returnResultSuccess(array(),'Hết modem hợp lý để cập nhật.Xóa bản ghi Packet');
                }
            }else{
                return $this->returnResultError(array(),'Không tồn tại dữ liệu này');
            }
        }else{
            return $this->returnResultError(array(),'Dữ liệu không đúng');
        }
    }
}
