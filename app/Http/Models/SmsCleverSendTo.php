<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;

class SmsCleverSendTo extends BaseModel
{
    protected $table = Define::TABLE_SMS_CLEVER;
    protected $primaryKey = 'sms_clever_id';
    public $timestamps = false;

    protected $fillable = array('key_action', 'user_customer_id', 'carrier_id', 'carrier_name', 'phone_receive', 'content', 'send_sms_deadline', 'created_date');

    public static function insertMultiple($dataInput){
        $str_sql = FunctionLib::buildSqlInsertMultiple(Define::TABLE_SMS_CLEVER, $dataInput);
        if(trim($str_sql) != ''){
            DB::statement($str_sql);
            return true;
        }else{
            return false;
        }
    }

    public static function createItem($data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SmsCleverSendTo();
            $fieldInput = $checkData->checkField($data);
            $item = new SmsCleverSendTo();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            //self::removeCache($item->sms_clever_id,$item);
            return $item->sms_clever_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id,$data){

        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SmsCleverSendTo();
            $fieldInput = $checkData->checkField($data);
            $item = SmsCleverSendTo::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            //self::removeCache($item->sms_clever_id,$item);
            return $item->sms_clever_id;
        } catch (PDOException $e) {
            //var_dump($e->getMessage());
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public function checkField($dataInput) {
        $fields = $this->fillable;
        $dataDB = array();
        if(!empty($fields)) {
            foreach($fields as $field) {
                if(isset($dataInput[$field])) {
                    $dataDB[$field] = $dataInput[$field];
                }
            }
        }
        return $dataDB;
    }

    public static function deleteItem($id){
        if($id <= 0) return false;
        try {
            DB::connection()->getPdo()->beginTransaction();
            $item = SmsCleverSendTo::find($id);
            if($item){
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            //self::removeCache($item->sms_clever_id,$item);
            return true;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
            return false;
        }
    }

    public static function searchByCondition($dataSearch = array(), $limit =0, $offset=0, &$total){
//        FunctionLib::debug($dataSearch);
        try{
            $query = SmsCleverSendTo::where('sms_clever_id','>',0);
            if (isset($dataSearch['key_action']) && $dataSearch['key_action'] > 0) {
                $query->where('key_action',$dataSearch['key_action']);
            }
            if (isset($dataSearch['user_customer_id']) && $dataSearch['user_customer_id'] > 0) {
                $query->where('user_customer_id',$dataSearch['user_customer_id']);
            }

            $total = $query->count();
            $query->orderBy('sms_clever_id', 'desc');

            //get field can lay du lieu
            $fields = (isset($dataSearch['field_get']) && trim($dataSearch['field_get']) != '') ? explode(',',trim($dataSearch['field_get'])): array();
            if(!empty($fields)){
                $result = $query->take($limit)->skip($offset)->get($fields);
            }else{
                $result = $query->take($limit)->skip($offset)->get();
            }
            return $result;

        }catch (PDOException $e){
            throw new PDOException();
        }
    }

}
