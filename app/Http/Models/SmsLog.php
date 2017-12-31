<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;

class SmsLog extends BaseModel
{
    protected $table = Define::TABLE_SMS_LOG;
    protected $primaryKey = 'sms_log_id';
    public $timestamps = false;

    protected $fillable = array('user_customer_id', 'sms_customer_id', 'carrier_id', 'carrier_name', 'user_manager_id',
        'total_sms', 'send_sussesssful', 'send_fail', 'sms_max', 'status', 'status_name', 'send_date', 'created_date', 'cost', 'sms_deadline', 'list_modem');

    public static function createItem($data)
    {
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SmsLog();
            $fieldInput = $checkData->checkField($data);
            $item = new SmsLog();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->sms_log_id, $item);
            return $item->sms_log_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id, $data)
    {
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SmsLog();
            $fieldInput = $checkData->checkField($data);
            $item = SmsLog::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($item->sms_log_id, $item);
            return true;
        } catch (PDOException $e) {
            //var_dump($e->getMessage());
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public function checkField($dataInput)
    {
        $fields = $this->fillable;
        $dataDB = array();
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if (isset($dataInput[$field])) {
                    $dataDB[$field] = $dataInput[$field];
                }
            }
        }
        return $dataDB;
    }

    public static function deleteItem($id)
    {
        if ($id <= 0) return false;
        try {
            DB::connection()->getPdo()->beginTransaction();
            $item = SmsLog::find($id);
            if ($item) {
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->sms_log_id, $item);
            return true;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
            return false;
        }
    }

    public static function searchByCondition($dataSearch = array(), $limit = 0, $offset = 0, &$total)
    {
//        FunctionLib::debug($dataSearch);
        try {
            $query = SmsLog::where('sms_log_id', '>', 0);
            if (isset($dataSearch['carrier_id']) && $dataSearch['carrier_id'] != -1) {
                $query->where('carrier_id', '=', $dataSearch['carrier_id']);
            }
            if (isset($dataSearch['user_customer_id']) && $dataSearch['user_customer_id'] != -1) {
                $query->where('user_customer_id', '=', $dataSearch['user_customer_id']);
            }
            if (isset($dataSearch['manager_id']) && $dataSearch['manager_id'] > 0) {
                $query->where('user_manager_id', '>', 0);
            }
            if (isset($dataSearch['user_manager_id']) && $dataSearch['user_manager_id'] > 0) {
                $query->where('user_manager_id', '=', $dataSearch['user_manager_id']);
            }
            if (isset($dataSearch['time_check_connect']) && $dataSearch['time_check_connect'] != '') {
                $query->where('time_check_connect', 'LIKE', '%' . $dataSearch['time_check_connect'] . '%');
            }
            if (isset($dataSearch['status']) && is_array($dataSearch['status'])) {
                $query->whereIn('status', $dataSearch['status']);
            } elseif (isset($dataSearch['status']) && $dataSearch['status'] != '') {
                $query->where('status', '=', $dataSearch['status']);
            }
            if (isset($dataSearch['from_date']) && $dataSearch['from_date'] != '') {
                $query->where('sms_deadline', '>=', strtotime($dataSearch['from_date'] . ' 00:00:00'));
            }
            if (isset($dataSearch['to_date']) && $dataSearch['to_date'] != '') {
                $query->where('sms_deadline', '<=', strtotime($dataSearch['to_date'] . ' 23:59:59'));
            }

            $total = $query->count();
            $query->orderBy('status', 'asc')
                ->orderBy('list_modem', 'asc')->orderBy('user_manager_id', 'asc');

            //get field can lay du lieu
            $fields = (isset($dataSearch['field_get']) && trim($dataSearch['field_get']) != '') ? explode(',', trim($dataSearch['field_get'])) : array();
            if (!empty($fields)) {
                $result = $query->take($limit)->skip($offset)->get($fields);
            } else {
                $result = $query->take($limit)->skip($offset)->get();
            }
            return $result;

        } catch (PDOException $e) {
            throw new PDOException();
        }
    }

    public static function getNumberNotifyUser($user_id,$role_type){
        if($role_type == Define::ROLE_TYPE_SUPER_ADMIN){
            $data = DB::select(trim('SELECT count(*) as numberTotal FROM '.Define::TABLE_SMS_LOG.' WHERE `status` = '.Define::SMS_STATUS_PROCESSING.' and user_manager_id = 0 OR `status` = '.Define::SMS_STATUS_REJECT.' and user_manager_id <> 0'));
            return $data[0]->numberTotal;
        }elseif($role_type == Define::ROLE_TYPE_ADMIN){
            $data = DB::select(trim('SELECT count(*) as numberTotal FROM '.Define::TABLE_SMS_LOG.' WHERE user_manager_id = '.$user_id.' AND (`status` = '.Define::SMS_STATUS_PROCESSING.' and list_modem = 0 OR `status` = '.Define::SMS_STATUS_REJECT.' and list_modem <> 0)'));
            return $data[0]->numberTotal;
        }
        return 0;
    }

    public static function removeCache($id = 0, $data)
    {
        if ($id > 0) {
            //Cache::forget(Define::CACHE_CATEGORY_ID.$id);
        }
    }
}
