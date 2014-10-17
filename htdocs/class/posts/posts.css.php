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
.posts {
	width: 90%;
	margin-left: auto;
	margin-right: auto;
	margin-bottom: 20px;
}
.post {
	border: 1px solid #666;
	margin: 5px;
}
.post-header {
	margin: 5px;
	color: #000;
	background: #666;
}
.post-title {
	font-size: 2em;
	display: block;
	text-align: center;
	width: 100%;
}
.post-author {
	margin-right: 50px;
}
.post-date {
	font-size: 0.8em;
	font-family: monospace;
}
.post-body {

}
.reply1 {
	background: #111;
	font-size: 0.9em;
}

.reply1 .post-header {
margin: 5px;
color: #000;
border: #666;
}

.reply1 .post-title {
font-size: 1.1em;
display: block;
text-align: center;
width: 100%;
}

.reply2 {
	background: #222;
	font-size: 0.9em;
}

.reply2 .post-header {
margin: 5px;
color: #000;
border: #666;
}

.reply2 .post-title {
font-size: 1.1em;
display: block;
text-align: center;
width: 100%;
}
