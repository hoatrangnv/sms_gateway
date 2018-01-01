<?php
/**
 * QuynhTM
 *
 */
//refuseModemSend: chọn lại modem trạm cần gửi
Route::post('refuseModemSend',array('as' => 'api.refuseModemSend','uses' => 'Api\ApiRefuseSmsController@refuseModemSend'));

Route::post('sendSuccess',array('as' => 'api.sendSuccess','uses' => 'Api\ApiSendSuccessController@index'));

//cronjob
Route::get('resetModemCom',array('as' => 'api.resetModemCom','uses' => 'Api\ApiCronjobController@resetModemCom'));
Route::get('resetUserSetting',array('as' => 'api.resetUserSetting','uses' => 'Api\ApiCronjobController@resetUserSetting'));