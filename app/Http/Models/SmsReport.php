<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;

class SmsReport extends BaseModel
{
    protected $table = Define::TABLE_SMS_REPORT;
    protected $primaryKey = 'sms_report_id';
    public $timestamps = false;

    protected $fillable = array('user_id', 'role_type', 'cost', 'carrier_id', 'success_number', 'fail_number',
        'hour', 'day', 'month', 'year', 'created_date');

    public static function createItem($data)
    {
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SmsReport();
            $fieldInput = $checkData->checkField($data);
            $item = new SmsReport();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->sms_report_id, $item);
            return $item->sms_report_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id, $data)
    {
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SmsReport();
            $fieldInput = $checkData->checkField($data);
            $item = SmsReport::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($item->sms_report_id, $item);
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
            $item = SmsReport::find($id);
            if ($item) {
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->sms_report_id, $item);
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
            $query = SmsReport::where('sms_report_id', '>', 0);
            if (isset($dataSearch['time_check_connect']) && $dataSearch['time_check_connect'] != '') {
                $query->where('time_check_connect', 'LIKE', '%' . $dataSearch['time_check_connect'] . '%');
            }

            $total = $query->count();
            $query->orderBy('sms_report_id', 'desc');

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

    public static function removeCache($id = 0, $data)
    {
        if ($id > 0) {
            //Cache::forget(Define::CACHE_CATEGORY_ID.$id);
            // Cache::forget(Define::CACHE_ALL_CHILD_CATEGORY_BY_PARENT_ID.$id);
        }
    }

    public static function executesSQL($str_sql = '')
    {
        //return (trim($str_sql) != '') ? DB::statement(trim($str_sql)): array();
        return (trim($str_sql) != '') ? DB::select(trim($str_sql)) : array();
    }
}
