<?php

//a generic loop that can be used to display WP query

global $wp_query, $_query, $wp_the_query;
//do we have a custom query ?
if ( false !== tc_get( 'query' ) ) {
  $_query = new WP_Query( tc_get( 'query' ) );
  $wp_query = $_query;
}

if ( have_posts() && ! is_404() ) {
  while ( have_posts() ) {
    the_post();
    //if this is the main wp loop then, render the various templates depending on the context and the user options
    if ( 'main_loop' == tc_get('id') ) {

      if ( tc_has('post_list_grid') /*&& CZR() -> controllers -> tc_is_possible('grid_wrapper') */) {
        tc_render_template('modules/grid/grid_wrapper', 'post_list_grid');
      }

      elseif ( tc_has('post_list') ){
        tc_render_template('content/post-lists/post_list_wrapper', 'post_list');
      }

      elseif ( tc_has('singular_article') ) {
        tc_render_template('content/article','singular_article');
      }
    }
    else {
      //if this is a custom loop, use a dynamic action hook to load a custom query loop
      do_action( 'in_' . tc_get('id') );
    }

  }//endwhile;
}
//Always reset the query to the main WP one
$wp_query = $wp_the_query;
