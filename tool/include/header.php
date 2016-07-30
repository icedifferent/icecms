<?php
ob_start();
session_start();
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('PRC');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
function title($title,$description=null,$keywords=null){
$title = empty($title) ? '标题未定义' : $title;
$description = empty($description) ? 'ICECMS旗下工具，源码查看，http请求查看，html在线练习，解码，加密' : $description;
$keywords = empty($keywords) ? '源代码查看,http请求,html在线练习,解码,加密,php,http,icecms,网站,冰封' : $keywords;
echo <<<HTML
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <title>{$title} - ICECMS论坛</title>
  <meta name="description" content="{$description}"/>
  <meta name="keywords" content="ICECMS,PHP工具,{$keywords}"/>
  <link rel="shortcut icon" type="image/x-icon" href="http://icecms.cn/favicon.ico"/>
  <link rel="stylesheet" type="text/css" href="./style.css"/>
</head>
<body>
HTML;
}
function footer(){
echo <<<HTML
<div class="footer">Copyright 2015 WWW.ICECMS.CN
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1254974892'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s11.cnzz.com/z_stat.php%3Fid%3D1254974892%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script>
<br/><a href="http://icp.aizhan.com/" target="_blank"><script type="text/javascript" src="http://icp.aizhan.com/geticp/?host=www.icecms.cn&style=1" charset="utf-8"></script></a>
</div>
</body></html>
HTML;
ob_end_flush();
}
?>
