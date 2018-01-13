<?php
/**
 * QuynhTM
 *
 */
//refuseModemSend: chọn lại modem trạm cần gửi
Route::post('refuseModemSend',array('as' => 'api.refuseModemSend','uses' => 'Api\ApiRefuseSmsController@refuseModemSend'));

//gửi tin thành công
Route::post('sendSmsSuccess',array('as' => 'api.sendSmsSuccess','uses' => 'Api\ApiSendSuccessController@sendSmsSuccess'));

//cronjob
Route::get('resetModemCom',array('as' => 'api.resetModemCom','uses' => 'Api\ApiCronjobController@resetModemCom'));
Route::get('resetUserSetting',array('as' => 'api.resetUserSetting','uses' => 'Api\ApiCronjobController@resetUserSetting'));