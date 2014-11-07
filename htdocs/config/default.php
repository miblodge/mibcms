<?php
	// Settins here override the default settings

/**************************************************************************

These settings can be overriden by local.php, so these values are only
used if they aren't defined in local.php first.

***************************************************************************/

	// Site title
	if(!defined('MIB_SITE_TITLE')) DEFINE('MIB_SITE_TITLE', 'MIB CMS');

	// Auth module to use.
	if(!defined('MIB_AUTH')) DEFINE('MIB_AUTH', 'self');

	// Default skin and view to use...
	if(!defined('MIB_SKIN')) DEFINE('MIB_SKIN', 'default');
	if(!defined('MIB_VIEW')) DEFINE('MIB_VIEW', 'web');

	// Post validation
	// HTMLPurifier last updated 10/2014. If that is old, check for updates manually.
	// It is not recommended to turn this off.
	if(!defined('MIB_POST_HTMLPURIFIER')) DEFINE('MIB_POST_HTMLPURIFIER', true);

	// Post formatting options
	// JBBCODE last updated 10/2014. If that is old, check for updates manually.
	if(!defined('MIB_POST_JBBCODE')) DEFINE('MIB_POST_JBBCODE', true);
	// WIKIRENDER last updated 10/2014. If that is old, check for updates manually.
	if(!defined('MIB_POST_WIKIRENDERER')) DEFINE('MIB_POST_WIKIRENDERER', true);
	// PARSEDOWN not yet installed. This constant exists because I plan to install it.
	if(!defined('MIB_POST_PARSEDOWN')) DEFINE('MIB_POST_PARSEDOWN', false);

	// Various other config options
	if(!defined('MIB_PW_SALT')) DEFINE('MIB_PW_SALT', 'AM5TGUN6Z7H6HS1K23ERIS5');
	if(!defined('MIB_DB_PREFIX')) DEFINE('MIB_DB_PREFIX', 'MIB_');

	// Debug mode for testing environments. Leave as false to suppress
	// debug output.
	if(!defined('MIB_DEBUG')) DEFINE('MIB_DEBUG', false);

/***************************************************************************

Following settings are not defined by default and are included in this
file only to explain what can be configured in local.php 

****************************************************************************/
	// Database connection settings MUST be set locally
	if(!defined('MIB_DB_HOST')) DEFINE('MIB_DB_HOST', '');
	if(!defined('MIB_DB_USER')) DEFINE('MIB_DB_USER', '');
	if(!defined('MIB_DB_PWD')) DEFINE('MIB_DB_PWD', '');
	if(!defined('MIB_DB_SCHEMA')) DEFINE('MIB_DB_SCHEMA', '');

	if(!defined('MIB_LOGIN_REDIRECT')) DEFINE('MIB_LOGIN_REDIRECT', '');

	// For security reasons, the default values for the following
	// Owner and Admin constants are blank and so have to be set
	// in local.php if you want to use them...

	// Optional Owner username or userid is always owner even if not set as such in database
	if(!defined('MIB_ARCHON_USERNAME')) DEFINE('MIB_ARCHON_USERNAME', '');
	if(!defined('MIB_ARCHON_USERID')) DEFINE('MIB_ARCHON_USERID', '');
	if(!defined('MIB_ARCHON_PASSWORD')) DEFINE('MIB_ARCHON_PASSWORD', ''); // Only used in setup
	if(!defined('MIB_ARCHON_EMAIL')) DEFINE('MIB_ARCHON_EMAIL', ''); // Only used in setup

	// Optional Admin usernames or userids are always set as admins even if not set as
	// such in database.  Comma delimited list
	if(!defined('MIB_HANDLER_USERNAME')) DEFINE('MIB_ARCHON_USERNAME', '');
	if(!defined('MIB_HANDLER_USERID')) DEFINE('MIB_ARCHON_USERID', '');

