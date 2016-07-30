<?php
namespace Home\Controller;
use Think\Controller;
class MessageController extends PublicController {	
	private $user_sid='';
	private $user_name='';
	private $user_id='';
	private $status_arr=array();
	private $user_arr=array();
	public function __construct(){
		parent::__construct();
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
				$this->error('请先登录',U('Login/index'),1);
				eixt();
			}
		}else{
				$this->error('请先登录',U('Login/index'),1);
				eixt();
		}
		//用户日志记录
		traceHttp();
	}
	public function index(){
		$this->setToken();//设置Crsf_Token
		$id=I('get.id','1000','htmlspecialchars');
		$u=M('User');
		$user_name=$u->where("user_id='$id'")->getField('user_name');
		$this->assign('id',$id);
		$this->assign('user_name',$user_name);
		$this->assign('title',"信箱");
		$this->display();
	}


	public function receive(){
		$m=M('Message');
		$data['message_to_id']=$this->user_id;
		$count=$m->where($data)->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->order('message_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$this->assign('title',"收件箱");
		$this->display('list');
		//把邮件标记为已读
		$m_data['message_if_read']=1;
		$m->where("message_to_id='$this->user_id'")->save($m_data);
	}


	public function sended(){
		$m=M('Message');
		$data['message_from_id']=$this->user_id;
		$count=$m->where($data)->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->order('message_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$this->assign('title',"发件箱");
		$this->display('list');
	}


	public function send_do(){
		$s_time=$this->user_arr[0]['user_message_time'];
		if(time()-$s_time<1){
			$this->error('前后两次信息发送时间间隔太短');
		}	
		$this->check_Token();//验证csrf_token
		//$this->setToken();//设置Crsf_Token
		$content=$data['message_content']=I('post.content','','htmlspecialchars');
		$to_id=(int)I('post.to_id','','htmlspecialchars');
		$content=sub_str($content,0,600,0);//截取300字
		$m=M('User');
		$count=$m->where("user_id='$to_id'")->count();
		if($count==0){
			$this->error('ID不存在');
		}
		$name=$m->where("user_id='$to_id'")->getField('user_name');
		$m=M('Message');
		$data['message_to_id']=$to_id;
		$data['message_to_name']=$name;
		$data['message_from_id']=$this->user_id;
		$data['message_from_name']=$this->user_name;
		$data['message_send_time']=date('Y-m-d H:i:s');
		$count=$m->add($data);
		if($count>0){
			$this->success('发送成功','',1);
		}else{
			$this->error('发送失败');
		}
	}



	public function atreceive(){
		$m=M('Atmessage');
		$data['atmessage_to_user_id']=$this->user_id;
		$count=$m->where($data)->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->order('atmessage_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$this->assign('title',"@消息箱");
		$this->display('atlist');
		//把邮件标记为已读
		$m_data['atmessage_if_read']=1;
		$m->where("atmessage_to_user_id='$this->user_id'")->save($m_data);
	}

}
