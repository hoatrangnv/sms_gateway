<?php
/**
 * Created by PhpStorm.
 * User: Quynhtm
 * Date: 29/05/2015
 * Time: 8:24 CH
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Request;
use App\Http\Controllers\BaseAdminController;
use App\Http\Models\SystemSetting;
use Illuminate\Support\Facades\Session;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\Define;

class AdminDashBoardController extends BaseAdminController{
    private $error = array();
    public function __construct(){
        parent::__construct();
    }

    public function dashboard(){
        $total = 0;
        $data = SystemSetting::searchByCondition(array("field_get"=>"system_setting_id,system_content,system_content_en"),1,0,$total);
        if (count($data)>0){
            if ($this->languageSite == Define::VIETNAM_LANGUAGE){
                $data['content'] = $data[0]['system_content'];
            }else{
                $data['content'] = $data[0]['system_content_en'];
            }
            $id = $data[0]['system_setting_id'];
        }else{
            $data = array();
            $id=0;
        }

        return view('admin.AdminDashBoard.index',[
            'user'=>$this->user,
            'menu'=>$this->menuSystem,
            'data'=>$data,
            'lang'=>$this->languageSite,
            'is_root'=>$this->is_root,
            'user_role_type'=>$this->role_type
        ]);
    }
}