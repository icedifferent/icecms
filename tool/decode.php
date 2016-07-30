<?php
require 'include/header.php';
error_reporting(0); 
$selected = ' selected="selected"';
switch($_POST['type']){
    case 'urldecode':
        $text = urldecode($_POST['text']);
        $urldecode = $selected;
    break;
    case 'urlencode':
        $text = urlencode($_POST['text']);
        $urlencode = $selected;
    break;
    case 'jsondecode':
        if(!($text = json_decode($_POST['text']))){
            $text = "请输入合格的JSON内容！";
        }
        $jsondecode = $selected;
    break;
    case 'jsonencode':
        $text = json_encode($_POST['text']);
        $jsonencode = $selected;
    break;
    case 'htmldecode':
        $text = html_entity_decode($_POST['text'],ENT_QUOTES,'UTF-8');
        $htmldecode = $selected;
    break;
    case 'htmlencode':
        $text = htmlentities($_POST['text'],ENT_QUOTES,'UTF-8');
        $htmlencode = $selected;
    break;
    case 'base64decode':
        $text = base64_decode($_POST['text']);
        if(!$text){
            $text = "请输入合格的BASE64内容！";
        }
        $base64decode = $selected;
    break;
    case 'base64encode':
        $text = base64_encode($_POST['text']);
        $base64encode = $selected;
    break;
    case 'asciitostr':
        $text = chr($_POST['text']);
        if(empty($text)){
            $text = "ascii参数可以是十进制、八进制或十六进制。通过前置 0 来规定八进制，通过前置 0x 来规定十六进制。\r52=>4,052=>*,0x52=>R";
        }
        $asciitostr = $selected;
    break;
    case 'md5':
        $text = md5($_POST['text']);
        $md5 = $selected;
    break;
    case 'timedecode':
        if(is_numeric($_POST['text'])){
            $text = date('Y-m-d H:i:s',substr($_POST['text'],0,10));
        }else{
            $text = '请输入正确的时间戳！';
        }
        $timedecode = $selected;
    break;
    case 'nowtime':
        $text = time();
        $nowtime = $selected;
    break;
    default:
    $text = '处理结果区';
}
title('编码解码器','URL编解码、JSON编解码、BASE64编解码、时间戳转换、MD5加密 ');
?>
<form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
<textarea style="height: 150px;" name="text" placeholder="请输入要处理的内容！"></textarea><br />
<select name="type">
    <option value="urldecode"<?=$urldecode?>>URL解码</option>
    <option value="urlencode"<?=$urlencode?>>URL编码</option>
    <option value="jsondecode"<?=$jsondecode?>>JSON解码</option>
    <option value="jsonencode"<?=$jsonencode?>>JSON编码</option>
    <option value="htmldecode"<?=$htmldecode?>>HTML实体转字符</option>
    <option value="htmlencode"<?=$htmlencode?>>字符转HTML实体</option>
    <option value="base64decode"<?=$base64decode?>>base64解码</option>
    <option value="base64encode"<?=$base64encode?>>base64编码</option>
    <option value="timedecode"<?=$timedecode?>>时间戳转日期</option>
    <option value="nowtime"<?=$nowtime?>>当前时间戳</option>
    <option value="asciitostr"<?=$asciitostr?>>ASCII转字符</option>
    <option value="md5"<?=$md5?>>md5加密</option>
</select>
<input type="submit" name="submit" value="确认"/><input type="reset" value="清空输入框"/>
</form>
<!-- 结果区 -->
<pre><?php print_r($text); footer(); ?></pre>

