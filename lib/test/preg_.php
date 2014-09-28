<?php
$str	=	">	
	
<7";

echo preg_replace('/>[\r\n\t]+</','><',$str);