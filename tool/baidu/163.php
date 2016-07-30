<?php
//登录163邮箱并且获取最新的邮件内容
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
function get_new($name,$pass){
//首次访问mail.163.com没有产生任何cookie/
$url="https://mail.163.com/entry/cgi/ntesdoor?df=mail163_letter&from=web&funcid=loginone&iframe=1&language=-1&passtype=1&product=mail163&net=t&style=-1&race=-2_484_-2_hz&uid=qianxi770231@163.com";
$httpheader = array(
'User-Agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/2008052906 Firefox/3.0',
            'Referer'    => 'http://www.163.com'
	);
  $fields_post = array(
            'username'      => $name,
            'password'      => $pass,
            'savelogin'  => 1,
            'url2'         => "http://mail.163.com/errorpage/error163.htm",
    ); 
        $fields_string = '';    
foreach($fields_post as $key => $value){
            $fields_string .= $key . '=' . $value . '&';
	}    
	$fields_string = rtrim($fields_string , '&');
//	$cookie ="cookie.txt";
	$content= curl($url,0,0,$httpheader,"http://mail.163.com/",0,1,$fields_string,0);
	preg_match('/\?sid=(.*)&/isU',$content[1],$arr);
	preg_match_all('/Set-Cookie: (.*);/isU',$content[1],$arr2);
	if(!isset($arr[1])){
		echo "邮箱密码错误<a href ='./index.php'>点击返回首页</a>";
		exit();
	}
        $sid=$arr[1];
	$c='';
	foreach($arr2[1] as $d){
		$c=$c.$d.'; ';
	}
	$url="http://mail.163.com/js6/main.jsp?sid={$sid}&df==mail163_letter";
	$content= curl($url,0,0,$httpheader,"http://mail.163.com/",0,1,0,$c);
	preg_match_all('/Set-Cookie: (.*);/isU',$content[1],$arr2);
	foreach($arr2[1] as $d){
		$c=$c.$d.'; ';
	}//合并cookie
	$url="http://mail.163.com/js6/s?sid={$sid}&func=mbox:listMessages&topNav_mobileIcon_show=1&TopTabReaderShow=1&TopTabLofterShow=1&welcome_welcomemodule_mailrecom_click=1";//未读邮件列表json
//	$url="http://icecms.cn/tool/http.php";
	$content= curl($url,0,0,$httpheader,"http://mail.163.com/",0,1,0,$c);

//	$fp = @fopen("Log.html", "w"); //记录捕获到的页面源码
//	fwrite($fp,$content[1]); 
//	fclose($fp);
	preg_match_all('/"id">(.*)</isU',$content[1],$arr);
	foreach($arr[1] as $d){
		;
	}
	$id=$d;
	preg_match_all('/Set-Cookie: (.*);/isU',$content[1],$arr2);
	foreach($arr2[1] as $d){
		$c=$c.$d.'; ';
	}//合并cookie
	http://mail.163.com/js6/read/readhtml.jsp?mid=72:1tbiSAsHMlXldOeJnAAAsA&font=15&color=064977
	$url="http://mail.163.com/js6/read/readhtml.jsp?mid={$id}&font=15&color=064977";
	$content= curl($url,0,0,$httpheader,"http://mail.163.com/",0,0,0,$c);
	return $content[1];//最新邮件的内容
}



		

