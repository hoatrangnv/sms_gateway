<?php
/**
 * QuynhTM:add
 */

namespace App\Console\Commands;
use Illuminate\Console\Command;

use App\Library\AdminFunction\Define;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ModemCom;

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
            $total_update = 0;
            foreach ($data as $k=>$modem_com){
                if(ModemCom::updateItem($modem_com->modem_com_id,$dataUpdate)){
                    $total_update ++;
                }
            }
            echo 'Co tong: '.count($total_update).' da cap nhat xong';
        }
    }
}
