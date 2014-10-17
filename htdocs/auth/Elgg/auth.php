<?php
/**
 * Note: This auth module was written years ago when I first
 * started this project using what must be by now an outdated
 * version of Elgg. It may require some tweaking to get it
 * working again, depending on how much has changed.
 *
 * If you do get it working again, please consider contributing
 * your fix back to github. Same applies if you mod this to
 * authenticate via other cms systems.
 */

	require_once('./auth/'.MIB_AUTH.'/config.php');
	includeClass('class_authentication.php');

	class ElggAuth extends Authentication {
		public $elgg_user_data = null;

		function __construct() {
			$cookieValue = $_COOKIE["Elgg"];

			$this->setElggUserData($cookieValue);

			parent::__construct();
		}

		function __get($name) {
			switch($name) {
				case 'id':
				case 'userid':
					return $this->elgg_user_data['id'];
					break;
				case 'username':
					return $this->elgg_user_data['username'];
					break;
				case 'name':
				case 'display_name':
					return $this->elgg_user_data['name'];
					break;
				case 'banned':
					$banned = ($this->elgg_user_data['banned'] != 'no');
					return $banned;
				case 'logged_in':
					return isset($this->elgg_user_data['id']);
					break;	
				default:
					// Important, for all other undefined values, try the parent class...
					return parent::__get($name);
					break;
			}
		}

		function setElggUserData($cookieValue) {
			$elggUser = array();
			//      $md5cookie = md5($cookieValue);
			$users = ELGG_DB_PREFIX . 'users_entity';
			$users_sess = ELGG_DB_PREFIX . 'users_sessions';

			$rows = $this->elggDbQuery("select $users.guid id, $users.username username, $users.name name, " .
						"$users.email email, $users.banned banned, $users_sess.session " .
						"from $users left join $users_sess on $users.last_action = $users_sess.ts " .
						"where $users_sess.session = '$cookieValue'");

			if(count($rows) > 0) {
				$elggUser = $rows[0];
			} else {
				$this->err[] = 'Couldnt get user';
			}
			$this->elgg_user_data = $elggUser;
		}

		// -----------------------------------------------------------------------
		function elggDbQuery($query)
		{
			if(!$cnx = mysql_connect(ELGG_DB_HOST, ELGG_DB_USER, ELGG_DB_PASSWORD)) {
				$this->err[] = 'Could not connect to database: ' . mysql_error();
			}
			
			if(!mysql_select_db(ELGG_DB_NAME, $cnx)) {
				$this->err[] = 'Could not select database: ' . mysql_error();
			}
			
			if(!$rs = mysql_query($query, $cnx)) {
				$this->err[] = 'Could not execute query: ' . mysql_error();
			}
			$rows = array();
			while ($row = mysql_fetch_assoc($rs)) {
				$rows[] = $row;
			}
			return $rows;
		}
	}

	$auth = new ElggAuth();
