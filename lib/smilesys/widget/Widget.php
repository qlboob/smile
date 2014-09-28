<?php
if (! class_exists ( 'Widget' )) {
	class Widget {
		protected $tpVar = array ();
		protected $template;
		protected $cacheId;
		protected $param = array ();
		protected $_data = array ();
		
		function __construct($param = NULL) {
			if (is_array ( $param )) {
				foreach ( $param as $k => $v ) {
					$this->$k = $v;
				}
			}
		}
		
		function render() {
			if (! Smile::checkCache ( $this->template, $this->cacheId, $this->params )) {
				$st = Smile::getInstance ( 'SmileTemplate', TRUE );
				$st->load ( $this->template, $this->tpVar, $this->cacheId, $this->params );
			}
			$cachePath = Smile::getCacheFilePath ( $this->template, $this->cacheId, $this->params );
			ob_start ();
			ob_implicit_flush ( 0 );
			extract ( $this->tpVar );
			include $cachePath;
			return ob_get_clean ();
		}
		
		function __get($key) {
			return $this->_data [$key];
		}
		
		function __set($key, $value) {
			$this->_data [$key] = $value;
		}
		
		function assign($name, $val = '') {
			if (is_string ( $name )) {
				$this->tpVar [$name] = $val;
			} else {
				$this->tpVar = array_merge ( $this->tpVar, $name );
			}
		}
	}
}