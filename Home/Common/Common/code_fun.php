	<?php
#####################
#
#code高亮
#
######################

require_once 'geshi.php';
function code($matches)
{	
//$matches[1]= html_entity_decode( $matches[1] );
$language='php';
trim($matches[1]);
$geshi=new GeSHi(htmlspecialchars_decode($matches[1] ), $language);
$geshi->set_header_type(GESHI_HEADER_DIV);
$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS); 
$html = $geshi->parse_code();
 // return $html;
return "<div style='font-size:12px;'>".$html."</div>";
}
