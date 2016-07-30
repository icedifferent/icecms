<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends PublicController {
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
		$this->assign('title',$this->status_arr[0]['status_title']);//分配网站标题
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
				$this->user_sid='';
				$this->user_name='';
				$this->user_id='';
			}
		}else{
			$this->user_sid='';
			$this->user_name='';
			$this->user_id='';
		}
		//用户日志记录
		traceHttp();
	}


	public function index(){
		Header("Location: /index.php/Home/Index/bbs");
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
		$m=M('Bbs_post');
		////#####################最新回复帖子#########################################3
		$count=$m->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->limit($Page->firstRow.','.$Page->listRows)->order('post_respond_time desc')->select();
		$this->assign('list',$list);//赋值数据集
		$this->assign('page',$show);
		//#############################################################################3



	
		

	
		////#####################最新评论的文章#########################################3
		$m=M('Article');
		$count=$m->where("article_status!=3")->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$list = $m->limit($Page->firstRow.','.$Page->listRows)->where("article_status!=3")->order('article_respond_time desc')->select();
		$this->assign('a_list',$list);//赋值数据集
		//#############################################################################3
		
		////#####################
		$m=M('Article');
		$count=$m->where("article_status!=3")->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$list = $m->limit($Page->firstRow.','.$Page->listRows)->where("article_status!=3 and article_board_id=2")->order('article_respond_time desc')->select();
		$this->assign('a_list_b',$list);//赋值数据集
		//#############################################################################3

		$me=M('Message');
		$sign=$me->where("message_to_id='$this->user_id' and message_if_read=0")->count();
		if($sign){
			$this->assign('msg',"<a href=".U('Message/receive').">你收到一条好友发过来的消息</a>");
		}else{
			$this->assign('msg',"");
		}

		$me=M('Atmessage');
		$sign=$me->where("atmessage_to_user_id='$this->user_id' and atmessage_if_read=0")->count();
		if($sign){
			$this->assign('atmsg',"<a href=".U('Message/atreceive').">你有未读@(#)消息</a>");
		}else{
			$this->assign('atmsg',"");
		}
		$this->assign('user_name',$this->user_name);//分配昵称
		$this->assign('user_id',$this->user_id);//分配id
		$this->assign('day',date('Y-m-d H:i:s'));//分配时间
		$this->assign('website_foot',$this->status_arr[0]['website_foot']);
		$this->display();
	}

	public function bbs(){
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
		$m=M('Bbs_post');
		////#####################最新回复帖子#########################################3
		$count=$m->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->limit($Page->firstRow.','.$Page->listRows)->order('post_respond_time desc')->select();
		$this->assign('list',$list);//赋值数据集
		$this->assign('page',$show);
		//#############################################################################3



	
		

	
		////#####################最新评论的文章#########################################3
		$m=M('Article');
		$count=$m->where("article_status!=3")->count();
		$Page = new \Think\Page($count,5);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$list = $m->limit($Page->firstRow.','.$Page->listRows)->where("article_status!=3")->order('article_respond_time desc')->select();
		$this->assign('a_list',$list);//赋值数据集
		//#############################################################################3
		


		$me=M('Message');
		$sign=$me->where("message_to_id='$this->user_id' and message_if_read=0")->count();
		if($sign){
			$this->assign('msg',"<a href=".U('Message/receive').">你收到一条好友发过来的消息</a>");
		}else{
			$this->assign('msg',"");
		}

		$me=M('Atmessage');
		$sign=$me->where("atmessage_to_user_id='$this->user_id' and atmessage_if_read=0")->count();
		if($sign){
			$this->assign('atmsg',"<a href=".U('Message/atreceive').">你有未读@(#)消息</a>");
		}else{
			$this->assign('atmsg',"");
		}
		$this->assign('user_name',$this->user_name);//分配昵称
		$this->assign('user_id',$this->user_id);//分配id
		$this->assign('day',date('Y-m-d H:i:s'));//分配时间
		$this->assign('website_foot',$this->status_arr[0]['website_foot']);
		$this->display();
	}
	

	public function search(){
		//if(!isset($_POST['content'])||){
			//$this->error("非法访问");
		//}
		$content=I('post.content','','htmlspecialchars');
		$type=I('post.sort','','htmlspecialchars');
		$data['_logic']="or";
		if($type=='user'){
			$data['post_ower_name'] = array('like',"%{$content}%");
		}else {
			$data['post_title'] = array('like',"%{$content}%");
			$data['post_content'] = array('like',"%{$content}%");
		}
		$m=M('Bbs_post');
		$count=$m->where($data)->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		//$Page->setConfig('theme',"%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%");
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		//$data['post_status']
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->order('post_id desc')->select();
		$this->assign('list',$list);//赋值数据集
		$this->assign('page',$show);
		$this->assign('title',$content);
		$this->display();
	}

}?>
