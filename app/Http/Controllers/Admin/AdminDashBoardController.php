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
use Illuminate\Support\Facades\Session;

class AdminDashBoardController extends BaseAdminController{
    private $error = array();
    public function __construct(){
        parent::__construct();
    }

    public function dashboard(){
        return view('admin.AdminDashBoard.index',[
            'user'=>$this->user,
            'menu'=>$this->menuSystem,
            'is_root'=>$this->is_root]);
    }
}