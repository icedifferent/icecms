<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>HTML在线可视化练习</title>
    <script type="text/javascript">
    var editboxHTML = 
        '<html class="expand close">' +
        '<head>' +
        '<style type="text/css">' +
        '.expand { width: 100%; height: 100%; }' +
        '.close { border: none; margin: 0px; padding: 0px; }' +
        'html,body { overflow: hidden; }' +
        '<\/style>' +
        '<\/head>' +
        '<body class="expand close" onload="document.f.ta.focus(); document.f.ta.select();">' +
        '<form class="expand close" name="f">' +
        '<textarea class="expand close" style="background: #C7E8C4;" name="ta" wrap="hard" spellcheck="false">' +
        '<\/textarea>' +
        '<\/form>' +
        '<\/body>' +
        '<\/html>';
    
    var defaultStuff = '<h3>Hello World!<\/h3>\n' +
        '<p>你好,世界!<\/p>';

    var extraStuff = '<div style="position:absolute; margin:.3em; bottom:0em; right:0em;"><small><a href="index.php">返回首页</a></small></div>';
    
    var old = '';
             
    function init(){
        window.editbox.document.write(editboxHTML);
        window.editbox.document.close();
        window.editbox.document.f.ta.value = defaultStuff;
        update();
    }
    function update(){
        var textarea = window.editbox.document.f.ta;
        var d = dynamicframe.document; 
    
        if(old != textarea.value){
            old = textarea.value;
            d.open();
            d.write(old);
            if (old.replace(/[\r\n]/g,'') == defaultStuff.replace(/[\r\n]/g,''))
                d.write(extraStuff);
            d.close();
        }
        window.setTimeout(update, 150);
    }
    </script>
</head>
<frameset resizable="yes" rows="50%,*" onload="init();">
  <!-- about:blank confuses opera, so use javascript: URLs instead -->
  <frame name="editbox" src="javascript:'';">
  <frame name="dynamicframe" src="javascript:'';">
</frameset>
</html>