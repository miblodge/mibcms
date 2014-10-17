<?php
	includeClass('class_content.php');

	class LoginForm extends Content {
		public $folder = 'loginform';

		public $css = array('loginform.css.php');

		function __construct($db) {
			parent::__construct($db);
		}

		function render() {
			if(!isset($this->user))	{
				if(checkUserInput('register', '') == '') {
					include($this->getTemplate('loginform.php'));
				} else {
					include($this->getTemplate('registerform.php'));
				}
			}
		}
	}
?>
