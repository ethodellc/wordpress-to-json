<?php
try {
	$dbConf = parse_ini_file("/opt/php-db-local.ini");
	$db = new PDO($dbConf['dsn'], $dbConf['user'], $dbConf['pass']);
} catch (PDOException $e) {
	echo "something went wrong";
}

$posts = array();

$allPostsSql = $db->prepare('SELECT * FROM wp_posts WHERE `post_status` = "publish" AND post_content != "{{unknown}}" AND post_content != ""');
$allPostsSql->execute();
$posts = $allPostsSql->fetchAll();

foreach ($posts as $post) {
	echo $post['post_title'];
}


?>