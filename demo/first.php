<?php
include '../lib/Smile.php';
$smile		=	new Smile();

/*$testvar	=	'n|testvar';
$smile->assign('testvar',$testvar);
$date	=	array('g'=>1);
$smile->assign('data',$date);
$smile->display('first',1);
$smile->display('first',2);
$smile->display('htmlcache');*/
$smile->display('nohtmlcache','',array('htmlcahce'=>TRUE));