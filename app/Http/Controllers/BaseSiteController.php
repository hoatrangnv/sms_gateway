<?php
/*
* @Created by: HSS
* @Author	 : nguyenduypt86@gmail.com
* @Date 	 : 08/2016
* @Version	 : 1.0
*/
namespace App\Http\Controllers;

class BaseSiteController extends Controller{

	public function __construct(){}
	public function header($catid=0){
       echo 'Header';
	}
    public function slider(){
		echo 'Slider';
    }
    public function footer(){
       echo 'Footer';
	}
	
	public function page403(){
		echo '403';
	}
	public function page404(){
		echo '404';
	}
}  