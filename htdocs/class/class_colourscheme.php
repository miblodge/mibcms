<?php
	class ColourScheme {
		// Main background series
		public $bg_main = '#000';
		public $bg_second = '#111';
		public $bg_third = '#000';
		public $bg_fourth = '#333';

		// Alt Background series
		public $alt_bg_main = '#633';
		public $alt_bg_second = '#744';
		public $alt_bg_third = '#400';
		public $alt_bg_fourth = '#522';

		// Text
		public $txt_main = '#fff';
		public $txt_second = '#ccc';

		// Links
		public $link = '#f00';
		public $link_visited = '#f33';
		public $link_hover = '#f99';
		public $link_active = '#f99';

		//Messages
		public $msg_bg = '#696';
		public $msg_border = '#9c9';
		public $msg_txt = '#cfc';

		//Warning Messages
		public $warn_bg = '#966';
		public $warn_border = '#c99';
		public $warn_txt = '#fcc';

		function __construct() {
			// TO DO: Get users custom style or page owners style
		}
	}
?>
