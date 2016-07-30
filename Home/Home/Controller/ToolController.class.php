<?php
namespace Home\Controller;
use Think\Controller;
class ToolController extends PublicController {	
	private $user_sid='';
	private $user_name='';
	private $user_id='';
	private $status_arr=array();
	private $user_arr=array();
	public function __construct(){
		parent::__construct();
		$this->ddos();
		$this->temp();
		$m=M('Website');
		$this->status_arr=$m->where('status_id=1')->select();
		$this->assign('key',$this->status_arr[0]['status_key']);//分配网站关键字
		$this->assign('description',$this->status_arr[0]['status_describe']);//分配网站描述
		if(isset($_COOKIE['user_sid'])){
			$this->user_sid=htmlspecialchars($_COOKIE['user_sid']);
			$m=M('User');
			$count=$m->where("user_sid='$this->user_sid'")->count();
			if($count>0){
				$this->user_arr=$m->where("user_sid='$this->user_sid'")->select();
				$this->user_name=$this->user_arr[0]['user_name'];
				$this->user_id=$this->user_arr[0]['user_id'];
			}else{	
				//$this->error('请先登录',U('Login/index'),3);
				//eixt();
				$this->user_sid='';
			}
		}else{
			$this->user_sid='';
			$this->user_name='游客';
			$this->user_id='';
		}
		//用户日志记录
		traceHttp();
	}
	public function index(){
		//$this->display();
	}


	//酷狗音乐搜索
	public function kugoumusic(){
		$this->display();
	}	
	//搜索音乐
	public function searchkugoumusic(){
		$word=I('get.word','我擦，你干嘛','htmlspecialchars');
		if((!isset($_GET['pages']))||($_GET['pages']<=0)){
			$pages=1;
		}else{
			$pages=(int)I('get.pages','','htmlspecialchars');
		}	
		$w = urlencode($word);
		$url='http://mobilecdn.kugou.com/api/v3/search/song?iscorrect=1&pagesize=20&plat=0&page='.$pages.'&keyword='.$w.'&sver=3&showtype=14&version=7612&with_res_tag=1';
		$ret=file_get_contents($url);
		$ret=preg_replace('/<!--.*?-->/', '', $ret);
		$arr=json_decode($ret,true);
		//dump($arr["data"]['info']);
		$i=0;
		foreach ($arr["data"]['info'] as $m){
			$m_arr[$i]['music_name']=$m['filename'];//歌曲名称
			$m_arr[$i]['singername']=$m["singername"];
			//m4a###########################################################
			$m_arr[$i]['m4asizw']=$m["m4afilesize"];
			$m4aurl[$i]="http://m.kugou.com/app/i/getSongInfo.php?hash={$m["hash"]}&cmd=playInfo";
			//$m4aurl=file_get_contents("http://m.kugou.com/app/i/getSongInfo.php?hash={$m["hash"]}&cmd=playInfo");
			//$m4aurl=json_decode($m4aurl,1);
			//$m_arr[$i]['m4aurl']=$m4aurl['url'];
			//mp3##########################################################
			$m_arr[$i]['mp3size']=$m["320filesize"];
			$key=md5($m["320hash"].'kgcloud');
			$mp3url[$i]="http://trackercdn.kugou.com/?key=$key&cmd=4&acceptmp3=1&hash={$m["320hash"]}&pid=1";
			//$mp3url=file_get_contents("http://trackercdn.kugou.com/?key=$key&cmd=4&acceptmp3=1&hash={$m["320hash"]}&pid=1");
			//$mp3url=json_decode($mp3url,1);
			//$m_arr[$i]['mp3url']=$mp3url['url'];
			$i++;
		}
		rolling_curl1($m4aurl,$m_arr,$httpheader,$referer,4);
		rolling_curl1($mp3url,$m_arr,$httpheader,$referer,3);
		$this->assign('word',$word);
		$this->assign('pages',$pages);
		//$this->assign('count',$count);
		$this->assign('m_arr',$m_arr);
		$this->display();
	}

	public function wo_submit(){
		exit("本站已经不提供此服务");
		ob_clean();
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),3);
			eixt();
		}
		//带访问登录页面获取cookie
		$contents=Ncurl("http://17wo.cn/Login.action",0,0,0,0,0,1,0,0);//返回cookie
	//	preg_match_all('/Set-Cookie: (.*);/isU',$contents[1],$array);
		preg_match_all('/sessionid=(.*) /uisU',$contents[1],$sessionids);
		preg_match_all('/JSESSIONID=(.*);/uisU',$contents[1],$sessionidss);
 		$c="JSESSIONID=".$sessionidss[1][0].";sessionid=".$sessionidss[1][0];
	//	foreach($array[1] as $d){
 		//	$c=$c.$d.'; ';
	//	}
	//	echo $contents[1];
//echo $c;
		$httpheader = array(
			'Accept:image/png,image/*;q=0.8,*/*;q=0.5',
			'Referer:http://wap.17wo.cn/Login.action',
			'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36',
			'Cookie: CNZZDATA3082302=cnzz_eid%3D1568494461-1458558927-%26ntime%3D1462275916; Hm_lvt_23e65108439f66359e34e47937449f75=1460176745,1461480413,1462273276; JSESSIONID='.$sessionidss[1][0].'; myGrowUp=2016-5-31; sessionid='.$sessionidss[1][0],
			
		);
		$tourl="http://wap.17wo.cn/captcha.do";
		$content=Ncurl($tourl,0,0,$httpheader,0,0,1,0,$c);//获取验证码返回cookie
	//	print_r($content[0]);
		$header = substr($content[1], 0, $content[0]['header_size']);//分离响应头和body
		$body = substr($content[1], $content[0]['header_size']);
		$filename=time().".png";
		$fp2=@fopen("./tempimg/".$filename,'a');
		fwrite($fp2,$body);
		fclose($fp2);
	//	echo $header;
	//	preg_match_all('/sessionid=(.*) /uisU',$header,$sessionid);
		//print_r($sessionid);
		$cookie=$sessionidss[1][0];
		//exit();
		$cookie="sessionid=".$cookie;
		$this->assign('cookie',$cookie);
		$this->assign('filename',$filename);
		$this->display();
	}




	//联通一起沃签到
	public function wo(){
		exit("本站已经不提供此服务");
		$mes='';
		if($this->user_sid==''){
			$arrs=array();
			$mes="请先<a href=/index.php/Home/Login/index.html>登录</a>才能添加账号<br />";
		}else{
		$a=rand(192,223);
		$b=rand(0,255);
		$c=rand(0,255);
		$d=rand(1,254);
		$httpheader = array(
			'CLIENT-IP:'.$a.'.'.$b.'.'.$c.'.'.$d,
			'X-FORWARDED-FOR:'.$a.'.'.$b.'.'.$c.'.'.$d,
			'Host:wap.17wo.cn',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
			'Connection:keep-alive',
			'Cache-Control: max-age=0',
		);
		$m=M('Tool_wo');
		$data['user_id']=$this->user_id;
		$arr=$m->where($data)->select();
		$number=$m->where($data)->count();
		$arrs=array();
		for($i=1;$i<=$number;$i++){
			$id=$arr[$i-1]['id'];
			$phone=$arr[$i-1]['mobile'];
			$cookiearr['cookie']=$arr[$i-1]['cookie'];
			$cookiearr['cookie1']=$arr[$i-1]['cookie1'];
			// 当前流量
			$tourl="http://wap.17wo.cn/MyZone!rich.action";
			$content=curl(0,$ip,$port,$tourl,0,0,$cookiearr,$httpheader,$referer,0,1,0);
			preg_match('/流量\<\/label\>(.*)\<\/a/isU',$content,$inarr);
			if($inarr[1]=='')
				$liu="出现错误";
			else
				$liu=$inarr[1];
			//短信条数
			preg_match('/短信\<\/label\>(.*)\<\/a/isU',$content,$inarr3);
			$duan=$inarr3[1];
			//沃币
			preg_match('/沃币\<\/label\>(.*)\<\/a/isU',$content,$inarr4);
			$bi=$inarr4[1];
			//沃豆
			preg_match('/沃豆\<\/label\>(.*)\<\/a/isU',$content,$inarr5);
			$dou=$inarr5[1];
			//当前积分
			$tourl="http://wap.17wo.cn/UserCenterGrowup.action?aId=117";
			$content=curl(0,$ip,$port,$tourl,0,0,$cookiearr,$httpheader,$referer,0,1,0);
			preg_match('/er="(.*)"/isU',$content,$inarr2);
			$jifen=$inarr2[1];
			$arrs[$i-1]['mobile']=$phone;
			$arrs[$i-1]['jifen']=$jifen;
			$arrs[$i-1]['liu']=$liu;
			$arrs[$i-1]['dou']=$dou;
			$arrs[$i-1]['bi']=$bi;
			$arrs[$i-1]['duan']=$duan;
			$arrs[$i-1]['id']=$id;
		}
		}		
		$this->assign('arrs',$arrs);
		$this->assign('mes',$mes);
		$this->display();
	}



	public function wo_do(){
		exit("本站已经不提供此服务");
		$a=rand(192,223);
		$b=rand(0,255);
		$c=rand(0,255);
		$d=rand(1,254);
		$httpheader = array(
			'CLIENT-IP:'.$a.'.'.$b.'.'.$c.'.'.$d,
			'X-FORWARDED-FOR:'.$a.'.'.$b.'.'.$c.'.'.$d,
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
			'Connection:keep-alive',
			'Cache-Control: max-age=0',
		);



			
		
		
			if(isset($_GET['exec'])||isset($_GET['e'])){
				$m=M('Tool_wo');
				$w=$_GET['where'];//从第几部分开始(0-3)//分流执行
				if($w=='')
					exit("error");
				$number=$m->count();
				$p=($number/4)+1;
				$from=$w*$p;
				$arr=$m->limit($from,$p)->select();//每次只选择一部分用户执行，防止用户过多卡死
				//dump($arr);
				//exit();
				for($i=1;$i<=$number;$i++){
					//获取cookie
					$cookiearr[$i-1]['cookie']=$arr[$i-1]['cookie'];
					$cookiearr[$i-1]['cookie1']=$arr[$i-1]['cookie1'];
				}	
				//访问首页获取时间
				$tourl="http://wap.17wo.cn/Index.action";
				$content=curl(0,0,0,$tourl,0,0,$cookiearr[0],$httpheader,$referer,0,1,0);
				preg_match('/timestamp: (.*),/isU',$content,$inarr);
				$time=$inarr[1];


				if(($_GET['exec']=='down')||($_GET['e']=='down')){
					//自动下载获取沃豆
					$tourl="http://wap.17wo.cn/RandomLook.action";
					$content=curl(0,0,0,$tourl,0,0,$cookiearr,$httpheader,$tourl,0,1,0);
					preg_match('/Location: (.*)\r/isU',$content,$inarr);//url
			 		$tourl=$inarr[1];
					$inarr=substr($tourl,38,strlen($tourl)-1); 
					$url="http://wap.17wo.cn/NewDownload.action?cpd={$inarr}&subjectId=0";	
					$content=curl(0,0,0,$url,0,0,$cookiearr,$httpheader,$tourl,0,1,0);
					preg_match('/Location: (.*)\r/isU',$content,$inarr);//url
					$tourl=$inarr[1];
					//高并发下载
					rolling_curl($url,$cookiearr,$httpheader,$tourl);
					//curl(0,$ip,$port,$tourl,0,0,$cookiearr,$httpheader,$referer,0,1,0);
					echo "下载";
				}


				//登录奖励
				if(($_GET['exec']=='denglu')||($_GET['e']=='denglu')){
					$referer="http://wap.17wo.cn/UserCenterGrowup.action?aId=117";//伪造来源
					$url="http://wap.17wo.cn/UserCenterGrowup!gainTaskAwards.action?aId=117&taskId=28&_={$time}";
					 rolling_curl($url,$cookiearr,$httpheader,$referer);
					//$contents3=curl(0,$ip,$port,$url,0,0,$cookiearr,$httpheader,$referer,0,1,0);
					echo '登录';
				}


				//签到
				if(($_GET['exec']=='qiandao')||($_GET['e']=='qiandao')){
					$referer="http://wap.17wo.cn/SignIn!sign.action?aId=211";//伪造来源
					$url="http://wap.17wo.cn/SignIn!checkin.action?checkIn=true&_={$time}";
					$content=rolling_curl($url,$cookiearr,$httpheader,$referer);
					//$contents4=curl(0,$ip,$port,$url,$cookie2,0,$cookiearr,$httpheader,$referer,0,1,0);//签到
					$referer="http://wap.17wo.cn/SignIn!sign.action?aId=117";//伪造来源
					$url="http://wap.17wo.cn/UserCenterGrowup!gainTaskAwards.action?aId=117&taskId=29&_={$time}";//领取奖励
					$content=rolling_curl($url,$cookiearr,$httpheader,$referer);
					//print_r($content);
					//$contents4=curl(0,$ip,$port,$url,$cookie2,0,$cookiearr,$httpheader,$referer,0,1,0);
					echo "签到";
				}

				//抽奖红包
				if(($_GET['exec']=='chouqu')||($_GET['e']=='chouqu')){
					$referer="http://wap.17wo.cn/FlowRedPacket.action";//伪造来源
					$url="http://wap.17wo.cn/FlowRedPacket!LuckDraw.action?pageName=&_={$time}";
					rolling_curl($url,$cookiearr,$httpheader,$referer);
					//$contents4=curl(0,$ip,$port,$url,$cookie2,0,$cookiearr,$httpheader,$referer,0,1,0);//抽取流量红包
					echo "红包";
				}


				//伪分享获赠5M//同时抽取签到7天的大礼包
				if(($_GET['exec']=='fenxiang')||($_GET['e']=='fenxiang')){
					$referer="http://wap.17wo.cn/FlowRedPacket.action";//伪造来源
					$url="http://wap.17wo.cn/FlowRedPacket!share.action?sendid=&sharecontent=undefined&subjectId=0&cpd=&_={$time}";
					 rolling_curl($url,$cookiearr,$httpheader,$referer);
					//$contents4=curl(0,$ip,$port,$url,$cookie2,0,$cookiearr,$httpheader,$referer,0,1,0);//抽取流量红包
					echo '分享';

					$referer="http://wap.17wo.cn/SignIn!signTurntable.action?aId=209";//伪造来源
					$url="http://wap.17wo.cn/SignIn!getTurnAwardLuckDraw.action";
					 rolling_curl($url,$cookiearr,$httpheader,$referer);
					//$contents4=curl(0,$ip,$port,$url,$cookie2,0,$cookiearr,$httpheader,$referer,0,1,0);//抽取流量红包
					echo '抽取签到7天的大礼包';
				}

				//摇杆抽奖
				if(($_GET['exec']=='yao')||($_GET['e']=='yao')){
					$a=3;
					$referer="http://wap.17wo.cn/FlowRedPacket.action?pageName=earnflow";//伪造来源
					$url="http://wap.17wo.cn/FlowRedPacket!LuckDraw.action?pageName=earnflow&_={$time}";
					while($a){
						 rolling_curl($url,$cookiearr,$httpheader,$referer);
						//$contents4=curl(0,$ip,$port,$url,$cookie2,0,$cookiearr,$httpheader,$referer,0,1,0);//抽取流量红包
						$a--;
					}
					echo "摇杆";
				}

				
				//http://wap.17wo.cn/SilentUser.action
				if(($_GET['exec']=='baoxiang')||($_GET['e']=='baoxiang')){
					$a=3;
					$referer="http://wap.17wo.cn/SilentUser.action";//伪造来源
					$url="http://wap.17wo.cn/SilentUser!receive.action?aId=41&qd=12030&_={$time}";
					while($a){
						 rolling_curl($url,$cookiearr,$httpheader,$referer);
						//$contents4=curl(0,$ip,$port,$url,$cookie2,0,$cookiearr,$httpheader,$referer,0,1,0);//抽取流量红包
						$a--;
					}
					echo "打开宝箱";
				}
			 
				/*有短信频繁提醒，麻烦
				//http://wap.17wo.cn/NewGrowupCard!gainCard.action?fd=0&_=
				//http://wap.17wo.cn/NewGrowupCard!gainFlow.action?fd=0&_=1458639092139
				//http://wap.17wo.cn/NewGrowupCard!gainIntegral.action?fd=0&_=1458638268112
				if(($_GET['exec']=='cheng')||($_GET['e']=='cheng')){
					$a=3;
					$referer="http://wap.17wo.cn/NewGrowupCard.action?aId=30";//伪造来源
					$url="http://wap.17wo.cn/NewGrowupCard!gainIntegral.action?fd=1&_={$time}";
					while($a){
						 //rolling_curl($url,$cookiearr,$httpheader,$referer);
						//$contents4=curl(0,$ip,$port,$url,$cookie2,0,$cookiearr,$httpheader,$referer,0,1,0);//抽取流量红包
						$a--;
					}
					echo "wo成长卡沃豆";
				}
				if(($_GET['exec']=='chengwodou')||($_GET['e']=='chengwodou')){
					$a=3;
					$referer="http://wap.17wo.cn/NewGrowupCard.action?aId=30";//伪造来源
					$url="http://wap.17wo.cn/NewGrowupCard!gainFlow.action?fd=1&_={$time}";
					while($a){
						 //rolling_curl($url,$cookiearr,$httpheader,$referer);
						//$contents4=curl(0,$ip,$port,$url,$cookie2,0,$cookiearr,$httpheader,$referer,0,1,0);//抽取流量红包
						$a--;
					}
					echo "wo成长卡流量";
				}
				if(($_GET['exec']=='chengka')||($_GET['e']=='chengka')){
					$a=3;
					$referer="http://wap.17wo.cn/NewGrowupCard.action?aId=30";//伪造来源
					$url="http://wap.17wo.cn/NewGrowupCard!gainCard.action?fd=1&_={$time}";
					while($a){
						 //rolling_curl($url,$cookiearr,$httpheader,$referer);
						//$contents4=curl(0,$ip,$port,$url,$cookie2,0,$cookiearr,$httpheader,$referer,0,1,0);//抽取流量红包
						$a--;
					}
					echo "wo成长卡";
				}*/

			//失效了
			//http://wap.17wo.cn/VideoIndex!luckDraw.action?_=
		/*	if(($_GET['exec']=='guaka')||($_GET['e']=='guaka')){
					$a=3;
					$referer="http://wap.17wo.cn/LetvActivity!showLsEntrance.action";//伪造来源
					$url="http://wap.17wo.cn/VideoIndex!luckDraw.action?_={$time}";
					while($a){
						 rolling_curl($url,$cookiearr,$httpheader,$referer);
						$a--;
					}
					echo "刮卡";
				}
			*/

					// 抽奖活动链接
				if(($_GET['exec']=='guaka')||($_GET['e']=='guaka')){
					$a=3;
					$referer="/http://17wo.cn/Sina.action?c=sinaweibo";//伪造来源
					$url="http://17wo.cn/Sina!luckDraw.action?_={$time}";
					while($a){
						 rolling_curl($url,$cookiearr,$httpheader,$referer);
						//$contents4=curl(0,$ip,$port,$url,$cookie2,0,$cookiearr,$httpheader,$referer,0,1,0);//抽取流量红包
						$a--;
					}
					echo "sina";
				}
				//http://17wo.cn/Sina.action?c=sinaweibo  抽奖活动链接  可以搞上去 自动抽奖 中奖率高 http://17wo.cn/Sina!luckDraw.action?_=

		}
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),3);
			eixt();
		}


		//删除手机号码
		$id=I('get.id','','htmlspecialchars');
		if($id!=''){
			$m=M('Tool_wo');
			$data['id']=$id;
			$data['user_id']=$this->user_id;
			$m->where($data)->delete();
			$this->success("删除成功");
			exit();
		}

		//增加手机号码
		$mobile=I('post.mobile','','htmlspecialchars');
		$pass=I('post.pass','','htmlspecialchars');
		$check=I('post.check','','htmlspecialchars');
		if(isset($_POST['pass']))
		if(($pass!='')&&($mobile!='')){
			$post_fields=array();
			$post_fields['backurl']='';
			$post_fields['backurl2']='';
			$post_fields['chk']='';
			$post_fields['loginType']='0';
			$post_fields['chkType']='on';
			$post_fields['password']=$pass;
			$post_fields['mobile']=$mobile;
			$post_fields['captchaCode']=$check;
			$fields_string = '';    
			foreach($post_fields as $key => $value){
          			  $fields_string .= $key . '=' . $value . '&';
			}    
			$post_fields = rtrim($fields_string , '&');
			$cookie[0]=$_POST['cookie'];
			$referer="http://wap.17wo.cn/Login!process.action";//伪造来源
			$url="http://wap.17wo.cn/Login!process.action" ;
			$content=curl(0,$ip,$port,$url,0,$cookie2,$cookie,$httpheader,$referer,0,1,$post_fields);
			$sign=preg_match('/验证码错误/',$content);
		//	$fp = @fopen("Log1.html", "w"); //记录捕获到的页面源码
		//	fwrite($fp,$content);
		//	fclose($fp);
		//	echo $content;
		//	exit();
			if($sign){
				echo "验证码错误";
				exit();
			}
			$sign=preg_match('/密码错误!/',$content);
			if($sign){
				echo "手机号/用户名或密码错误!";
				exit();
			}
			preg_match_all('/sessionid=(.*);/uisU',$content,$sessionid);
			preg_match_all('/cuid=(.*);/uisU',$content,$cuid);
			$data['cookie']="sessionid=".$sessionid[1][0];
			if($cuid[1][0]==''){
				print_r($cuid);
				$this->error('未知错误,请联系管理员,谢谢');
				exit();
			}
		 	$data['cookie1']='cuid='.$cuid[1][0].'; Version=1; Max-Age=2592000000000; Expires=Wed, 17-Feb-2017 10:26:58 GMT; Path=/';
			$m=M('Tool_wo');
			$data['user_id']=$this->user_id;
			$numbers=$m->where($data)->count();
			if($numbers>=3){
				$this->error('添加失败，一个用户只能添加三个账户');
				exit();
			}
			$data['mobile']=$mobile;
			$data['pass']='NULL';
			$m->add($data);
			$this->success('添加成功');
		}else{
			echo '请不要留空';
		}
		$cuid=I('post.cuid','','htmlspecialchars');
		//$sessionid=I('post.sessionid','','htmlspecialchars');
		if(isset($_POST['cuid']))
		if(($cuid!='')&&($mobile!='')){
			$data['cookie']= "NULL";//"sessionid=".$sessionid;
			$data['cookie1']= 'cuid="'.$cuid.'"; Version=1; Max-Age=259200000000; Expires=Wed, 17-Feb-2017 10:26:58 GMT; Path=/';
			$m=M('Tool_wo');
			$data['user_id']=$this->user_id;
			$numbers=$m->where($data)->count();
			if($numbers>=3){
				$this->error('添加失败，一个用户只能添加三个账户');
				exit();
			}
			$data['mobile']=$mobile;
			$m->add($data);
			$this->success('添加成功');
		}
	}






	//代理ip
	public function getip(){
	if(isset($_GET['sort'])){
		if($_GET['sort']=='china_high')
			$url="http://ip.izmoney.com/search/china/high/index.html";//中国高匿
		else if($_GET['sort']=='foreign_high')
			$url="http://ip.izmoney.com/search/foreign/high/index.html";//国外高匿
		else if($_GET['sort']=='foreign_normal')
			$url="http://ip.izmoney.com/search/foreign/normal/index.html";//国外透明
		else if($_GET['sort']=='china_normal')
			$url="http://ip.izmoney.com/search/china/normal/index.html";//国内透明
		else{
			echo "error";
			exit();
		}
	}else{
		$url="http://ip.izmoney.com/search/china/high/index.html";//中国高匿
	}
	$result=Ncurl($url,0,0,$httpheader,$referer="http://baidu.com",0,1,0);
	$content=$result['1'];
	//匹配最后更新时间
	preg_match_all('/统计数据于(.*)更新/uisU',$content,$time);
	$updatetime=$time[0][0];
	//匹配ip
	preg_match_all('/\<tr class(.*)\<\/tr/uisU',$content,$content);
	//count($content[0]);
	$i=0;
	$code[0]='A';
	$code[1]='B';
	$code[2]='C';
	$code[3]='D';
	$code[4]='E';
	$code[5]='F';
	$code[6]='G';
	$code[7]='H';
	$code[8]='I';
	$code[9]='Z';
	foreach($content[0] as $content){
		preg_match_all('/;\'\>(.*)\</uisU',$content,$str);
		$ip='';
		foreach($str[1] as $pip){
			$ip=$ip.$pip;
		}
		$data[$i]['ip']=$ip;//IP
		preg_match_all('/l"\>(.*)\</uisU',$content,$str);
		$data[$i]['country']=$str[1][0];
		$data[$i]['province']=$str[1][1];
		$data[$i]['city']=$str[1][2];
		$data[$i]['Operator']=$str[1][3];
		$data[$i]['degree']=$str[1][4];
		$data[$i]['type']=$str[1][5];
		preg_match_all('/td\>(.*)\</uisU',$content,$str);
		//速度
		$data[$i]['speed']=$str[1][10];
		//最近检查时间
		$data[$i]['checktime']=$str[1][12];
		preg_match_all('/port (.*)"\>/uisU',$content,$str);
			$arr=str_split($str[1][0]);
		$port='';
		foreach($arr as $datas){
			foreach ($code as $key=>$d){
				if($datas==$d)
					$port=$port.$key;
			}
		}
		$port=$port>>3;
	 	$data[$i]['port']=$port;
		$i++;
	}
		$this->assign("data",$data);
		$this->assign("updatetime",$updatetime);
		$this->display();
	}

	//酷我音乐
	public function kuwomusic(){
		$this->display();
	}

	public function kuwosearchid(){
		if((!isset($_GET['url']))||(!isset($_GET['id']))){
			$this->error("请勿非法操作");
			exit();
		}
		if(isset($_GET['url']))
			$m = explode('MUSIC_', @$_GET['url']);
		else
			$id = @$m['1'];
		if ($id == false) {
			$id = @$_GET['url'];
		}
		$music = "http://player.kuwo.cn/webmusic/st/getNewMuiseByRid?rid=MUSIC_{$id}";
		$ch = curl_init($music);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// 获取数据返回
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		// 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		$con = curl_exec($ch);
		preg_match('/<mp3path>(.*)<\/mp3path>/', $con, $result_1);
		preg_match('/<mp3dl>(.*)<\/mp3dl>/', $con, $result_2);
		$result_url = "http://{$result_2['1']}/resource/{$result_1['1']}";
		if ($result_1) {
			/*
			* textarea禁止编辑 disabled=\"disabled\"
			 */
		$this->assign('url',$result_url);
		//echo" <div class=\"kw\"><textarea  >$result_url</textarea><a href = \"{$result_url}\" class=\"but\">下载</a></div>";	
		} else {
			//echo '<div class="error"><font color=red>转换失败，请检查你的网址</font></div>';
		$this->error("转换失败，请检查你的网址");
		}
	}


	public function kuwosearchname(){
		if(!isset($_GET['word'])){
			$this->error("请勿非法操作");
			exit();
		}
		$word = @$_GET['word'];
		$pn = @$_GET['pn'] ? @$_GET['pn'] : 1;
		$music = "http://sou.kuwo.cn/ws/NSearch?type=music&key={$word}&pn={$pn}";
		$ch = curl_init($music);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		$con = curl_exec($ch);
		preg_match_all('/<p class="m_name">
                                        <a href="http:\/\/www.kuwo.cn\/yinyue\/(.*)\/" title="(.*)" target="_blank">/U', $con, $result);
		preg_match_all('/&pn=(.*)">尾页<\/a>/U', $con, $endPage);
		preg_match_all('/&pn=(.*)">首页<\/a>/U', $con, $homePage);
		$num = count($result['1']) - 1;
		if ($result['2']){
  
			$this->assign('arr',$result);
			$this->assign('pages',$pn);
			if (@$endPage[1][0] > 1) {
				$this->assign('count',$endPage[1][0]);
			}
			$this->assign('word',$word);
		}else{
			$this->error("error");
		}
	}

}
