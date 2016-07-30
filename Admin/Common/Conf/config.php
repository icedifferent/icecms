<?php
$arr=include './config.php';
$arr2=array(
'TMPL_TEMPLATE_SUFFIX'=>'.html',//更改模板文件后缀名
//'DEFAULT_THEME'  => 'wap',//默认主题
//'DEFAULT_THEME'  => 'web',//默认主题
//'TMPL_DETECT_THEME'=>true,//自动侦测模板主题
//'THEME_LIST'=>'wap,web,html5',//支持的模板主题列表
'DEFAULT_FILTER' => 'htmlspecialchars',// 默认参数过滤方法用于I函数...

);
return array_merge($arr,$arr2);
