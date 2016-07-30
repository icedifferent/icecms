var ualist={
    "我当前的UA":navigator.userAgent,
    "自定义UA":"setua",
    "WIN7-谷歌浏览器":"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36",
    "WIN7-IE11" :"Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko",
    "XP-IE8":"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)",
    "XP-IE6":"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)",
    "Android4.2-UC浏览器":"Mozilla/5.0 (Linux; U; Android 4.2.2; zh-CN; LA2-T1 Build/JDQ39) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 UCBrowser/9.9.5.489 U3/0.8.0 Mobile Safari/533.1",
    "Android4.2-QQ浏览器":"Mozilla/5.0 (Linux; U; Android 4.2.2; zh-cn; LA2-T1 Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko)Version/4.0 MQQBrowser/5.5 Mobile Safari/537.36",
    "NokiaN9":"Mozilla/5.0 (MeeGo; NokiaN9) AppleWebKit/534.13 (KHTML, like Gecko) NokiaBrowser/8.5.0 Mobile Safari/534.13",
    "IPhone6":"Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A403 Safari/8536.25"
};
for(var key in ualist){
    document.getElementById("ua").options.add(new Option(key, ualist[key]));
}
function IsURL(str_url){ 
  var strRegex = "^((https|http|ftp|rtsp|mms)?://)"  
    + "?(([0-9a-z_!~*'().&=+$%-]+: )?[0-9a-z_!~*'().&=+$%-]+@)?" //ftp的user@  
    + "(([0-9]{1,3}\.){3}[0-9]{1,3}" // IP形式的URL- 199.194.52.184  
    + "|" // 允许IP和DOMAIN（域名） 
    + "([0-9a-z_!~*'()-]+\.)*" // 域名- www.  
    + "([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\." // 二级域名  
      + "[a-z]{2,6})" // first level domain- .com or .museum  
      + "(:[0-9]{1,4})?" // 端口- :80  
      + "((/?)|" // a slash isn't required if there is no file name  
      + "(/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+/?)$";  
  var re=new RegExp(strRegex);  
  if (re.test(str_url)){ 
    return true;
  }else{
    alert('亲，您输入的源代码地址有误哦~');
    return false;
  }
}