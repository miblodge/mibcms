<?php
	$url = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
	$url .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

	define('BASE_PATH',realpath('./../..')); // For use in PHP if needed
	define('BASE_URL', $url); // For use in client output (css and js includes for example).

	define('MIB_SPECIAL_PAGE_CLASS',true);
	require_once(BASE_PATH.'/config/config.php');
	includeClass('class_colourscheme.php');

	$colours = new ColourScheme();

	header("Content-type: text/css");
?>
.post-form {
	width: 90%;
	margin-left: auto;
	margin-right: auto;
	margin-bottom: 20px;
	background: #090909;
}
.post-form-name {
	font-size: 1.2em;
}
.post-form-body {
	display: inline;
}
.post-form-body textarea {
	width: 90%;
	height: 30vh;
}