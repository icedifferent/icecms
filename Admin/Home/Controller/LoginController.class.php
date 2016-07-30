<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
	private $status_arr=array();
	public function __construct(){
		parent::__construct();
		$m=M('Website');
		$this->status_arr=$m->where('status_id=1')->select();
	}
	public function index(){
		$this->assign('title',$this->status_arr[0]['status_title']);//分配网站标题
		$this->assign('key',$this->status_arr[0]['status_key']);//分配网站关键字
		$this->assign('describe',$this->status_arr[0]['status_describe']);//分配网站描述
 		$this->display('index');
	}

	public function do_login(){
		$data['admin_name']=I('post.admin_name','','htmlspecialchars');
		$data['admin_password']=MD5(I('post.admin_password','','htmlspecialchars'));
		$m=M('Admin');
		$count=$m->where($data)->count();
		//是否开启验证码
		//默认开启
		//检验
		dump($_POST);
		$verify = new \Think\Verify();
		$ok= $verify->check($_POST['code'],'');
		if(!$ok){
			$this->error('验证码错误');
			exit;
		}
		if($count>0){
			$admin_sid=MD5($data['admin_name'].$data['admin_password']);
			$admin_last_login_time=$m->where($data)->getField('admin_login_time');
			$admin_login_last_ip=$m->where($data)->getField('admin_login_ip');
			$data_s['admin_sid']=$admin_sid;//sid
			$data_s['admin_login_time']=date("Y-m-d H:i:s");//time
			$data_s['admin_last_login_time']=$admin_last_login_time;//last time
			$data_s['admin_login_ip']=getip();//ip
			$data_s['admin_login_last_ip']=$admin_login_last_ip;//ip
			$m->where($data)->save($data_s);//更新sid以及登陆时间，登陆ip
			//$m->where($data)->getField('admin_name');//find name
			cookie('admin_sid',$admin_sid,3600000);//把sid存进cookie
         		$this->success('欢迎你',U('Index/index'),1);
		}else{
			$this->error('登陆失败');
			exit;
		}
	}


   	 public function logout(){
		 cookie('admin_sid',null);//退出删除cookie
		 $this->success('退出成功',U('Index/index'),2);
	 }
}

