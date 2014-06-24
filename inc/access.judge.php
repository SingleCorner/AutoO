<?php

/**
 * 文件不允许访问
 *
 */
if (!defined('__ROOT__')) {
	header("Status: 404");
	exit;
}

/**
 * 检测登出操作
 *
 */
if (isset($_GET["logout"]) && $_GET["logout"] == md5($_SESSION['login']["timestamp"])) {
	session_destroy();
	header('Location: /');
	exit;
}


/**
 * 检测验证码状态
 *
 */
if (isset($_SESSION['login']["AuthToken"])) {
	if (sha1($_SERVER["REMOTE_ADDR"]) != $_SESSION['login']["AuthToken"]) {
		session_regenerate_id();	//遇到伪造session登陆的，先改变其sessionID，
		session_destroy();			//再将所有值清空.
		echo "<script>alert('Warning: 在线状态异常，请重启浏览器。');window.open('','_self');window.close();</script>";
		exit;
	}
}


/**
 * 检测代理状态
 *
 */
if (__IP_ADDR__ == "") {
	echo "<script>alert('Warning: 禁止代理方式访问！');window.open('','_self');window.close();</script>";
	exit;
} else {
	if (!isset($_SESSION['login']["timestamp"])) {
		$_SESSION['login']["timestamp"] = time();//对于每次会话，生成新的时间戳
	}
}


/**
 * 检测超时状态
 *
 */
if (isset($_SESSION['login']["timeout_check"])) {
	$time_now = time();
	if ($time_now - $_SESSION['login']["timeout_check"] > __SYS_TIMEOUT__) {
		session_destroy();
		header('Location: /?timeout=1');
		exit;
	} else {
		$_SESSION['login']["timeout_check"] = $time_now;
	}
}
?>