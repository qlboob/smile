<?php
if (!function_exists('widget')) {
	function widget($name,$param) {
		$widget	=	new $name;
		return $widget->render();
	}
	abstract class Widget {
		protected $tpVar	=	array();
		protected $template;
		protected $cacheId;
		protected $param	=	array();
		protected $_data	=	array();
		
		function __construct($param=NULL) {
			if (is_array($param)) {
				foreach ($param as $k => $v)
				{
					$this->$k = $v;
				}
			}
		}
		
		function render() {
			$smile	=	new Smile();
			$smile->assign($this->tpVar);
			return $smile->fetch($this->template,$this->cacheId,$this->param);
		}
		
		function __get($key) {
			return $this->_data[$key];
		}
		
		function __set($key,$value){
			$this->_data[$key]	=	$value;
		}
	}
}