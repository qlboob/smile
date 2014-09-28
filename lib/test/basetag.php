<?php
/*require '../smilesys/tag/BaseTag.php';

$attr	=	array(
	'g="g" c	="  fds"',
	"g='g' c='ff'",
	"g=g c= ff ",
	'fffffff s f',
);
$basetag = new BaseTag();
foreach ($attr as $v) {
	$result	=	$basetag->getAttrArray($v);
	var_dump($result);
}*/

class Preg {
	
	function exe() {
//		$str	=	file_get_contents(__FILE__);
		$str	=	'{It\' \\\' my \\"job"} {good\'s live} {ks"fs}';
		$stripStr	=	stripslashes($str);
		$re	=	preg_replace('/\{(.+?)\}/eis',"\$this->test('\\1')",$str);
	}
	
	function test($str) {
		$tmp	=	stripslashes($str);
	}
}
$std	=	new Preg();
$std->exe();