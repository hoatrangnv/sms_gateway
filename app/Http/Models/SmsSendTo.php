<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;

class SmsSendTo extends BaseModel
{
    protected $table = Define::TABLE_SMS_SENDTO;
    protected $primaryKey = 'sms_sendTo_id';
    public $timestamps = false;

    protected $fillable = array('sms_log_id', 'sms_customer_id', 'user_customer_id', 'carrier_id', 'user_manager_id',
        'modem_id', 'com_id','phone_receive','phone_send','status','status_name',
        'content','hour','day','month','year','send_date','send_date_at','created_date','cost','content_grafted');

    public static function insertMultiple($dataInput){
        $str_sql = FunctionLib::buildSqlInsertMultiple(Define::TABLE_SMS_SENDTO, $dataInput);
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
            $checkData = new SmsSendTo();
            $fieldInput = $checkData->checkField($data);
            $item = new SmsSendTo();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->sms_sendTo_id,$item);
            return $item->sms_sendTo_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id,$data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SmsSendTo();
            $fieldInput = $checkData->checkField($data);
            $item = SmsSendTo::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($item->sms_sendTo_id,$item);
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
            $item = SmsSendTo::find($id);
            if($item){
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->sms_sendTo_id,$item);
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
            $query = SmsSendTo::where('sms_sendTo_id','>',0);
            if (isset($dataSearch['time_check_connect']) && $dataSearch['time_check_connect'] != '') {
                $query->where('time_check_connect','LIKE', '%' . $dataSearch['time_check_connect'] . '%');
            }

            $total = $query->count();
            $query->orderBy('sms_sendTo_id', 'desc');

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

    public static function joinByCondition($dataSearch = array(), $limit =0, $offset=0, &$total){
//        FunctionLib::debug($dataSearch);
        try{
            $web_sms_sendTo = Define::TABLE_SMS_SENDTO;
            $web_carrier_setting = Define::TABLE_CARRIER_SETTING;
            $web_sms_customer = Define::TABLE_SMS_CUSTOMER;

            $query = SmsSendTo::query()
                ->select($web_carrier_setting.'.carrier_name',$web_sms_sendTo.'.sms_sendTo_id',$web_sms_sendTo.'.send_date_at',$web_sms_sendTo.'.phone_receive',$web_sms_sendTo.'.content',$web_sms_sendTo.'.cost',$web_sms_sendTo.'.status',$web_sms_customer.'.incorrect_number_list',$web_sms_customer.'.sms_customer_id')
                ->leftJoin($web_carrier_setting,$web_sms_sendTo.'.carrier_id','=',$web_carrier_setting.'.carrier_setting_id')
                ->leftJoin($web_sms_customer,$web_sms_sendTo.'.sms_customer_id','=',$web_sms_customer.'.sms_customer_id')
            ;

            $query ->where($web_sms_sendTo.'.sms_customer_id','=',$dataSearch['id_cs']);
            if (isset($dataSearch['carrier_id']) && $dataSearch['carrier_id'] != '') {
                $query->where('carrier_id','=',$dataSearch['carrier_id']);
            }
            if (isset($dataSearch['status']) && $dataSearch['status'] != '') {
                $query->where($web_sms_sendTo.'.status','=',$dataSearch['status']);
            }

            $total = $query->count();
            $query->orderBy('sms_sendTo_id', 'desc');

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

    /**
     * QuynhTM
     * @param int $sms_log_id
     */
    public static function getListSmsSendToBySmsLogId($sms_log_id =0){
        if($sms_log_id >0){
            $query = SmsSendTo::where('sms_sendTo_id','>',0);
            $query->where('sms_log_id','=', $sms_log_id);
            $result = $query->orderBy('sms_sendTo_id', 'desc')->get(array('sms_sendTo_id','sms_log_id','carrier_id','phone_receive','content_grafted'));
            return $result;
        }
        return array();
    }
    public static function removeCache($id = 0,$data){
        if($id > 0){
            //Cache::forget(Define::CACHE_CATEGORY_ID.$id);
           // Cache::forget(Define::CACHE_ALL_CHILD_CATEGORY_BY_PARENT_ID.$id);
        }
    }
}
