<?php
/**
 * Created by JetBrains PhpStorm.
 * User: QuynhTM
 */
namespace App\Library\AdminFunction;
class Define{
    /***************************************************************************************************************
    //Database
     ***************************************************************************************************************/
    const DB_CONNECTION_MYSQL = 'mysql';
    const DB_CONNECTION_SQLSRV = 'sqlsrv';
    const DB_CONNECTION_PGSQL = 'pgsql';

    //local
    const DB_HOST = 'localhost';
    const DB_PORT = '3306';
    const DB_DATABASE = 'sms_gateways';
    const DB_USERNAME = 'root';
    const DB_PASSWORD = '';
    //server

//    const DB_HOST = '27.118.26.157';
//    const DB_PORT = '3306';
//    const DB_DATABASE = 'sms_gateways';
//    const DB_USERNAME = 'dev';
//    const DB_PASSWORD = 'dev.S@2k17';

    const DB_SOCKET = '';
    const TABLE_USER = 'web_user';
    const TABLE_GROUP_USER = 'web_group_user';
    const TABLE_GROUP_USER_PERMISSION = 'web_group_user_permission';
    const TABLE_PERMISSION = 'web_permission';
    const TABLE_MENU_SYSTEM = 'web_menu_system';
    const TABLE_MEMBER = 'web_member';
    const TABLE_PROVINCE = 'web_province';
    const TABLE_DISTRICTS = 'web_districts';
    const TABLE_WARDS = 'web_wards';
    const TABLE_SYSTEM_SETTING = 'web_system_setting';
    const TABLE_CARRIER_SETTING = 'web_carrier_setting';
    const TABLE_CUSTOMER_SETTING = 'web_customer_setting';
    const TABLE_DEVICE_TOKEN = 'web_device_token';
    const TABLE_MANAGER_SETTING = 'web_manager_setting';
    const TABLE_MODEM = 'web_modem';
    const TABLE_MODEM_COM = 'web_modem_com';
    const TABLE_SMS_CUSTOMER = 'web_sms_customer';
    const TABLE_SMS_LOG = 'web_sms_log';
    const TABLE_SMS_REPORT = 'web_sms_report';
    const TABLE_SMS_SENDTO = 'web_sms_sendTo';
    const TABLE_USER_CARRIER_SETTING = 'web_user_carrier_setting';
    const TABLE_USER_SETTING = 'web_user_setting';
    const TABLE_ROLE_MENU = 'web_role_menu';

    /***************************************************************************************************************
    //Memcache
    ***************************************************************************************************************/
    const CACHE_ON = 1 ;// 0: khong dung qua cache, 1: dung qua cache
    const CACHE_TIME_TO_LIVE_5 = 300; //Time cache 5 phut
    const CACHE_TIME_TO_LIVE_15 = 900; //Time cache 15 phut
    const CACHE_TIME_TO_LIVE_30 = 1800; //Time cache 30 phut
    const CACHE_TIME_TO_LIVE_60 = 3600; //Time cache 60 phut
    const CACHE_TIME_TO_LIVE_ONE_DAY = 86400; //Time cache 1 ngay
    const CACHE_TIME_TO_LIVE_ONE_WEEK = 604800; //Time cache 1 tuan
    const CACHE_TIME_TO_LIVE_ONE_MONTH = 2419200; //Time cache 1 thang
    const CACHE_TIME_TO_LIVE_ONE_YEAR =  29030400; //Time cache 1 nam
    //user customer
    const CACHE_DEBUG = 'cache_debug';
    const CACHE_CUSTOMER_ID = 'cache_customer_id_';
    const CACHE_ALL_PARENT_MENU = 'cache_all_parent_menu_';
    const CACHE_TREE_MENU = 'cache_tree_menu_';
    const CACHE_LIST_MENU_PERMISSION = 'cache_list_menu_permission';
    const CACHE_ALL_PARENT_CATEGORY = 'cache_all_parent_category_';
    const CACHE_USER_NAME    = 'haianhem';
    const CACHE_USER_KEY    = 'admin!@133';
    const CACHE_EMAIL_NAME    = 'manager@gmail.com';

    const CACHE_OPTION_USER = 'cache_option_user';
    const CACHE_OPTION_DEVICE = 'cache_option_device';

    /***************************************************************************************************************
    //Define
     ***************************************************************************************************************/
    const ERROR_PERMISSION = 1;

    const VIETNAM_LANGUAGE = 1;
    const ENGLISH_LANGUAGE = 2;
    static $arrLanguage = array(Define::VIETNAM_LANGUAGE => 'vi',Define::ENGLISH_LANGUAGE => 'en');

    const PAYMENT_TYPE_FIRST = 1;
    const PAYMENT_TYPE_AFTER = 2;
    static $arrPayment = array(Define::PAYMENT_TYPE_FIRST => 'Thanh toán trước',Define::PAYMENT_TYPE_AFTER => 'Thanh toán sau');

    const SCAN_AUTO_TRUE = 1;
    const SCAN_AUTO_FASLE = 0;
    static $arrScanAuto = array(Define::SCAN_AUTO_TRUE => 'Có',Define::PAYMENT_TYPE_AFTER => 'Không');

    const SEND_AUTO_TRUE = 1;
    const SEND_AUTO_FASLE = 0;
    static $arrSendAuto = array(Define::SEND_AUTO_TRUE => 'Tự động',Define::PAYMENT_TYPE_AFTER => 'Qua kiểm duyệt');

    const STATUS_SHOW = 1;
    const STATUS_HIDE = 0;
    const STATUS_BLOCK = -2;

    //SuperAdmin, Admin, Customer
    const ROLE_TYPE_SUPER_ADMIN = 1;
    const ROLE_TYPE_ADMIN = 2;
    const ROLE_TYPE_CUSTOMER = 3;
    static $arrUserRole = array(Define::ROLE_TYPE_SUPER_ADMIN => 'SuperAdmin',Define::ROLE_TYPE_ADMIN => 'Admin',Define::ROLE_TYPE_CUSTOMER => 'Customer');



}