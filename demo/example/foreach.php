<?php
$tpl	=	'foreach';
include 'common.php';
$smile->assign('title','Foreach');
$smile->assign('data',array(1,2,3,4));
$smile->display($tpl);