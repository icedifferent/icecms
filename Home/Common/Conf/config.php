<?php
$arr=include './config.php';
$arr2=array(
	//'配置项'=>'配置值'
'DEFAULT_THEME'  => 'wap2',//默认主题
'TMPL_DETECT_THEME'=>true,//自动侦测模板主题
'THEME_LIST'=>'web,wap2',//支持的模板主题列表
'DEFAULT_FILTER' => 'htmlspecialchars',// 默认参数过滤方法用于I函数...
'DEFAULT_TIMEZONE' => 'PRC', // 默认时区
);
return array_merge($arr,$arr2);
