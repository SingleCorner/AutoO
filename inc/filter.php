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
 * 1.1	过滤 -> 注入检测
 *
 * param $string -> 传入的字符串
 */
function string_filter($string) {
	if (preg_match("/[\<\>]+/", $string) !== 0) {
		return false;
	}
	if (preg_match("/(union)|[,='\"]/i", $string) !== 0) {
		return false;
	}
	
	return $string;
}


/**
 * 1.2	过滤 -> 数字检测
 *
 * param $string -> 传入的数字字符串
 */
function numeric_filter($string) {
	if (is_numeric($string)) {
		return $string;
	} else {
		return FALSE;
	}
	
}


/**
 * 1.3	过滤 -> XSS检测
 *
 * param $string -> 传入的数字字符串
 */
function XSS_filter($string) {
	if (preg_match("/(<script>)/", $string) !== 0) {
		return false;
	}	
	return $string;
}


/**
 * 2.1	检测 -> 过滤RSYNC不必要信息
 *
 * param $string -> 传入的数字字符串
 */
function rsync_check($string) {
	if (preg_match("/(B\/s)|(sending)|(sent\ )|(total\ size)/", $string) === 0) {
		return $string;
	}	
}


?>