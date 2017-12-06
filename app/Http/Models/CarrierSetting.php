<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;
use App\library\AdminFunction\Memcache;
use Illuminate\Support\Facades\Cache;

class CarrierSetting extends BaseModel
{
    protected $table = Define::TABLE_CARRIER_SETTING;
    protected $primaryKey = 'carrier_setting_id';
    public $timestamps = false;

    protected $fillable = array('carrier_name', 'slipt_number', 'first_number', 'min_number', 'max_number', 'status', 'created_date','updated_date');

    public static function createItem($data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new CarrierSetting();
            $fieldInput = $checkData->checkField($data);
            $item = new CarrierSetting();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->carrier_setting_id,$item);
            return $item->carrier_setting_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id,$data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new CarrierSetting();
            $fieldInput = $checkData->checkField($data);
            $item = CarrierSetting::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($item->carrier_setting_id,$item);
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
            $item = CarrierSetting::find($id);
            if($item){
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->carrier_setting_id,$item);
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
            $query = CarrierSetting::where('carrier_setting_id','>',0);
            if (isset($dataSearch['carrier_name']) && $dataSearch['carrier_name'] != '') {
                $query->where('carrier_name','LIKE', '%' . $dataSearch['carrier_name'] . '%');
            }

            $total = $query->count();
            $query->orderBy('carrier_setting_id', 'desc');

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
        }
        Cache::forget(Define::CACHE_INFO_CARRIER);
    }

    public static function getListAll() {
        $query = CarrierSetting::where('carrier_setting_id','>',0);
        $query->where('status','=', 1);
        $list = $query->get();
        return $list;
    }

    public static function getInfoCarrier() {
        $data = Cache::get(Define::CACHE_INFO_CARRIER);
        if (sizeof($data) == 0) {
            $arr =  CarrierSetting::getListAll();
            foreach ($arr as $value){
                $data[$value->carrier_setting_id] = array(
                    'carrier_setting_id'=>$value->carrier_setting_id,
                    'carrier_name'=>$value->carrier_name,
                    'slipt_number'=>$value->slipt_number,
                    'first_number'=>$value->first_number,
                    'min_number'=>$value->min_number,
                    'max_number'=>$value->max_number,
                );
            }
            if(!empty($data)){
                Cache::put(Define::CACHE_INFO_CARRIER, $data, Define::CACHE_TIME_TO_LIVE_ONE_MONTH);
            }
        }
        return $data;
    }

    public static function getOptionCarrier() {
        $data = Cache::get(Define::CACHE_OPTION_CARRIER);
        if (sizeof($data) == 0) {
            $arr =  CarrierSetting::getListAll();
            foreach ($arr as $value){
                $data[$value->carrier_setting_id] = $value->carrier_name;
            }
            if(!empty($data)){
                Cache::put(Define::CACHE_OPTION_CARRIER, $data, Define::CACHE_TIME_TO_LIVE_ONE_MONTH);
            }
        }
        return $data;

        $data = Cache::get(Define::CACHE_INFO_CARRIER);
        if (sizeof($data) == 0) {
            $arr =  CarrierSetting::getListAll();
            foreach ($arr as $value){
                $data[$value->carrier_setting_id] = array(
                    'carrier_setting_id'=>$value->carrier_setting_id,
                    'carrier_name'=>$value->carrier_name,
                    'slipt_number'=>$value->slipt_number,
                    'first_number'=>$value->first_number,
                    'min_number'=>$value->min_number,
                    'max_number'=>$value->max_number,
                );
            }
            if(!empty($data)){
                Cache::put(Define::CACHE_INFO_CARRIER, $data, Define::CACHE_TIME_TO_LIVE_ONE_MONTH);
            }
        }
        return $data;
    }


}
