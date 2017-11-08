<?php

namespace App\Http\Models;

use Illuminate\Support\Facades\DB;
use App\library\AdminFunction\Define;


class Province extends BaseModel{
    protected $table = Define::TABLE_PROVINCE;
    protected $primaryKey = 'province_id';
    public $timestamps = false;

    protected $fillable = array('province_name', 'province_position', 'province_status', 'province_area');

    /**
     * @param $name
     * @return mixed
     */
    public static function getByName($name){
        $item = Province::where('province_name', $name)->first();
        return $item;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getById($id){
        $item = Province::find($id);
        return $item;
    }

    public static function searchByCondition($data = array(), $limit = 0, $offset = 0, &$size)
    {
        try {
            $query = Province::where('province_id', '>', 0);

            if (isset($data['province_id']) && $data['province_id'] > 0) {
                $query->where('province_id', $data['province_id']);
            }
            if (isset($data['province_name']) && $data['province_name'] != '') {
                $query->where('province_name', 'LIKE', '%' . $data['province_name'] . '%');
            }
            if (isset($data['province_status']) && $data['province_status'] != 0) {
                $query->where('province_status', $data['province_status']);
            }
            $size = $query->count();
            $data = $query->orderBy('province_id', 'desc')->take($limit)->skip($offset)->get();

            return $data;

        } catch (PDOException $e) {
            $size = 0;
            return null;
            throw new PDOException();
        }
    }

    public static function getListAllProvince() {
        $item = Province::where('province_status', '>', 0)->lists('province_name','province_id');
        return $item ? $item : array();
    }

    public static function createItem($data)
    {
        try {
            DB::connection()->getPdo()->beginTransaction();
            $item = new Province();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
            }
            $item->save();

            DB::connection()->getPdo()->commit();
            return $item->province_id;
        } catch (PDOException $e) {
            //var_dump($e->getMessage());die;
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateItem($id,$data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $item = Province::find($id);
            foreach ($data as $k => $v) {
                $item->$k = $v;
            }
            $item->update();
            DB::connection()->getPdo()->commit();
            return true;
        } catch (PDOException $e) {
            //var_dump($e->getMessage());
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function removeItem($item){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $item->delete();
            DB::connection()->getPdo()->commit();
            return true;
        } catch (PDOException $e) {
            //var_dump($e->getMessage());
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
            return false;
        }
    }

}
