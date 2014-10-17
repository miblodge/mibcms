<?php
	class Authentication {
		public $err = array();

		function __construct() {
			if(!$this->logged_in) {
				// check if we need to redirect to a login page...
				if(MIB_LOGIN_REDIRECT != '') {
					//echo '<pre>'.print_r($this,true).'</pre>';
					header('Location: '.MIB_LOGIN_REDIRECT, true, 307);
				}
			}
		}

		function __get($name) {
			switch($name) {
				case 'id':
				case 'userid':
				case 'username':
				case 'name':
				case 'display_name':
					return '';
					break;
				case 'banned':
					return false;
				case 'logged_in':
					return false;
					break;
				case 'config_owner':
					$owner_ids = explode(',',MIB_ARCHON_USERID);
					foreach($owner_ids as $owner_id) if($owner_id == $this->userid) return true;
					$owner_names = explode(',',MIB_ARCHON_USERNAME);
					foreach($owner_names as $owner_name) if($owner_name == $this->username) return true;
					return false;
					break;
				case 'config_admin':
					if($this->config_owner) return true;
					$admin_ids = explode(',',MIB_HANDLER_USERID);
					foreach($admin_ids as $admin_id) if($admin_id == $this->userid) return true;
					$admin_names = explode(',',MIB_HANDLER_USERNAME);
					foreach($admin_names as $admin_name) if($admin_name == $this->username) return true;
					return false;
					break;
				case 'error':
					if(empty($this->err)) return false;
					else return true;
					break;
				case 'errors':
					return $this->err;
					break;
			}
		}

		function checkLogin($params = array()) {
			// Override in any auth class that can handle a login form submission and start a session...
			$message = array('warning' => 'Direct login not supported.');
			return $message;
		}


		/* Event hooks */
		function onLoggedIn() {
			// Override in any auth class that can tell page what to do when user logged in.
			// i.e. Add a menu item to logout.
			$actions = array();
			return $actions;
		}

		function onNotLoggedIn() {
			// Override in any auth class that can tell page what to do when user not logged in.
			// i.e. Provide content objects such as a login form.
			$actions = array();
			return $actions;
		}

		function getDisplayNameByID($db,$id) {
			return 'Anon';
		}
	}
?>
