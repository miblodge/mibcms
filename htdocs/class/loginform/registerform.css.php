<?php
	$url = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
	$url .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

	define('BASE_PATH',realpath('./../../..')); // For use in PHP if needed
	define('BASE_URL', $url); // For use in client output (css and js includes for example).

	define('MIB_SPECIAL_PAGE_CLASS',true);
	require_once(BASE_PATH.'/config/config.php');
	includeClass('class_colourscheme.php');

	$colours = new ColourScheme();

	header("Content-type: text/css");
?>
.login-form {
	margin-left: 5px;
	margin-bottom: 20px;
}
.login-form-name {
	display: inline;
}
.login-form-pswd {
	display: inline;
}
.login-form-button {
	display: inline;
}
