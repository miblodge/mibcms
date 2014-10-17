<?php
	includeClass('class_content.php');

	class PostForm extends Content {
		public $folder = 'postform';

		public $css = array('postform.css.php');
		public $show_title = true;

		public $thread = 0;
		public $reply_to = 0;

		function __construct($db) {
			parent::__construct($db);
		}

		function render() {
			$session_form_guid = uniqid();
			$_SESSION['session_form_guid'] = $session_form_guid;
//debug($_SESSION);
			$show_title = $this->show_title;
			$thread = $this->thread;
			$reply_to = $this->reply_to;
			if(isset($this->user))	{
				$logged_in = true;
//debug($this->user);
				$display_name = $this->user->displayname;
			} else {
				$logged_in = false;
//debug($this->user);
				$display_name = '';
			}
			include($this->getTemplate('postform.php'));
		}
	}
?>
