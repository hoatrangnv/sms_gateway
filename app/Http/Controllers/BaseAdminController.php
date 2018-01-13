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
use App\Http\Models\SmsLog;
use View;
use App\Library\AdminFunction\FunctionLib;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;


class BaseAdminController extends Controller
{

    protected $permission = array();
    protected $user = array();
    protected $menuSystem = array();
    protected $user_group_menu = array();
    protected $is_root = false;
    protected $is_boss = false;
    protected $user_id = 0;
    protected $role_type = Define::ROLE_TYPE_CUSTOMER;
    protected $languageSite = Define::VIETNAM_LANGUAGE;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!User::isLogin()) {
                Redirect::route('admin.login')->send();
            }
            $this->user = User::user_login();
            if (!empty($this->user)) {
                if (sizeof($this->user['user_permission']) > 0) {
                    $this->permission = $this->user['user_permission'];
                }
                if (trim($this->user['user_group_menu']) != '') {
                    $this->user_group_menu = explode(',', $this->user['user_group_menu']);
                }
                if (isset($this->user['role_type']) && trim($this->user['role_type'])) {
                    $this->role_type = $this->user['role_type'];
                }
                if (isset($this->user['user_id']) && trim($this->user['user_id'])) {
                    $this->user_id = $this->user['user_id'];
                }
            }
            if (in_array('is_boss', $this->permission) || $this->user['user_view'] == CGlobal::status_hide) {
                $this->is_boss = true;
            }
            if (in_array('root', $this->permission)) {
                $this->is_root = true;
            }
            $this->is_root = ($this->is_boss) ? true : $this->is_root;

            $arrMenu = array();
            if ($this->is_boss) {
                $this->menuSystem = $this->getMenuSystem();
            } else {
                $arrMenu = $this->getMenuSystem();
            }
            if (!empty($arrMenu)) {
                foreach ($arrMenu as $menu_id => $menu) {
                    if ($menu['show_menu'] == CGlobal::status_show) {
                        if (!empty($menu['sub'])) {
                            $checkMenu = false;
                            foreach ($menu['sub'] as $ks => $sub) {
                                if (!empty($this->user_group_menu) && in_array($sub['menu_id'], $this->user_group_menu)) {
                                    $checkMenu = true;
                                }
                            }
                            if ($checkMenu) {
                                $this->menuSystem[$menu_id] = $menu;
                            }
                        }
                    }
                }
            }

            //FunctionLib::debug($this->user);
            $error = isset($_GET['error']) ? $_GET['error'] : 0;
            $msg = array();
            if ($error == Define::ERROR_PERMISSION) {
                $msg[] = 'Bạn không có quyền truy cập';
                View::share('error', $msg);
            }
            //get lang
            if (isset($_GET['lang']) && (int)$_GET['lang'] > 0) {
                $get_lang = $_GET['lang'];
                $lang = (isset(Define::$arrLanguage[$get_lang])) ? $get_lang : $this->languageSite;
                $request->session()->put('languageSite', $lang, Define::CACHE_TIME_TO_LIVE_ONE_MONTH);
            }
            $this->languageSite = (Session::has('languageSite')) ? Session::get('languageSite') : $this->languageSite;

            //get notify
            $number_notify = $this->getNotifyUser($this->user_id,$this->role_type);
            //FunctionLib::debug($number_notify);
            View::share('languageSite', $this->languageSite);
            View::share('menu', $this->menuSystem);
            View::share('aryPermissionMenu', $this->user_group_menu);
            View::share('is_root', $this->is_root);
            View::share('is_boss', $this->is_boss);
            View::share('role_type', $this->role_type);
            View::share('user_id', $this->user_id);
            View::share('user', $this->user);
            View::share('number_notify', $number_notify);
            return $next($request);
        });
    }

    public function getNotifyUser($user_id,$role_type)
    {
        $numberNotify = SmsLog::getNumberNotifyUser($user_id,$role_type);
        return $numberNotify;
    }

    public function getMenuSystem()
    {
        $menuTree = MenuSystem::buildMenuAdmin();
        return $menuTree;
    }

    public function getControllerAction()
    {
        return $routerName = Route::currentRouteName();
    }
}