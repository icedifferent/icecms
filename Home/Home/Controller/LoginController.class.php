<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends PublicController {
	private $status_arr=array();
	public function __construct(){
		parent::__construct();
		//
		$this->ddos();
		$this->temp();
		$m=M('Website');
		$this->status_arr=$m->where('status_id=1')->select();
		$this->assign('title',$this->status_arr[0]['status_title']);//分配网站标题
		$this->assign('key',$this->status_arr[0]['status_key']);//分配网站关键字
		$this->assign('description',$this->status_arr[0]['status_describe']);//分配网站描述
		//用户日志记录
		traceHttp();
	}
	public function index(){
 		$this->display('index');
	}

	public function do_login(){
		//是否开启验证码
		$verify = new \Think\Verify();
		$ok= $verify->check($_POST['code'],'');
		if(!$ok){
			$this->error('验证码错误');
			exit;
		}
		$pwd=$data['user_password']=MD5(I('post.user_password','','htmlspecialchars'));
		$m=M('User');
		if($_POST['m']=='name'){
			$user_name=$data['user_name']=I('post.user_name','','htmlspecialchars');
			$count=$m->where("user_name='$user_name' and user_password='$pwd'")->count();
		}else{
			$user_id=I('post.user_name','','htmlspecialchars');
			$count=$m->where("user_id='$user_id' and user_password='$pwd'")->count();
			if($count>0)
			$user_name=$data['user_name']=$m->where("user_id='$user_id'and user_password='$pwd'")->getField('user_name');
		}
		
		if($count>0){
			$user_sid=MD5($data['user_name'].$data['user_password']);
			$user_last_login_time=$m->where($data)->getField('user_login_time');
			$user_login_last_ip=$m->where($data)->getField('user_login_ip');
			$data_s['user_sid']=$user_sid;//sid
			$data_s['user_login_time']=@date("Y-m-d H:i:s");//time
			$data_s['user_last_login_time']=$user_last_login_time;//last time
			$data_s['user_login_ip']=getip();//ip
			$data_s['user_login_last_ip']=$user_login_last_ip;//ip
			$k=$m->where($data)->save($data_s);//更新sid以及登陆时间，登陆ip
			//$m->where($data)->getField('user_name');//find name
			cookie('user_sid',$user_sid,2592000);//把sid存进cookie
         		$this->success('欢迎你',U('Index/index'),1);
		}else{
			$this->error('登陆失败');
			exit;
		}
	}


   	 public function logout(){
		 cookie('user_sid',null);//退出删除cookie
		 $this->success('退出成功',U('Index/index'),2);
	 }


	//注册
 	public function reg(){
		$this->assign('title',$this->status_arr[0]['status_title']);//分配标题
 		$this->display('reg');
	}


	public function do_reg(){
		//判断网站是否开放注册
		if($this->status_arr[0]['reg_method']==4){
			$this->error('网站暂时关闭注册',U('Index/index'),3);
			exit;
		}
		$name=$data['user_name']=I('post.user_name','','htmlspecialchars');
		$password=I('post.user_password','','htmlspecialchars');
		$data['user_password']=MD5(I('post.user_password','','htmlspecialchars'));
		$phone=$data['user_phone']=I('post.user_phone','','htmlspecialchars');
		//是否开启验证码
		//默认开启
		//检验
		$verify = new \Think\Verify();
		$ok= $verify->check($_POST['code'],'');
		if(!$ok){
			$this->error('验证码错误');
			exit;
		}
		//检验信息是否已经存在以及合法性
		$m=M('User');
		$count=$m->where("user_name='$name'")->count();
		if($count>0){
			$this->error('昵称已经存在');
			exit;
		}
		$count=$m->where("user_phone='$phone'")->count();
		if($count>0){
			$this->error('手机号码已经存在');
			exit;
		}
		if((preg_match('/[^0-9]/', $phone))||(strlen($phone)!=11)){
			$this->error('手机号码应该是纯数字而且是11位数字');
			exit;
		}
		$uok=(strlen($password) < 6)||(strlen($password)) >20||(preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$password));
		if($uok){
			$this->error('密码不符合规范');
			exit;
		}
		$uok=((strlen($data['user_name']) < 2)||(strlen($data['user_name'])) >30||(!preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/us',$data['user_name'])));
		if($uok){
			$this->error('用户名可由数字、小写字母和下划线组成');
			exit;
		}
		$data['user_reg_date']=date("Y-m-d H:i:s");
		$data['user_reg_ip']=getip();//ip
		$count=$m->add($data);
		if($count>0){
			$user_sid=MD5($data['user_name'].$data['user_password']);
			$user_last_login_time=$m->where($data)->getField('user_login_time');
			$user_login_last_ip=$m->where($data)->getField('user_login_ip');
			$data_s['user_sid']=$user_sid;//sid
			$data_s['user_login_time']=date("Y-m-d H:i:s");//time
			$data_s['user_last_login_time']=$user_last_login_time;//last time
			$data_s['user_login_ip']=getip();//ip
			$data_s['user_login_last_ip']=$user_login_last_ip;//ip
			$m->where($data)->save($data_s);//更新sid以及登陆时间，登陆ip
			//$m->where($data)->getField('user_name');//find name
			cookie('user_sid',$user_sid,2592000);//把sid存进cookie
         		$this->success('注册成功',U('Home/index'),1);
		}else{
			$this->error('注册失败');
			exit;
		}
	}


//ajax返回是否存在此用户
	public function checkuser(){
		if(!isset($_POST['user_name'])){
			exit();
		}
		$u=M('User');
		$user_name=I('post.user_name','','htmlspecialchars');
		$count=$u->where("user_name='$user_name' or user_id='$user_name'")->count();
		$data['status']  = $count;
		$this->ajaxReturn($data);
	}


	//ajax返回是否存在此手机号码
	public function checkphone(){
		if(!isset($_POST['user_phone'])){
			exit();
		}
		$u=M('User');
		$user_phone=I('post.user_phone','','htmlspecialchars');
		$count=$u->where("user_phone='$user_phone'")->count();
		$data['status']  = $count;
		$this->ajaxReturn($data);
	}

		//ajax返回是否存在此邮箱
	public function checkemail(){
		if(!isset($_POST['user_email'])){
			exit();
		}
		$u=M('User');
		$user_email=I('post.user_email','','htmlspecialchars');
		$count=$u->where("user_email='$user_email'")->count();
		$data['status']  = $count;
		$this->ajaxReturn($data);
	}
}

