<?php
/**
 * make directories
 * @param string $dir directory name
 * @param string $mode mode
 */
if (!function_exists('mk_dir')) {
	function mk_dir($dir, $mode = 0755) {
		if (is_dir ( $dir ) || @mkdir ( $dir, $mode ))
			return true;
		if (! mk_dir ( dirname ( $dir ), $mode ))
			return false;
		return @mkdir ( $dir, $mode );
	}
}
/**
 * write file
 * @param $filePath file path
 * @param $content file content
 */
if (!function_exists('writeFile')) {
	function writeFile($filePath, $content) {
		$dir = dirname ( $filePath );
		mk_dir ( $dir );
		file_put_contents ( $filePath, $content );
	}
}