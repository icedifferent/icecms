<?php
namespace Home\Controller;
use Think\Controller;
class HomeController extends PublicController {	
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
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),3);
			eixt();
		}
		$this->assign('user_phone',$this->user_arr[0]['user_phone']);
		$this->assign('user_sex',$this->user_arr[0]['user_sex']);
		$this->assign('user_reg_date',$this->user_arr[0]['user_reg_date']);
		$this->assign('user_status',$this->user_arr[0]['user_status']);
		$this->assign('user_character',$this->user_arr[0]['user_character']);
		$this->assign('user_money',$this->user_arr[0]['user_money']);
		$this->assign('user_theme_number',$this->user_arr[0]['user_theme_number']);
		$this->assign('user_respond_number',$this->user_arr[0]['user_respond_number']);
		$this->assign('user_integral',$this->user_arr[0]['user_integral']);
		$this->assign('user_rank',$this->user_arr[0]['user_rank']);
		$this->assign('user_login_last_ip',$this->user_arr[0]['user_login_last_ip']);
		$this->assign('user_last_login_time',$this->user_arr[0]['user_last_login_time']);
		$this->assign('user_email',$this->user_arr[0]['user_email']);
		$this->assign('user_id',$this->user_arr[0]['user_id']);
		$this->assign('user_name',$this->user_arr[0]['user_name']);
		$this->assign('user_img',$this->user_arr[0]['user_img']);
		//	$this->assign(,$this->user_arr[0]['']);
		$this->display();
	}



	public function change(){
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),3);
			eixt();
		}
		$this->setToken();//设置Crsf_Token
		$this->assign('user_sex',$this->user_arr[0]['user_sex']);
		$this->assign('user_character',$this->user_arr[0]['user_character']);
		$this->assign('user_email',$this->user_arr[0]['user_email']);
		$this->assign('user_name',$this->user_arr[0]['user_name']);
		$this->assign('user_rank',$this->user_arr[0]['user_rank']);
		$this->display();
	}



	public function change_do(){
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),3);
			eixt();
		}
		if(($this->user_arr['user_status']==8)||($this->user_arr['user_status']==2)){
			$this->error("你已经被禁止所有操作了");
			eixt();
		}
		$this->check_Token();//验证csrf_token
		//$this->setToken();//设置Crsf_Token
		$name=$data['user_name']=I('post.user_name','','htmlspecialchars');
		$password=I('post.user_password','','htmlspecialchars');
		$newpassword=I('post.new_password','','htmlspecialchars');
		$email=$data['user_email']=I('post.user_email','','htmlspecialchars');
		$sex=$data['user_sex']=I('post.user_sex','','htmlspecialchars');
		$rank=I('post.user_rank','','htmlspecialchars');
		$data['user_rank']=sub_str($rank,0,30,0);//截取15字
		$character=I('post.user_character','','htmlspecialchars');
		$data['user_character']=sub_str($character,0,80,0);//截取40字
		$m=M('User');
		//检验信息是否已经存在以及合法性
		if($password!=''){
			if($password!=$newpassword){
				$this->error('两次密码不一致');
				exit;
			}
			$uok=(strlen($password) < 6)||(strlen($password)) >20||(preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$password));
			if($uok){
				$this->error('密码不符合规范');
				exit;
			}
			$data['user_password']=MD5(I('post.user_password','','htmlspecialchars'));
		}

		$count=$m->where("user_name='$name' and user_id!='$this->user_id'")->count();
		if($count>0){
			$this->error('昵称已经存在');
			exit;
		}

		$count=$m->where("user_email='$email' and user_id!='$this->user_id'")->count();
		if($count>0){
			$this->error('邮箱已经存在');
			exit;
		}

		if(($sex=!"男")&&($sex!="女")){
			$this->error('性别错误');
			exit;
		}
		if(!preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',$email)){
			$this->error('电子邮箱格式错误');
			exit;
		}
	
		$uok=((strlen($data['user_name']) < 2)||(strlen($data['user_name'])) >30||(!preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/us',$data['user_name'])));
		if($uok){
			$this->error('用户名不符合规范');
			exit;
		}
		$count=$m->where("user_sid='$this->user_sid'")->save($data);
		if($count>0){
			$this->success('修改成功,建议重新登录');
		}else{
			$this->error('未知错误');
		}
	}

	public function zone(){
		//假冒sid
		//setcookie("user_sid","http://icecms.cn",time()+24*3600);
		$user=M('User');
		if((!isset($_GET['id']))&&(!isset($_GET['name']))){
			//$this->error("非法访问");
			$u=$user->where("user_id='$this->user_id'")->select();
		}
		if(isset($_GET['id'])){
			$user_id=(int)I('get.id','1','htmlspecialchars');
			$u=$user->where("user_id='$user_id'")->select();
		}else{
			$user_name=I('get.name','管理员','htmlspecialchars');
			$u=$user->where("user_name='$user_name'")->select();
		}		
		$this->assign('user_phone',$u[0]['user_phone']);
		$this->assign('user_sex',$u[0]['user_sex']);
		$this->assign('user_reg_date',$u[0]['user_reg_date']);
		$this->assign('user_status',$u[0]['user_status']);
		$this->assign('user_character',$u[0]['user_character']);
		$this->assign('user_money',$u[0]['user_money']);
		$this->assign('user_theme_number',$u[0]['user_theme_number']);
		$this->assign('user_respond_number',$u[0]['user_respond_number']);
		$this->assign('user_integral',$u[0]['user_integral']);
		$this->assign('user_rank',$u[0]['user_rank']);
		$this->assign('user_login_last_ip',$u[0]['user_login_last_ip']);
		$this->assign('user_last_login_time',$u[0]['user_last_login_time']);
		$this->assign('user_email',$u[0]['user_email']);
		$this->assign('user_id',$u[0]['user_id']);
		$this->assign('user_name',$u[0]['user_name']);
		$this->assign('user_img',$u[0]['user_img']);
		$this->display();
	}



	public function change_zone(){
		$this->error("正在开发");
		exit();
		$this->assign('zone',$this->user_arr[0]['user_zone']);
		$this->display('zone');
	}

	public function zone_do(){
		$this->error("正在开发");
		exit();
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),3);
			eixt();
		}
		if(($this->user_arr['user_status']==8)||($this->user_arr['user_status']==2)){
			$this->error("你已经被禁止所有操作了");
			eixt();
		}
		//$content=I('post.content','','htmlspecialchars');
		$data['user_zone']=$_POST['content'];
		$m=M('User');
		$count=$m->where("user_sid='$this->user_sid'")->save($data);
		if($count>0){
			$this->success("修改成功");
		}else{
			$this->error("修改失败");
		}
	}

	public function dark_home(){
		$u=M('User');
		$data['user_status']=array(0,2,3,4,6,7,8,"or");
		$count=$u->where($data)->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$Page->setConfig('header','条数据');
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$Page->setConfig('last','最后一页');
		//$Page->setConfig('theme',"%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%");
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		//$data['post_status']
		$list = $u->where($data)->limit($Page->firstRow.','.$Page->listRows)->order('user_id desc')->select();
		$this->assign('list',$list);//赋值数据集
		$this->assign('page',$show);
		$this->assign('title','小黑屋');
		$this->display();
	}

//找回密码
	public function password(){
		$this->display();
	}




	public function to_password(){
		$user=I('post.user','','htmlspecialchars');
		$email=I('post.email','','htmlspecialchars');
		$u=M('User');
	        $count=$u->where("(`user_name`='$user' or `user_id`='$user') and `user_email`='$email'")->count();
		if(!$count){
				$this->error("信息不一致或者该用户信息不存在");
				exit();
		}
		$token=$data['user_token']=MD5(rand(-3994949,3994949).$email);
		$s=$u->where("(user_name='$user' or user_id='$user') and user_email='$email'")->save($data);
		$sign=find_password($email,$token,$user);
		if($sign&&$s){
			$this->success("找回密码信息已经发送到你的邮箱，请登录邮箱$eamil找回密码");
		}else{
			$this->error("未知错误，请联系管理员");
		}
		//$this->dispaly();
	}


	public function	find_pwdo(){
		$token=I('get.token','','htmlspecialchars');
		$p=time();
		$m=M('User');
		$data['user_password']=MD5($p);
		$c=$m->where("user_token='$token'")->save($data);
		if($c){
			$this->assign('mes',"你的密码暂时被重置为$p,请尽快登录网站修改密码<a href=http://".$_SERVER['SERVER_NAME']."/index.php/Home/Login>点此登录</a>");
			$this->display();
		}else{
			$this->error("未知错误，请联系管理员");
		}
	}



	//修改用户头像
	public function change_user_head(){
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),3);
			eixt();
		}
		$this->assign('user_name',$this->user_arr[0]['user_name']);
		$this->assign('user_img',$this->user_arr[0]['user_img']);
		$this->display();
	}



	public function change_user_head_do(){
		if($this->user_sid==''){
			$this->error('请先登录',U('Login/index'),3);
			eixt();
		}
		if(($this->user_arr['user_status']==8)||($this->user_arr['user_status']==2)){
			$this->error("你已经被禁止所有操作了");
			eixt();
		}
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize = 3145728 ;// 设置附件上传大小
		$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath = './Public/Uploads/user_head/'; // 设置附件上传根目录
		$upload->savePath = ''; // 设置附件上传（子）目录
		// 上传文件
		$info = $upload->upload();
		if(!$info) {// 上传错误提示错误信息
			$this->error($upload->getError());
		}else{
			$u=M('User');
			$data['user_sid']=$this->user_sid;
			$old_img_url=$u->where($data)->getField('user_img');
			if($old_img_url!=''){
				$old_img_url='./Public/Uploads/user_head/'.$old_img_url;
				@unlink($old_img_url);//删除用户之前的头像文件
			}
			$dataf['user_img']=$info['upfile']['savepath'].$info['upfile']['savename'];//img_url
			$u->where($data)->save($dataf);//保存新的头像地址
			// 上传成功
			// 压缩图片,生成缩略图
			$image = new \Think\Image();
			$url='./Public/Uploads/user_head/'.$dataf['user_img'];
			$image->open($url);
			$width=180;
			$height=180;
			$image->thumb($width, $height)->save($url);
			$this->success('更新头像成功！');
		}
		
	}

}

