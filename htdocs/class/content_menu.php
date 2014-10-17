<?php
	includeClass('class_content.php');

	class Menu extends Content {
		public $folder = 'menu';

		public $menu;

		public $css = array('menu.css.php');

		function __construct($db) {
			parent::__construct($db);
		}

		function setMenuItems() {
			// Get menu items from page.
		}

		function renderMenuItems() {
			GLOBAL $page;
//debug($this);

			if(isset($page->menus[$this->menu])) {
				$menu_items = $page->menus[$this->menu]; 
//debug($menu_items);

				// Get menu items from page.
				foreach($menu_items as $menuitemtxt => $menuitemurl) {			
					include($this->getTemplate('menuitem.php'));
				}
			}
		}

		function render() {
			include($this->getTemplate('menu.php'));
		}
	}
?>
