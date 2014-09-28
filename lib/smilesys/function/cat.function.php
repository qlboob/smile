<?php
function cat() {
	$args	=	func_get_args();
	return implode('',$args);
}