<?php
$tpl	=	'clear';
include 'common.php';
$smile->assign('title','Clear Cache');
Smile::delCache();
$smile->display($tpl);