<?php
/**
 * Created by JetBrains PhpStorm.
 * User: QuynhTM
 */
namespace App\Library\AdminFunction;

class ArrayPermission{
    public static $arrPermit = array(
        'root' => array('name_permit'=>'Quản trị site','group_permit'=>'Quản trị site'),//admin site
        'is_boss' => array('name_permit'=>'Boss','group_permit'=>'Boss'),//tech dùng quyen cao nhat

        'user_view' => array('name_permit'=>'Xem danh sách user Admin','group_permit'=>'Tài khoản Admin'),
        'user_create' => array('name_permit'=>'Tạo user Admin','group_permit'=>'Tài khoản Admin'),
        'user_edit' => array('name_permit'=>'Sửa user Admin','group_permit'=>'Tài khoản Admin'),
        'user_change_pass' => array('name_permit'=>'Thay đổi user Admin','group_permit'=>'Tài khoản Admin'),
        'user_remove' => array('name_permit'=>'Xóa user Admin','group_permit'=>'Tài khoản Admin'),

        'group_user_view' => array('name_permit'=>'Xem nhóm quyền','group_permit'=>'Nhóm quyền'),
        'group_user_create' => array('name_permit'=>'Tạo nhóm quyền','group_permit'=>'Nhóm quyền'),
        'group_user_edit' => array('name_permit'=>'Sửa nhóm quyền','group_permit'=>'Nhóm quyền'),

        'permission_full' => array('name_permit'=>'Full tạo quyền','group_permit'=>'Tạo quyền'),
        'permission_create' => array('name_permit'=>'Tạo tạo quyền','group_permit'=>'Tạo quyền'),
        'permission_edit' => array('name_permit'=>'Sửa tạo quyền','group_permit'=>'Tạo quyền'),

        'banner_full' => array('name_permit'=>'Full quảng cáo','group_permit'=>'Quyền quảng cáo'),
        'banner_view' => array('name_permit'=>'Xem quảng cáo','group_permit'=>'Quyền quảng cáo'),
        'banner_delete' => array('name_permit'=>'Xóa quảng cáo','group_permit'=>'Quyền quảng cáo'),
        'banner_create' => array('name_permit'=>'Tạo quảng cáo','group_permit'=>'Quyền quảng cáo'),
        'banner_edit' => array('name_permit'=>'Sửa quảng cáo','group_permit'=>'Quyền quảng cáo'),

        'category_full' => array('name_permit'=>'Full danh mục','group_permit'=>'Quyền danh mục'),
        'category_view' => array('name_permit'=>'Xem danh mục','group_permit'=>'Quyền danh mục'),
        'category_delete' => array('name_permit'=>'Xóa danh mục','group_permit'=>'Quyền danh mục'),
        'category_create' => array('name_permit'=>'Tạo danh mục','group_permit'=>'Quyền danh mục'),
        'category_edit' => array('name_permit'=>'Sửa danh mục','group_permit'=>'Quyền danh mục'),

        'items_full' => array('name_permit'=>'Full tin rao','group_permit'=>'Quyền tin rao'),
        'items_view' => array('name_permit'=>'Xem tin rao','group_permit'=>'Quyền tin rao'),
        'items_delete' => array('name_permit'=>'Xóa tin rao','group_permit'=>'Quyền tin rao'),
        'items_create' => array('name_permit'=>'Tạo tin rao','group_permit'=>'Quyền tin rao'),
        'items_edit' => array('name_permit'=>'Sửa tin rao','group_permit'=>'Quyền tin rao'),

        'news_full' => array('name_permit'=>'Full tin tức','group_permit'=>'Quyền tin tức'),
        'news_view' => array('name_permit'=>'Xem tin tức','group_permit'=>'Quyền tin tức'),
        'news_delete' => array('name_permit'=>'Xóa tin tức','group_permit'=>'Quyền tin tức'),
        'news_create' => array('name_permit'=>'Tạo tin tức','group_permit'=>'Quyền tin tức'),
        'news_edit' => array('name_permit'=>'Sửa tin tức','group_permit'=>'Quyền tin tức'),

        'province_full' => array('name_permit'=>'Full tỉnh thành','group_permit'=>'Quyền tỉnh thành'),
        'province_view' => array('name_permit'=>'Xem tỉnh thành','group_permit'=>'Quyền tỉnh thành'),
        'province_delete' => array('name_permit'=>'Xóa tỉnh thành','group_permit'=>'Quyền tỉnh thành'),
        'province_create' => array('name_permit'=>'Tạo tỉnh thành','group_permit'=>'Quyền tỉnh thành'),
        'province_edit' => array('name_permit'=>'Sửa tỉnh thành','group_permit'=>'Quyền tỉnh thành'),

        'user_customer_full' => array('name_permit'=>'Full khách hàng','group_permit'=>'Quyền khách hàng'),
        'user_customer_view' => array('name_permit'=>'Xem khách hàng','group_permit'=>'Quyền khách hàng'),
        'user_customer_delete' => array('name_permit'=>'Xóa khách hàng','group_permit'=>'Quyền khách hàng'),
        'user_customer_create' => array('name_permit'=>'Tạo khách hàng','group_permit'=>'Quyền khách hàng'),
        'user_customer_edit' => array('name_permit'=>'Sửa khách hàng','group_permit'=>'Quyền khách hàng'),

        //quyền dự án SMS
        /***********************************************************************************************************************/
        /*private $permission_view = 'waittingSms_view';
    private $permission_full = 'waittingSms_full';
    private $permission_delete = 'waittingSms_delete';
    private $permission_create = 'waittingSms_create';
    private $permission_edit = 'waittingSms_edit';*/
        /***********************************************************************************************************************/

        'waittingSms_full' => array('name_permit'=>'Full waittingSms','group_permit'=>'Quyền waittingSms'),
        'waittingSms_view' => array('name_permit'=>'Xem waittingSms','group_permit'=>'Quyền waittingSms'),
        'waittingSms_delete' => array('name_permit'=>'Xóa waittingSms','group_permit'=>'Quyền waittingSms'),
        'waittingSms_create' => array('name_permit'=>'Tạo waittingSms','group_permit'=>'Quyền waittingSms'),
        'waittingSms_edit' => array('name_permit'=>'Sửa waittingSms','group_permit'=>'Quyền waittingSms'),

        'systemSetting_full' => array('name_permit'=>'Full systemSetting','group_permit'=>'Quyền systemSetting'),
        'systemSetting_view' => array('name_permit'=>'Xem systemSetting','group_permit'=>'Quyền systemSetting'),
        'systemSetting_delete' => array('name_permit'=>'Xóa systemSetting','group_permit'=>'Quyền systemSetting'),
        'systemSetting_create' => array('name_permit'=>'Tạo systemSetting','group_permit'=>'Quyền systemSetting'),
        'systemSetting_edit' => array('name_permit'=>'Sửa systemSetting','group_permit'=>'Quyền systemSetting'),

        'stationSetting_full' => array('name_permit'=>'Full stationSetting','group_permit'=>'Quyền stationSetting'),
        'stationSetting_view' => array('name_permit'=>'Xem stationSetting','group_permit'=>'Quyền stationSetting'),
        'stationSetting_delete' => array('name_permit'=>'Xóa stationSetting','group_permit'=>'Quyền stationSetting'),
        'stationSetting_create' => array('name_permit'=>'Tạo stationSetting','group_permit'=>'Quyền stationSetting'),
        'stationSetting_edit' => array('name_permit'=>'Sửa stationSetting','group_permit'=>'Quyền stationSetting'),

        'sendSmsHistory_full' => array('name_permit'=>'Full sendSmsHistory','group_permit'=>'Quyền sendSmsHistory'),
        'sendSmsHistory_view' => array('name_permit'=>'Xem sendSmsHistory','group_permit'=>'Quyền sendSmsHistory'),
        'sendSmsHistory_delete' => array('name_permit'=>'Xóa sendSmsHistory','group_permit'=>'Quyền sendSmsHistory'),
        'sendSmsHistory_create' => array('name_permit'=>'Tạo sendSmsHistory','group_permit'=>'Quyền sendSmsHistory'),
        'sendSmsHistory_edit' => array('name_permit'=>'Sửa sendSmsHistory','group_permit'=>'Quyền sendSmsHistory'),

        'sendSms_full' => array('name_permit'=>'Full sendSms','group_permit'=>'Quyền sendSms'),
        'sendSms_delete' => array('name_permit'=>'Xóa sendSms','group_permit'=>'Quyền sendSms'),
        'sendSms_create' => array('name_permit'=>'Tạo sendSms','group_permit'=>'Quyền sendSms'),
        'sendSms_edit' => array('name_permit'=>'Sửa sendSms','group_permit'=>'Quyền sendSms'),

        'stationReport_full' => array('name_permit'=>'Full reportChart','group_permit'=>'Quyền reportChart'),
        'stationReport_view' => array('name_permit'=>'Xem reportChart','group_permit'=>'Quyền reportChart'),

        'reportChart_full' => array('name_permit'=>'Full stationSetting','group_permit'=>'Quyền stationSetting'),
        'reportChart_view' => array('name_permit'=>'Xem stationSetting','group_permit'=>'Quyền stationSetting'),

        'modem_full' => array('name_permit'=>'Full Modem','group_permit'=>'Quyền Modem'),
        'modem_view' => array('name_permit'=>'Xem Modem','group_permit'=>'Quyền Modem'),
        'modem_delete' => array('name_permit'=>'Xóa Modem','group_permit'=>'Quyền Modem'),
        'modem_create' => array('name_permit'=>'Tạo Modem','group_permit'=>'Quyền Modem'),
        'modem_edit' => array('name_permit'=>'Sửa Modem','group_permit'=>'Quyền Modem'),

        'menu_full' => array('name_permit'=>'Full menu','group_permit'=>'Quyền menu'),
        'menu_view' => array('name_permit'=>'Xem menu','group_permit'=>'Quyền menu'),
        'menu_delete' => array('name_permit'=>'Xóa menu','group_permit'=>'Quyền menu'),
        'menu_create' => array('name_permit'=>'Tạo menu','group_permit'=>'Quyền menu'),
        'menu_edit' => array('name_permit'=>'Sửa menu','group_permit'=>'Quyền menu'),

        'deviceToken_full' => array('name_permit'=>'Full thiết bị','group_permit'=>'Quyền thiết bị'),
        'deviceToken_view' => array('name_permit'=>'Xem thiết bị','group_permit'=>'Quyền thiết bị'),
        'deviceToken_delete' => array('name_permit'=>'Xóa thiết bị','group_permit'=>'Quyền thiết bị'),
        'deviceToken_create' => array('name_permit'=>'Tạo thiết bị','group_permit'=>'Quyền thiết bị'),
        'deviceToken_edit' => array('name_permit'=>'Sửa thiết bị','group_permit'=>'Quyền thiết bị'),

        'carrierSetting_full' => array('name_permit'=>'Full nhà mạng','group_permit'=>'Quyền nhà mạng'),
        'carrierSetting_view' => array('name_permit'=>'Xem nhà mạng','group_permit'=>'Quyền nhà mạng'),
        'carrierSetting_delete' => array('name_permit'=>'Xóa nhà mạng','group_permit'=>'Quyền nhà mạng'),
        'carrierSetting_create' => array('name_permit'=>'Tạo nhà mạng','group_permit'=>'Quyền nhà mạng'),
        'carrierSetting_edit' => array('name_permit'=>'Sửa nhà mạng','group_permit'=>'Quyền nhà mạng'),
    );

}