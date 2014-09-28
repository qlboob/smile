<?php
$rootDir	=	$_SERVER['DOCUMENT_ROOT'];
//$rootDir	=	str_replace('\\','/',$rootDir);
//echo $rootDir;
$file		=	dirname(__FILE__);
$file		=	str_replace('\\','/',$file);
$script		=	substr($file,strlen($rootDir));
$minifyArr	=	explode('/',$script);
$minifyArr	=	array_slice($minifyArr,0,count($minifyArr)-1);
$minifyUrl	=	implode('/',$minifyArr).'/vendor/min/';
//var_dump($minifyArr);

return array(
	//template file directory
	'tplDir'		=>	SMILE_PATH.'template/',
	//the template cache directory
	'cacheDir'		=>	SMILE_TMP_PATH.'cache/',
	//the tag library directory
	'tagDir'		=>	SMILE_SYS_PATH.'tag/',
	//the function directory
	'functionDir'	=>	SMILE_SYS_PATH.'function/',
	'widgetDir'	=>	SMILE_SYS_PATH.'widget/',
	//user config file path
	'configFile'	=>	SMILE_PATH.'config/config.php',
	//template cahce enable
	'cacheOn'		=>	TRUE,
	//template cahce time
	'cacheTime'		=>	-1,
	//tag begin character
	'tagBegin'		=>	'<',
	//tag end character
	'tagEnd'		=>	'>',
	//var begin character
	'varBegin'		=>	'{',
	//var end character
	'varEnd'		=>	'}',
	//auto include tag lib
	'autoTags'		=>	'Smile',
	//compile first tags
	'firstTags'		=>	'First',
	//tag nest 
	'nested'		=>	3,
	
	//template file suffix
	'tplSuffix'		=>	'.php',
	//template cache file suffix
	'cacheSuffix'	=>	'.php',
	//forece include function
	'forceFuntion'	=>	array(),
	'forceWidget'	=>	array(),
	
	//compress php code
	'compressPhp'	=>	FALSE,
	//compress html code
	'compressHtml'	=>	FALSE,
	'compressJs'	=>	FALSE,
	'compressJsFile'	=>	FALSE,
	'compressCss'	=>	FALSE,
	//secure string
	'secureStr'		=>	"<?php defined('SMILE_PATH')||die();?>",
	//delete dependence when delete cache file
	'deleteDependence'=>FALSE,
	//dependence type
	'dependenceType'	=>	'File',

	'jsDir'				=>	$rootDir,
	'cssDir'			=>	$rootDir,

	//use minify
	'combine'			=>	FALSE,
	//the minify url
	'minifyURL'			=>	$minifyUrl,
	//minify groups config file path
	'minifyGroupsConfigFilePath'=>	dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'min'.DIRECTORY_SEPARATOR.'groupsConfig.php',
	'combinejs'			=>	FALSE,
	'combinecss'		=>	FALSE,
);