<?php
	includeClass('class_authentication.php');

	class SelfAuth extends Authentication {
		public $user_data = null;

		function __construct() {
			if(isset($_COOKIE[MIB_SESSION_COOKIE_NAME])) {
				$cookieValue = $_COOKIE[MIB_SESSION_COOKIE_NAME];
//debug('[',$cookieValue,']');
				$this->setUserData($cookieValue);
			}
			parent::__construct();
		}

		function __get($name) {
			switch($name) {
				case 'id':
				case 'userid':
					return $this->user_data['id'];
					break;
				case 'username':
					return $this->user_data['username'];
					break;
				case 'name':
				case 'display_name':
					return $this->user_data['name'];
					break;
				case 'banned':
					$banned = ($this->user_data['banned'] != 'no');
					return $banned;
				case 'logged_in':
					return isset($this->user_data['id']);
					break;	
				default:
					// Important, for all other undefined values, try the parent class...
					return parent::__get($name);
					break;
			}
		}

		function setUserData($cookieValue) {
			GLOBAL $db;

			$user = array();
			$users = MIB_DB_PREFIX . 'user';
			$user_auth = MIB_DB_PREFIX . 'user_auth';
			$users_sess = MIB_DB_PREFIX . 'user_session';
			$ip_address = getenv('REMOTE_ADDR');			

			$sql="
select 
	u.id id, 
	ua.username username, 
	u.displayname name, 
	u.email email, 
	'no' banned, 
	us.session_id 
from 
	$users u 
	left join $user_auth ua
		on u.id = ua.userid
	left join $users_sess us 
		on u.id = us.userid where us.session_id = '$cookieValue' 
		and us.ip_address = '$ip_address' 
limit 1";

			$row = $db->getRow($sql);
//debug('{',$row,'}');
			if($row) {
				$this->user_data = $row;
			}
		}

		function onLoggedIn() {
			// If user is logged in, add logout link to main menu.
			$menuitem = array('menu'=>'main','txt'=>'log out','url'=>'index.php?logout=true');

			$actions['menuitem'][] = $menuitem;
			$actions['pagetitle'] = 'Welcome '.$this->name;

			return $actions;
		}
		function onNotLoggedIn() {
			// If user isn't logged in, display the login form.
			$content = array();
			$actions = array();

			includeClass('content_loginform.php');
			$content[] = new LoginForm($this->db);

			$actions['content'] = $content;

			if(checkUserInput('register', '') == '') {
				// And add a register menu item.
				$menuitem = array('menu'=>'main','txt'=>'register','url'=>'index.php?register=true');
			} else {
				// Check login details

				// Add menu item to return to login page
				$menuitem = array('menu'=>'main','txt'=>'log in','url'=>'index.php');
				$actions['pagetitle'] = 'Register';
			}

			$actions['menuitem'][] = $menuitem;

			return $actions;
		}

		function logout() {
			GLOBAL $db;

			if(isset($_COOKIE[MIB_SESSION_COOKIE_NAME])) {
				$cookieValue = $_COOKIE[MIB_SESSION_COOKIE_NAME];

				// Remove this session from the database...
				$user_sess = MIB_DB_PREFIX . 'user_session';
				$sql = "delete from $user_sess where session_id = '$cookieValue'";
				$db->execute($sql);

				// Then make sure the cookie gets removed.
				setcookie (MIB_SESSION_COOKIE_NAME, "", time() - 3600);

				// Erase user data as no longer logged in.
				$this->user_data = null;

				$message = array('message' => 'Logout successful.');
			} else {
				$message = array('warning' => 'You are already logged out.');
			}
			return $message;
		}

		function checkLogin($params = array()) {
			$username = $params['user'];
			$password = $params['pass'];

			if(checkUserInput('login', '') != '') {
				if($username == '') $message = array('warning' => 'Please enter a username to login.');
				else if($password == '') $message = array('warning' => 'Please enter your password to login.');
				else {
					GLOBAL $db;
					// validate username and password...
					$user_auth = MIB_DB_PREFIX . 'user_auth';
					$pw_hash = md5($password.MIB_PW_SALT);
//debug($pw_hash);
					$sql = "select id from $user_auth ua where ua.username = '$username' and ua.password ='$pw_hash' limit 1";

					$id = $db->getValue($sql);
					if($id === false) $message = array('warning' => 'Login failed. Check username and password are correct.');
					else {
						// We have a userid, login successful
						// now lets create a session and populate the user data
						$ip_address = getenv('REMOTE_ADDR');
						$session_id = md5($pw_hash.$ip_address.MIB_PW_SALT);
						
						$user_sess = MIB_DB_PREFIX . 'user_session';
						//first make sure any existing sessions for this user get closed...
						$sql = "delete from $user_sess where userid = $id";
						$db->execute($sql);

						//then insert new session
						$sql = "insert into $user_sess (userid, ip_address, session_id) values ($id, '$ip_address', '$session_id')";
						$db->execute($sql);
						setcookie(MIB_SESSION_COOKIE_NAME, $session_id);

						// Now reset user data
						$this->setUserData($session_id);

						$message = array('message' => 'Login successful.');
					}
				}
				return $message;
			}
			return null;
		}

		function getDisplayNameByID($db,$id) {
			$users = MIB_DB_PREFIX . 'user';
			$sql = "select displayname from ".$users." where id = ".$id;
			return $db->getValue($sql);
		}
	}

