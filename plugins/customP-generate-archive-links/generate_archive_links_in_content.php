<?php 
/*
Plugin Name:  Generate Archive Links In Content
Description: Search for words in content mentioning a custom post type 
that has an archive page e.g. 'campuses', 'programs', ecc...
and substitutes the word with a link to the related archive.
*/

// returns an associative array containing all custom post types in the db
// with the archive name as key and the link to the archive as value
// (excludes post types without archive page)
function returnArchiveLinks()
{
	$postTypes = get_post_types(array(
		"public" => true,
		"_builtin" => false
	));

	$archiveLinks = array();

	foreach ($postTypes as $postType) 
	{
		$archiveLink = get_post_type_archive_link($postType);
		$archiveSlug = trim(parse_url($archiveLink, PHP_URL_PATH), '/');

		if($archiveLink)
			$archiveLinks[$archiveSlug] = $archiveLink;
	}

	return $archiveLinks;
}

// generates two arrays for target words and substitutions and performs content changes
function replaceArchiveNamesWithLinks($content)
{
	$archiveLinks = returnArchiveLinks();
	$wordsToBeReplaced = array_keys($archiveLinks);
	$linksToInsert = array();

	foreach($archiveLinks as $key => $value)
		array_push($linksToInsert, "<a href='$value'>$key</a>");
	
	$content = str_replace($wordsToBeReplaced, $linksToInsert, $content);
	
	return $content;
}

add_filter('the_content','replaceArchiveNamesWithLinks');
