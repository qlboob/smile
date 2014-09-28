<?php
if (!function_exists('ul')) {
	function ul($menu) {
		$ret	=	'<ul>';
		foreach ($menu as $v) {
			$v['node'] && $ret.="<li>".li($v['node'],$v['href'])."</li>";
			$v['children'] && $ret.=ul($v['children']);
		}
		
		$ret	.=	'</ul>';
		return $ret;
	}
	function li($node,$li) {
		if ($li) {
			return '<a href="'.$li.'">'.$node.'</a>';
		}
		return $node;
	}
}