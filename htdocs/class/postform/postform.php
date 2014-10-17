	<div class="post-form">
	<form action="post.php" method="post">
		<input type="hidden" name="session_form_guid" value="<?=$session_form_guid?>" />
		<input type="hidden" name="thread" value="<?=$thread?>" />
		<input type="hidden" name="reply_to" value="<?=$reply_to?>" />
		<div class="post-form-name"><?php if($logged_in) { ?><label>Posting as:</label> <span><?=$display_name ?></span> <?php } else { ?><label>Name:</label> <input type="text" name="username" value="" /><?php } ?></div>
		<?php if($show_title) { ?><div class="post-form-title"><label>Title:</label> <input type="text" name="title" value="" /></div><?php } ?>
		<div class="post-form-body"><div class="post-form-toolbar">[b] [i] [url]</div><textarea name="body" id="body_<?=$session_form_guid?>" value="" /></textarea></div>
		<div class="post-form-button"><input type="submit" name="post" value="Post" /></div>
	</form>
	</div>
