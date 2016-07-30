<?php
/**
 * 零零网 ll00.cn
 * UPDATE: 2014-11-30
 * 请保留此处的版权信息
 * 如果可以，也请保留页面的版权信息
 * 目前只能查看utf8的网页不乱码
**/
// 公共头部文件
require 'include/header.php';
// 网页head
title('网页源代码查看');
// 判断用户提交的地址是否合法
if(filter_var($_POST['url'],FILTER_VALIDATE_URL)){
    echo '<div class="title">网页源代码-<a href="?">主界面</a><!-- -< a href="javascript:void(0);" onclick="viewhtml(this,false)">高亮开关</a> --></div></div>';
    // 引入Sonnpy类
    require_once 'Snoopy.class.php';
    // 初始Sonnpy类
    $snoopy = new Snoopy;
    // 设置获取远程网页超时时间
    $snoopy->read_timeout=5;
    // 设置来源地址
    $snoopy->referer = $_POST['referer'];
    // 设置模拟cookie
    $snoopy->rawheaders["COOKIE"] = $_POST['cookie'];
    // 判断设置ua
    if($_POST['ua'] == 'my'){
        $snoopy->agent = $_SERVER['HTTP_USER_AGENT'];
    }elseif(empty($_POST['ua'])){
        $snoopy->agent = $_POST['uas'];
    }else{
        $snoopy->agent = $_POST['ua'];
    }
    // 判断是否模拟提交post
    if(empty($_POST['post'])){
        // 判断显示内容
        switch($_POST['text']){
            case 'text':
                // 只显示文字
                $snoopy->fetchtext($_POST['url']);
            break;
            case 'form':
                // 只显示表单
                $snoopy->fetchform($_POST['url']);
            break;
            case 'links':
                // 只显示链接
                $snoopy->fetchlinks($_POST['url']);
            break;
            default:
                // 显示完整源代码
                $snoopy->fetch($_POST['url']);
        }
    }else{
        $p = explode('&',$_POST['post']);
        foreach($p as $po){
            $pos = explode('=',$po);
            $post[$pos[0]] = $pos[1];
        }
        switch($_POST['text']){
            case 'text':
                $snoopy->submittext($_POST['url'], $post);
            break;
            case 'form':
                $snoopy->submitform($_POST['url'], $post);
            break;
            case 'links':
                $snoopy->submitlinks($_POST['url'], $post);
            break;
            default:
                $snoopy->submit($_POST['url'], $post);
        }
    }
    // 判断获取源代码有无错误
    if(empty($snoopy->error)){
        if($_POST['text'] == 'links'){
            $data = implode("\n",$snoopy->results);
        }else{
            $data = $snoopy->results;
        }
        // 判断用户是否指定了源代码的页面编码
        if($_POST['encoding'] && function_exists('mb_convert_encoding')){
            // 把源代码的编码转换为UTF-8
            $data = mb_convert_encoding($data, 'UTF-8', $_POST['encoding']);
        }
        $data = htmlentities($data, ENT_COMPAT, 'UTF-8');
    }else{
        $data = $snoopy->error;
    }
    echo '<!-- 高亮类的样式  -->
    <link href="./prism.css" rel="stylesheet"/>
    <pre style="width:98%"><code class="language-markup" id="viewhtml">'.$data.'</code></pre>
    <!-- 高亮类js文件 -->
    <script src="./prism.js"></script>';
}else{
echo <<<HTML
<style type="text/css">
form {text-align: center; color:#277CDA;}
input[type=text], select {width:85%; border:1px solid #3C88DD; height:25px; color:#3C88DD; border-radius:2px; margin:2px;}
input[type=submit] {background:#3C88DD; color:#fff; width:85%; height:25px; border-radius:2px;}
</style>
<div class="title">网页源代码查看</div>
<form action="viewhtml.php" method="post">
源代码网址<br/>
<input type="text" name="url" value="http://" placeholder="请输入网址,带http://"/><hr/>
UA模拟<br/>
<select name="ua">
    <option value="my">我的UA</option>
    <option value="">自定义UA</option>
    <option value="Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36">WIN 7 - 谷歌浏览器</option>
    <option value="Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko">WIN 7 - IE11</option>
    <option value="Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)">WIN XP - IE8</option>
    <option value="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)">WIN XP - IE6</option>
    <option value="Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0; HTC; Titan)">诺基亚 - WP</option>
    <option value="Mozilla/5.0 (Linux; U; Android 4.2.2; zh-CN; LA2-T1 Build/JDQ39) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 UCBrowser/9.9.5.489 U3/0.8.0 Mobile Safari/533.1">Android4.2 - UC浏览器</option>
</select><br/>
<input type="text" name="myua" placeholder="Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko"/><hr/>
来源地址模拟<br/>
<input type="text" name="referer" placeholder="http://icecms.cn"/><hr/>
POST提交<br/>
<input type="text" name="post" placeholder="user=admin&pass=123456"/><hr/>
COOKIE模拟<br/>
<input type="text" name="cookie" placeholder="user=admin&pass=123456"/><br/>
显示内容<br/>
<select name="text">
    <option value="all">显示原代码</option>
    <option value="text">只显示文字</option>
    <option value="form">只显示表单</option>
    <option value="links">只显示链接</option>
</select><br/>
源网页编码<br/>
<select name="encoding">
    <option>UTF-8</option>
    <option>GBK</option>
    <option>BIG5</option>
    <option>ISO-8859-1</option>
</select><br/>
<input type="submit" value="查看源代码"/>
</form>
HTML;
}
footer();
?>
