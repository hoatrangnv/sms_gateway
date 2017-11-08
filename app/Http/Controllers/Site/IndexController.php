<?php
/*
* @Created by: HSS
* @Author	 : nguyenduypt86@gmail.com
* @Date 	 : 08/2016
* @Version	 : 1.0
*/

namespace App\Http\Controllers\Site;

use App\Http\Controllers\BaseSiteController;

class IndexController extends BaseSiteController{
	
	public function __construct(){
		parent::__construct();
	}
	public function index(){
		echo "Index";
		$this->header();
		$this->slider();
		$this->footer();
	}
    public function actionRouter($catname, $catid){
        
    }
}
