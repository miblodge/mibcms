<?php
	$url = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
	$url .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

	define('BASE_PATH',realpath('.')); // For use in PHP if needed
	define('BASE_URL', $url); // For use in client output (css and js includes for example).

//echo BASE_URL;

	require_once(BASE_PATH.'/config/config.php');

	// Show posts
	includeClass('content_posts.php');
	$posts = new Posts($db);
	$posts->show_reply_total = true;
	$posts->orderby = 'activity desc, created desc';
	$posts->getPosts();
	$page->content[] = $posts;	
	
	// OK this is a normal page, so show post form, if user is logged in (non-logged in users
	// can only make comments on existing public posts).
	if(isset($page->user)) {
		includeClass('content_postform.php');
		$postform = new PostForm($db);
		$page->content[] = $postform;	

		if(checkUserInput('post', '') != '') {
			$params = array();
			$params['user'] = $page->user->id;
			$params['title'] = checkUserInput('title', '');
			$params['body'] = checkUserInput('body', '');

			$message = $posts->addPost($params);

			if(isset($message)) $this->addMessage($message);
		}
	}

//debug($page);
	$page->renderPage();

