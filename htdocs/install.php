<?php
	$url = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
	$url .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

	define('BASE_PATH',realpath('.')); // For use in PHP if needed
	define('BASE_URL', $url); // For use in client output (css and js includes for example).

	define('MIB_SPECIAL_PAGE_CLASS','true'); // For use in PHP if needed
	require_once(BASE_PATH.'/config/config.php');

	includeClass('page_installation.php');
//debug($db,$auth);
//die();
	$page = new InstallationPage($db,$auth);
//debug($page);
	$page->renderPage();

