<?php

/**
 * 文件不允许访问
 *
 */
if (!defined('__ROOT__')) {
	header("Status: 404 Not Found");
	exit;
}

$Projmgr_sql = new _SQL(0);
switch ($_GET['module']) {
	case "account":
		switch ($_GET['action']) {
			case "add":
				$no = $_POST['no'];
				$name = $_POST['name'];
				$pass = sha1($_POST['passwd']);
				$module = $_POST['module'];
				$mgr = $_POST['manage'];
				$Projmgr_sql -> insAccount($no,$name,$pass,$mgr,$module);
				$affected = $Projmgr_sql -> affected();
				if ($affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "添加账号成功"
					);
				} else {
					$result = array(
						"code" => 0,
						"message" => "添加账号失败"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			case "frz":
				$id = $_POST['id'];
				$Projmgr_sql -> frzAccount($id);
				$affected = $Projmgr_sql -> affected();
				if ($affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "锁定/解锁账号成功"
					);
				} else {
					$result = array(
						"code" => 0,
						"message" => "锁定/解锁账号失败"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			default:
				exit;
			break;
		}
	break;
	case "proj":
		switch ($_GET['action']) {
			case "addMain":
				$name = $_POST['name'];
				$alias = $_POST['alias'];
				$Projmgr_sql -> insProjectMain($name,$alias);
				$affected = $Projmgr_sql -> affected();
				if ($affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "添加主项目成功"
					);
				} else {
					$result = array(
						"code" => 0,
						"message" => "添加主项目失败"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			case "addSub":
				$name = $_POST['name'];
				$mid = $_POST['mid'];
				$Projmgr_sql -> insProjectSub($name,$mid);
				$affected = $Projmgr_sql -> affected();
				if ($affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "添加子项目成功"
					);
				} else {
					$result = array(
						"code" => 0,
						"message" => "添加子项目失败"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			case "delMain":
				$id = $_POST['id'];
				$Projmgr_sql -> delProjectMain($id);
				$affected = $Projmgr_sql -> affected();
				if ($affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "删除主项目成功"
					);
				} else {
					$result = array(
						"code" => 0,
						"message" => "删除主项目失败"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			case "delSub":
				$id = $_POST['id'];
				$Projmgr_sql -> delProjectSub($id);
				$affected = $Projmgr_sql -> affected();
				if ($affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "删除子项目成功"
					);
				} else {
					$result = array(
						"code" => 0,
						"message" => "删除子项目失败"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			default:
				exit;
			break;
		}	
	break;
	case "web":
		switch ($_GET['action']) {
			case "add":
				$sid = $_POST['sid'];
				$name = $_POST['name'];
				$addr = $_POST['addr'];
				$uld = $_POST['uld'];
				$bak = $_POST['bak'];
				$rls = $_POST['rls'];
				$Projmgr_sql -> insProjectWeb($sid,$name,$addr,$uld,$bak,$rls);
				$affected = $Projmgr_sql -> affected();
				if ($affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "网站添加成功"
					);
				} else {
					$result = array(
						"code" => 0,
						"message" => "网站添加失败"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			case "del":
				$id = $_POST['id'];
				$Projmgr_sql -> delProjectWeb($id);
				$affected = $Projmgr_sql -> affected();
				if ($affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "删除网站成功"
					);
				} else {
					$result = array(
						"code" => 0,
						"message" => "删除网站失败"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			default:
				exit;
			break;
		}	
	break;
	case "asset":
		switch ($_GET['action']) {
			case "trans":
				$arr = "";
				if (!empty($_POST['pid']) && string_filter($_POST['pid'])) {
					$arr[] = "pid={$_POST['pid']}";
				}
				if (!empty($_POST['addr']) && string_filter($_POST['addr'])) {
					$arr[] = "ip={$_POST['addr']}";
				}
				if ($_POST['stat'] == "0") {
					$arr[] = "stat=0";
				}
				if (!empty($_POST['stat']) && string_filter($_POST['stat'])) {
					$arr[] = "stat={$_POST['stat']}";
				}
				$qry_data = implode("&",$arr);
				$url = "admin.php?module=asset&action=query&".$qry_data;
				$result = array(
					"code" => 1,
					"message" => $url
				);
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;

			case "del":
				$id = string_filter($_POST['id']);
				$asset_SQL = new _SQL(2);
				$asset_SQL -> delAsset($id);
				$affected = $asset_SQL -> affected();
				$asset_SQL -> close();
				if ($affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "删除成功"
					);
				} else {
					$result = array(
						"code" => 0,
						"message" => "删除失败"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			case "add":
				$sqldata['id'] = string_filter($_POST['pid']);
				$sqldata['ip_addr'] = string_filter($_POST['ip']);
				$sqldata['cpu_core'] = numeric_filter($_POST['cpu']);
				$sqldata['ram_g'] = numeric_filter($_POST['ram']);
				$sqldata['disk_g'] = numeric_filter($_POST['disk']);
				$sqldata['app_desc'] = string_filter($_POST['desc']);
				$sqldata['status'] = numeric_filter($_POST['stat']);
				$sqldata['cacti'] = string_filter($_POST['cacti']);
				$sqldata['nagios'] = string_filter($_POST['nagios']);
				$asset_SQL = new _SQL(2);
				$asset_SQL -> insAsset("t_asset_info",$sqldata);
				$affected = $asset_SQL -> affected();
				$asset_SQL -> close();
				if ($affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "添加成功"
					);
				} else {
					$result = array(
						"code" => 0,
						"message" => "添加失败"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			case "display":
				HTMLHeader();
				if (!empty($_GET['id']) && numeric_filter($_GET['id'])) {
					$asset_SQL = new _SQL(2);
					$asset_query_result = $asset_SQL -> getAssetID($_GET['id']);
					$asset_SQL -> close();
					$result = $asset_query_result;
				}
?>
			<div><?php echo "当前修改的ID为：".$result['sid'];?></div>
			<form id="asset_mod_form" method="post">
				<input id="asset_mod_id" type="hidden" value="<?php echo $result['sid']; ?>" />
				IP地址<input id="asset_mod_ip" value="<?php echo $result['ip_addr']; ?>" /><br />
				CPU核数<input id="asset_mod_cpu" value="<?php echo $result['cpu_core']; ?>" />
				内存（G）<input id="asset_mod_ram" value="<?php echo $result['ram_g']; ?>" />
				磁盘空间（G）<input id="asset_mod_disk" value="<?php echo $result['disk_g']; ?>" /><br />
				描述<input id="asset_mod_desc" value="<?php echo $result['app_desc']; ?>" /><br />
				状态
				<?php if ($result['status'] == 1) {?>
				<input name="asset_mod_stat" type="radio" value="1" checked />在用
				<input name="asset_mod_stat" type="radio" value="0" />未用<br />
				<?php } else {?>
				<input name="asset_mod_stat" type="radio" value="1" />在用
				<input name="asset_mod_stat" type="radio" value="0" checked />未用<br />
				<?php }?>
				CACTI
				<?php if ($result['cacti'] == "yes") {?>
				<input name="asset_mod_mon1" type="radio" value="yes" checked />在用
				<input name="asset_mod_mon1" type="radio" value="no" />未用<br />
				<?php } else {?>
				<input name="asset_mod_mon1" type="radio" value="yes" />在用
				<input name="asset_mod_mon1" type="radio" value="nos" checked />未用<br />
				<?php }?>
				NAGIOS
				<?php if ($result['nagios'] == "yes") {?>
				<input name="asset_mod_mon2" type="radio" value="yes" checked />在用
				<input name="asset_mod_mon2" type="radio" value="no" />未用<br />
				<?php } else {?>
				<input name="asset_mod_mon2" type="radio" value="yes" />在用
				<input name="asset_mod_mon2" type="radio" value="no" checked />未用<br />
				<?php }?>
				<input type="submit" />
			</form>
<?php
				HTMLFooter();
			break;
			case "modify":
				$id = string_filter($_POST['id']);
				$sqldata['ip_addr'] = string_filter($_POST['ip']);
				$sqldata['cpu_core'] = numeric_filter($_POST['cpu']);
				$sqldata['ram_g'] = numeric_filter($_POST['ram']);
				$sqldata['disk_g'] = numeric_filter($_POST['disk']);
				$sqldata['app_desc'] = string_filter($_POST['desc']);
				$sqldata['status'] = numeric_filter($_POST['stat']);
				$sqldata['cacti'] = string_filter($_POST['cacti']);
				$sqldata['nagios'] = string_filter($_POST['nagios']);
				if (!empty($id)){
					$asset_SQL = new _SQL(2);
					$asset_SQL -> uptAsset("t_asset_info",$sqldata,$id);
					$affected = $asset_SQL -> affected();
					$asset_SQL -> close();
				} 
				if ($affected == 1) {
					$result = array(
						"code" => 1,
						"message" => "更新成功"
					);
				} else {
					$result = array(
						"code" => 0,
						"message" => "更新失败"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			case "query":
				HTMLHeader();
				if (!empty($_GET['pid']) && string_filter($_GET['pid'])) {
					$arr['project_id'] = $_GET['pid'];
				}
				if (!empty($_GET['ip']) && string_filter($_GET['ip'])) {
					$arr['ip_addr'] = $_GET['ip'];
				}
				if (!empty($_GET['stat']) && string_filter($_GET['stat']) || $_GET['stat'] == 0) {
					$arr['status'] = $_GET['stat'];
				}
				//获取URI并进行分页地址更改	
				$uri = $_SERVER['QUERY_STRING'];
				$page_pattern = "/&page=[0-9]+/";
				preg_match_all($page_pattern,$uri,$par_value);
				$uri = str_replace($par_value[0],"",$uri);


				$asset_SQL = new _SQL(2);

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

				$asset_query_total = $asset_SQL -> getAssets($arr) -> num_rows;

				if (($asset_query_total / $records) > floor($asset_query_total / $records) || $asset_query_total == 0) {
					$pages = floor($asset_query_total / $records) + 1;
				} else {
					$pages = $asset_query_total / $records;
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
				$url_fp = "?".$uri."&page=1";
				$url_pp = "?".$uri."&page=".$prepage;
				$url_np = "?".$uri."&page=".$nxpage;
				$url_lp = "?".$uri."&page=".$pages;
				//分页功能完成

				$asset_result = $asset_SQL -> getAssets($arr,$start,$records);
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
				HTMLFooter();
			break;
		}
	break;
	default:
		exit;
	break;
}
$Projmgr_sql -> close();
?>