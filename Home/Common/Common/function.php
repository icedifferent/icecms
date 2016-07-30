<?php


//通过id找出论坛板块名称的函数
function find_bbs_board($id){
	$m=M('Bbs_board');
	$name=$m->where("bbs_board_id='$id'")->getField("bbs_board_name");
	return $name;
}

function find_article_board($id){
	$m=M('Article_board');
	$name=$m->where("article_board_id='$id'")->getField("article_board_name");
	return $name;
}
//最新回复的帖子
function show_post_r($id,$num){
		$m=M('Bbs_post');
		$list = $m->where("post_board_id={$id}")->order('post_respond_time desc')->limit(0,$num)->select();
		foreach($list as $key=>$post){
			echo '<div class="title_bbs"><a href=__MODULE__/bbs/read/id/'.$post['post_id'].'.html>'.$post['post_title'].'</a></div>';
		}
}


//最新发表的帖子
function show_post_n($id,$num){
		$m=M('Bbs_post');
		$list = $m->where("post_board_id={$id}")->order('post_date desc')->limit(0,$num)->select();
		foreach($list as $key=>$post){
			echo '<div class="title_bbs"><a href=__MODULE__/bbs/read/id/'.$post['post_id'].'.html>'.$post['post_title'].'</a></div>';
		}
}
//最新回复的文章
function show_article_r($id,$num){
		$m=M('Bbs_Article');
		$list = $m->where("post_board_id={$id}")->order('article_respond_time desc')->limit(0,$num)->select();
		foreach($list as $key=>$post){
			echo '<div class="title_bbs"><a href=__MODULE__article/read/id/'.$post['article_id'].'.html>'.$post['article_title'].'</a></div>';
		}
}


//最新发表的文章
function show_article_n($id,$num){
		$m=M('Bbs_Article');
		$list = $m->where("post_board_id={$id}")->order('article_date desc')->limit(0,$num)->select();
		foreach($list as $key=>$post){
			echo '<div class="title_bbs"><a href=__MODULE__/article/read/id/'.$post['article_id'].'.html>'.$post['article_title'].'</a></div>';
		}
}



//正值表达式比对解析$_SERVER['HTTP_USER_AGENT']中的字符串 获取访问用户的浏览器的信息
function determinebrowser ($Agent) {
$browseragent="";   //浏览器
$browserversion=""; //浏览器的版本
if (preg_match('/MSIE ([0-9].[0-9]{1,2})/',$Agent,$version)) {
 $browserversion=$version[1];
 $browseragent="Internet Explorer";
} else if (preg_match( '/Opera\/([0-9]{1,2}.[0-9]{1,2})/i',$Agent,$version)) {
 $browserversion=$version[1];
 $browseragent="Opera";
} else if (preg_match( '/Firefox\/([0-9.]{1,5})/',$Agent,$version)) {
 $browserversion=$version[1];
 $browseragent="Firefox";
}else if (preg_match( '/Chrome\/([0-9.]{1,3})/',$Agent,$version)) {
 $browserversion=$version[1];
 $browseragent="Chrome";
}
else if (preg_match( '/UCBrowser\/([0-9.]{1,3})/',$Agent,$version)) {
 $browseragent="UCBrowser";
 $browserversion="";
}
else if (preg_match( '/MQQBrowser\/([0-9.]{1,3})/',$Agent,$version)) {
 $browseragent="MQQBrowser";
 $browserversion="";
}
else {
$browserversion="";
$browseragent="Unknown";
}
return $browseragent." ".$browserversion;
}

// 同理获取访问用户的浏览器的信息
function determineplatform ($Agent) {
$browserplatform='';
if (preg_match('/Windows/',$Agent) && strpos($Agent, '/95/')) {
$browserplatform="Windows 95";
}
elseif (preg_match('/Windows 9x/',$Agent) && strpos($Agent, '/4.90/')) {
$browserplatform="Windows ME";
}
elseif (preg_match('/Windows/',$Agent) && preg_match('/98/',$Agent)) {
$browserplatform="Windows 98";
}
elseif (preg_match('/Windows/',$Agent) && preg_match('/NT 5.0/',$Agent)) {
$browserplatform="Windows 2000";
}
elseif (preg_match('/Windows/',$Agent) && preg_match('/NT 5.1/',$Agent)) {
$browserplatform="Windows XP";
}
elseif (preg_match('/Windows/',$Agent) && preg_match('/NT 6.0/',$Agent)) {
$browserplatform="Windows Vista";
}
elseif (preg_match('/Windows/',$Agent) && preg_match('/NT 10.0/',$Agent)) {
$browserplatform="Windows 10";
}
elseif (preg_match('/Windows/',$Agent) && preg_match('/32/',$Agent)) {
$browserplatform="Windows 32";
}
elseif (preg_match('/Windows/',$Agent) && preg_match('/NT 6.1/',$Agent)) {
$browserplatform="Windows 7";
}
elseif (preg_match('/Windows/',$Agent) && preg_match('/NT/',$Agent)) {
$browserplatform="Windows NT";
}elseif (preg_match('/Mac OS/',$Agent)) {
$browserplatform="Mac OS";
}
elseif (preg_match('/linux/',$Agent)) {
$browserplatform="Linux";
}
elseif (preg_match('/unix/',$Agent)) {
$browserplatform="Unix";
}
elseif (preg_match('/sun/',$Agent) && preg_match('os',$Agent)) {
$browserplatform="SunOS";
}
elseif (preg_match('/ibm/',$Agent) && preg_match('os',$Agent)) {
$browserplatform="IBM OS/2";
}
elseif (preg_match('/Mac/',$Agent) && preg_match('PC',$Agent)) {
$browserplatform="Macintosh";
}
elseif (preg_match('/PowerPC/',$Agent)) {
$browserplatform="PowerPC";
}
elseif (preg_match('/AIX/',$Agent)) {
$browserplatform="AIX";
}
elseif (preg_match('/HPUX/',$Agent)) {
$browserplatform="HPUX";
}
elseif (preg_match('/NetBSD/',$Agent)) {
$browserplatform="NetBSD";
}
elseif (preg_match('/BSD/',$Agent)) {
$browserplatform="BSD";
}
elseif (preg_match('/OSF1/',$Agent)) {
$browserplatform="OSF1";
}
elseif (preg_match('/IRIX/',$Agent)) {
$browserplatform="IRIX";
}
elseif (preg_match('/FreeBSD/',$Agent)) {
$browserplatform="FreeBSD";
}
elseif (preg_match('/Android/',$Agent)) {
$browserplatform="Android";
}
if ($browserplatform=='') {$browserplatform = "Unknown"; }
return $browserplatform;
}

/*
@获取用户的ip//如果是加密的话，此方法则不行
*/
		function getip() {    
// $ip = $_server['remote_addr'];     
 //if (!empty($_server['http_client_ip'])) {        
 // $ip = $_server['http_client_ip'];    
 //} elseif (!empty($_server['http_x_forwarded_for'])) {        
 // $ip = $_server['http_x_forwarded_for'];    
 //}  
 $ip=getenv('REMOTE_ADDR');  
  return $ip;
}
		
		
		
		
/**
 * 通过IP获取城市
 * @param string $ip ip地址
 * @return string 【城市名称】
 */
function get_ip_city($ip)
{
    $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=';
    @$city = file_get_contents($url . $ip);
    $city = str_replace(array('var remote_ip_info = ', '};'), array('', '}'), $city);
    $city = json_decode($city, true);
    if ($city['city']) {
        $location = $city['city'];
    } else {
        $location = $city['province'];
    }
	if($location){
		return $location;
	}else{
		return;
	}
}


/**
 * 数据安全处理函数
 * @param string $str 待过滤字符串
 * @return 
 */	
function get_safe_str($str){
	$str=htmlspecialchars_decode($str,ENT_QUOTES);
	$str=str_replace(array('<','>','\'','"','%','/*'),array('《','》','‘','”','',''),$str);
	$str=mysql_escape_string($str);
	return $str;
}



/**
  * 判断是否是手机
*
* @return  boolean
*/
function  isMobile() {
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	 $mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
	  $is_mobile = false;
	foreach ($mobile_agents as $device) {
		if (stristr($user_agent, $device)) {
			$is_mobile = true;
			break;
		}
	}
	return $is_mobile;
}


/**
 * 截取字符串
 * @param string $str 待截取字符串
 * @param string $start 开始位置
 * @param string $len 截取长长度
 * @param string $add 末尾是否添加字符 
 * @return string 
 */
function sub_str($str,$start=0,$len,$add=0){
	if($add){
		if(mb_strlen($str,'UTF8')>$len){
			return mb_substr($str,$start,$len,'UTF8').$add;
		}else{
			return mb_substr($str,$start,$len,'UTF8');
		}
	}else{
		return mb_substr($str,$start,$len,'UTF8');
	}
}

//传入文件路径。获取文件大小（字节）
function getsize($file_url)   {
	//$s=stat($file_url);
	// $size = $s["size"];
	// 获取远程文件大小
    $f=file_get_contents($file_url);
    $size=strlen($f);
    return $size;
    }


//把字节单位转换层其他单位
function size($bytesize){ //当$bytesize 大于是1024字节时，开始循环，当循环到第4次时跳出；
        $i=0;
        while(abs($bytesize)>=1024){        
        $bytesize=$bytesize/1024;
        $i++;
        if($i==4)break;
        }
        //将Bytes,KB,MB,GB,TB定义成一维数组；
        $units= array("B","KB","MB","GB","TB");
        $newsize=round($bytesize,2);
        return $newsize.$units[$i];

}



//找回密码
include_once("smtp.class.php");
function find_password($email,$token,$username){  
    $smtpserver = "smtp.mxhichina.com"; //SMTP服务器，如：smtp.163.com 
    $smtpserverport = 25; //SMTP服务器端口，一般为25 
    $smtpusermail = "postmaster@icecms.cn"; //SMTP服务器的用户邮箱，如xxx@163.com 
    $smtpuser = "postmaster@icecms.cn"; //SMTP服务器的用户帐号xxx@163.com 
    $smtppass = "mima"; //SMTP服务器的用户密码 
    $smtp = new Smtp($smtpserver, $smtpserverport, true, $smtpuser, $smtppass); //实例化邮件类 
    $emailtype = "HTML"; //信件类型，文本:text；网页：HTML 
    $smtpemailto = $email; //接收邮件方，本例为注册用户的Email 
    $smtpemailfrom = $smtpusermail; //发送邮件方，如xxx@163.com 
    $emailsubject = "ICECMS用户帐号密码找回";//邮件标题 
    //邮件主体内容 
     $emailbody ="亲爱的$user_name<br/>感谢您对我站（ICECMS.cn）的支持<br/>请点击链接重置你的密码。<br/> 
	  <a href=http://".$_SERVER['SERVER_NAME']."/index.php/Home/Home/find_pwdo/token/$token target=_blank> http://".$_SERVER['SERVER_NAME']."/index.php/Home/Home/find_pwdo/token/$token</a><br/> 如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问，该链接24小时内有效。"; 

    //发送邮件 
   $rs = $smtp->sendmail($smtpemailto, $smtpemailfrom, $emailsubject, $emailbody, $emailtype); 
    if($rs==1){ 
           return 1; 
    }else{ 
    	  return 0;		
    } 
} 


//利用HTMLPurifier过滤不安全字符
	function xss_clean($str){
		vendor('library.HTMLPurifier#auto');
		$config = HTMLPurifier_Config::createDefault();   //创建默认配置
		$purifier = new HTMLPurifier($config);   //实例化 并传入默认配置 ($config为空也可)
		$str = $purifier->purify($str); //开始过滤 返回过滤后的字符串
		return $str;
	}


//curl0
		function curl($proxy,$ip,$port,$url,$cookiefile=0,$cookiejar=0,$cookiearr=0,$httpheader=0,$referer="http://baidu.com",$follow=0,$header=1,$post=0){
			$ch = curl_init();
			curl_setopt ( $ch, CURLOPT_NOSIGNAL,true);//开启毫秒超时
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, 20000);//20s超时
			//设定为不验证证书和host。
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_URL,$url) ;
			curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; U; Android 4.4.4; zh-CN; MI 3W Build/KTU84P) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/10.8.0.654 U3/0.8.0 Mobile Safari/534.30');
			curl_setopt($ch, CURLOPT_REFERER, $referer);       //伪装REFERER
			curl_setopt($ch, CURLOPT_HEADER, $header);
			if($httpheader){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
			}
			//curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate,sdch');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt ( $ch, CURLOPT_COOKIESESSION, true );仅仅获取session
			if($follow){
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION,$follow);//跟随重定向
			}	
			if($cookiefile){
				curl_setopt($ch, CURLOPT_COOKIEFILE,$cookiefile);
			}
			foreach($cookiearr as $w){
				curl_setopt($ch,CURLOPT_COOKIE,$w);
			}
			curl_setopt($ch, CURLOPT_POST, 1);
			if($post){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			}else{
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			}
			if($cookiejar){
				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar);
			}
			if($proxy!=0){
				curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
				curl_setopt($ch, CURLOPT_PROXY, $ip); //代理服务器地址
				curl_setopt($ch, CURLOPT_PROXYPORT,$port); //代理服务器端口
				curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
			}
 	 		$result=curl_exec($ch);
			curl_close($ch);
			return $result;
			}


//curl
	function curl1($proxy,$ip,$port,$url,$cookiefile=0,$cookiejar=0,$cookiearr=0,$httpheader=0,$referer="http://baidu.com",$follow=0,$header=0,$post=0,$https=0){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url) ;
			curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; U; Android 4.4.4; zh-CN; MI 3W Build/KTU84P) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/10.8.0.654 U3/0.8.0 Mobile Safari/534.30');
			curl_setopt($ch, CURLOPT_REFERER, $referer);       //伪装REFERER
			curl_setopt($ch, CURLOPT_HEADER, $header);
			if($httpheader){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if($follow){
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION,$follow);//跟随重定向
			}	
			if($cookiefile){
				curl_setopt($ch, CURLOPT_COOKIEFILE,$cookiefile);
			}
			foreach($cookiearr as $w){
				curl_setopt($ch,CURLOPT_COOKIE,$w);
			}
			curl_setopt($ch, CURLOPT_POST, 1);
			if($post){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			}else{
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			}
			if($cookiejar){
				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar);
			}
			curl_setopt($ch, CURLOPT_NOSIGNAL, true); 
			return $ch;//返回句柄
			}



	//并发执行curl,带不同cookie访问同一个网址//用于联通一起沃辅助
function rolling_curl($url,$cookiearr,$httpheader,$referer) { 
		$stime=microtime(true); //开始执行时间
		$ch = curl_init();
		$queue = curl_multi_init(); 
		$i=0;
		foreach ($cookiearr as $cookie) {
		//	echo $cookie['cookie1']."<br />";
			$ch=curl1(0,0,0,$url,0,0,$cookie,$httpheader,$referer,0,1,0,0);
			curl_multi_add_handle($queue, $ch); 
			$map[(string) $ch] = $i++; 
						//		if($i>=3)
					//	break;
		}
		$responses = array(); 
		$active = null;
		do { 
			while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ; 
			// a request was just completed -- find out which one 
			while ($done = curl_multi_info_read($queue)) { 
				// get the info and content returned on the request 
				$info = curl_getinfo($done['handle']); 
				$error = curl_error($done['handle']); 
				$results =curl_multi_getcontent($done['handle']); 
				$responses[$map[(string) $done['handle']]] = compact('info', 'error', 'results'); 
				// remove the curl handle that just completed 
				curl_multi_remove_handle($queue, $done['handle']); 
				curl_close($done['handle']); 
			} 
			// Block for data in / output; error handling is done by curl_multi_exec 
			if ($active > 0) { 
				curl_multi_select($queue,3); 
			}
		} while (($active)&&($code == CURLM_OK)); 
		curl_multi_close($queue);
		$etime=microtime(true);//获取程序执行结束的时间
		echo $total=$etime-$stime;   //计算差值
		//print_r($responses);
		return $responses; //返回执行数组
}




//新curl函数
function Ncurl($url,$cookiefile=0,$cookiejar=0,$httpheader=0,$referer="http://baidu.com",$follow=0,$header=1,$postdata=0,$cookiearr=ARRAY(),$proxy=0,$outtime=2000,$UA=0){
		$ch = curl_init();
		curl_setopt ( $ch, CURLOPT_NOSIGNAL,true);//开启毫秒超时
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $outtime);//10s超时
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
		//读取cookie
	//	foreach($cookiearr as $c){
	//		curl_setopt($ch,CURLOPT_COOKIE,$c);
	//	}
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



//用于酷狗音乐搜索
function rolling_curl1($url,&$data,$httpheader,$referer,$sign) { 
		$stime=microtime(true); //开始执行时间
		$ch = curl_init();
		$queue = curl_multi_init(); 
		$i=0;
		foreach ($url as $key=>$urls) {
			$ch=curl1(0,0,0,$urls,0,0,0,$httpheader,$referer,0,0,0,0);
			curl_multi_add_handle($queue, $ch); 
			$map[(string) $ch] = $key; 
		}
		$active = null;
		do { 
			while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ; 
			// a request was just completed -- find out which one 
			while ($done = curl_multi_info_read($queue)) { 
				// get the info and content returned on the request 
				$results =curl_multi_getcontent($done['handle']); 
				$results=json_decode($results,1);
				if($sign==4)
					$data[$map[(string) $done['handle']]]['m4aurl'] = $results['url'];
				else
					$data[$map[(string) $done['handle']]]['mp3url'] = $results['url'];
				// remove the curl handle that just completed 
				curl_multi_remove_handle($queue, $done['handle']); 
				curl_close($done['handle']); 
			} 
			// Block for data in / output; error handling is done by curl_multi_exec 
			if ($active > 0) { 
				curl_multi_select($queue,0.5); 
			}
		} while (($active)&&($code == CURLM_OK)); 
		curl_multi_close($queue);
		$etime=microtime(true);//获取程序执行结束的时间
		$total=$etime-$stime;   //计算差值
		//return $responses; //返回执行数组
}
//记录用户日志
function traceHttp(){
    $content = "## 来访时间： " . date('Y-m-d H:i:s') . "\n## 访问来源： " . $_SERVER["REMOTE_ADDR"] . "\n## 请求地址/字段： ".__ROOT__. __SELF__."\n## 真实ip：" . $_SERVER["REMOTE_ADDR"] . "\n\n"; 
        $max_size = 100000;
        $log_filename = "./log.md";
        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){
            unlink($log_filename);
        }
        file_put_contents($log_filename, $content, FILE_APPEND);
}
include_once 'ubb_fun.php';
?>
