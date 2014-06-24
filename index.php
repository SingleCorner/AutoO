<?php
/* 加载全局配置文件 */
require_once("global.php");

/* 初始登录验证 */
if (isset($_GET['a']) && $_GET['a'] == 'login'){
	if (isset($_SESSION['login']['AuthToken']) && $_SESSION['login']['AuthToken'] != ""){
		header('Location: /');
	} else {
		if (!$_POST["username"]) {
			header('Location: /');
		} else {
			/**
			 *	登录验证过程
			 *	@param $USER_username POST来的用户名
			 *	@param $USER_passwd	POST来的密码，前端加密或者未加密
			 *	@param $USER_sql 实例化SQL类用于登录
			 *	@param $USER_get_info 用于获取用户信息
			 *	@param $USER_get_passwd 从用户信息中获取密码
			 *	@param $_SESSION['user'] 存放用户信息
			 *	@param $_SESSION['login'] 存放登录信息
			 */
			$USER_username = string_filter($_POST["username"]);
			$USER_passwd = $_POST["password"];
			$USER_sql = new _SQL(1);	//连接从数据库进行登录验证
			$USER_get_info = $USER_sql -> LoginAuth($USER_username);
			if ($_POST['encrypto'] == "on"){
				$USER_get_passwd = sha1($USER_get_info['passwd'].$_SESSION['login']["timestamp"]);
			} else {
				$USER_passwd = sha1($USER_passwd);
				$USER_get_passwd = $USER_get_info['passwd'];
			}
			if ($USER_get_passwd == $USER_passwd) {
				$_SESSION['user']['account'] = $USER_get_info['account']; //账号
				$_SESSION['user']['name'] = $USER_get_info['name']; //姓名
				$_SESSION['user']['policy'] = $USER_get_info['authorize']; //管理权限
				$_SESSION['user']['module'] = $USER_get_info['module']; //项目权限
				$_SESSION['login']['AuthToken'] = sha1($_SERVER["REMOTE_ADDR"]); //TOKEN，用于判断登录
				$_SESSION['login']['timeout_check'] = time();	//记录登录时间，用于判断是否操作超时

				$_SESSION['proj']['subid'] = "";

				$USER_sql -> close();
				$result = array(
					"code" => 0,
					"message" => "Login success , but the browser do not support JavaScript.We're sorry to Pls you to PRESS F5."
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			} else {
				$result = array(
					"code" => -1,
					"message" => '验证失败：账号密码错误或者本账号已冻结'
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			}
		}
	}
} else {
	if ($_SESSION['login']['AuthToken'] != ""){
		//header("Location: /admin.php");
		if (!empty($_GET['module']) && !empty($_GET['action'])) {
			require("./procdata.php");
		} else {
			HTMLHeader();
			HTMLAssets();
			HTMLFooter();
		}
	} else {
		HTMLUserLogin();
	}
}

?>