<?php
/*
* Loop template
*/
if ( have_posts() ) {
    do_action( '__loop_start', czr_fn_get_property('id') );

      while ( have_posts() ) {
          the_post();
          czr_fn_render_template(
              czr_fn_get_property( 'loop_item_template' ),//the loop item template is set the loop model. Example : "modules/grid/grid_wrapper"
              czr_fn_get_property( 'loop_item_args' )
          );
      }

    do_action('__loop_end', czr_fn_get_property('id') );
}