<?php

//a generic loop that can be used to display WP query

global $wp_query, $_query, $wp_the_query;
//do we have a custom query ?
if ( false !== $loop_model -> query ) {
  $_query = new WP_Query( $loop_model -> query );
  $wp_query = $_query;
}


  if ( have_posts() ):
    while ( have_posts() ):
      the_post();
      do_action("in_{$loop_model ->id}");
    endwhile;
  endif;


//Always reset the query to the main WP one
$wp_query = $wp_the_query;
