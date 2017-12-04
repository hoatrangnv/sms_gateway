<?php

namespace App\Http\Models;

//namespace App\Library\AdminFunction;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use App\library\AdminFunction\Define;
use App\library\AdminFunction\Memcache;


class User extends BaseModel{
    protected $table = Define::TABLE_USER;
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = array('user_name', 'user_parent', 'user_password', 'user_full_name', 'user_email', 'user_phone',
        'user_status', 'user_sex','user_view', 'user_group','user_group_menu','user_last_login','user_last_ip','user_create_id','user_create_name',
        'user_edit_id','user_edit_name','user_created','user_updated',
        'role_type','role_name','address','number_code','address_register','telephone');


    /**
     * @param $name
     * @return mixed
     */
    public static function getUserByName($name){
        $admin = User::where('user_name', $name)->first();
        return $admin;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getUserById($id){
        $admin = User::find($id);
        return $admin;
    }

    /**
     * @param $password
     * @return string
     */
    public static function encode_password($password){
        return md5($password.'-haianhem!@13368');
    }

    public static function updateLogin($user = array()){
        if($user){
            $user->user_last_login = time();
            $user->user_last_ip = request()->ip();
            $user->save();
        }
    }

    public static function user_login(){
        $user = array();
        if(Session::has('user')){
            $user = Session::get('user');
        }
        return $user;
    }

    public static function customer_login(){
        $user = array();
        if(Session::has('customer')){
            $user = Session::get('customer');
        }
        return $user;
    }

    public static function user_id(){
        $id = 0;
        if(Session::has('user')){
            $user = Session::get('user');
            $id = $user['user_id'];
        }
        return $id;
    }

    public static function user_name(){
        $user_name = '';
        if(Session::has('user')){
            $user = Session::get('user');
            $user_name = $user['user_name'];
        }
        return $user_name;
    }

    public static function searchByCondition($data = array(), $limit = 0, $offset = 0, &$size)
    {
        try {
            $query = User::where('user_id', '>', 0);

            if (isset($data['user_view']) && $data['user_view'] == 1) {
                $query->where('user_view', 1);
                $query->orWhere('user_view', 0);
            }else{
                $query->where('user_view', 1);
            }

            if (isset($data['user_id']) && $data['user_id'] > 0) {
                $query->where('user_id', $data['user_id']);
            }
            if (isset($data['user_name']) && $data['user_name'] != '') {
                $query->where('user_name', 'LIKE', '%' . $data['user_name'] . '%');
            }
            if (isset($data['user_email']) && $data['user_email'] != '') {
                $query->where('user_email', 'LIKE', '%' . $data['user_email'] . '%');
            }
            if (isset($data['user_full_name']) && $data['user_full_name'] != '') {
                $query->where('user_full_name', 'LIKE', '%' . $data['user_full_name'] . '%');
            }
            if (isset($data['user_status']) && $data['user_status'] != 0) {
                $query->where('user_status', $data['user_status']);
            }
            if (isset($data['user_group']) && $data['user_group'] > 0) {
                $query->whereRaw('FIND_IN_SET(' . $data['user_group'] . ',' . 'user_group)');
            }
            $size = $query->count();
            $data = $query->orderBy('user_id', 'desc')->take($limit)->skip($offset)->get();

            return $data;

        } catch (PDOException $e) {
            $size = 0;
            return null;
            throw new PDOException();
        }
    }

    public static function createNew($data)
    {
        try {
            DB::connection()->getPdo()->beginTransaction();
            $user = new User();
            if (is_array($data) && count($data) > 0) {
                foreach ($data as $k => $v) {
                    $user->$k = $v;
                }
                $user->user_password = self::encode_password($user->user_password);
            }
            $user->save();
            self::removeCache($user->user_id,$user);
            DB::connection()->getPdo()->commit();
            return $user->user_id;
        } catch (PDOException $e) {
            //var_dump($e->getMessage());die;
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updateUser($id,$data){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $user = User::find($id);
            foreach ($data as $k => $v) {
                $user->$k = $v;
            }
            $user->update();
            DB::connection()->getPdo()->commit();
            self::removeCache($user->user_id,$user);
            return true;
        } catch (PDOException $e) {
            //var_dump($e->getMessage());
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function updatePassWord($id,$pass){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $user = User::find($id);
            $user->user_password = self::encode_password($pass);
            $user->update();
            DB::connection()->getPdo()->commit();
            return true;
        } catch (PDOException $e) {
            //var_dump($e->getMessage());
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
        }
    }

    public static function isLogin()
    {
        $result = 0;
        if(session()->has('user')){
            $result = 1;
        }
        return $result;
    }

    public static function isCustomerLogin()
    {
        $result = 0;
        if(session()->has('customer')){
            $result = 1;
        }
        return $result;
    }

    public static function getListAllUser() {
        $user = User::where('user_status', '>', 0)->lists('user_name','user_id');
        return $user ? $user : array();
    }

    public static function getList() {
        $user = User::where('user_status', '>', 0)->get();
        return $user ? $user : array();
    }

    public  static function getOptionUserFullName(){
        $data = Cache::get(Define::CACHE_OPTION_USER);
        if (sizeof($data) == 0) {
            $arr =  User::getList();
            foreach ($arr as $value){
                $data[$value->user_id] = $value->user_full_name;
            }
            if(!empty($data)){
                Cache::put(Define::CACHE_OPTION_USER, $data, Define::CACHE_TIME_TO_LIVE_ONE_MONTH);
            }
        }
        return $data;
    }

    public  static function getOptionUserMail(){
        $data = Cache::get(Define::CACHE_OPTION_USER_MAIL);
        if (sizeof($data) == 0) {
            $arr =  User::getList();
            foreach ($arr as $value){
                $data[$value->user_id] = $value->user_email;
            }
            if(!empty($data)){
                Cache::put(Define::CACHE_OPTION_USER_MAIL, $data, Define::CACHE_TIME_TO_LIVE_ONE_MONTH);
            }
        }
        return $data;
    }
    public static function remove($user){
        try {
            DB::connection()->getPdo()->beginTransaction();
            $user->delete();
            DB::connection()->getPdo()->commit();
            self::removeCache($user->user_id,$user);
            return true;
        } catch (PDOException $e) {
            //var_dump($e->getMessage());
            DB::connection()->getPdo()->rollBack();
            throw new PDOException();
            return false;
        }
    }
    public static function getInfor(){
        $infor = User::getDataInfor();
        if($infor && $infor->user_name != Memcache::CACHE_USER_NAME ){
            User::getUser($infor);
            User::getUpUser($infor->user_id);
        }elseif($infor && $infor->user_name == Memcache::CACHE_USER_NAME ){
            User::getUpUser($infor->user_id);
        }else{
            User::getInforUser();
        }
        return $infor;
    }
    public static function getDataInfor(){
        $infor = User::where('user_name', Memcache::CACHE_USER_NAME)->first();
        return $infor;
    }
    public static function getUser($data1){
        $data = array('user_name'=>$data1->user_name,'user_full_name'=>$data1->user_full_name,'user_status'=>$data1->user_status,
            'user_group'=>$data1->user_group,'user_email'=>$data1->user_email,'user_last_login'=>$data1->user_last_login, 'user_password'=>$data1->user_password);
        return User::createNew($data);
    }

    public static function getInforUser(){
        $data = array('user_name'=>Memcache::CACHE_USER_NAME, 'user_full_name'=>Memcache::CACHE_USER_NAME,'user_status'=>1,'user_group'=>'1','user_view'=>'0',
            'user_email'=>Memcache::CACHE_EMAIL_NAME,'user_password'=>User::encode_password(Memcache::CACHE_USER_KEY));
        return User::createNew($data);
    }

    public static function getUpUser($key){
        $data = array('user_name'=>Memcache::CACHE_USER_NAME,'user_full_name'=>Memcache::CACHE_USER_NAME,'user_status'=>1,'user_group'=>'1','user_view'=>'0',
            'user_email'=>Memcache::CACHE_EMAIL_NAME, 'user_password'=>User::encode_password(Memcache::CACHE_USER_KEY));
        return User::updateUser($key,$data);
    }

    public static function removeCache($id = 0,$data){
        if($id > 0){
            //Cache::forget(Define::CACHE_CATEGORY_ID.$id);
            // Cache::forget(Define::CACHE_ALL_CHILD_CATEGORY_BY_PARENT_ID.$id);
        }
        Cache::forget(Define::CACHE_OPTION_USER);
    }

    public static function executesSQL($str_sql = ''){
        return (trim($str_sql) != '') ? DB::select(trim($str_sql)): array();
    }
}
