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
 * 输出页面头部
 *
 */
function APP_html_header() {
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="Content-Language" content="zh-cn" />
	<meta http-equiv="Content-Type" content="text/html" />
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<meta name="copyright" content="Single Corner" /> 
	<meta name="description" content="运维管理系统" />
	<title>运维管理系统</title>
	<link rel="stylesheet" href="./css/css.css" />
	<script src="./js/jquery-1.7.2.min.js"></script>
	<script src="./js/app.js"></script>
</head>
<body>
	<!-- 顶栏开始 -->
	<header id="APP_top">
		<!-- LOGO开始 -->
		<div id="APP_top_logo">
			<a href="/"><img src="./images/logo.png" /></a>
		</div>
		<!-- LOGO结束 -->
		<!-- 用户区开始 -->
		<div id="APP_top_user"><?php echo $_SESSION['Login_jobtitle']." ".$_SESSION["Login_name"]; ?> 正在管理本系统 <a href="../">离开后台</a></div>
		<!-- 用户区结束 -->
		<!-- 导航区开始 -->
		<nav id="APP_top_nav">
			<ul>
			<?php 
			$module = module_mgrcheck($_SESSION['Login_section']);
			foreach ($module as $key => $value) {
			?>
				<li><a href="./admin.php?a=<?php echo $value;?>"><?php echo $key;?></a></li>
			<?php 
			}
			?>
			

			</ul>
		</nav>
		<!-- 导航区结束 -->
	</header>
	<!-- 顶栏结束 -->
	<!-- 主容器开始 -->
	<div id="APP">
		<!-- 内容区开始 -->
		<div id="APP_main">
<?php
}

/**
 * 页首信息
 *
 */
function APP_mgr_main() {
	if ($_GET['a']=="") {
	} else {
		switch ($_GET['a']) {
			case "staff":
?>
			<div class="title_container">
				<span class="title_more">
					<form id="APP_queryStaff_form" action="?a=&p=query" method="post">
						<input type="text" size="10" name="name" id="APP_queryStaff_name" placeholder="姓名" />
						<input type="submit" value="查询" />
					</form>
				</span>
				<h1>
					<button onclick="load_newStaff()">新账户</button>
					<button onclick="load_verifyStaff()">账户审核</button>
				</h1>
			</div>
			<div id="APP_newStaff">
				<div class="title_container"><h1>新账号</h1></div><br />
				<form id="APP_newstaff_form" action="?a=staff&p=add" method="post">
					姓名<input type="text" name="name" size="5" id="APP_newStaff_name" />
					性别
					<select name="gender" id="APP_newStaff_gender">
						<option value="1" selected>男</option>
						<option value="0" >女</option>
					</select>
					部门
					<select size="1" name="department" id="APP_newStaff_department">
						<option value="4" >技术部</option>
					</select>
					职位
					<select name="position" id="APP_newStaff_position">
						<option value="2" selected>系统工程师</option>
						<option value="3" >PC工程师</option>
						<option value="4" >数据库工程师</option>
					</select>
					电话<input type="text" name="tel" size="11" maxlength="11" id="APP_newStaff_tel" />
					email<input type="text" name="mail" size="11" id="APP_newStaff_mail" />
					<input type="submit" value="生成账号" />
					<span id="APP_newStaff_status"></span>
				</form>
			</div>
			<div id="APP_verifyStaff"> 
			</div>
			<div id="APP_listStaff">
				<div class="title_container"><h1>在库账号</h1></div><br />
				<table class="datatable">
					<tr>
						<th width=10%>姓名</th>
						<th width=17%>部门&职位</th>
						<th width=15%>电话</th>
						<th width=25%>email</th>
						<th width=10%>状态</th>
						<th></th>
					</tr>
				<?php 
				$APP_sql = new APP_SQL();
				$App_listStaff = $APP_sql -> getStaffList();
				$APP_sql -> close();
				while ($App_listStaff_query = $APP_sql -> fetch_assoc($App_listStaff)) {
					$id = $App_listStaff_query['id'];
					$name = $App_listStaff_query['name'];
					$job = job_converter($App_listStaff_query['department'],$App_listStaff_query['position']);
					$tel = $App_listStaff_query['tel'];
					$mail = $App_listStaff_query['mail'];
					$status = status_converter($App_listStaff_query['status']);
				?>
					<tr>
						<td><?php echo $name;?></td>
						<td><?php echo $job;?></td>
						<td><?php echo $tel;?></td>
						<td><?php echo $mail;?></td>
						<td><?php echo $status;?></td>
						<td>
							<button>账号赋权</button>
							<button>冻结账号</button>
						</td>
					</tr>
				<?php
				} 
				?>
				</table>
			</div>
<?php
				break;
			case "customer":
?>
			<div class="title_container">
				<span class="title_more">
					<form id="APP_queryStaff_form" action="?a=customer&p=query" method="post">
						<input type="text" size="10" name="name" id="APP_queryCustomer_name" placeholder="客户名称" />
						<input type="submit" value="查询" />
					</form>
				</span>
				<h1>
					<button onclick="load_newCustomer()">建立新客户</button>
				</h1>
			</div>
			<div id="APP_newCustomer">
				<div class="title_container"><h1>客户资料</h1></div><br />
				<form id="APP_newcustomer_form" action="?a=customer&p=add" method="post">
					客户名称<input type="text" name="name" size="20" id="APP_newCustomer_name" />
					客户别名<input type="text" name="nickname" size="10" id="APP_newCustomer_nickname" />
					联系人<input type="text" name="contact" size="10" maxlength="11" id="APP_newCustomer_contact" />
					联系电话<input type="text" name="tel" size="11" id="APP_newCustomer_tel" />
					客户地址<input type="text" name="addr" size="11" id="APP_newCustomer_addr" />
					<input type="submit" value="添加客户" />
					<span id="APP_new_status"></span>
				</form>
			</div>
			<div id="APP_listCustomer">
				<div class="title_container"><h1>现有客户</h1></div><br />
				<table class="datatable">
					<tr>
						<th width=10%>客户编号</th>
						<th width=25%>客户名称</th>
						<th width=10%>联系人</th>
						<th width=15%>联系电话</th>
						<th width=15%>客户地址</th>
						<th></th>
					</tr>
				<?php 
				$APP_sql = new APP_SQL();
				if (isset($_GET['page']) && $_GET['page'] >= 1) {
					$curpage = floor($_GET['page']); 
				} else {
					$curpage = 1; 
				}
				if (isset($_GET['record']) && $_GET['record'] >= 1) {
					$records = $_GET['record'];
				} else {
					$records = 15;
				}
				$start = ($curpage - 1) * $records;
				$App_listCustomer = $APP_sql -> getCustomerList($start,$records);
				$App_countCustomer = $APP_sql -> getCustomerList();
				$App_result_rows = $App_countCustomer -> num_rows;
				$APP_sql -> close();
				while ($App_listCustomer_query = $App_listCustomer -> fetch_assoc()) {
					$id = $App_listCustomer_query['id'];
					$url = "?a=customer&p=query&id=$id";
					$name = $App_listCustomer_query['name'];
					$contacter = $App_listCustomer_query['contact'];
					$tel = $App_listCustomer_query['tel'];
					$address = $App_listCustomer_query['address'];
					$status = $App_listCustomer_query['status'];
					if ($status == 1) {
						$status_text = "断开合作";
					} else {
						$status_text = "重新合作";
					}
					$display = $App_listCustomer_query['display'];
					if ($display == 1) {
						$display_text = "前端隐藏";
					} else {
						$display_text = "前端显示";
					}
					if ($_SESSION['Login_section'] == 0 || $status % 2 == 1) {
?>
					<tr class="<?php echo "APP_customer_".$id; ?>">
						<input type="hidden" value="<?php echo $App_listCustomer_query['nickname'];?>" />
						<td class="customer_id"><a href="<?php echo $url;?>" target="_blank"><?php echo $id;?></a></td>
						<td class="customer_name"><?php echo $name;?></td>
						<td class="customer_contacter"><?php echo $contacter;?></td>
						<td class="customer_tel"><?php echo $tel;?></td>
						<td class="customer_address"><?php echo $address;?></td>
						<td>
							<button class="APP_customer_chdata">资料修改</button>
<?php 
						if ($_SESSION['Login_section'] == 0) {
?>
							<button onclick="chstatCustomer(<?php echo $id;?>)"><?php echo $status_text;?></button>
<?php 
						} else {
?>
							<button onclick="chDisplay(<?php echo $id;?>)"><?php echo $display_text;?></button>
<?php
						}
?>
						</td>
					</tr>
<?php
					}
				}
				if (($App_result_rows / $records) > floor($App_result_rows / $records) || $App_result_rows == 0) {
					$pages = floor($App_result_rows / $records) + 1;
				} else {
					$pages = $App_result_rows / $records;
				}
				$url_fp = "?a=customer";
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
				$url_pp = "?a=customer&record=".$records."&page=".$prepage;
				$url_np = "?a=customer&record=".$records."&page=".$nxpage;
				$url_lp = "?a=customer&record=".$records."&page=".$pages;
?>
				</table>
				<p></p>
				<center><a href="<?php echo $url_fp;?>"><<</a><a href="<?php echo $url_pp;?>"><</a><a href="<?php echo $url_np;?>">></a><a href="<?php echo $url_lp;?>">>></a></center>
				<center><?php echo $_SERVER['PHP_SCRIPT'];?></center>
			</div>
<?php
				break;
			case "device":
?>
			<div class="title_container">
				<span class="title_more">
					<form id="APP_queryDevice" action="?a=device&p=query" method="post">
						<input type="text" size="20" name="keyword" id="APP_queryDevice_keyword" placeholder="前置机号/SN/名称" />
						<input type="submit" value="查询" />
					</form>
				</span>
				<h1>
					<button  onclick="load_newDevice()">添加设备</button>
				</h1>
			</div>
			<div id="APP_newDevice">
				<div class="title_container"><h1>设备信息</h1></div><br />
				<form id="APP_newDevice_form" action="?a=device&p=add" method="post">
					类型
					<select name="type" id="APP_newDevice_type">
						<option value="1">UNIX服务器</option>
						<option value="2">PC服务器</option>
						<option value="3">PC</option>
						<option value="4">存储设备</option>
						<option value="5">网络设备</option>
						<option value="6">扩展柜</option>
						<option value="7">其他</option>
					</select>
					所属客户
					<select name="customer" id="APP_newDevice_customer">
<?php
					$APP_sql = new APP_SQL();
					$sql_customer = "SELECT * from `s_customer` WHERE `status` = '1';";
					$query = $APP_sql -> userDefine($sql_customer);
					$APP_sql -> close();
					while ($APP_result = $query -> fetch_assoc()) {
						$id = $APP_result['id'];
						$name = $APP_result['nickname'];
?>
						<option value="<?php echo $id;?>"><?php echo $name;?></option>
<?php
					}

?>
					</select>
					名称<input type="text" name="name" size="11" maxlength="11" id="APP_newDevice_name" placeholder="P7 720C" />
					序列号<input type="text" name="sn" size="11" id="APP_newDevice_sn" placeholder="06E353C" />
					OS<input type="text" name="os" size="11" id="APP_newDevice_os" placeholder="7100-06" />
					FirmWare<input type="text" name="fw" size="15" id="APP_newDevice_fw" placeholder="350_132" />
					<br />
					基本配置信息<br />
					<strong>CPU</strong><input type="text" name="cpu" size="8" id="APP_newDevice_cpu" placeholder="16(CORE)" />
					<strong>RAM</strong><input type="text" name="ram" size="4" id="APP_newDevice_ram" placeholder="4(G)" />
					<strong>DISK</strong><input type="text" name="disk" size="18" id="APP_newDevice_disk" placeholder="139/139/139/139(G)" />
					<strong>RAID</strong>
					<input type="radio" name="raid" value="1" />已做
					<input type="radio" name="raid" value="0" />未做
					<strong>HBA</strong>
					<input type="radio" name="hba" value="1" />有
					<input type="radio" name="hba" value="0" />无
					<strong title="电池安装时间">BTRY</strong><input type="text" name="bat" size="11" id="APP_newDevice_bat" placeholder="2013-11-18" />
					<input type="submit" value="添加" />
					<span id="APP_new_status"></span>
				</form>
			</div>
			<div id="APP_listDevice">
				<div class="title_container"><h1>现有设备</h1></div><br />
				<table class="datatable">
					<tr>
						<th width=15%>类型</th>
						<th width=15%>名称</th>
						<th width=10%>前置机号</th>
						<th width=10%>序列号</th>
						<th width=10%>OS</th>
						<th width=10%>微码</th>
						<th width=10%>电池</th>
						<th></th>
					</tr>
				<?php 
				$APP_sql = new APP_SQL();
				if (isset($_GET['page']) && $_GET['page'] >= 1) {
					$curpage = floor($_GET['page']); 
				} else {
					$curpage = 1; 
				}
				if (isset($_GET['record']) && $_GET['record'] >= 1) {
					$records = $_GET['record'];
				} else {
					$records = 15;
				}
				$start = ($curpage - 1) * $records;
				$App_listDevice = $APP_sql -> getDeviceList($start,$records);
				$App_countDevice = $APP_sql -> getDeviceList();
				$App_result_rows = $App_countDevice -> num_rows;
				$APP_sql -> close();
				while ($App_listDevice_query = $App_listDevice -> fetch_assoc()) {
					$id = $App_listDevice_query['id'];
					$type = $App_listDevice_query['type'];
					//$url = "?a=device&p=query&id=$id";
					$name = $App_listDevice_query['name'];
					$mid = $App_listDevice_query['mid'];
					$sn = $App_listDevice_query['sn'];
					$os = $App_listDevice_query['opsys'];
					$fw = $App_listDevice_query['firmware'];
					$base_info = $App_listDevice_query['cfgfiles'];
						$bat_parttern = "/\[BAT=(.*)\]/U";
						preg_match_all($bat_parttern,$base_info,$arr);
						$bat_date = strtotime($arr[1][0]);
						$bat_useday = floor((time() - $bat_date) / 86400);
						$bat_leftday = (971 - $bat_useday) ."天";
						if ($bat_date < 0) {
							$bat_leftday = "无电池";
						}
?>
					<tr class="<?php echo "APP_device_".$id; ?>">
						<td class="device_type"><?php echo $type;?></td>
						<td class="device_name"><?php echo $name;?></td>
						<td class="device_mid"><?php echo $mid;?></td>
						<td class="device_contacter"><?php echo $sn;?></td>
						<td class="device_tel"><?php echo $os;?></td>
						<td class="device_address"><?php echo $fw;?></td>
						<td><?php echo $bat_leftday;?></td>
						<td>
							<button onclick="show_DeviceMeta(<?php echo $id;?>)">详细信息</button>
						</td>
					</tr>
<?php
				}
				if (($App_result_rows / $records) > floor($App_result_rows / $records) || $App_result_rows == 0) {
					$pages = floor($App_result_rows / $records) + 1;
				} else {
					$pages = $App_result_rows / $records;
				}
				$url_fp = "?a=device&record=".$records;
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
				$url_pp = "?a=device&record=".$records."&page=".$prepage;
				$url_np = "?a=device&record=".$records."&page=".$nxpage;
				$url_lp = "?a=device&record=".$records."&page=".$pages;
?>
				</table>
				<p></p>
				<center><a href="<?php echo $url_fp;?>"><<</a><a href="<?php echo $url_pp;?>"><</a><a href="<?php echo $url_np;?>">></a><a href="<?php echo $url_lp;?>">>></a></center>
				<center><?php echo $_SERVER['PHP_SCRIPT'];?></center>
			</div>
<?php
				break;
			case "sparepart":
				break;
			default:
				echo "不存在的模块";
				break;
		}
	}
}
?>
