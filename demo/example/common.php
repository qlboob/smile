<?php
session_start();
//common include
include '../../lib/index.php';
$smile		=	new Smile();
$configCheckbox	=	array(	'compressHtml', 'compressPhp', 'compressJs', 'compressJsFile', 'exeJs', 'compressCss', 'exeCss' , 'compressCssFile','htmlCache', 'cacheOn', );
$configValue	=	array('cacheTime','cacheid');
if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
	foreach ($configCheckbox as $value) {
		$_SESSION[$value]	=	$_POST[$value]?1:0;
	}
	foreach ($configValue as $value) {
		$_SESSION[$value]	=	$_POST[$value];
	}
	$_POST['delcache'] && Smile::delCache();
}else {
	foreach (array_merge($configCheckbox,$configValue) as $value) {
		isset($_SESSION[$value]) || $_SESSION[$value]	=	Smile::config($value);
	}
}
foreach (array_merge($configCheckbox,$configValue) as $value) {
	Smile::config($value,$_SESSION[$value]);
	$smile->assign($value,$_SESSION[$value]);
}
$tplFile	=	Smile::getTemplateFilePath($tpl,$_SESSION['cacheid']);
$cacheFile	=	Smile::getCacheFilePath($tpl,$_SESSION['cacheid']);
$smile->assign('tplFile',$tplFile);
$smile->assign('cacheFile',$cacheFile);
$smile->assign('opt',array("ok"=>1,'no'=>0,'unknown'=>-1));
$smile->assign('values',array('one','two','three'));
$smile->assign('output',array('first','second','third'));