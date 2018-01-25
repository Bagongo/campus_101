<?php get_header(); ?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri("/images/library-hero.jpg") ?>);"></div>
    <div class="page-banner__content container t-center c-white">
      <h1 class="headline headline--large">Welcome!</h1>
      <h2 class="headline headline--medium">We think you&rsquo;ll like it here.</h2>
      <h3 class="headline headline--small">Why don&rsquo;t you check out the <strong>major</strong> you&rsquo;re interested in?</h3>
      <a href="<?php echo site_url('/programs') ?>" class="btn btn--large btn--blue">Find Your Major</a>
    </div>
  </div>

  <div class="full-width-split group">
    <div class="full-width-split__one">
      <div class="full-width-split__inner">
        <h2 class="headline headline--small-plus t-center">Upcoming Events</h2>
        
        <?php 
          $currentDay = date("Ymd");
          $homepageEvents = new WP_Query(array(
                                "posts_per_page" => 2,
                                "post_type" => "event",
                                "meta_key" => "event_date",
                                "order_by" => "meta_value_num",
                                "order" => "DESC",
                                "meta_query" => array(array("key" => "event_date", "compare" => ">=", "value" => $currentDay), "type" => "numeric")
                              ));

            if(!$homepageEvents->have_posts())
            {
              echo "<h3>No upcoming events at the moment...</h3>";
            ?>
            
            <p><b>Wanna see some events you missed?</b><a href="<?php echo site_url("/past-events") ?>"> >> Go to past events...</a></p>

            <?php 

            }
            else
            {
              while($homepageEvents->have_posts())
              { 
                $homepageEvents->the_post();
                // the two arguments combined form the name (with a dash '-' in between) of the file that will get loaded 
                get_template_part("template-parts/content", "event");           
              } 

        ?>
        
        <p class="t-center no-margin"><a href="<?php echo get_post_type_archive_link("event"); ?>" class="btn btn--blue">View All Events</a></p>

        <?php } ?>

      </div>
    </div>
    <div class="full-width-split__two">
      <div class="full-width-split__inner">
        <h2 class="headline headline--small-plus t-center">From Our Blogs</h2>

        <?php

          $homepagePosts = new WP_Query(array("posts_per_page" => 2));
          
          while ($homepagePosts->have_posts()) : $homepagePosts->the_post() 
        ?>
            
            <div class="event-summary">
              <a class="event-summary__date event-summary__date--beige t-center" href="<?php echo get_day_link(get_the_time('Y'), get_the_time('m'), get_the_time('d')); ?>">
                <span class="event-summary__month"><?php the_time('M'); ?></span>
                <span class="event-summary__day"><?php the_time('d'); ?></span>  
              </a>
              <div class="event-summary__content">
                <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                <p><?php if(has_excerpt()) 
                            echo get_the_excerpt();
                        else echo wp_trim_words(get_the_content(), 15); ?> 
                    <a href="<?php the_permalink(); ?>" class="nu gray">Read more</a>
                </p>
              </div>
            </div>

        <?php endwhile;
              wp_reset_postdata(); ?>

        <p class="t-center no-margin"><a href="<?php echo site_url("/blog"); ?>" class="btn btn--yellow">View All Blog Posts</a></p>
      </div>
    </div>
  </div>

  <div class="hero-slider">

    <?php 
      $slides = new WP_Query(array(
                    "posts_per_page" => 5,
                    "post_type" => "slide",
                  ));

      while ($slides->have_posts())
      {
        $slides->the_post();

        $bgImageURL = get_field("page_banner_background_image")["url"]; 
        $pageLink = get_field("custom_link") ? site_url(get_field("custom_link")) : get_field("related_page");
    ?>

      <div class="hero-slider__slide" style="background-image: url(<?php echo $bgImageURL; ?>);">
        <div class="hero-slider__interior container">
          <div class="hero-slider__overlay">
            <h2 class="headline headline--medium t-center"><?php echo the_title(); ?></h2>
            <p class="t-center"><?php echo wp_trim_words(get_the_content(), 25); ?></p>
            <p class="t-center no-margin"><a href="<?php echo $pageLink ?>" class="btn btn--blue">Learn more</a></p>
          </div>
        </div>
      </div>

    <?php

      }

    ?>

</div>
    

<?php get_footer(); ?>
