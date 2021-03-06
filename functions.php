<?php
try {
	echo "Connecting to database...\n";
	$dbConf = parse_ini_file("migration-db.ini");
	$db = new PDO($dbConf['dsn'], $dbConf['user'], $dbConf['pass']);
} catch (PDOException $e) {
	echo "Connection failed...\n";
	echo $e;
	exit(1);
}

if ($db) {
	echo "Database connection success!\n";
	echo "Starting content extraction\n";
}

function getYoastMetadata (PDO $db, Int $postId) {
	$yoastQuery = $db->prepare("SELECT * FROM wp_yoast_indexable WHERE `object_id` = :postId");
	$yoastQuery->bindParam(":postId", $postId);
	$yoastQuery->execute();
	return $yoastQuery->fetch(PDO::FETCH_ASSOC);
}


function getSEOTitle (PDO $db, Int $postId, string $originalTitle) {
	$postYoast = getYoastMetadata($db, $postId);
	if ($postYoast['title'] !== null) {
		return $postYoast['title'];
	}
	return $originalTitle;
}


function getSEODescription (PDO $db, Int $postId) {
	$postYoast = getYoastMetadata($db, $postId);
	if ($postYoast['description'] !== null) {
		return $postYoast['description'];
	}
	return "";
}


function getFeatureImage (PDO $db, Int $postId) {
	$thumbnailIdQuery = $db->prepare('SELECT * FROM wp_postmeta WHERE `post_id` = :postId AND `meta_key` = "_thumbnail_id"');
	$thumbnailIdQuery->bindParam(":postId", $postId);
	$thumbnailIdQuery->execute();
	$thumbnailIdRow = $thumbnailIdQuery->fetch(PDO::FETCH_ASSOC);

	if ($thumbnailIdRow !== false) {
		$thumbnailFilePathQuery = $db->prepare('SELECT * FROM wp_postmeta WHERE `post_id` = :postId AND `meta_key` = "_wp_attached_file"');
		$thumbnailFilePathQuery->bindParam("postId", $thumbnailIdRow['meta_value']);
		$thumbnailFilePathQuery->execute();
		$thumbnail = $thumbnailFilePathQuery->fetch(PDO::FETCH_ASSOC);
		if ($thumbnail !== false) {
			return $thumbnail['meta_value'];
		}
	}
	return "";
}


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


function getTagsList (PDO $db, Int $postId, array $tags, string $type) {
	$out = array();
	$tagList = getPostTags($db, $postId, $tags);
	if (count($tagList) > 0) {
		foreach ($tagList as $tag) {
			array_push($out, $tag['name']);
		}
		if ($type === "array") {
			return $out;
		} else if ($type === "string") {
			return join(",", $out);
		}
	}

	if ($type === "array") {
		return array();
	} else if ($type === "string") {
		return '';
	}
}


function getTagsListFromPostData (array $post) {
	$out = array();
	foreach ($post['legacyData']['tags'] as $tag) {
		array_push($out, $tag['name']);
	}
	return $out;
}


function getPostAuthor (Int $authorId, array $authors) {
	foreach ($authors as $author) {
		if ($author['ID'] === $authorId) {
			return $author;
		}
	}
}


function getUniqueTags (array $posts) {
	$uniqueTags = array();

	foreach ($posts as $post) {
		$postTags = getTagsListFromPostData($post);
		foreach ($postTags as $tag) {
			if (in_array($tag, $uniqueTags, false) === false) {
				array_push($uniqueTags, $tag);
			}
		}
	}
	return $uniqueTags;
}


function getUniqueCategories (array $posts) {
	$uniqueCategories = array();
	foreach ($posts as $post) {
		if (in_array($post['category'], $uniqueCategories, false) === false) {
			array_push($uniqueCategories, $post['category']);
		}
	}
	return $uniqueCategories;
}

?>