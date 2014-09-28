<?php
$tpl	=	'empty';
include 'common.php';
$smile->assign('var','It\'s var');
$smile->assign('title','Empty');
$smile->display($tpl);