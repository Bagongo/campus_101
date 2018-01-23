<?php

add_action("rest_api_init", "universityLikeRoutes");

function universityLikeRoutes()
{	
	register_rest_route("university/v1", "manageLike", array(
		"methods" => "POST",
		"callback" => createLike
	));

	register_rest_route("university/v1", "manageLike", array(
		"methods" => "DELETE",
		"callback" => deleteLike
	));

}

function createLike($data)
{
	if(is_user_logged_in())
	{
		$profID = sanitize_text_field($data["professorID"]);

		$existQuery = new WP_Query(array(
			"author" => get_current_user_id(),
			"post_type" => "like",
			"meta_query" => array(
				array(
					"key" => "liked_professor_id",
					"compare" => "=",
					"value" => $profID
				)
			)
		));

		if($existQuery->found_posts == 0 AND get_post_type($profID) == "professor")
		{
			return wp_insert_post(array(
				"post_title" => "Like",
				"post_type" => "like",
				"post_status" => "publish",
				"meta_input" => array(
					"liked_professor_id" => $profID
				)
			));
		}
		else
		{
			die("Invalid prof id....");
		}
	}
	else
	{
		die("You must be logged in to give a like....");
	}
}

function deleteLike()
{
	return "deleting like...";
}