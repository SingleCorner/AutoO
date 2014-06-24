<?php

/**
 * 文件不允许访问
 *
 */
if (!defined('__ROOT__')) {
	header("Status: 404");
	exit;
}

function rsync_replace($arr) {
	$info = "变更的文件如下：\r";
	foreach ($arr as $value){
		if(rsync_check($value)) {
			$info .= $value."更新成功\r";
		}
	}
	return $info;
}

?>