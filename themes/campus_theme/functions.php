<?php 

	require get_theme_file_path("inc/like-route.php");
	require get_theme_file_path("inc/search-route.php");

	function university_custom_rest()
	{
		register_rest_field("post", "authorName", array(
			get_callback => function(){return get_the_author();}
		));

		register_rest_field("note", "userNoteCount", array(
			get_callback => function(){return count_user_posts(get_current_user_id(), "note");}
		));
	}

	add_action("rest_api_init", "university_custom_rest");

	function university_files()
	{
		wp_enqueue_script("google_map", "//maps.googleapis.com/maps/api/js?key=AIzaSyBq679gUNs1FEqyWBvBT47i6B5bFaDyMio", NULL, "1.0", true);
		wp_enqueue_script("university_main_js", get_theme_file_uri("/js/scripts-bundled.js"), NULL, "1.0", true);
		wp_enqueue_style("university_main_stiles", get_stylesheet_uri());
		wp_enqueue_style("font-awesome", "//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
		wp_enqueue_style("roboto-font", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i");
		wp_localize_script("university_main_js", "universityData", array(
			"root_url" => get_site_url(),
			"nonce" => wp_create_nonce("wp_rest")
		));
	}

	add_action("wp_enqueue_scripts", "university_files");

	function university_features()
	{
		// implement this to register menus to be edited in admin interface and used in html pages
		//register_nav_menu("headerMenuLocation", "Header menu location");
		
		//dynamic page titling...
		add_theme_support("title-tag");
		add_theme_support("post-thumbnails");
		add_image_size("professor-landscape", "400", "260", true);
		add_image_size("professor-portrait", "480", "650", true);
		add_image_size("page-banner", "1500", "350", true);
	}

	add_action("after_setup_theme", "university_features");

	function university_adjust_queries($query)
	{
		if(!is_admin() AND is_post_type_archive("campus") AND $query->is_main_query())
		{
			$query->set("posts_per_page", -1);
		}

		if(!is_admin() AND is_post_type_archive("program") AND $query->is_main_query())
		{
			$query->set("posts_per_page", -1);
			$query->set("orderby", "title");
			$query->set("order", "ASC");

		}

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

	//custom function to clearly print out vars (var dumping)
	function vd($var) 
	{
	    echo "<pre>";
	    print_r($var);
	    echo "</pre>";
	}

	function universityMapKey($api)
	{
		$api["key"] = "AIzaSyBq679gUNs1FEqyWBvBT47i6B5bFaDyMio";
		return $api;
	}

	add_filter("acf/fields/google_map/api", "universityMapKey");

	function pageBanner($args = null)
	{
		if(!$args["title"])
			$args["title"] = get_the_title();

		if(!$args["subtitle"])
			$args["subtitle"] = get_field("page_banner_subtitle");

		if(!$args["photo"])
		{
			if(get_field("page_banner_background_image"))
				$args["photo"] = get_field("page_banner_background_image")["sizes"]["page-banner"];
			else
			$args["photo"] = get_theme_file_uri("/images/ocean.jpg");
		}
		
		?>

		<div class="page-banner">
		  <div class="page-banner__bg-image" style="background-image: url(<?php echo $args["photo"]; ?>);"></div>
		  <div class="page-banner__content container container--narrow">
		    <h1 class="page-banner__title"><?php echo $args["title"]; ?></h1>
		    <div class="page-banner__intro">
		      <p><?php echo $args["subtitle"]; ?></p>
		    </div>
		  </div>  
		</div>
		
	<?php  
	}

	// Subscribers redirection to fronted after having logged in..
	function redirectSubscriberToFrontend()
	{
		$ourCurrentUser = wp_get_current_user();

		if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == "subscriber")
		{
			wp_redirect(site_url("/"));
			exit;
		}
	}

	add_action("admin_init", "redirectSubscriberToFrontend");

	//Hide admin bar to subscribers
	function hideAdminBarToMinorRoles()
	{
		$ourCurrentUser = wp_get_current_user();

		if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == "subscriber")
		{
			show_admin_bar(false);
		}
	}

	add_action("wp_loaded", "hideAdminBarToMinorRoles");

	//Customizes login page
	function ourHeaderURL()
	{
		return esc_url(site_url("/"));
	}

	add_filter("login_headerurl", "ourHeaderURL");

	
	function ourLoginCSS()
	{
		wp_enqueue_style("university_main_stiles", get_stylesheet_uri());
		wp_enqueue_style("roboto-font", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i");	
	}

	add_action("login_enqueue_scripts", "ourLoginCSS");

	function ourLoginTitle()
	{
		return get_bloginfo("name");
	}
	
	add_filter("login_headertitle", "ourLoginTitle");

	//force note posts to be private
	add_filter("wp_insert_post_data", "makeNotePrivate", 10, 2);

	function makeNotePrivate($data, $postarr)
	{
		if($data["post_type"] == "note")
		{
			if(count_user_posts(get_current_user_id(), "note") > 4 AND !$postarr["ID"])
			{
				die("You have reached note limit!");
			}

			$data["post_content"] = sanitize_textarea_field($data["post_content"]);
			$data["post_title"] = sanitize_text_field($data["post_title"]);
		}

		if($data["post_type"] == "note" AND $data["post_status"] != "trash")
		{
			$data["post_status"] = "private";
		}

		return $data;
	}





