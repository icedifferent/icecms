<?php
require 'include/header.php';
title('Whois查询');
echo '<div class="title">Whois查询-<a href="/">ICECMS论坛</a></div>
<style type="text/css">
form {text-align:center;} input[type=text] {width:75%;}
</style>
<form action="whois.php" method="get">
<span class="nice">暂不支持查询中文域名，查询中文域名前请先把<a href="domaindecode.php">中文域名编码</a>！</span>
<input type="text" name="domain" placeholder="请输入要查询的域名" />
<input type="submit" value="查询" />
</form>';
if(isset($_GET['domain'])){
    $url = "http://whoisxmlapi.com/whoisserver/WhoisService?domainName={$_GET['domain']}&outputFormat=json";
    $whois = json_decode(file_get_contents($url), true);
    if($whois['ErrorMessage']){
        echo '<div class="error">'.$whois['ErrorMessage']['msg'].'</div>';
    }elseif($whois['WhoisRecord']['parseCode'] == 0){
        echo '<div class="info">该域名尚未注册!</div>';
    }else{
        $whois = $whois['WhoisRecord'];
        echo "<p>
        当前域名: {$whois['domainName']}<br/>
        域名状态: {$whois['registryData']['status']}<br/>
        更新时间: {$whois['audit']['updatedDate']['$']}<br/>
        域注册商: {$whois['registrarName']}<br/>
        域注册人: {$whois['registryData']['registrant']['name']}<br/>
        联系邮箱: {$whois['registryData']['registrant']['email']}<br/>
        注册时间: {$whois['registryData']['createdDateNormalized']}<br/>
        过期时间: {$whois['registryData']['expiresDateNormalized']}<br/>
        使用天数: {$whois['estimatedDomainAge']} 天</p>
        <pre>{$whois['registryData']['rawText']}</pre>";
    }
}else{
    echo '<div class="title">什么是Whois?</div>
    <p class="details">whois是用来查询域名的IP以及所有者等信息的传输协议。简单说，whois就是一个用来查询域名是否已经被注册，以及注册域名的详细信息的数据库（如域名所有人、域名注册商）。<br/>“WHOIS”[2] 是当前域名系统中不可或缺的一项信息服务。在使用域名进行Internet冲浪时，很多用户希望进一步了解域名、名字服务器的详细信息，这就会用到WHOIS。对于域名的注册服务机构（registrar）而言，要确认域名数据是否已经正确注册到域名注册中心（registry），也经常会用到WHOIS。直观来看，WHOIS就是链接到域名数据库的搜索引擎，一般来说是属于网络信息中心（NIC）所提供和维护的名字服务之一。</p>';
}
footer();
?>
