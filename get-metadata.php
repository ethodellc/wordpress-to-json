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


$allAuthors = array();
$allAuthorIds = $db->prepare('SELECT * FROM wp_users');
$allAuthorIds->execute();
foreach ($allAuthorIds->fetchAll(PDO::FETCH_ASSOC) as $author) {
	$authorMeta = array(
		'ID' => $author['ID'],
		'display_name' => $author['display_name'],
		'user_email' => $author['user_email']
	);
	$authorMetaQuery = $db->prepare('SELECT * FROM wp_usermeta WHERE user_id = :userId');
	$authorMetaQuery->bindParam(":userId", $author['ID']);
	$authorMetaQuery->execute();
	foreach ($authorMetaQuery->fetchAll(PDO::FETCH_ASSOC) as $authorMetaItem) {
		if ($authorMetaItem['meta_key'] !== 'rich_editing' && $authorMetaItem['meta_key'] !== 'admin_color' && $authorMetaItem['meta_key'] !== 'dismissed_wp_pointers' && $authorMetaItem['meta_key'] !== 'wp_capabilities' && $authorMetaItem['meta_key'] !== 'wp_user_level')
		$authorMeta[$authorMetaItem['meta_key']] = $authorMetaItem['meta_value'];
	}
	
	array_push($allAuthors, $authorMeta);
}


?>