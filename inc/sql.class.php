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
 * 数据库操作类
 * @param $SRVR_type
 *
 */
class _SQL {

	private $SRVR_type;	//指定数据库类型：主库或从库
	private $SRVR_num;	//指定数据库的轮询

	private $db;	//用于对mysqli进行实例化的变量
	
	/**
	 * 构造函数
	 */
	public function __construct($SRVR_type) {
		$this -> SRVR_type = $SRVR_type;	
		if ($this -> SRVR_type == 0 || $this -> SRVR_type == "") {
			/**
			 *	数据库配置文件
			 *	主服务器配置文件
			 */
			$DB_host = array("localhost");			//主数据库地址
			$DB_port = 3306;					//主数据库端口
			$DB_user = array("prerelease");			//主数据库用户名
			$DB_passwd = array("3Jm2TcLjZ7H7QTp4");	//主数据库密码
			$DB = __DB__;

		} elseif ($this -> SRVR_type == 1) {
			/**
			 *	数据库配置文件
			 *	从服务器配置文件
			 */	
			$DB_host = array("localhost") ;			//从数据库地址
			$DB_port = 3306;					//从数据库端口
			$DB_user = array("prerelease");			//从数据库用户名
			$DB_passwd = array("3Jm2TcLjZ7H7QTp4");	//从数据库密码
			$DB = __DB__;

		} elseif ($this -> SRVR_type == 2) {
			/**
			 *	数据库配置文件
			 *	资产数据库配置文件
			 */	
			$DB_host = array("10.200.70.131") ;			//数据库地址
			$DB_port = 3306;					//数据库端口
			$DB_user = array("ituser");			//数据库用户名
			$DB_passwd = array("1qaz!QAZ");	//数据库密码
			$DB = "ityw";

		}

		$this -> _connect($DB_host[0], $DB_user[0], $DB_passwd[0], $DB, $DB_port);
	}
	
	/**
	 * 连接数据库
	 *
	 * @access private
	 *
	 * @return void
	 */
   	private function _connect($DB_host, $DB_user, $DB_passwd, $DB_NAME, $DB_port) {
		$this -> db = new mysqli($DB_host, $DB_user, $DB_passwd, $DB_NAME, $DB_port);
		if ($this -> db -> connect_errno) {
			die("您无权限访问数据库");
			return false;
		}
		
		$this -> db -> query('SET NAMES UTF8;');
	}
	/**
	 * 关闭数据库连接
	 */
	public function close() {
		return $this -> db -> close();
	}

	
	/**
	 * 执行事务处理
	 */
	public function transationSQL() {
		$this -> db -> autocommit(false);
		$sql = func_get_args();
		foreach ($sql as $query) {
			$result = $this -> db -> query($query);
			if (!$result) {
				$check = "1";
			}
		}
		if ($check == "1") {
			$this -> db -> rollback();
		} else {
			$this -> db -> commit();
			return "commit";
		}
	}
	/**
	 * 关闭自动提交，初始化事务
	 */
	public function initcmt() {
		return $this -> db -> autocommit(false);
	}
	/**
	 * 事务回滚
	 */
	public function cmtroll() {
		return $this -> db -> rollback();
	}
	/**
	 * 事务提交
	 */
	public function cmtcommit() {
		return $this -> db -> commit();
	}

	/**
	 * 查询上次query影响行数
	 */
	public function affected() {
		return $this -> db -> affected_rows;
	}	
	/**
	 * 查询结果数组化
	 */
	public function fetch_assoc($query) {
		return $query -> fetch_assoc();
	}	
	/**
	 * 自定义SQL（临时调用）
	 */
	public function userDefine($sql){
		return $this -> db -> query($sql);
	}


	//专用功能SQL
	/**
	 * 登陆验证
	 *
	 * @access public
	 *
	 * @param string $value_user 用户名
	 *
	 * @return array
	 */
	public function LoginAuth($value_user) {
		$sql = "SELECT * FROM `Account` WHERE `account` = '{$value_user}' AND `status` = 1;";
		$query = $this -> db -> query($sql);
		return $query -> fetch_assoc();
	}


	/**
	 * 更新 -> 修改用户密码
	 *
	 * @param newpasswd 提交sha1加密后的密码
	 * @param account 用户账号
	 *
	 */
	public function updateLoginPasswd($newpasswd, $account) {
		$sql = "UPDATE `Account` SET `passwd` = '{$newpasswd}' WHERE `account` = '{$account}';";
		return $this -> db -> query($sql);
	}



	/**
	 * 查询 -> 列出主项目
	 *
	 */
	public function getProjectMain($start = -1,$records = -1) {
		if ($_SESSION['user']['module'] == -1) {
			$sql = "SELECT * from `ProjMain`";
		} else {
			$sql = "SELECT * from `ProjMain` where `id` in({$_SESSION['user']['module']})"; 
		}
		if (isset($start) && $start != -1) {
			$sql .= " LIMIT {$start},{$records};";
		} else {
			$sql .= ";";
		}
		$query = $this -> db -> query($sql);
		return $query;
	}

	/**
	 * 查询 -> 列出子项目
	 * 
	 * @param mid 主项目id
	 *
	 */
	public function getProjectSub($mid = "",$start = -1,$records = -1) {
		if ($_SESSION['proj']['mid'] == "0" || $mid == -1) {
			$sql = "SELECT * from `ProjSub`";
		} else {
			$sql = "SELECT * from `ProjSub` where `mid` = {$mid}";
		}
		if (isset($start) && $start != -1) {
			$sql .= " LIMIT {$start},{$records};";
		} else {
			$sql .= ";";
		}
		$query = $this -> db -> query($sql);
		return $query;
	}

	/**
	 * 查询 -> 列出项目中网站
	 * 
	 * @param wid 子项目id
	 *
	 */
	public function getProjectWebs($sid = "",$start = -1,$records = -1) {
		if ($sid == "") {
			$sql = "SELECT * from `WebSites`";
		} else {
			$sql = "SELECT * from `WebSites` where `sid` in ({$sid})";
		}
		if (isset($start) && $start != -1) {
			$sql .= " LIMIT {$start},{$records};";
		} else {
			$sql .= ";";
		}
		$query = $this -> db -> query($sql);
		return $query;
	}

	/**
	 * 查询 -> 列出网站信息
	 * 
	 * @param 项目id
	 *
	 */
	public function getWeb($wid,$start = -1,$record = -1) {
		$sql = "SELECT * from `WebSites` WHERE `id` = {$wid}";
		if (isset($start) && $start != -1) {
			$sql .= " LIMIT {$start},{$records};";
		} else {
			$sql .= ";";
		}
		$query = $this -> db -> query($sql);
		return $query -> fetch_assoc();
	}

	/**
	 * 查询 -> 列出网站备份信息
	 * 
	 * @param wid 项目id
	 * @param id 备份id
	 *
	 */
	public function getBackup($wid,$id = "",$start = -1,$record = -1) {
		$sql = "SELECT * from `Backup` WHERE `wid` = {$wid} AND `status` = '1'";
		if ($id != "") {
			$sql .= " AND `id` = {$id}";
		}
		if (isset($start) && $start != -1) {
			$sql .= " LIMIT {$start},{$records};";
		} else {
			$sql .= ";";
		}
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 插入 -> 备份记录
	 * 
	 * @param id 项目id
	 * @param bak 备份的文件名
	 *
	 */
	public function insBackup($id,$bak) {
		$sql = "INSERT INTO `Backup` () VALUES ('','{$id}','1',now(),'{$bak}');";
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 删除 -> 备份记录
	 * 
	 * @param id 备份id
	 * @param wid 项目id
	 *
	 */
	public function delBackup($id,$wid) {
		$sql = "DELETE FROM `Backup` WHERE `id` = {$id} AND `wid` = {$wid};";
		$query = $this -> db -> query($sql);
		return $query;
	}

	/**
	 * 查询 -> 账号
	 * 
	 * @param id 备份id
	 *
	 */
	public function getAccount($id = "",$start = -1,$record = -1){
		$sql = "SELECT * from `Account`";
		if ($id != "") {
			$sql .= "WHERE `id` = {$id} AND `status` = '1'";
		}
		if (isset($start) && $start != -1) {
			$sql .= " LIMIT {$start},{$records};";
		} else {
			$sql .= ";";
		}
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 插入 -> 账号
	 * 
	 */
	public function insAccount($no,$name,$pass,$mgr,$module){
		$sql = "INSERT INTO `Account` () VALUES ('','{$no}','{$name}','{$pass}',NULL,NULL,'1',now(),'{$mgr}','{$module}');";
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 变更 -> 账号冻结
	 * 
	 */
	public function frzAccount($id){
		$sql = "UPDATE `Account` SET `status` = (status+1)%2 WHERE `id` = {$id};";
		$query = $this -> db -> query($sql);
		return $query;
	}

	/**
	 * 插入 -> 主项目
	 * 
	 * @param name 主项目名称
	 * @param alias 主项目别名
	 *
	 */
	public function insProjectMain($name,$alias){
		$sql = "INSERT INTO `ProjMain` () VALUES ('','{$name}','{$alias}',now())";
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 删除 -> 主项目
	 * 
	 * @param id 主项目id
	 *
	 */
	public function delProjectMain($id) {
		$sql = "DELETE FROM `ProjMain` WHERE `id` = {$id};";
		$query = $this -> db -> query($sql);
		return $query;
	}
	/**
	 * 插入 -> 子项目
	 * 
	 * @param id 备份id
	 * @param wid 项目id
	 *
	 */
	public function insProjectSub($name,$wid){
		$sql = "INSERT INTO `ProjSub` () VALUES ('','{$wid}','{$name}')";
		$query = $this -> db -> query($sql);
		return $query;
	}

	/**
	 * 删除 -> 子项目
	 * 
	 * @param id 备份id
	 * @param wid 项目id
	 *
	 */
	public function delProjectSub($id) {
		$sql = "DELETE FROM `ProjSub` WHERE `id` = {$id};";
		$query = $this -> db -> query($sql);
		return $query;
	}


	/**
	 * 插入 -> 网站
	 * 
	 * @param id 备份id
	 *
	 */
	public function insProjectWeb($sid,$name,$addr,$uld,$bak,$rls){
		$sql = "INSERT INTO `WebSites` () VALUES ('','{$sid}','{$name}','{$addr}','{$uld}','{$bak}','{$rls}',NULL,NULL)";
		$query = $this -> db -> query($sql);
		return $query;
	}

	/**
	 * 删除 -> 子项目
	 * 
	 * @param id 备份id
	 * @param wid 项目id
	 *
	 */
	public function delProjectWeb($id) {
		$sql = "DELETE FROM `WebSites` WHERE `id` = {$id};";
		$query = $this -> db -> query($sql);
		return $query;
	}



	/**
	 * 查询 -> 资产
	 * 
	 * @param $arr 传入的数据
	 * @param $start 数据起始点（用于分页）
	 * @param $record 数据记录数（用于分页）
	 *
	 */
	public function getAssets($arr,$start = -1,$records = 20) {
		$sql = "SELECT * FROM `t_asset_info` AS a,`t_project_info` AS b where a.id=b.id ";
		if (is_array($arr) && !empty($arr)) {
			$field = "";
			foreach ($arr as $key => $value) {
				$field[] = "`{$key}` LIKE '%{$value}%' ";
			}
			$where = implode(" AND ",$field);
			$sql .= "AND ".$where;
		}
		if ($start >= 0 && $records > 0) {
			$sql .= "ORDER BY `sid` ASC LIMIT {$start},{$records};";
		} else {
			$sql .= "ORDER BY `sid` ASC;";
		}
		$query = $this -> db -> query($sql);
		return $query;

	}

	public function getAssetID($id) {
		$sql = "SELECT * FROM `t_asset_info` WHERE `sid` = '{$id}' ";
		$query = $this -> db -> query($sql);
		return $query -> fetch_assoc();
	}


	public function delAsset($id) {
		$sql = "DELETE FROM `t_asset_info` WHERE `sid` = {$id};";
		$query = $this -> db -> query($sql);
		return $query;
	}

	public function insAsset($tb,$arr) {
		if (is_array($arr) && !empty($arr)) {
			$field = "";
			$value = "";
			foreach ($arr as $key => $value) {
				$field[] = "`".$key."`";
				$val[] = "'".$value."'";
			}
			$field_val = implode(",",$field);
			$value_val = implode(",",$val);
		}
		$sql = "INSERT INTO `{$tb}` ($field_val) VALUES ($value_val);";
		$query = $this -> db -> query($sql);
		return $query;
	}
	public function uptAsset($tb,$arr,$id) {
		if (is_array($arr) && !empty($arr)) {
			$data = "";
			foreach ($arr as $key => $value) {
				$data[] = "`{$key}` = '{$value}'";
			}
			$modi_data = implode(",",$data);
		}
		$sql = "UPDATE `{$tb}` SET {$modi_data} WHERE `sid` = '{$id}';";
		$query = $this -> db -> query($sql);
		return $query;
	}

		

}
?>