<?php
	includeClass('class_content.php');

	class Posts extends Content {
		public $folder = 'posts';
		public $author = null; // By default show posts by all authors
		public $thread = null; // By default show only top level posts
		public $total = 23; // By default, show max 23 posts
		public $orderby = 'created desc'; // By default, order by date created.
		public $post_style = array(); // By default, no extra post classes shown.
		public $replies_only = false; // Excludes the thread head if set to true.
		public $show_reply_total = false;
		public $show_reply_link = false;

		public $posts = array();

		public $css = array('posts.css.php');

		function __construct($db) {
			parent::__construct($db);
		}

		function labelExists($label) {
			$content_table = MIB_DB_PREFIX . 'content';
			$sql = "select count(*) from ".$content_table." where label = '$label'";
			$count = $this->db->getValue($sql);

			if($count == 0) return false;
			else return true;
		}

		// TO DO: Make form submission use these functions, not the
		// ones in PostForm
		function getLabel($title) {
			$label = str_replace(' ','_',$title);


			// TO DO: Check it hasn't already been used, if so, add a sufix...
			$suffix = 0;
			$base_label = $label;
			while($this->labelExists($label)) {
				$suffix++;
				$label = $base_label.'_'.$suffix;
			}

			return $label;
		}

		function getIDByLabel($label) {
			$content_table = MIB_DB_PREFIX . 'content';

			$sql = "select id from ".$content_table." where label ='$label' limit 1";
			$id = $this->db->getValue($sql);

			if($id === false) return 0;
			else return $id;
		}

		function getTitleByID($id) {
				$content_table = MIB_DB_PREFIX . 'content';

				$sql = "select title from ".$content_table." where id ='$id' limit 1";
				$title = $this->db->getValue($sql);

				return $title;
		}

		function getThreadByID($id) {
			$content_table = MIB_DB_PREFIX . 'content';

			$sql = "select thread from ".$content_table." where id ='$id' limit 1";
			$thread = $this->db->getValue($sql);

			if($thread == 0) return $id;
			else return $thread;
		}

		function addPost($params=array()) {
			$content_table = MIB_DB_PREFIX . 'content';
			$user = $params['user'];

//debug($_SESSION);

			if (!isset($_SESSION['session_form_guid']) or !isset($_POST['session_form_guid']) or ($_POST['session_form_guid'] != $_SESSION['session_form_guid'])) {
				// double submit
				sleep(1); // Make sure first post is in DB then select most recent post by this user.
				$sql = "select id, thread from ".$content_table." where creator = ".$user." order by created desc";
				$row = $this->db->getRow($sql);
				if($row) {
					debug($row,$_SESSION);
					// Okay, there was a row.
					if($row['thread']) $id = $row['thread'];
					else $id = $row['id'];
					header('Location: post.php?id='.$id);
					die();
				} else {
					debug($sql);
					header('Location: index.php?sql='.$sql);
					die();
				}
			} else {
				// Now unset the SESSION var
				$_SESSION['session_form_guid'] = '';
			}

			$sql = "select id, thread from ".$content_table." where created > (now() - INTERVAL 5 seconds)";
			$row = $this->db->getRow($sql);
			if($row) {
				// Okay, there was a row.
				if($row['thread']) $id = $row['thread'];
				else $id = $row['id'];
				header('Location: post.php?id='.$id);
				die();
			}

			$title = $params['title'];
			$label = $this->getLabel($title);
			$body = $params['body'];
			$thread = $params['thread'];
			$reply_to = $params['reply_to'];

			if(MIB_POST_HTMLPURIFIER) {
				require BASE_PATH.'/lib/HTMLPurifier/HTMLPurifier.path.php';
				require 'HTMLPurifier.includes.php';
				require 'HTMLPurifier.autoload.php';

				$purifier = new HTMLPurifier($config);
				$body = $purifier->purify($body);
			}

			// TO DO: Use HTML Purify on body before saving content.
			$sql = "insert into ".$content_table." (creator,label,title,body,created) values ($user,'$label','$title','$body',now())";
			$this->db->execute($sql);

			$id = $this->db->getID();
			if($reply_to > 0) {
				// TO DO: This is lazy coding. I should have inserted these FKs in original insert if needed.
				$sql = "update ".$content_table." set thread = ".$thread.", reply_to = ".$reply_to." where id = ".$id;
				$this->db->execute($sql);

				// Now update the activity date on the thread...
				$sql = "update ".$content_table." set activity = now() where id = ".$thread;

				$this->db->execute($sql);
			}

			// TO DO: After making insert we should redirect to
			// avoid form resubmission on page refresh. Also, we
			// otherwise won't see new post.
			if(isset($params['redirect'])) header('Location: '.$params['redirect']);
			elseif($thread > 0) header('Location: post.php?id='.$thread.'&message=post+complete.');
			else header('Location: post.php?id='.$id.'&message=post+complete.');
			die();
		}
		function processWikiCode($body) {
			require_once BASE_PATH."/lib/wikirenderer/WikiRenderer.lib.php";

			$parser = new WikiRenderer('mib_to_xhtml');
			$body = $parser->render($body);

			return $body;
		}
		function processMarkdown($body) {
			// TO DO: Markdown support.
			return $body;
		}
		function processEmotes($body) {
			return $body;
		}

		function processBBCode($body) {
			require_once BASE_PATH."/lib/JBBCode/Parser.php";
 
			$parser = new JBBCode\Parser();
			$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
			 
			$parser->parse($body);
			 
			$body = $parser->getAsHtml();

			return $body;
		}

		function processPost($body) {

			// TO DO: Add markdown support...
			if(MIB_POST_PARSEDOWN) $body = $this->processMarkdown($body);
						
			// Wiki style link support
			if(MIB_POST_WIKIRENDERER) $body = $this->processWikiCode($body);
			
			// BBCode support
			if(MIB_POST_JBBCODE) $body = $this->processBBCode($body);

			// TO DO: Add emoticons support...
			$body = $this->processEmotes($body);			

			return $body;
		}

		function getPosts() {
			$content_table = MIB_DB_PREFIX . 'content';

			$sql = "select id, label, title, creator, created, body from ".$content_table;

			$clause = ' where';
			if(!is_null($this->author)) {
				$sql.=$clause.' creator="'.$this->author.'"'; 
				$clause = ' and';
			}
			if(!is_null($this->thread)) {
				if($this->replies_only) $sql.=$clause.' thread='.$this->thread;
				else $sql.=$clause.' (thread='.$this->thread.' or id='.$this->thread.')';
			} else {
				$sql.=$clause.' (thread is null)';
			}
			$clause = ' and';
			$sql.=' order by '.$this->orderby; 
			$sql.=' limit '.$this->total; 
//debug($sql);
			$this->posts = $this->db->getData($sql);
			//debug($this->posts);
		}

		function getTotalPostsInThread($id) {
			$content_table = MIB_DB_PREFIX . 'content';
			$sql = "select count(*) from ".$content_table." where thread = ".$id;
			return $this->db->getValue($sql);
		}

		function render() {
			include($this->getTemplate('posts.php'));
		}

		function renderPosts() {
			global $auth;

			$post_styles = $this->post_style;
			$post_style = '';
			foreach($this->posts as $post) {;
				if(is_array($post_styles)) {
					$post_style = array_pop($post_styles);
					$post_styles = array_unshift($post_styles,$post_style);
				}

				// TO DO: Get actual author name
				$creator_id = $post['creator'];

				includeClass('class_user.php');
				$creator = new User($this->db,$post['creator']);
				$creator_name = $creator->getDisplayName($auth);

				$title = $post['title'];
				$label = $post['label'];
				$created = $post['created'];
				$body = $this->processPost($post['body']);

				// Number of replies.
				$show_reply_total = $this->show_reply_total;
				if($show_reply_total) $replies = $this->getTotalPostsInThread($post['id']);
				$show_reply_link = $this->show_reply_link;

				// Now render the post.
				include($this->getTemplate('post.php'));
			}
		}
	}
?>
