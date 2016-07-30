<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
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
		$this->assign('status_status',$this->status_arr[0]['status_status']);
		$this->assign('status_title',$this->status_arr[0]['status_title']);
		$this->assign('status_describe',$this->status_arr[0]['status_describe']);
		$this->assign('status_announce',$this->status_arr[0]['status_announce']);
		$this->assign('status_ddos',$this->status_arr[0]['status_ddos']);
		$this->assign('status_ddos_times',$this->status_arr[0]['status_ddos_times']);
		$this->assign('reg_method',$this->status_arr[0]['reg_method']);
		$this->assign('status_key',$this->status_arr[0]['status_key']);
		$this->assign('r_integral',$this->status_arr[0]['r_integral']);
		$this->assign('s_integral',$this->status_arr[0]['s_integral']);
		$this->assign('r_money',$this->status_arr[0]['r_money']);
		$this->assign('s_money',$this->status_arr[0]['s_money']);
		$this->assign('f',$this->status_arr[0]['f_maxsize']);
		$this->assign('website_foot',$this->status_arr[0]['website_foot']);
		$this->assign('admin_name',$this->admin_name);
		$Version='V 4.3.1';
		$this->assign('Version',$Version);
		$v=file_get_contents("http://icecms.cn/version.php");
		$readme=file_get_contents("http://icecms.cn/readme.txt");
		$readme = iconv("gb2312","utf-8",$readme);
		$v = iconv("gb2312","utf-8",$v);
		$this->assign('mes',"{$v}");
		$this->assign('readme',"{$readme}");
		//待审文章数目
		$A=M('Article');
		$article_c=$A->where('article_status=3')->count();
		$this->assign('article_c',$article_c);

			//检测版块
		$b=M('Article_board');
		$board_arr_=$b->select();//版块数组
		$this->assign('a_board_arr_',$board_arr_);

			//检测版块
		$b=M('Bbs_board');
		$board_arr_=$b->select();//版块数组
		$this->assign('b_board_arr_',$board_arr_);



     	        $this->display('index');
	}

	public function change(){
		$data['status_status']=I('post.status_status','','htmlspecialchars');	
		$data['status_title']=I('post.status_title','','htmlspecialchars');	
		$data['status_describe']=I('post.status_describe','','htmlspecialchars');	
		$data['status_announce']=I('post.status_announce','','htmlspecialchars');	
		$data['status_ddos']=I('post.status_ddos','','htmlspecialchars');	
		$data['status_ddos_times']=I('post.status_ddos_times','','htmlspecialchars');	
		$data['reg_method']=I('post.reg_method','','htmlspecialchars');	
		$data['status_key']=I('post.status_key','','htmlspecialchars');	
		$data['r_integral']=I('post.r_integral','','htmlspecialchars');	
		$data['s_integral']=I('post.s_integral','','htmlspecialchars');	
		$data['s_money']=I('post.s_money','','htmlspecialchars');	
		$data['r_money']=I('post.r_money','','htmlspecialchars');	
		$data['f_maxsize']=I('post.f','','htmlspecialchars');	
		$data['website_foot']=$_POST['website_foot'];
		$a=M('Website');
		$count=$a->where('status_id=1')->save($data);
		if($count>0){
			$this->success("修改成功");
		}else{
			$this->error("修改失败");
		}
	}


	public function change_post(){
		$id=(int)I('post.id','','htmlspecialchars');
		if($id=='')
			$id=(int)I('get.id','','htmlspecialchars');
		$p=M('Bbs_post');
		$count=$p->where("post_id='$id'")->count();
		if(!$count){
			$this->error("帖子不存在");
		}
		$p_arr=$p->where("post_id='$id'")->select();
		$this->assign('post_title',$p_arr[0]['post_title']);
		$this->assign('post_content',$p_arr[0]['post_content']);
		$this->assign('post_board_id',$p_arr[0]['post_board_id']);
		$this->assign('post_hot',$p_arr[0]['post_hot']);
		$this->assign('post_status',$p_arr[0]['post_status']);
		$this->assign('post_id',$p_arr[0]['post_id']);
	      	 $this->display();
	}

	public function change_post_do(){
		$id=I('post.id','','htmlspecialchars');
		if($id==''){
			$id=(int)I('get.id','','htmlspecialchars');
			$data['post_status']=I('get.post_status','','htmlspecialchars');	
			$a=M('Bbs_post');
			$count=$a->where("post_id='$id'")->save($data);
			if($count>0){
				$this->success("删除成功","",1);
			}else{
				$this->error("修改失败");
			}
		}else{
			$data['post_title']=I('post.post_title','','htmlspecialchars');	
			//$data['post_content']=I('post.post_content','','htmlspecialchars');
			$data['post_content']=$_POST['post_content'];
			$data['post_board_id']=I('post.post_board_id','','htmlspecialchars');	
			$data['post_hot']=I('post.post_hot','','htmlspecialchars');	
			$data['post_status']=I('post.post_status','','htmlspecialchars');	
			$a=M('Bbs_post');
			$count=$a->where("post_id='$id'")->save($data);
			if($count>0){
				$this->success("修改成功","index",1);
			}else{
				$this->error("修改失败");
			}
		}
	}


	public function change_article(){
		$id=(int)I('post.id','','htmlspecialchars');
		if($id=='')
			$id=(int)I('get.id','','htmlspecialchars');
		$p=M('Article');
		$count=$p->where("article_id='$id'")->count();
		if(!$count){
			$this->error("文章不存在");
		}
		$p_arr=$p->where("article_id='$id'")->select();
		$this->assign('article_title',$p_arr[0]['article_title']);
		$this->assign('article_content',$p_arr[0]['article_content']);
		$this->assign('article_board_id',$p_arr[0]['article_board_id']);
		$this->assign('article_hot',$p_arr[0]['article_hot']);
		$this->assign('article_status',$p_arr[0]['article_status']);
		$this->assign('article_praise_times',$p_arr[0]['article_praise_times']);
		$this->assign('article_lookdown_times',$p_arr[0]['article_lookdown_times']);
		$this->assign('article_id',$p_arr[0]['article_id']);
	      	 $this->display();
	}


	public function change_article_do(){
		$id=I('post.id','','htmlspecialchars');
		if($id==''){
			$id=(int)I('get.id','','htmlspecialchars');
			$data['article_status']=I('get.article_status','','htmlspecialchars');	
			$a=M('Article');
			$count=$a->where("article_id='$id'")->save($data);
			if($count>0){
				$this->success("状态更改成功","",1);
			}else{
				$this->error("状态更改失败");
			}
		}else{
			$data['article_title']=I('post.article_title','','htmlspecialchars');	
			//$data['article_content']=I('post.article_content','','htmlspecialchars');
			$data['post_content']=$_POST['article_content'];	
			$data['articleboard_id']=I('post.article_board_id','','htmlspecialchars');	
			$data['article_hot']=I('post.article_hot','','htmlspecialchars');	
			$data['article_status']=I('post.article_status','','htmlspecialchars');	
			$data['article_praise_times']=I('post.article_praise_times','','htmlspecialchars');	
			$data['article_lookdown_times']=I('post.article_lookdown_times','','htmlspecialchars');	
			$a=M('Article');
			$count=$a->where("article_id='$id'")->save($data);
			if($count>0){
				$this->success("修改成功","index",1);
			}else{
				$this->error("修改失败");
			}
		}
	}



	public function change_user(){
		$id=I('post.id','','htmlspecialchars');
		if($id=='')
			$id=I('get.id','','htmlspecialchars');
		$p=M('User');
		$p_arr=$p->where("user_id='$id'")->select();
		$this->assign('user_name',$p_arr[0]['user_name']);
		$this->assign('user_email',$p_arr[0]['user_email']);
		$this->assign('user_phone',$p_arr[0]['user_phone']);
		$this->assign('user_sex',$p_arr[0]['user_sex']);
		$this->assign('user_status',$p_arr[0]['user_status']);
		$this->assign('user_img',$p_arr[0]['user_img']);
		$this->assign('user_character',$p_arr[0]['user_character']);
		$this->assign('user_violations_content',$p_arr[0]['user_violations_content']);
		$this->assign('user_money',$p_arr[0]['user_money']);
		$this->assign('user_password',$p_arr[0]['user_password']);
		$this->assign('user_integral',$p_arr[0]['user_integral']);
		$this->assign('user_rank',$p_arr[0]['user_rank']);
		//$this->assign('user_zone',$p_arr[0]['user_zone']);
		$this->assign('user_id',$p_arr[0]['user_id']);
	      	 $this->display();
	}


	
	public function change_user_do(){
		$id=I('post.id','','htmlspecialchars');
		$data['user_name']=I('post.user_name','','htmlspecialchars');	
		$data['user_email']=I('post.user_email','','htmlspecialchars');
		$data['user_phone']=I('post.user_phone','','htmlspecialchars');
		$data['user_sex']=I('post.user_sex','','htmlspecialchars');
		$data['user_status']=I('post.user_status','','htmlspecialchars');
		$data['user_img']=I('post.user_img','','htmlspecialchars');
		$data['user_character']=I('post.user_character','','htmlspecialchars');
		$data['user_violations_content']=I('post.user_violations_content','','htmlspecialchars');
		$data['user_money']=I('post.user_money','','htmlspecialchars');
		$data['user_password']=I('post.user_password','','htmlspecialchars');
		$data['user_integral']=I('post.user_integral','','htmlspecialchars');
		$data['user_rank']=I('post.user_rank','','htmlspecialchars');
		$data['user_zone']=I('post.user_zone','','htmlspecialchars');
		$u=M('User');
		$count=$u->where("user_id='$id'")->save($data);
		if($count>0){
			$this->success("修改成功","index",1);
		}else{
			$this->error("修改失败");
		}
	}


	public function sql(){
		include 'conn.php';
		if(isset($_POST['sql'])){
			$sql=$_POST['sql'];
			dump($_POST);
			$count=mysql_query($sql);
			dump($count);
			if($count){
				$this->success("执行成功","index",1);
			}else{
				echo mysql_errno().mysql_error();
				$this->error("执行失败");
			}
		}else{
			$this->error("没有语句");
		}
	}
	public function mysql(){
		$this->assign('title',"MYSQL的备份以及还原");
		$this->display();	
	}
	public function backup(){
		header('content-type:text/html;charset=utf-8');
		include 'conn.php';
		$sign=I('post.sign','','htmlspecialchars');
		if($sign){
		//	dump($_FILES);
			if (isset ($_FILES['sqlFile']) ) { 
				$url=$_FILES['sqlFile']["tmp_name"];
				$sql=file_get_contents($url); //把SQL语句以字符串读入$sql 
				$a=explode("\r\n",$sql); //用explode()函数把$sql字符串以“;”分割为数组 
			//	dump($a);
				foreach($a as $b){ //遍历数组 
					mysql_query($b); //执行SQL语句 
				}
				echo	$mes5= "<br>导入完成！"; 
			}
		}else{
			//数据库数据备份
			//数据库中有哪些表
			$tables = mysql_list_tables($cfg_dbname);
			//将这些表记录到一个数组
			$tabList = array();
			while($row = mysql_fetch_row($tables)){
				$tabList[] = $row[0];
			}		
			echo "运行中，请耐心等待...<br/>";
			$info = "-- ----------------------------\r\n";
			$info .= "-- 日期：".date("Y-m-d H:i:s",time())."\r\n";
			$info .= "-- Power by ICECMS(http://www.icecms.cn)\r\n";
			$info .= "-- 仅用于测试和学习,本程序不适合处理超大量数据\r\n";
			$info .= "-- ----------------------------\r\n\r\n";
			$to_file_name = "icecms".time().".sql";
			file_put_contents($to_file_name,$info,FILE_APPEND);
			//将每个表的表结构导出到文件
			foreach($tabList as $val){
				$sql = "show create table ".$val;
				$res = mysql_query($sql,$conn);
				$row = mysql_fetch_array($res);
				$info = "-- ----------------------------\r\n";
				$info .= "-- Table structure for `".$val."`\r\n";
				$info .= "-- ----------------------------\r\n";
				$info .= "DROP TABLE IF EXISTS `".$val."`;\r\n";
				$sqlStr = $info.$row[1].";\r\n\r\n";
				//追加到文件
				file_put_contents($to_file_name,$sqlStr,FILE_APPEND);
				//释放资源
				mysql_free_result($res);
			}

			//将每个表的数据导出到文件
			foreach($tabList as $val){
				$sql = "select * from ".$val;
				$res = mysql_query($sql,$conn);
				//如果表中没有数据，则继续下一张表
				if(mysql_num_rows($res)<1) continue;
					//
					$info = "-- ----------------------------\r\n";
					$info .= "-- Records for `".$val."`\r\n";
					$info .= "-- ----------------------------\r\n";
					file_put_contents($to_file_name,$info,FILE_APPEND);
					//读取数据
					while($row = mysql_fetch_row($res)){
					$sqlStr = "INSERT INTO `".$val."` VALUES (";
					foreach($row as $zd){
						$sqlStr .= "'".$zd."', ";
					}
					//去掉最后一个逗号和空格
					$sqlStr = substr($sqlStr,0,strlen($sqlStr)-2);
					$sqlStr .= ");\r\n";
					file_put_contents($to_file_name,$sqlStr,FILE_APPEND);
				}
				//释放资源
				mysql_free_result($res);
				file_put_contents($to_file_name,"\r\n",FILE_APPEND);
			}	
			echo "OK成功备份<br/>!<a href=/{$to_file_name}>点击下载回来</a>";		
		}
	}
	public function add_bbs_board(){
		$data['bbs_board_name']=I('post.name','NULL','htmlspecialchars');	
		$m=M('Bbs_board');
		$count=$m->add($data);
		if($count>0){
			$this->success("增加成功","index",1);
		}else{
			$this->error("增加失败");
		}
	}



	public function change_bbs_board(){
		$data['bbs_board_name']=I('post.name','NULL','htmlspecialchars');
		$id=I('post.id','NULL','htmlspecialchars');		
		$m=M('Bbs_board');
		$count=$m->where("bbs_board_id='$id'")->save($data);
		if($count>0){
			$this->success("修改成功","index",1);
		}else{
			$this->error("修改失败");
		}
	
	}



	public function add_article_board(){
		$data['article_board_name']=I('post.name','NULL','htmlspecialchars');	
		$m=M('Article_board');
		$count=$m->add($data);
		if($count>0){
			$this->success("增加成功","index",1);
		}else{
			$this->error("增加失败");
		}
	}


	public function change_article_board(){
		$data['article_board_name']=I('post.name','NULL','htmlspecialchars');
		$id=I('post.id','NULL','htmlspecialchars');	
		$m=M('Article_board');
		$count=$m->where("article_board_id='$id'")->save($data);
		if($count>0){
			$this->success("修改成功","index",1);
		}else{
			$this->error("修改失败");
		}
	
	}



	//待审核文章
	public function article_list(){
		$data['article_status'] =3;
		$m=M('Article');
		$count=$m->where($data)->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->order('article_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$this->assign('title',"待审核文章");
		$this->display();
	}

	//所有文章
	public function article_lists(){
	//	$data['article_status'] =3;
		$m=M('Article');
		$count=$m->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->order('article_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$this->assign('title',"待审核文章");
		$this->display();
	}

	//所有帖子
	public function post_list(){
		//$data['article_status'] =3;
		$m=M('Bbs_post');
		$count=$m->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->order('post_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$this->assign('title',"所有帖子");
		$this->display();
	}

	//所有用户
	public function user_list(){
		//$data['article_status'] =3;
		$m=M('user');
		$count=$m->count();
		$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(10)
		$show = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $m->where($data)->limit($Page->firstRow.','.$Page->listRows)->order('user_id desc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);
		$this->assign('title',"用户列表");
		$this->display();
	}


	//后台增加文章
	
	public function add_article(){
		//检测版块
		$b=M('Article_board');
		$board_arr_=$b->select();//版块数组
		$this->assign('board_arr_',$board_arr_);
		$this->display();
	}

	public function add_article_do(){
		//$title=I('post.title','','htmlspecialchars');
		//$content=I('post.content','','htmlspecialchars');
		$title=$_POST['title'];
		$content=$_POST['content'];
		$board_id=(int)I('post.board_id','','htmlspecialchars');
			if ((substr($title, 90, 1) != '')||($title=='')) {
			$this->error('标题不能为空或超过30个字！！');
			exit();
		}
		if ((substr($content, 1500000, 1) != '')||($content=='')) {
			$this->error('发表的内容不能为空！或超过50000字!');
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
		$data['article_title']=$title;
		$data['article_content']=$content;
		$data['article_ip']=getip();//ip
		$data['article_board_id']=$board_id;
		$data['article_read_time']=date('Y-m-d H:i:s');
		$data['article_respond_time']=date('Y-m-d H:i:s');
		$data['article_date']=date('Y-m-d H:i:s');
		$data['article_ower_id']=1;
		$data['article_ower_name']="ICECMS运营组";
		$data['article_status']=1;
		$data['article_hot']=rand(5,200);
		$m=M('Article');
		$count=$m->add($data);
		if($count>0){
			$this->success("投稿成功",U('Index/index'),1);
		}else{
			$this->error("投稿失败");
		}
	}


	public function change_admin_pwd(){
		$data['admin_password']=MD5(I('post.content','','htmlspecialchars'));
		$m=M('Admin');
		$count=$m->where("admin_name='admin'")->save($data);
		if($count>0){
			$this->success("修改成功",U('Index/index'),1);
		}else{
			$this->error("修改失败");
		}
	}
}
