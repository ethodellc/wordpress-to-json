<?php

require './functions.php';
require './get-metadata.php';

$posts = array();

foreach ($allPosts as $post) {
	$out = array(
		'stName' => 'Blog',
		'title' => $post['post_title'],
		'seoTitle' => getSEOTitle($db, $post['ID'], $post['post_title']),
		'description' => getSEODescription($db, $post['ID']),
		'excerpt' => $post['post_excerpt'],
		'featureImage' => getFeatureImage($db, $post['ID']),
		'urlTitle' => $post['post_name'],
		'body' => $post['post_content'],
		'postDate' => $post['post_date'],
		'publishDate' => $post['post_date'],
		'legacyId' => $post['ID'],
		'legacyGuid' => $post['guid'],
		'legacyAuthor' => $post['post_author'],
		'category' => getPostCategory($db, $post['ID'], $categories),
		'tags' => getPostTags($db, $post['ID'], $tags),
		'author' => getPostAuthor($post['post_author'], $allAuthors)
	);

	array_push($posts, $out);
}

$fp = fopen('all-posts.json', 'w');
fwrite($fp, json_encode($posts));
fclose($fp);

$fp = fopen('all-authors.json', 'w');
fwrite($fp, json_encode($allAuthors));
fclose($fp);
?>