<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;
use App\library\AdminFunction\Memcache;
use Illuminate\Support\Facades\Cache;

class ApiApp extends BaseModel
{
    protected $table = Define::TABLE_API_APP;
    protected $primaryKey = 'app_id';
    public $timestamps = false;

    protected $fillable = array('app_name','client_id', 'client_secret', 'user_id', 'created_at', 'description','update_at');

    public static function createItem($data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new ApiApp();
            $fieldInput = $checkData->checkField($data);
            $item = new ApiApp();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->app_id,$item);
            return $item->app_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id,$data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new ApiApp();
            $fieldInput = $checkData->checkField($data);
            $item = ApiApp::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($item->app_id,$item);
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
            $item = ApiApp::find($id);
            if($item){
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->app_id,$item);
            return true;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
            return false;
        }
    }

    public static function searchByCondition($dataSearch = array(), $limit =0, $offset=0, &$total){
        try{
            $query = ApiApp::where('app_id','>',0);
            if (isset($dataSearch['app_name']) && $dataSearch['app_name'] != '') {
                $query->where('app_name','LIKE', '%' . $dataSearch['app_name'] . '%');
            }

            if (isset($dataSearch['client_id']) && $dataSearch['client_id'] != '' && isset($dataSearch['client_secret']) && $dataSearch['client_secret'] != '') {
                $query->where('client_id','=', $dataSearch['client_id']);
                $query->where('client_secret','=', $dataSearch['client_secret']);
            }

            $total = $query->count();
            $query->orderBy('app_id', 'desc');

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
    public static function getAll(){
        try{
            $query = ApiApp::where('app_id','>',0);
            $query->orderBy('app_id', 'desc');
            //get field can lay du lieu
            $result = $query->get();
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
        Cache::forget(Define::CACHE_OPTION_CARRIER);
    }

    public static function getListAll() {
        $query = ApiApp::where('app_id','>',0);
        $query->where('status','=', 1);
        $list = $query->get();
        return $list;
    }


}
