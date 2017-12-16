<?php
/*
* @Created by: DUYNX
* @Author    : nguyenduypt86@gmail.com
* @Date      : 06/2016
* @Version   : 1.0
*/
namespace App\Http\Controllers\Admin;

use App\Library\AdminFunction\FunctionLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;

use App\Http\Models\User;
use App\Http\Models\GroupUserPermission;

use App\Library\CGlobal;

class AdminLoginController extends Controller{

    public function __construct(){

    }

    public function loginInfo($url = ''){
        if (Session::has('user')) {
            if ($url === '' || $url === 'login') {
                return Redirect::route('admin.dashboard');
            } else {
                return Redirect::to(self::buildUrlDecode($url));
            }
        } else {
            return view('admin.AdminUser.login');
        }
    }

    public function login(Request $request, $url = ''){

        $token = $request->input('_token', '');
        $username = $request->input('user_name', '');
        $password = $request->input('user_password', '');
        $error = '';
        if(Session::token() === $token) {
            if ($username != '' && $password != '') {
                if (strlen($username) < 3 || strlen($username) > 50 || preg_match('/[^A-Za-z0-9_\.@]/', $username) || strlen($password) < 5) {
                    $error = 'Không tồn tại tên đăng nhập!';
                } else {
                    $user = User::getUserByName($username);
                    //User::getInfor();
                    //FunctionLib::debug($user);
                    if ($user !== NULL) {
                        if ($user->user_status == 0) {
                            $error = 'Tài khoản bị khóa!';
                        } elseif ($user->user_status == 1) {
                            if ($user->user_password == User::encode_password($password)) {
                                $permission_code = array();
                                $group = explode(',', $user->user_group);
                                if ($group) {
                                    $permission = GroupUserPermission::getListPermissionByGroupId($group);
                                    if ($permission) {
                                        foreach ($permission as $v) {
                                            $permission_code[] = $v->permission_code;
                                        }
                                    }
                                }
                                $data = array(
                                    'user_id' => $user->user_id,
                                    'user_name' => $user->user_name,
                                    'user_full_name' => $user->user_full_name,
                                    'user_email' => $user->user_email,
                                    'user_employee_id' => $user->user_employee_id,
                                    'user_is_admin' => $user->user_is_admin,
                                    'user_group_menu' => $user->user_group_menu,
                                    'user_view' => $user->user_view,
                                    'role_type' => $user->role_type,
                                    'user_permission' => $permission_code
                                );
								$request->session()->put('user', $data, 60 * 24);
                                User::updateLogin($user);
                                if ($url === '' || $url === 'login') {
                                    //return redirect()->route('dashboard.html');
                                    //return redirect()->intended('/admin/dashboard');
                                    return Redirect::route('admin.dashboard');
                                } else {
                                    return Redirect::to(self::buildUrlDecode($url));
                                }
                            } else {
                                $error = 'Mật khẩu không đúng!';
                            }
                        }
                    } else {
                        $error = 'Không tồn tại tên đăng nhập!';
                    }
                }
            } else {
                $error = 'Chưa nhập thông tin đăng nhập!';
            }
        }
        return view('admin.AdminUser.login',['error'=>$error, 'username'=>$username]);
    }

    public function logout(Request $request){
		if($request->session()->has('user')){
            $request->session()->forget('user');
        }
        //return Redirect::route('admin.login', array('url' => self::buildUrlEncode(URL::previous())));
        return Redirect::route('admin.login');
    }

}