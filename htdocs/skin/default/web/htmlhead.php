	<head>
		<title><?php echo $this->title.' - '.$this->site_title; ?></title>
		<?php foreach($css_file as $css) { ?>
		<link rel="stylesheet" href="<?php echo BASE_URL.$css; ?>" type="text/css" />
		<?php } ?>
		<?php foreach($scripts as $script) { ?>
			<script type="text/javascript" src="<?php echo BASE_URL.$script; ?>"></script>
		<?php } ?>
	</head>
