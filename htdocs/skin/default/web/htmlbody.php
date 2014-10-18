	<body>
		<?php include($this->getTemplate('bodyheader.php')); ?>
			<div id="main_content">
				<h2><?php echo $this->title; ?></h2>
				<?php $this->renderWarnings(); ?>
				<?php $this->renderMessages(); ?>
				<?php $this->renderContent(); ?>
			</div>
		<?php include($this->getTemplate('bodyfooter.php')); ?>
	</body>
