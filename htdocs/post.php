<?php
	$url = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
	$url .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

	define('BASE_PATH',realpath('.')); // For use in PHP if needed
	define('BASE_URL', $url); // For use in client output (css and js includes for example).

//echo BASE_URL;

	require_once(BASE_PATH.'/config/config.php');

	includeClass('content_posts.php');
	$posts = new Posts($db);

	// First, is a post selected by either id or label?
	$label = checkUserInput('f', '');
	if(checkUserInput('f', '') == '') {
		$id = checkUserInput('id', 0);
	} else {
		$id = $posts->getIDByLabel($label);
	}

	if(isset($page->user)) {
		if($id == 0) {
			$edit = true;
		} else {
			$edit = checkUserInput('edit',false);
		}
	} else {
		$edit = false;
		if($id == 0) header('Location: index.php?message=No+such+post');
	}

	includeClass('content_posts.php');
	$posts = new Posts($db);

	if(checkUserInput('post', '') != '') {
		$params = array();
		$params['user'] = $page->user->id;
		if($id > 0) $params['title'] = 'RE: '.$posts->getTitleByID($id);
		else $params['title'] = checkUserInput('title', '');
		$params['body'] = checkUserInput('body', '');
		$params['reply_to'] = checkUserInput('reply_to', $id);
		$params['thread'] = checkUserInput('thread', $posts->getThreadByID($id));

		$posts->addPost($params);
	}

	includeClass('content_postform.php');
	$postform = new PostForm($db);
	if($edit) {
		$page->content[] = $postform;
	} else {
		// Show post
		$posts->thread = $id;
		$posts->total = 1;
		$posts->orderby = 'id asc';
		$posts->getPosts();
		$page->content[] = $posts;

		// Show replies
		$replies = new Posts($db);
		$replies->replies_only = true;
		$replies->thread = $id;
		$replies->orderby = 'id asc';
		$replies->total = 100;
		$replies->post_style = array('reply1','reply2');
		$replies->show_reply_link = true;
		$replies->getPosts();
		$page->content[] = $replies;

		// Now add the postform. As a reply to the thread.
		$postform->show_title = false;
		$postform->reply_to = $id;
		$postform->thread = $posts->getThreadByID($id);
		$page->content[] = $postform;
	}

//debug($page);
	$page->renderPage();

