<?php
/**
 * Created by JetBrains PhpStorm.
 * User: QuynhTM
 */
namespace App\Library\AdminFunction;

use App\library\AdminFunction\Define;

class CGlobal{
    static $css_ver = 1;
    static $js_ver = 1;
    public static $POS_HEAD = 1;
    public static $POS_END = 2;
    public static $extraHeaderCSS = '';
    public static $extraHeaderJS = '';
    public static $extraFooterCSS = '';
    public static $extraFooterJS = '';
    public static $extraMeta = '';
    public static $pageAdminTitle = 'Dashboard Admin';
    public static $pageShopTitle = '';

    const code_shop_share = 'Manager SMS';
    const web_name = 'Manager SMS';
    const web_keywords= 'Manager SMS';
    const web_description= 'Manager SMS';
    public static $pageTitle = 'Manager SMS';

    const phoneSupport = '';

    const num_scroll_page = 2;
    const number_limit_show = 30;
    const number_show_30 = 30;
    const number_show_40 = 40;
    const number_show_20 = 20;
    const number_show_15 = 15;
    const number_show_10 = 10;
    const number_show_5 = 5;
    const number_show_8 = 8;
    const number_show_1000 = 1000;

    const status_show = 1;
    const status_hide = 0;
    const status_block = -2;

    const concatenation_rule_first = 1;
    const concatenation_rule_center = 2;
    const concatenation_rule_end = 3;

    //is_login Customer
    const not_login = 0;
    const is_login = 1;

    const active = 1;
    const not_active = 0;

}