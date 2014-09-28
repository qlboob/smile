<?php
$tpl	=	'var';
include 'common.php';
$smile->assign('title','Var');
$smile->assign('var','It\'s var content');
$smile->assign('data',array('g'=>3));
$smile->display($tpl);