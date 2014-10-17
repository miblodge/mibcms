<?php
	class User {
		public $id = 0;
		public $username = '';

		public $db = null;
		public $auth = null;

		public $displayname = '';
		public $title = '';

		public $role = array();

		function __construct($db,$id = null,$auth = null) {
			$this->db = $db;

			if(isset($auth)) {
				$this->auth = $auth;
	
				$this->username = $auth->username;
				$this->displayname = $auth->name;
				$this->id = $auth->userid;
			} else {
				$this->id = $id;
			}

			if(is_null($this->id)) $this->id = 0;

			if($this->id == 0) $this->createUser();
			else $this->initialiseUser();
		}

		function createUser() {
//debug('User::createUser: ',$username);
			return null;
		}

		function initialiseUser() {
//debug('User::initialiseUser: ',$this);
			return null;
		}

		function getDisplayName($auth) {
			// This is used to get the displayname of users other than the logged in user.
			return $auth->getDisplayNameByID($this->db,$this->id);
		}
	}
?>
