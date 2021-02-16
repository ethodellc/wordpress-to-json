<?php
$allPostsSql = $db->prepare('SELECT * FROM wp_posts WHERE `post_status` = "publish" AND post_content != "{{unknown}}" AND post_content != ""');
$allPostsSql->execute();
$allPosts = $allPostsSql->fetchAll(PDO::FETCH_ASSOC);


$tags = array();
$allTagIds = $db->prepare('SELECT * FROM wp_term_taxonomy WHERE taxonomy = "post_tag"');
$allTagIds->execute();
foreach ($allTagIds->fetchAll() as $tagId) {
	$tagQuery = $db->prepare('SELECT * FROM wp_terms WHERE `term_id` = :termId');
	$tagQuery->bindParam(':termId', $tagId['term_id']);
	$tagQuery->execute();
	array_push($tags, $tagQuery->fetch(PDO::FETCH_ASSOC));
}


$categories = array();
$allCategoryIds = $db->prepare('SELECT * FROM wp_term_taxonomy WHERE taxonomy = "category"');
$allCategoryIds->execute();
foreach ($allCategoryIds->fetchAll() as $tagId) {
	$categoryQuery = $db->prepare('SELECT * FROM wp_terms WHERE `term_id` = :termId');
	$categoryQuery->bindParam(':termId', $tagId['term_id']);
	$categoryQuery->execute();
	array_push($categories, $categoryQuery->fetch(PDO::FETCH_ASSOC));
}
?>