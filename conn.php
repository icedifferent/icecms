<?php
echo '--------------';
$cfg_dbhost="qdm176405669.my3w.com";
$cfg_dbuser="qdm176405669";
$cfg_dbpwd="icecmss394960830";
$cfg_dbname="qdm176405669_db";
$conn = @mysql_connect("qdm176405669.my3w.com","qdm176405669","icecmss394960830");
	if (!$conn){
		die("连接数据库失败：" . mysql_error());
	}
	mysql_select_db( "install", $conn);
	mysql_query("SET NAMES utf8");
	mysql_query("set character_set_client=utf8"); 
	mysql_query("set character_set_results=utf8");
