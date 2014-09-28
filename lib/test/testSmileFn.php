<?php
//define the smile path
defined ( 'SMILE_PATH' ) || define ( 'SMILE_PATH', str_replace ( '\\', '/', dirname ( __FILE__ ) ) . '/../' );

//define the tmp path
defined('SMILE_TMP_PATH') || define('SMILE_TMP_PATH',SMILE_PATH.'tmp/');

//define the system path
defined('SMILE_SYS_PATH') || define('SMILE_SYS_PATH',SMILE_PATH.'smilesys/');
require '../Smile.php';
require SMILE_SYS_PATH.'common.php';
$SmileTemplate	=	Smile::getInstance('SmileTemplate');
//var_dump($SmileTemplate);
$SmileTemplate2	=	Smile::getInstance('SmileTemplate');

Smile::config('g.g.g.t','test');

echo Smile::config('g.g.g.t');