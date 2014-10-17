<?php
	/* This is a dummy base class that includes the actual base class.  By default it
	uses the mysql class. To override, create a copy of this file in the /mod folder
	and simply include a different database class, and set $db to an instance of that. */	

	includeClass('class_mysql_db.php');

	global $db;
	$db = new MySQLDataBase();
?>
