<?php

/**
 * 文件不允许访问
 *
 */
if (!defined('__ROOT__')) {
	header("Status: 404");
	exit;
}

//*******Part1 User Defined Variales***********//

/* logo*/
$APP_name = "PreRelease";		//应用系统名称

/* logo*/
$COM_name = "通路快建IT运维部";		//公司名称

/* logo*/
$LOGO = "img/logo.png";		//LOGO地址

/* Database */
//数据库配置文件---sql.class.php

/* Database Name */
$DB_NAME = "PreRelease";			//数据库名		

/* User IP Address */
if(empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$ip_addr = $_SERVER["REMOTE_ADDR"];
}else{
	$ip_addr = "";
}

/* Login timeout */
$sys_timeout = '7200';		//Logout without operating in 5 minutes


/* WebsFiles Directory */
$upload_path = "/PreReleaseDIR/upload/";
$backup_path = "/PreReleaseDIR/backup/";
$temp_path = "/PreReleaseDIR/temp/";
$md5_path = "/PreReleaseDIR/md5/";


//*******Part2  Variable -> Constant************//

/* Prevent the program has change extremely */

define("__COM_APP__",$APP_name);
define("__COM_NAME__",$COM_name);
define("__COM_LOGO__",$LOGO);
define("__DB__",$DB_NAME);
define("__IP_ADDR__",$ip_addr);
define("__SYS_TIMEOUT__",$sys_timeout);
define("__UPLOAD__",$upload_path);
define("__BACKUP__",$backup_path);
define("__TEMP__",$temp_path);
define("__MD5__",$md5_path);


//*******Part3  Other file************//

/*
 * Judge the security access 访问方式检测
 */
require_once(__ROOT__."/inc/access.judge.php");

/*
 * Operating the database 数据库操作
 */
require_once(__ROOT__."/inc/sql.class.php");

/*
 * Filter for POST 对所有提交的数据进行检测
 */
require_once(__ROOT__."/inc/filter.php");

/*
 * Convert the data 对数据显示进行转换
 */
require_once(__ROOT__."/inc/convert.php");

/*
 * Authorize for Account 账号权限分配 
 */
require_once(__ROOT__."/inc/authorize.php");

?>