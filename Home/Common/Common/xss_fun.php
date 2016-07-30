<?php
#过滤字符的合法情况~~
#@2015.5.26
#
#

function xss($content,$what){
	if($what=='user_name'){
	//匹配用户名是否为中文
	//不能为空而且大于3个字符长度
	if(!preg_match('/^[\w\x80-\xff]{3,15}$/', $content)&&$content=''&&strlen($content)<=3){
		return 0;
		}	
	}else if($what=='user_email'){
	if(!preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $content)){
	return 0;
	}
		}else if($what=='user_phone'){
			if(preg_match('/[^0-9]/', $content)||$content=''||strlen($content)<=5){
				return 0;
				}
					}else if($what=='user_password'){
					if((strlen($content)<6)||(!preg_match("/^[0-9a-zA-Z]+$/",$content))){
						return 0;
						}
			}
		return 1;
	}
	
	

?>