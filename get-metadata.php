<?php

echo "Retrieving posts...\n";

$allPostsSql = $db->prepare('SELECT * FROM wp_posts WHERE `post_status` = "publish" AND post_content != "{{unknown}}" AND post_content != ""');
$allPostsSql->execute();
$allPosts = $allPostsSql->fetchAll(PDO::FETCH_ASSOC);

echo "Retrieving tags...\n";
$tags = array();
$allTagIds = $db->prepare('SELECT * FROM wp_term_taxonomy WHERE taxonomy = "post_tag"');
$allTagIds->execute();
foreach ($allTagIds->fetchAll() as $tagId) {
	$tagQuery = $db->prepare('SELECT * FROM wp_terms WHERE `term_id` = :termId');
	$tagQuery->bindParam(':termId', $tagId['term_id']);
	$tagQuery->execute();
	array_push($tags, $tagQuery->fetch(PDO::FETCH_ASSOC));
}



echo "Retrieving categories...\n";
$categories = array();
$allCategoryIds = $db->prepare('SELECT * FROM wp_term_taxonomy WHERE taxonomy = "category"');
$allCategoryIds->execute();
foreach ($allCategoryIds->fetchAll() as $tagId) {
	$categoryQuery = $db->prepare('SELECT * FROM wp_terms WHERE `term_id` = :termId');
	$categoryQuery->bindParam(':termId', $tagId['term_id']);
	$categoryQuery->execute();
	array_push($categories, $categoryQuery->fetch(PDO::FETCH_ASSOC));
}


echo "Retrieving authors...\n";
$allAuthors = array();
$allAuthorIds = $db->prepare('SELECT * FROM wp_users');
$allAuthorIds->execute();
foreach ($allAuthorIds->fetchAll(PDO::FETCH_ASSOC) as $author) {
	$authorMeta = array(
		'ID' => (int)$author['ID'],
		'display_name' => $author['display_name'],
		'user_email' => $author['user_email']
	);
	$authorMetaQuery = $db->prepare('SELECT * FROM wp_usermeta WHERE user_id = :userId');
	$authorMetaQuery->bindParam(":userId", $author['ID']);
	$authorMetaQuery->execute();
	foreach ($authorMetaQuery->fetchAll(PDO::FETCH_ASSOC) as $authorMetaItem) {
		if (
			$authorMetaItem['meta_key'] === 'first_name' ||
			$authorMetaItem['meta_key'] === 'last_name' ||
			$authorMetaItem['meta_key'] === 'user_email' ||
			$authorMetaItem['meta_key'] === 'description' ||
			$authorMetaItem['meta_key'] === 'custom_jobtitle' ||
			$authorMetaItem['meta_key'] === 'facebook' ||
			$authorMetaItem['meta_key'] === 'twitter' ||
			$authorMetaItem['meta_key'] === 'custom_twitter' ||
			$authorMetaItem['meta_key'] === 'custom_linkedin' ||
			$authorMetaItem['meta_key'] === 'custom_googleplus' ||
			$authorMetaItem['meta_key'] === 'wpseo_metadesc' ||
			$authorMetaItem['meta_key'] === 'wpseo_title'
		) {
			$authorMeta[$authorMetaItem['meta_key']] = $authorMetaItem['meta_value'];
		}
	}
	
	array_push($allAuthors, $authorMeta);
}

?>