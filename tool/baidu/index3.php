<?php
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
            'Referer'    => 'http://passport.baidu.com'
    	);
	$code=$_POST['code'];
	$ve=$_POST['ve'];
	$phone=$_POST['phone'];
	$c=$_POST['cookie'];
	$urls=$_POST['urls'];
	//print_r($_POST);
	$fields_post = array(
		'country'	=>'中国香港',
		'phone'=>	$phone,
		'vcode'=>$code,
		'isbindmobile'=>1,
		'vstr'=>$ve,
		'verifytype'=>2
	);    
	$fields_string = '';    
	foreach($fields_post as $key => $value){
            $fields_string .= $key . '=' . $value . '&';
	}    
	$fields_string = rtrim($fields_string , '&');
	$url="http://passport.baidu.com/v2/?regverifycheck";
	$content= curl($url,0,0,$httpheader,"http://zhixin.baidu.com/Reg/index?module=onesite&u=http%3A%2F%2Fjiaoyu.baidu.com%2Fmp%2Findex&from=jiaoyu",0,1,$fields_string,$c);//验证验证码
	//	echo $content[1];
	echo "<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
			<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0,  user-scalable=0;' name='viewport' />
				<title>百度账号注册</title>
				</head>
				<body>";
	if(preg_match('/验证码已失效/',$content[1])){
		echo "验证码已失效,请返回首页重新再试》》<a href ='./index.php'>点击返回首页</a>";
	}else{
		echo "注册成功，账号为163邮箱账号，密码也是163邮箱密码，可以自行登录修改密码<a href ='./index.php'>点击返回首页</a>";
	}
	echo "<br />powered by <a href='http://icecms.cn'>icecms</a><br />	<body>
		</html>";
	$url=$urls;
	$content= curl($url,0,0,$httpheader,"http://zhixin.baidu.com/Reg/index?module=onesite&u=http%3A%2F%2Fjiaoyu.baidu.com%2Fmp%2Findex&from=jiaoyu",0,1,0,$c);//激活
//	exit("验证成功(如果验证码错误则不成功)");
	//{"bindphone":null,"errno":300001,"errmsg":"验证码已失效，请重新发送"}
