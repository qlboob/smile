<?php
$tpl	=	'present';
include 'common.php';
$smile->assign('var','It\'s var');
$smile->assign('title','Present');
$smile->display($tpl);