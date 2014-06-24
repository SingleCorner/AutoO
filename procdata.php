<?php

/**
 * 文件不允许访问
 *
 */
if (!defined('__ROOT__')) {
	header("Status: 404 Not Found");
	exit;
}

$Project_sql = new _SQL(1); //打开数据库连接
switch ($_GET['module']) {
	//模块 -- 设置参数
	case "set":
		switch ($_GET['action']) {
			//设置主项目id
			case "mid":
				$mid = numeric_filter($_POST['mid']);
				$_SESSION['proj']['sid'] = "";
				if ($mid == 0) {
					$_SESSION['proj']['mid'] = "";
					$_SESSION['proj']['sublist'] = "";
					$result = array("code" => 1);
				} else {
					$idcheck = in_array($mid,$_SESSION['proj']['mainid']);
					if ($idcheck == 1) {
						$_SESSION['proj']['mid'] = $mid;
						$_SESSION['proj']['sublist'] = "";
						$_SESSION['proj']['subid'] = "";
						$ProjectSub_result = $Project_sql -> getProjectSub($mid);
						while ($result = $ProjectSub_result -> fetch_assoc()) {
							$id = $result['id'];
							$_SESSION['proj']['subid'][] = $id;
							$name = $result['name'];
							$_SESSION['proj']['sublist'][$id]['name'] = $name;
						}
					$result = array("code" => 1);
					} else {
						$result = array(
							"code" => 0,
							"message" => "疑似非法操作,若有问题，联系D970"
						);
					}
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;

			//设置子项目id
			case "sid":
				$sid = numeric_filter($_POST['sid']);
				if ($sid == 0) {
					$_SESSION['proj']['sid'] = "";
					$result = array("code" => 1);
				} else {
					$idcheck = in_array($sid,$_SESSION['proj']['subid']);
					if ($idcheck == 1) {
						$_SESSION['proj']['sid'] = $sid;
						$result = array("code" => 1);
					} else {
						$result = array(
							"code" => 0,
							"message" => "疑似非法操作,若有问题，联系D970"
						);
					}
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

	//模块 -- 网站操作
	case "web":
		switch ($_GET['action']) {
			case "update":
				if ($wid = numeric_filter($_POST['wid'])) {
					$ProjectWeb_Result = $Project_sql -> getWeb($wid);
					$sid = $ProjectWeb_Result['sid'];
					if (in_array($sid,$_SESSION['proj']['subid']) || ($sid != "" && $_SESSION['user']['module'] == -1)) {
						$name = $ProjectWeb_Result['name'];
						$upload_path = __UPLOAD__.$ProjectWeb_Result['upload']."/";
						$update_path = $ProjectWeb_Result['release'];
						$md5_file = __MD5__.$ProjectWeb_Result['upload'].date("_Ymd_His").".md5";
						$md5_command = "find {$upload_path} -type f -print0 | xargs -0 md5sum > {$md5_file}";
						exec($md5_command);
						$command = "rsync -av --progress --exclude=CKE --delete {$upload_path} {$update_path} --password-file=/PreReleaseDIR/nobody.pass";
						exec($command,$exec_result);
						$rsync_info = rsync_replace($exec_result);
						$result = array(
							"code" => 1,
							"message" => "{$rsync_info}"
						);
					} else {
						$result = array(
							"code" => 0,
							"message" => "疑似非法操作,若有问题，联系D970"
						);
					}
				} else {
					$result = array(
						"code" => 0,
						"message" => "非法数据！操作已中断"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			case "rollback":
				$wid = numeric_filter($_POST['wid']);
				$id = numeric_filter($_POST['id']);
				if ($wid != "" && $id != "") {
					$ProjectWeb_Result = $Project_sql -> getWeb($wid);
					$sid = $ProjectWeb_Result['sid'];
					$update_path = $ProjectWeb_Result['release'];
					$dirname = $ProjectWeb_Result['upload'];
					if (in_array($sid,$_SESSION['proj']['subid']) || ($sid != "" && $_SESSION['user']['module'] == -1)) {
						$Backup_set = $Project_sql -> getBackup($wid,$id);
						$Backup_result = $Backup_set -> fetch_assoc();
						$Backup_id = $Backup_result['id'];
						$Backup_file = $Backup_result['filepath'];
						if ($Backup_id != "") {
							$backup_path = __BACKUP__.$Backup_file;
							$temp_path = __TEMP__.$dirname."_baking/";
							$temp_dir = $temp_path.$dirname."/";
							$command = "rm -rf {$temp_path};mkdir {$temp_path};tar xf {$backup_path} -C {$temp_path};";
							$command .= "rsync -rv --delete {$temp_dir} {$update_path} --password-file=/PreReleaseDIR/nobody.pass;";
							$command .= "rm -rf {$temp_path};";
							exec($command,$exec_result);
							$rsync_info = rsync_replace($exec_result);
						}
						if ($exec_result[0] != "") {
							$result = array(
								"code" => 1,
								"message" => "{$Backup_file}回滚成功",
								"alertmsg" => $rsync_info
							);
						} else {
							$result = array(
								"code" => 0,
								"message" => "回滚失败"
							);
						}
					} else {
						$result = array(
							"code" => 0,
							"message" => "疑似非法操作,若有问题，联系D970"
						);
					}
				} else {
					$result = array(
						"code" => 0,
						"message" => "非法数据！操作已中断"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			case "backup":
				if ($wid = numeric_filter($_POST['wid'])) {
					$ProjectWeb_Result = $Project_sql -> getWeb($wid);
					$sid = $ProjectWeb_Result['sid'];
					if (in_array($sid,$_SESSION['proj']['subid']) || ($sid != "" && $_SESSION['user']['module'] == -1)) {
						$name = $ProjectWeb_Result['backup'];
						$upload_name = $ProjectWeb_Result['upload'];
						$temp_path = __TEMP__.$upload_name."/";
						$md5_file = __MD5__.$ProjectWeb_Result['upload'].date("_Ymd_His")."_bak.md5";
						$rmt_addr = $ProjectWeb_Result['release'];
						$bakfile = $name."_".date("YmdHis").".tar";
						$bak_path = __BACKUP__.$bakfile;
						$backup_cmd = "rsync -a --delete {$rmt_addr} {$temp_path} --password-file=/PreReleaseDIR/nobody.pass;";
						exec($backup_cmd);
						$md5_cmd = "find {$temp_path} -type f -print0 | xargs -0 md5sum > {$md5_file}";
						exec($md5_cmd);
						$command = "cd ".__TEMP__.";tar cvzf {$bak_path} {$upload_name}/;rm -rf {$temp_path};";
						exec($command,$exec_result);
						if ($exec_result[0] !== "") {
							$Project_sql -> insBackup($wid,$bakfile);
							$affected = $Project_sql -> affected();
							if ($affected == 1) {
								$exec_result = "备份完成";
							} else {
								$exec_result = "备份完成，未影响数据库";
							}
						} else {
							$exec_result = "备份异常,联系D970";
						}
						$result = array(
							"code" => 1,
							"message" => "{$exec_result}"
						);
					} else {
						$result = array(
							"code" => 0,
							"message" => "疑似非法操作,若有问题，联系D970"
						);
					}
				} else {
					$result = array(
						"code" => 0,
						"message" => "非法数据！操作已中断"
					);
				}
				header('Content-Type: application/json');
				echo json_encode($result);
				exit;
			break;
			case "delbak":
				$wid = numeric_filter($_POST['wid']);
				$id = numeric_filter($_POST['id']);
				if ($wid != "" && $id != "") {
					$ProjectWeb_Result = $Project_sql -> getWeb($wid);
					$sid = $ProjectWeb_Result['sid'];
					if (in_array($sid,$_SESSION['proj']['subid']) || ($sid != "" && $_SESSION['user']['module'] == -1)) {
						$Backup_set = $Project_sql -> getBackup($wid,$id);
						$Backup_result = $Backup_set -> fetch_assoc();
						$Backup_file = $Backup_result['filepath'];
						$Project_sql -> delBackup($id,$wid);
						$affected = $Project_sql -> affected();
						if ($affected == 1) {
							$backup_path = __BACKUP__.$Backup_file;
							$command .= "rm -rvf {$backup_path};";
							exec($command,$exec_result);
						}
						if ($exec_result[0] != "") {
							$result = array(
								"code" => 1,
								"message" => "备份删除成功"
							);
						} else {
							$result = array(
								"code" => 0,
								"message" => "删除失败"
							);
						}
					} else {
						$result = array(
							"code" => 0,
							"message" => "疑似非法操作,若有问题，联系D970"
						);
					}
				} else {
					$result = array(
						"code" => 0,
						"message" => "非法数据！操作已中断"
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
				$url = "?module=asset&action=query&".$qry_data;
				$result = array(
					"code" => 1,
					"message" => $url
				);
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
				if (!empty($_GET['stat']) && string_filter($_GET['stat'])) {
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
				HTMLFooter();
			break;
		}
	
	
	//模块 -- 默认
	default:
		exit;
	break;
}




$Project_sql -> close();
?>