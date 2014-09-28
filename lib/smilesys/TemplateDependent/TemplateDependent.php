<?php
abstract class TemplateDependent {
	abstract function add($key,$val) ;
	abstract function minus($key='',$val='',$cacheId='',$order=TRUE);
	abstract function getDependence($key,$cacheId='',$order=TRUE);
	protected function trimValue($value) {
		return substr($value,strlen(Smile::config('cacheDir')));
	}
	protected function reValue($value) {
		return Smile::config('cacheDir').$value;
	}
}