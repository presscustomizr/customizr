<?php

//a generic loop that can be used to display WP query

global $wp_query, $_query, $wp_the_query;
//do we have a custom query ?
if ( false !== tc_get( 'query' ) ) {
  $_query = new WP_Query( tc_get( 'query' ) );
  $wp_query = $_query;
}

if ( CZR() -> controllers -> tc_is_no_results() || is_404() ) {
  do_action( 'in_' . tc_get('id') );
} else if ( have_posts() && ! is_404() ) {
  while ( have_posts() ):
    the_post();
    do_action( 'in_' . tc_get('id') );
  endwhile;
}


//Always reset the query to the main WP one
$wp_query = $wp_the_query;
