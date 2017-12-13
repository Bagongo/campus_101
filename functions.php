<?php 

	function university_files()
	{
		wp_enqueue_script("university_main_js", get_theme_file_uri("/js/scripts-bundled.js"), NULL, "1.0", true);
		wp_enqueue_style("university_main_stiles", get_stylesheet_uri());
		wp_enqueue_style("font-awesome", "//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
		wp_enqueue_style("roboto-font", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i");
	}

	add_action("wp_enqueue_scripts", "university_files");

	function university_features()
	{
		// implement this to register menus to be edited in admin interface and used in html pages
		//register_nav_menu("headerMenuLocation", "Header menu location");
		
		//dynamic page titling...
		add_theme_support("title-tag");
	}

	add_action("after_setup_theme", "university_features");

	function university_adjust_queries($query)
	{
		if(!is_admin() AND is_post_type_archive("event") AND $query->is_main_query())
		{
			$currentDay = date("Ymd");

			$query->set("meta_key", "event_date");
			$query->set("orderby", "meta_value_num");
			$query->set("order", "ASC");
			$query->set("meta_query", array(array("key" => "event_date", "compare" => ">=", "value" => $currentDay), "type" => "numeric"));
		}
	}

	add_action("pre_get_posts", "university_adjust_queries");


