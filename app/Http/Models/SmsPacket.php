<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;

class SmsPacket extends BaseModel
{
    protected $table = Define::TABLE_SMS_PACKET;
    protected $primaryKey = 'packet_id';
    public $timestamps = false;

    protected $fillable = array('type', 'sms_log_id', 'send_successful', 'send_fail', 'user_manager_id',
        'modem_id', 'sms_max','sms_error_max','time_delay_from','time_delay_to','status','modem_history','sms_deadline'
        ,'created_date','updated_date','user_customer_id');

    public static function searchByCondition($dataSearch = array(), $limit =0, $offset=0, &$total){
        try{
            $query = SmsPacket::where('packet_id','>',0);
            $total = $query->count();
            $query->orderBy('packet_id', 'desc');

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

    public static function removeCache($id = 0,$data){
        if($id > 0){
            //Cache::forget(Define::CACHE_CATEGORY_ID.$id);
           // Cache::forget(Define::CACHE_ALL_CHILD_CATEGORY_BY_PARENT_ID.$id);
        }
    }
    public static function createItem($data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SmsPacket();
            $fieldInput = $checkData->checkField($data);
            $item = new SmsPacket();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->packet_id,$item);
            return $item->packet_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id,$data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SmsPacket();
            $fieldInput = $checkData->checkField($data);
            $item = SmsPacket::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($item->packet_id,$item);
            return true;
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
            $item = SmsPacket::find($id);
            if($item){
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->packet_id,$item);
            return true;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
            return false;
        }
    }
}
