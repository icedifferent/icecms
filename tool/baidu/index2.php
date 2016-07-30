<?php
//提交注册信息，接受验证码
echo "<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
			<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0,  user-scalable=0;' name='viewport' />
				<title>百度账号注册</title>
				</head>
				<body>";
	$httpheader = array(
		'User-Agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/2008052906 Firefox/3.0',
                'Referer'    => 'http://baidu.com'
       );
	$name=$_POST['name'];
	$pass=$_POST['pass'];
	$gid=$_POST['gid'];
	$token=$_POST['token'];
	$codeString=$_POST['codeString'];
	$code=$_POST['code'];
	$phone=$_POST['phone'];
	$c=$_POST['cookie'];
	require_once("163.php");
	$fields_post = array(
		'staticpage'=>	'http://zhixin.baidu.com/Jump/index?module=onesite',
		'charset'=>	'UTF-8',
		'registerType'=>1,
		'verifypass'=>	$pass,
		'token'=>	$token,
		'tpl'=>	'zhixin',
		'subpro'=>'',
		'apiver'=>'v3',
		'tt'=>	1454648880433,
		'retu'=>'http://jiaoyu.baidu.com/mp/index',
		'u'=>	'http://zhixin.baidu.com/Reg/done?module=onesite&from=jiaoyu',
		'quick_user'=>	1,
		'regmerge'=>'true',
		'suggestIndex'=>'',	
		'suggestType'=>	'',
		'codestring'=>$codeString,
		'vcodesign'=>'',
		'vcodestr'=>'',
		'gid'=>$gid,
		'app'=>	'',
		'pass_reg_suggestuserradio_0'=>	'',
		'islowpwdcheck'=>'undefined',
		'logRegType'=>'pc_regBasic',
		'isexchangeable'=>	0,
		'exchange'=>	0,
		'sloc'=>'loaded###223#167#1454648856896##1454648880421@email#######@userName#######@phone#######@smscode#######@verifyCode#137#30#73.5#10#1454648873033#1454648875854#1454648880027@password#282#30#93.5#28#1454648864656#1454648871675#1454648865917@submit#300#34#84.5#23#1454648880125##@',
		'account'=>$name,
		'loginpass'=>$pass,
		'verifycode'=>$code,
		'isagree'=>'on',
		'ppui_regtime'=>'23701',
		'email'=>$name,
		'callback'=>'parent.bd__pcbs__yexs4d',
	 ); 
        $fields_string = '';    
	foreach($fields_post as $key => $value){
            $fields_string .= $key . '=' . $value . '&';
	}    
	$fields_string = rtrim($fields_string , '&');
	$url="https://passport.baidu.com/v2/api/?reg";
	$content= curl($url,0,0,$httpheader,"http://zhixin.baidu.com/Reg/index?module=onesite&u=http%3A%2F%2Fjiaoyu.baidu.com%2Fmp%2Findex&from=jiaoyu",0,1,$fields_string,$c);
	preg_match_all('/Set-Cookie: (.*);/isU',$content[1],$arr2);
	foreach($arr2[1] as $d){
		$c=$c.$d.'; ';
	}//合并cookie
	$url="http://zhixin.baidu.com/Reg/done?module=onesite&from=jiaoyu&email={$name}&username=&tpl=zhixin&ppregtype=email&regmerge=true";
	$content= curl($url,0,0,$httpheader,"http://zhixin.baidu.com/Reg/index?module=onesite&u=http%3A%2F%2Fjiaoyu.baidu.com%2Fmp%2Findex&from=jiaoyu",0,1,0,$c);
	//发送邮箱验证
//	sleep(6);//等待邮件的接收
	$content=get_new($name,$pass);//获取邮件
	preg_match('/><a href="(.*)"/isU',$content,$arr);
	preg_match('/vstr=(.*)&/isU',$content,$arr1);
	if(!preg_match('/百度/',$content)){
		echo "没有接收到邮件,<a href ='./index.php'>点击返回首页</a>";
		exit();
	}
	$urls=$url=$arr[1];//得到激活链接
	$ve=$arr1[1];//得到激活码
	if($urls==''){
		echo "没有接收到邮件,<a href ='./index.php'>点击返回首页</a>";
		exit();
	}
	$url=preg_replace("/&amp;/",'&',$url);
	$httpheader = array(
            'Referer'    => 'http://passport.baidu.com'
    	);
//	$url=urldecode($url);
	$content= curl($url,0,0,$httpheader,"http://passport.baidu.com",0,1);
	preg_match_all('/Set-Cookie: (.*);/isU',$content[1],$arr2);
	$c='';
	foreach($arr2[1] as $d){
		$c=$c.$d.'; ';
	}
	$fields_post = array(
		'country'	=>'中国香港',
		'phone'=>	$phone,
		'vcode'=>'',
		'isbindmobile'=>1,
		'vstr'=>$ve,
		'verifytype'=>1
	); 
 	$fields_string = '';    
	foreach($fields_post as $key => $value){
            $fields_string .= $key . '=' . $value . '&';
	}    
	$fields_string = rtrim($fields_string , '&');
	$url="http://passport.baidu.com/v2/?regverifycheck";
	$content= curl($url,0,0,$httpheader,"http://zhixin.baidu.com/Reg/index?module=onesite&u=http%3A%2F%2Fjiaoyu.baidu.com%2Fmp%2Findex&from=jiaoyu",0,1,$fields_string,$c);//发送验证码
	preg_match_all('/Set-Cookie: (.*);/isU',$content[1],$arr2);
	foreach($arr2[1] as $d){
		$c=$c.$d.'; ';
	}
	echo "
			<form method='post' action ='index3.php'>
			请输入手机验证码:<input name='code' type='text' ><br />
			<input name='cookie' type='hidden' value={$c}>
			<input name='ve' type='hidden' value={$ve}>
			<input name='phone' type='hidden' value={$phone}>
			<input name='urls' type='hidden' value={$urls}>
			<input name='submit' type='submit' value='提交'>
			</form>
			<br />powered by <a href='http://icecms.cn'>icecms</a><br />
			<body>
		</html>";


