<?php
//define the smile path
defined ( 'SMILE_PATH' ) || define ( 'SMILE_PATH', str_replace ( '\\', '/', dirname ( __FILE__ ) ) . '/' );

//define the tmp path
defined('SMILE_TMP_PATH') || define('SMILE_TMP_PATH',SMILE_PATH.'tmp/');

//define the system path
defined('SMILE_SYS_PATH') || define('SMILE_SYS_PATH',SMILE_PATH.'smilesys/');

if (file_exists(SMILE_TMP_PATH.'smile_sapp.php')) {
	require SMILE_TMP_PATH.'smile_sapp.php';
}else {
	require SMILE_SYS_PATH.'run.php';
}