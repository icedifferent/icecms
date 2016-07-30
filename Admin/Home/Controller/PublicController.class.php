<?php
namespace Home\Controller;
use Think\Controller;
class PublicController extends Controller {
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
}
?>
