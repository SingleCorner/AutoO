<?php

/**
 * 配置文件不允许访问
 *
 */
if ($_SERVER['PHP_SELF'] == "/global.php") {
	header("Status: 404");
	exit;
}

/*
 * 网站初始化工作
 * 包括执行session初始化、定义网站根目录、加载配置文件
 *
*/
session_start();
define("__ROOT__", dirname(__FILE__));//定义网站的根目录
require_once(__ROOT__.'/inc/config.php');//加载主配置文件

require_once(__ROOT__.'/templates/user.php');
require_once(__ROOT__.'/templates/default.php');
?>