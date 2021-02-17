<?php
try {
	$dbConf = parse_ini_file("/opt/php-db-local.ini");
	$db = new PDO($dbConf['dsn'], $dbConf['user'], $dbConf['pass']);
} catch (PDOException $e) {
	echo "something went wrong";
}

require './get-metadata.php';

$posts = array();

function getPostMetaIdentifiers (PDO $db, Int $postId) {
	$postQuery = $db->prepare('SELECT * FROM wp_term_relationships WHERE object_id = :postId');
	$postQuery->bindParam(":postId", $postId);
	$postQuery->execute();
	return $postQuery->fetchAll(PDO::FETCH_ASSOC);
}

function getPostCategory (PDO $db, Int $postId, array $categories) {
	$postTaxonomy = getPostMetaIdentifiers($db, $postId);
	foreach ($postTaxonomy as $taxonomyEntry) {
		foreach ($categories as $category) {
			if ($taxonomyEntry['term_taxonomy_id'] === $category['term_id']) {
				return $category;
			}
		}
	}
}

function getPostTags (PDO $db, Int $postId, array $tags) {
	$out = array();
	$postTaxonomy = getPostMetaIdentifiers($db, $postId);
	foreach ($postTaxonomy as $taxonomyEntry) {
		foreach ($tags as $tag) {
			if ($taxonomyEntry['term_taxonomy_id'] === $tag['term_id']) {
				array_push($out, $tag);
			}
		}
	}
	return $out;
}

function getPostAuthor (Int $authorId, array $authors) {
	foreach ($authors as $author) {
		if ($author['ID'] == $authorId) {
			return $author;
		}
	}
}

foreach ($allPosts as $post) {
	$out = Array(
		'stName' => 'Blog',
		'title' => $post['post_title'],
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

$siteJSON = json_encode($posts);
?>