<?php
require( SMILE_PATH.'Smile.php');

function getPHPContent($path) {
	$content	=	file_get_contents($path);
	$content	=	trim($content);
	substr($content,-2) != '?>' && $content.='?>';
	return $content;
}

$defaultConf	=	require SMILE_SYS_PATH.'config.php';
Smile::config($defaultConf);
if (file_exists(SMILE_PATH.'config/config.php')) {
	Smile::config(require SMILE_PATH.'config/config.php');
}
$content	=	Smile::config('secureStr');
$content	.=	'<?php Smile::config('.var_export(Smile::config(),TRUE).');?>';

$content	.=	getPHPContent(SMILE_PATH.'Smile.php');

$compressor	=	Smile::getInstance('CompressCode',TRUE);
$content	=	$compressor->compress($content,array('php'=>1));
$content	=	trim($content);
'?>'==substr($content,-2) && $content	=	trim(substr($content,0,-2));
file_put_contents(SMILE_TMP_PATH.'smile_sapp.php',$content);