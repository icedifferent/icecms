<?php
return array(
	//'配置项'=>'配置值'
	// 添加数据库配置信息
 'DB_TYPE'   => 'mysql', // 数据库类型
 'DB_HOST'   => 'localhost', // 服务器地址
 'DB_NAME'   => 'icecms', // 数据库名
 'DB_USER'   => 'icecms', // 用户名
 'DB_PWD'    => 'mima', // 密码
 'DB_PORT'   => 3306, // 端口
 'DB_PREFIX' => 'icebbs_', // 数据库表前缀
 'DEFAULT_TIMEZONE' => 'PRC', // 默认时区
'TMPL_L_DELIM'=>'<{', //修改左定界符
'TMPL_R_DELIM'=>'}>', //修改右定界符
'TMPL_EXCEPTION_FILE' => './Home/Home/View/Public/404.html',
//'ERROR_PAGE'=>'/404.html',
//'URL_CASE_INSENSITIVE' => true, 
// 默认false 表示URL区分大小写 true则表示不区分大小写
//'TMPL_CACHE_ON' => TRUE,//禁止模板编译缓存
//'HTML_CACHE_ON' => TRUE,//禁止静态缓存 
//'SHOW_PAGE_TRACE'=>true,//开启页面Trace
//'SHOW_ERROR_MSG' => true,
//'DB_FIELDS_CACHE' = false,
);?>
