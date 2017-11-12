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

    public static function getAllParentMenu() {
        $data = Cache::get(Define::CACHE_ALL_PARENT_MENU);
        if (sizeof($data) == 0) {
            $menu = SystemSetting::where('system_setting_id', '>', 0)
                ->where('parent_id',0)
                ->where('active',Define::STATUS_SHOW)
                ->orderBy('ordering','asc')->get();
            if($menu){
                foreach($menu as $itm) {
                    $data[$itm['system_setting_id']] = $itm['menu_name'];
                }
            }
            if(!empty($data)){
                Cache::put(Define::CACHE_ALL_PARENT_MENU, $data, Define::CACHE_TIME_TO_LIVE_ONE_MONTH);
            }
        }
        return $data;
    }
    public static function buildMenuAdmin(){
        $data = $menuTree = array();
        $menuTree = Cache::get(Define::CACHE_TREE_MENU);
        if (sizeof($menuTree) == 0) {
            $search['active'] = Define::STATUS_SHOW;
            $dataSearch = SystemSetting::searchByCondition($search, 200, 0,$total);
            if(!empty($dataSearch)){
                $data = SystemSetting::getTreeMenu($dataSearch);
                $data = !empty($data)? $data :$dataSearch;
            }
            if(!empty($data)){
                foreach($data as $menu){
                    if($menu['parent_id'] == 0){
                        $menuTree[$menu['system_setting_id']] = array(
                            'name'=>$menu['menu_name'],
                            'name_en'=>$menu['menu_name_en'],
                            'show_menu'=>$menu['show_menu'],
                            'link'=>'javascript:void(0)',
                            'icon'=>$menu['menu_icons']
                        );
                    }else{
                        if(isset($menuTree[$menu['parent_id']]['arr_link_sub'])){
                            $tempLink = $menuTree[$menu['parent_id']]['arr_link_sub'];
                            array_push($tempLink,$menu['menu_url']);
                            $menuTree[$menu['parent_id']]['arr_link_sub'] = $tempLink;

                            //sub
                            $tempSub = $menuTree[$menu['parent_id']]['sub'];
                            $arrSub = array('system_setting_id'=>$menu['system_setting_id'],'show_menu'=>$menu['show_menu'],'name'=>$menu['menu_name'],'name_en'=>$menu['menu_name_en'],'RouteName'=>$menu['menu_url'],'icon'=>$menu['menu_icons'].' icon-4x','showcontent'=>$menu['showcontent'], 'permission'=>'');
                            array_push($tempSub,$arrSub);
                            $menuTree[$menu['parent_id']]['sub'] = $tempSub;
                        }else{
                            $menuTree[$menu['parent_id']]['arr_link_sub'] = array($menu['menu_url']);
                            $menuTree[$menu['parent_id']]['sub'] = array(
                                array('system_setting_id'=>$menu['system_setting_id'],'show_menu'=>$menu['show_menu'],'name'=>$menu['menu_name'],'name_en'=>$menu['menu_name_en'],'RouteName'=>$menu['menu_url'],'icon'=>$menu['menu_icons'].' icon-4x','showcontent'=>$menu['showcontent'], 'permission'=>''),);
                        }
                    }
                }
            }
            if(!empty($menuTree)){
                Cache::put(Define::CACHE_TREE_MENU, $menuTree, Define::CACHE_TIME_TO_LIVE_ONE_MONTH);
            }
        }
        return $menuTree;
    }
    public static function getTreeMenu($data){
        $max = 0;
        $aryCategoryProduct = $arrCategory = array();
        if(!empty($data)){
            foreach ($data as $k=>$value){
                $max = ($max < $value->parent_id)? $value->parent_id : $max;
                $arrCategory[$value->system_setting_id] = array(
                    'system_setting_id'=>$value->system_setting_id,
                    'parent_id'=>$value->parent_id,
                    'ordering'=>$value->ordering,
                    'menu_icons'=>$value->menu_icons,
                    'menu_url'=>$value->menu_url,
                    'showcontent'=>$value->showcontent,
                    'show_permission'=>$value->show_permission,
                    'show_menu'=>$value->show_menu,
                    'active'=>$value->active,
                    'menu_name_en'=>$value->menu_name_en,
                    'menu_name'=>$value->menu_name);
            }
        }

        if($max > 0){
            $aryCategoryProduct = self::showMenu($max, $arrCategory);
        }
        return $aryCategoryProduct;
    }
    public static function showMenu($max, $aryDataInput) {
        $aryData = array();
        if(is_array($aryDataInput) && count($aryDataInput) > 0) {
            foreach ($aryDataInput as $k => $val) {
                if((int)$val['parent_id'] == 0) {
                    $val['padding_left'] = '';
                    $val['menu_name_parent'] = '';
                    $val['menu_name_parent_en'] = '';
                    $aryData[] = $val;
                    self::showSubMenu($val['system_setting_id'],$val['menu_name'],$val['menu_name_en'], $max, $aryDataInput, $aryData);
                }
            }
        }
        return $aryData;
    }
    public static function showSubMenu($cat_id,$cat_name,$cat_name_en, $max, $aryDataInput, &$aryData) {
        if($cat_id <= $max) {
            foreach ($aryDataInput as $chk => $chval) {
                if($chval['parent_id'] == $cat_id) {
                    $chval['padding_left'] = '--- ';
                    $chval['menu_name_parent'] = $cat_name;
                    $chval['menu_name_parent_en'] = $cat_name_en;
                    $aryData[] = $chval;
                    self::showSubMenu($chval['system_setting_id'],$chval['menu_name'],$chval['menu_name_en'], $max, $aryDataInput, $aryData);
                }
            }
        }
    }

    public static function getListMenuPermission(){
        $data = (Define::CACHE_ON)? Cache::get(Define::CACHE_LIST_MENU_PERMISSION) : array();
        if (sizeof($data) == 0) {
            $result = SystemSetting::where('system_setting_id', '>', 0)
                ->where('active',Define::STATUS_SHOW)
                ->where('show_permission',Define::STATUS_SHOW)
                ->orderBy('parent_id','asc')->orderBy('ordering','asc')->get();
            if($result){
                foreach($result as $itm) {
                    $data[$itm['system_setting_id']] = $itm;
                }
            }
            if($data && Define::CACHE_ON){
                Cache::put(Define::CACHE_LIST_MENU_PERMISSION, $data, Define::CACHE_TIME_TO_LIVE_ONE_MONTH);
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
