<?php
Smile::requireOnce(SMILE_SYS_PATH.'function/rmdirr.function.php');
function smileDeleteCache($template, $cacheId = NULL, $params = NULL,$smile,$delAssoc=NULL) {
	$dependenceCls	=	'TemplateDependent'.Smile::config('dependenceType');
	$tplDependence	=	Smile::getInstance($dependenceCls.','.SMILE_PATH.'smilesys/TemplateDependent/'.$dependenceCls.'.php');
	$delAssoc===NULL && $delAssoc = Smile::config('deleteDependence');
	
	if ($template || $cacheId) {
		if ($template)
			$tplFilePath	=	$smile->getTemplateFilePath($template,$cacheId,$params);
		$delFiles		=	$tplDependence->getDependence($tplFilePath,$cacheId);
	}else {//delete all cache
		rmdirr(Smile::config('cacheDir'));
		$delAssoc && $tplDependence->minus();
	}
	if (isset($delFiles)){
		foreach ($delFiles as $f) {
			@unlink($f);
			$delAssoc && $tplDependence->minus($tplFilePath,$f);
		}
	}
}

