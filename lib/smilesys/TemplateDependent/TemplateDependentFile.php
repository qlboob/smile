<?php
Smile::requireOnce(SMILE_SYS_PATH.'TemplateDependent/TemplateDependent.php');
Smile::requireOnce(SMILE_SYS_PATH.'function/writeFile.function.php');
class TemplateDependentFile extends TemplateDependent {
	//dependence template
	private $dependence	=	array();
	//secuire string
	private $secureStr;
	//file to write/read
	private $file;
	
	//whether write file
	private $writeFile	=	FALSE;
	
	/**
	 * read template dependence
	 */
	function __construct() {
		$this->file			=	SMILE_TMP_PATH.'smile_de.php';
		$this->secureStr	=	Smile::config('secureStr');
		if (file_exists($this->file)) {
			$this->dependence	=	include $this->file;
		}
	}
	
	/**
	 * add template dependence
	 * @param string $key the included file path 
	 * @param string $val 
	 */
	function add($key,$val) {
		$val	=	$this->trimValue($val);
		if (!isset($this->dependence[$key]) ||
			!is_array($this->dependence[$key]) ||
			!in_array($val,$this->dependence[$key])) {
			$this->dependence[$key][]	=	$val;
			$this->writeFile	=	TRUE;
		}
	}
	
	/**
	 * delete template dependence
	 * @param string $key
	 * @param string $val
	 */
	function minus($key='',$val='',$cacheId='',$order=TRUE) {
		if ($val) {
			$val	=	$this->reValue($val);
		}
		
		if ($key && $val) {
			$valKey	=	array_search($val,$this->dependence[$key]);
			if (FALSE!==$valKey) {
				unset($this->dependence[$key][$valKey]);
			}
		}elseif ($key && $cacheId){//only key && cacheid
			foreach ($this->getDependence($key,$cacheId) as $value) {
				$pos	=	array_search($value,$this->dependence[$key]);
				unset($this->dependence[$key][$pos]);
			}
		}elseif ($key){//only key
			unset($this->dependence[$key]);
		}elseif ($val){//only value
			foreach ($this->dependence as $dkey => $dvalue) {
				$pos	=	array_search($val,$dvalue);
				if (FALSE !== $pos) {
					unset($this->dependence[$key][$pos]);
				}
			}
		}elseif ($cacheId){
			$dependenceArr	=	$this->getDependence($key,$cacheId);
			if ($dependenceArr) {
				foreach ($this->dependence as $dkey => $dvalue) {
					$this->dependence[$dkey]	=	array_diff($dvalue,$dependenceArr);
				}
			}
		}else {//no parameter  delete all
			$this->dependence	=	array();
		}
		$this->writeFile	=	TRUE;
	}
	
	/**
	 * get all cache files path of template
	 * @param string $key
	 * @return array
	 */
	function getDependence($key,$cacheId='',$order=TRUE) {
		$result	=	array();
		if ($key && $cacheId) {
			if (is_array($this->dependence[$key])){
				foreach ($this->dependence[$key] as $value) {
					if ($this->contain($value,$cacheId,$order)) {
						$result[]	=	$value;
					}
				}
			}
		}elseif ($key){
			if ($this->dependence[$key])
				$result	=	$this->dependence[$key];
		}elseif ($cacheId){
			$all	=	array();
			foreach ($this->dependence as $val){
				$all	=	array_merge($all,$val);
			}
			$all	=	array_unique($all);
			foreach ($all as $value) {
				if ($this->contain($value,$cacheId,$order)) {
					$result[]	=	$value;
				}
			}
		}
		return array_map(array(&$this,'reValue'),$result);
		
	}
	
	function contain($string,$cacheId,$order) {
		if (is_string($cacheId)) {
			return $this->containDir($string,$cacheId,$order);
		}elseif (is_array($cacheId)){
			if ($cacheId['dir']) {
				if (FALSE === $this->containDir($string,$cacheId['dir'],$order)) {
					return FALSE;
				}
			}
			if ($cacheId['id']) {
				return  $this->containId($string,$cacheId['id'],$order);
			}
		}
		return TRUE;
	}
	
	function containDir($str,$dir,$order=TRUE) {
		if (is_array($dir)) {
			if ($order) {
				$strDir	=	implode('/',$dir);
				return $this->containDir($str,$strDir);
			}else {
				foreach ($dir as $value) {
					if (!$this->containDir($str,$value)) {
						return FALSE;
					}
				}
				return TRUE;
			}
		}
		//if $dir is a string
		$dir	=	rtrim($dir,'/').'/';
		$pos	=	strpos($str,$dir);
		if (FALSE === $pos) {
			return FALSE;
		}elseif (0 === $pos){
			return TRUE;
		}else {
			return FALSE !== strpos($str,'/'.$dir);
		}
		
	}
	
	function containId($str,$id,$order=TRUE) {
		if (is_array($id)) {
			if ($order) {
				$strId	=	implode('.',$id);
			}else {
				foreach ($id as $value) {
					if (!$this->containId($str,$strId)) {
						return FALSE;
					}
				}
				return TRUE;
			}
		}
		$id		=	rtrim($id,'.').'.';
		$pos	=	strpos($str,$id.'.');
		if (FALSE === $pos) {
			return FALSE;
		}elseif (0 === $pos) {
			return TRUE;
		}else{
			if (FALSE !== strpos($str,'.'.$id.'.') ) {
				return TRUE;
			}elseif ( FALSE !== strpos($str,'/'.$id.'.') ){
				return TRUE;
			}
			return FALSE;
		}
	}
	
	/**
	 * write dependence to file
	 */
	function __destruct() {
		if ($this->writeFile) {
			$arr			=	array_filter($this->dependence,'count');
			$dependenceStr	=	var_export($arr,TRUE);
			$fileContent	=	$this->secureStr."<?php return {$dependenceStr};";
			writeFile($this->file,$fileContent);
		}
	}
	
	
	
}