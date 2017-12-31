<?php

//use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('sendSuccess',array('as' => 'api.sendSuccess','uses' => 'Api\ApiSendSuccessController@index'));

Route::get('resetModemCom',array('as' => 'api.resetModemCom','uses' => 'Api\ApiCronjobController@resetModemCom'));
Route::get('resetUserSetting',array('as' => 'api.resetUserSetting','uses' => 'Api\ApiCronjobController@resetUserSetting'));