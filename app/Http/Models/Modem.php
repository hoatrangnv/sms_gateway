<?php

namespace App\Http\Models;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\library\AdminFunction\Define;

class Modem extends BaseModel
{
    protected $table = Define::TABLE_MODEM;
    protected $primaryKey = 'modem_id';
    public $timestamps = false;

    protected $fillable = array('modem_name', 'user_id', 'device_id', 'digital', 'is_active', 'created_date', 'updated_date');

    public static function createItem($data)
    {
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new Modem();
            $fieldInput = $checkData->checkField($data);
            $item = new Modem();
            if (is_array($fieldInput) && count($fieldInput) > 0) {
                foreach ($fieldInput as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            self::removeCache($item->modem_id, $item);
            return $item->modem_id;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id, $data)
    {
        try {
            DB::connection()->getPdo()->beginTransaction();
            $checkData = new Modem();
            $fieldInput = $checkData->checkField($data);
            $item = Modem::find($id);
            foreach ($fieldInput as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($item->modem_id, $item);
            return true;
        } catch (PDOException $e) {
            //var_dump($e->getMessage());
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public function checkField($dataInput)
    {
        $fields = $this->fillable;
        $dataDB = array();
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if (isset($dataInput[$field])) {
                    $dataDB[$field] = $dataInput[$field];
                }
            }
        }
        return $dataDB;
    }

    public static function deleteItem($id)
    {
        if ($id <= 0) return false;
        try {
            DB::connection()->getPdo()->beginTransaction();
            $item = Modem::find($id);
            if ($item) {
                $item->delete();
            }
            DB::connection()->getPdo()->commit();
            self::removeCache($item->modem_id, $item);
            return true;
        } catch (PDOException $e) {
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
            return false;
        }
    }

    public static function searchByCondition($dataSearch = array(), &$total)
    {
//        FunctionLib::debug($dataSearch);
        try {
            $table_user = Define::TABLE_USER;
            $table_modem = Define::TABLE_MODEM;
            $table_modem_com = Define::TABLE_MODEM_COM;
//            $query = Modem::query()
//            ,\DB::raw('SUM(success_number)'),\DB::raw('SUM(error_number)')
            $query = Modem::select(DB::raw('SUM(success_number) as sum_success,SUM(error_number) as sum_error'),$table_modem.'.modem_id',$table_user.'.user_name','error_number','success_number',$table_modem.'.modem_name',$table_modem.'.modem_type',$table_modem.'.updated_date',$table_modem.'.digital',$table_modem.'.is_active')
//                ->select(DB::raw('SUM(success_number) as sum_success,SUM(error_number) as sum_error'))
                ->join($table_modem_com, $table_modem . '.modem_id', '=', $table_modem_com . '.modem_id')
                ->join($table_user, $table_modem . '.user_id', '=', $table_user . '.user_id')
                ->get()
                ->groupBy($table_modem.'.modem_id')
//                ->orderBy($table_modem.'modem_id', 'desc');
            ;
            FunctionLib::debug($query);
//            if (isset($dataSearch['modem_name']) && $dataSearch['modem_name'] != '') {
//                $query->where('modem_name', 'LIKE', '%' . $dataSearch['modem_name'] . '%');
//            }

            $total = $query->count();


            //get field can lay du lieu
            return $query;

        } catch (PDOException $e) {
            throw new PDOException();
        }
    }
    public static function searchByCondition1($dataSearch = array(), &$total)
    {
        $data = DB::statement('SELECT m.modem_id,sum(mc.error_number),sum(mc.success_number),m.modem_name,m.modem_type,m.updated_date,m.digital,m.is_active,u.user_name FROM web_modem m INNER JOIN web_modem_com mc ON mc.modem_id = m.modem_id 
INNER JOIN web_user u ON m.user_id = u.user_id 
GROUP BY m.modem_id 
ORDER BY m.modem_id DESC');
        FunctionLib::debug($data);
    }

    public static function removeCache($id = 0, $data)
    {
        if ($id > 0) {
            //Cache::forget(Define::CACHE_CATEGORY_ID.$id);
            // Cache::forget(Define::CACHE_ALL_CHILD_CATEGORY_BY_PARENT_ID.$id);
        }
        Cache::forget(Define::CACHE_LIST_MENU_PERMISSION);
        Cache::forget(Define::CACHE_ALL_PARENT_MENU);
        Cache::forget(Define::CACHE_TREE_MENU);
    }

    public static function executesSQL($str_sql = ''){
        //return (trim($str_sql) != '') ? DB::statement(trim($str_sql)): array();
        return (trim($str_sql) != '') ? DB::select(trim($str_sql)): array();
    }

}
