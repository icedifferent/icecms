<?php
require 'include/header.php';
require 'include/domainidna.class.php';
title('中文域名编码解码  icecms编程论坛');
echo '<div class="title">中文域名编码/解码工具-<a href="/">返回ICECMS论坛</a></div>';
$IDN = new idna_convert();
if(isset($_POST['type'])&&$_POST['type'] == '编码成英文域名'){
    if(empty($_POST['domain'])){
        echo '<div class="info">请输入要编码的域名,如:&nbsp;蒋文健.cn</div>';
    }elseif($data = $IDN->encode($_POST['domain'])){
        echo '<div class="success">编码成功!<br/>结果:'.$data.'</div>';
    }else{
        echo '<div class="error">'.$IDN->error.'</div>';
    }
}elseif(isset($_POST['type'])&&$_POST['type'] == '解码成中文域名'){
    if(empty($_POST['domain'])){
        echo '<div class="info">请输入要解码的域名,如:&nbsp;xn--nyqz82ctuw.cn</div>';
    }elseif($data = $IDN->decode($_POST['domain'])){
        echo '<div class="success">解码成功!<br/>结果:'.$data.'</div>';
    }else{
        echo '<div class="error">'.$IDN->error.'</div>';
    }
}
echo '
<style type="text/css">
.domain {text-align:center}
.domain input[type=text] {width:70%;}
</style>
<form action="'.$_SERVER['PHP_SELF'].'" method="post" class="domain">
<input type="text" name="domain" value="'.@$data.'" placeholder="请输入要编码或解码的域名"/><br/>
<input type="submit" name="type" value="编码成英文域名"/>
<input type="submit" name="type" value="解码成中文域名"/>
</form>';

echo '
<div class="title">什么是中文域名编码？</div>
<p class="details">中文域名转码就是将中文字符串与punycode标准编码的字符串相互转换。<br/>
Punycode是一个根据RFC 3492标准而制定的编码系统,主要用於把域名从地方语言所采用的Unicode编码转换成为可用於DNS系统的编码。Punycode可以防止所谓的IDN欺骗。<br/>
早期的DNS（Domain Name System）是只支持英文域名解析。在IDNs（国际化域名Internationalized Domain Names）推出以后，为了保证兼容以前的DNS，所以，对IDNs进行punycode转码，转码后的punycode就由26个字母+10个数字，还有“-”组成。</p>';
footer();
?>
