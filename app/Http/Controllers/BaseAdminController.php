<?php
/**
 * Created by JetBrains PhpStorm.
 * User: QuynhTM
 */

namespace App\Http\Controllers;

use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use Illuminate\Support\Facades\Redirect;
use App\Http\Models\User;
use App\Http\Models\MenuSystem;
use View;
use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;


class BaseAdminController extends Controller{

    protected $permission = array();
    protected $user = array();
    protected $menuSystem = array();
    protected $user_group_menu = array();
    protected $is_root = false;
    protected $is_boss = false;

    public function __construct(){
		$this->middleware(function ($request, $next) {
           if (!User::isLogin()) {
				Redirect::route('admin.login')->send();
			}
            $this->user = User::user_login();
           if(!empty($this->user)){
               if(sizeof($this->user['user_permission']) > 0) {
                   $this->permission = $this->user['user_permission'];
               }
               if(trim($this->user['user_group_menu']) != ''){
                   $this->user_group_menu = explode(',',$this->user['user_group_menu']);
               }
           }
            if(in_array('is_boss',$this->permission) || $this->user['user_view'] == CGlobal::status_hide){
                $this->is_boss = true;
            }
            if(in_array('root',$this->permission)){
                $this->is_root = true;
            }
           $this->is_root = ($this->is_boss)? true: $this->is_root;
           $this->menuSystem = $this->getMenuSystem();

           //FunctionLib::debug($this->menuSystem);
           $error = isset($_GET['error'])? $_GET['error']: 0;
           $msg=array();
           if($error == Define::ERROR_PERMISSION){
               $msg[] = 'Bạn không có quyền truy cập';
               View::share('error', $msg);
           }

           View::share('menu', $this->menuSystem);
           View::share('aryPermissionMenu', $this->user_group_menu);
           View::share('is_root', $this->is_root);
           View::share('is_boss', $this->is_boss);
           View::share('user', $this->user);
           return $next($request);
        });
    }
    public function getMenuSystem(){
        $menuTree = MenuSystem::buildMenuAdmin();
        return $menuTree;
    }

    public function getControllerAction(){
        return $routerName = Route::currentRouteName();
    }
}