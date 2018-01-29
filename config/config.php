<?php
/**
 * Created by PhpStorm.
 * User: Quynhtm
 * Date: 6/14/14
 * Time: 3:50 PM
 */
$strWebroot = '';
if (isset($_SERVER['HTTP_HOST'])){
    $webroot=str_replace('\\','/','http://'.$_SERVER['HTTP_HOST'].(dirname($_SERVER['SCRIPT_NAME'])?dirname($_SERVER['SCRIPT_NAME']):''));
    $webroot.=$webroot[strlen($webroot)-1]!='/'?'/':'';
    $strWebroot = $webroot;
}

$dir_root = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT'].(dirname($_SERVER['SCRIPT_NAME'])?dirname($_SERVER['SCRIPT_NAME']):''));
$dir_root.=$dir_root[strlen($dir_root)-1]!='/'?'/':'';

return array(
    'TIME_NOW'=> time(),
    'DEVMODE'=> true,//false: tren server, True: local
    'REDIS_ON'=> false,
    'WEB_ROOT' => $strWebroot,
    'DIR_ROOT' => $dir_root,
	'SECURE' => false,
	'DEBUG' => false,
	'DOMAIN_COOKIE_SERVER' => '',
	'CACHE_QUERY' => false,
);