<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;

class SmsCustomer extends BaseModel
{
    protected $table = Define::TABLE_SMS_CUSTOMER;
    protected $primaryKey = 'sms_customer_id';
    public $timestamps = false;

    protected $fillable = array('user_id', 'correct_number', 'incorrect_number', 'incorrect_number_list', 'status','status_name', 'send_date','created_date','cost','sms_deadline','user_customer_id');

    public static function createItem($data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SmsCustomer();
            $fieldInput = $checkData->checkField($data);
            $item = new SmsCustomer();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->sms_customer_id,$item);
            return $item->sms_customer_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id,$data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SmsCustomer();
            $fieldInput = $checkData->checkField($data);
            $item = SmsCustomer::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($item->sms_customer_id,$item);
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
            $item = SmsCustomer::find($id);
            if($item){
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->sms_customer_id,$item);
            return true;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
            return false;
        }
    }

    public static function searchByCondition($dataSearch = array(), $limit =0, $offset=0, &$total){
        $table_sms_customer = Define::TABLE_SMS_CUSTOMER;
        $table_user = Define::TABLE_USER;
        try{

            $query = SmsCustomer::query()
                ->select($table_sms_customer.'.*',$table_user.'.user_full_name')
                ->join($table_user,$table_sms_customer.'.user_customer_id','=',$table_user.'.user_id');

//            $query = SmsCustomer::where('sms_customer_id','>',0);
            $query->where('sms_customer_id','>', 0);

            if (isset($dataSearch['user_id']) && $dataSearch['user_id'] != '') {
                $query->where('user_customer_id','=', $dataSearch['user_id']);
            }
            if (isset($dataSearch['status']) && $dataSearch['status'] != '') {
                $query->where('status','=', $dataSearch['status']);
            }

            if (isset($dataSearch['from_day']) && $dataSearch['from_day'] != '') {
                $query->where('created_date','>=', date('Y-m-d H:i',strtotime($dataSearch['from_day'])));
            }

            if (isset($dataSearch['to_day']) && $dataSearch['to_day'] != '') {
                $query->where('created_date','<=', date('Y-m-d H:i',strtotime($dataSearch['to_day'])));
            }

            $total = $query->count();
            $query->orderBy('sms_customer_id', 'desc');

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

    public static function executesSQL($str_sql = ''){
        return (trim($str_sql) != '') ? DB::select(trim($str_sql)): array();
    }

    public static function removeCache($id = 0,$data){
        if($id > 0){
            //Cache::forget(Define::CACHE_CATEGORY_ID.$id);
        }
    }
}
