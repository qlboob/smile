<?php
//define the smile path
//defined ( 'SMILE_PATH' ) || define ( 'SMILE_PATH', str_replace ( '\\', '/', dirname ( __FILE__ ) ) . '/' );

//define the tmp path
//defined('SMILE_TMP_PATH') || define('SMILE_TMP_PATH',SMILE_PATH.'tmp/');

//define the system path
//defined('SMILE_SYS_PATH') || define('SMILE_SYS_PATH',SMILE_PATH.'smilesys/');

/**
 * @author Luke.qin
 * make you smile
 *
 */
class Smile {
	
	//template variable
	protected $tpVar = array ();
	
	
	/**
	 * assign value to template
	 * @param string $name
	 * @param mixed $val
	 */
	function assign($name, $val = '') {
		if (is_string($name)) {
			$this->tpVar[$name]	=	$val;
		}else {
			$this->tpVar	=	array_merge($this->tpVar,$name);
		}
	}
	
	/**
	 * display the template
	 * @param string $template template file
	 * @param string $cacheId cache id
	 * @param array $params other parameters
	 */
	function display($template, $cahceId = NULL, $params = array()) {
		$this->fetch ( $template, $cahceId, array_merge($params,array ('display' => TRUE )) );
	}
	
	/**
	 * display or fetch the template
	 * @param string $template template file
	 * @param string $cacheId cache id
	 * @param array $params other parameters
	 */
	function fetch($template, $cacheId = NULL, $params = array()) {
		isset($params['display']) || $params['display'] = FALSE;
		if (!self::checkCache($template,$cacheId,$params)) {
			$this->compile($template,$cacheId,$params);
		}
		$cachePath	=	Smile::getCacheFilePath($template,$cacheId,$params);
		if (isset($params['display']) && !$params['display']) {
			ob_start();
			ob_implicit_flush(0);
		}
		
		extract($this->tpVar);
		include $cachePath;
		if (isset($params['display']) && !$params['display']) {
			return ob_get_clean();
		}
	}
	/**
	 * 
	 * compile template
	 * @param string $template
	 * @param string|array $cacheId
	 * @param array $params
	 */
	function compile($template,$cacheId,$params) {
// 		if (!self::checkCache($template,$cacheId,$params)) {
			$st	=	self::getInstance('SmileTemplate',TRUE);
			return $st->load($template,$this->tpVar,$cacheId,$params);
// 		}
	}
	
	
	/**
	 * get template file path
	 * @param string $template template file
	 * @param string $cacheId cache id
	 * @param array $params other parameters
	 * @return string template file path
	 */
	static function getTemplateFilePath($template, $cacheId = NULL, $params = NULL) {
		if (file_exists($template)) {
			return $template;
		}
		$path	=	Smile::config('tplDir').str_replace(array(':',''),'/',$template).Smile::config('tplSuffix');
		if (strpos($path,'\\')) {
			$path	=	str_replace('\\','/',$path);
		}
		return $path;
	}
	
	/**
	 * check cache is avirable
	 * @param string $template
	 * @param string $cacheId
	 * @param array $params
	 * @return boolean
	 */
	static function checkCache($template, $cacheId = NULL, $params = NULL) {
		if (!Smile::config('cacheOn')) {
			return FALSE;
		}
		$cacheFile	=	self::getCacheFilePath($template, $cacheId , $params);
		$tplFile	=	self::getTemplateFilePath($template, $cacheId , $params);
		$cacheTime	=	isset($params['cacheTime'])?$params['cacheTime']:Smile::config('cacheTime');
		if (!file_exists($cacheFile)) {
			return FALSE;
		}elseif (filemtime($tplFile) > filemtime($cacheFile)){// template file updated
			return FALSE;
		}elseif ($cacheTime!=-1 && filemtime($cacheFile)+$cacheTime>time()){//out time cache
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * delete cache template files
	 * @param string $template
	 * @param string $cacheId
	 * @param array $params
	 */
	static function delCache($template=NULL, $cacheId = NULL, $params = NULL) {
		self::requireOnce(SMILE_PATH.'/smilesys/smiledeletecache.php');
		smileDeleteCache($template,$cacheId,$params,$this);
	}
	
	/**
	 * get the cache file path
	 * @param stirng $template
	 * @param mixed $cacheId
	 * @param array $params
	 * @return string cache file path
	 */
	static function getCacheFilePath($template, $cacheId = NULL, $params = NULL) {
		$template	=	self::getTemplateFilePath($template, $cacheId , $params );
		$ret	=	'';
		if($cacheId){
			if(is_string($cacheId))
			{
				$ret	.=	$cacheId.'/';
			}else {
				if ($cacheId['dir']) {
					if (is_array($cacheId['dir'])) {
						$ret	.=	implode('/',$cacheId['dir']).'/';
					}else {
						$ret	.=	$cacheId['dir'].'/';
					}
				}
				if ($cacheId['id']) {
					if (is_array($cacheId['id'])) {
						$ret	.=	implode('.',$cacheId['id']).'.';
					}else {
						$ret	.=	$cacheId['id'].'.';
					}
				}
			}
		}
		$ret =Smile::config('cacheDir').$ret.basename($template,Smile::config('tplSuffix')).Smile::config('cacheSuffix');
		if (strpos($ret,"\\")) {
			$ret	=	str_replace("\\",'/',$ret);
		}
		return $ret;
	}
	
	/**
	 * get Template file var;
	 * @param string $name
	 * @return multitype:
	 */
	function get($name) {
		return $this->tpVar[$name];
	}
	
	/**
	 * the same as require_once function
	 * @param string $path the required file path
	 * @return void
	 */
	static function requireOnce($path) {
		static $_cache	=	array();
		if (!isset($_cache[$path])) {
			require $path;
			$_cache[$path]=TRUE;
		}
	}
	
	/**
	 * get class instance
	 * @param string $className the class name | the class and class path
	 * @param boolean $newInstance whether create a new instance
	 * @param mixed $params
	 * @return Object
	 */
	static function getInstance($className,$newInstance=FALSE,$param=NULL) {
		static $_class	=	array();
		
		if (strpos($className, ',')){ //className contains a path
			$aClass		=	explode(',', $className);
			$className	=	$aClass[0];
			$requiredPath=	$aClass[1];
		}else { //the system path
			$path	=	array(
				'SmileTemplate'	=>	SMILE_SYS_PATH.'SmileTemplate.php',
				'CompressCode'	=>	SMILE_SYS_PATH.'CompressCode.php',
			);
			$requiredPath = $path[$className];
		}
		if (!$newInstance && isset($_class[$className])) {
			return $_class[$className];
		}
		self::requireOnce($requiredPath);
		if ($param) {
			$cls	=	new $className ($param);
		}else {
			$cls	=	new $className;
		}
		$_class[$className]	=	$cls;
		return $cls;
	}
	
	/**
	 * get/set config
	 * @param mixed $name
	 * @param mixed $value
	 */
	static function config($name=NULL,$value=NULL) {
		static $_config	=	array();
		if (is_null($name)) {
			return $_config;
		}
		if (is_string($name)) {
			$name	=	strtolower($name);
			if (! strpos ( $name, '.' )) {
				if (is_null($value))
					return isset($_config[$name])?$_config[$name]:null;
				$_config[$name]	=	$value;
				return;
			}
			$name	=	explode('.', $name);
			$config	=	&$_config;
			$confKey=	array_pop($name);
			foreach ($name as $k) {
				if (!isset($config[$k])) {
					$config[$k]	=	array();
				}
				$config	=	&$config[$k];
			}
			if (is_null ( $value ))
				return isset($config[$confKey])?$config[$confKey]:NULL;
			$config[$confKey]	=	$value;
			return;
		}
		if (is_array($name)) {
			return $_config = array_merge ( $_config, array_change_key_case ( $name ) );
		}
		return NULL;
	}
}