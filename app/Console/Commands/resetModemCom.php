<?php
/**
 * QuynhTM:add
 */

namespace App\Console\Commands;
use Illuminate\Console\Command;

use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\Define;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ModemCom;
use App\Http\Models\UserSetting;

class resetModemCom extends Command{
    protected $signature = 'resetModemCom';
    protected $description = 'Reset Modem com';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
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
}
