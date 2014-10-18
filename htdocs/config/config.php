<?php
	session_start();

	// Get local and default settings.
	includeConfig('local.php'); // Can put local.php in /mod/ or /config/ it doesn't really matter
	includeConfig('default.php'); //Possible to have a /mod/default.php but why when local.php does same thing?
	//for best practise, avoid using /mod/default.php in most cases.

	$db = null;
	// Get db class
	includeClass('class_db.php');

	// Get authorisation class based on settings
	require_once(BASE_PATH.'/auth/'.MIB_AUTH.'/auth.php');

	// get page class
	if(!defined('MIB_SPECIAL_PAGE_CLASS')) {
		includeClass('class_page.php');
		$page = new Page($db,$auth);
	}

//debug(BASE_URL);
	// Otherwise the calling page is responsible for creating $page

	// Some useful common functions to use throughout the site.
	function includeConfig($config_filename) {
		require_once(getFileToUse($config_filename));
	}

	function includeClass($class_filename) {
//debug($class_filename);
		require_once(getFileToUse($class_filename,'/class/'));
	}

	function checkUserInput($name, $default='') {
		foreach($_REQUEST as $key => $value) {
			if($key == $name) {
				if(is_integer($default)) {
					settype($value, 'integer');
				} elseif(is_bool($default)) {
					return ($value != '' and strtolower($value) != 'false');
				}
				return $value;
			}
		}
		return $default;
	}

	function getFileToUse($filename,$apath='/config/') {
		//Given a filename and an ordered list of paths, return the first path
		//for which the file exists...
		$paths = array();
		$paths[] = '/mod/'; //Always check mod folder first, to allow overrides

		if(is_array($apath)) {
			foreach($apath as $path) $paths[] = $path;
		} else {
			$paths[] = $apath;
		}

		foreach($paths as $path) {
			if(file_exists(BASE_PATH.$path.$filename)) {
				// if file exists at this location, use it
				//require_once($path.$filename);
				return BASE_PATH.$path.$filename;
			}
		}
		return false;
	}

	function debug() {
		// only output stuff if debugging is switched on.  Just so we don't
		// accidentally display stuff on a production server.
		if(MIB_DEBUG) {
			$prefix = '';
			for($i = 0 ; $i < func_num_args(); $i++) {
				$arg = func_get_arg($i);
				if(is_object($arg) or is_array($arg)) {
					if(is_object($arg)) {
						// Make sure we don't accidentally display things we shouldn't
						// during debug operations...
					
						if(isset($arg->pswd)) {
							$arg->pswd = '*******************';
							$temp_pswd = $arg->pswd;
						}
					}
					echo '<pre>'.$prefix.print_r($arg,true).'</pre>';
					$prefix = '';
					if(is_object($arg)) {					
						if(isset($arg->pswd)) {
							$arg->pswd = $temp_pswd;
						}
					}
				} else $prefix .= $arg;
			}
			echo '<pre>'.$prefix.'</pre>';
		}
	}

