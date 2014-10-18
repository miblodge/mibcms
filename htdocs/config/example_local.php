<?php
	// Copy this file (example_local.php) to local.php and edit local settings

	// Settings here override the default settings
	//if(!defined('MIB_PW_SALT')) DEFINE('MIB_PW_SALT', '');

	// Database settings must be set locally
	if(!defined('MIB_DB_HOST')) DEFINE('MIB_DB_HOST', 'localhost');
	if(!defined('MIB_DB_USER')) DEFINE('MIB_DB_USER', '');
	if(!defined('MIB_DB_PWD')) DEFINE('MIB_DB_PWD', '');
	if(!defined('MIB_DB_SCHEMA')) DEFINE('MIB_DB_SCHEMA', '');

	// Optional Owner username or userid is always owner even if not set as such in database
	if(!defined('MIB_ARCHON_USERNAME')) DEFINE('MIB_ARCHON_USERNAME', '');
	if(!defined('MIB_ARCHON_USERID')) DEFINE('MIB_ARCHON_USERID', '');

	// Optional Admin usernames or userids are always set as admins even if not set as
	// such in database.  Comma delimited list
	if(!defined('MIB_HANDLER_USERNAME')) DEFINE('MIB_HANDLER_USERNAME', '');
	if(!defined('MIB_HANDLER_USERID')) DEFINE('MIB_HANDLER_USERID', '');

	// Auth module to use
	//if(!defined('MIB_AUTH')) DEFINE('MIB_AUTH', 'self');

	// If not logged in, redirect to url (leave blank for no redirect)...
	if(!defined('MIB_LOGIN_REDIRECT')) DEFINE('MIB_LOGIN_REDIRECT', '');

	// On a development server, set this to true to see debugging output
	//if(!defined('MIB_DEBUG')) DEFINE('MIB_DEBUG', false);

	// Post formatting options
	// JBBCODE last updated 10/2014. If that is old, check for updates manually.
	if(!defined('MIB_POST_JBBCODE')) DEFINE('MIB_POST_JBBCODE', true);
	// WIKIRENDER last updated 10/2014. If that is old, check for updates manually.
	if(!defined('MIB_POST_WIKIRENDERER')) DEFINE('MIB_POST_WIKIRENDERER', true);
	// PARSEDOWN not yet installed. This constant exists because I plan to install it.
	if(!defined('MIB_POST_PARSEDOWN')) DEFINE('MIB_POST_PARSEDOWN', false);
