<?php
namespace Home\Controller;
use Think\Controller;
class PublicController extends Controller {
	//构造函数生成随机Token，防止跨站攻击
	public function __construct(){
		parent::__construct();
	
	}
	//验证码
	public function code(){
		$config = array(
		'fontSize' => 20, // 验证码字体大小
		'length' => 4, // 验证码位数
		'useNoise' => false, // 关闭验证码杂点
		);
		$Verify = new \Think\Verify($config);
		ob_clean();
		return $Verify->entry();	
	}


	//防止快速刷新函数
	public  function ddos(){
		return 0;
		//待定
		//
	}
	public function temp(){
	//	echo "test";
		if($_COOKIE['think_template']==''){
				cookie('think_template','wap2',2592000);
		}
		if($_COOKIE['think_template']=='wap2'){
			if(!isMobile()){
			//	echo "web";
				cookie('think_template','web',2592000);
			//	header( "HTTP/1.1 301 Moved Permanently"); 
			//	header("Location: /index.php");
			}
		}
		if($_COOKIE['think_template']=='web'){
			if(isMobile()){
				cookie('think_template','wap2',2592000);
			//	echo "mobile";
			//	@header( "HTTP/1.1 301 Moved Permanently"); 
			//	@header("Location: /index.php");
			}
		}
	}

	//生成csrf_Toen的函数
    public function getToken( $len = 32, $md5 = true ) {  
              mt_srand( (double)microtime()*1000000 );  
              $chars = array(  
                  'Q', '@', '8', 'y', '%', '^', '5', 'Z', '(', 'G', '_', 'O', '`',  
                  'S', '-', 'N', '<', 'D', '{', '}', '[', ']', 'h', ';', 'W', '.',  
                  '/', '|', ':', '1', 'E', 'L', '4', '&', '6', '7', '#', '9', 'a',  
                  'A', 'b', 'B', '~', 'C', 'd', '>', 'e', '2', 'f', 'P', 'g', ')',  
                  '?', 'H', 'i', 'X', 'U', 'J', 'k', 'r', 'l', '3', 't', 'M', 'n',  
                  '=', 'o', '+', 'p', 'F', 'q', '!', 'K', 'R', 's', 'c', 'm', 'T',  
                  'v', 'j', 'u', 'V', 'w', ',', 'x', 'I', '$', 'Y', 'z', '*'  
              );  
              $numChars = count($chars) - 1; $token = '';  
              for ( $i=0; $i<$len; $i++ )  
                  $token .= $chars[ mt_rand(0, $numChars) ];  
              if ( $md5 ) {  
                  $chunks = ceil( strlen($token) / 32 ); $md5token = '';  
                  for ( $i=1; $i<=$chunks; $i++ )  
                      $md5token .= md5( substr($token, $i * 32 - 32, 32) );  
                  $token = substr($md5token, 0, $len);  
              } return $token;  
    }  

	//设置csrf_token
	function setToken(){	//随机Token
		$Token=$this->getToken(32,true);
		cookie('csrf_Token',$Token,2592000);
		$this->assign("csrf_token",$Token);
	}
	//验证csrf_token
	public 	function check_Token(){
	//	echo $_COOKIE['csrf_Token'];
	//	echo "<br/>";	
	//	echo $_POST['csrf_token'];
	//	exit();
		if($_COOKIE['csrf_Token']!=$_POST['csrf_token']){
			$this->error('csrf_toekn错误',U('Index/index'),3);
		}

	}
}

