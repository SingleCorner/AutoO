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
 * 输出页脚信息与版权信息
 *
 */
function HTMLFooter(){
?>
	</div>
	<div id="infomation">
	</div>
	<!-- 主容器结束 -->
	<!-- 页脚开始 -->
	<footer id="APP_foot">
		<div id="APP_foot_copyright">
			Copyright © 2014 - 2017<br />
		</div>
		<div id="APP_foot_license">
			<div>本项目遵循GPLv2协议开源</div>
			<div>上海通路快建网络外包服务有限公司</div>
		</div>
	</footer>
	<!-- 页脚结束 -->
</body>
</html>
<?php
}
?>