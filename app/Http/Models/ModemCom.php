<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;

class ModemCom extends BaseModel
{
    protected $table = Define::TABLE_MODEM_COM;
    protected $primaryKey = 'modem_com_id';
    public $timestamps = false;

    protected $fillable = array('modem_com_name', 'user_id', 'modem_id', 'carrier_id', 'carrier_name',
        'mei_com', 'content','sms_max_com_day','success_number','error_number','is_active','created_date','updated_date');

    public static function createItem($data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new ModemCom();
            $fieldInput = $checkData->checkField($data);
            $item = new ModemCom();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->modem_com_id,$item);
            return $item->modem_com_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id,$data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new ModemCom();
            $fieldInput = $checkData->checkField($data);
            $item = ModemCom::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($item->modem_com_id,$item);
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
            $item = ModemCom::find($id);
            if($item){
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->modem_com_id,$item);
            return true;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
            return false;
        }
    }

    public static function searchByCondition($dataSearch = array(),&$total){

        $table_modem_com = Define::TABLE_MODEM_COM;
        $table_web_user = Define::TABLE_USER;
        $table_modem = Define::TABLE_MODEM;

        try{
            $query = ModemCom::query()
                ->select($table_modem_com.'.modem_com_name',$table_modem_com.'.carrier_name',$table_modem_com.'.mei_com',$table_modem_com.'.success_number',$table_modem_com.'.error_number',$table_modem_com.'.updated_date',$table_modem_com.'.content',$table_modem_com.'.is_active',$table_web_user.'.user_name',$table_modem.'.modem_name',$table_modem.'.status_content')
                ->join($table_web_user,$table_modem_com.'.user_id','=',$table_web_user.'.user_id')
                ->join($table_modem,$table_modem_com.'.modem_id','=',$table_modem.'.modem_id');
            if (isset($dataSearch['modem_com_name']) && $dataSearch['modem_com_name'] != '') {
                $query->where('modem_com_name','LIKE', '%' . $dataSearch['modem_com_name'] . '%');
            }

            $total = $query->count();
            $query->orderBy('modem_com_id', 'desc');

            //get field can lay du lieu
            $fields = (isset($dataSearch['field_get']) && trim($dataSearch['field_get']) != '') ? explode(',',trim($dataSearch['field_get'])): array();
            if(!empty($fields)){
                $result = $query->get($fields);
            }else{
                $result = $query->get();
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
        Cache::forget(Define::CACHE_LIST_MENU_PERMISSION);
        Cache::forget(Define::CACHE_ALL_PARENT_MENU);
        Cache::forget(Define::CACHE_TREE_MENU);
    }
}
