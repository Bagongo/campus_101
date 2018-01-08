<?php 

	function university_files()
	{
		wp_enqueue_script("google_map", "//maps.googleapis.com/maps/api/js?key=AIzaSyBq679gUNs1FEqyWBvBT47i6B5bFaDyMio", NULL, "1.0", true);
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


