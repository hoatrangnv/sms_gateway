<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;

class UserSetting extends BaseModel
{
    protected $table = Define::TABLE_USER_SETTING;
    protected $primaryKey = 'user_setting_id';
    public $timestamps = false;

    protected $fillable = array('user_id', 'priority', 'payment_type', 'account_balance', 'scan_auto',
        'count_sms_number', 'sms_max','sms_error_max','time_delay_from','time_delay_to','concatenation_strings'
        ,'role_type','role_name','sms_send_auto','concatenation_rule','created_date','updated_date');

    public static function getUserSettingByUserId($user_id) {
        $tbl_user_setting = Define::TABLE_USER_SETTING;
        $tbl_user = Define::TABLE_USER;
        $users = DB::table($tbl_user_setting)->where('user_id', '=', $user_id)->get();
        return isset($users[0]) ? $users[0]: array();
    }

    public static function searchByCondition($dataSearch = array(), $limit =0, $offset=0, &$total){
//        FunctionLib::debug($dataSearch);
        try{
            $query = UserSetting::where('user_setting_id','>',0);
            $total = $query->count();
            $query->orderBy('user_setting_id', 'desc');

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
        Cache::forget(Define::CACHE_LIST_MENU_PERMISSION);
        Cache::forget(Define::CACHE_ALL_PARENT_MENU);
        Cache::forget(Define::CACHE_TREE_MENU);
    }
    public static function createItem($data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new UserSetting();
            $fieldInput = $checkData->checkField($data);
            $item = new UserSetting();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->user_setting_id,$item);
            return $item->user_setting_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id,$data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new UserSetting();
            $fieldInput = $checkData->checkField($data);
            $item = UserSetting::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($item->user_setting_id,$item);
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
            $item = UserSetting::find($id);
            if($item){
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->user_setting_id,$item);
            return true;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
            return false;
        }
    }
}
