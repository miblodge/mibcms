<?php
	class Page {
		public $site_title = 'site title goes here';
		public $title = 'Welcome';
		public $search = 'EnigMagick';

		/** @var MySQLDataBase */
		public $db;
		/** @var Authentication */
		public $auth;

		/** @var User  */
		public $user;
		public $character;
		public $mode = 'welcome';

		public $skin = 'default';
		public $view = 'web';
		public $css = array('page.css.php');
		public $scripts = array();

		public $messages = array();
		public $warnings = array();
		public $menus = array();

		/** @var Content[] */
		public $header_content = array();
		/** @var Content[] */
		public $custom_header_content = array();
		/** @var Content[] */
		public $content = array();
		/** @var Content[] */
		public $custom_footer_content = array();
		/** @var Content[] */
		public $footer_content = array();

		function __construct($db,$auth) {
			$this->db = $db;
			$this->auth = $auth;

			$actions['menuitem'][] = array('menu'=>'main','url'=>'index.php','txt'=>'home');
			$this->processActions($actions);

			if(defined('MIB_SITE_TITLE')) $this->site_title = MIB_SITE_TITLE;

			// Check to see if user logged in via form or has selected logout.
			$this->checkLogin();

			if($this->auth->logged_in) {
				includeClass('class_user.php');
				$this->user = new User($this->db,null,$this->auth);
				$actions = $this->auth->onLoggedIn();
			} else {
				$actions = $this->auth->onNotLoggedIn();
			}
			$this->processActions($actions);

			// Add main menu			
			$content = array();
			$actions = array();

			includeClass('content_menu.php');
			$menu = new Menu($this->db);
			$menu->menu = 'main';
			$content[] = $menu;

			$actions['custom_header_content'] = $content;
			$this->processActions($actions);
		}

		function __get($name) {
			switch($name) {
				case 'content':
					return $this->getContent();
					break;
			}
		}

		function processActions($actions) {
			if(isset($actions['pagetitle'])) $this->title = $actions['pagetitle'];
			if(isset($actions['custom_header_content'])) {
				foreach($actions['custom_header_content'] as $content) {
					$this->custom_header_content[] = $content;
				}
			}
			if(isset($actions['content'])) {
				foreach($actions['content'] as $content) {
					$this->content[] = $content;
				}
			}
			if(isset($actions['custom_footer_content'])) {
				foreach($actions['custom_footer_content'] as $content) {
					$this->custom_footer_content[] = $content;
				}
			}
			if(isset($actions['menuitem'])) {
				foreach($actions['menuitem'] as $menuitem) {
					$this->addMenuItem($menuitem);
				}
			}
		}
	
		function checkLogin() {
			if(checkUserInput('login', '') != '') {
				$params = array();
				$params['user'] = checkUserInput('username', '');
				$params['pass'] = checkUserInput('password', '');

				$message = $this->auth->checkLogin($params);

				if(isset($message)) $this->addMessage($message);
			}
			if(checkUserInput('logout', '') != '') {
				$message = $this->auth->logout();

				if(isset($message)) $this->addMessage($message);
			}
		}
	
		function checkForDataBaseErrors() {
			$errs = $this->db->err;

			if(!empty($errs)) {
				$warning_msg = 'Database experienced the following errors <ul><li>'.implode('</li><li>',$errs).'</li></ul>';
				$this->addMessage(array('warning'=>$warning_msg));
			}
		}

		function getTemplate($filename,$serverpath=true) {
			// Check which path we need for this template (ie
			// selected skin, site default, or wtg default.)
			if(file_exists(BASE_PATH.'/skin/'.$this->skin.'/'.$this->view.'/'.$filename)) {
				$path_to_file = '/skin/'.$this->skin.'/'.$this->view.'/'.$filename;
			} elseif(file_exists(BASE_PATH.'/skin/default/'.$this->view.'/'.$filename)) {
				$path_to_file = '/skin/default/'.$this->view.'/'.$filename;
			} elseif(file_exists(BASE_PATH.'/skin/'.$this->skin.'/web/'.$filename)) {
				$path_to_file = '/skin/'.$this->skin.'/web/'.$filename;
			} else {
				$path_to_file = '/skin/default/web/'.$filename;
			}

			if($serverpath) $path_to_file = BASE_PATH.$path_to_file;

			return $path_to_file;
		}

		function addMenuItem($menuitem) {
			if(!isset($this->menus[$menuitem['menu']])) $this->menus[$menuitem['menu']] = array();
			$this->menus[$menuitem['menu']][$menuitem['txt']] = $menuitem['url'];
//debug($this);
		}

		function addMessage($message) {
			if(is_array($message)) {
				if(isset($message['message'])) $this->messages[] = $message['message'];
				if(isset($message['warning'])) $this->warnings[] = $message['warning'];
			} else {
				$this->messages[] = $message;
			}
		}

		function renderWarnings() {
			foreach($this->warnings as $warning) {
				include($this->getTemplate('warning.php'));
			}
		}

		function renderMessages() {
			foreach($this->messages as $message) {
				include($this->getTemplate('message.php'));
			}
		}

		function renderHeaderContent() {
			foreach($this->header_content as $content) {
				$content->skin = $this->skin;
				$content->view = $this->view;
				if(isset($this->user)) $content->user = $this->user;
				$content->render();
			}
			foreach($this->custom_header_content as $content) {
				$content->skin = $this->skin;
				$content->view = $this->view;
				if(isset($this->user)) $content->user = $this->user;
				$content->render();
			}
		}
		function renderContent() {
			foreach($this->content as $content) {
				$content->skin = $this->skin;
				$content->view = $this->view;
				if(isset($this->user)) $content->user = $this->user;
				$content->render();
			}
		}

		function getCSSFiles() {
			$css_files = array();
			foreach($this->css as $css) {
//echo BASE_PATH.'/skin/'.$this->skin.'/'.$this->view.'/'.$css;
				// check it exists in the selected skin, if so use that, otherwise use default
				$css_files[] = $this->getTemplate($css,false);
			}
			foreach($this->custom_header_content as $content) {
				$content_css = $content->getCSS();
				if(isset($content_css)) $css_files = array_merge($css_files,$content_css);
			}
			foreach($this->content as $content) {
				$content_css = $content->getCSS();
				if(isset($content_css)) $css_files = array_merge($css_files,$content_css);
			}
			foreach($this->custom_footer_content as $content) {
				$content_css = $content->getCSS();
				if(isset($content_css)) $css_files = array_merge($css_files,$content_css);
			}
			return $css_files;
		}

		function getScripts() {
			$scripts = array();
			foreach($this->scripts as $script) {
//echo BASE_PATH.'/skin/'.$this->skin.'/'.$this->view.'/'.$css;
				// check it exists in the selected skin, if so use that, otherwise use default
				$scripts[] = $this->getTemplate($script,false);
			}
			foreach($this->custom_header_content as $content) {
				$content_script = $content->getScripts();
				if(isset($content_css)) $scripts = array_merge($scripts,$content_script);
			}
			foreach($this->content as $content) {
				$content_script = $content->getScripts();
				if(isset($content_css)) $scripts = array_merge($scripts,$content_script);
			}
			foreach($this->custom_footer_content as $content) {
				$content_script = $content->getScripts();
				if(isset($content_css)) $scripts = array_merge($scripts,$content_script);
			}
			return $scripts;
		}

		function renderPage() {
			$this->checkForDataBaseErrors();
//debug($this);
			$css_file = $this->getCSSFiles();
			$scripts = $this->getScripts();

			include($this->getTemplate('page.php'));
		}
	}
?>
