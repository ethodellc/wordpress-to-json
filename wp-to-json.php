<?php

require './functions.php';
require './get-metadata.php';

$posts = array();

echo "Building objects...\n";
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
		'legacyData' => array(
			'legacyId' => $post['ID'],
			'legacyGuid' => $post['guid'],
			'legacyAuthor' => $post['post_author'],
			'author' => getPostAuthor($post['post_author'], $allAuthors),
			'tags' => getPostTags($db, $post['ID'], $tags),
		),
		'category' => getPostCategory($db, $post['ID'], $categories),
		'tags' => getTagsList($db, $post['ID'], $tags, "string")
	);

	array_push($posts, $out);
}
echo "Finished base extraction...\n";
echo "\n";

echo "Building metadata objects...\n";
$uniqueTags = getUniqueTags($posts);
$uniqueCategories = getUniqueCategories($posts);

echo "Finished metadata extraction!\n";
echo "\n";
echo 'Live posts extracted:         ' . count($posts) . "\n";
echo 'Unique tags extracted:        ' . count($uniqueTags) . "\n";
echo 'Unique categories extracted:  ' . count($uniqueCategories) . "\n";


// Cleanup
echo "Starting cleanup...\n";
echo "\n";
echo "Serializing legacy data...";
foreach ($posts as $post) {
	$post['legacyData'] = json_encode($post['legacyData']);
}


echo "Writing files...\n";
if (is_dir(('./out')) === false) {
	mkdir("./out", 0755);
}
if (is_dir('./out') === true) {
	$fp = fopen('./out/all-posts.json', 'w');
	fwrite($fp, json_encode($posts));
	fclose($fp);
	
	$fp = fopen('./out/all-tags.json', 'w');
	fwrite($fp, json_encode($uniqueTags));
	fclose($fp);
	
	$fp = fopen('./out/all-authors.json', 'w');
	fwrite($fp, json_encode($allAuthors));
	fclose($fp);
	echo "Finished!\n";
} else {
	echo "Error: Could not create output directory!";
	exit(1);
}
