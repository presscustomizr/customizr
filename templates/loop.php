<?php
echo '<pre>';
global $wp_query;
//print_r($wp_query);
echo '</pre>';

if ( have_posts() ):
  while ( have_posts() ):
    the_post();    
    do_action('__loop__');
  endwhile;  
endif;
