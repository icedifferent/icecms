<?php
namespace Home\Controller;
use Think\Controller;
class ArticleController extends PublicController {
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
				$this->error('请先登录',U('Login/index'),3);
				eixt();
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
		$m=M('Article_board');
		$board_number=$m->count();//版块总数
		$b_arr=$m->select();
		$m=M('Article');
		for($i=1;$i<=$board_number;$i++){
			$id=$b_arr[$i-1]['article_board_id'];
			$count=$m->where("article_board_id='$id'")->order('article_respond_time desc')->count();
			$arr=$m->where("article_board_id='$id' and article_status!=3")->order('article_respond_time desc')->limit(8)->select();
			$this->assign("article_list_$id",$arr);
			$this->assign("count_$id",$count);
		}
		$this->assign('user_name',$this->user_name);//分配昵称
		$this->assign('user_id',$this->user_id);//分配id
		$this->assign('day',date('Y-m-d H:i:s'));//分配时间
	//	$this->assign('announce',$this->status[0]['status_announce']);//分配网站公告
		$this->assign('title',$this->status_arr[0]['status_title']);//分配网站标题
		$this->assign('key',$this->status_arr[0]['status_key']);//分配网站关键字
		$this->assign('describe',$this->status_arr[0]['status_describe']);//分配网站描述
		$this->display();
	}



	public function add(){
		//检测版块
		$this->setToken();//设置Crsf_Token
		$b=M('Article_board');
		$board_arr_=$b->select();//版块数组
		$this->assign('board_arr_',$board_arr_);
		$this->display();
	}

	public function add_do(){
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),3);
			eixt();
		}
		$this->check_Token();//验证csrf_token
		//$this->setToken();//设置Crsf_Token
		$user_status=$this->user_arr[0]['user_status'];
		$title=I('post.title','','htmlspecialchars');
		//$content=I('post.content','','htmlspecialchars');
		$content=$_POST['content'];
		$content=xss_clean($content);//过滤XSS代码
		$board_id=(int)I('post.board_id','','htmlspecialchars');
			if ((substr($title, 90, 1) != '')||($title=='')) {
			$this->error('标题不能为空或超过30个字！！');
			exit();
		}
		if ((substr($content, 1500000, 1) != '')||($content=='')) {
			$this->error('发表的内容不能为空！或超过50000字!');
			exit();
		}
		if($user_status==2||$user_status==8){
			$this->error('你已经被禁止在论坛发帖');
			exit();
		}
		//检测版块是否存在
		$b=M('Article_board');
		$board_arr_=$b->select();//版块数组
		$board_number=$b->count();
		$y=0;
		for($i=1;$i<=$board_number;$i++){
			if($board_id==$board_arr_[$i-1]['article_board_id']){
				$y=1;
			}//此为版块id
		}
		if($y==0){
			$this->error('版块不存在');
			exit();
		}
		$data['article_title']=htmlspecialchars($title);
		$data['article_content']=$content;
		$data['article_ip']==htmlspecialchars(getip());//ip
		$data['article_board_id']=htmlspecialchars($board_id);
		$data['article_read_time']=date('Y-m-d H:i:s');
		$data['article_respond_time']=date('Y-m-d H:i:s');
		$data['article_date']=date('Y-m-d H:i:s');
		$data['article_ower_id']=$this->user_id;
		$data['article_ower_name']=$this->user_name;
		$data['article_browser']=htmlspecialchars($_SERVER['HTTP_USER_AGENT']);//浏览器信息
		$m=M('Article');
		$count=$m->add($data);
		if($count>0){
			$this->success("投稿成功,等待管理员审核",U('Index/index'),1);
		}else{
			$this->error("投稿失败");
		}
	}



	public function read(){
		if(!isset($_GET['id'])){
			$this->error('非法访问!');
			exit();
		}
		$this->setToken();//设置Crsf_Token
		$post_id=(int)I('get.id','','htmlspecialchars');
		$m=M('Article');
		$data['article_id']=$post_id;
		$count=$m->where($data)->count();
		if(!$count){
			$this->error('文章不存在!');
			exit();
		}
		$post_arr=$m->where($data)->select();
		//找出版块名称
		$b=M('Article_board');
		$datas['article_board_id']=$post_arr[0]['article_board_id'];
		$board_name=$b->where($datas)->getField('article_board_name');
		//找用户信息
		$u=M('User');
		$u_id=$post_arr[0]['article_ower_id'];
		$u_arr=$u->where("user_id='$u_id'")->select();
		$u_name=$u_arr[0]['user_name'];
		$u_rank=$u_arr[0]['user_rank'];
		$u_character=$u_arr[0]['user_character'];
		$user_img=$u_arr[0]['user_img'];
		//更新帖子信息
		$number=$post_arr[0]['article_hot']+1;
		$udata['article_hot']=$number;
		$udata['article_read_time']=date('Y-m-d H:i:s');
		$m->where("article_id={$post_id}")->save($udata);
		//输出数据
		$status=$post_arr[0]['article_status'];
		if(($status==2)||($status==3)){
			$content='内容已经被管理员屏蔽';
		}else{
			$content=$post_arr[0]['article_content'];
			$content=ubb($content);
		}
		$this->assign('title',$post_arr[0]['article_title']);
		$this->assign('content',$content);
		$this->assign('hot',$number);
		$this->assign('date',$post_arr[0]['article_date']);
		$this->assign('user_id',$post_arr[0]['article_ower_id']);
		$this->assign('my_id',$this->user_id);
		$this->assign('user_name',$u_name);
		$this->assign('board_id',$post_arr[0]['article_board_id']);
		$this->assign('lookdown_times',$post_arr[0]['article_lookdown_times']);
		$this->assign('praise_times',$post_arr[0]['article_praise_times']);
		$this->assign('status',$status);
		$this->assign('user_img',$user_img);
		$this->assign('board',$board_name);
		$this->assign('user_rank',$u_rank);
		$this->assign('user_character',$u_character);
		$this->assign('article_id',$post_id);
		$Agent=$post_arr[0]['articlet_browser'];
		$browser=determinebrowser ($Agent);
		$system=determineplatform ($Agent);
		$this->assign('browser',$Agent);
		$this->assign('browsers',$browser);
		$this->assign('system',$system);
		//帖子当前的链接
		$url= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$this->assign('url',$url);
		//回复的内容输出
		$r=M('Article_respond');
		$respond_arr=$r->where("respond_article_id='$post_id' and respond_status=1")->order('respond_id desc')->limit(5)->select();
		$count=$r->where("respond_article_id='$post_id' and respond_status=1")->count();
		$this->assign('respond_arr',$respond_arr);
		$this->assign('count',$count);

		////#####################//右侧栏目
		$m=M('Article');
		$count=$m->where("article_status!=3")->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$list = $m->limit($Page->firstRow.','.$Page->listRows)->where("article_status!=3 and article_board_id=2")->order('article_respond_time desc')->select();
		$this->assign('a_list_b',$list);//赋值数据集
		//#############################################################################3
	 	$this->display();
	}

	public function praise(){
		if(!isset($_POST['id'])){
			$this->error('非法访问!');
			exit();
		}
		$post_id=(int)I('post.id','','htmlspecialchars');
		$m=M('Article');
		$data['article_id']=$post_id;
		$count=$m->where($data)->count();
		if(!$count){
			$this->error('文章不存在!');
			exit();
		}
		$post_arr=$m->where($data)->select();
		$data['article_praise_times']=$m->where($data)->getField('article_praise_times')+1;
		$m->save($data);
		//$this->success('','','0');
			$isdata['status']  = 1;
			$isdata['content']  = $data['article_praise_times'];
			$this->ajaxReturn($isdata);
	}
	public function lookdown(){
		if(!isset($_POST['id'])){
			$this->error('非法访问!');
			exit();
		}
		$post_id=(int)I('post.id','','htmlspecialchars');
		$m=M('Article');
		$data['article_id']=$post_id;
		$count=$m->where($data)->count();
		if(!$count){
			$this->error('文章不存在!');
			exit();
		}
		$post_arr=$m->where($data)->select();
		$data['article_lookdown_times']=$m->where($data)->getField('article_lookdown_times')+1;
		$m->save($data);
		//$this->success('','','0');
		$isdata['status']  = 1;
		$isdata['content']  = $data['article_lookdown_times'];
		$this->ajaxReturn($isdata);
	}

	public function respond_list(){
		if(!isset($_GET['id'])){
			$this->error('非法访问!');
			exit();
		}
		$post_id=(int)I('get.id','','htmlspecialchars');
		$m=M('Article');
		$data['article_id']=$post_id;
		$count=$m->where($data)->count();	
		if(!$count){
			$this->error('帖子不存在!');
			exit();
		}
		$post_arr=$m->where($data)->select();
		//评论的内容输出
		$this->assign('title',$post_arr[0]['article_title']);
		$r=M('Article_respond');
		$count=$r->where("respond_status=1 and respond_article_id='$post_id'")->order('respond_id desc')->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $r->where("respond_status=1 and respond_article_id='$post_id'" )->order('respond_time')->limit($Page->firstRow.','.$Page->listRows)->order('respond_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$this->assign('article_id',$post_id);
		$this->assign('count',$count);
		$this->display();
	}



	public function board_list(){
		if(!isset($_GET['id'])){
			$this->error("非法访问");
		}
		$id=(int)I('get.id','','htmlspecialchars');
		$data['article_board_id'] =$id;
		$m=M('Article');
		$count=$m->where($data)->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->where("article_status!=3")->order('article_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$b=M('article_board');
		$datas['article_board_id']=$id;
		$board_name=$b->where($datas)->getField('article_board_name');

		////#####################//右侧栏目
		$m=M('Article');
		$count=$m->where("article_status!=3")->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$list = $m->limit($Page->firstRow.','.$Page->listRows)->where("article_status!=3 and article_board_id=2")->order('article_respond_time desc')->select();
		$this->assign('a_list_b',$list);//赋值数据集
		//#############################################################################3

		$this->assign('title',$board_name);
		$this->display();
	}



	public function new_article(){
		$m=M('Article');
		$count=$m->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->limit($Page->firstRow.','.$Page->listRows)->where("article_status!=3")->order('article_id desc')->select();
		$this->assign('list',$list);//赋值数据集
		$this->assign('page',$show);
		$this->assign('title',"最新文章");

		////#####################//右侧栏目
		$m=M('Article');
		$count=$m->where("article_status!=3")->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$list = $m->limit($Page->firstRow.','.$Page->listRows)->where("article_status!=3 and article_board_id=2")->order('article_respond_time desc')->select();
		$this->assign('a_list_b',$list);//赋值数据集
		//#############################################################################3
		$this->display('board_list');
	}


	public function search(){
		if(!isset($_POST['content'])){
			$this->error("非法访问");
		}
		$content=I('post.content','','htmlspecialchars');
		$type=I('post.sort','','htmlspecialchars');
		$data['_logic']="or";
		if($type=='title'){
			$data['article_title'] = array('like',"%{$content}%");
			$data['article_content'] = array('like',"%{$content}%");
		}else {
			$data['article_ower_name'] = array('like',"%{$content}%");
		}
		$m=M('Article');
		$count=$m->where($data)->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->where("article_status!=3")->order('article_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$this->assign('title',$content);
		$this->display();
	}


	public function add_respond(){
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
		$m=M('Article');
		$data['article_id']=$post_id;
		$count=$m->where($data)->count();
		$post_arr=$m->where($data)->select();
		if(!$count){
			$this->error('文章不存在!');
			exit();
		}
		if(($post_arr[0]['article_status']==0)||($post_arr[0]['article_status']==3)){
			/*$this->error('此文章已经被锁定，无法回复');*/
			$isdata['status']  = 0;
			$isdata['content']  = "此文章已经被锁定，无法回复";
			$this->ajaxReturn($isdata);
			exit();
		}
		$user_respond_time=$this->user_arr[0]['user_respond_a_time'];
		$user_status=$this->user_arr[0]['user_status'];
		//$content=I('post.content','','htmlspecialchars');
		$content=$_POST['content'];
		$content=xss_clean($content);//过滤XSS代码
		if ((substr($content, 3000, 1) != '')||($content=='')) {
		/*	$this->error('回复的内容不能为空！或超过1000字!');*/
			$isdata['status']  = 0;
			$isdata['content']  = "回复的内容不能为空！或超过1000字!";
			$this->ajaxReturn($isdata);
			exit();
		}
		//有效防止刷帖
		if(time()-$user_respond_time<=5){
			/*$this->error('评论间隔不得小于5s!');*/
			$isdata['status']  = 0;
			$isdata['content']  = "评论间隔不得小于5s!";
			$this->ajaxReturn($isdata);
			exit();
		}
		if($user_status==7||$user_status==8){
			/*$this->error('你已经被禁止在论坛回帖');*/
			$isdata['status']  = 0;
			$isdata['content']  = "你已经被禁止在论坛回帖";
			$this->ajaxReturn($isdata);
			exit();
		}
		$pdata['article_respond_time']=date('Y-m-d H:i:s');
		$m->where($data)->save($pdata);
		$data['respond_content']=$content;
		$data['respond_time']=date('Y-m-d H:i:s');
		$data['respond_user_name']=$this->user_name;
		$data['respond_user_id']=$this->user_id;
		$data['respond_article_id']=htmlspecialchars($post_id);
		$data['respond_ip']=htmlspecialchars(getip());
		$data['respond_browser']=htmlspecialchars($_SERVER['HTTP_USER_AGENT']);//浏览器信息
		$r=M('Article_respond');
		$count=$r->add($data);
		if($count>0){
			$m=M('User');
			$datas['user_money']=$this->user_arr[0]['user_money']+$this->status_arr[0]['r_money'];//发帖赠送金币
			$datas['user_integral']=$this->user_arr[0]['user_integral']+$this->status_arr[0]['r_integral'];//积分
			$datas['user_respond_time']=time();
			$m->where("user_sid='$this->user_sid'")->save($datas);
			/*$this->success('评论成功','',0);*/
			$isdata['status']  = $count;
			$isdata['content']  ='我说:'.ubb($content);
			$this->ajaxReturn($isdata);
		}else{
			/*$this->error('评论失败');*/
			$isdata['status']  = $count;
			$isdata['content']  = "评论失败";
			$this->ajaxReturn($isdata);   
		}
	}


}
