<?php
$tpl	=	'Delete Cache';
include 'common.php';
$smile->delCache($_POST['template'],$_POST['cacheid']);