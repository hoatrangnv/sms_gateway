<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;
use App\Http\Models\User;

class RoleMenu extends BaseModel
{
    protected $table = Define::TABLE_ROLE_MENU;
    protected $primaryKey = 'role_menu_id';
    public $timestamps = false;

    protected $fillable = array('role_group_menu_id','role_group_permission', 'role_status', 'role_id', 'role_name');

    public static function getInfoByRoleId($role_id){
        $infor = RoleMenu::where('role_id', $role_id)->first();
        return $infor;
    }

    public static function createItem($data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new RoleMenu();
            $fieldInput = $checkData->checkField($data);
            $item = new RoleMenu();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->role_menu_id,$item);
            return $item->role_menu_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id,$data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new RoleMenu();
            $fieldInput = $checkData->checkField($data);
            $item = RoleMenu::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            User::updateUserPermissionWithRole($item);
            self::removeCache($item->role_menu_id,$item);
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
            $item = RoleMenu::find($id);
            if($item){
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->role_menu_id,$item);
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
            $query = RoleMenu::where('role_menu_id','>',0);
            if (isset($dataSearch['role_name']) && $dataSearch['role_name'] != '') {
                $query->where('role_name','LIKE', '%' . $dataSearch['role_name'] . '%');
            }

            $total = $query->count();
            $query->orderBy('role_menu_id', 'desc');

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
}
