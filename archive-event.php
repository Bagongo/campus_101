<?php 
	get_header();
	pageBanner(array(
		"title" => "All events",
		"subtitle" => "See what's happening...."
	));

 ?>

<div class="container container--narrow page-section">
	<?php while(have_posts()) :
		the_post();
	?>

	<div class="event-summary">
		<a class="event-summary__date t-center" href="">
	      <span class="event-summary__month"><?php 
	        $eventDate = new DateTime(get_field("event_date"));
	        echo $eventDate->format('M');
	       ?></span>
	      <span class="event-summary__day"><?php echo $eventDate->format('d') ?></span>  
	    </a>
		<div class="event-summary__content">
		  <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h5>
		  <p><?php the_excerpt(); ?> <a href="<?php the_permalink() ?>" class="nu gray">Learn more</a></p>
		</div>
	</div>

	<?php endwhile ?> 
	<?php echo paginate_links() ?>

	<p><b>Wanna see some events you missed?</b><a href="<?php echo site_url("/past-events") ?>"> >> Go to past events...</a></p>

</div>

<?php get_footer() ?>