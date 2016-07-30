<?php
		
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
 * 判断是不是手机访问
 * @return true 
 */
function isMobile() {
	//在此加入宣传增加vip 时长
	
	
	// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
	if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
		return true;
	}
	//如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
	if (isset ($_SERVER['HTTP_VIA'])) {
		//找不到为flase,否则为true
		return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
	}
	//脑残法，判断手机发送的客户端标志,兼容性有待提高
	if (isset ($_SERVER['HTTP_USER_AGENT'])) {
		$clientkeywords = array ('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
		// 从HTTP_USER_AGENT中查找手机浏览器的关键字
		if (preg_match("/(" . implode('|', $clientkeywords) . ")/i",strtolower($_SERVER['HTTP_USER_AGENT']))) {
			return true;
		}
	}
	 //协议法，因为有可能不准确，放到最后判断
	if (isset ($_SERVER['HTTP_ACCEPT'])) {
	// 如果只支持wml并且不支持html那一定是移动设备
	// 如果支持wml和html但是wml在html之前则是移动设备
		if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
			return true;
		}
	}
	return false;
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
function getsize($file_name)   {
	$s=stat(dirname(dirname(dirname(dirname(dirname((__FILE__)))))).$file_name);
        $size = $s["size"];
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

function get_zip_originalsize($filename, $path) {
	 //先判断待解压的文件是否存在
	 if(!file_exists($filename)){
 		 die("文件 $filename 不存在！,或许官网网络延迟或者你的空间不支持file_get_contents()函数");
	 } 
	 $starttime = explode(' ',microtime()); //解压开始的时间
	 //将文件名和路径转成windows系统默认的gb2312编码，否则将会读取不到
	 $filename = iconv("utf-8","gb2312",$filename);
	 $path = iconv("utf-8","gb2312",$path);
	 //打开压缩包
	 $resource = zip_open($filename);
	 $i = 1;
	 //遍历读取压缩包里面的一个个文件
	 while ($dir_resource = zip_read($resource)) {
 		 //如果能打开则继续
 		 if (zip_entry_open($resource,$dir_resource)) {
 			  //获取当前项目的名称,即压缩包里面当前对应的文件名
   			$file_name = $path.zip_entry_name($dir_resource);
   			//以最后一个“/”分割,再用字符串截取出路径部分
   			$file_path = substr($file_name,0,strrpos($file_name, "/"));
   			//如果路径不存在，则创建一个目录，true表示可以创建多级目录
   			if(!is_dir($file_path)){
   				 mkdir($file_path,0777,true);
  		 	}
  		 	//如果不是目录，则写入文件
   			if(!is_dir($file_name)){
   				 //读取这个文件
   				 $file_size = zip_entry_filesize($dir_resource);
   				 //最大读取6M，如果文件过大，跳过解压，继续下一个
  				  if($file_size<(1024*1024*6)){
  					   $file_content = zip_entry_read($dir_resource,$file_size);
    					 file_put_contents($file_name,$file_content);
    				}else{
   					  echo "<p> ".$i++." 此文件已被跳过，原因：文件过大， -> ".iconv("gb2312","utf-8",$file_name)." </p>";
  			 	 }	
   			}
 	  	//关闭当前
	   	zip_entry_close($dir_resource);
	  }
	 }
	 //关闭压缩包
	 zip_close($resource); 
	 $endtime = explode(' ',microtime()); //解压结束的时间
	 $thistime = $endtime[0]+$endtime[1]-($starttime[0]+$starttime[1]);
	 $thistime = round($thistime,3); //保留3为小数
	 echo "<p>升级完毕完毕！，本次升级花费：$thistime 秒。有问题可以去官网反馈哦</p>";
}
?>
