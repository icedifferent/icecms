
<?php
//获取百度注册页面的信息
function curl($url,$cookiefile=0,$cookiejar=0,$httpheader=0,$referer="http://baidu.com",$follow=0,$header=1,$postdata=0,$cookie=0,$proxy=0,$outtime=200000,$UA=0){
		$ch = curl_init();
		curl_setopt ( $ch, CURLOPT_NOSIGNAL,true);//开启毫秒超时
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $outtime);//10s超时
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_URL,$url) ;
		if($UA==0){
			curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36');
		}else{
			curl_setopt($ch, CURLOPT_USERAGENT,$UA);
		}
		curl_setopt($ch, CURLOPT_REFERER, $referer);       //伪装REFERER
		curl_setopt($ch, CURLOPT_HEADER, $header);
		if($httpheader){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ( $ch, CURLOPT_COOKIESESSION, true );
		if($follow){
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,$follow);//跟随重定向
		}
		if($cookie)
			curl_setopt($ch,CURLOPT_COOKIE,$cookie);
		if($cookiefile){
			curl_setopt($ch, CURLOPT_COOKIEFILE,$cookiefile);
		}
		if($postdata){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		}else{
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		}
		 curl_setopt($ch, CURLINFO_HEADER_OUT, true);//开启返回请求头查看
		//保存cookie
		if($cookiejar){
			curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar);
		}
		//代理
		if($proxy){
			curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
			curl_setopt($ch, CURLOPT_PROXY, $proxy['ip']); //代理服务器地址
			curl_setopt($ch, CURLOPT_PROXYPORT,$proxy['port']); //代理服务器端口
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
		}
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		$content=curl_exec($ch);
		if($content === false){
   			 $content= 'Curl error: ' . curl_error($ch);
		}
		$info=curl_getinfo($ch);
		$result[1]=$content;
		$result[0]=$info;
		curl_close($ch);
		return $result;
}
$httpheader = array(
'User-Agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/2008052906 Firefox/3.0',
            'Referer'    => 'http://baidu.com'
    );
$url="http://zhixin.baidu.com/Reg/index?module=onesite&u=http%3A%2F%2Fjiaoyu.baidu.com%2Fmp%2Findex&from=jiaoyu";
$content= curl($url,0,0,$httpheader,"http://baidu.com/",0,1);//获取code和token
preg_match_all('/Set-Cookie: (.*);/isU',$content[1],$arr2);
$c='';
foreach($arr2[1] as $d){
 	$c=$c.$d.'; ';
}

$cookie = dirname(__FILE__)."/cookie.txt";
function guideRandom(){
    return strtoupper(preg_replace_callback('/[xy]/',function ($n){
        $m=rand(0,15) | 0;
        $l = ($n[0] == "x") ? $m : ($m & 3 | 8);
        return dechex($l);
    },'xxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'));
}
$gid=guideRandom();
$url="https://passport.baidu.com/v2/api/?getapi&tpl=zhixin&apiver=v3&tt=1454648856733&class=reg&gid={$gid}&app=&callback=bd__cbs__lcb1cw";
$content= curl($url,0,0,$httpheader,"http://baidu.com/",0,1,0,$c);//获取code和token
//echo file_get_contents($url);
//echo $content[1];
//exit();
preg_match_all('/Set-Cookie: (.*);/isU',$content[1],$arr2);
foreach($arr2[1] as $d){
 	$c=$c.$d.'; ';
}//合并cookie
preg_match('/codeString" : "(.*)",/isU',$content[1],$arr);
$codeString=$arr[1];//string
preg_match('/token" : "(.*)",/isU',$content[1],$arr);
$token=$arr[1];//token
//再获取验证码
//echo $codeString;
$url="https://passport.baidu.com/cgi-bin/genimage?{$codeString}";
$content= curl($url,0,0,$httpheader,"http://baidu.com/",0,1,0,$c);//验证码
preg_match_all('/Set-Cookie: (.*);/isU',$content[1],$arr2);
foreach($arr2[1] as $d){
	$c=$c.$d.'; ';
}//合并cookie
// 获得响应结果里的：头大小
$headerSize = $content[0]['header_size'] ;
// 根据头大小去获取头信息内容
$img = substr($content[1],$headerSize);
$f=time();
ob_clean();
	$fp2=@fopen("{$f}.png",'w');
    	fwrite($fp2,$img);
    	fclose($fp2);
		echo "<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
			<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0,  user-scalable=0;' name='viewport' />
				<title>百度账号注册_powered by icecms</title>
				</head>
				<body>
				<form method='post' action ='index2.php'>
			163邮箱账号:<input name='name' type='text' ><br />
			163邮箱密码:<input name='pass' type='password' ><br />
			激活百度账号的手机号码:<input name='phone' type='text' ><br />
			<input name='gid' type='hidden' value={$gid}>
			<input name='token' type='hidden' value={$token}>
			<input name='codeString' type='hidden' value={$codeString}>
			<input name='cookie' type='hidden' value={$c}>
			<img src='{$f}.png'><br />
			验证码:<input name='code' type='text'><br />
			<input name='submit' type='submit' value='提交'>
			</form>
			<br />此页面提交需要等待6s<br />
			<br />手机号码是拿来激活，并不绑定，可以重复利用<br />
			<br />注册后，百度的登录密码就是你的163邮箱密码<br />
			<br />此程序不判断验证码是否正确，如验证码错误，则可能接收不到邮件<br />
			<br />仅限163邮箱哦,注册后可以通过邮箱更改密码<br />
			<br />如果没收到邮件，可能网络延迟或者密码错误或者已经注册百度账号<br />
			<br />powered by <a href='http://icecms.cn'>icecms</a><br />
			<body>
			</html>";

