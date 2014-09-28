<?php
$tpl	=	'nohtmlcache';
include 'common.php';
$smile->assign('title',ucwords($tpl));
$smile->assign('var','It\'s var content');
$smile->assign('g',"It's a string");
$smile->assign('data',array('g'=>3,2,3,4,5));
$smile->display($tpl,NULL,array('htmlcache'=>TRUE));