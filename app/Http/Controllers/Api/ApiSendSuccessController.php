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
use App\Http\Models\ModemCom;
use App\Http\Models\Modem;
use App\Http\Models\UserSetting;
use App\Http\Models\SmsPacket;
use App\Http\Models\SmsLog;

class ApiSendSuccessController extends BaseApiController{
	
	public function __construct(){
		parent::__construct();
	}
     public function sendSmsSuccess(Request $request){
         $packet_id = $request->json('packet_id');
         $type = $request->json('type');
         $sms_log_id = $request->json('sms_log_id');
         $send_successful = $request->json('send_successful');
         $send_fail = $request->json('send_fail');
         $status = $request->json('status');

         /**
          * Table "web_user_carrier_setting", "web_sms_log": Dựa vào thông tin 2 bảng này để lấy 2 giá trị bên dưới
         manager_sms_cost	Chi phí cần gửi 1 tin tương ứng với khách hàng (input:  carrier_id, user_customer_id)
         customer_sms_cost	Chi phí trả khi gửi 1 tin tương ứng với tài khoản trạm (input:  carrier_id, user_customer_id)

          * Table "web_sms_report": Dữ liệu báo cáo theo admin
         sms_report_id	Update nếu tôn tại bản ghi theo (user_id, carrier_id, hour, day, month, year)
         carrier_id	id nhà mạng
         success_number	Tổng số gửi thành công (Nếu update thì công dồn)
         fail_number	Tổng số gửi thất bại (Nếu update thì công dồn)
         hour	Giờ ghi nhận gửi
         day	Ngày ghi nhận gửi
         month	Tháng ghi nhận gửi
         year	Năm ghi nhận gửi
         cost	+= send_successful *manager_sms_cost
         user_id	= user_manager_id trong bảng "web_sms_log"
         role_type	= 2

          * Table "web_sms_report": Dữ liệu báo cáo theo customer
         sms_report_id	Update nếu tôn tại bản ghi theo (user_id, carrier_id, hour, day, month, year)
         carrier_id	id nhà mạng
         success_number	Tổng số gửi thành công (Nếu update thì công dồn)
         fail_number	Tổng số gửi thất bại (Nếu update thì công dồn)
         hour	Giờ ghi nhận gửi
         day	Ngày ghi nhận gửi
         month	Tháng ghi nhận gửi
         year	Năm ghi nhận gửi
         cost	+= send_successful *customer_sms_cost
         user_id	= user_customer_id trong bảng "web_sms_log"
         role_type	= 3

          * Table "web_sms_log" => Update
         sms_log_id	id gói tin theo nhà mạng cần update
         send_successful	Tổng số gửi thành công lấy từ giá trị trả về qua API
         send_fail	Tổng số gửi thất bại lấy từ giá trị trả về qua API
         status	= 1
         status_name	= Successful
         cost	= send_successful *customer_sms_cost
          *
         Table "web_sms_customer" => Update
         sms_customer_id	id gói tin khách hàng cần update
         status	= 1  (Chỉ cập nhật nếu tất cả các gói tin trong web_sms_log đã update succsessul)
         status_name	= Successful (chỉ cập nhật nếu tất cả các gói tin trong web_sms_log đã update succsessul)
         cost	= sum (cost trong sms_log_id theo user_customer_id)
          *
         Table "web_user_setting" => Cộng tiền cho  admin
         account_balance	Cộng tiền nếu role_type = 2  payment_type = 1 (+= send_succesful *manager_sms_cost)
         user_id	id admin
          *
         Table "web_user_setting" => Trừ tiền của khách hàng
         account_balance	Cộng tiền nếu role_type = 3 và payment_type = 1  (-= send_succesful *customer_sms_cost)
         user_id	id khách hàng
          *
         Table "web_sms_packet"
         Xóa bản ghi tương ứng theo sms_log_id

          */
	    return $this->returnResultSuccess(array());
     }
}
