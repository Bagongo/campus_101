<?php 
/*
Plugin Name:  Programs Counter
Description: Dinamically outputs the current number of programs where mentioned in content (via shortcodes) 
*/


add_shortcode("programCounter", "countPrograms");

function countPrograms()
{
	$programsQuery = new WP_Query(array(
		"post_type" => "program"
	));

	$programsNumber = $programsQuery->post_count;

	return $programsNumber; 
}
