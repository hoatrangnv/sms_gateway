<?php
/*
* @Created by: HSS
* @Author    : nguyenduypt86@gmail.com
* @Date      : 08/2016
* @Version   : 1.0
*/

//Index
//Route::any('/',array('as' => 'admin.login','uses' => Admin.'\AdminLoginController@loginInfo'));

Route::get('/', array('as' => 'admin.login','uses' => Admin.'\AdminLoginController@loginInfo'));
Route::post('/', array('as' => 'admin.login','uses' => Admin.'\AdminLoginController@login'));