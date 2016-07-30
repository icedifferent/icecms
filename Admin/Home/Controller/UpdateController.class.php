<?php
namespace Home\Controller;
use Think\Controller;
class UpdateController extends Controller {
	private $admin_sid='';
	private $admin_id='';
	private $status_arr=array();
	private $admin_arr=array();
	public function __construct(){
		parent::__construct();
		$m=M('Website');
		$this->status_arr=$m->where('status_id=1')->select();
		if(isset($_COOKIE['admin_sid'])){
			$this->admin_sid=htmlspecialchars($_COOKIE['admin_sid']);
			$m=M('Admin');
			$count=$m->where("admin_sid='$this->admin_sid'")->count();
			if($count>0){
				$this->admin_arr=$m->where("admin_sid='$this->admin_sid'")->select();
				$this->admin_name=$this->admin_arr[0]['admin_name'];
				$this->admin_id=$this->admin_arr[0]['admin_id'];
			}else{	
				$this->error('请先登录',U('Login/index'),1);
				eixt();
			}
		}else{
				$this->error('请先登录',U('Login/index'),1);
				eixt();
		}
	}


	public function index(){
		header("Content-type:text/html;charset=utf-8");
		echo "正在升级....<br />";
		if(!function_exists("zip_open")) {
			die("需开启配置 php_zip.dll才能升级");
		}
		if(!function_exists("file_get_contents")) {
			die("需开启配置支持file_get_contents函数才能升级");
		}
		$up=file_get_contents("http://icecms.cn/update.zip");
		$fp2=fopen(dirname(dirname(dirname(dirname(__FILE__)))).'/update/update.zip','w');
    		fwrite($fp2,$up);
    		fclose($fp2);
		$size = get_zip_originalsize(dirname(dirname(dirname(dirname(__FILE__)))).'/update/update.zip',dirname(dirname(dirname(dirname(__FILE__)))).'/');
	//	echo @file_get_contents("http://icecms.cn/readme.txt");
	//	$this->display();
	}
}

