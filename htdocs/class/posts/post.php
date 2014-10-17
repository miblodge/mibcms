	<div class="post <?=$post_style ?>">
		<div class="post-header">
			<span class="post-title"><a href="post.php?f=<?=$label ?>"><?=$title ?></a></span>
			<span class="post-author"><?=$creator_name ?></span>
			<span class="post-date"><?=$created ?></span>
		</div>

		<div class="post-body"><?=$body ?></div>

		<div class="post-footer">
			<?php if($show_reply_total) { ?><span class="post-replies"><a href="post.php?f=<?=$label ?>"><?php if($replies > 0) { ?><?=$replies ?> comments<?php } else { ?>Be first to comment!<?php  } ?></a></span><?php }
			elseif($show_reply_link) { ?><span class="post-replies"><a href="post.php?f=<?=$label ?>">Reply</a></span><?php } ?>
		</div>
	</div>