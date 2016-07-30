<?php
namespace Home\Controller;
use Think\Controller;
class BbsController extends IndexController {
	private $user_sid='';
	private $user_name='';
	private $user_id='';
	private $status_arr=array();
	private $user_arr=array();
	public function __construct(){
		parent::__construct();
		$m=M('Website');
		$this->temp();
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
				$this->error('请先登录',U('Login/index'),3);
				eixt();
			}
		}else{
			$this->user_sid='';
			$this->user_name='游客';
			$this->user_id='';
		}
		//用户日志记录
		//	traceHttp();
      
	}



	public function index(){
		$m=M('Bbs_board');
		$board_number=$m->count();//版块总数
		$b_arr=$m->select();
		$m=M('Bbs_post');
		for($i=1;$i<=$board_number;$i++){
			$id=$b_arr[$i-1]['bbs_board_id'];
			$arr=$m->where("post_board_id='$id'")->order('post_respond_time desc')->limit(3)->select();
			//$count=$m->where("post_board_id='$id'")->order('post_respond_time desc')->count();
			$this->assign("post_list_$id",$arr);
			//$this->assign("count_$id",$count);
		}
	
		$this->assign('user_name',$this->user_name);//分配昵称
		$this->assign('user_id',$this->user_id);//分配id
		$this->assign('day',date('Y-m-d H:i:s'));//分配时间
		$this->assign('website_foot',$this->status_arr[0]['website_foot']);
		$this->display();
	}


	public function read(){
		if(!isset($_GET['id'])){
			$this->error('非法访问!');
			exit();
		}
		$this->setToken();//设置Crsf_Token
		$post_id=(int)I('get.id','','htmlspecialchars');
		$m=M('Bbs_post');
		$data['post_id']=$post_id;
		$count=$m->where($data)->count();
		$post_arr=$m->where($data)->select();
		if(!$count){
			$this->error('帖子不存在!');
			exit();
		}
		//通过id找出版块名字
		$b=M('Bbs_board');
		$datas['bbs_board_id']=$post_arr[0]['post_board_id'];
		$board_name=$b->where($datas)->getField('bbs_board_name');
		$u=M('User');
		$u_id=$post_arr[0]['post_ower_id'];
		$u_arr=$u->where("user_id='$u_id'")->select();
		//通过id找出用户信息
		$u_name=$u_arr[0]['user_name'];
		$u_rank=$u_arr[0]['user_rank'];
		$u_character=$u_arr[0]['user_character'];
		$user_img=$u_arr[0]['user_img'];
		//更新帖子信息
		$number=$post_arr[0]['post_hot']+1;
		$udata['post_hot']=$number;
		$udata['post_read_time']=date('Y-m-d H:i:s');
		$m->where("post_id={$post_id}")->save($udata);
		//输出数据
		$status=$post_arr[0]['post_status'];
		if(($status==2)||($status==3)){
			$content='内容已经被管理员屏蔽';
		}else{
		$content=$post_arr[0]['post_content'];
		$content=ubb($content);
		//附件处理
		//$f=M('Accessory');
		//$f_data['accessory_post_id']=$post_id;	
		//$f_arr=$f->where($f_data)->select();
		//$this->assign('file',$f_arr);
		//投票处理
		//$v=M('Bbs_vote');
		//$v_data['bbs_vote_post_id']=$post_id;
		//$v_arr=$v->where($v_data)->select();
		//$this->assign('vote',$v_arr);
		}
		$this->assign('title',$post_arr[0]['post_title']);
		$this->assign('content',$content);
		$this->assign('hot',$number);
		$this->assign('date',$post_arr[0]['post_date']);
		$this->assign('user_id',$post_arr[0]['post_ower_id']);
		$this->assign('my_id',$this->user_id);
		$this->assign('user_name',$u_name);
		$this->assign('board_id',$post_arr[0]['post_board_id']);
		$this->assign('status',$status);
		$this->assign('user_img',$user_img);
		$this->assign('board',$board_name);
		$this->assign('user_rank',$u_rank);
		$this->assign('user_character',$u_character);
		$this->assign('post_id',$post_id);
		$Agent=$post_arr[0]['post_browser'];
		$browser=determinebrowser ($Agent);
		$system=determineplatform ($Agent);
		$this->assign('browser',$Agent);
		$this->assign('browsers',$browser);
		$this->assign('system',$system);
		//帖子当前的链接
		$url= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$this->assign('url',$url);
		//回复的内容输出
		$r=M('Bbs_respond');
		$respond_arr=$r->where("respond_post_id='$post_id' and respond_status=1")->order('respond_id desc')->limit(5)->select();
		$count=$r->where("respond_post_id='$post_id' and respond_status=1")->count();
		$this->assign('respond_arr',$respond_arr);
		$this->assign('count',$count);
		$filename='http://icecms.cn/content.txt';//随机回复语录
		$lines=explode("\n",file_get_contents($filename));
		$key=rand(0,count($lines)-1);
		$phase=$lines[$key];
		$phase = iconv("gb18030", "utf-8//IGNORE",$phase);
		$this->assign('phase',$phase);
	 	$this->display();
	}

	public function shua(){
	/*	$k=rand(0,10);
		if($k>5){
			exit();
		}
		$filename='http://icecms.cn/content.txt';
		$lines=explode("\n",file_get_contents($filename));
		//print_r($lines);
		//mt_srand(time());	
		$key=rand(0,count($lines)-1);
		$content=$lines[$key];
		echo  $content = iconv("gb18030", "utf-8//IGNORE",$content);
		$m=M('Bbs_post');
		$P=$m->where("post_id!=0")->order('post_id desc')->limit(5)->select();
		//dump($P);
		echo $post_id=$P[rand(0,4)]['post_id'];
		$r=M('Bbs_respond');
		$count=$r->where("respond_post_id='$post_id' and respond_status=1")->count();
		if($count>=2){
			echo '>2';
			exit();
		}
		$data['post_id']=$post_id;//随机从前五张帖子里面。。。找
		$count=$m->where($data)->count();
		$post_arr=$m->where($data)->select();
		if(!$count){
			//$this->error('帖子不存在!');
			echo "帖子不存在";
			exit();
		}
		if(($post_arr[0]['post_status']==0)||($post_arr[0]['post_status']==3)){
			//	$this->error('此帖子已经被锁定，无法回复');
			echo '此帖子已经被锁定，无法回复';
			exit();
		}
		$m=M("User");
		$status_arr=$m->where("user_id<150 and user_login_last_ip")->select();
		$count=$m->where("user_id<150 and user_login_last_ip")->count();
	//	echo $m->getLastSql();
	//	print_r($status_arr);
		$key=rand(0,$count-1);
		$user_respond_time=$status_arr[$key]['user_respond_time'];
		$pdata['post_respond_time']=date('Y-m-d H:i:s');
		$m->where($data)->save($pdata);
		$data['respond_content']=$content;
		$data['respond_time']=date('Y-m-d H:i:s');
	echo 	$data['respond_user_name']=$status_arr[$key]['user_name'];
		$data['respond_user_id']=$id=$status_arr[$key]['user_id'];
		$data['respond_post_id']=htmlspecialchars($post_id);
		$data['respond_ip']=htmlspecialchars(getip());
		$data['respond_browser']="Mozilla/5.0 (Linux; U; Android 4.4.4; zh-CN; MI 3W Build/KTU84P) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 UCBrowser/10.8.0.654 U3/0.8.0 Mobile Safari/534.30";//浏览器信息
		$r=M('Bbs_respond');
		echo $count=$r->add($data);
		if($count>0){
			$m=M('User');
			$datas['user_money']=$status_arr[$key]['user_money']+15;//发帖赠送金币
			$datas['user_integral']=$status_arr[$key]['user_integral']+5;//积分
			$datas['user_respond_number']=$status_arr[$key]['user_respond_number']+1;//回复次数
			$datas['user_respond_time']=time();
			$m->where("user_id='$id'")->save($datas);
			echo "success";
			//$this->success('回复成功,币币和积分有所提升~','',0);
		}else{
			//	$this->error('回复失败');
			echo "faile";
		}	*/
	}
	public function add_respond(){
		/*
			$isdata['status']  = 0;
			echo $isdata['content']  =$_POST['content'];
			$this->ajaxReturn($isdata);   
			exit();
		 */
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),3);
			eixt();
		}
	 	if(!isset($_POST['id'])){
				$this->error('非法访问!');
				exit();
		}
		$this->check_Token();//验证csrf_token
		//$this->setToken();//设置Crsf_Token
		$post_id=(int)I('post.id','','htmlspecialchars');
		$m=M('Bbs_post');
		$data['post_id']=$post_id;
		$count=$m->where($data)->count();
		$post_arr=$m->where($data)->select();
		if(!$count){
			$this->error('帖子不存在!');
			exit();
		}
		if(($post_arr[0]['post_status']==0)||($post_arr[0]['post_status']==3)){
			/*$this->error('此帖子已经被锁定，无法回复');*/
			
			$isdata['status']  = 0;
			$isdata['content']  = "此帖子已经被锁定，无法回复";
			$this->ajaxReturn($isdata);   
			exit();
		}
		$user_respond_time=$this->user_arr[0]['user_respond_time'];
		$user_status=$this->user_arr[0]['user_status'];
		//$content=I('post.content','','htmlspecialchars');
		$content=$_POST['content'];
		if(!isMobile()){
			$content=xss_clean($content);//过滤XSS代码
		}else{
			$content=htmlspecialchars($content);
		}

		if ((substr($content, 3000, 1) != '')||($content=='')) {
			/*$this->error('回复的内容不能为空！或超过1000字!');*/  
			
			$isdata['status']  = 0;
			$isdata['content']  = "回复的内容不能为空！或超过1000字!";
			$this->ajaxReturn($isdata); 
			exit();
		}
		//有效防止刷帖
		
		if(time()-$user_respond_time<=3){
		/*	$this->error('回复间隔不得小于3s!');*/

			$isdata['status']  = 0;
			$isdata['content']  = "回复间隔不得小于3s!";
			$this->ajaxReturn($isdata);   
			exit();
			
		}
		if($user_status==7||$user_status==8){
		/*	$this->error('你已经被禁止在论坛回帖'); */
			$isdata['status']  = 0;
			$isdata['content']  = "你已经被禁止在论坛回帖";
			$this->ajaxReturn($isdata); 
			exit();
		}
		$pdata['post_respond_time']=date('Y-m-d H:i:s');
		$m->where($data)->save($pdata);
		$data['respond_content']=$content;
		$data['respond_time']=date('Y-m-d H:i:s');
		$data['respond_user_name']=$this->user_name;
		$data['respond_user_id']=$this->user_id;
		$data['respond_post_id']=htmlspecialchars($post_id);
		$data['respond_ip']=htmlspecialchars(getip());
		$data['respond_browser']=htmlspecialchars($_SERVER['HTTP_USER_AGENT']);//浏览器信息
		$r=M('Bbs_respond');
		$count=$r->add($data);
		if($count>0){
			$m=M('User');
			$datas['user_money']=$this->user_arr[0]['user_money']+$this->status_arr[0]['r_money'];//发帖赠送金币
			$datas['user_integral']=$this->user_arr[0]['user_integral']+$this->status_arr[0]['r_integral'];//积分
			$datas['user_respond_number']=$this->user_arr[0]['user_respond_number']+1;//回复次数
			$datas['user_respond_time']=time();
			$m->where("user_sid='$this->user_sid'")->save($datas);
			$this->atmessage($post_id,$content,2);//@消息内信发送
			/*$this->success('回复成功,币币和积分有所提升~','',0);*/
			$isdata['status']  = 1;
			$isdata['content']  = '我说:'.ubb($content);
			$this->ajaxReturn($isdata);
		}else{
			/*$this->error('回复失败');*/  
			$isdata['status']  = $count;
			$isdata['content']  = "回复失败";
			$this->ajaxReturn($isdata); 
		}
	}


	public function respond_list(){
		if(!isset($_GET['id'])){
			$this->error('非法访问!');
			exit();
		}
		$post_id=(int)I('get.id','','htmlspecialchars');
		$m=M('Bbs_post');
		$data['post_id']=$post_id;
		$count=$m->where($data)->count();
		if(!$count){
			$this->error('帖子不存在!');
			exit();
		}
		$post_arr=$m->where("post_id='$post_id'")->select();
		$this->assign('title',$post_arr[0]['post_title']);
			//回复的内容输出
		$r=M('Bbs_respond');
		$count=$r->where("respond_post_id='$post_id' and respond_status=1")->order('respond_id desc')->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $r->where("respond_status=1 and respond_post_id='$post_id'" )->order('respond_time')->limit($Page->firstRow.','.$Page->listRows)->order('respond_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$this->assign('post_id',$post_id);
		$this->assign('count',$count);
		$this->display();

	}


	public function board_list(){
		if(!isset($_GET['id'])){
			$this->error("非法访问");
		}
		$id=(int)I('get.id','','htmlspecialchars');
		$data['post_board_id'] =$id;
		$m=M('Bbs_post');
		$count=$m->where($data)->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->order('post_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$b=M('Bbs_board');
		$datas['bbs_board_id']=$id;
		$board_name=$b->where($datas)->getField('bbs_board_name');
		$this->assign('title',$board_name);
		$this->display();
	}


	public function Last_Respond_Post(){
		$m=M('Bbs_post');
		////#####################最新回复帖子#########################################3
		$count=$m->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->limit($Page->firstRow.','.$Page->listRows)->order('post_respond_time desc')->select();
		$this->assign('list',$list);//赋值数据集
		$this->assign('page',$show);
		$this->assign('title',"最新回复的帖子");
		$this->display('board_list');
		//#############################################################################3
	}


	public function Good_Post(){
		$m=M('Bbs_post');
		$data['post_status']=5;//精华帖子
		////#####################精华帖子#########################################3
		$count=$m->where($data)->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->order('post_id desc')->select();
		$this->assign('list',$list);//赋值数据集
		$this->assign('page',$show);
		$this->assign('title',"精华帖子");
		$this->display('board_list');
		//#############################################################################3
	}


	public function user_post(){
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),1);
			eixt();
		}
		$id=(int)I('get.id',$this->user_id,'htmlspecialchars');
		$data['post_ower_id'] =$id;
		$m=M('Bbs_post');
		$count=$m->where($data)->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->order('post_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$this->assign('title',"我的帖子");
		$this->display();
	}


	public function new_post(){
		$m=M('Bbs_post');
		$count=$m->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->limit($Page->firstRow.','.$Page->listRows)->order('post_id desc')->select();
		$this->assign('list',$list);//赋值数据集
		$this->assign('page',$show);
		$this->assign('title',"最新帖子");
		$this->display('board_list');
	}




	public function add(){
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),1);
			eixt();
		}
		$this->setToken();//设置Crsf_Token
		//检测版块
		$b=M('Bbs_board');
		$board_arr_=$b->select();//版块数组
		$this->assign('board_arr_',$board_arr_);
		$this->display();
	}



	public function add_do(){
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),1);
			eixt();
		}
		$this->check_Token();//验证csrf_token
		//$this->setToken();//设置Crsf_Token
		$user_announce_time=$this->user_arr[0]['user_announce_time'];
		$user_status=$this->user_arr[0]['user_status'];
		$title=I('post.title','','htmlspecialchars');
		//$content=I('post.content','','htmlspecialchars');
		$content=$_POST['content'];
		if(!isMobile()){
			$content=xss_clean($content);//过滤XSS代码
		}else{
			$content=htmlspecialchars($content);
		}
		$board_id=(int)I('post.board_id','','htmlspecialchars');
		if ((substr($title, 150, 1) != '')||($title=='')) {	
			$this->error('标题不能为空或超过50个字！！');
			exit();
		}
		if ((substr($content, 1200000, 1) != '')||($content=='')) {
			$this->error('发表的内容不能为空！或超过40000字!');
			exit();
		}
		//有效防止刷帖
		if(time()-$user_announce_time<=1){
			$this->error('发帖间隔不得小于3分钟!');
			exit();
		}
		if($user_status==2||$user_status==8){
			$this->error('你已经被禁止在论坛发帖');
			exit();
		}
		//检测版块是否存在
		$b=M('Bbs_board');
		$board_arr_=$b->select();//版块数组
		$board_number=$b->count();
		$y=0;
		for($i=1;$i<=$board_number;$i++){
			if($board_id==$board_arr_[$i-1]['bbs_board_id']){
			$y=1;
			}//此为版块id
		}
		if($y==0){
			$this->error('版块不存在');
			exit();
		}
		//更新用户的发帖时间
		$data['post_title']=htmlspecialchars($title);
		$data['post_content']=$content;
		$data['post_ip']=getip();//ip
		$data['post_board_id']=htmlspecialchars($board_id);
		$data['post_read_time']=date('Y-m-d H:i:s');
		$data['post_respond_time']=date('Y-m-d H:i:s');
		$data['post_date']=date('Y-m-d H:i:s');
		$data['post_ower_id']=$this->user_id;
		$data['post_ower_name']=$this->user_name;
		$data['post_browser']=htmlspecialchars($_SERVER['HTTP_USER_AGENT']);//浏览器信息
		$m=M('Bbs_post');
		$count=$m->add($data);
		if($count>0){
			$m=M('User');
			$datas['user_money']=$this->user_arr[0]['user_money']+$this->status_arr[0]['s_money'];//发帖赠送金币
			$datas['user_integral']=$this->user_arr[0]['user_integral']+$this->status_arr[0]['s_integral'];//积分
			$datas['user_theme_number']=$this->user_arr[0]['user_theme_number']+1;//发帖次数
			$datas['user_announce_time']=time();
			$m->where("user_sid='$this->user_sid'")->save($datas);
			$this->atmessage($count,$content,1);
			$this->success("发表成功，金币+{$this->status_arr[0]['s_money']}，积分+{$this->status_arr[0]['s_integral']},！",U('Index/bbs'),1);	
		}
		else{
			$this->error('发表失败!');
			exit();
		}
	}




	//修改帖子的页面
	public function change_post(){
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),3);
			eixt();
		}
	 	if(!isset($_GET['id'])){
			$this->error('非法访问!');
			exit();
		}
		$this->setToken();//设置Crsf_Token
		$post_id=(int)I('get.id','','htmlspecialchars');
		$m=M('Bbs_post');
		$data['post_id']=$post_id;
		$data['post_ower_id']=$this->user_id;
		$count=$m->where($data)->count();
 		$post_arr=$m->where($data)->select();
		$content=$post_arr[0]['post_content'];
		$title=$post_arr[0]['post_title'];
		if(!$count){
			$this->error('帖子不存在!或者并非你的帖子!');
			exit();
		}
		//检测版块
		$b=M('Bbs_board');
		$board_arr_=$b->select();//版块数组
		$this->assign('board_arr_',$board_arr_);
		$this->assign('post_id',$post_id);
		$this->assign('content',$content);
		$this->assign('title',$title);
		$this->display();

	}


	//修改帖子
	public function change_postdo(){
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),3);
			eixt();
		}
	 	if(!isset($_POST['id'])){
			$this->error('非法访问!');
			exit();
		}
		$this->check_Token();//验证csrf_token
		//$this->setToken();//设置Crsf_Token
		$board_id=(int)I('post.board_id','','htmlspecialchars');
		$post_id=(int)I('post.id','','htmlspecialchars');
		$m=M('Bbs_post');
		$data['post_id']=$post_id;
		$data['post_ower_id']=$this->user_id;
		$count=$m->where($data)->count();
		$post_arr=$m->where($data)->select();
		if(!$count){
			$this->error('帖子不存在!或者并非你的帖子!');
			exit();
		}
		if(($post_arr[0]['post_status']==0)||($post_arr[0]['post_status']==3)){
			$this->error('此帖子已经被锁定，无法修改');
			exit();
		}
			//检测版块是否存在
		$b=M('Bbs_board');
		$board_arr_=$b->select();//版块数组
		$board_number=$b->count();
		$y=0;
		for($i=1;$i<=$board_number;$i++){
			if($board_id==$board_arr_[$i-1]['bbs_board_id']){
			$y=1;
			}//此为版块id
		}
		if($y==0){
			$this->error('版块不存在');
			exit();
		}
		$user_status=$this->user_arr[0]['user_status'];
		$title=I('post.title','','htmlspecialchars');
		//$content=I('post.content','','htmlspecialchars');
		$content=$_POST['content'];
		$content=xss_clean($content);//过滤XSS代码
		if ((substr($content, 1200000, 1) != '')||($content=='')) {
			$this->error('内容不能为空！或超过40000字!');
			exit();
		}
		if ((substr($title, 150, 1) != '')||($title=='')) {	
			$this->error('标题不能为空或超过50个字！！');
			exit();
		}
		if($user_status==2||$user_status==8){
			$this->error('你已经被禁止在论坛发帖');
			exit();
		}
		$datas['post_board_id']=$board_id;
		$datas['post_title']=$title;
		$datas['post_content']=$content.'<br/ >最后一次修改时间为:'.date('Y-m-d H:i:s');
		$datas['post_respond_time']=date('Y-m-d H:i:s');
		$count=$m->where($data)->save($datas);
		if($count){
			$this->success('修改成功！',U('Index/bbs'),1);	
		}else{
			$this->error('修改失败!');
			exit();
		}
		
	}





	//@消息处理
	private function atmessage($post_id,$content1,$s){
		if(!preg_match_all("!(@|＠)(#|＃)?([\\x{4e00}-\\x{9fa5}A-Za-z0-9_\\-]{1,})(\x20|\\[|\xC2\xA0|\r|\n|\x03|\t|,|\\?|\\!|:|;|，|。|？|！|：|；|、|…|$)!u",$content1,$arr,PREG_SET_ORDER)){
			return false;
		}
		$u=M('User');
		$b=M('Bbs_post');
		$title=$b->where("post_id='$post_id'")->getField('post_title');
		$i=0;
		foreach($arr as $d){
		      if(!in_array($d['3'],$name)){
			 	$count=$u->where("user_name='$d[3]'")->count();
	 			if($count){
					$user_id=$u->where("user_name='$d[3]'")->getField('user_id');
					$m=M('Atmessage');
					$data['atmessage_send_time']=date('Y-m-d H:i:s');
					$content=sub_str($content1,0,80,0);//截取40字
					if($s==1){
						$content="'$this->user_name'(ID:'$this->user_id')在帖子《'$title'》的内容中@你--<br>".$content."...<a href=../Bbs/read/id/$post_id>点击进入帖子查看</a>";
					}else{
						$content="'$this->user_name'(ID:'$this->user_id')在帖子《'$title'》的回复中@你--<br>".$content."...<a href=../Bbs/read/id/$post_id>点击进入帖子查看</a>";
					}
					$data['atmessage_postid']=$post_id;
					$data['atmessage_from_user_name']=$this->user_name;
					$data['atmessage_from_user_id']=$this->user_id;
					$data['atmessage_to_user_name']=$d['3'];
					$data['atmessage_to_user_id']=$user_id;
					$data['atmessage_content']=$content;
					$m->add($data);
				}
			$name[$i]=$d['3'];//把已经@了的用户放进数组里面
			$i++;
		      }
		}
	}
	
}

