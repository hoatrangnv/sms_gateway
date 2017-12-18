<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;

class SystemSetting extends BaseModel
{
    protected $table = Define::TABLE_SYSTEM_SETTING;
    protected $primaryKey = 'system_setting_id';
    public $timestamps = false;

    protected $fillable = array('time_check_connect', 'concatenation_strings', 'concatenation_rule', 'api_manager', 'api_manager_en',
        'api_customer', 'api_customer_en','system_content','system_content_en','created_date','updated_date');

    public static function createItem($data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SystemSetting();
            $fieldInput = $checkData->checkField($data);
            $item = new SystemSetting();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->system_setting_id,$item);
            return $item->system_setting_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id,$data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new SystemSetting();
            $fieldInput = $checkData->checkField($data);
            $item = SystemSetting::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($item->system_setting_id,$item);
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
            $item = SystemSetting::find($id);
            if($item){
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->system_setting_id,$item);
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
            $query = SystemSetting::where('system_setting_id','>',0);
            if (isset($dataSearch['time_check_connect']) && $dataSearch['time_check_connect'] != '') {
                $query->where('time_check_connect','LIKE', '%' . $dataSearch['time_check_connect'] . '%');
            }

            $total = $query->count();
            $query->orderBy('system_setting_id', 'desc');

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

    public static function getSystemSetting() {
        $data = DB::table(Define::TABLE_SYSTEM_SETTING)->where('system_setting_id', '>', 0)->orderBy('system_setting_id', 'desc')->get();
        return isset($data[0]) ? $data[0]: array();
    }
}
