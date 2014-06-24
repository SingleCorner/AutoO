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
 * 加载 -> 登录界面
 *
 */
function HTMLUserLogin($user_login_error = "") {
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Language" content="zh-cn" />
	<meta http-equiv="Content-Type" content="text/html" />
	<meta http-equiv="X-UA-Compatible" content="IE=10" />
	<meta name="description" content="网站预发布平台" />
	<title><?php echo __COM_APP__;?>-<?php echo __COM_NAME__;?></title>
	<link rel="stylesheet" href="./css/login.css" />
	<script src="./js/jquery-1.7.2.min.js"></script>
	<script src="./js/jquery.sha1.js"></script>
	<script src="./js/login.js"></script>
</head>
<body>
<?php
	if (preg_match('/MSIE (\d+\.\d+)/i', $_SERVER['HTTP_USER_AGENT'], $msie) != 0 && $msie[1] < 10) {
?>
	<div id="MSIE_Warning">
		<strong>检测到您正在使用低于10.0版本的IE浏览器访问本页面！</strong>
		<p>由于本站前端使用HTML5 + CSS3</p>
		<p>为了给您最佳的使用体验，请<a href="http://ie.microsoft.com/" target="_blank">升级IE</a>至最新版本或使用<a href="http://chrome.google.com/" target="_blank">google浏览器</a>（推荐）。</p>
	</div>
<?php
	}
?>
	<div id="USER_login">
		<form id="USER_login_form" action="?a=login" method="post">
			<input type="hidden" id="USER_login_timestamp" value=<?php echo $_SESSION['login']["timestamp"];?> />
			<ul>
				<li>
					<label for="USER_login_user">工号</label>
					<input type="text" name="username" id="USER_login_user" maxlength="8" />
				</li>
				<li>
					<label for="USER_login_pswd">密码</label>
					<input type="password" name="password" id="USER_login_pswd" autocomplete="off" />
				</li>
				<li id="USER_login_buttons">
					<input type="submit" value="登录" id="USER_login_submit" title="登录" />
				</li>
			</ul>
		</form>
		<div id="USER_login_status"><?php echo $user_login_error; ?></div>
	</div>
	<footer>Copyright © 2014 - 2017 </footer>
</body>
</html>
<?php
}

/**
 * 加载 -> 用户页面头部
 *
 */
function HTMLHeader() {
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Language" content="zh-cn" />
	<meta http-equiv="Content-Type" content="text/html" />
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta name="description" content="网站预发布平台" />
	<title><?php echo __COM_APP__;?>-<?php echo __COM_NAME__;?></title>
	<link rel="stylesheet" href="./css/css.css" />
	<script src="./js/jquery-1.7.2.min.js"></script>
	<script src="./js/jquery.sha1.js"></script>
	<script src="./js/app.js"></script>
	<script src="/CKE/ckeditor.js"></script>
	<script src="/CKE/adapters/jquery.js"></script>
</head>
<body scroll=auto>
	<!-- 顶栏开始 -->
	<header id="APP_top">
		<!-- LOGO开始 -->
		<div id="APP_top_logo">
			<a href="/"><img src="<?php echo __COM_LOGO__?>" /></a>
		</div>
		<!-- LOGO结束 -->
		<!-- 用户区开始 -->
		<div id="APP_top_user"><?php echo $_SESSION['user']['name']." ".__COM_APP__; ?> 系统使用中 <a href="./?logout=<?php echo md5($_SESSION['login']["timestamp"]); ?>">安全退出</a></div>
		<!-- 用户区结束 -->
		<!-- 导航区开始 -->
		<nav id="APP_top_nav">
			<ul>
				<li><a href="./">首页</a></li>
				<?php
					if ($_SESSION['user']['policy'] == 1 && $_SERVER['PHP_SELF'] != "/admin.php") {
				?>

				<li><a href="./admin.php">后台管理</a></li>
				<?php
				} elseif ($_SERVER['PHP_SELF'] == "/admin.php") {
				?>

				<li><a href="?module=account">账号管理</a></li>
				<!--<li><a href="?module=proj">项目管理</a></li>
				<li><a href="?module=web">网站管理</a></li>-->
				<li><a href="?module=asset">资产管理</a></li>
				<?php
				}
				?>

			</ul>
		</nav>
		<!-- 导航区结束 -->
	</header>
	<!-- 顶栏结束 -->
	<!-- 主容器开始 -->
	<div id="APP_main">
<?php
}
/**
 * 加载 -> 资产管理界面
 *
 */
function HTMLAssets() {
			$asset_SQL = new _SQL(2);
			$arr = array();

			//分页功能
			if (isset($_GET['page']) && $_GET['page'] >= 1) {
				$curpage = floor($_GET['page']); 
			} else {
				$curpage = 1; 
			}
			if (isset($_GET['record']) && $_GET['record'] >= 1) {
				$records = floor($_GET['record']);
			} else {
				$records = 20;
			}
			$start = ($curpage - 1) * $records;
			$asset_total_num = $asset_SQL -> getAssets() -> num_rows;
			if (($asset_total_num / $records) > floor($asset_total_num / $records) || $asset_total_num == 0) {
				$pages = floor($asset_total_num / $records) + 1;
			} else {
				$pages = $asset_total_num / $records;
			}
			if ($curpage < $pages) {
				$prepage = $curpage - 1;
				$nxpage = $curpage + 1;
			} else {
				$prepage = $curpage - 1;
				$nxpage = $pages;
			}
			if ($curpage == 1) {
				$prepage = 1;
			}
			$url_fp = "?page=1";
			$url_pp = "?page=".$prepage;
			$url_np = "?page=".$nxpage;
			$url_lp = "?page=".$pages;
			//分页功能完成
			
			$asset_result = $asset_SQL -> getAssets($arr,$start);
			$TEMP_SQL = "select * from `t_project_info`;";
			$TEMP_RESULT =  $asset_SQL -> userDefine($TEMP_SQL);
			$asset_SQL -> close();

?>
			<div class="title_container">
				<span class="title_more">
					<form id="asset_query_form">
						<input type="text" placeholder="暂时未用" />
						<select id="proj_id">
							<option value="">项目名称</option>
<?php
					while ($rslt = $TEMP_RESULT -> fetch_assoc()) {
?>
							<option value="<?php echo $rslt['project_id'];?>"><?php echo $rslt['project_name'];?></option>
<?php
					}
?>
						</select>
						<input type="text" id="proj_ip" placeholder="IP地址" />
						服务器状态：
						<select id="proj_stat">
							<option value="">ALL</option>
							<option value=1 selected>使用中</option>
							<option value=0>待释放</option>
						</select>
						<input type="submit" value="查询" />
					</form>
				</span>
			</div>
			<div class="title_container"><h1>资产列表</h1></div><br />
			<table class="datatable">
				<tr>
					<td>ID</td>
					<td>project_id</td>
					<td>project_name</td>
					<td>ip_addr</td>
					<td>cpu_core</td>
					<td>ram_g</td>
					<td>disk_g</td>
					<td>app_desc</td>
					<td>status</td>
					<td>cacti</td>
					<td>nagios</td>
				</tr>
<?php
while ($arr = $asset_result -> fetch_assoc()) {
?>
				<tr>
					<td><?php echo $arr['sid']; ?></td>
					<td><?php echo $arr['project_id']; ?></td>
					<td><?php echo $arr['project_name']; ?></td>
					<td><?php echo $arr['ip_addr']; ?></td>
					<td><?php echo $arr['cpu_core']; ?></td>
					<td><?php echo $arr['ram_g']; ?></td>
					<td><?php echo $arr['disk_g']; ?></td>
					<td title="<?php echo $arr['app_desc']; ?>"><?php echo $arr['app_desc']; ?></td>
					<td><?php echo $arr['status']; ?></td>
					<td><?php echo $arr['cacti']; ?></td>
					<td><?php echo $arr['nagios']; ?></td>
				</tr>
<?php
}
?>
			</table>
			<p></p>
			<center><a href="<?php echo $url_fp;?>"><<</a><a href="<?php echo $url_pp;?>"><</a><a href="<?php echo $url_np;?>">></a><a href="<?php echo $url_lp;?>">>></a></center>
			<div> &nbsp; </div>
<?php
}




/**
 * 加载 -> 预发布操作页面
 *
 */
function HTMLOperate() {
	$Proj_sql = new _SQL(1);
	$_SESSION['proj']['mainlist']['id'] = "";
?>
		<div id="Project_main" class="project_main_list">
			<div>项目列表</div>
			<ul>
				<li><a onclick="setMainID(0)">所有项目</a></li>
			<?php
				$ProjMain_result = $Proj_sql -> getProjectMain();
				while ($result = $ProjMain_result -> fetch_assoc()) {
					$_SESSION['proj']['mainid'][] = $result['id'];
			?>
				<li><a title="<?php echo $result['alias'];?>" onclick="setMainID(<?php echo $result['id'];?>)"><?php echo $result['name']; ?></a></li>
			<?php
				}
			?>
			</ul>
		</div>
		<div id="Project_display">
			<div id="Project_sub">
				<ul>
					<li><a onclick="setSubID(0)">所有子项目</a></li>
				<?php
					if (!empty($_SESSION['proj']['sublist'])) {
						$ProjectSub_list = $_SESSION['proj']['sublist'];
					} else {
						$ProjectSub_list = "";
					}
					if ($ProjectSub_list != "") {
						foreach ($ProjectSub_list as $key => $value){
				?>
					<li><a onclick="setSubID(<?php echo $key;?>)"><?php echo $value['name'];?></a></li>
				<?php
						}
					}
				?>
				</ul>
				<hr />
			</div>
			<div id="Project_webs">
				<div id="Web_verbose"></div>
				<table class="datatable">
					<tr>
						<th width=10%>名称</th>
						<th width=15%>Address</th>
						<th width=15%>远程目录</th>
						<th width=40%></th>
						<th width=20%>当前状态</th>
					</tr>
				<?php
					if (!isset($_SESSION['proj']['mid'])) {
						if ($_SESSION['user']['module'] == -1) {
							$sid = "";
						} else {
							$ProjectSub_result = $Proj_sql -> getProjectSub($_SESSION['user']['module']);
							$_SESSION['proj']['subid'] = "";
							while ($result = $ProjectSub_result -> fetch_assoc()) {
								$id = $result['id'];
								$_SESSION['proj']['subid'][] = $id;
							}
							$sid = implode(",",$_SESSION['proj']['subid']);
						}
					} else {
						if ($_SESSION['proj']['sid'] == "") {
							$sid = implode(",",$_SESSION['proj']['subid']);
						} else {
							$sid = $_SESSION['proj']['sid'];
						}
					}
					$ProjWebs_result = $Proj_sql -> getProjectWebs($sid);
					while ($result = $ProjWebs_result -> fetch_assoc()) {
						$wid = $result['id'];
						$sid = $result['sid'];
						$name = $result['name'];
						$address = $result['address'];
						$upload = $result['upload'];
						$backup = $result['backup'];
						$release = $result['release'];
				?>
					<tr>
						<td><?php echo $name;?></td>
						<td><a href="http://<?php echo $address;?>" target="_blank"><?php echo $address;?></a></td>
						<td><?php echo $release;?></td>
						<td class="projop">
							<input type="hidden" value="<?php echo $wid;?>" />
							<button onclick="updateWeb(<?php echo $wid;?>)">更新</button>
							<button onclick="backupWeb(<?php echo $wid;?>)">备份</button>
							<select>
							<?php
								$Backup_result = $Proj_sql -> getBackup($wid);
								while ($result = $Backup_result -> fetch_assoc()){
									$id = $result['id'];
									$date = $result['datetime'];
									$backupfile = $result['filepath'];
							?>
								<option value="<?php echo $id;?>"><?php echo $date;?></option>
							<?php
								}
							?>
							</select>
							<button class="rollbackWeb red">回滚</button>
							<button class="delWeb red">删除备份</button>
						</td>
						<td class="proj_stat"></td>
					</tr>
				<?php
					}
				?>
				</table>
			</div>
		</div>
<?php
	$Proj_sql -> close();
}


/**
 * 加载 -> 后台管理操作页面
 *
 */
function HTMLMGR() {
	$mgr_sql = new _SQL(0);
	switch ($_GET['module']) {
		case "":
			header("Location: ?module=asset");
		break;
		//账号管理
		case "account":
			$Account_result = $mgr_sql -> getAccount();
?>
			<div class="title_container">
				<span class="title_more">
					<form id="query_form">
						<input type="text" id="query_name" placeholder="姓名" />
						<input type="submit" value="查询" />
					</form>
				</span>
			</div>
			<div>
				<div class="title_container"><h1>新账号</h1></div><br />
				<form id="Accnt_add">
					工号<input type="text" size="5" id="Accnt_id" />
					姓名<input type="text" size="5" id="Accnt_name" />
					密码<input type="text" size="20" id="Accnt_pass" />
					管理员<input type="radio" name="Accnt_mgr" value=1>是
						  <input type="radio" name="Accnt_mgr" value=0>否
					项目权限<input type="text" size="4" id="Accnt_proj">
					<input type="submit" value="生成账号" />
					<span id="Account_stat"></span>
				</form>
			</div>
			<div id="APP_listStaff">
				<div class="title_container"><h1>在库账号</h1></div><br />
				<table class="datatable">
					<tr>
						<th>工号</th>
						<th>姓名</th>
						<th>使用状态</th>
						<th>管理员</th>
						<th>项目权限</th>
						<th></th>
					</tr>
				<?php
					while ($result = $Account_result -> fetch_assoc()) {
						$id = $result['id'];
						$no = $result['account'];
						$name = $result['name'];
						$stat = $result['status'];
						$policy = $result['authorize'];
						$module = $result['module'];
				?>
					<tr>
						<td><?php echo $no;?></td>
						<td><?php echo $name;?></td>
						<td><?php echo $stat;?></td>
						<td><?php echo $policy;?></td>
						<td><?php echo $module;?></td>
						<td>
							<button class="red" onclick="frzAccount(<?php echo $id;?>)">账号冻结/解冻</button>
						</td>
					</tr>
				<?php
					}
				?>
				</table>
			</div>
<?php
		break;
		//项目管理
		case "proj":
			$ProjMain_result = $mgr_sql -> getProjectMain();
			$ProjSub_result = $mgr_sql -> getProjectSub("-1");
?>
			<div class="title_container">
				<span class="title_more">
					<form id="query_form">
						<input type="text" id="query_name" />
						<input type="submit" value="查询" />
					</form>
				</span>
			</div>
			<div>
				<div class="title_container"><h1>新增主项目</h1></div><br />
				<form id="addProjMain">
					项目名<input type="text" size="5" id="ProjMain_name" />
					别名<input type="text" size="5" id="ProjMain_alias" />
					<input type="submit" value="提交" />
					<span id="Account_stat"></span>
				</form>
			</div>
			<div>
				<div class="title_container"><h1>新增子项目</h1></div><br />
				<form id="addProjSub">
					项目名<input type="text" size="5" id="ProjSub_name" />
					主项目id<input type="text" size="2" id="ProjSub_mid">
					<input type="submit" value="提交" />
					<span id="Account_stat"></span>
				</form>
			</div>
			<div id="listProjMain">
				<div class="title_container"><h1>主项目列表</h1></div><br />
				<table class="datatable">
					<tr>
						<th>编号</th>
						<th>名称</th>
						<th>别名</th>
						<th>生成日期</th>
						<th></th>
					</tr>
				<?php
					while ($result = $ProjMain_result -> fetch_assoc()) {
						$id = $result['id'];
						$name = $result['name'];
						$alias = $result['alias'];
						$timestamp = $result['timestamp'];
				?>
					<tr>
						<td><?php echo $id;?></td>
						<td><?php echo $name;?></td>
						<td><?php echo $alias;?></td>
						<td><?php echo $timestamp;?></td>
						<td>
							<button>修改项目</button>
							<button class="red" onclick="delProjMain(<?php echo $id;?>)">删除主项目</button>
						</td>
					</tr>
				<?php
					}
				?>
				</table>
			</div>
			<div id="listProjSub">
				<div class="title_container"><h1>子项目列表</h1></div><br />
				<table class="datatable">
					<tr>
						<th>编号</th>
						<th>所属主项目</th>
						<th>名称</th>
						<th></th>
					</tr>
				<?php
					while ($result = $ProjSub_result -> fetch_assoc()) {
						$id = $result['id'];
						$name = $result['name'];
						$timestamp = $result['timestamp'];
				?>
					<tr>
						<td><?php echo $id;?></td>
						<td></td>
						<td><?php echo $name;?></td>
						<td>
							<button>修改项目</button>
							<button class="red" onclick="delProjSub(<?php echo $id;?>)">删除子项目</button>
						</td>
					</tr>
				<?php
					}
				?>
				</table>
			</div>
<?php
		break;
		//网站管理
		case "web":
			$ProjWebs_result = $mgr_sql -> getProjectWebs();
?>
			<div class="title_container">
				<span class="title_more">
					<form id="query_form">
						<input type="text" id="query_name" />
						<input type="submit" value="查询" />
					</form>
				</span>
			</div>
			<div>
				<div class="title_container"><h1>新网站</h1></div><br />
				<form id="addProjWeb">
					所属子项目<input type="text" size="2" id="ProjWeb_sid" />
					名称<input type="text" size="10" id="ProjWeb_name" />
					网址<input type="text" size="10" id="ProjWeb_addr" />
					上传目录<input type="text" size="5" id="ProjWeb_uld" />
					备份前缀<input type="text" size="5" id="ProjWeb_bak" />
					发布地址(Rsync)<input type="text" id="ProjWeb_rls">
					<input type="submit" value="提交" />
					<span id="Account_stat"></span>
				</form>
			</div>
			<div id="APP_listStaff">
				<div class="title_container"><h1>在库账号</h1></div><br />
				<table class="datatable">
					<tr>
						<th>所属子项目</th>
						<th>名称</th>
						<th>网址</th>
						<th>发布地址</th>
						<th></th>
					</tr>
				<?php
					while ($result = $ProjWebs_result -> fetch_assoc()) {
						$id = $result['id'];
						$sid = $result['sid'];
						$name = $result['name'];
						$addr = $result['address'];
						$rls = $result['release'];
				?>
					<tr>
						<td><?php echo $sid;?></td>
						<td><?php echo $name;?></td>
						<td><?php echo $addr;?></td>
						<td><?php echo $rls;?></td>
						<td>
							<button>修改网站</button>
							<button class="red" onclick="delProjWeb(<?php echo $id;?>)">删除网站</button>
						</td>
					</tr>
				<?php
					}
				?>
				</table>
			</div>
<?php
		break;
		//资产管理系统乱入
		case "asset":
			$asset_SQL = new _SQL(2);
			$arr = array();

			//分页功能
			if (isset($_GET['page']) && $_GET['page'] >= 1) {
				$curpage = floor($_GET['page']); 
			} else {
				$curpage = 1; 
			}
			if (isset($_GET['record']) && $_GET['record'] >= 1) {
				$records = floor($_GET['record']);
			} else {
				$records = 20;
			}
			$start = ($curpage - 1) * $records;
			$asset_total_num = $asset_SQL -> getAssets() -> num_rows;
			if (($asset_total_num / $records) > floor($asset_total_num / $records) || $asset_total_num == 0) {
				$pages = floor($asset_total_num / $records) + 1;
			} else {
				$pages = $asset_total_num / $records;
			}
			if ($curpage < $pages) {
				$prepage = $curpage - 1;
				$nxpage = $curpage + 1;
			} else {
				$prepage = $curpage - 1;
				$nxpage = $pages;
			}
			if ($curpage == 1) {
				$prepage = 1;
			}
			$url_fp = "?module=asset&page=1";
			$url_pp = "?module=asset&page=".$prepage;
			$url_np = "?module=asset&page=".$nxpage;
			$url_lp = "?module=asset&page=".$pages;
			//分页功能完成
			
			$asset_result = $asset_SQL -> getAssets($arr,$start);
			$TEMP_SQL = "select * from `t_project_info`;";
			$TEMP_RESULT =  $asset_SQL -> userDefine($TEMP_SQL);
			$TEMP_RESULT_2 = $asset_SQL -> userDefine($TEMP_SQL);
			$asset_SQL -> close();

?>
			<div class="title_container">
				<span class="title_more">
					<form id="asset_query_form">
						<input type="text" placeholder="暂时未用" />
						<select id="proj_id">
							<option value="">项目名称</option>
<?php
					while ($rslt = $TEMP_RESULT -> fetch_assoc()) {
?>
							<option value="<?php echo $rslt['project_id'];?>"><?php echo $rslt['project_name'];?></option>
<?php
					}
?>
						</select>
						<input type="text" id="proj_ip" placeholder="IP地址" />
						服务器状态：
						<select id="proj_stat">
							<option value="">ALL</option>
							<option value=1 selected>使用中</option>
							<option value=0>待释放</option>
						</select>
						<input type="submit" value="查询" />
					</form>
				</span>
				<h1>
					<button onclick="load_newAsset()">新资产</button>
				</h1>
			</div>
			<div id="asset_add">
				<form id="asset_add_form" method="post">
					<select id="asset_add_pid">
						<option value="">项目名称</option>
<?php
					while ($rslt = $TEMP_RESULT_2 -> fetch_assoc()) {
?>
						<option value="<?php echo $rslt['id'];?>"><?php echo $rslt['project_name'];?></option>
<?php
					}
?>
					</select>
					<input id="asset_add_ip" placeholder="IP地址" /><br />
					<input id="asset_add_cpu" placeholder="CPU核数" />
					<input id="asset_add_ram" placeholder="RAM(G)" />
					<input id="asset_add_disk" placeholder="DISK(G)" /><br />
					<input id="asset_add_desc" placeholder="描述" /><br />
					状态
					<label><input name="asset_add_stat" type="radio" value="1" />在用</label>
					<label><input name="asset_add_stat" type="radio" value="0" />未用</label><br />
					CACTI
					<label><input name="asset_add_mon1" type="radio" value="yes" />启用</label>
					<label><input name="asset_add_mon1" type="radio" value="no" />未用</label><br />
					NAGIOS
					<label><input name="asset_add_mon2" type="radio" value="yes" />启用</label>
					<label><input name="asset_add_mon2" type="radio" value="no" />未用</label><br />
					<input type="submit" />
				</form>
			</div>
			<div class="title_container"><h1>资产列表</h1></div><br />
			<table class="datatable">
				<tr>
					<td width=5%>ID</td>
					<td>project_id</td>
					<td>project_name</td>
					<td>ip_addr</td>
					<td>cacti</td>
					<td>nagios</td>
					<td>操作</td>
				</tr>
<?php
while ($arr = $asset_result -> fetch_assoc()) {
?>
				<tr>
					<td><?php echo $arr['sid']; ?></td>
					<td><?php echo $arr['project_id']; ?></td>
					<td><?php echo $arr['project_name']; ?></td>
					<td><?php echo $arr['ip_addr']; ?></td>
					<td><?php echo $arr['cacti']; ?></td>
					<td><?php echo $arr['nagios']; ?></td>
					<td>
						<a href="?module=asset&action=display&id=<?php echo $arr['sid'];?>"><button>修改</button></a>
						<button class="red" onclick="delAsset(<?php echo  $arr['sid'];?>)">删除</button>
					</td>
				</tr>
<?php
}
?>
			</table>
			<p></p>
			<center><a href="<?php echo $url_fp;?>"><<</a><a href="<?php echo $url_pp;?>"><</a><a href="<?php echo $url_np;?>">></a><a href="<?php echo $url_lp;?>">>></a></center>
			<div> &nbsp; </div>
<?php
		break;
		default:
			echo "不存在的模块";
		break;
	}
	$mgr_sql -> close();
}
?>