<?php
$tpl	=	'iterate';
include 'common.php';
$smile->assign('data',array(1,2,3));
$smile->assign('title','Iterate');
$smile->display($tpl);