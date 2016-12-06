<?php
/*
* Related posts wrapper template
*/

if ( have_posts() ) {
  do_action( '__loop_start', czr_fn_get('id') );

    while ( have_posts() ):
      the_post();
      //echo "sono qui";
      czr_fn_render_template( czr_fn_get( 'loop_item_template' ), czr_fn_get('loop_item_args') );
    endwhile;

  do_action('__loop_end', czr_fn_get('id') );
}