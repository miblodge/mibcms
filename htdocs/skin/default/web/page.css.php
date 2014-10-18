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
body {
	background: <?php echo $colours->bg_main; ?>;
	color: <?php echo $colours->txt_main; ?>;
}

a {
	color: <?php echo $colours->link; ?>;
}
a:visited {
	color: <?php echo $colours->link_visited; ?>;
}
a:hover {
	color: <?php echo $colours->link_hover; ?>;
}
a:active {
	color: <?php echo $colours->link_active; ?>;
}

table th {
	background: <?php echo $colours->alt_bg_second; ?>;
	font-weight: bold;
	text-align: center;
}

table td {
	background: <?php echo $colours->alt_bg_main; ?>;
	padding-left: 20px;
	padding-right: 20px;
}

table a {
	color: <?php echo $colours->link; ?>;
}
table a:visited {
	color: <?php echo $colours->link_visited; ?>;
}
table a:hover {
	color: <?php echo $colours->link_hover; ?>;
}
table a:active {
	color: <?php echo $colours->link_active; ?>;
}

#page_container {
	border: 0px;
	background: <?php echo $colours->bg_second; ?>;
	-webkit-border-radius: 15px;
	-moz-border-radius: 15px;
	border-radius: 15px;
	margin: 5px;
	padding: 5px;
}

#main_content {
	border: 1px solid <?php echo $colours->bg_fourth; ?>;
	background: <?php echo $colours->bg_third; ?>;
	-webkit-border-radius: 9px;
	-moz-border-radius: 9px;
	border-radius: 9px;
	padding: 5px;
}

#credits {
	border: 1px solid <?php echo $colours->bg_fourth; ?>;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	background: <?php echo $colours->bg_third; ?>;
	font-size: 0.8em;
	margin: 5px;
	padding: 5px;
	margin-top: 50px;
}

.wtg_message {
	color: <?php echo $colours->msg_txt; ?>;
	background: <?php echo $colours->msg_bg; ?>;
	border: 1px solid <?php echo $colours->msg_border; ?>;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}

.wtg_warning {
	color: <?php echo $colours->warn_txt; ?>;
	background: <?php echo $colours->warn_bg; ?>;
	border: 1px solid <?php echo $colours->warn_border; ?>;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}
