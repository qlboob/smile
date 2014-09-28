<?php
$tpl	=	'if';
include 'common.php';
$smile->assign('_if','if_string');
$smile->assign('title','If-else-elesif');
$smile->assign('var',4);
$smile->display($tpl);