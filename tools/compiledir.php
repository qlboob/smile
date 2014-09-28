<?php
/**
* usage
* php -f __FILE__ {被编译的目录} {输出目录} [{被编译的文件}] [{输出文件的扩展名}]
 */
error_reporting(E_ALL & ~E_NOTICE);
$pwd = dirname ( __FILE__ );
require "$pwd/../lib/index.php";
require "$pwd/../lib/smilesys/function/writeFile.function.php";

$templateDir = '';
$outputDir = '';
$ext = '';

#test
//$argv = array("$pwd/../lib/template","$pwd/../lib/tmp",'php');
if (!empty($argv)) {
	#命令行方式运行，把第一个参数当模板目录，第二个参数当输出目录
	list($file,$templateDir,$outputDir,$complieFile,$ext) = $argv;
}elseif(!empty($_REQUEST)){
	$templateDir = $_REQUEST['in'];
	$outputDir = $_REQUEST['out'];
	$ext = $_REQUEST['ext'];
}else {
	exit('input arg error');
}
if ( !empty($complieFile) ) {
	if ( !is_file($complieFile) ) {
		$ext = $complieFile;
	}
}

if ( !$templateDir || !is_dir($templateDir)) {
	exit('template dir is error');
}elseif(!$outputDir or !is_dir($outputDir)){
	exit('output dir is error');
}else{
	$templateDir = realpath($templateDir);
	$outputDir = realpath($outputDir);
}
if ( empty($ext) ) {
	$ext = 'php';
}

/**
 * 编译目录
 **/
function smile_compileDir($templateDir,$outputDir,$ext){
	$smile = new Smile();
	if ( $handle = opendir($templateDir) ) {
		#读取目录
		while ( $file=readdir($handle) ) {
			if ( in_array($file[0],array('.','_')) ) {
				continue;
			}
			$outName = preg_replace('#\.\w+$#',".$ext",$file);
			$templateFile = "$templateDir/$file";
			$outputFile = "$outputDir/$outName";
			if ( is_dir($templateFile) ) {
				#递归编译子目录
				smile_compileDir("$templateDir/$file","$outputDir/$file",$ext);
			}elseif(is_file($templateFile)) {
				if ( file_exists($outputFile) and !is_writable($outputFile) ) {
					#跳过不能写的文件
					continue;
				}
				$arrTpl = explode('.', $templateFile);
				$tplExt = end($arrTpl);
				Smile::config('tplSuffix',".$tplExt");
				$compliedContent = $smile->compile("$templateFile",'',array('cacheTime'=>-1000000000));
				writeFile($outputFile,trim($compliedContent));
				echo "$outputFile \n";
			}
		}
	}
}
/**
 * 编译一个文件
 */
function smile_compileFile($templateDir,$outputDir,$templateFile,$ext){
	$templateFile = realpath($templateFile);
	$outName = preg_replace('#\.\w+$#',".$ext",$templateFile);
	$outputFile = str_ireplace($templateDir,$outputDir,$outName);
	if ( file_exists($outputFile) and (!is_writable($outputFile) or filemtime($outputFile)> filemtime($templateFile) )) {
		#跳过不能写的文件
		return ;
	}
	$arrTpl = explode('.', $templateFile);
	$tplExt = end($arrTpl);
	Smile::config('tplSuffix',".$tplExt");
	$fileContent = file_get_contents($templateFile);
	$fileContent = "<taglib form>\n".$fileContent;
	$smile = new Smile();
	$compliedContent = $smile->compile($fileContent);
	writeFile($outputFile,$compliedContent);
	echo "$outputFile \n";
}

Smile::config('secureStr','');
Smile::config('TMPL_VAR_IDENTIFY','array');
Smile::config('tplDir',$templateDir.'/');

if ( empty($complieFile) or !is_file($complieFile) ) {
	smile_compileDir($templateDir,$outputDir,$ext);
}else {
	smile_compileFile($templateDir,$outputDir,$complieFile,$ext);
}
