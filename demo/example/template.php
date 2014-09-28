<?php
$tpl	=	$_GET['t'];
$tpl	=	str_replace('..','',$tpl);
include 'common.php';
$smile->assign('title',ucwords($_GET['t']));
$smile->assign('var','It\'s var content');
$smile->assign('g',"It's a string");
$smile->assign('data',array('g'=>3,2,3,4,5));
$smile->fetch($tpl,$_SESSION['cacheid']);
$smile->display('template');