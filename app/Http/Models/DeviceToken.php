<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;

class DeviceToken extends BaseModel
{
    protected $table = Define::TABLE_DEVICE_TOKEN;
    protected $primaryKey = 'device_token_id';
    public $timestamps = false;

    protected $fillable = array('user_id', 'device_code', 'token', 'messeger_center', 'status',
        'created_date', 'updated_date');

    public static function createItem($data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new DeviceToken();
            $fieldInput = $checkData->checkField($data);
            $item = new DeviceToken();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->device_token_id,$item);
            return $item->device_token_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id,$data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new DeviceToken();
            $fieldInput = $checkData->checkField($data);
            $item = DeviceToken::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($item->device_token_id,$item);
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
            $item = DeviceToken::find($id);
            if($item){
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->device_token_id,$item);
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
            $query = DeviceToken::where('device_token_id','>',0);
            if (isset($dataSearch['device_code']) && $dataSearch['device_code'] != '') {
                $query->where('device_code','LIKE', '%' . $dataSearch['device_code'] . '%');
            }

            $total = $query->count();
            $query->orderBy('device_token_id', 'desc')
            ;

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

    public static function getList() {
        $device = DeviceToken::where('status', '>', 0)->get();
        return $device ? $device : array();
    }

    public  static function getOptionDevice(){
        $data = Cache::get(Define::CACHE_OPTION_DEVICE);
        if (sizeof($data) == 0) {
            $arr =  DeviceToken::getList();
            foreach ($arr as $value){
                $data[$value->device_token_id] = $value->device_code;
            }
            if(!empty($data)){
                Cache::put(Define::CACHE_OPTION_DEVICE, $data, Define::CACHE_TIME_TO_LIVE_ONE_MONTH);
            }
        }
        return $data;
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
