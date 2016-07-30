<?php
function geturl($Url,$Post=null){
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$Url);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    if(!empty($Post)){
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$Post);
    }
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    return curl_exec($ch);
    curl_close($ch);
}
function GetIP(){ 
    if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown")){
        $ip = getenv("HTTP_CLIENT_IP");
    }elseif(getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    }elseif(getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
        $ip = getenv("REMOTE_ADDR");
    }elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
        $ip = $_SERVER['REMOTE_ADDR'];
    }else{
        $ip = "0.0.0.0";
    }
    return($ip);
}