<?php 
	get_header();
	pageBanner(array(
		"title" => "All events",
		"subtitle" => "See what's happening...."
	));

 ?>

<div class="container container--narrow page-section">
	<?php 
		while(have_posts())
		{
			the_post();
			get_template_part("template-parts/content-event");
		} 
	?> 
	<?php echo paginate_links() ?>

	<p><b>Wanna see some events you missed?</b><a href="<?php echo site_url("/past-events") ?>"> >> Go to past events...</a></p>

</div>

<?php get_footer() ?>