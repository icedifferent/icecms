<?php
error_reporting(0);
require 'include/header.php';
if($_GET['small'] == 'http'){
    echo '<pre>';
    $array = array('HTTP_ACCEPT','HTTP_ACCEPT_ENCODING','HTTP_ACCEPT_LANGUAGE','HTTP_CONNECTION','HTTP_HOST','HTTP_REFERER','HTTP_USER_AGENT','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR','REMOTE_PORT','REQUEST_METHOD','REQUEST_TIME','REQUEST_URI','SERVER_PROTOCOL');
    foreach($array as $row){
      echo $row.' = '.$_SERVER[$row]."\n";
    }
    echo "-----POST-----\n";
    foreach($_POST as $key=>$row){
        echo $key.' = '.$row."\n";
    }
    echo "-----COOKIE-----\n";
    foreach($_COOKIE as $key=>$row){
        if($key == 'PHPSESSID'){ continue; }
        echo $key.' = '.$row."\n";
    }
    die();
}elseif($_GET['small'] == 'server'){
  echo '<pre>';
  print_r($_SERVER);
  die();
}
title('浏览器UA查看','查看你浏览器浏览网页时所携带的信息');
require 'include/qqwry.php';  
$czip = new IpLocation(ROOT.'/include/qqwry.dat');
$czip->encoding = 'UTF-8';
echo '
<div class="title">浏览器UA信息查看-<a href="?small=http">开发模式</a></div>
<style type="text/css">
.httpbg {
background: #a9aca9;
color: blue;
margin: 2px;
padding: 0 0 2px 0;
border-radius: 2px;
}
.httptitle {
background-color: #7836ff;
color: #fff;
border-radius: 2px 2px 0 0;
}
</style>
<div class="httpbg"><div class="httptitle">IP地址</div>';
if($remote_addr = getenv("REMOTE_ADDR")){
    $ipaddr = $czip->getlocation($remote_addr);
    echo 'Remote Addr:'.$remote_addr.'&nbsp;'.$ipaddr['country'].'&nbsp;'.$ipaddr['area'].'<br/>';
}
if($client_ip = getenv("HTTP_CLIENT_IP")){
    $ipaddr = $czip->getlocation($client_ip);
    echo 'client_ip:'.$client_ip.'&nbsp;'.$ipaddr['country'].'&nbsp;'.$ipaddr['area'].'<br/>';
}
if($x_forwarded_for = getenv("HTTP_X_FORWARDED_FOR")){
    echo 'x_forwarded_for:<br/>';
    foreach(array_unique(explode(',',strtr($x_forwarded_for,array(' '=>'')))) as $x_ip){
      $ipaddr = $czip->getlocation($x_ip);
      echo '&nbsp;&nbsp;'.$x_ip.'&nbsp;'.$ipaddr['country'].'&nbsp;'.$ipaddr['area'].'<br/>';
    }
    
}elseif($x_forwarded = getenv("HTTP_X_FORWARDED_FOR")){
    $ipaddr = $czip->getlocation($x_forwarded);
    echo 'x_forwarded: '.$x_forwarded.'&nbsp;'.$ipaddr['country'].'&nbsp;'.$ipaddr['area'].'<br/>';
}elseif($forwarded = getenv("HTTP_FORWARDED_FOR")){
    $ipaddr = $czip->getlocation($forwarded);
    echo 'x_forwarded: '.$forwarded.'&nbsp;'.$ipaddr['country'].'&nbsp;'.$ipaddr['area'].'<br/>';
}
echo '
</div>
<div class="httpbg"><div class="httptitle">请求方法-Method</div>
'.$_SERVER['REQUEST_METHOD'].'</div>
<div class="httpbg"><div class="httptitle">通用资源标志符-URI</div>
'.$_SERVER['REQUEST_URI'].'</div>
<div class="httpbg"><div class="httptitle">主机-host</div>
'.$_SERVER['HTTP_HOST'].'</div>
<div class="httpbg"><div class="httptitle">Post</div>
';
if(empty($_POST)){
      echo '无POST请求';
  }else{
      foreach($_POST as $key=>$value){
          echo $key.'='.$value.'<br/>';
      }
}
echo '</div><div class="httpbg"><div class="httptitle">Cookie</div>';
if(empty($_COOKIE)){
      echo '无COOKIE数据';
  }else{
      foreach($_COOKIE as $key=>$value){
        if($key == 'PHPSESSID'){ continue; }
          echo $key.'='.$value.'<br/>';
      }
}
echo '</div>
<div class="httpbg"><div class="httptitle">来源地址-Referer</div>
'.$_SERVER['HTTP_REFERER'].'</div>
<div class="httpbg"><div class="httptitle">连接-Connection</div>
'.$_SERVER['HTTP_CONNECTION'].'</div>
<div class="httpbg"><div class="httptitle">支持的MIME-Accept</div>
'.$_SERVER['HTTP_ACCEPT'].'</div>
<div class="httpbg"><div class="httptitle">Accept-Encoding</div>
'.$_SERVER['HTTP_ACCEPT_ENCODING'].'</div>
<div class="httpbg"><div class="httptitle">浏览器语言-Language</div>
'.$_SERVER['HTTP_ACCEPT_LANGUAGE'].'</div>
<div class="httpbg"><div class="httptitle">浏览器UA-User Agent</div>
'.$_SERVER['HTTP_USER_AGENT'].'</div>
';
footer();
?>
