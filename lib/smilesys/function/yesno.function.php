<?php
if (!function_exists('yesno')) {
function yesno($str) {
	if ($str) {
		return 'Yes';
	}
	return 'No';
}
}
