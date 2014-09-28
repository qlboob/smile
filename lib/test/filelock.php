<?php
$file	=	'../tmp/filelock.lock';
touch($file);
$handle	=	fopen($file,'w');
$handle2	=	fopen($file,'w');
echo $handle2 ?'2':'1';
echo is_writable($file) ?'w':'r';
fclose($handle);
unlink($file);