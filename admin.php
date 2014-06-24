<?php
/* 加载全局配置文件 */
require_once("global.php");

/* 判断用户具有管理权限 */
if (empty($_SESSION['login']['AuthToken']) || $_SESSION['user']['policy'] != 1) {
	header("Location: /");
	exit;
}

if (!empty($_GET['module']) && !empty($_GET['action'])) {
	require("./procmgr.php");
} else {
	HTMLHeader();
	HTMLMGR();
	HTMLFooter();
}

?>